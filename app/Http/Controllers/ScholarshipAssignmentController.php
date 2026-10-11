<?php

namespace App\Http\Controllers;

use App\Models\Scholarship;
use App\Models\ScholarshipTransaction;
use App\Models\Student;
use App\Models\StudentScholarship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScholarshipAssignmentController extends Controller
{
    public function index()
    {
        $scholarships = Scholarship::orderBy('name')->get();
        $studentSearch = trim((string) request('student_search'));

        $assignments = StudentScholarship::with(['student', 'scholarship'])
            ->when($studentSearch !== '', function ($query) use ($studentSearch) {
                $query->whereHas('student', function ($studentQuery) use ($studentSearch) {
                    $studentQuery->where('first_name', 'like', '%' . $studentSearch . '%')
                        ->orWhere('last_name', 'like', '%' . $studentSearch . '%')
                        ->orWhere('name', 'like', '%' . $studentSearch . '%')
                        ->orWhere('admission_number', 'like', '%' . $studentSearch . '%');
                });
            })
            ->when(request('scholarship_id'), function ($query, $scholarshipId) {
                $query->where('scholarship_id', $scholarshipId);
            })
            ->when(request('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderByDesc('created_at')
            ->get();

        return view('Accountants.scholarships.list', compact('assignments', 'scholarships', 'studentSearch'));
    }

    public function showForm()
    {
        $scholarships = Scholarship::where('is_active', true)->get();

        return view('Accountants.scholarships.assign_form', compact('scholarships'));
    }

    public function fetchStudent(Request $request)
    {
        $student = Student::where('admission_number', $request->input('admission_number'))->first();

        if (! $student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        return response()->json($student);
    }

    public function assign(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'scholarship_id' => 'required|exists:scholarships,id',
            'applied_on' => 'nullable|date',
            'remarks' => 'nullable|string|max:5000',
        ]);

        return DB::transaction(function () use ($validated) {
            $student = Student::select('id', 'tenant_id')->findOrFail($validated['student_id']);
            $scholarship = Scholarship::findOrFail($validated['scholarship_id']);
            $appliedOn = $validated['applied_on'] ?? now()->toDateString();
            $approvedAmount = $this->calculateApprovedAmount($student->id, $scholarship);
// dd($approvedAmount);
            $studentScholarship = StudentScholarship::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'scholarship_id' => $validated['scholarship_id'],
                ],
                [
                    'tenant_id' => $student->tenant_id,
                    'approved_amount' => $approvedAmount,
                    'used_amount' => 0,
                    'balance_amount' => $approvedAmount,
                    'applied_on' => $appliedOn,
                    'status' => 'pending',
                    'remarks' => $validated['remarks'] ?? null,
                ]
            );

            return redirect()
                ->route('scholarships.list')
                ->with('success', 'Scholarship saved as pending. Approve it from the list to clear installments.');
        });
    }

    public function approve($id)
    {
        // dd($id);
        return DB::transaction(function () use ($id) {
            $assignment = StudentScholarship::with('scholarship')->findOrFail($id);
// dd($assignment);
            $remainingBalance = (float) $assignment->balance_amount;
            $usedAmount = (float) $assignment->used_amount;

            $unpaidInstallments = DB::table('fee_installments')
                ->where('student_id', $assignment->student_id)
                ->whereIn('status', ['unpaid', 'partial'])
                ->orderBy('due_date')
                ->orderBy('id')
                ->get();
// dd($unpaidInstallments);
            foreach ($unpaidInstallments as $inst) {
                if ($remainingBalance <= 0) {
                    break;
                }

                $existingPaid = (float) ($inst->paid_amount ?? 0);
                $installmentDue = max(0, (float) $inst->amount - $existingPaid);

                if ($installmentDue <= 0) {
                    continue;
                }

                $amountUsed = min($remainingBalance, $installmentDue);
                $newPaidAmount = $existingPaid + $amountUsed;
                $newStatus = $newPaidAmount >= (float) $inst->amount ? 'paid' : 'partial';

                DB::table('fee_installments')
                    ->where('id', $inst->id)
                    ->update([
                        'status' => $newStatus,
                        'paid_amount' => $newPaidAmount,
                    ]);

                ScholarshipTransaction::create([
                    'student_scholarship_id' => $assignment->id,
                    'installment_id' => $inst->id,
                    'amount_used' => $amountUsed,
                    'remarks' => 'Scholarship approval clearance',
                ]);

                $remainingBalance -= $amountUsed;
                $usedAmount += $amountUsed;
            }

            $assignment->update([
                'used_amount' => $usedAmount,
                'balance_amount' => $remainingBalance,
                'status' => $remainingBalance > 0 ? 'approved' : 'completed',
            ]);

            return back()->with('success', 'Scholarship approved and installments processed.');
        });
    }

    public function reject($id)
    {
        $assignment = StudentScholarship::findOrFail($id);

        $assignment->update([
            'status' => 'rejected',
        ]);

        return back()->with('success', 'Scholarship rejected successfully.');
    }

    private function calculateApprovedAmount(int $studentId, Scholarship $scholarship): float
    {
        if ($scholarship->type === 'fixed') {
            return round((float) $scholarship->value, 2);
        }

        $totalFeeStructureAmount = (float) DB::table('student_fee_structures as sfs')
            ->join('fee_structures as fs', 'fs.id', '=', 'sfs.fee_structure_id')
            ->join('fee_structure_items as fsi', 'fsi.fee_structure_id', '=', 'fs.id')
            ->where('sfs.student_id', $studentId)
            // ->where('sfs.status', 'active')
            ->sum('fsi.amount');
// dd($totalFeeStructureAmount);
        return round($totalFeeStructureAmount * ((float) $scholarship->value / 100), 2);
    }
}
