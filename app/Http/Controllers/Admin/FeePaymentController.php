<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\BankAccounts;
use App\Models\FeeInstallment;
use App\Models\FeePayment;
use App\Models\Stream;
use App\Models\Student;
use App\Services\LedgerService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FeePaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = FeePayment::where('tenant_id', Auth::user()->tenant_id)
            ->with('student');

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('payment_date')) {
            $query->whereDate('payment_date', $request->payment_date);
        }

        if ($request->filled('receipt_number')) {
            $query->where('receipt_number', 'like', '%' . $request->receipt_number . '%');
        }

        $payments = $query->latest()->paginate(20);
        $students = Student::where('tenant_id', Auth::user()->tenant_id)->get();

        return view('Accountants.fee_payments.index', compact('payments', 'students'));
    }

    public function create()
    {
        $students = Student::where('tenant_id', Auth::user()->tenant_id)->get();
        return view('Accountants.fee_payments.form', compact('students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'payment_method' => 'required|in:cash,online,bank_transfer',
            'transaction_id' => 'nullable|string|max:255',
            'gateway_name' => 'nullable|string|max:255',
            'amount_paid' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'receipt_number' => 'required|string|unique:fee_payments,receipt_number',
        ]);

        $ledgerService = new LedgerService();

        DB::transaction(function () use ($request, $ledgerService) {
            $payment = FeePayment::create([
                'tenant_id' => Auth::user()->tenant_id,
                'student_id' => $request->student_id,
                'payment_method' => $request->payment_method,
                'transaction_id' => $request->transaction_id,
                'gateway_name' => $request->gateway_name,
                'amount_paid' => $request->amount_paid,
                'paid_at' => now(),
                'payment_date' => $request->payment_date,
                'receipt_number' => $request->receipt_number,
            ]);

            $ledgerService->recordFeePayment($payment);
        });

        return redirect()->route('admin.fee-payments.index')
            ->with('success', 'Payment recorded successfully.');
    }

    public function edit($id)
    {
        $payment = FeePayment::where('tenant_id', Auth::user()->tenant_id)
            ->with('student')
            ->findOrFail($id);

        $students = Student::where('tenant_id', Auth::user()->tenant_id)->get();

        return view('Accountants.fee_payments.form', compact('payment', 'students'));
    }

    public function update(Request $request, $id)
    {
        $payment = FeePayment::where('tenant_id', Auth::user()->tenant_id)->findOrFail($id);
        $ledgerService = new LedgerService();

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'payment_method' => 'required|in:cash,online,bank_transfer',
            'transaction_id' => 'nullable|string|max:255',
            'gateway_name' => 'nullable|string|max:255',
            'amount_paid' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'receipt_number' => 'required|string|unique:fee_payments,receipt_number,' . $payment->id,
        ]);

        DB::transaction(function () use ($request, $payment, $ledgerService) {
            $payment->update([
                'student_id' => $request->student_id,
                'payment_method' => $request->payment_method,
                'transaction_id' => $request->transaction_id,
                'gateway_name' => $request->gateway_name,
                'amount_paid' => $request->amount_paid,
                'payment_date' => $request->payment_date,
                'receipt_number' => $request->receipt_number,
            ]);

            $ledgerService->repostFeePayment($payment->fresh());
        });

        return redirect()->route('admin.fee-payments.index')
            ->with('success', 'Payment updated successfully.');
    }

    public function destroy($id)
    {
        $payment = FeePayment::where('tenant_id', Auth::user()->tenant_id)->findOrFail($id);
        $ledgerService = new LedgerService();

        DB::transaction(function () use ($payment, $ledgerService) {
            $ledgerService->forgetReference(FeePayment::class, $payment->id, $payment->tenant_id);
            $payment->delete();
        });

        return redirect()->route('admin.fee-payments.index')
            ->with('success', 'Payment deleted successfully.');
    }

    public function collect(Request $request)
    {
        $streams = Stream::where('tenant_id', Auth::user()->tenant_id)->get();
        $academicYears = AcademicYear::where('tenant_id', Auth::user()->tenant_id)->get();
        $students = collect();
        $installments = collect();
        $paidInstallments = collect();
        $totalDue = 0;

        $bankAccounts = BankAccounts::where('tenant_id', Auth::user()->tenant_id)
            ->where('status', 'active')
            ->get();

        if ($request->has('stream_id') && $request->has('academic_year_id')) {
            $students = Student::where('tenant_id', Auth::user()->tenant_id)
                ->whereHas('enrollments', function ($q) use ($request) {
                    $q->where('stream_id', $request->stream_id)
                        ->where('academic_year_id', $request->academic_year_id)
                        ->where('status', 'active');
                })
                ->orderBy('first_name')
                ->get();

            if ($request->has('student_id')) {
                $student = Student::where('tenant_id', Auth::user()->tenant_id)
                    ->findOrFail($request->student_id);

                $installments = FeeInstallment::where('tenant_id', $student->tenant_id)
                    ->where('student_id', $student->id)
                    ->orderBy('due_date')
                    ->get();

                $totalDue = $installments->whereIn('status', ['unpaid', 'partial'])->sum('amount');
                $paidInstallments = $installments->where('status', 'paid');
            }
        }

        return view('Accountants.fee_payments.fee_collect', compact(
            'streams',
            'academicYears',
            'students',
            'installments',
            'paidInstallments',
            'totalDue',
            'bankAccounts'
        ));
    }

    public function storeCollect(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'installment_ids' => 'required|array',
            'installment_ids.*' => 'exists:fee_installments,id',
            'amount_paid' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,online,bank_transfer',
            'payment_date' => 'required|date',
            'receipt_number' => 'required|string|unique:fee_payments,receipt_number',
            'transaction_id' => 'nullable|string',
            'gateway_name' => 'nullable|string',
            'bank_account_id' => 'required_if:payment_method,bank_transfer|nullable|exists:bank_accounts,id',
        ]);

        $student = Student::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($request->student_id);

        $installments = FeeInstallment::where('tenant_id', $student->tenant_id)
            ->whereIn('id', $request->installment_ids)
            ->whereIn('status', ['unpaid', 'partial'])
            ->get();

        $totalSelected = $installments->sum('amount');

        if ($request->amount_paid != $totalSelected) {
            return back()->with('error', 'Amount paid must match selected installments total.');
        }

        $gatewayName = $request->gateway_name;
        if ($request->payment_method == 'bank_transfer' && $request->bank_account_id) {
            $bankAccount = BankAccounts::where('tenant_id', Auth::user()->tenant_id)
                ->where('id', $request->bank_account_id)
                ->first();

            if ($bankAccount) {
                $gatewayName = $bankAccount->bank_name . ' - ' . $bankAccount->account_number;
            }
        }

        $ledgerService = new LedgerService();

        DB::transaction(function () use ($installments, $request, $student, $gatewayName, $ledgerService) {
            foreach ($installments as $installment) {
                $receiptNumber = $request->receipt_number . '-' . $installment->id;

                $payment = FeePayment::create([
                    'tenant_id' => $student->tenant_id,
                    'student_id' => $student->id,
                    'fee_installment_id' => $installment->id,
                    'payment_method' => $request->payment_method,
                    'transaction_id' => $request->transaction_id,
                    'gateway_name' => $gatewayName,
                    'amount_paid' => $installment->amount,
                    'payment_date' => $request->payment_date,
                    'receipt_number' => $receiptNumber,
                    'paid_at' => now(),
                ]);

                $installment->update([
                    'status' => 'paid',
                ]);

                $ledgerService->recordFeePayment($payment);
            }
        });

        return redirect()
            ->route('admin.payments.collect', [
                'stream_id' => $request->stream_id,
                'academic_year_id' => $request->academic_year_id,
                'student_id' => $student->id,
            ])
            ->with('success', 'Payment recorded successfully. Receipt Number: ' . $request->receipt_number);
    }

    public function show(FeePayment $feePayment)
    {
        $feePayment->load([
            'student',
            'installment.feeCategory',
            'bankAccount',
        ]);

        return view('Accountants.fee_payments.show', compact('feePayment'));
    }

    public function receipt(FeePayment $feePayment)
    {
        $feePayment->load([
            'student',
            'installment.feeCategory',
            'bankAccount',
        ]);

        $pdf = Pdf::loadView('Accountants.fee_payments.receipt', compact('feePayment'));

        return $pdf->download('receipt-' . $feePayment->receipt_number . '.pdf');
    }

    private function createLedgerEntry($payment)
    {
        // Implement if needed.
    }
}
