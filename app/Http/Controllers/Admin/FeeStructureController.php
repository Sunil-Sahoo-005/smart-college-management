<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\FeeStructure;
use App\Models\FeeCategory;
use App\Models\AcademicYear;
use App\Models\Classe;
use App\Models\Course;
use App\Models\CourseStage;
use App\Models\FeeInstallment;
use App\Models\Stream;
use App\Models\ScholarshipTransaction;
use App\Models\StudentScholarship;
use App\Models\StudentFeeStructure;
use App\Traits\HandlesFeeInstallments;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class FeeStructureController extends Controller
{
    use HandlesFeeInstallments;
    //
    protected $feeStructureListRoute = 'admin.fee_structures.index';

    // public function index(Request $request)
    // {
    //     $feeStructures = FeeStructure::where('tenant_id', Auth::user()->tenant_id)
    //         ->with('stream', 'class')
    //         ->latest()
    //         ->paginate(10);

    //     return view('Accountants.fee_structures.index', compact('feeStructures'));
    // }
    public function index(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;

        $streams = Stream::where('tenant_id', $tenantId)
            ->with(['feeStructures.course', 'feeStructures.items', 'feeStructures.studentFeeAssignments'])
            ->has('feeStructures')
            ->get();

        $standaloneFees = FeeStructure::where('tenant_id', $tenantId)
            ->whereNull('stream_id')
            ->with(['course', 'items'])
            ->latest()
            ->limit(5)
            ->get();

        return view('Accountants.fee_structures.index', compact('streams', 'standaloneFees'));
    }
    public function create()
    {
        $courses = Course::where('tenant_id', Auth::user()->tenant_id)
            ->with(['streams' => function ($query) {
                $query->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        $streams       = Stream::where('tenant_id', Auth::user()->tenant_id)->get();
        $feeCategories = FeeCategory::where('tenant_id', Auth::user()->tenant_id)->get();

        return view('Accountants.fee_structures.form', compact(
            'courses',
            'streams',
            'feeCategories'
        ));
    }

    public function edit(FeeStructure $feeStructure)
    {
        $feeStructure->load('items.feeCategory');

        $courses = Course::where('tenant_id', Auth::user()->tenant_id)
            ->with(['streams' => function ($query) {
                $query->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        $streams       = Stream::where('tenant_id', Auth::user()->tenant_id)->get();
        $feeCategories = FeeCategory::where('tenant_id', Auth::user()->tenant_id)->get();

        return view('Accountants.fee_structures.form', compact(
            'feeStructure',
            'courses',
            'streams',
            'feeCategories'
        ));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name'        => 'required|string|max:255',
            'course_id'   => 'required|exists:courses,id',
            'stream_id'   => 'required|exists:streams,id',
            'year_number' => 'required|integer|min:1',
            'items'       => 'required|array|min:1',
            'items.*.fee_category_id' => 'required|exists:fee_categories,id',
            'items.*.amount'          => 'required|numeric|min:0.01',
            'items.*.due_month'       => 'nullable|integer|between:1,12',
        ]);

        $course = Course::where('tenant_id', Auth::user()->tenant_id)->findOrFail($request->course_id);

        if (! $course->streams()->where('streams.id', $request->stream_id)->exists()) {
            return back()->withErrors(['stream_id' => 'Selected branch is not linked to the chosen course.'])->withInput();
        }

        if ((int) $request->year_number > (int) $course->duration_years) {
            return back()->withErrors(['year_number' => 'Selected year is outside the course duration.'])->withInput();
        }

        $feeStructure = FeeStructure::create([
            'tenant_id' => Auth::user()->tenant_id,
            'course_id' => $request->course_id,
            'stream_id' => $request->stream_id,
            'year_number' => $request->year_number,
            'name'      => $request->name,
        ]);

        foreach ($request->items as $item) {
            $feeStructure->items()->create([
                'fee_category_id' => $item['fee_category_id'],
                'amount'          => $item['amount'],
                'due_month'       => $item['due_month'] ?? null,
            ]);
        }

        return redirect()
            ->route('admin.fee_structures.index')
            ->with('success', 'Fee structure created successfully.');
    }

    public function update(Request $request, FeeStructure $feeStructure)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'course_id'   => 'required|exists:courses,id',
            'stream_id'   => 'required|exists:streams,id',
            'year_number' => 'required|integer|min:1',
            'items'       => 'required|array|min:1',
            'items.*.fee_category_id' => 'required|exists:fee_categories,id',
            'items.*.amount'          => 'required|numeric|min:0.01',
            'items.*.due_month'       => 'nullable|integer|between:1,12',
        ]);

        $course = Course::where('tenant_id', Auth::user()->tenant_id)->findOrFail($request->course_id);

        if (! $course->streams()->where('streams.id', $request->stream_id)->exists()) {
            return back()->withErrors(['stream_id' => 'Selected branch is not linked to the chosen course.'])->withInput();
        }

        if ((int) $request->year_number > (int) $course->duration_years) {
            return back()->withErrors(['year_number' => 'Selected year is outside the course duration.'])->withInput();
        }

        $feeStructure->update([
            'course_id' => $request->course_id,
            'stream_id' => $request->stream_id,
            'year_number' => $request->year_number,
            'name'      => $request->name,
        ]);

        // Delete old items
        $feeStructure->items()->delete();

        foreach ($request->items as $item) {
            $feeStructure->items()->create([
                'fee_category_id' => $item['fee_category_id'],
                'amount'          => $item['amount'],
                'due_month'       => $item['due_month'] ?? null,
            ]);
        }

        return redirect()
            ->route('admin.fee_structures.index')
            ->with('success', 'Fee structure updated successfully.');
    }
    public function destroy($id)
    {
        $feeStructure = FeeStructure::where('tenant_id', Auth::user()->tenant_id)->find($id);

        if (empty($feeStructure)) {
            abort(404);
        }

        $feeStructure->items()->delete();
        $feeStructure->delete();

        return redirect()->route('admin.fee_structures.index')->with('success', 'Fee structure deleted successfully.');
    }


    // FeeStructureController.php

    public function wizardItems($id)
    {
        $feeStructure = FeeStructure::where('tenant_id', Auth::user()->tenant_id)
            ->with('items.feeCategory')
            ->find($id);

        if (empty($feeStructure)) {
            abort(404);
        }

        $courses = Course::where('tenant_id', Auth::user()->tenant_id)
            ->with(['streams' => function ($query) {
                $query->orderBy('name');
            }])
            ->orderBy('name')
            ->get();
        $academicYears = AcademicYear::where('tenant_id', Auth::user()->tenant_id)->get();
        $feeCategories = FeeCategory::where('tenant_id', Auth::user()->tenant_id)->get();
        $streams = Stream::where('tenant_id', Auth::user()->tenant_id)->get();

        return view('Accountants.fee_structures.form', compact(
            'feeStructure',
            'courses',
            'streams',
            'academicYears',
            'feeCategories'
        ));
    }



    public function generate(FeeStructure $feeStructure)
    {
        // dd();
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

    public function generateStore(Request $request, FeeStructure $feeStructure)
    {
        $studentPlans = StudentFeeStructure::where('tenant_id', Auth::user()->tenant_id)
            ->where('fee_structure_id', $feeStructure->id)
            ->with('student')
            ->get();

        if ($studentPlans->isEmpty()) {
            return redirect()
                ->back()
                ->with('info', 'No students assigned to this fee structure.');
        }

        $createdCount = 0;
        $skippedCount = 0;

        foreach ($studentPlans as $plan) {
            $start = Carbon::parse($plan->start_date);
            $end   = Carbon::parse($plan->end_date);

            $existingCount = FeeInstallment::where('tenant_id', $plan->tenant_id)
                ->where('student_id', $plan->student_id)
                ->whereBetween('due_date', [$start, $end])
                ->count();

            if ($existingCount > 0) {
                $skippedCount++;
                continue;
            }

            foreach ($feeStructure->items as $item) {
                $amount = $item->amount;

                // Use due_month from the item
                if ($item->due_month === null) {
                    // If no due_month, fall back to end of year
                    $due = $start->copy()->endOfYear();
                } else {
                    $due = $start->copy()
                        ->month($item->due_month)
                        ->endOfMonth();
                }

                // If due is outside the plan period, skip
                if ($due < $start || $due > $end) {
                    continue;
                }

                FeeInstallment::create([
                    'tenant_id'       => $plan->tenant_id,
                    'student_id'      => $plan->student_id,
                    'fee_category_id' => $item->fee_category_id,
                    'amount'          => $amount,
                    'due_date'        => $due,
                    'status'          => 'unpaid',
                ]);

                $createdCount++;
            }

            // Update plan status to 'generated'
            $plan->update(['status' => 'generated']);
        }

        return redirect()
            ->route('admin.fee_structures.index')
            ->with('success', "Fee schedule generated for {$studentPlans->count()} students. "
                . "Created {$createdCount} installments, skipped {$skippedCount} (already exist).");
    }

    public function individualGenerate($id)
    {
        // dd($id);
        $plan = StudentFeeStructure::where('tenant_id', Auth::user()->tenant_id)
            ->with(['feeStructure.items', 'student'])
            ->findOrFail($id);

        $createdCount = $this->generateInstallments($plan);

        if ($createdCount > 0) {
            $this->applyScholarshipBalanceToStudent(
                (int) $plan->tenant_id,
                (int) $plan->student_id
            );
        }

        if ($createdCount > 0) {
            return back()->with('success', "Success! Generated {$createdCount} installments.");
        } else {
            return back()->with('info', "No new installments were generated. They might already exist or fall outside the validity period.");
        }
    }

    private function applyScholarshipBalanceToStudent(int $tenantId, int $studentId): void
    {
        $scholarships = StudentScholarship::where('tenant_id', $tenantId)
            ->where('student_id', $studentId)
            ->whereIn('status', ['pending', 'approved'])
            ->where('balance_amount', '>', 0)
            ->orderBy('created_at')
            ->get();

        foreach ($scholarships as $studentScholarship) {
            $remainingBalance = (float) $studentScholarship->balance_amount;
            $usedAmount = (float) $studentScholarship->used_amount;

            if ($remainingBalance <= 0) {
                continue;
            }

            $installments = FeeInstallment::where('tenant_id', $tenantId)
                ->where('student_id', $studentId)
                ->whereIn('status', ['unpaid', 'partial'])
                ->orderBy('due_date')
                ->orderBy('id')
                ->get();

            foreach ($installments as $installment) {
                if ($remainingBalance <= 0) {
                    break;
                }

                $existingPaid = (float) ($installment->paid_amount ?? 0);
                $installmentDue = max(0, (float) $installment->amount - $existingPaid);

                if ($installmentDue <= 0) {
                    continue;
                }

                $amountUsed = min($remainingBalance, $installmentDue);
                $newPaidAmount = $existingPaid + $amountUsed;
                $newStatus = $newPaidAmount >= (float) $installment->amount ? 'paid' : 'partial';

                $installment->update([
                    'status' => $newStatus,
                    'paid_amount' => $newPaidAmount,
                ]);

                ScholarshipTransaction::create([
                    'student_scholarship_id' => $studentScholarship->id,
                    'installment_id' => $installment->id,
                    'amount_used' => $amountUsed,
                    'remarks' => 'Scholarship cleared during fee structure generation',
                ]);

                $remainingBalance -= $amountUsed;
                $usedAmount += $amountUsed;
            }

            $studentScholarship->update([
                'used_amount' => $usedAmount,
                'balance_amount' => $remainingBalance,
                'status' => $remainingBalance > 0 ? 'approved' : 'completed',
            ]);
        }
    }
}
