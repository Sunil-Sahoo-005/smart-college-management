<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentFeeStructure;
use App\Models\FeeInstallment;
use App\Models\Promotion;
use App\Models\StudentScholarship;
use App\Models\StudentClassEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentAccountController extends Controller
{
    /**
     * Display the student ledger search page or results.
     */
    public function index(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        $student = null;
        $admissionNo = $request->get('admission_no');

        if ($admissionNo) {
            $student = Student::where('tenant_id', $tenantId)
                ->where('admission_number', $admissionNo)
                ->with([
                    'enrollments.course',
                    'enrollments.stream',
                    'enrollments.class',
                    'enrollments.section',
                    'enrollments.academicYear',
                    'feeInstallments' => function($query) {
                        $query->with(['feeCategory', 'latestPayment'])->orderBy('due_date', 'asc');
                    },
                ])
                ->first();

            if ($student) {
                // Fetch additional related data
                $feeStructures = StudentFeeStructure::where('tenant_id', $tenantId)
                    ->where('student_id', $student->id)
                    ->with(['feeStructure', 'academicYear'])
                    ->get();

                $promotions = Promotion::where('tenant_id', $tenantId)
                    ->where('student_id', $student->id)
                    ->with('academicYear')
                    ->orderBy('created_at', 'desc')
                    ->get();

                $scholarships = StudentScholarship::where('tenant_id', $tenantId)
                    ->where('student_id', $student->id)
                    ->with(['scholarship', 'transactions'])
                    ->orderByDesc('created_at')
                    ->get();

                // Calculate total financial summary
                $summary = [
                    'total_amount' => $student->feeInstallments->sum('amount'),
                    'paid_amount' => $student->feeInstallments->where('status', 'paid')->sum('amount'),
                    'pending_amount' => $student->feeInstallments->whereIn('status', ['unpaid', 'partial'])->sum('amount'), // Assuming 'partial' means some remaining
                    'scholarship_approved' => $scholarships->whereIn('status', ['approved', 'completed'])->sum('approved_amount'),
                    'scholarship_used' => $scholarships->whereIn('status', ['approved', 'completed'])->sum('used_amount'),
                    'scholarship_balance' => $scholarships->whereIn('status', ['approved', 'completed'])->sum('balance_amount'),
                ];

                return view('Accountants.student_ledger', compact('student', 'feeStructures', 'promotions', 'scholarships', 'summary', 'admissionNo'));
            } else {
                return redirect()->route('admin.student-ledger.index')
                    ->with('error', 'Student with admission number ' . $admissionNo . ' not found.')
                    ->withInput();
            }
        }

        return view('Accountants.student_ledger', compact('student', 'admissionNo'));
    }
}
