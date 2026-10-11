<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\StudentClassEnrollment;
use App\Models\Student;
use App\Models\Classe;
use App\Models\Section;
use App\Models\Stream;
use App\Models\AcademicYear;
use App\Models\ClassStreamMapping;
use App\Models\FeeStructure;
use App\Models\StudentFeeStructure;
use App\Models\Course;
use App\Models\CourseStream;
use App\Models\Hostel;
use App\Models\HostelAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentClassEnrollmentController extends Controller
{
    protected $enrollmentListRoute = 'admin.student_class_enrollments.index';

    public function index()
    {
        $enrollments = StudentClassEnrollment::where('tenant_id', auth()->user()->tenant_id)
            ->with(['student', 'class', 'section', 'stream', 'academicYear'])
            ->orderBy('academic_year_id')
            ->orderBy('class_id')
            ->paginate(10);

        return view('Academics.student_class_enrollments.index', compact('enrollments'));
    }

    public function create()
    {
        $students = Student::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $classes = Classe::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('order_index')
            ->orderBy('name')
            ->get();

        $sections = Section::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        $streams = Stream::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        $academicYears = AcademicYear::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        $hostels = Hostel::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        $feeStructuresByStream = FeeStructure::where('tenant_id', auth()->user()->tenant_id)
            ->with(['stream', 'class', 'items.feeCategory'])
            ->get()
            ->groupBy('stream_id')
            ->map(function ($structures, $streamId) {
                return $structures->map(function ($fs) {
                    $fs->total_amount = $fs->items->sum('amount');
                    return $fs;
                });
            });

        $existingFeeStructureIds = [];
        $existingHostel = null;

        return view('Academics.student_class_enrollments.form', compact(
            'students', 'classes', 'sections', 'streams',
            'academicYears', 'feeStructuresByStream', 'existingFeeStructureIds',
            'hostels', 'existingHostel'
        ));
    }

    public function ajaxFeeStructures(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'stream_id'        => 'required|exists:streams,id',
            'course_id'        => 'nullable|exists:courses,id',
            'class_id'         => 'nullable|exists:classes,id',
            'selected_fee_structure_ids'   => 'nullable|array',
            'selected_fee_structure_ids.*' => 'integer|exists:fee_structures,id',
        ]);

        $existingFeeStructureIds = [];
        $selectedFeeStructureIds = $request->input('selected_fee_structure_ids', []);

        if ($request->filled('enrollment_id') && $request->filled('student_id')) {
            $existingFeeStructureIds = StudentFeeStructure::where('tenant_id', auth()->user()->tenant_id)
                ->where('student_id', $request->student_id)
                ->where('academic_year_id', $request->academic_year_id)
                ->pluck('fee_structure_id')
                ->toArray();
        }

        if (empty($selectedFeeStructureIds)) {
            $selectedFeeStructureIds = $existingFeeStructureIds;
        }

        $feeStructures = FeeStructure::where('tenant_id', auth()->user()->tenant_id)
            ->where('stream_id', $request->stream_id)
            ->when($request->filled('course_id'), function ($query) use ($request) {
                $query->where('course_id', $request->course_id);
            })
            // ->when($request->filled('course_id') && $request->filled('class_id'), function ($query) use ($request) {
            //     $yearNumber = $this->resolveYearNumberFromSelection((int) $request->course_id, (int) $request->stream_id, (int) $request->class_id);
            //     if ($yearNumber) {
            //         $query->where('year_number', $yearNumber);
            //     }
            // })
            ->with(['class', 'items.feeCategory'])
            ->get()
            ->map(function ($fs) {
                $fs->total_amount = $fs->items->sum('amount');
                return $fs;
            });

        $html = view('Academics.student_class_enrollments._fee_structures', compact(
            'feeStructures',
            'existingFeeStructureIds',
            'selectedFeeStructureIds'
        ))->render();

        return response()->json(['html' => $html]);
    }

    public function edit(StudentClassEnrollment $enrollment)
    {
        $this->authorizeTenant($enrollment);

        $students = Student::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $classes = Classe::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('order_index')
            ->orderBy('name')
            ->get();

        $sections = Section::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        $streams = Stream::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        $academicYears = AcademicYear::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        $hostels = Hostel::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        $feeStructuresByStream = FeeStructure::where('tenant_id', auth()->user()->tenant_id)
            ->with(['stream', 'class', 'items'])
            ->get()
            ->groupBy('stream_id');

        $existingFeeStructureIds = StudentFeeStructure::where('tenant_id', auth()->user()->tenant_id)
            ->where('student_id', $enrollment->student_id)
            ->where('academic_year_id', $enrollment->academic_year_id)
            ->pluck('fee_structure_id')
            ->toArray();

        $existingHostel = HostelAllocation::where('tenant_id', auth()->user()->tenant_id)
            ->where('student_id', $enrollment->student_id)
            ->with(['hostel', 'room', 'bed'])
            ->latest()
            ->first();

        return view('Academics.student_class_enrollments.form', compact(
            'enrollment', 'students', 'classes', 'sections', 'streams',
            'academicYears', 'feeStructuresByStream', 'existingFeeStructureIds',
            'hostels', 'existingHostel'
        ));
    }

    public function store(Request $request)
    {
        $rules = [
            'student_id'          => 'required|exists:students,id',
            'academic_year_id'    => 'required|exists:academic_years,id',
            'class_id'            => 'required|exists:classes,id',
            'section_id'          => 'required|exists:sections,id',
            'stream_id'           => 'nullable|exists:streams,id',
            'student_type'        => 'nullable|in:regular,lateral',
            'residence_type'      => 'nullable|in:day_scholar,hosteller',
            'hostel_id'           => 'nullable|exists:hostels,id',
            'room_id'             => 'nullable|exists:hostel_rooms,id',
            'bed_id'              => 'nullable|exists:hostel_beds,id',
            'hostel_fee'          => 'nullable|numeric|min:0',
            'hostel_from_date'    => 'nullable|date',
            'hostel_to_date'      => 'nullable|date|after_or_equal:hostel_from_date',
            'fee_structure_ids'   => 'required|array|min:1',
            'fee_structure_ids.*' => 'exists:fee_structures,id',
            'dateof_admission'    => 'nullable|date',
            'date_of_leaving'     => 'nullable|date|after_or_equal:dateof_admission', // ✅ add kiya
            'status'              => 'required|in:active,promoted,transferred,completed',
        ];

        // Agar hosteller select kiya hai toh hostel fields required
        if ($request->residence_type === 'hosteller') {
            $rules['hostel_id']        = 'required|exists:hostels,id';
            $rules['room_id']          = 'required|exists:hostel_rooms,id';
            $rules['bed_id']           = 'required|exists:hostel_beds,id';
            $rules['hostel_from_date'] = 'required|date';
        }

        $messages = [
            'student_id.required'               => 'Please select student!',
            'student_id.exists'                 => 'Invalid student selected!',
            'academic_year_id.required'         => 'Please select academic year!',
            'academic_year_id.exists'           => 'Invalid academic year selected!',
            'class_id.required'                 => 'Please select class!',
            'class_id.exists'                   => 'Invalid class selected!',
            'section_id.required'               => 'Please select section!',
            'section_id.exists'                 => 'Invalid section selected!',
            'stream_id.exists'                  => 'Invalid stream selected!',
            'student_type.in'                   => 'Student type must be Regular or Lateral!',
            'residence_type.in'                 => 'Residence type must be Day Scholar or Hosteller!',
            'hostel_id.required'                => 'Please select hostel!',
            'room_id.required'                  => 'Please select room!',
            'bed_id.required'                   => 'Please select bed!',
            'hostel_from_date.required'         => 'Please select hostel from date!',
            'fee_structure_ids.required'        => 'Please select at least one fee structure!',
            'fee_structure_ids.min'             => 'Please select at least one fee structure!',
            'fee_structure_ids.*.exists'        => 'Invalid fee structure selected!',
            'dateof_admission.date'             => 'Invalid admission date!',
            'date_of_leaving.date'              => 'Invalid leaving date!',           // ✅ add kiya
            'date_of_leaving.after_or_equal'    => 'Leaving date must be after or equal to admission date!', // ✅ add kiya
            'status.required'                   => 'Please select status!',
            'status.in'                         => 'Invalid status selected!',
           
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors()->first())->withInput();
        }

        if (!empty($request->get('id'))) {
            $enrollment = StudentClassEnrollment::where('id', $request->get('id'))
                ->where('tenant_id', auth()->user()->tenant_id)
                ->first();

            if (empty($enrollment)) {
                abort(404);
            }

            $redirect = [
                'url' => redirect()->back(),
                'msg' => 'Student enrollment updated successfully + fees reassigned',
            ];
        } else {
            $existing = StudentClassEnrollment::where('tenant_id', auth()->user()->tenant_id)
                ->where('student_id', $request->student_id)
                ->where('academic_year_id', $request->academic_year_id)
                ->first();

            if ($existing) {
                return redirect()->back()
                    ->withErrors('Student is already enrolled for this academic year!')
                    ->withInput();
            }

            $enrollment = new StudentClassEnrollment;
            $enrollment->tenant_id = auth()->user()->tenant_id;

            $redirect = [
                'url' => redirect()->route($this->enrollmentListRoute),
                'msg' => 'Student enrolled successfully + fees assigned',
            ];
        }

        $enrollment->student_id       = $request->student_id;
        $enrollment->academic_year_id = $request->academic_year_id;
        $enrollment->class_id         = $request->class_id;
        $enrollment->section_id       = $request->section_id;
        $enrollment->stream_id        = $request->stream_id;
        $enrollment->student_type     = $request->student_type;
        $enrollment->residence_type   = $request->residence_type;
        $enrollment->dateof_admission = $request->dateof_admission;
        $enrollment->date_of_leaving  = $request->date_of_leaving;  // ✅ add kiya
        $enrollment->status           = $request->status;

        try {
            $enrollment->save();

            // ✅ Hostel allocation save/update
            if ($request->residence_type === 'hosteller' && $request->filled('hostel_id')) {
                HostelAllocation::updateOrCreate(
                    [
                        'tenant_id'  => auth()->user()->tenant_id,
                        'student_id' => $request->student_id,
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
                HostelAllocation::where('tenant_id', auth()->user()->tenant_id)
                    ->where('student_id', $request->student_id)
                    ->delete();
            }

            $studentId       = $request->student_id;
            $academicYearId  = $request->academic_year_id;
            $feeStructureIds = $request->fee_structure_ids;

            if (!empty($request->get('id'))) {
                StudentFeeStructure::where('tenant_id', auth()->user()->tenant_id)
                    ->where('student_id', $studentId)
                    ->where('academic_year_id', $academicYearId)
                    ->delete();
            }

            foreach ($feeStructureIds as $fsId) {
                StudentFeeStructure::firstOrCreate(
                    [
                        'tenant_id'        => auth()->user()->tenant_id,
                        'student_id'       => $studentId,
                        'fee_structure_id' => $fsId,
                        'academic_year_id' => $academicYearId,
                    ],
                    [
                        'start_date' => $request->dateof_admission ?? now(),
                        'end_date'   => now()->endOfYear(),
                        'status'     => 'active',
                    ]
                );
            }

            $feeCount = count($feeStructureIds);
            return $redirect['url']->with('success', $redirect['msg'] . " | {$feeCount} fee structure(s) assigned!");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Something went wrong: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(StudentClassEnrollment $enrollment)
    {
        $this->authorizeTenant($enrollment);

        try {
            $enrollment->delete();
            return back()->with('success', 'Student enrollment deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete enrollment, it may be in use.');
        }
    }

    protected function authorizeTenant(StudentClassEnrollment $enrollment)
    {
        if ($enrollment->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }
    }

    protected function resolveCourseStream(int $courseId, int $streamId): ?CourseStream
    {
        return CourseStream::where('tenant_id', auth()->user()->tenant_id)
            ->where('course_id', $courseId)
            ->where('stream_id', $streamId)
            ->first();
    }

    protected function resolveYearNumberFromSelection(int $courseId, int $streamId, int $classId): ?int
    {
        $course = Course::where('tenant_id', auth()->user()->tenant_id)->find($courseId);
        $class = Classe::where('tenant_id', auth()->user()->tenant_id)->find($classId);

        if (! $course || ! $class) {
            return null;
        }

        $sequence = (int) ($class->order_index ?: (preg_match('/\d+/', $class->name, $matches) ? $matches[0] : 1));

        $courseStream = $this->resolveCourseStream($courseId, $streamId);
        $mappedClassCount = 0;

        if ($courseStream) {
            $mappedClassCount = ClassStreamMapping::where('tenant_id', auth()->user()->tenant_id)
                ->where('course_stream_id', $courseStream->id)
                ->count();
        }

        if ($mappedClassCount > 0 && $mappedClassCount <= (int) $course->duration_years) {
            return min(max($sequence, 1), (int) $course->duration_years);
        }

        return min((int) ceil(max($sequence, 1) / 2), (int) $course->duration_years);
    }
}
