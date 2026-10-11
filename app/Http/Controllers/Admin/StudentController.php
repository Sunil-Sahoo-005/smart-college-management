<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hostel;
use App\Models\HostelAllocation;
use App\Models\Hostelbed;
use App\Models\HostelRoom;
use App\Models\Routes;
use App\Models\RouteStop;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentClassEnrollment;
use App\Models\StudentRouteMapping;
use App\Models\Tenant;
use App\Models\Timetable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\StudentRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use App\Imports\StudentsImport;
use App\Models\AcademicYear;
use App\Models\ClassStreamMapping;
use App\Models\Classe;
use App\Models\Course;
use App\Models\CourseStream;
use App\Models\FeeInstallment;
use App\Models\Section;
use App\Models\Stream;
use App\Models\StudentFeeStructure;
use App\Models\Promotion;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentsExport;
use App\Exports\StudentsTemplateExport;
use App\Models\ExamMark;
use App\Models\TimetableSlot;

class StudentController extends Controller
{
    protected $studentListRoute = 'admin.students.index';

    public function index(Request $request)
    {
        // EXPORT TO EXCEL (still server-side — needs full query)
        if ($request->has('export') && $request->export == 'excel') {
            $students = Student::with([
                'enrollments.course',
                'enrollments.stream',
                'enrollments.class',
                'enrollments.academicYear',
            ])
                ->where('tenant_id', auth()->user()->tenant_id)
                ->get();

            return Excel::download(
                new StudentsExport($students),
                'students_' . date('Y-m-d_His') . '.xlsx'
            );
        }

        // ── Load ALL students (no filters) ──────────────────────────────
        $students = Student::with([
            'enrollments.course',
            'enrollments.stream',
            'enrollments.class',
            'enrollments.academicYear',
        ])
            ->where('tenant_id', auth()->user()->tenant_id)
            ->latest()
            ->get();

        // ── Dropdown data ───────────────────────────────────────────────
        $courses = Course::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get(['id', 'name']);

        $streams = Stream::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get(['id', 'name']);

        $academicYears = AcademicYear::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name', 'desc')
            ->get(['id', 'name']);

        $classes = Classe::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get(['id', 'name']);

        $statuses       = ['active', 'discontinued', 'transfer_out', 'alumni'];
        $categories     = ['sc', 'st', 'obc', 'general', 'sebc'];
        $studentTypes   = ['regular', 'lateral'];
        $residenceTypes = ['day_scholar', 'hosteller'];

        // ── Build a flat JSON array for client-side filtering ───────────
        $studentsJson = $students->map(function ($student) {
            $activeEnrollment = $student->enrollments
                ->where('status', 'active')
                ->first();
            return [
                'id'               => $student->id,
                'admission_number' => $student->admission_number,
                'roll_number'      => $student->roll_number,
                'first_name'       => $student->first_name,
                'last_name'        => $student->last_name,
                'full_name'        => $student->first_name . ' ' . $student->last_name,
                'gender'           => $student->gender,
                'dob'              => $student->dob?->format('d M Y'),
                'blood_group'      => $student->blood_group,
                'status'           => $student->status,
                'category'         => $student->category,
                'photo_url'        => $student->photo_url
                    ? asset('storage/' . $student->photo_url)
                    : null,
                // Enrollment fields (from the active enrollment)
                'academic_year_id'   => $activeEnrollment?->academic_year_id,
                'academic_year_name' => $activeEnrollment?->academicYear?->name,
                'course_id'          => $activeEnrollment?->course_id,
                'course_name'        => $activeEnrollment?->course?->name,
                'stream_id'          => $activeEnrollment?->stream_id,
                'stream_name'        => $activeEnrollment?->stream?->name,
                'class_id'           => $activeEnrollment?->class_id,
                'class_name'         => $activeEnrollment?->class?->name,
                'student_type'       => $activeEnrollment?->student_type,
                'residence_type'     => $activeEnrollment?->residence_type,
                // URLs for actions
                'show_url'    => route('admin.students.show', $student),
                'edit_url'    => route('admin.students.edit', $student),
                'delete_url'  => route('admin.students.destroy', $student),
            ];
        });

        return view('Academics.student.index', compact(
            'students',        // still passed for @forelse fallback / total count
            'studentsJson',    // JSON for JS filtering
            'courses',
            'streams',
            'academicYears',
            'classes',
            'statuses',
            'categories',
            'studentTypes',
            'residenceTypes'
        ));
    }

    public function create()
    {
        $student    = new Student;
        $enrollment = new StudentClassEnrollment;

        $selectedCourseId = old('course_id');
        $selectedStreamId = old('stream_id');
        $selectedClassId = old('class_id');

        $academicYears          = AcademicYear::where('tenant_id', Auth::user()->tenant_id)->get();
        $courses                = Course::where('tenant_id', Auth::user()->tenant_id)->with(['streams' => function ($query) {
            $query->orderBy('name');
        }])->orderBy('name')->get();
        $streams                = $selectedCourseId ? $this->getCourseStreamsCollection((int) $selectedCourseId) : collect();
        $classes                = ($selectedCourseId && $selectedStreamId)
            ? $this->getCourseClassesCollection((int) $selectedCourseId, (int) $selectedStreamId)
            : collect();
        $sections               = ($selectedCourseId && $selectedStreamId && $selectedClassId)
            ? $this->getCourseSectionsCollection((int) $selectedCourseId, (int) $selectedStreamId, (int) $selectedClassId)
            : collect();
        $students               = Student::where('tenant_id', Auth::user()->tenant_id)->get();
        $hostels                = Hostel::where('tenant_id', Auth::user()->tenant_id)->get();
        $existingHostel         = null;
        $existingFeeStructureIds = [];

        return view('Academics.student.form', compact(
            'student',
            'enrollment',
            'academicYears',
            'courses',
            'streams',
            'classes',
            'sections',
            'students',
            'hostels',
            'existingHostel',
            'existingFeeStructureIds'
        ));
    }

    public function downloadTemplate()
    {
        $filePath = public_path('templates/students_import_template.xlsx');

        if (!file_exists($filePath)) {
            // dd();
            abort(404, 'Template file not found!');
        }

        return response()->download($filePath, 'students_import_template.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            Excel::import(new StudentsImport, $request->file('file'));
            return back()->with('success', 'Students imported successfully');
        } catch (\Exception $e) {
            return back()->withErrors('Import failed: ' . $e->getMessage());
        }
    }

    public function edit(Student $student)
    {
        $enrollment = StudentClassEnrollment::where('tenant_id', Auth::user()->tenant_id)
            ->where('student_id', $student->id)
            ->latest()
            ->first() ?? new StudentClassEnrollment;

        $selectedCourseId = old('course_id', $enrollment->course_id);
        $selectedStreamId = old('stream_id', $enrollment->stream_id);
        $selectedClassId = old('class_id', $enrollment->class_id);

        $academicYears = AcademicYear::where('tenant_id', Auth::user()->tenant_id)->get();
        $courses       = Course::where('tenant_id', Auth::user()->tenant_id)->with(['streams' => function ($query) {
            $query->orderBy('name');
        }])->orderBy('name')->get();
        $streams       = $selectedCourseId ? $this->getCourseStreamsCollection((int) $selectedCourseId) : collect();
        $classes       = ($selectedCourseId && $selectedStreamId)
            ? $this->getCourseClassesCollection((int) $selectedCourseId, (int) $selectedStreamId)
            : collect();
        $sections      = ($selectedCourseId && $selectedStreamId && $selectedClassId)
            ? $this->getCourseSectionsCollection((int) $selectedCourseId, (int) $selectedStreamId, (int) $selectedClassId)
            : collect();
        $students      = Student::where('tenant_id', Auth::user()->tenant_id)->get();
        $hostels       = Hostel::where('tenant_id', Auth::user()->tenant_id)->get();

        $existingHostel = HostelAllocation::with(['hostel', 'room', 'bed'])
            ->where('tenant_id', Auth::user()->tenant_id)
            ->where('student_id', $student->id)
            ->first();

        $existingFeeStructureIds = StudentFeeStructure::where('tenant_id', Auth::user()->tenant_id)
            ->where('student_id', $student->id)
            ->when(
                $enrollment->academic_year_id,
                fn($q) => $q->where('academic_year_id', $enrollment->academic_year_id)
            )
            ->pluck('fee_structure_id')
            ->toArray();

        return view('Academics.student.form', compact(
            'student',
            'enrollment',
            'academicYears',
            'courses',
            'streams',
            'classes',
            'sections',
            'students',
            'hostels',
            'existingHostel',
            'existingFeeStructureIds'
            
        ));
    }

