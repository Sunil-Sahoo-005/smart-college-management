<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeInstallment;
use App\Models\Student;
use App\Models\FeeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeeInstallmentController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = FeeInstallment::where('tenant_id', Auth::user()->tenant_id)
            ->with(['student', 'feeCategory']);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('fee_category_id')) {
            $query->where('fee_category_id', $request->fee_category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $installments = $query->latest()->paginate(20)->appends($request->all());

        $students = Student::where('tenant_id', Auth::user()->tenant_id)->get();
        $feeCategories = FeeCategory::where('tenant_id', Auth::user()->tenant_id)->get();

        return view('Accountants.fee_installments.index', compact(
            'installments',
            'students',
            'feeCategories'
        ));
    }

    public function create()
    {
        $students = Student::where('tenant_id', Auth::user()->tenant_id)->get();
        $feeCategories = FeeCategory::where('tenant_id', Auth::user()->tenant_id)->get();

        return view('Accountants.fee_installments.form', compact('students', 'feeCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id'        => 'required|exists:students,id',
            'fee_category_id'   => 'required|exists:fee_categories,id',
            'amount'            => 'required|numeric|min:0.01',
            'due_date'          => 'required|date',
            'status'            => 'required|in:unpaid,paid,partial',
        ]);

        FeeInstallment::create([
            'tenant_id'         => Auth::user()->tenant_id,
            'student_id'        => $request->student_id,
            'fee_category_id'   => $request->fee_category_id,
            'amount'            => $request->amount,
            'due_date'          => $request->due_date,
            'status'            => $request->status,
        ]);

        return redirect()->route('admin.fee_installments.index')
            ->with('success', 'Fee installment created successfully.');
    }

    public function edit($id)
    {
        $installment = FeeInstallment::where('tenant_id', Auth::user()->tenant_id)
            ->with(['student', 'feeCategory'])
            ->findOrFail($id);

        $students = Student::where('tenant_id', Auth::user()->tenant_id)->get();
        $feeCategories = FeeCategory::where('tenant_id', Auth::user()->tenant_id)->get();

        return view('Accountants.fee_installments.form', compact(
            'installment',
            'students',
            'feeCategories'
        ));
    }

    public function update(Request $request, $id)
    {
        $installment = FeeInstallment::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        $request->validate([
            'student_id'        => 'required|exists:students,id',
            'fee_category_id'   => 'required|exists:fee_categories,id',
            'amount'            => 'required|numeric|min:0.01',
            'due_date'          => 'required|date',
            'status'            => 'required|in:unpaid,paid,partial',
        ]);

        $installment->update([
            'student_id'        => $request->student_id,
            'fee_category_id'   => $request->fee_category_id,
            'amount'            => $request->amount,
            'due_date'          => $request->due_date,
            'status'            => $request->status,
        ]);

        return redirect()->route('admin.fee_installments.index')
            ->with('success', 'Fee installment updated successfully.');
    }

    public function destroy($id)
    {
        $installment = FeeInstallment::where('tenant_id', Auth::user()->tenant_id)->findOrFail($id);
        $installment->delete();

        return redirect()->route('admin.fee_installments.index')
            ->with('success', 'Fee installment deleted.');
    }
}
