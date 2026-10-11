<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;

use App\Models\ExpenseHead;
use App\Models\ExpenseRecord;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExpenseRecordsExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Services\LedgerService;

class ExpenseRecordController extends Controller
{
    //
    protected $userListRoute = 'expense-records.index';

    public function index(Request $request)
    {
        $query = ExpenseRecord::with('expenseHead')
            ->where('tenant_id', auth()->user()->tenant_id);

        // Category Filter
        if ($request->filled('head_id')) {
            $query->where('expense_head_id', $request->head_id);
        }

        // Date Filter
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $expenseRecords = $query->orderBy('date', 'desc')->paginate(20);
        $expenseHeads = ExpenseHead::where('tenant_id', auth()->user()->tenant_id)->get();

        return view('Accountants.expense-records.index', compact('expenseRecords', 'expenseHeads'));
    }

    public function create()
    {
        $expenseHeads = ExpenseHead::where('tenant_id', auth()->user()->tenant_id)->get();
        return view('Accountants.expense-records.form', compact('expenseHeads'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_head_id' => 'required|exists:expense_heads,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'description' => 'nullable|string|max:500',
        ], [
            'expense_head_id.required' => 'Please select expense head!',
            'amount.required' => 'Please enter amount!',
            'date.required' => 'Please select date!',
        ]);

        $tenantId = auth()->user()->tenant_id;
        $ledgerService = new LedgerService();

        DB::transaction(function () use ($request, $tenantId, $ledgerService) {
            $expenseHead = ExpenseHead::where('tenant_id', $tenantId)
                ->findOrFail($request->expense_head_id);

            $expenseRecord = ExpenseRecord::create([
                'tenant_id' => $tenantId,
                'expense_head_id' => $request->expense_head_id,
                'amount' => $request->amount,
                'date' => $request->date,
                'description' => $request->description,
            ]);

            $ledgerName = trim($expenseHead->name);

            $existingLedger = DB::table('ledgers')
                ->where('tenant_id', $tenantId)
                ->where('name', $ledgerName)
                ->first();

            if (!$existingLedger) {
                DB::table('ledgers')->insert([
                    'tenant_id' => $tenantId,
                    'name' => $ledgerName,
                    'balance_type' => 'debit',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $ledgerService->recordExpense($expenseRecord, 'cash');
        });

        return redirect()->route($this->userListRoute)
            ->with('success', 'Expense record created successfully');
    }

    public function edit($id)
    {
        $expenseRecord = ExpenseRecord::where('tenant_id', auth()->user()->tenant_id)->findOrFail($id);
        $expenseHeads = ExpenseHead::where('tenant_id', auth()->user()->tenant_id)->get();
        return view('Accountants.expense-records.form', compact('expenseRecord', 'expenseHeads'));
    }

    public function update(Request $request, $id)
    {
        $expenseRecord = ExpenseRecord::where('tenant_id', auth()->user()->tenant_id)->findOrFail($id);
        $tenantId = auth()->user()->tenant_id;
        $ledgerService = new LedgerService();

        $request->validate([
            'expense_head_id' => 'required|exists:expense_heads,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'description' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($request, $expenseRecord, $tenantId, $ledgerService) {
            ExpenseHead::where('tenant_id', $tenantId)
                ->findOrFail($request->expense_head_id);

            $expenseRecord->update($request->only(['expense_head_id', 'amount', 'date', 'description']));
            $expenseRecord->load('expenseHead');

            $ledgerService->repostExpense($expenseRecord, 'cash');
        });

        return redirect()->route($this->userListRoute)
            ->with('success', 'Expense record updated successfully');
    }

    public function destroy($id)
    {
        $expenseRecord = ExpenseRecord::where('tenant_id', auth()->user()->tenant_id)->findOrFail($id);
        $ledgerService = new LedgerService();

        DB::transaction(function () use ($expenseRecord, $ledgerService) {
            $ledgerService->forgetReference(ExpenseRecord::class, $expenseRecord->id, $expenseRecord->tenant_id);
            $expenseRecord->delete();
        });

        return redirect()->route($this->userListRoute)
            ->with('success', 'Expense record deleted successfully');
    }

    // Export Single Record as PDF Receipt
    public function receipt($id)
    {
        $expenseRecord = ExpenseRecord::with('expenseHead')->where('tenant_id', auth()->user()->tenant_id)->findOrFail($id);

        $pdf = Pdf::loadView('Accountants.expense-records.receipt', compact('expenseRecord'));
        return $pdf->download('expense-receipt-' . $expenseRecord->id . '.pdf');
    }

    // Export Multiple Records (Excel)
    public function export(Request $request)
    {
        $query = ExpenseRecord::with('expenseHead')->where('tenant_id', auth()->user()->tenant_id);

        if ($request->filled('ids')) {
            $query->whereIn('id', explode(',', $request->ids));
        }

        $records = $query->get();
        return Excel::download(new ExpenseRecordsExport($records), 'expense-records.xlsx');
    }
}