    /**
     * Generate admission number in format:
     * SERC/course_code/branch_code/current_year/student_type/serial_no
     */
   private function generateAdmissionNumber(string $courseCode, string $branchCode, string $studentType): string
{
    $tenantId = auth()->user()->tenant_id;
    $year = date('Y');

    $typeCode = ($studentType === 'lateral') ? 'L' : 'F';

    $prefix = "SERC/{$courseCode}/{$branchCode}/{$year}/{$typeCode}/";

    $lastStudent = Student::where('tenant_id', $tenantId)
        ->where('admission_number', 'like', $prefix . '%')
        ->orderBy('id', 'desc')
        ->first();

    $nextSerial = 1;
    if ($lastStudent) {
        $parts = explode('/', $lastStudent->admission_number);
        $lastSerial = (int) end($parts);
        $nextSerial = $lastSerial + 1;
    }

    $serialPadded = str_pad($nextSerial, 6, '0', STR_PAD_LEFT);
    return $prefix . $serialPadded;
}

    public function checkPendingFees(Student $student)
    {
        $tenantId = auth()->user()->tenant_id;

        $installments = FeeInstallment::with('feeCategory')
            ->where('tenant_id', $tenantId)
            ->where('student_id', $student->id)
            ->whereIn('status', ['unpaid', 'partial'])
            ->get()
            ->map(function ($installment) {
                return [
                    'id' => $installment->id,
                    'amount' => $installment->amount,
                    'due_date' => $installment->due_date,
                    'status' => $installment->status,
                    'fee_category_name' => $installment->feeCategory->name
                ];
            });

        $summary = FeeInstallment::where('tenant_id', $tenantId)
            ->where('student_id', $student->id)
            ->selectRaw('
                COALESCE(SUM(amount), 0) as total_fees,
                COALESCE(SUM(CASE WHEN status = "paid" THEN amount ELSE 0 END), 0) as paid_amount,
                COALESCE(SUM(CASE WHEN status IN ("unpaid", "partial") THEN amount ELSE 0 END), 0) as pending_amount
            ')
            ->first();

        return response()->json([
            'has_pending' => $installments->count() > 0,
            'installments' => $installments,
            'summary' => [
                'total_fees' => number_format((float) $summary->total_fees, 2),
                'paid_amount' => number_format((float) $summary->paid_amount, 2),
                'pending_amount' => number_format((float) $summary->pending_amount, 2)
            ]
        ]);
    }


// Sirf preview() method ka code hai — apne StudentController mein replace karo


public function preview(Request $request)
{
    // ── 1. Validation pehle karo ──────────────────────────────────────────
    $studentId = $request->input('student_id_edit');
    $tenantId  = auth()->user()->tenant_id;

    $rules = [
            'roll_number'               => 'nullable|max:20',
            'first_name'                => 'required|min:1|max:50',
            'last_name'                 => 'required|min:1|max:50',
            'gender'                    => 'nullable|in:male,female,other',
            'dob'                       => 'nullable|date',
            'blood_group'               => 'nullable|max:5',
            'religion'                  => 'nullable|max:50',
            'nationality'               => 'nullable|max:50',
            'physically_handicapped'    => 'nullable|in:yes,no',
            'handicap_details'          => 'nullable|string|max:500',
            'aadhaar'                   => 'nullable|digits:12|unique:students,aadhaar,' . $studentId . ',id,tenant_id,' . $tenantId,
            'emergency_contact_name'    => 'nullable|max:100',
            'emergency_contact_phone'   => 'nullable|min:10|max:15',
            'medical_conditions'        => 'nullable|max:500',
            'allergies'                 => 'nullable|max:500',
            // Photo: preview mein already store ho gayi, ab sirf temp path validate
            '_temp_photo_path'          => 'nullable|string',
            'old_photo'                 => 'nullable|string',
            'student_status'            => 'required',
            'account_holder_name'       => 'nullable|string|max:100',
            'bank_name'                 => 'nullable|string|max:255',
            'branch_name'               => 'nullable|string|max:100',
            'account_type'              => 'nullable|in:savings,current',
            'account_number'            => 'nullable|digits_between:9,18',
            'ifsc_code'                 => ['nullable', 'max:11', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
            'category'                  => 'nullable|in:sc,st,obc,general,sebc,other',
            'sub_caste'                 => 'nullable|string|max:100',
            'transfer_out_reason'       => 'nullable|string',
            'email'                     => 'nullable|email|max:255|unique:students,email,' . $studentId . ',id,tenant_id,' . $tenantId,
            'whatsapp_number'           => 'nullable|min:10|max:15',
            // Enrollment
            'academic_year_id'          => 'required|exists:academic_years,id',
            'course_id'                 => 'required|exists:courses,id',
            'class_id'                  => 'required|exists:classes,id',
            'section_id'                => 'required|exists:sections,id',
            'stream_id'                 => 'required|exists:streams,id',
            'student_type'              => 'required|in:regular,lateral',
            'residence_type'            => 'nullable|in:day_scholar,hosteller',
            'hostel_id'                 => 'nullable|exists:hostels,id',
            'room_id'                   => 'nullable|exists:hostel_rooms,id',
            'bed_id'                    => 'nullable|exists:hostel_beds,id',
            'hostel_fee'                => 'nullable|numeric|min:0',
            'hostel_from_date'          => 'nullable|date',
            'hostel_to_date'            => 'nullable|date|after_or_equal:hostel_from_date',
            'fee_structure_ids'         => 'required|array|min:1',
            'fee_structure_ids.*'       => 'exists:fee_structures,id',
            'dateof_admission'          => 'nullable|date',
            'date_of_leaving'           => 'nullable|date|after_or_equal:dateof_admission',
            'enrollment_status'         => 'required|in:active,promoted,transferred,completed',

        // Unique checks — tenant scoped, edit mode mein current student ignore
        'aadhaar'         => [
            'nullable', 'digits:12',
            \Illuminate\Validation\Rule::unique('students', 'aadhaar')
                ->where('tenant_id', $tenantId)
                ->ignore($studentId),
        ],
        'email'           => [
            'nullable', 'email',
            \Illuminate\Validation\Rule::unique('students', 'email')
                ->where('tenant_id', $tenantId)
                ->ignore($studentId),
        ],
        'whatsapp_number' => [
            'nullable', 'digits_between:10,15',
            \Illuminate\Validation\Rule::unique('students', 'whatsapp_number')
                ->where('tenant_id', $tenantId)
                ->ignore($studentId),
        ],
        'roll_number'     => [
            'nullable', 'string', 'max:50',
            \Illuminate\Validation\Rule::unique('students', 'roll_number')
                ->where('tenant_id', $tenantId)
                ->ignore($studentId),
        ],
        'account_number'  => ['nullable', 'string', 'max:30'],
        'ifsc_code'        => ['nullable', 'string', 'max:11'],
        'student_photo'    => ['nullable', 'image', 'max:2048'],
    ];

    $messages = [
         'first_name.required'             => 'Please enter first name.',
            'last_name.required'              => 'Please enter last name.',
            'aadhaar.digits'                  => 'Aadhaar must be exactly 12 digits.',
            'aadhaar.unique'                  => 'This Aadhaar number is already registered.',
            'student_status.required'         => 'Please select a student status.',
            'academic_year_id.required'       => 'Please select an academic year.',
            'course_id.required'              => 'Please select a course.',
            'stream_id.required'              => 'Please select a branch.',
            'class_id.required'               => 'Please select a semester.',
            'section_id.required'             => 'Please select a section.',
            'student_type.required'           => 'Please select student type.',
            'hostel_id.required'              => 'Please select a hostel.',
            'room_id.required'                => 'Please select a room.',
            'bed_id.required'                 => 'Please select a bed.',
            'hostel_from_date.required'       => 'Please enter the hostel from date.',
            'fee_structure_ids.required'      => 'Please select at least one fee structure.',
            'fee_structure_ids.min'           => 'Please select at least one fee structure.',
            'enrollment_status.required'      => 'Please select an enrollment status.',
            'enrollment_status.in'            => 'Invalid enrollment status.',
            'date_of_leaving.after_or_equal'  => 'Date of leaving must be on or after the admission date.',
            'physically_handicapped.in'       => 'Invalid value for physically handicapped.',
            'email.email'                     => 'Please enter a valid email address.',
            'email.unique'                    => 'This email is already registered.',
            'account_number.digits_between'   => 'Account number must be 9–18 digits.',
            'ifsc_code.regex'                 => 'Invalid IFSC format (e.g. SBIN0001234).',
    ];

    $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    // ── 2. Data collect karo ─────────────────────────────────────────────
    $data = $request->all();

    // ── 3. Photo temp store ──────────────────────────────────────────────
    if ($request->hasFile('student_photo')) {
        $tempPath = $request->file('student_photo')->store('temp_photos', 'public');
        $data['_temp_photo_path'] = $tempPath;
    }

    if ($request->filled('old_photo')) {
        $data['old_photo'] = $request->old_photo;
    }

    // ── 4. Labels resolve karo ───────────────────────────────────────────
    $academicYear = \App\Models\AcademicYear::find($data['academic_year_id'] ?? null);
    $course       = \App\Models\Course::find($data['course_id'] ?? null);
    $stream       = \App\Models\Stream::find($data['stream_id'] ?? null);
    $class        = \App\Models\Classe::find($data['class_id'] ?? null);
    $section      = \App\Models\Section::find($data['section_id'] ?? null);

    $data['academic_year_id_label'] = $academicYear?->name ?? '';
    $data['course_id_label']        = $course?->name       ?? '';
    $data['stream_id_label']        = $stream?->name       ?? '';
    $data['class_id_label']         = $class?->name        ?? '';
    $data['section_id_label']       = $section?->name      ?? '';

    // ── 5. Sections config ────────────────────────────────────────────────
    $sectionsConfig = [
        'personal' => [
            'label' => 'Personal info',
            'icon'  => 'user',
            'fields' => [
                ['key' => 'first_name',             'label' => 'First name',             'required' => true],
                ['key' => 'last_name',              'label' => 'Last name',              'required' => true],
                ['key' => 'gender',                 'label' => 'Gender',                 'required' => true],
                ['key' => 'dob',                    'label' => 'Date of birth',          'required' => false],
                ['key' => 'blood_group',            'label' => 'Blood group',            'required' => false],
                ['key' => 'category',               'label' => 'Caste / category',       'required' => false],
                ['key' => 'religion',               'label' => 'Religion',               'required' => false],
                ['key' => 'nationality',            'label' => 'Nationality',            'required' => false],
                ['key' => 'aadhaar',                'label' => 'Aadhaar',                'required' => false],
                ['key' => 'student_status',         'label' => 'Student status',         'required' => true],
                ['key' => 'physically_handicapped', 'label' => 'Physically handicapped', 'required' => false],
            ],
        ],
        'contact' => [
            'label' => 'Contact & emergency',
            'icon'  => 'phone',
            'fields' => [
                ['key' => 'email',                   'label' => 'Email',                   'required' => false],
                ['key' => 'whatsapp_number',         'label' => 'WhatsApp number',         'required' => false],
                ['key' => 'emergency_contact_name',  'label' => 'Emergency contact name',  'required' => false],
                ['key' => 'emergency_contact_phone', 'label' => 'Emergency phone',         'required' => false],
            ],
        ],
        'medical' => [
            'label' => 'Medical',
            'icon'  => 'heart',
            'fields' => [
                ['key' => 'medical_conditions', 'label' => 'Medical conditions',             'required' => false],
                ['key' => 'allergies',          'label' => 'Historical disease / allergies', 'required' => false],
            ],
        ],
        'bank' => [
            'label' => 'Bank details',
            'icon'  => 'credit-card',
            'fields' => [
                ['key' => 'account_holder_name', 'label' => 'Account holder name', 'required' => false],
                ['key' => 'bank_name',           'label' => 'Bank name',           'required' => false],
                ['key' => 'branch_name',         'label' => 'Branch name',         'required' => false],
                ['key' => 'account_type',        'label' => 'Account type',        'required' => false],
                ['key' => 'account_number',      'label' => 'Account number',      'required' => false],
                ['key' => 'ifsc_code',           'label' => 'IFSC code',           'required' => false],
            ],
        ],
        'enrollment' => [
            'label' => 'Enrollment',
            'icon'  => 'book-open',
            'fields' => [
                ['key' => 'academic_year_id_label', 'label' => 'Academic year',     'required' => true],
                ['key' => 'course_id_label',        'label' => 'Course',            'required' => true],
                ['key' => 'stream_id_label',        'label' => 'Branch',            'required' => true],
                ['key' => 'class_id_label',         'label' => 'Semester',          'required' => true],
                ['key' => 'section_id_label',       'label' => 'Section',           'required' => true],
                ['key' => 'student_type',           'label' => 'Student type',      'required' => true],
                ['key' => 'residence_type',         'label' => 'Residence type',    'required' => false],
                ['key' => 'dateof_admission',       'label' => 'Admission date',    'required' => false],
                ['key' => 'enrollment_status',      'label' => 'Enrollment status', 'required' => true],
            ],
        ],
    ];

    // ── 6. Section analysis ───────────────────────────────────────────────
    $sections = [];
    foreach ($sectionsConfig as $key => $cfg) {
        $filled          = 0;
        $total           = count($cfg['fields']);
        $missingRequired = [];
        $fields          = [];

        foreach ($cfg['fields'] as $field) {
            $value = trim($data[$field['key']] ?? '');
            if ($field['required'] && $value === '') {
                $missingRequired[] = $field['label'];
            }
            if ($value !== '') $filled++;

            if ($field['key'] === 'account_number' && $value) {
                $value = str_repeat('●', max(0, strlen($value) - 4)) . substr($value, -4);
            }

            $fields[] = [
                'key'      => $field['key'],
                'label'    => $field['label'],
                'required' => $field['required'],
                'value'    => $value,
            ];
        }

        $status = 'empty';
        if ($filled === $total) $status = 'complete';
        elseif ($filled > 0)   $status = 'partial';

        $sections[$key] = [
            'label'            => $cfg['label'],
            'icon'             => $cfg['icon'],
            'status'           => $status,
            'filled'           => $filled,
            'total'            => $total,
            'missing_required' => $missingRequired,
            'fields'           => $fields,
        ];
    }

    // ── 7. Hidden inputs ──────────────────────────────────────────────────
    $skipKeys = ['_token', '_method'];

    $hiddenInputs = collect($data)
        ->except($skipKeys)
        ->map(function ($value, $key) {
            if ($key === 'fee_structure_ids' && !is_array($value)) {
                return array_filter([$value]);
            }
            return $value;
        })
        ->filter(function ($value) {
            if (is_array($value)) return true;
            return $value !== null && $value !== '';
        })
        ->toArray();

    // ── 8. Student object ─────────────────────────────────────────────────
    $student = \App\Models\Student::find($data['student_id_edit'] ?? null) ?? new \App\Models\Student;

    // ── 9. Submit route ───────────────────────────────────────────────────
    $submitRoute = route('admin.students.store');

    return view('Academics.student.preview', compact(
        'data', 'sections', 'hiddenInputs', 'student', 'submitRoute'
    ));
}
 
    // ─────────────────────────────────────────────────────────────────────────
    // STORE  —  Final save (preview form submit hone par)
    // ─────────────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        // dd($request->all());
        $studentEditId    = $request->get('student_id_edit');
        $enrollmentEditId = $request->get('enrollment_id');
 
        // ── VALIDATION ────────────────────────────────────────────────────────
        $rules = [
            'roll_number'               => 'nullable|max:20',
            'first_name'                => 'required|min:1|max:50',
            'last_name'                 => 'required|min:1|max:50',
            'gender'                    => 'nullable|in:male,female,other',
            'dob'                       => 'nullable|date',
            'blood_group'               => 'nullable|max:5',
            'religion'                  => 'nullable|max:50',
            'nationality'               => 'nullable|max:50',
            'physically_handicapped'    => 'nullable|in:yes,no',
            'handicap_details'          => 'nullable|string|max:500',
            'aadhaar'                   => 'nullable|digits:12|unique:students,aadhaar,' . $studentEditId,
            'emergency_contact_name'    => 'nullable|max:100',
            'emergency_contact_phone'   => 'nullable|min:10|max:15',
            'medical_conditions'        => 'nullable|max:500',
            'allergies'                 => 'nullable|max:500',
            // Photo: preview mein already store ho gayi, ab sirf temp path validate
            '_temp_photo_path'          => 'nullable|string',
            'old_photo'                 => 'nullable|string',
            'student_status'            => 'required',
            'account_holder_name'       => 'nullable|string|max:100',
            'bank_name'                 => 'nullable|string|max:255',
            'branch_name'               => 'nullable|string|max:100',
            'account_type'              => 'nullable|in:savings,current',
            'account_number'            => 'nullable|digits_between:9,18',
            'ifsc_code'                 => ['nullable', 'max:11', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
            'category'                  => 'nullable|in:sc,st,obc,general,sebc,other',
            'sub_caste'                 => 'nullable|string|max:100',
            'transfer_out_reason'       => 'nullable|string',
            'email'                     => 'nullable|email|max:255|unique:students,email,' . $studentEditId,
            'whatsapp_number'           => 'nullable|min:10|max:15',
            // Enrollment
            'academic_year_id'          => 'required|exists:academic_years,id',
            'course_id'                 => 'required|exists:courses,id',
            'class_id'                  => 'required|exists:classes,id',
            'section_id'                => 'required|exists:sections,id',
            'stream_id'                 => 'required|exists:streams,id',
            'student_type'              => 'required|in:regular,lateral',
            'residence_type'            => 'nullable|in:day_scholar,hosteller',
            'hostel_id'                 => 'nullable|exists:hostels,id',
            'room_id'                   => 'nullable|exists:hostel_rooms,id',
            'bed_id'                    => 'nullable|exists:hostel_beds,id',
            'hostel_fee'                => 'nullable|numeric|min:0',
            'hostel_from_date'          => 'nullable|date',
            'hostel_to_date'            => 'nullable|date|after_or_equal:hostel_from_date',
            'fee_structure_ids'         => 'required|array|min:1',
            'fee_structure_ids.*'       => 'exists:fee_structures,id',
            'dateof_admission'          => 'nullable|date',
            'date_of_leaving'           => 'nullable|date|after_or_equal:dateof_admission',
            'enrollment_status'         => 'required|in:active,promoted,transferred,completed',
        ];
 
        // Hosteller ke liye extra required fields
        if ($request->residence_type === 'hosteller') {
            $rules['hostel_id']        = 'required|exists:hostels,id';
            $rules['room_id']          = 'required|exists:hostel_rooms,id';
            $rules['bed_id']           = 'required|exists:hostel_beds,id';
            $rules['hostel_from_date'] = 'required|date';
        }
 
        $messages = [
            'first_name.required'             => 'Please enter first name.',
            'last_name.required'              => 'Please enter last name.',
            'aadhaar.digits'                  => 'Aadhaar must be exactly 12 digits.',
            'aadhaar.unique'                  => 'This Aadhaar number is already registered.',
            'student_status.required'         => 'Please select a student status.',
            'academic_year_id.required'       => 'Please select an academic year.',
            'course_id.required'              => 'Please select a course.',
            'stream_id.required'              => 'Please select a branch.',
            'class_id.required'               => 'Please select a semester.',
            'section_id.required'             => 'Please select a section.',
            'student_type.required'           => 'Please select student type.',
            'hostel_id.required'              => 'Please select a hostel.',
            'room_id.required'                => 'Please select a room.',
            'bed_id.required'                 => 'Please select a bed.',
            'hostel_from_date.required'       => 'Please enter the hostel from date.',
            'fee_structure_ids.required'      => 'Please select at least one fee structure.',
            'fee_structure_ids.min'           => 'Please select at least one fee structure.',
            'enrollment_status.required'      => 'Please select an enrollment status.',
            'enrollment_status.in'            => 'Invalid enrollment status.',
            'date_of_leaving.after_or_equal'  => 'Date of leaving must be on or after the admission date.',
            'physically_handicapped.in'       => 'Invalid value for physically handicapped.',
            'email.email'                     => 'Please enter a valid email address.',
            'email.unique'                    => 'This email is already registered.',
            'account_number.digits_between'   => 'Account number must be 9–18 digits.',
            'ifsc_code.regex'                 => 'Invalid IFSC format (e.g. SBIN0001234).',
        ];
 
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            // dd( $validator->errors()->first());
            return redirect()->back()
                ->withErrors($validator->errors()->first())
                ->withInput();
        }
//  dd(3456);
        // ── Course + Stream validation ────────────────────────────────────────
        $course = Course::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($request->course_id);
 
        $stream = Stream::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($request->stream_id);
 
        if ($request->student_type === 'lateral' && !$course->is_lateral_entry) {
            // DD('Lateral entry not allowed for this course');
            return redirect()->back()
                ->withErrors('Lateral admission is not available for the selected course.')
                ->withInput();
        }
 
        if (!$course->streams()->where('streams.id', $request->stream_id)->exists()) {
            // DD('Stream does not belong to course');
            return redirect()->back()
                ->withErrors('Selected branch does not belong to the chosen course.')
                ->withInput();
        }
 
        $mapping = $this->resolveClassStreamMapping(
            (int) $request->course_id,
            (int) $request->stream_id,
            (int) $request->class_id
        );
 
        if (!$mapping) {
            // DD('No mapping for course, stream, class');
            return redirect()->back()
                ->withErrors('Selected semester is not linked to the chosen course and branch.')
                ->withInput();
        }
 
        $sectionBelongsToMapping = Section::where('tenant_id', Auth::user()->tenant_id)
            ->where('id', $request->section_id)
            ->where('class_stream_id', $mapping->id)
            ->exists();
 
        if (!$sectionBelongsToMapping) {
            // DD('Section does not belong to class-stream mapping');
            return redirect()->back()
                ->withErrors('Selected section does not belong to the chosen semester.')
                ->withInput();
        }
 
        // ── Seat availability ─────────────────────────────────────────────────
        $seatAvailability = $this->getSeatAvailabilityData(
            (int) $request->academic_year_id,
            (int) $request->course_id,
            (int) $request->stream_id,
            (int) $request->class_id,
            (int) $request->section_id,
            $request->student_type,
            $enrollmentEditId ? (int) $enrollmentEditId : null
        );
 
        if (!$seatAvailability['available']) {
            DD('No seats available');
            return redirect()->back()
                ->withErrors($seatAvailability['message'])
                ->withInput();
        }
 
        // ── Save Student ──────────────────────────────────────────────────────
        if (!empty($studentEditId)) {
            // DD('Edit mode - updating existing student');
            $student = Student::where('id', $studentEditId)
                ->where('tenant_id', Auth::user()->tenant_id)
                ->firstOrFail();
        } else {
            $student             = new Student;
            $student->tenant_id  = Auth::user()->tenant_id;
            $student->admission_number = $this->generateAdmissionNumber(
                $course->code,
                $stream->code,
                $request->student_type
            );
            $student->username = $student->admission_number;
            $dobPassword       = $request->dob
                ? Carbon::parse($request->dob)->format('dmY')
                : Str::random(8);
            $student->password = Hash::make($dobPassword);
        }
 
        $student->user_id                  = Auth::id();
        $student->roll_number              = $request->roll_number;
        $student->username                 = $student->admission_number;
        $student->first_name               = trim($request->first_name);
        $student->last_name                = trim($request->last_name);
        $student->gender                   = $request->gender;
        $student->dob                      = $request->dob;
        $student->blood_group              = $request->blood_group;
        $student->religion                 = $request->religion;
        $student->nationality              = $request->nationality;
        $student->aadhaar                  = $request->aadhaar;
        $student->email                    = $request->email;
        $student->whatsapp_number          = $request->whatsapp_number;
        $student->emergency_contact_name   = $request->emergency_contact_name;
        $student->emergency_contact_phone  = $request->emergency_contact_phone;
        $student->medical_conditions       = $request->medical_conditions;
        $student->allergies                = $request->allergies;
        $student->status                   = $request->student_status;
        $student->account_holder_name      = $request->account_holder_name;
        $student->bank_name                = $request->bank_name;
        $student->branch_name              = $request->branch_name;
        $student->account_type             = $request->account_type;
        $student->account_number           = $request->account_number;
        $student->ifsc_code                = $request->ifsc_code;
        $student->category                 = $request->category;
        $student->sub_caste                = $request->sub_caste;
        $student->physically_handicapped   = $request->physically_handicapped;
        $student->handicap_details         = $request->physically_handicapped === 'yes'
            ? $request->handicap_details
            : null;
 
        if ($request->student_status === 'discontinued') {
            $student->discontinue_date   = $request->discontinue_date;
            $student->discontinue_reason = $request->discontinue_reason;
        }
        if ($request->student_status === 'transfer_out') {
            $student->transfer_out_reason = $request->transfer_out_reason;
        }
 
        // ── Photo handling ────────────────────────────────────────────────────
        // Priority order:
        //   1. Temp path (user ne preview step mein naya photo upload kiya)
        //   2. Old photo (edit mode, koi nayi upload nahi)
        //   3. Kuch nahi (create mode, no photo)
 
        if ($request->filled('_temp_photo_path')) {
            // Temp se permanent move karo
            $tempPath = $request->_temp_photo_path;
 
            if (Storage::disk('public')->exists($tempPath)) {
                $ext     = pathinfo($tempPath, PATHINFO_EXTENSION);
                $newPath = 'students/photos/' . time() . '_' . Str::random(10) . '.' . $ext;
 
                // Purani photo delete karo (edit mode)
                if (!empty($studentEditId) && $student->photo_url) {
                    Storage::disk('public')->delete($student->photo_url);
                }
 
                Storage::disk('public')->move($tempPath, $newPath);
                $student->photo_url = $newPath;
            }
        } elseif ($request->filled('old_photo')) {
            // Edit mode — existing photo rakho
            $student->photo_url = $request->old_photo;
        }
        // else: create mode, no photo — photo_url null rehta hai
 
        // ── DB Save ───────────────────────────────────────────────────────────
        try {
            $student->save();
        } catch (\Exception $e) {
            
            return redirect()->back()
                ->withErrors('Failed to save student: ' . $e->getMessage())
                ->withInput();
        }
 
        $studentId = $student->id;
 
        // ── Save Enrollment ───────────────────────────────────────────────────
        if (!empty($enrollmentEditId)) {
            $enrollment = StudentClassEnrollment::where('id', $enrollmentEditId)
                ->where('tenant_id', Auth::user()->tenant_id)
                ->firstOrFail();
        } else {
            $duplicate = StudentClassEnrollment::where('tenant_id', Auth::user()->tenant_id)
                ->where('student_id', $studentId)
                ->where('academic_year_id', $request->academic_year_id)
                ->first();
 
            if ($duplicate) {
                return redirect()->back()
                    ->withErrors('Student saved, but already enrolled for this academic year. Choose a different year.')
                    ->withInput();
            }
 
            $enrollment            = new StudentClassEnrollment;
            $enrollment->tenant_id = Auth::user()->tenant_id;
        }
 
        $enrollment->student_id        = $studentId;
        $enrollment->academic_year_id  = $request->academic_year_id;
        $enrollment->course_id         = $request->course_id;
        $enrollment->class_id          = $request->class_id;
        $enrollment->class_stream_id   = $mapping->id;
        $enrollment->section_id        = $request->section_id;
        $enrollment->stream_id         = $request->stream_id;
        $enrollment->student_type      = $request->student_type;
        $enrollment->residence_type    = $request->residence_type;
        $enrollment->dateof_admission  = $request->dateof_admission;
        $enrollment->date_of_leaving   = $request->date_of_leaving;
        $enrollment->status            = $request->enrollment_status;
 
        try {
            $enrollment->save();
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors('Student saved but enrollment failed: ' . $e->getMessage())
                ->withInput();
        }
 
        // ── Hostel Allocation ─────────────────────────────────────────────────
        if ($request->residence_type === 'hosteller' && $request->filled('hostel_id')) {
            HostelAllocation::updateOrCreate(
                [
                    'tenant_id'  => Auth::user()->tenant_id,
                    'student_id' => $studentId,
                ],
                [
                    'hostel_id' => $request->hostel_id,
                    'room_id'   => $request->room_id,
                    'bed_id'    => $request->bed_id,
                    'from_date' => $request->hostel_from_date,
                    'to_date'   => $request->hostel_to_date ?? null,
                ]
            );
        } elseif ($request->residence_type === 'day_scholar') {
            HostelAllocation::where('tenant_id', Auth::user()->tenant_id)
                ->where('student_id', $studentId)
                ->delete();
        }
 
        // ── Fee Structures ────────────────────────────────────────────────────
        if (!empty($enrollmentEditId)) {
            StudentFeeStructure::where('tenant_id', Auth::user()->tenant_id)
                ->where('student_id', $studentId)
                ->where('academic_year_id', $request->academic_year_id)
                ->delete();
        }
 
        foreach ($request->fee_structure_ids as $fsId) {
            StudentFeeStructure::firstOrCreate(
                [
                    'tenant_id'        => Auth::user()->tenant_id,
                    'student_id'       => $studentId,
                    'fee_structure_id' => $fsId,
                    'academic_year_id' => $request->academic_year_id,
                ],
                [
                    'start_date' => $request->dateof_admission ?? now(),
                    'end_date'   => now()->endOfYear(),
                    'status'     => 'assigned',
                ]
            );
        }
 
        // ── Cleanup old temp files (30 min se purani) ─────────────────────────
        // Optional: background job mein karo
        // Storage::disk('public')->delete($request->_temp_photo_path ?? '');
 
        $feeCount = count($request->fee_structure_ids);
        $action   = empty($enrollmentEditId) ? 'created' : 'updated';
 
        return redirect()->route('admin.students.index')
            ->with('success', "Student {$action} & enrolled successfully! {$feeCount} fee structure(s) assigned.");
    }


  public function show(Request $request, $id)
{
    $student = Student::with([
        'tenant',
        'user',
        'transfer',
        'routeMappings.route.vehicle',
        'routeMappings.stop',
        'hostelAllocations.hostel',
        'hostelAllocations.room',
        'hostelAllocations.bed',
        'records',
        'bookFines',
    ])->find($id);

    if (empty($student)) {
        abort(404);
    }

    // ── Paginated tab data ──────────────────────────────────────────
    $parents = \App\Models\StudentParent::where('student_id', $student->id)
        ->paginate(10, ['*'], 'parents_page');

    $addresses = \App\Models\StudentAddress::where('student_id', $student->id)
        ->paginate(10, ['*'], 'addresses_page');

    $documents = \App\Models\StudentDocument::where('student_id', $student->id)
        ->paginate(10, ['*'], 'documents_page');

    $academicQualifications = \App\Models\StudentAcademicQualification::where('student_id', $student->id)
        ->orderByRaw("FIELD(qualification_level, '10th', '12th', 'diploma', 'graduation', 'post_graduation', 'other')")
        ->paginate(10, ['*'], 'quals_page');

    $feeInstallments = \App\Models\FeeInstallment::with('feeCategory')
        ->where('student_id', $student->id)
        ->where('tenant_id', $student->tenant_id)
        ->paginate(10, ['*'], 'dues_page');

    $records = StudentRecord::where('tenant_id', auth()->user()->tenant_id)
        ->where('student_id', $student->id)
        ->orderBy('created_at', 'desc')
        ->paginate(10);


    $enrollments = StudentClassEnrollment::where('tenant_id', $student->tenant_id)
        ->where('student_id', $student->id)
        ->where('status', 'active')
        ->with(['course', 'class', 'section', 'stream', 'academicYear'])
        ->get();

    // ── Timetables ──────────────────────────────────────────────────
    $timetables = collect();

    foreach ($enrollments as $enrollment) {
        $timetablesForEnrollment = Timetable::where('tenant_id', $student->tenant_id)
            ->where('class_id', $enrollment->class_id)
            ->where('section_id', $enrollment->section_id)
            ->whereHas('stream', fn($q) => $q->where('id', $enrollment->stream_id))
            ->with(['slots.period', 'slots.subject', 'slots.staff', 'slots.room'])
            ->get();

        $timetables = $timetables->concat($timetablesForEnrollment);
    }

    $practicalTimetables = \App\Models\PracticalTimetable::where('tenant_id', $student->tenant_id)
        ->whereHas('practicalGroup.students', fn ($q) => $q->where('students.id', $student->id))
        ->with(['practicalGroup.groupMaster', 'practicalGroup.course', 'practicalGroup.stream', 'slots.period', 'slots.subject', 'slots.staff', 'slots.room'])
        ->get()
        ->values();

    // ── Enrollment history ──────────────────────────────────────────
    $enrollmentHistory = StudentClassEnrollment::where('tenant_id', $student->tenant_id)
        ->where('student_id', $student->id)
        ->with(['course', 'class', 'section', 'stream', 'academicYear'])
        ->orderBy('academic_year_id', 'desc')
        ->orderBy('enrolled_at', 'desc')
        ->get();

    // ── Promotion history ───────────────────────────────────────────
    $promotionHistory = Promotion::where('tenant_id', $student->tenant_id)
        ->where('student_id', $student->id)
        ->with('academicYear')
        ->latest()
        ->get();

    // ── Attendance semester options ─────────────────────────────────
    $attendanceSemesterOptions = collect();

    foreach ($promotionHistory as $promotion) {
        $academicYearId = (int) $promotion->academic_year_id;

        foreach ([$promotion->from_class_name, $promotion->to_class_name] as $className) {
            $className = trim((string) $className);
            if ($className === '') {
                continue;
            }

            $key = $academicYearId . '|' . $className;
            $attendanceSemesterOptions->push([
                'key'              => $key,
                'academic_year_id' => $academicYearId,
                'class_name'       => $className,
                'label'            => trim(
                    ($promotion->academicYear?->name ? $promotion->academicYear->name . ' - ' : '')
                    . $className
                ),
            ]);
        }
    }

    // Fallback: use enrollment history if no promotions exist
    if ($attendanceSemesterOptions->isEmpty()) {
        $attendanceSemesterOptions = $enrollmentHistory
            ->filter(fn($enrollment) => (bool) $enrollment->academic_year_id && (bool) $enrollment->class?->name)
            ->map(function ($enrollment) {
                $academicYearId = (int) $enrollment->academic_year_id;
                $className      = (string) ($enrollment->class?->name ?? '');
                $key            = $academicYearId . '|' . $className;
                return [
                    'key'              => $key,
                    'academic_year_id' => $academicYearId,
                    'class_name'       => $className,
                    'label'            => trim(
                        ($enrollment->academicYear?->name ? $enrollment->academicYear->name . ' - ' : '')
                        . $className
                    ),
                ];
            })
            ->values();
    }

    $attendanceSemesterOptions = $attendanceSemesterOptions
        ->unique('key')
        ->values();

    // ── Selected attendance semester ────────────────────────────────
    $selectedAttendanceSemesterKey = $request->string('attendance_semester')->toString() ?: '';
    if (
        $selectedAttendanceSemesterKey !== ''
        && ! $attendanceSemesterOptions->pluck('key')->contains($selectedAttendanceSemesterKey)
    ) {
        $selectedAttendanceSemesterKey = '';
    }

    // ── Attendance queries ──────────────────────────────────────────
    if ($selectedAttendanceSemesterKey !== '') {
        [$selectedAcademicYearId, $selectedClassName] = array_pad(
            explode('|', $selectedAttendanceSemesterKey, 2),
            2,
            ''
        );
        $selectedAcademicYearId = (int) $selectedAcademicYearId;
        $selectedClassName      = trim((string) $selectedClassName);

        $enrollmentIds = $enrollmentHistory
            ->filter(fn($enrollment) =>
                (int) $enrollment->academic_year_id === $selectedAcademicYearId
                && (string) ($enrollment->class?->name ?? '') === $selectedClassName
            )
            ->pluck('id');
    } else {
        $enrollmentIds = $enrollmentHistory->pluck('id');
    }

    $studentAttendanceQuery = StudentAttendance::where('tenant_id', $student->tenant_id);

    if ($enrollmentIds->isNotEmpty()) {
        $studentAttendanceQuery->whereIn('enrollment_id', $enrollmentIds);
    } else {
        $studentAttendanceQuery->whereRaw('1 = 0');
    }

    $studentAttendance = $studentAttendanceQuery
    ->with(['enrollment.course', 'enrollment.class', 'enrollment.section', 'enrollment.stream', 'period', 'markedBy'])
    ->orderBy('date', 'desc')
    ->orderBy('created_at', 'desc')
    ->paginate(10, ['*'], 'attendance_page');
    // Timetable slot se subject inject karo
$slots = TimetableSlot::with(['subject', 'timetable'])
    ->whereIn('period_id', $studentAttendance->pluck('period_id')->unique())
    ->whereHas('timetable', function ($q) use ($student) {
        $q->where('tenant_id', $student->tenant_id);
    })
    ->get();

$studentAttendance->each(function ($record) use ($slots) {
    $dayOfWeek = \Carbon\Carbon::parse($record->date)->dayOfWeek;

    $matchedSlot = $slots->first(function ($slot) use ($record, $dayOfWeek) {
        return $slot->period_id == $record->period_id
            && $slot->day_of_week == $dayOfWeek
            && $slot->timetable->class_id == $record->class_id
            && $slot->timetable->section_id == $record->section_id;
    });

    $record->resolved_subject = $matchedSlot?->subject;
});

    // ── Attendance stats & recent ───────────────────────────────────
    $attendanceStats  = collect();
    $recentAttendance = collect();

    if ($enrollmentIds->isNotEmpty()) {
        $attendanceStats = StudentAttendance::where('tenant_id', $student->tenant_id)
            ->whereIn('enrollment_id', $enrollmentIds)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $recentAttendance = StudentAttendance::where('tenant_id', $student->tenant_id)
            ->whereIn('enrollment_id', $enrollmentIds)
            ->where('date', '>=', now()->subDays(7))
            ->selectRaw('DATE(date) as date, status, COUNT(*) as count')
            ->groupBy('date', 'status')
            ->get()
            ->groupBy('date');
    }

    // ── Exam marks ──────────────────────────────────────────────────
    $currentEnrollment = $enrollments->first();

    $allExams = \App\Models\Exam::where('tenant_id', $student->tenant_id)
        ->where('class_id', $currentEnrollment?->class_id)
        ->where('class_id', $currentEnrollment?->class_id)          // ✅ course+branch class se aata hai
        ->where('academic_year_id', $currentEnrollment?->academic_year_id) // ✅ academic year
        ->with(['subject', 'gradeLevels'])
        ->get();

    $examMarks = \App\Models\ExamMark::where('student_id', $student->id)
        ->where('tenant_id', $student->tenant_id)
        ->get()
        ->keyBy('exam_id');

    // ── Transport ───────────────────────────────────────────────────
    $routes          = Routes::where('tenant_id', $student->tenant_id)->with('vehicle')->get();
    $existingMapping = $student->routeMappings()->first();
    $routeStops      = collect();

    if ($existingMapping && $existingMapping->route_id) {
        $routeStops = RouteStop::where('route_id', $existingMapping->route_id)
            ->orderBy('arrival_time')
            ->get();
    }

    // ── Hostel ──────────────────────────────────────────────────────
    $hostels       = Hostel::where('tenant_id', $student->tenant_id)->get();
    $existingHostel = $student->hostelAllocations()->with(['hostel', 'room', 'bed'])->latest()->first();
    $hostelbed     = $existingHostel;

    // ── Enrollment stats ────────────────────────────────────────────
    $enrollmentStats = [
        'total'          => $enrollmentHistory->count(),
        'active'         => $enrollmentHistory->where('status', 'active')->count(),
        'completed'      => $enrollmentHistory->where('status', 'completed')->count(),
        'current_class'  => $enrollments->first()?->class?->name ?? 'N/A',
        'current_stream' => $enrollments->first()?->stream?->name ?? 'N/A',
    ];
// ── Library Book Issues ─────────────────────────────────────────
$bookIssues = \App\Models\BookIssues::where('student_id', $student->id)
    ->where('tenant_id', $student->tenant_id)
     ->with('book')  
    ->orderBy('issue_date', 'desc')
    ->get();
$bookFines = FeeInstallment::where('student_id', $student->id)
            ->whereHas('feeCategory', fn($q) => $q->where('name', 'Book Fine'))
    ->latest()
    ->get();
    $totalLibraryFine = $bookIssues->sum('fine_amount') + $bookFines->sum('amount');
    return view('Academics.student.show', compact(
        'student',
        'bookIssues',
        'bookFines',
        'totalLibraryFine',
        'enrollments',
        'timetables',
        'practicalTimetables',
        'routes',
         'records',
        'existingMapping',
        'routeStops',
        'hostels',
        'existingHostel',
        'studentAttendance',
        'attendanceStats',
        'recentAttendance',
        'attendanceSemesterOptions',
        'selectedAttendanceSemesterKey',
        'hostelbed',
        'enrollmentHistory',
        'promotionHistory',
        'enrollmentStats',
        'allExams',
        'examMarks',
        // Paginated tab data
        'parents',
        'addresses',
        'documents',
        'academicQualifications',
        'feeInstallments',
    ));
}

    public function getStops(Routes $route)
    {
        $routeStops = RouteStop::where('route_id', $route->id)
            ->orderBy('arrival_time')
            ->get();
        return view('Academics.student._route_stops', compact('routeStops'))->render();
    }

    public function storeTransport(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'route_id' => 'required|exists:routes,id',
            'stop_id' => 'nullable|exists:route_stops,id',
        ]);

