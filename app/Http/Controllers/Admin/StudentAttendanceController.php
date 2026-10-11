<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AttendanceExport;
use App\Http\Controllers\Controller;
use App\Models\StudentAttendance;
use App\Models\StudentClassEnrollment;
use App\Models\Student;
use App\Models\Classe;
use App\Models\Section;
use App\Models\Stream;
use App\Models\Course;
use App\Models\CourseStream;
use App\Models\ClassStreamMapping;
use App\Models\AcademicYear;
use App\Models\AttendancePeriod;
use App\Models\MergedClassSchedule;
use App\Models\Term;
use App\Models\Timetable;
use App\Models\TimetableSlot;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class StudentAttendanceController extends Controller
{

    public function attendanceByDate(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $academicYears = AcademicYear::where('tenant_id', $tenantId)
            ->orderBy('start_year', 'desc')
            ->get();

        $courses = Course::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();

        $streams = Stream::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();

        $classQuery = Classe::where('tenant_id', $tenantId);

        if ($request->filled('stream_id')) {
            $classQuery->whereHas('streams', function ($q) use ($request) {
                $q->where('stream_id', $request->stream_id);
            });
        }

        if ($request->filled('academic_year_id')) {
            $classQuery->whereHas('enrollments.academicYear', function ($q) use ($request) {
                $q->where('academic_year_id', $request->academic_year_id);
            });
        }

        $classes = $classQuery->orderBy('name')->get();

        $sections = collect();
        if ($request->filled('class_id')) {
            $sections = Section::where('tenant_id', $tenantId)
                ->where('class_id', $request->class_id)
                ->orderBy('name')
                ->get();
        }

        $query = StudentAttendance::where('tenant_id', $tenantId)
            ->where('date', $request->filled('date') ? $request->date : now()->toDateString());

        if ($request->filled('academic_year_id')) {
            $query->whereHas('enrollment', fn($q) => $q->where('academic_year_id', $request->academic_year_id));
        }

        if ($request->filled('course_id')) {
            $query->whereHas('enrollment', fn($q) => $q->where('course_id', $request->course_id));
        }

        if ($request->filled('stream_id')) {
            $query->whereHas('enrollment', fn($q) => $q->where('stream_id', $request->stream_id));
        }

        if ($request->filled('class_id')) {
            $query->whereHas('enrollment', fn($q) => $q->where('class_id', $request->class_id));
        }

        if ($request->filled('section_id')) {
            $query->whereHas('enrollment', fn($q) => $q->where('section_id', $request->section_id));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('enrollment.student', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('roll_number', 'like', "%{$search}%");
            });
        }

        $attendances = $query->with([
            'enrollment.student',
            'enrollment.class',
            'enrollment.section',
            'period',
            'markedBy',
        ])->orderBy('date', 'desc')->orderBy('created_at', 'desc')
            ->paginate(50)->withQueryString();

        $statsQuery = clone $query;
        $stats = $statsQuery->reorder()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Reset the ORDER BY clause before grouping
        $stats = $statsQuery->reorder() // This resets all order by clauses
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
        return view('Academics.student_attendance.date_view', compact(
            'attendances',
            'stats',
            'academicYears',
            'courses',
            'streams',
            'classes',
            'sections'
        ));
    }

    public function index()
    {
        $courses = Course::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        $academicYears = AcademicYear::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('start_year', 'desc')
            ->get();

        $streams = Stream::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        $terms = Term::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('start_date')
            ->get();

        $attendanceRecords = StudentAttendance::where('tenant_id', auth()->user()->tenant_id)
            ->with([
                'enrollment.student',
                'enrollment.class',
                'enrollment.section',
                'period',
                'markedBy'
            ])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('Academics.student_attendance.index', compact('attendanceRecords', 'courses', 'academicYears', 'streams', 'terms'));
    }

    public function create()
    {
        $courses = Course::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        $academicYears = AcademicYear::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        $streams = Stream::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        $classes = Classe::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('order_index')
            ->orderBy('name')
            ->get();

        $sections = Section::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        $periods = AttendancePeriod::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('start_time')
            ->get();

        return view('Academics.student_attendance.form', compact(
            'courses',
            'academicYears',
            'streams',
            'classes',
            'sections',
            'periods'
        ));
    }

    public function edit(StudentAttendance $attendance)
    {
        $this->authorizeTenant($attendance);

        $courses = Course::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        $academicYears = AcademicYear::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        $streams = Stream::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        $classes = Classe::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('order_index')
            ->orderBy('name')
            ->get();

        $sections = Section::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        $periods = AttendancePeriod::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('start_time')
            ->get();

        return view('Academics.student_attendance.form', compact(
            'attendance',
            'courses',
            'academicYears',
            'streams',
            'classes',
            'sections',
            'periods'
        ));
    }

    public function destroy(StudentAttendance $attendance)
    {
        $this->authorizeTenant($attendance);

        try {
            $attendance->delete();
            return back()->with('success', 'Attendance record deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete attendance record.');
        }
    }

    /**
     * AJAX: Get streams for a selected course
     */
    public function getStreams(Request $request)
{
    $request->validate([
        'course_id' => 'required|exists:courses,id',
    ]);

    $tenantId = auth()->user()->tenant_id;

    $streams = Stream::query()
        ->join('course_stream', function ($join) use ($tenantId) {
            $join->on('streams.id', '=', 'course_stream.stream_id')
                 ->where('course_stream.tenant_id', '=', $tenantId);
        })
        ->where('streams.tenant_id', $tenantId)
        ->where('course_stream.course_id', $request->course_id)
        ->select('streams.id', 'streams.name')
        ->distinct()
        ->orderBy('streams.name')
        ->get();

    return response()->json(['streams' => $streams]);
}

    public function getTimetableForDate(Request $request)
    {
        $rules = [
            'academic_year_id' => 'required|exists:academic_years,id',
            'course_id'        => 'required|exists:courses,id',
            'stream_id'        => 'required|exists:streams,id',
            'class_id'         => 'required|exists:classes,id',
            'section_id'       => 'required|exists:sections,id',
            'date'             => 'required|date',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid data.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $date = Carbon::parse($request->date);
        $dayOfWeek = $date->dayOfWeek;

        $timetable = Timetable::where('tenant_id', auth()->user()->tenant_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('course_id', $request->course_id)
            ->where('stream_id', $request->stream_id)
            ->where('class_id', $request->class_id)
            ->where('section_id', $request->section_id)
            ->first();

        if (!$timetable) {
            return response()->json([
                'message' => 'No timetable found for this class and section.',
                'timetable' => null,
                'slots' => [],
            ]);
        }

        $slots = TimetableSlot::where('timetable_id', $timetable->id)
            ->where('day_of_week', $dayOfWeek)
            ->with(['period', 'subject', 'staff', 'room'])
            ->orderBy('period.start_time')
            ->get();

        return response()->json([
            'timetable' => $timetable,
            'slots'     => $slots,
        ]);
    }

    public function getClasses(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'course_id'        => 'required|exists:courses,id',
            'stream_id'        => 'required|exists:streams,id',
        ]);

        $tenantId = auth()->user()->tenant_id;
        $courseStream = CourseStream::where('tenant_id', $tenantId)
            ->where('course_id', $request->course_id)
            ->where('stream_id', $request->stream_id)
            ->first();

        if (! $courseStream) {
            return response()->json(['classes' => []]);
        }

        $classes = Classe::query()
            ->join('class_stream_mappings', 'class_stream_mappings.class_id', '=', 'classes.id')
            ->where('classes.tenant_id', $tenantId)
            ->where('class_stream_mappings.tenant_id', $tenantId)
            ->where('class_stream_mappings.course_stream_id', $courseStream->id)
            ->orderBy('classes.name')
            ->select('classes.id', 'classes.name')
            ->distinct()
            ->get();

        return response()->json(['classes' => $classes]);
    }

    public function getSections(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'stream_id' => 'required|exists:streams,id',
            'class_id'  => 'required|exists:classes,id',
        ]);

        $mapping = $this->resolveClassStreamMapping($request->course_id, $request->stream_id, $request->class_id);

        if (! $mapping) {
            return response()->json(['sections' => []]);
        }

        $sections = Section::where('tenant_id', auth()->user()->tenant_id)
            ->where('class_stream_id', $mapping->id)
            ->orderBy('name')
            ->select('id', 'name')
            ->get();

        return response()->json(['sections' => $sections]);
    }

    public function getSlots(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $rules = [
            'academic_year_id' => 'required|exists:academic_years,id',
            'course_id'        => 'required|exists:courses,id',
            'stream_id'        => 'required|exists:streams,id',
            'class_id'         => 'required|exists:classes,id',
            'section_id'       => 'required|exists:sections,id',
            'date'             => 'required|date',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid data.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $date = Carbon::parse($request->date);
        $dayOfWeek = $date->dayOfWeek;

        $timetable = Timetable::where('tenant_id', auth()->user()->tenant_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('course_id', $request->course_id)
            ->where('stream_id', $request->stream_id)
            ->where('class_id', $request->class_id)
            ->where('section_id', $request->section_id)
            ->first();

        if (!$timetable) {
            return response()->json([
                'message' => 'No timetable found for this class, section, stream.',
                'slots'   => [],
            ]);
        }

        $slots = TimetableSlot::where('timetable_id', $timetable->id)
            ->where('day_of_week', $dayOfWeek)
            ->with(['timetable.class', 'timetable.section', 'period', 'subject', 'staff', 'room'])
            ->orderBy('period_id')
            ->get();

        return response()->json(['slots' => $slots]);
    }

    public function getStudents(Request $request, TimetableSlot $slot)
    {
        $request->validate([
            'date'             => 'required|date',
            'academic_year_id' => 'nullable|integer',
            'course_id'        => 'nullable|integer',
            'stream_id'        => 'nullable|integer',
            'class_id'         => 'nullable|integer',
            'section_id'       => 'nullable|integer',
        ]);

        if (!$slot->timetable) {
            return response()->json([
                'message' => 'Invalid slot or timetable not found.',
            ], 404);
        }

        if ($slot->timetable->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        foreach (['academic_year_id', 'course_id', 'stream_id', 'class_id', 'section_id'] as $field) {
            if ($request->filled($field) && (int) $request->{$field} !== (int) $slot->timetable->{$field}) {
                return response()->json([
                    'message' => 'Selected period does not match the current filters. Please select the period again.',
                    'students' => [],
                    'existing' => [],
                ], 422);
            }
        }

        $students = StudentClassEnrollment::where('tenant_id', auth()->user()->tenant_id)
            ->where('course_id', $slot->timetable->course_id)
            ->where('stream_id', $slot->timetable->stream_id)
            ->where('class_id', $slot->timetable->class_id)
            ->where('section_id', $slot->timetable->section_id)
            ->where('academic_year_id', $slot->timetable->academic_year_id)
            ->where('status', 'active')
            ->whereHas('student', fn($query) => $query->where('tenant_id', auth()->user()->tenant_id)->where('status', 'active'))
            ->with(['student'])
            ->get()
            ->sortBy(fn($enrollment) => str_pad((string) ($enrollment->student->roll_number ?? ''), 20, '0', STR_PAD_LEFT))
            ->values()
            ->map(function ($enrollment) {
                return [
                    'id'            => $enrollment->id,
                    'enrollment_id' => $enrollment->id,
                    'roll_number'   => $enrollment->student->roll_number,
                    'first_name'    => $enrollment->student->first_name,
                    'last_name'     => $enrollment->student->last_name,
                ];
            });

        $existing = StudentAttendance::where('tenant_id', auth()->user()->tenant_id)
            ->where('period_id', $slot->period_id)
            ->where('date', $request->date)
            ->whereIn('enrollment_id', $students->pluck('id'))
            ->get(['enrollment_id', 'status', 'remarks'])
            ->mapWithKeys(fn($attendance) => [
                $attendance->enrollment_id => [
                    'status' => $attendance->status,
                    'remarks' => $attendance->remarks,
                ],
            ])
            ->toArray();

        return response()->json([
            'students' => $students,
            'existing' => $existing,
        ]);
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
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
            ->where('tenant_id', auth()->user()->tenant_id)
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

        $alreadyMarked = StudentAttendance::where('tenant_id', auth()->user()->tenant_id)
            ->where('period_id', $slot->period_id)
            ->where('date', $request->date)
            ->exists();

        if ($alreadyMarked) {
            return response()->json([
                'message' => 'Attendance is already saved for this regular class period and cannot be edited.',
            ], 422);
        }

        $validEnrollmentIds = StudentClassEnrollment::where('tenant_id', auth()->user()->tenant_id)
            // ->where('academic_year_id', $slot->timetable->academic_year_id)
            ->where('course_id', $slot->timetable->course_id)
            ->where('stream_id', $slot->timetable->stream_id)
            ->where('class_id', $slot->timetable->class_id)
            ->where('section_id', $slot->timetable->section_id)
            ->where('status', 'active')
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->all();

        $submittedEnrollmentIds = array_map('strval', array_keys($request->attendance));

        if (array_diff($submittedEnrollmentIds, $validEnrollmentIds)) {
            return response()->json([
                'message' => 'Attendance contains students outside the selected course, branch, semester or section. Please reload the student list.',
            ], 422);
        }

        foreach ($request->attendance as $enrollmentId => $data) {
            StudentAttendance::updateOrCreate(
                [
                    'tenant_id'      => auth()->user()->tenant_id,
                    'enrollment_id'  => $enrollmentId,
                    'period_id'      => $slot->period_id,
                    'date'           => $request->date,
                ],
                [
                    'status'         => $data['status'] ?? 'absent',
                    'remarks'        => $data['remarks'] ?? null,
                    'marked_by_user_id' => auth()->user()->id,
                ]
            );
        }

        return response()->json([
            'message' => 'Attendance marked successfully!',
        ]);
    }

    // Excel Export
    public function exportExcel(Request $request)
    {
        $query = $this->buildAttendanceQuery($request);
        $attendances = $query->get();
        return Excel::download(new AttendanceExport($attendances), 'attendance-report-' . $request->date . '.xlsx');
    }

    // PDF Export
    public function exportPdf(Request $request)
    {
        $query = $this->buildAttendanceQuery($request);
        $attendances = $query->get();
        $stats = $query->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
        $pdf = Pdf::loadView('Academics.student_attendance.pdf_export', compact('attendances', 'stats', 'request'));
        return $pdf->download('attendance-report-' . $request->date . '.pdf');
    }

    private function buildAttendanceQuery(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        $query = StudentAttendance::where('tenant_id', $tenantId)
            ->where('date', $request->filled('date') ? $request->date : now()->toDateString());

        if ($request->filled('academic_year_id')) {
            $query->whereHas('enrollment', fn($q) => $q->where('academic_year_id', $request->academic_year_id));
        }
        if ($request->filled('course_id')) {
            $query->whereHas('enrollment', fn($q) => $q->where('course_id', $request->course_id));
        }
        if ($request->filled('stream_id')) {
            $query->whereHas('enrollment', fn($q) => $q->where('stream_id', $request->stream_id));
        }
        if ($request->filled('class_id')) {
            $query->whereHas('enrollment', fn($q) => $q->where('class_id', $request->class_id));
        }
        if ($request->filled('section_id')) {
            $query->whereHas('enrollment', fn($q) => $q->where('section_id', $request->section_id));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('enrollment.student', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('roll_number', 'like', "%{$search}%");
            });
        }

        return $query->with(['enrollment.student', 'enrollment.class', 'enrollment.section', 'period', 'markedBy']);
    }

    public function markDirect(TimetableSlot $slot, $date)
    {
        $this->authorizeTenantForSlot($slot);

        $date = Carbon::parse($date);
        
        // Fetch students
        $students = StudentClassEnrollment::where('tenant_id', auth()->user()->tenant_id)
            ->where('course_id', $slot->timetable->course_id)
            ->where('stream_id', $slot->timetable->stream_id)
            ->where('class_id', $slot->timetable->class_id)
            ->where('section_id', $slot->timetable->section_id)
            // ->where('academic_year_id', $slot->timetable->academic_year_id)
            ->where('status', 'active')
            ->whereHas('student', fn($query) => $query->where('tenant_id', auth()->user()->tenant_id)->where('status', 'active'))
            ->with(['student'])
            ->get()
            ->sortBy(fn($enrollment) => str_pad((string) ($enrollment->student->roll_number ?? ''), 20, '0', STR_PAD_LEFT))
            ->values();
// dd($students);
        // Fetch existing attendance
        $existing = StudentAttendance::where('tenant_id', auth()->user()->tenant_id)
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

        $isLocked = ! empty($existing);

        return view('Academics.student_attendance.direct_mark', compact('slot', 'date', 'students', 'existing', 'isLocked'));
    }

    public function markMergedDirect(MergedClassSchedule $schedule, $date)
    {
        $this->authorizeTenantForMergedSchedule($schedule);

        $date = Carbon::parse($date);
        $students = $this->getMergedScheduleStudents($schedule);

        $existing = StudentAttendance::where('tenant_id', auth()->user()->tenant_id)
            ->where('period_id', $schedule->period_id)
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

        $isLocked = ! empty($existing);

        return view('Academics.student_attendance.direct_mark', compact('schedule', 'date', 'students', 'existing', 'isLocked'));
    }

    public function storeMerged(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'schedule_id' => 'required|exists:merged_class_schedules,id',
            'attendance' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid data.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $schedule = MergedClassSchedule::where('id', $request->schedule_id)
            ->where('tenant_id', auth()->user()->tenant_id)
            ->first();

        if (! $schedule) {
            return response()->json([
                'message' => 'Invalid merged schedule or you are not allowed to mark attendance for it.',
            ], 403);
        }

        $alreadyMarked = StudentAttendance::where('tenant_id', auth()->user()->tenant_id)
            ->where('period_id', $schedule->period_id)
            ->where('date', $request->date)
            ->exists();

        if ($alreadyMarked) {
            return response()->json([
                'message' => 'Attendance is already saved for this merged class period and cannot be edited.',
            ], 422);
        }

        $validEnrollmentIds = $this->getMergedScheduleStudents($schedule)
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->all();

        $submittedEnrollmentIds = array_map('strval', array_keys($request->attendance));

        if (array_diff($submittedEnrollmentIds, $validEnrollmentIds)) {
            return response()->json([
                'message' => 'Attendance contains students outside the selected merged class combinations. Please reload the student list.',
            ], 422);
        }

        foreach ($request->attendance as $enrollmentId => $data) {
            StudentAttendance::updateOrCreate(
                [
                    'tenant_id' => auth()->user()->tenant_id,
                    'enrollment_id' => $enrollmentId,
                    'period_id' => $schedule->period_id,
                    'date' => $request->date,
                ],
                [
                    'status' => $data['status'] ?? 'absent',
                    'remarks' => $data['remarks'] ?? null,
                    'marked_by_user_id' => auth()->user()->id,
                ]
            );
        }

        return response()->json([
            'message' => 'Attendance marked successfully!',
        ]);
    }

    protected function authorizeTenantForSlot(TimetableSlot $slot)
    {
        if ($slot->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }
    }

    protected function authorizeTenantForMergedSchedule(MergedClassSchedule $schedule)
    {
        if ($schedule->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }
    }

    protected function getMergedScheduleStudents(MergedClassSchedule $schedule)
    {
        $tenantId = auth()->user()->tenant_id;
        $combinations = collect($schedule->class_combinations ?? [])
            ->filter(function ($combo) {
                return is_array($combo)
                    && ! empty($combo['course_id'])
                    && ! empty($combo['stream_id'])
                    && ! empty($combo['class_id'])
                    && ! empty($combo['section_id']);
            })
            ->values();

        if ($combinations->isEmpty()) {
            return collect();
        }

        $buildQuery = function (?int $academicYearId = null) use ($tenantId, $combinations) {
            return StudentClassEnrollment::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->when($academicYearId, fn($query) => $query->where('academic_year_id', $academicYearId))
            ->whereHas('student', fn($query) => $query->where('tenant_id', $tenantId)->where('status', 'active'))
            ->where(function ($query) use ($combinations) {
                foreach ($combinations as $combo) {
                    $query->orWhere(function ($subQuery) use ($combo) {
                        $subQuery->where('course_id', $combo['course_id'])
                            ->where('stream_id', $combo['stream_id'])
                            ->where('class_id', $combo['class_id'])
                            ->where('section_id', $combo['section_id']);
                    });
                }
            })
            ->with(['student', 'course', 'stream', 'class', 'section']);
        };

        $students = $buildQuery($schedule->academic_year_id)->get();

        // Fallback: if no students are found for the merged schedule's academic year,
        // retry without the academic year filter so attendance still works for valid active enrollments.
        if ($students->isEmpty()) {
            $students = $buildQuery(null)->get();
        }

        return $students
            ->sortBy([
                fn($enrollment) => $enrollment->class?->name ?? '',
                fn($enrollment) => $enrollment->section?->name ?? '',
                fn($enrollment) => str_pad((string) ($enrollment->student->roll_number ?? ''), 20, '0', STR_PAD_LEFT),
            ])
            ->values();
    }

    protected function resolveClassStreamMapping($courseId, $streamId, $classId): ?ClassStreamMapping
    {
        $tenantId = auth()->user()->tenant_id;

        $courseStream = CourseStream::where('tenant_id', $tenantId)
            ->where('course_id', $courseId)
            ->where('stream_id', $streamId)
            ->first();

        if (! $courseStream) {
            return null;
        }

        return ClassStreamMapping::where('tenant_id', $tenantId)
            ->where('course_stream_id', $courseStream->id)
            ->where('class_id', $classId)
            ->first();
    }

    protected function authorizeTenant(StudentAttendance $attendance)
    {
        if ($attendance->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }
    }
}
