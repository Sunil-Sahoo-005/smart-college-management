<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\FeeCategory;
use App\Models\FeeInstallment;
use App\Models\StudentFeeStructure;
use App\Models\Student;
use App\Models\FeeStructure;
use App\Models\Stream;
use App\Models\StudentClassEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentFeePlanController extends Controller
{

    public function destroy($id)
    {
        $plan = StudentFeeStructure::where('tenant_id', Auth::user()->tenant_id)->findOrFail($id);
        $plan->delete();

        return redirect()->route('admin.student-fee-plans.index')
            ->with('success', 'Fee plan removed from student.');
    }
     public function generate(FeeStructure $feeStructure)
    {
        $feeStructure->load('items');

        $studentPlans = StudentFeeStructure::where('tenant_id', Auth::user()->tenant_id)
            ->where('fee_structure_id', $feeStructure->id)
            ->with('student')
            ->get();

        return view('Accountants.fee_structures.generate', compact(
            'feeStructure',
            'studentPlans'
        ));
    }
    public function generateForm(Request $request)
    {
        $studentFeePlans = StudentFeeStructure::where('tenant_id', Auth::user()->tenant_id)
            ->with(['student', 'feeStructure'])
            ->get();

        // Optional: if a plan is selected in the URL, show its installments
        $installments = collect();
        if ($request->filled('student_fee_structure_id')) {
            $plan = StudentFeeStructure::where('tenant_id', Auth::user()->tenant_id)
                ->with('feeStructure.items')
                ->find($request->student_fee_structure_id);

            if ($plan) {
                $installments = FeeInstallment::where('student_id', $plan->student_id)
                    ->whereBetween('due_date', [$plan->start_date, $plan->end_date])
                    ->with('feeCategory')
                    ->orderBy('due_date')
                    ->get();
            }
        }

        return view('Accountants.student_billing.generate_schedule', compact(
            'studentFeePlans',
            'installments'
        ));
    }


    public function generateStore(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'student_fee_structure_id' => 'required|exists:student_fee_structures,id',
            'frequency'                => 'required|in:monthly,quarterly,termly',
        ]);

        $plan = StudentFeeStructure::where('tenant_id', Auth::user()->tenant_id)
            ->with('feeStructure.items')
            ->findOrFail($request->student_fee_structure_id);

        // Optional: clear existing installments for this student in this period
        // FeeInstallment::where('student_id', $plan->student_id)
        //     ->whereBetween('due_date', [$plan->start_date, $plan->end_date])
        //     ->delete();

        $start = \Carbon\Carbon::parse($plan->start_date);
        $end   = \Carbon\Carbon::parse($plan->end_date);

        $interval = match ($request->frequency) {
            'monthly'   => 'month',
            'quarterly' => '3 months',
            'termly'    => '3 months', // adjust if your term is different
        };

        foreach ($plan->feeStructure->items as $item) {
            $amountPerPeriod = $item->amount / 12; // simple monthly split; adjust as needed

            $current = clone $start;
            while ($current <= $end) {
                // If due_month is set, only create installment for that month
                if ($item->due_month !== null) {
                    if ($current->month != $item->due_month) {
                        $current->addMonth();
                        continue;
                    }
                }

                FeeInstallment::create([
                    'tenant_id'         => Auth::user()->tenant_id,
                    'student_id'        => $plan->student_id,
                    'fee_category_id'   => $item->fee_category_id,
                    'amount'            => $amountPerPeriod,
                    'due_date'          => $current->copy()->endOfMonth(),
                    'status'            => 'unpaid',
                ]);

                $current->addMonth();
            }
        }

        return redirect()->route('admin.student-fee-plans.generate')
            ->with('success', 'Fee schedule generated successfully.');
    }

    public function showEnrolledStudents(Request $request)
    {
        $request->validate([
            'stream_id'        => 'required|exists:streams,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $streamId       = $request->stream_id;
        $academicYearId = $request->academic_year_id;

        $enrollments = StudentClassEnrollment::where('tenant_id', Auth::user()->tenant_id)
            ->where('stream_id', $streamId)
            ->where('academic_year_id', $academicYearId)
            ->where('status', 'active')
            ->with('student', 'class', 'section')
            ->get();

        $streams       = Stream::where('tenant_id', Auth::user()->tenant_id)->get();
        $academicYears = AcademicYear::where('tenant_id', Auth::user()->tenant_id)->get();
        $feeStructures = FeeStructure::where('tenant_id', Auth::user()->tenant_id)
            ->with('stream', 'class')
            ->get();

        return view('Accountants.fee_structures.assign', compact(
            'enrollments',
            'streams',
            'academicYears',
            'feeStructures'
        ));
    }


    public function index(Request $request)
    {
        $studentFeeStructures = StudentFeeStructure::where('tenant_id', Auth::user()->tenant_id)
            ->with('student', 'feeStructure.stream', 'feeStructure.class', 'academicYear')
            ->latest()
            ->paginate(10);

        return view('Accountants.student_fee_structures.index', compact('studentFeeStructures'));
    }

    public function create(Request $request)
    {
        $streamId       = $request->query('stream_id');
        $academicYearId = $request->query('academic_year_id');
        $feeStructureId = $request->query('fee_structure_id');
        $planId         = $request->query('id'); // optional: edit mode

        $streams       = Stream::where('tenant_id', Auth::user()->tenant_id)->get();
        $academicYears = AcademicYear::where('tenant_id', Auth::user()->tenant_id)->get();
        $feeStructures = FeeStructure::where('tenant_id', Auth::user()->tenant_id)
            ->with('stream', 'class')
            ->get();

        $enrollments = collect();
        $assignedStudentIds = [];

        if ($streamId && $academicYearId) {
            // Get enrolled students in this branch + year
            $enrollments = StudentClassEnrollment::where('tenant_id', Auth::user()->tenant_id)
                ->where('stream_id', $streamId)
                ->where('academic_year_id', $academicYearId)
                ->where('status', 'active')
                ->with('student', 'class', 'section')
                ->get();

            // If editing an existing plan, get already‑assigned students
            if ($planId && $feeStructureId) {
                $assignedStudentIds = StudentFeeStructure::where('tenant_id', Auth::user()->tenant_id)
                    ->where('id', $planId)
                    ->pluck('student_id')
                    ->toArray();
            }
        }

        return view('Accountants.student_fee_structures.assign', compact(
            'streams',
            'academicYears',
            'feeStructures',
            'enrollments',
            'assignedStudentIds',
            'streamId',
            'academicYearId',
            'feeStructureId',
            'planId'
        ));
    }

    public function editAssignment($id)
    {
        $plan = StudentFeeStructure::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        return redirect()->route('admin.student_fee_structures.create', [
            'stream_id'        => $plan->feeStructure->stream_id,
            'academic_year_id' => $plan->academic_year_id,
            'fee_structure_id' => $plan->fee_structure_id,
            'id'               => $id,
        ]);
    }

    public function ajaxEnrolledStudentsAdd(Request $request)
    {
        $request->validate([
            'stream_id'        => 'required|exists:streams,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $enrollments = StudentClassEnrollment::where('tenant_id', Auth::user()->tenant_id)
            ->where('stream_id', $request->stream_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('status', 'active')
            ->with('student', 'class', 'section')
            ->get();

        return response()->json([
            'enrollments' => $enrollments,
            'html' => view('Accountants.student_fee_structures._enrollments_table', compact('enrollments'))->render(),
        ]);
    }

    public function ajaxEnrolledStudents(Request $request)
    {
        $request->validate([
            'stream_id'        => 'required|exists:streams,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'fee_structure_id' => 'required|exists:fee_structures,id',
            'plan_id'          => 'nullable|exists:student_fee_structures,id',
        ]);

        $enrollments = StudentClassEnrollment::where('tenant_id', Auth::user()->tenant_id)
            ->where('stream_id', $request->stream_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('status', 'active')
            ->with('student', 'class', 'section')
            ->get();

        $assignedStudentIds = [];

        if ($request->plan_id) {
            $assignedStudentIds = StudentFeeStructure::where('tenant_id', Auth::user()->tenant_id)
                ->where('id', $request->plan_id)
                ->pluck('student_id')
                ->toArray();
        }

        return response()->json([
            'enrollments'        => $enrollments,
            'assignedStudentIds' => $assignedStudentIds,
            'html'               => view('Accountants.student_fee_structures._enrollments_table', compact('enrollments', 'assignedStudentIds'))->render(),
        ]);
    }

    public function assign(Request $request)
    {
        $request->validate([
            'stream_id'          => 'required|exists:streams,id',
            'academic_year_id'   => 'required|exists:academic_years,id',
            'fee_structure_id'   => 'required|exists:fee_structures,id',
            'student_ids'        => 'nullable|array',
            'student_ids.*'      => 'exists:students,id',
            'plan_id'            => 'nullable|exists:student_fee_structures,id',
        ]);

        $streamId       = $request->stream_id;
        $academicYearId = $request->academic_year_id;
        $feeStructureId = $request->fee_structure_id;
        $planId         = $request->plan_id;
        $studentIds     = $request->student_ids ?? [];

        $enrollments = StudentClassEnrollment::where('tenant_id', Auth::user()->tenant_id)
            ->where('stream_id', $streamId)
            ->where('academic_year_id', $academicYearId)
            ->where('status', 'active')
            ->with('student')
            ->get();

        $assignedCount = 0;
        $skippedCount  = 0;

        foreach ($enrollments as $enrollment) {
            $studentId = $enrollment->student_id;

            // If student is selected (or "Select All" is used)
            $shouldBeAssigned = empty($studentIds) || in_array($studentId, $studentIds);

            // Check if already assigned
            $existing = StudentFeeStructure::where('tenant_id', $enrollment->tenant_id)
                ->where('student_id', $studentId)
                ->where('fee_structure_id', $feeStructureId)
                ->where('academic_year_id', $academicYearId)
                ->first();

            if ($shouldBeAssigned) {
                if (!$existing) {
                    StudentFeeStructure::create([
                        'tenant_id'         => $enrollment->tenant_id,
                        'student_id'        => $studentId,
                        'fee_structure_id'  => $feeStructureId,
                        'academic_year_id'  => $academicYearId,
                        'start_date'        => $enrollment->enrolled_at ?? now()->startOfYear(),
                        'end_date'          => $enrollment->enrolled_at ? $enrollment->enrolled_at->endOfYear() : now()->endOfYear(),
                    ]);
                    $assignedCount++;
                } else {
                    $skippedCount++;
                }
            } else {
                // Student is not selected → remove assignment
                if ($existing) {
                    $existing->delete();
                    $assignedCount--;
                }
            }
        }

        return redirect()
            ->route('admin.student_fee_structures.index')
            ->with('success', "Assignment updated for {$assignedCount} students. "
                . "Skipped {$skippedCount} (already assigned).");
    }

     public function createExtraFee()
    {
        // $streams       = Stream::where('tenant_id', Auth::user()->tenant_id)->get();
        // $academicYears = AcademicYear::where('tenant_id', Auth::user()->tenant_id)->get();
        // $categories    = FeeCategory::where('tenant_id', Auth::user()->tenant_id)->get();

        // return view('Accountants.extra_fee.create', compact('streams', 'academicYears', 'categories'));

    $streams       = Stream::where('tenant_id', Auth::user()->tenant_id)->get();
        $academicYears = AcademicYear::where('tenant_id', Auth::user()->tenant_id)->get();
        $categories    = FeeCategory::where('tenant_id', Auth::user()->tenant_id)->get();

        return view('Accountants.extra_fee.create', compact(
            'streams',
            'academicYears',
            'categories'
        ));
        // ));
    }
 public function studentsByBranchYear(Request $request)
    {
        $request->validate([
            'stream_id'        => 'required|exists:streams,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $enrollments = StudentClassEnrollment::where('tenant_id', Auth::user()->tenant_id)
            ->where('stream_id', $request->stream_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('status', 'active')
            ->with('student')
            ->get();

        // Return minimal data for dropdown
        $students = $enrollments->map(function ($enrollment) {
            return [
                'id'   => $enrollment->student->id,
                'name' => $enrollment->student->first_name . ' ' . $enrollment->student->last_name,
                'roll' => $enrollment->student->roll_number,
            ];
        });

        return response()->json(['students' => $students]);
    }
    public function storeExtraFee(Request $request)
    {
        $request->validate([
            'student_id'      => 'required|exists:students,id',
            'fee_category_id' => 'required|exists:fee_categories,id',
            'amount'          => 'required|numeric|min:0.01',
            'due_date'        => 'required|date',
        ]);

        $student = Student::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($request->student_id);

        // Optional: prevent exact duplicate (same category + same due_date)
        $existing = FeeInstallment::where('tenant_id', $student->tenant_id)
            ->where('student_id', $student->id)
            ->where('fee_category_id', $request->fee_category_id)
            ->where('due_date', $request->due_date)
            ->exists();

        if ($existing) {
            return back()->with('info', 'This extra fee is already added for the student on this date.');
        }

        FeeInstallment::create([
            'tenant_id'       => $student->tenant_id,
            'student_id'      => $student->id,
            'fee_category_id' => $request->fee_category_id,
            'amount'          => $request->amount,
            'due_date'        => $request->due_date,
            'status'          => 'unpaid',
        ]);

        return redirect()
            ->back()
            ->with('success', 'Extra fee added for the student.');
    // }
    }
}