        $student = Student::findOrFail($request->student_id);

        StudentRouteMapping::updateOrCreate(
            ['student_id' => $student->id, 'route_id' => $request->route_id],
            [
                'tenant_id' => $student->tenant_id,
                'stop_id' => $request->stop_id,
                'transport_fee' => 3000,
            ]
        );

        return back()->with('success', 'Transport assigned successfully!');
    }

    public function destroyTransport(Student $student)
    {
        StudentRouteMapping::where('student_id', $student->id)->delete();
        return back()->with('success', 'Transport removed successfully!');
    }

    public function destroy($id)
    {
        $student = Student::find($id);

        if (empty($student)) {
            abort(404);
        }

        $student->delete();

        return redirect()->route('admin.students.index')->with('success', 'Student moved to trash successfully.');
    }

    public function getHostelBeds(HostelRoom $room)
    {
        $hostelBeds = HostelBed::where('room_id', $room->id)
            ->where('status', 'vacant')
            ->get();
        return view('Academics.student._hostel_beds', compact('hostelBeds'))->render();
    }

    public function storeHostel(Request $request, Student $student)
    {
        $request->validate([
            'hostel_id' => 'required|exists:hostels,id',
            'room_id' => 'required|exists:hostel_rooms,id',
            'bed_id' => 'required|exists:hostel_beds,id',
            'from_date' => 'required|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'hostel_fee' => 'nullable|numeric|min:0'
        ]);

        // Check if bed is already occupied
        $bed = HostelBed::findOrFail($request->bed_id);
        if ($bed->status === 'occupied' && !$request->has('allocation_id')) {
            return back()->withErrors(['bed_id' => 'This bed is already occupied']);
        }

        HostelAllocation::updateOrCreate(
            [
                'student_id' => $student->id,
                'hostel_id' => $request->hostel_id,
                'from_date' => $request->from_date
            ],
            [
                'tenant_id' => $student->tenant_id,
                'room_id' => $request->room_id,
                'bed_id' => $request->bed_id,
                'to_date' => $request->to_date,
            ]
        );

        // Update bed status
        $bed->update(['status' => 'occupied']);

        return back()->with('success', 'Hostel allocated successfully!');
    }

    public function destroyHostel(Student $student)
    {
        $allocation = $student->hostelAllocations()->latest()->first();
        if ($allocation) {
            $allocation->bed->update(['status' => 'vacant']);
            $allocation->delete();
        }
        return back()->with('success', 'Hostel allocation removed!');
    }

    public function ajaxFeeStructures(Request $request)
    {
        $academicYearId = $request->academic_year_id;
        $courseId = $request->course_id;
        $streamId = $request->stream_id;
        $classId = $request->class_id;
        $enrollmentId = $request->enrollment_id;
        $studentId = $request->student_id;

        $feeStructures = \App\Models\FeeStructure::where('tenant_id', auth()->user()->tenant_id)
            ->when($academicYearId, function ($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId);
            })
            ->when($courseId, function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            })
            ->when($streamId, function ($q) use ($streamId) {
                $q->where('stream_id', $streamId);
            })
            ->when($courseId && $classId, function ($q) use ($courseId, $streamId, $classId) {
                $yearNumber = $this->resolveYearNumberFromSelection((int) $courseId, (int) $streamId, (int) $classId);
                if ($yearNumber) {
                    $q->where('year_number', $yearNumber);
                }
            })
            ->with(['class', 'items.feeCategory'])
            ->get()
            ->map(function ($fs) {
                $fs->total_amount = $fs->items->sum('amount');
                return $fs;
            });

        $selectedFeeStructureIds = [];

        if ($enrollmentId && $studentId) {
            $selectedFeeStructureIds = StudentFeeStructure::where('tenant_id', auth()->user()->tenant_id)
                ->where('student_id', $studentId)
                ->where('academic_year_id', $academicYearId)
                ->pluck('fee_structure_id')
                ->toArray();
        }

        return view('Academics.student_class_enrollments._fee_structures', compact('feeStructures', 'selectedFeeStructureIds'))->render();
    }

    public function getStreams($courseId)
    {
        return response()->json(
            $this->getCourseStreamsCollection((int) $courseId)->values()
        );
    }

    public function getClasses($courseId, $streamId)
    {
        return response()->json(
            $this->getCourseClassesCollection((int) $courseId, (int) $streamId)->values()
        );
    }

    public function getSections($courseId, $streamId, $classId)
    {
        return response()->json(
            $this->getCourseSectionsCollection((int) $courseId, (int) $streamId, (int) $classId)->values()
        );
    }

    public function seatAvailability(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'academic_year_id' => 'required|integer',
            'course_id'        => 'required|integer',
            'stream_id'        => 'required|integer',
            'class_id'         => 'required|integer',
            'section_id'       => 'required|integer',
            'student_type'     => 'required|in:regular,lateral',
            'enrollment_id'    => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'available' => false,
                'message' => 'Please select academic year, course, branch, semester, section and student type.',
            ], 422);
        }

        return response()->json($this->getSeatAvailabilityData(
            (int) $request->academic_year_id,
            (int) $request->course_id,
            (int) $request->stream_id,
            (int) $request->class_id,
            (int) $request->section_id,
            $request->student_type,
            $request->filled('enrollment_id') ? (int) $request->enrollment_id : null
        ));
    }

    protected function getSeatAvailabilityData(
        int $academicYearId,
        int $courseId,
        int $streamId,
        int $classId,
        int $sectionId,
        string $studentType,
        ?int $excludeEnrollmentId = null
    ): array {
        $course = Course::where('tenant_id', Auth::user()->tenant_id)->find($courseId);

        if (! $course) {
            return [
                'available' => false,
                'message' => 'Selected course is invalid.',
            ];
        }

        $regularLimit = (int) $course->strength;
        if ($studentType === 'lateral' && ! $course->is_lateral_entry) {
            return [
                'available' => false,
                'course_strength' => $regularLimit,
                'lateral_seat_percentage' => 0,
                'limit' => 0,
                'used' => 0,
                'remaining' => 0,
                'student_type' => $studentType,
                'message' => 'Lateral admission is not available for the selected course.',
            ];
        }

        $lateralLimit = (int) floor($regularLimit * ((float) $course->lateral_seat_percentage / 100));
        $limit = $studentType === 'lateral' ? $lateralLimit : $regularLimit;

        $used = StudentClassEnrollment::where('tenant_id', Auth::user()->tenant_id)
            ->where('academic_year_id', $academicYearId)
            ->where('course_id', $courseId)
            ->where('stream_id', $streamId)
            ->where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->where('student_type', $studentType)
            ->where('status', 'active')
            ->when($excludeEnrollmentId, fn($query) => $query->where('id', '!=', $excludeEnrollmentId))
            ->count();

        $remaining = max($limit - $used, 0);
        $label = $studentType === 'lateral' ? 'lateral' : 'regular';

        return [
            'available' => $remaining > 0,
            'course_strength' => $regularLimit,
            'lateral_seat_percentage' => (float) $course->lateral_seat_percentage,
            'limit' => $limit,
            'used' => $used,
            'remaining' => $remaining,
            'student_type' => $studentType,
            'message' => $remaining > 0
                ? ucfirst($label) . " seats available: {$remaining} of {$limit}."
                : "No {$label} seats available for the selected semester and section.",
        ];
    }

    protected function getCourseStreamsCollection(int $courseId)
    {
        return DB::table('course_stream')
            ->join('streams', 'course_stream.stream_id', '=', 'streams.id')
            ->where('course_stream.course_id', $courseId)
            ->where('course_stream.tenant_id', Auth::user()->tenant_id)
            ->orderBy('streams.name')
            ->get(['streams.id', 'streams.name']);
    }

    protected function getCourseClassesCollection(int $courseId, int $streamId)
    {
        $courseStream = $this->resolveCourseStream($courseId, $streamId);

        if (! $courseStream) {
            return collect();
        }

        return DB::table('class_stream_mappings')
            ->join('classes', 'class_stream_mappings.class_id', '=', 'classes.id')
            ->where('class_stream_mappings.course_stream_id', $courseStream->id)
            ->where('class_stream_mappings.tenant_id', Auth::user()->tenant_id)
            ->orderBy('classes.order_index')
            ->orderBy('classes.name')
            ->get(['classes.id', 'classes.name', 'classes.order_index']);
    }

    protected function getCourseSectionsCollection(int $courseId, int $streamId, int $classId)
    {
        $mapping = $this->resolveClassStreamMapping($courseId, $streamId, $classId);

        if (! $mapping) {
            return collect();
        }

        return Section::where('tenant_id', Auth::user()->tenant_id)
            ->where('class_stream_id', $mapping->id)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    protected function resolveCourseStream(int $courseId, int $streamId): ?CourseStream
    {
        return CourseStream::where('tenant_id', Auth::user()->tenant_id)
            ->where('course_id', $courseId)
            ->where('stream_id', $streamId)
            ->first();
    }

    protected function resolveClassStreamMapping(int $courseId, int $streamId, int $classId): ?ClassStreamMapping
    {
        $courseStream = $this->resolveCourseStream($courseId, $streamId);

        if (! $courseStream) {
            return null;
        }

        return ClassStreamMapping::where('tenant_id', Auth::user()->tenant_id)
            ->where('course_stream_id', $courseStream->id)
            ->where('class_id', $classId)
            ->first();
    }

    protected function resolveYearNumberFromSelection(int $courseId, int $streamId, int $classId): ?int
    {
        $course = Course::where('tenant_id', Auth::user()->tenant_id)->find($courseId);
        $class = Classe::where('tenant_id', Auth::user()->tenant_id)->find($classId);

        if (! $course || ! $class) {
            return null;
        }

        $sequence = (int) ($class->order_index ?: (preg_match('/\d+/', $class->name, $matches) ? $matches[0] : 1));

        $courseStream = $this->resolveCourseStream($courseId, $streamId);
        $mappedClassCount = 0;

        if ($courseStream) {
            $mappedClassCount = ClassStreamMapping::where('tenant_id', Auth::user()->tenant_id)
                ->where('course_stream_id', $courseStream->id)
                ->count();
        }

        if ($mappedClassCount > 0 && $mappedClassCount <= (int) $course->duration_years) {
            return min(max($sequence, 1), (int) $course->duration_years);
        }

        return min((int) ceil(max($sequence, 1) / 2), (int) $course->duration_years);
    }
}