<?php

namespace App\Http\Controllers\teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Classe;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\ExamType;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\MergedClassSchedule;
use App\Models\Payslip;
use App\Models\PayrollRun;
use App\Models\Section;
use App\Models\Staff;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentClassEnrollment;
use App\Models\Subject;
use App\Models\Term;
use App\Models\Timetable;
use App\Models\TimetableSlot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class teacherController extends Controller
{
    protected $examListRoute = 'admin.exams.index';


    protected $examMarkListRoute = 'admin.exam_marks.index';


    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = Staff::where('username', $request->username)->first();

        if ($user && Hash::check($request->password, $user->password)) {

            // ✅ Manual session login
            session([
                'user_id' => $user->id,
                'username' => $user->username,
                'tenant_id' => $user->tenant_id,
                'portal_type' => 'teacher'
            ]);

            return redirect('/teachersacademic/dashboard');
        }

        return back()->with('error', 'Invalid username or password');
    }


    public function logout(Request $request)
    {
        // remove specific session
        $request->session()->forget(['user_id', 'username', 'tenant_id', 'portal_type']);

        // OR destroy all session
        $request->session()->flush();

        return redirect('/teachersacademic')->with('success', 'Logged out successfully');
    }


    public function dashboard()
    {
        $teacherId = session('user_id');
        $tenantId = session('tenant_id');
        $today = Carbon::today();
        $dayOfWeek = $today->dayOfWeekIso;
        $startOfWeek = $today->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $today->copy()->endOfWeek(Carbon::SATURDAY);

        $teachers = Staff::with(['designation', 'department'])
            ->where('id', $teacherId)
            ->first();

        $staffSlots = TimetableSlot::query()
            ->where('tenant_id', $tenantId)
            ->where('staff_id', $teacherId)
            ->with(['timetable.class', 'timetable.section', 'timetable.stream', 'period', 'subject', 'room'])
            ->get();

        $activeTimetables = Timetable::query()
            ->whereIn('id', $staffSlots->pluck('timetable_id')->unique())
            ->with(['class', 'section', 'stream'])
            ->get();

        $classGroups = $staffSlots
            ->map(function ($slot) {
                return optional($slot->timetable)->class_id . '_' .
                    (optional($slot->timetable)->section_id ?? 0) . '_' .
                    (optional($slot->timetable)->stream_id ?? 0);
            })
            ->filter()
            ->unique();

        $currentAcademicYearId = AcademicYear::where('tenant_id', $tenantId)
            ->where('is_current', true)
            ->value('id');

        $studentReach = 0;
        foreach ($classGroups as $groupKey) {
            [$classId, $sectionId, $streamId] = explode('_', $groupKey);
            $studentReach += StudentClassEnrollment::query()
                ->where('tenant_id', $tenantId)
                ->where('class_id', $classId)
                ->where('section_id', $sectionId ?: null)
                ->where('stream_id', $streamId ?: null)
                ->when($currentAcademicYearId, fn($query) => $query->where('academic_year_id', $currentAcademicYearId))
                ->where('status', 'active')
                ->count();
        }

        $todayRegularSlots = $staffSlots
            ->where('day_of_week', $dayOfWeek)
            ->sortBy('period_id')
            ->values();

        $mergedSchedules = MergedClassSchedule::query()
            ->where('tenant_id', $tenantId)
            ->where('staff_id', $teacherId)
            ->whereBetween('schedule_date', [$startOfWeek, $endOfWeek])
            ->with(['subject', 'room', 'period'])
            ->orderBy('schedule_date')
            ->orderBy('period_id')
            ->get();

        $todayMergedSchedules = $mergedSchedules
            ->filter(fn($schedule) => optional($schedule->schedule_date)->toDateString() === $today->toDateString())
            ->values();

        $subjectCount = $staffSlots->pluck('subject_id')->filter()->unique()->count();

        return view('teachersacademic.dashboard', compact(
            'teachers',
            'activeTimetables',
            'staffSlots',
            'todayRegularSlots',
            'mergedSchedules',
            'todayMergedSchedules',
            'studentReach',
            'subjectCount',
            'startOfWeek',
            'endOfWeek',
            'today'
        ));
    }






    public function show()
    {
        $teacherId = session('user_id');

        if (!$teacherId) {
            abort(404, 'User not logged in');
        }

        $staff = Staff::where('id', $teacherId)->first();

        if (!$staff) {
            abort(404, 'Staff not found');
        }

        $staff->load([
            'designation',
            'department',
            'tenant'
        ]);

        $staffSchedules = TimetableSlot::where('tenant_id', $staff->tenant_id)
            ->where('staff_id', $staff->id)
            ->with([
                'timetable.class',
                'timetable.section',
                'timetable.stream',
                // 'timetable.term',
                'period'
            ])
            ->get()
            ->groupBy('timetable_id');

        $timetables = Timetable::whereHas('slots', function ($q) use ($staff) {
            $q->where('staff_id', $staff->id);
        })
            ->where('tenant_id', $staff->tenant_id)
            ->with(['class', 'section', 'stream'])
            ->get()
            ->filter(fn($t) => $staffSchedules->has($t->id));

        return view('teachersacademic.show', compact('staff', 'staffSchedules', 'timetables'));
    }



    public function index(Request $request)
    {
        $exams = Exam::where('tenant_id', Auth::user()->tenant_id)
            ->with(['examType', 'class', 'section', 'subject', 'academicYear'])
            ->latest()
            ->paginate(20);

        return view('teachersacademic.index', compact('exams'));
    }

    public function create()
    {
        $exam = new Exam;
        $examTypes = ExamType::where('tenant_id', Auth::user()->tenant_id)->get();
        $classes = Classe::where('tenant_id', Auth::user()->tenant_id)->get();
        $sections = Section::where('tenant_id', Auth::user()->tenant_id)->get();
        $subjects = Subject::where('tenant_id', Auth::user()->tenant_id)->get();
        $academicYears = AcademicYear::where('tenant_id', Auth::user()->tenant_id)->get();
        $terms = Term::where('tenant_id', Auth::user()->tenant_id)->get();

        return view('teachersacademic.form', compact('exam', 'examTypes', 'classes', 'sections', 'subjects', 'academicYears', 'terms'));
    }

    public function edit($id)
    {
        $exam = Exam::where('tenant_id', Auth::user()->tenant_id)
            ->with(['examType', 'class', 'section', 'subject', 'academicYear'])
            ->find($id);

        if (empty($exam)) {
            abort(404);
        }

        $examTypes = ExamType::where('tenant_id', Auth::user()->tenant_id)->get();
        $classes = Classe::where('tenant_id', Auth::user()->tenant_id)->get();
        $sections = Section::where('tenant_id', Auth::user()->tenant_id)->get();
        $subjects = Subject::where('tenant_id', Auth::user()->tenant_id)->get();
        $academicYears = AcademicYear::where('tenant_id', Auth::user()->tenant_id)->get();
        $terms = Term::where('tenant_id', Auth::user()->tenant_id)->get();

        return view('teachersacademic.form', compact('exam', 'examTypes', 'classes', 'sections', 'subjects', 'academicYears', 'terms'));
    }

    public function store(Request $request)
    {
        $rules = [
            'exam_type_id'    => 'required|exists:exam_types,id',
            'name'            => 'required|string|max:191',
            'class_id'        => 'required|exists:classes,id',
            'section_id'      => 'required|exists:sections,id',
            'subject_id'      => 'required|exists:subjects,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'term_id'         => 'nullable|exists:terms,id',
            'exam_date'       => 'required|date',
            'start_time'      => 'required|date_format:H:i',
            'end_time'        => 'required|date_format:H:i|after:start_time',
            'total_marks'     => 'required|integer|min:1',
            'pass_marks'      => 'required|integer|min:0|max_field:total_marks',
        ];

        $messages = [
            'exam_type_id.required' => 'Please select exam type!',
            'name.required'         => 'Please enter exam name!',
            'name.max'              => 'Exam name should be maximum of 191 characters!',
            'class_id.required'     => 'Please select class!',
            'section_id.required'   => 'Please select section!',
            'subject_id.required'   => 'Please select subject!',
            'academic_year_id.required' => 'Please select academic year!',
            'exam_date.required'    => 'Please select exam date!',
            'start_time.required'   => 'Please enter start time!',
            'end_time.required'     => 'Please enter end time!',
            'end_time.after'        => 'End time must be after start time!',
            'total_marks.required'  => 'Please enter total marks!',
            'total_marks.min'       => 'Total marks must be at least 1!',
            'pass_marks.required'   => 'Please enter pass marks!',
            'pass_marks.max_field'  => 'Pass marks cannot exceed total marks!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        // if ($validator->fails()) {
        //     $errorMessage = $validator->errors()->first();
        //     return redirect()->back()->withErrors($errorMessage)->withInput();
        // }

        if (!empty($request->get('id'))) {
            $exam = Exam::where('tenant_id', Auth::user()->tenant_id)->find($request->get('id'));
            if (empty($exam)) {
                abort(404);
            }
            $redirect = [
                'url' => redirect()->back(),
                'msg' => 'Exam updated successfully',
            ];
        } else {
            $exam = new Exam;
            $redirect = [
                'url' => redirect()->route($this->examListRoute),
                'msg' => 'Exam created successfully',
            ];
        }

        $exam->tenant_id        = Auth::user()->tenant_id;
        $exam->exam_type_id     = $request->exam_type_id;
        $exam->name             = trim($request->name);
        $exam->class_id         = $request->class_id;
        $exam->section_id       = $request->section_id;
        $exam->subject_id       = $request->subject_id;
        $exam->academic_year_id = $request->academic_year_id;
        $exam->term_id          = $request->term_id;
        $exam->exam_date        = $request->exam_date;
        $exam->start_time       = $request->start_time;
        $exam->end_time         = $request->end_time;
        $exam->total_marks      = $request->total_marks;
        $exam->pass_marks       = $request->pass_marks;

        try {
            $exam->save();

            return $redirect['url']->with('success', $redirect['msg']);
        } catch (\Exception $e) {
            return redirect('/exam')->back()->with('error', 'Something went wrong, please try again later!');
        }
    }

    public function shows($id)
    {
        $exam = Exam::where('tenant_id', Auth::user()->tenant_id)
            ->with(['examType', 'class', 'section', 'subject', 'academicYear'])
            ->find($id);

        if (empty($exam)) {
            abort(404);
        }

        return view('teachersacademic.form', compact('exam'));
    }

    public function destroy($id)
    {
        $exam = Exam::where('tenant_id', Auth::user()->tenant_id)->find($id);

        if (empty($exam)) {
            abort(404);
        }

        $exam->delete();

        return redirect()->route('teachersacademic.index')->with('success', 'Exam deleted successfully.');
    }




    public function exammarkindex(Request $request)
    {
        $examMarks = ExamMark::where('tenant_id', Auth::user()->tenant_id)
            ->with(['exam', 'student', 'enteredBy'])
            ->latest()
            ->paginate(20);

        return view('teachersacademic.exammarkshow', compact('examMarks'));
    }

    public function exammarkcreate()
    {
        $examMark = new ExamMark;
        $exams = Exam::where('tenant_id', Auth::user()->tenant_id)->get();
        $students = Student::where('tenant_id', Auth::user()->tenant_id)->get();
        $users = User::all(); // or filter as needed

        return view('teachersacademic.exammarkform', compact('examMark', 'exams', 'students', 'users'));
    }

    public function exammarkedit($id)
    {
        $examMark = ExamMark::where('tenant_id', Auth::user()->tenant_id)
            ->with(['exam', 'student', 'enteredBy'])
            ->find($id);

        if (empty($examMark)) {
            abort(404);
        }

        $exams = Exam::where('tenant_id', Auth::user()->tenant_id)->get();
        $students = Student::where('tenant_id', Auth::user()->tenant_id)->get();
        $users = User::all();

        return view('teachersacademic.exammarkform', compact('examMark', 'exams', 'students', 'users'));
    }

    public function exammarkstore(Request $request)
    {
        $rules = [
            'exam_id'             => 'required|exists:exams,id',
            'student_id'          => 'required|exists:students,id',
            'marks_obtained'      => 'required|numeric|min:0|max:9999.99',
            'grade_letter'        => 'nullable|string|max:5',
            'remarks'             => 'nullable|string|max:1000',
            'entered_by_user_id'  => 'required|exists:users,id',
        ];

        $messages = [
            'exam_id.required'             => 'Please select exam!',
            'student_id.required'          => 'Please select student!',
            'marks_obtained.required'      => 'Please enter marks obtained!',
            'marks_obtained.numeric'       => 'Marks must be a number!',
            'marks_obtained.min'           => 'Marks cannot be negative!',
            'marks_obtained.max'           => 'Marks too large!',
            'grade_letter.max'             => 'Grade letter too long!',
            'remarks.max'                  => 'Remarks too long!',
            'entered_by_user_id.required'  => 'Please select who entered the marks!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            return redirect()->back()->withErrors($errorMessage)->withInput();
        }

        if (!empty($request->get('id'))) {
            $examMark = ExamMark::where('tenant_id', Auth::user()->tenant_id)->find($request->get('id'));
            if (empty($examMark)) {
                abort(404);
            }
            $redirect = [
                'url' => redirect()->back(),
                'msg' => 'Exam mark updated successfully',
            ];
        } else {
            $examMark = new ExamMark;
            $redirect = [
                'url' => redirect()->route($this->examMarkListRoute),
                'msg' => 'Exam mark created successfully',
            ];
        }

        $examMark->tenant_id           = Auth::user()->tenant_id;
        $examMark->exam_id             = $request->exam_id;
        $examMark->student_id          = $request->student_id;
        $examMark->marks_obtained      = $request->marks_obtained;
        $examMark->grade_letter        = $request->grade_letter;
        $examMark->remarks             = $request->remarks;
        $examMark->entered_by_user_id  = $request->entered_by_user_id;

        try {
            $examMark->save();

            return $redirect['url']->with('success', $redirect['msg']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong, please try again later!');
        }
    }

    public function exammarkshow($id)
    {
        $examMark = ExamMark::where('tenant_id', Auth::user()->tenant_id)
            ->with(['exam', 'student', 'enteredBy'])
            ->find($id);

        if (empty($examMark)) {
            abort(404);
        }

        return view('teachersacademic.exammarkshow', compact('examMark'));
    }

    public function exammarkdestroy($id)
    {
        $examMark = ExamMark::where('tenant_id', Auth::user()->tenant_id)->find($id);

        if (empty($examMark)) {
            abort(404);
        }

        $examMark->delete();

        return redirect()->route('teachersacademic.exammarkform')->with('success', 'Exam mark deleted successfully.');
    }

    // ==========================================
    // ATTENDANCE MARKING (TEACHER PORTAL)
    // ==========================================
    public function markDirect(TimetableSlot $slot, $date)
    {
        $tenantId = session('tenant_id');
        $teacherId = session('user_id');

        if (!$tenantId || !$teacherId) {
            abort(401, 'Unauthorized access.');
        }

        if ($slot->tenant_id !== $tenantId || $slot->staff_id !== $teacherId) {
            abort(403, 'Unauthorized access to this schedule.');
        }

        $date = Carbon::parse($date);
        
        // Fetch students enrolled in this class
        $students = StudentClassEnrollment::where('tenant_id', $tenantId)
            ->where('course_id', $slot->timetable->course_id)
            ->where('stream_id', $slot->timetable->stream_id)
            ->where('class_id', $slot->timetable->class_id)
            ->where('section_id', $slot->timetable->section_id)
            ->where('status', 'active')
            ->whereHas('student', fn($query) => $query->where('tenant_id', $tenantId)->where('status', 'active'))
            ->with(['student'])
            ->get()
            ->sortBy(fn($enrollment) => str_pad((string) ($enrollment->student->roll_number ?? ''), 20, '0', STR_PAD_LEFT))
            ->values();

        // Fetch existing attendance
        $existing = StudentAttendance::where('tenant_id', $tenantId)
            ->where('period_id', $slot->period_id)
            ->where('date', $date->toDateString())
            ->whereIn('enrollment_id', $students->pluck('id'))
            ->get(['enrollment_id', 'status', 'remarks'])
            ->mapWithKeys(fn($attendance) => [
                $attendance->enrollment_id => [
                    'status' => $attendance->status,
                    'remarks' => $attendance->remarks,
                ],
            ])
            ->toArray();

        return view('teachersacademic.student_attendance.direct_mark', compact('slot', 'date', 'students', 'existing'));
    }

    public function storeAttendance(Request $request)
    {
        $tenantId = session('tenant_id');
        $teacherId = session('user_id');

        if (!$tenantId || !$teacherId) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $rules = [
            'date'     => 'required|date',
            'slot_id'  => 'required|exists:timetable_slots,id',
            'attendance' => 'required|array',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid data.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $slot = TimetableSlot::where('id', $request->slot_id)
            ->where('tenant_id', $tenantId)
            ->where('staff_id', $teacherId)
            ->first();

        if (!$slot) {
            return response()->json([
                'message' => 'Invalid slot or you are not allowed to mark attendance for this slot.',
            ], 403);
        }

        if (! $slot->timetable) {
            return response()->json([
                'message' => 'Invalid slot or timetable not found.',
            ], 404);
        }

        $staff = Staff::find($teacherId);

        foreach ($request->attendance as $enrollmentId => $data) {
            StudentAttendance::updateOrCreate(
                [
                    'tenant_id'      => $tenantId,
                    'enrollment_id'  => $enrollmentId,
                    'period_id'      => $slot->period_id,
                    'date'           => $request->date,
                ],
                [
                    'status'         => $data['status'] ?? 'absent',
                    'remarks'        => $data['remarks'] ?? null,
                    'marked_by_user_id' => $staff ? $staff->id : null,
                ]
            );
        }

        return response()->json([
            'message' => 'Attendance marked successfully!',
        ]);
    }


    // ==========================================
    // SELF-SERVICE: STAFF ATTENDANCE (PUNCH IN/OUT)
    // ==========================================

    public function attendanceReport(Request $request)
    {
        $staffId  = session('user_id');
        $tenantId = session('tenant_id');

        if (!$staffId || !$tenantId) {
            return redirect('/teachersacademic')->with('error', 'Session expired. Please log in again.');
        }

        $staff = Staff::find($staffId);
        $today = Carbon::today()->toDateString();

        // Month/Year filter
        $month = (int) $request->get('month', Carbon::now()->month);
        $year  = (int) $request->get('year',  Carbon::now()->year);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate   = $startDate->copy()->endOfMonth();

        // Today's record
        $todayRecord = Attendance::where('tenant_id', $tenantId)
            ->where('staff_id', $staffId)
            ->whereDate('attendance_date', $today)
            ->first();

        // Monthly records
        $monthlyRecords = Attendance::where('tenant_id', $tenantId)
            ->where('staff_id', $staffId)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->orderBy('attendance_date', 'desc')
            ->get();

        // Monthly stats
        $stats = [
            'present'  => $monthlyRecords->where('status', 'present')->count(),
            'absent'   => $monthlyRecords->where('status', 'absent')->count(),
            'half_day' => $monthlyRecords->where('status', 'half_day')->count(),
            'leave'    => $monthlyRecords->where('status', 'leave')->count(),
            'late'     => $monthlyRecords->where('is_late', true)->count(),
        ];

        return view('teachersacademic.my_attendance', compact(
            'staff', 'today', 'todayRecord', 'monthlyRecords', 'stats', 'month', 'year'
        ));
    }

    public function attendancePunch(Request $request)
    {
        $staffId  = session('user_id');
        $tenantId = session('tenant_id');

        if (!$staffId || !$tenantId) {
            return redirect('/teachersacademic')->with('error', 'Session expired.');
        }

        $today = Carbon::today()->toDateString();
        $now   = Carbon::now()->format('H:i:s');

        $record = Attendance::where('tenant_id', $tenantId)
            ->where('staff_id', $staffId)
            ->whereDate('attendance_date', $today)
            ->first();

        if (!$record) {
            // First punch → Punch In
            Attendance::create([
                'tenant_id'       => $tenantId,
                'staff_id'        => $staffId,
                'attendance_date' => $today,
                'check_in'        => $now,
                'check_out'       => null,
                'working_minutes' => 0,
                'is_late'         => false,
                'status'          => 'present',
                'source'          => 'self',
                'remark'          => 'Punched in via staff portal',
            ]);
            return redirect()->route('teacher.my_attendance.index')
                ->with('success', '✅ Punched In at ' . Carbon::now()->format('h:i A'));
        }

        if ($record->check_in && !$record->check_out) {
            // Second punch → Punch Out
            $checkIn  = Carbon::parse($record->check_in);
            $checkOut = Carbon::now();
            $minutes  = (int) $checkIn->diffInMinutes($checkOut);

            $record->update([
                'check_out'       => $checkOut->format('H:i:s'),
                'working_minutes' => $minutes,
                'remark'          => $record->remark . ' | Punched out via staff portal',
            ]);
            return redirect()->route('teacher.my_attendance.index')
                ->with('success', '✅ Punched Out at ' . $checkOut->format('h:i A') . ' — ' . floor($minutes / 60) . 'h ' . ($minutes % 60) . 'm worked.');
        }

        return redirect()->route('teacher.my_attendance.index')
            ->with('info', 'You have already punched in and out for today.');
    }


    // ==========================================
    // SELF-SERVICE: LEAVE REQUESTS
    // ==========================================

    public function leaveIndex()
    {
        $staffId  = session('user_id');
        $tenantId = session('tenant_id');

        if (!$staffId) {
            return redirect('/teachersacademic')->with('error', 'Session expired.');
        }

        $leaveRequests = LeaveRequest::where('staff_id', $staffId)
            ->with('leaveType')
            ->latest('id')
            ->paginate(10);

        $stats = [
            'pending'  => LeaveRequest::where('staff_id', $staffId)->where('status', 'pending')->count(),
            'approved' => LeaveRequest::where('staff_id', $staffId)->where('status', 'approved')->count(),
            'rejected' => LeaveRequest::where('staff_id', $staffId)->where('status', 'rejected')->count(),
        ];

        return view('teachersacademic.my_leave_index', compact('leaveRequests', 'stats'));
    }

    public function leaveCreate()
    {
        $tenantId = session('tenant_id');

        if (!$tenantId) {
            return redirect('/teachersacademic')->with('error', 'Session expired.');
        }

        $leaveTypes = LeaveType::where('tenant_id', $tenantId)->orderBy('name')->get();

        return view('teachersacademic.my_leave_create', compact('leaveTypes'));
    }

    public function leaveStore(Request $request)
    {
        $staffId  = session('user_id');
        $tenantId = session('tenant_id');

        if (!$staffId) {
            return redirect('/teachersacademic')->with('error', 'Session expired.');
        }

        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'from_date'     => 'required|date',
            'to_date'       => 'required|date|after_or_equal:from_date',
            'total_days'    => 'required|numeric|min:0.5',
            'half_day'      => 'required|in:none,first,second',
            'reason'        => 'nullable|string|max:2000',
        ]);

        // Ensure leave type belongs to this tenant
        abort_unless(
            LeaveType::where('tenant_id', $tenantId)->whereKey($request->leave_type_id)->exists(),
            403
        );

        LeaveRequest::create([
            'staff_id'      => $staffId,
            'leave_type_id' => $request->leave_type_id,
            'from_date'     => $request->from_date,
            'to_date'       => $request->to_date,
            'total_days'    => $request->total_days,
            'half_day'      => $request->half_day,
            'reason'        => $request->reason,
            'status'        => 'pending',
        ]);

        return redirect()->route('teacher.my_leave.index')
            ->with('success', '✅ Leave request submitted successfully. Awaiting approval.');
    }


    // ==========================================
    // SELF-SERVICE: SALARY / PAYSLIPS
    // ==========================================

    public function salaryIndex()
    {
        $staffId = session('user_id');

        if (!$staffId) {
            return redirect('/teachersacademic')->with('error', 'Session expired.');
        }

        $payslips = Payslip::where('staff_id', $staffId)
            ->where('is_published', true)
            ->with('payrollRun')
            ->orderByDesc('id')
            ->get();

        return view('teachersacademic.my_salary_index', compact('payslips'));
    }

    public function salaryShow(Payslip $payslip)
    {
        $staffId = session('user_id');

        if (!$staffId) {
            return redirect('/teachersacademic')->with('error', 'Session expired.');
        }

        // Only allow viewing own payslip
        abort_unless((int) $payslip->staff_id === (int) $staffId, 403);

        // Also ensure published
        abort_unless($payslip->is_published, 403);

        $payslip->load('payrollRun', 'staff.designation', 'staff.department');

        return view('teachersacademic.my_salary_show', compact('payslip'));
    }
}
