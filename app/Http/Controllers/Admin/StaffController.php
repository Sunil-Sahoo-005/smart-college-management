<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendancePeriod;
use App\Models\Classe;
use App\Models\Staff;
use App\Models\Designation;
use App\Models\Department;
use App\Models\MergedClassSchedule;
use App\Models\Permission;
use App\Models\Section;
use App\Models\Stream;
use App\Models\StudentAttendance;
use App\Models\StudentClassEnrollment;
use App\Models\Timetable;
use App\Models\TeacherAttendance;
use App\Models\TimetableSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class StaffController extends Controller
{
    protected $staffListRoute = 'admin.staff.index';

   public function index()
{
    $staff = Staff::where('tenant_id', auth()->user()->tenant_id)
        ->with(['designation', 'department'])
        ->orderBy('first_name')
        ->paginate(10);
        // ->get();

    $departments  = Department::where('tenant_id', auth()->user()->tenant_id)->orderBy('name')->get();
    $designations = Designation::where('tenant_id', auth()->user()->tenant_id)->orderBy('name')->get();

    $staffJson = $staff->map(function ($s) {
        return [
            'id'               => $s->id,
            'full_name'        => $s->first_name . ' ' . $s->last_name,
            'first_name'       => $s->first_name,
            'last_name'        => $s->last_name,
            'designation_name' => $s->designation?->name ?? 'N/A',
            'designation_id'   => $s->designation_id,
            'department_name'  => $s->department?->name ?? 'N/A',
            'department_id'    => $s->department_id,
            'phone'            => $s->phone,
            'status'           => $s->status,
            'photo_url'        => $s->photo_url ? asset('storage/' . $s->photo_url) : null,
            'show_url'         => route('admin.staff.show', $s),
            'edit_url'         => route('admin.staff.edit', $s),
            'delete_url'       => route('admin.staff.destroy', $s),
        ];
    });

    return view('Academics.staff.index', compact('staff', 'staffJson', 'departments', 'designations'));
}
    public function create()
    {
        $staff = new Staff; // For form binding
        $designations = Designation::where('tenant_id', auth()->user()->tenant_id)->orderBy('name')->get();
        $departments  = Department::where('tenant_id', auth()->user()->tenant_id)->orderBy('name')->get();

        return view('Academics.staff.form', compact('designations', 'departments', 'staff'));
    }

    public function edit(Staff $staff)
    {
        $this->authorizeTenant($staff);

        $designations = Designation::where('tenant_id', auth()->user()->tenant_id)->orderBy('name')->get();
        $departments  = Department::where('tenant_id', auth()->user()->tenant_id)->orderBy('name')->get();

        return view('Academics.staff.form', compact('staff', 'designations', 'departments'));
    }

    public function store(Request $request)

    {
        // dd($request->all());
        $rules = [
            'first_name'       => 'required|min:1|max:50',
            'last_name'        => 'required|min:1|max:50',
            'email'            => 'nullable|email|max:100|unique:staff,email,' . $request->get('id') . ',id,tenant_id,' . auth()->user()->tenant_id,
            'phone'            => 'required|min:10|max:15|unique:staff,phone,' . $request->get('id') . ',id,tenant_id,' . auth()->user()->tenant_id,
            'gender'           => 'required|in:male,female,other',
            'dob'              => 'required|date',
            'blood_group'      => 'required|max:5',
            'religion'         => 'required|max:50',
            'nationality'      => 'required|max:50',
            'designation_id'   => 'required|exists:designations,id',
            'department_id'    => 'required|exists:departments,id',
            'joining_date'     => 'required|date',
            'resignation_date' => 'nullable|date|after_or_equal:joining_date',
            'status'           => 'required|in:active,resigned,retired,on_leave',
            'pan'              => 'required|max:10',
            'aadhaar'          => 'required|max:12',
            'bank_account_name' => 'required|max:100',
            'bank_account_number' => 'required|max:50',
            'ifsc'             => 'required|max:11',
            // 'photo_url'        => 'nullable|max:255',
            'staff_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'alternate_phone'   => 'nullable|max:15',
            'marital_status'    => 'required|in:Single,Married,Divorced,Widowed',
            'permanent_address' => 'required|max:500',
            'address'           => 'required|max:500',
            'employee_id'       => 'required|max:50',
            'bput_id'           => 'required|max:50',
            'employment_type'   => 'required|in:Full-Time,Part-Time,Contract,Guest,Visiting',
            'leaving_date'      => 'nullable|date',
            'experience'        => 'required|integer|min:0|max:40',
            'qualification'     => 'required|max:50',
            'category'          => 'required|in:General,OBC,SC,ST,EWS',
            'pf_number'         => 'required|max:50',
            'bank_name'         => 'required|max:100',
            'branch_name'       => 'required|max:255',
            'account_type'      => 'required|in:Savings,Current',
        ];

        $messages = [
            'first_name.required' => 'Please enter first name!',
            'first_name.min'      => 'First name should be minimum of 1 character!',
            'first_name.max'      => 'First name should be maximum of 50 characters!',
            'last_name.required'  => 'Please enter last name!',
            'last_name.min'       => 'Last name should be minimum of 1 character!',
            'last_name.max'       => 'Last name should be maximum of 50 characters!',
            'email.email'         => 'Please enter a valid email address!',
            'email.max'           => 'Email should be maximum of 100 characters!',
            'email.unique'        => 'Email already exists!',
            'phone.required'      => 'Please enter phone number!',
            'phone.min'           => 'Phone number should be minimum of 10 characters!',
            'phone.max'           => 'Phone number should be maximum of 15 characters!',
            'phone.unique'        => 'Phone number already exists!',
            'gender.in'           => 'Invalid gender selected!',
            'dob.date'            => 'Date of birth must be a valid date!',
            'blood_group.max'     => 'Blood group should be maximum of 5 characters!',
            'religion.max'        => 'Religion should be maximum of 50 characters!',
            'nationality.max'     => 'Nationality should be maximum of 50 characters!',
            'designation_id.required' => 'Please select designation!',
            'designation_id.exists'   => 'Invalid designation selected!',
            'department_id.required'  => 'Please select department!',
            'department_id.exists'    => 'Invalid department selected!',
            'joining_date.required'   => 'Please enter joining date!',
            'joining_date.date'       => 'Joining date must be a valid date!',
            'resignation_date.date'   => 'Resignation date must be a valid date!',
            'resignation_date.after_or_equal' => 'Resignation date must be after or equal to joining date!',
            'status.required'         => 'Please select status!',
            'status.in'               => 'Invalid status selected!',
            'pan.max'                 => 'PAN should be maximum of 10 characters!',
            'aadhaar.max'             => 'Aadhaar should be maximum of 12 characters!',
            'bank_account_name.max'   => 'Bank account name should be maximum of 100 characters!',
            'bank_account_name.required'   => 'Please enter account holder name!',
            'bank_account_number.required' => 'Please enter account number!',
            'ifsc.required'                => 'Please enter IFSC code!',
            'bank_name.required'           => 'Please select bank name!',
            'bank_account_number.max' => 'Bank account number should be maximum of 50 characters!',
            'ifsc.max'                => 'IFSC should be maximum of 11 characters!',
            // 'photo_url.max'           => 'Photo URL should be maximum of 255 characters!',
            'staff_photo.image'       => 'Photo must be an image.',
            'staff_photo.mimes'       => 'Photo must be JPEG, PNG, JPG, or GIF.',
            'staff_photo.max'         => 'Photo size must not exceed 2MB.',
            'alternate_phone.max'       => 'Alternate phone should be maximum of 15 characters!',
            'marital_status.in'         => 'Invalid marital status selected!',
            'permanent_address.max'     => 'Permanent address should be maximum of 500 characters!',
            'address.max'               => 'Address should be maximum of 500 characters!',
            'employee_id.max'           => 'Employee ID should be maximum of 50 characters!',
            'bput_id.max'               => 'BPUT ID should be maximum of 50 characters!',
            'employment_type.in'        => 'Invalid employment type selected!',
            'leaving_date.date'         => 'Leaving date must be a valid date!',
            'experience.integer'        => 'Experience must be a number!',
            'experience.min'            => 'Experience cannot be negative!',
            'experience.max'            => 'Experience cannot be more than 40 years!',
            'qualification.max'         => 'Qualification should be maximum of 50 characters!',
            'category.in'               => 'Invalid category selected!',
            'pf_number.max'             => 'PF Number should be maximum of 50 characters!',
            'bank_name.max'             => 'Bank name should be maximum of 100 characters!',
            'branch_name.max'           => 'Branch name should be maximum of 255 characters!',
            'account_type.in'           => 'Invalid account type selected!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
        return redirect()->back()
        ->withErrors($validator)
        ->withInput()
        ->with('validation_failed', true);
}

        if (!empty($request->get('id'))) {
            $staff = Staff::where('id', $request->get('id'))
                ->where('tenant_id', auth()->user()->tenant_id)
                ->first();

            if (empty($staff)) {
                abort(404);
            }

            $redirect = [
                'url' => redirect()->back(),
                'msg' => 'Staff updated successfully',
            ];
        } else {
            $staff = new Staff;
            $staff->tenant_id = auth()->user()->tenant_id;

            $redirect = [
                'url' => redirect()->route($this->staffListRoute),
                'msg' => 'Staff created successfully',
            ];
        }

        $staff->first_name          = trim($request->get('first_name'));
        $staff->last_name           = trim($request->get('last_name'));
        $staff->email               = $request->get('email');
        $staff->phone               = $request->get('phone');
        $staff->gender              = $request->get('gender');
        $staff->dob                 = $request->get('dob');
        $staff->blood_group         = $request->get('blood_group');
        $staff->religion            = $request->get('religion');
        $staff->nationality         = $request->get('nationality');
        $staff->designation_id      = $request->get('designation_id');
        $staff->department_id       = $request->get('department_id');
        $staff->joining_date        = $request->get('joining_date');
        $staff->resignation_date    = $request->get('resignation_date');
        $staff->status              = $request->get('status');
        $staff->pan                 = $request->get('pan');
        $staff->aadhaar             = $request->get('aadhaar');
        $staff->bank_account_name   = $request->get('bank_account_name');
        $staff->bank_account_number = $request->get('bank_account_number');
        $staff->ifsc                = $request->get('ifsc');
        // Existing code ke baad, $staff->ifsc ke neeche add karo:
        $staff->alternate_phone     = $request->get('alternate_phone');
        $staff->marital_status      = $request->get('marital_status');
        $staff->permanent_address   = $request->get('permanent_address');
        $staff->address             = $request->get('address');
        $staff->employee_id         = $request->get('employee_id');
        $staff->bput_id             = $request->get('bput_id');
        $staff->employment_type     = $request->get('employment_type');
        $staff->leaving_date        = $request->get('leaving_date');
        $staff->experience          = $request->get('experience');
        $staff->qualification       = $request->get('qualification');
        $staff->category            = $request->get('category');
        $staff->pf_number           = $request->get('pf_number');
        $staff->bank_name           = $request->get('bank_name');
        $staff->branch_name         = $request->get('branch_name');
        $staff->account_type        = $request->get('account_type');
        // $staff->staff_photo         = $request->get('staff_photo'); // Assuming you handle file upload and set the path here
        if ($request->hasFile('staff_photo')) {

            if (!empty($request->get('id')) && $staff->photo_url) {
                $oldPath = public_path('storage/' . $staff->photo_url);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $file = $request->file('staff_photo');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('staff/photos', $filename, 'public');

            $staff->photo_url = $path; // ✅ SAME AS MODEL
        }


       try {
        // dd($request->file('documents'));
    $staff->save();

    // ✅ Documents handle karo
    if ($request->file('documents')) {

    // dd($request->file('documents'));
        foreach ($request->file('documents') as $index => $doc) {
            if (isset($doc['file']) && $doc['file']->isValid()) {
                $file     = $doc['file'];
                $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $path     = $file->storeAs('staff/documents', $filename, 'public');

                \App\Models\StaffDocument::create([
                    'staff_id'            => $staff->id,
                    'doc_type'            => $request->input("documents.{$index}.type"),
                    'file_path'           => $path,
                    'note'                => $request->input("documents.{$index}.note"),
                    'uploaded_by_user_id' => auth()->id(),
                ]);
            }
        }
        }
        // dd();

    return $redirect['url']->with('success', $redirect['msg']);

}catch (\Exception $e) {
    return redirect()->back()
        ->withInput()
        ->with('error', 'Something went wrong: ' . $e->getMessage());
}
    }
    public function show(Staff $staff)
    { 
        $this->authorizeTenant($staff);

        $staff->load([
            'designation',
            'department',
            'tenant',
            'scheduleDocuments',
            'documents',  

        ]);

        // 1. Regular Timetable Slots - Unified Grid
        $regularSlots = TimetableSlot::where('tenant_id', $staff->tenant_id)
            ->where('staff_id', $staff->id)
            ->with(['timetable.class', 'timetable.section', 'timetable.stream', 'period', 'subject', 'room'])
            ->get();

        // Group regular slots: period_id -> day_of_week -> slots (could be multiple if overlapping)
        $unifiedGrid = $regularSlots->groupBy('period_id')->map(function ($periodGroup) {
            return $periodGroup->groupBy('day_of_week');
        });

        // Get all unique periods involved in regular schedule
        $regularPeriods = $regularSlots->pluck('period')->unique('id')->sortBy('id');

        // 2. Merged/Special Schedules (Date specific)
        $startOfWeek = now()->startOfWeek(Carbon::MONDAY);
        $endOfWeek   = now()->endOfWeek(Carbon::SATURDAY);

        $mergedSchedules = MergedClassSchedule::where('tenant_id', $staff->tenant_id)
            ->where('staff_id', $staff->id)
            ->whereBetween('schedule_date', [$startOfWeek, $endOfWeek])
            ->with(['subject', 'room', 'period'])
            ->get();

        $mergedByPeriodAndDay = $mergedSchedules->groupBy('period_id')->map(function ($periodGroup) {
            return $periodGroup->keyBy(fn($s) => Carbon::parse($s->schedule_date)->dayOfWeekIso);
        });

        $mergedPeriods = $mergedSchedules->pluck('period')->unique('id')->sortBy('id');

        // 3. Attendance & Other Data
        $attendanceRecords = TeacherAttendance::where('staff_id', $staff->id)
            ->where('tenant_id', $staff->tenant_id)
            ->with(['period'])
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();
        $bookIssues = \App\Models\BookIssues::with('book')
        ->where('staff_id', $staff->id)
        ->orderByDesc('issue_date')
        ->get();
$practicalSlots = \App\Models\PracticalTimetableSlot::where('tenant_id', $staff->tenant_id)
    ->where('staff_id', $staff->id)
    ->with([
        'timetable.practicalGroup.groupMaster',
        'timetable.practicalGroup.course',
        'timetable.practicalGroup.stream',
        'period',
        'room',
        'subject',
    ])
    ->get()
    ->sortBy([['day_of_week', 'asc'], ['attendance_period_id', 'asc']])
    ->values();

$practicalByDay = $practicalSlots->groupBy('day_of_week');
        return view('Academics.staff.show', compact(
            'staff', 
            'unifiedGrid', 
            'regularPeriods', 
            'mergedByPeriodAndDay', 
            'mergedPeriods', 
            'mergedSchedules', 
            'startOfWeek', 
            'endOfWeek', 
            'bookIssues',
            'attendanceRecords',
            'practicalSlots',
            'practicalByDay'
        ));
    }
    public function showClassesForTimetable(Staff $staff)
    {
        $this->authorizeTenant($staff);

        $classGroups = collect();

        $schedules = TimetableSlot::where('tenant_id', $staff->tenant_id)
            ->where('staff_id', $staff->id)
            ->with(['timetable.class', 'timetable.section', 'timetable.stream'])
            ->get();

        foreach ($schedules as $slot) {
            $key = $slot->timetable->class_id . '_' .
                ($slot->timetable->section_id ?? 0) . '_' .
                ($slot->timetable->stream_id ?? 0);

            if (!$classGroups->has($key)) {
                $classGroups->put($key, [
                    'class_id' => $slot->timetable->class_id,
                    'stream_id' => $slot->timetable->stream_id,
                    'section_id' => $slot->timetable->section_id,
                    'class' => $slot->timetable->class,
                    'stream' => $slot->timetable->stream,
                    'section' => $slot->timetable->section,
                    'slot_count' => 0
                ]);
            }
        }

        // 🔥 FIXED: Correct slot counting
        $classGroups = $classGroups->map(function ($group) use ($schedules) {
            $group['slot_count'] = $schedules->filter(function ($slot) use ($group) {
                return $slot->timetable->class_id == $group['class_id'] &&
                    ($slot->timetable->section_id ?? 0) == ($group['section_id'] ?? 0) &&
                    ($slot->timetable->stream_id ?? 0) == ($group['stream_id'] ?? 0);
            })->count();
            return $group;
        });

        return view('Academics.staff.classes_list', compact('staff', 'classGroups'));
    }


    // public function showClassesForTimetable(Staff $staff)
    // {
    //     $this->authorizeTenant($staff);

    //     $classGroups = collect();

    //     $schedules = TimetableSlot::where('tenant_id', $staff->tenant_id)
    //         ->where('staff_id', $staff->id)
    //         ->with(['timetable.class', 'timetable.section', 'timetable.stream'])
    //         ->get();

    //     foreach ($schedules as $slot) {
    //         $key = $slot->timetable->class_id . '_' . ($slot->timetable->section_id ?? 0) . '_' . ($slot->timetable->stream_id ?? 0);
    //         if (!$classGroups->has($key)) {
    //             $classGroups->put($key, [
    //                 'class_id' => $slot->timetable->class_id,
    //                 'stream_id' => $slot->timetable->stream_id,
    //                 'section_id' => $slot->timetable->section_id,
    //                 'class' => $slot->timetable->class,
    //                 'stream' => $slot->timetable->stream,
    //                 'section' => $slot->timetable->section,
    //                 'slot_count' => 0
    //             ]);
    //         }
    //     }

    //     // Count slots per class/section
    //     $classGroups = $classGroups->map(function ($group) use ($schedules) {
    //         $group['slot_count'] = $schedules->where('timetable.class_id', $group['class_id'])
    //             ->when($group['section_id'], fn($q) => $q->where('timetable.section_id', $group['section_id']))
    //             ->count();
    //         return $group;
    //     });

    //     return view('Academics.staff.classes_list', compact('staff', 'classGroups'));
    // }
    // public function showClassesForTimetable(Staff $staff)
    // {
    //     $this->authorizeTenant($staff);

    //     $schedules = TimetableSlot::where('tenant_id', $staff->tenant_id)
    //         ->where('staff_id', $staff->id)
    //         ->with(['timetable.class', 'timetable.section', 'timetable.stream'])
    //         ->get();

    //     $classGroups = collect();

    //     foreach ($schedules as $slot) {
    //         $key = $slot->timetable->class_id . '_' . 
    //                ($slot->timetable->stream_id ?? 0) . '_' . 
    //                ($slot->timetable->section_id ?? 0);

    //         if (!$classGroups->has($key)) {
    //             $classGroups->put($key, [
    //                 'class_id' => $slot->timetable->class_id,
    //                 'stream_id' => $slot->timetable->stream_id,
    //                 'section_id' => $slot->timetable->section_id,
    //                 'class' => $slot->timetable->class,
    //                 'stream' => $slot->timetable->stream,
    //                 'section' => $slot->timetable->section,
    //                 'slot_count' => 0
    //             ]);
    //         }
    //     }

    //     $classGroups = $classGroups->values();

    //     return view('Academics.staff.classes_list', compact('staff', 'classGroups'));
    // }


    public function getStudentsForClass(Staff $staff, Request $request)
    {
        $classId = $request->class_id;
        $sectionId = $request->section_id;

        $enrollments = StudentClassEnrollment::where('tenant_id', $staff->tenant_id)
            ->where('class_id', $classId)
            ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
            ->where('status', 'active')
            ->with(['student', 'academicYear'])
            ->get();

        return response()->json([
            'students' => $enrollments->map(fn($e) => [
                'id' => $e->id,
                'student_id' => $e->student_id,
                'name' => $e->student->first_name . ' ' . $e->student->last_name,
                'roll_number' => $e->student->roll_number,
                'photo_url' => $e->student->photo_url
            ])
        ]);
    }

    // public function showAttendance(Staff $staff, $classId, $sectionId = null)
    // {
    //     $this->authorizeTenant($staff);

    //     $class = Classe::where('tenant_id', $staff->tenant_id)
    //         ->findOrFail($classId);

    //     $section = $sectionId ? Section::where('tenant_id', $staff->tenant_id)
    //         ->where('id', $sectionId)
    //         ->first() : null;

    //     // 🔥 PASS ENROLLMENTS as $students
    //     $students = StudentClassEnrollment::where('tenant_id', $staff->tenant_id)
    //         ->where('class_id', $classId)
    //         ->when($section, fn($q) => $q->where('section_id', $section->id))
    //         ->where('status', 'active')
    //         ->with(['student'])
    //         ->get()
    //         ->map(function($enrollment) {
    //             $enrollment->name = $enrollment->student->first_name . ' ' . $enrollment->student->last_name;
    //             return $enrollment;
    //         });

    //     // Default period (you can pass from timetable or make dropdown)
    //     $period = AttendancePeriod::where('tenant_id', $staff->tenant_id)->first();

    //     return view('Academics.staff.attendance', compact(
    //         'staff', 'class', 'section', 'students', 'period'  // ✅ $students not $enrollments
    //     ));
    // }
    public function showAttendance(Staff $staff, $classId, $streamId = null, $sectionId = null)
    {
        $this->authorizeTenant($staff);

        $class = Classe::where('tenant_id', $staff->tenant_id)->findOrFail($classId);

        $stream = $streamId && $streamId != 0 ? Stream::where('tenant_id', $staff->tenant_id)
            ->where('id', $streamId)->first() : null;

        $section = $sectionId && $sectionId != 0 ? Section::where('tenant_id', $staff->tenant_id)
            ->where('id', $sectionId)->first() : null;

        // 🔥 FIXED: Simple query - students WILL show
        $studentsQuery = StudentClassEnrollment::where('tenant_id', $staff->tenant_id)
            ->where('class_id', $classId)
            ->where('status', 'active');

        // Only apply section filter if valid
        if ($sectionId && $sectionId != 0) {
            $studentsQuery->where('section_id', $sectionId);
        }

        // 🔥 NO STREAM FILTER - students show immediately
        $students = $studentsQuery->with(['student'])
            ->orderBy('id') // ✅ FIXED: No more SQL error
            ->get()
            ->map(function ($enrollment) {
                $enrollment->name = $enrollment->student->first_name . ' ' . $enrollment->student->last_name;
                return $enrollment;
            });

        $period = AttendancePeriod::where('tenant_id', $staff->tenant_id)->first();

        return view('Academics.staff.attendance', compact(
            'staff',
            'class',
            'stream',
            'section',
            'students',
            'period'
        ));
    }



    // public function storeAttendance(Staff $staff, $classId, $sectionId = null, Request $request)
    // {
    //     $this->authorizeTenant($staff);

    //     $request->validate([
    //         'attendance' => 'required|array|min:1',
    //         'attendance.*.enrollment_id' => 'required|exists:student_class_enrollments,id',
    //         'attendance.*.status' => 'required|in:present,absent,late,half_day',
    //         'attendance.*.student_id' => 'exists:students,id'
    //     ]);

    //     $today = now()->toDateString();
    //     $periodId = $request->period_id ?? 1; // Default period

    //     $savedCount = 0;

    //     foreach ($request->attendance as $data) {
    //         StudentAttendance::updateOrCreate(
    //             [
    //                 'tenant_id' => $staff->tenant_id,
    //                 'enrollment_id' => $data['enrollment_id'],
    //                 'date' => $today,
    //                 'period_id' => $periodId
    //             ],
    //             [
    //                 'status' => $data['status'],
    //                 'marked_by_user_id' => $staff->id,
    //                 // 'student_id' => $data['student_id'],
    //                 'remarks' => $data['remarks'] ?? null
    //             ]
    //         );
    //         $savedCount++;
    //     }

    //     return response()->json([
    //         'message' => "✅ Attendance saved for {$savedCount} students!",
    //         'saved_count' => $savedCount
    //     ]);
    // }
    public function storeAttendance(Staff $staff, $classId, $streamId = null, $sectionId = null, Request $request)
    {
        $this->authorizeTenant($staff);

        $request->validate([
            'attendance' => 'required|array|min:1',
            'attendance.*.enrollment_id' => 'required|exists:student_class_enrollments,id',
            'attendance.*.status' => 'required|in:present,absent,late,half_day',
            'attendance.*.student_id' => 'exists:students,id'
        ]);

        $today = now()->toDateString();
        $periodId = $request->period_id ?? 1;

        $savedCount = 0;
        $skippedCount = 0;

        foreach ($request->attendance as $data) {
            // 🔥 PREVENT DUPLICATES - Check if exists for today + period
            $exists = StudentAttendance::where('tenant_id', $staff->tenant_id)
                ->where('enrollment_id', $data['enrollment_id'])
                ->where('date', $today)
                ->where('period_id', $periodId)
                ->exists();

            if ($exists) {
                $skippedCount++;
                continue;
            }

            // 🔥 CREATE NEW (not updateOrCreate for strict duplicate prevention)
            StudentAttendance::create([
                'tenant_id' => $staff->tenant_id,
                'enrollment_id' => $data['enrollment_id'],
                'student_id' => $data['student_id'],
                'date' => $today,
                'period_id' => $periodId,
                'status' => $data['status'],
                'marked_by_user_id' => $staff->id,  // ✅ Your field name
                'remarks' => $data['remarks'] ?? null
            ]);

            $savedCount++;
        }

        return response()->json([
            'message' => "✅ Saved: {$savedCount}, Skipped (today): {$skippedCount}",
            'saved_count' => $savedCount,
            'skipped_count' => $skippedCount
        ]);
    }



    public function destroy(Staff $staff)
    {
        $this->authorizeTenant($staff);

        try {
            $staff->delete();
            return back()->with('success', 'Staff deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete staff, it may be in use.');
        }
    }

    protected function authorizeTenant(Staff $staff)
    {
        if ($staff->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }
    }
}