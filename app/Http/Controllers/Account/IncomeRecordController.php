<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IncomeHead;
use App\Models\IncomeRecord;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\IncomeRecordsExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Services\LedgerService;

class IncomeRecordController extends Controller
{
    //
    protected $userListRoute = 'income-records.index';

    public function index(Request $request)
    {
        $query = IncomeRecord::with('incomeHead')
            ->where('tenant_id', auth()->user()->tenant_id);

        // Category Filter
        if ($request->filled('head_id')) {
            $query->where('income_head_id', $request->head_id);
        }

        // Date Filter
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $incomeRecords = $query->orderBy('date', 'desc')->paginate(20);
        $incomeHeads = IncomeHead::where('tenant_id', auth()->user()->tenant_id)->get();

        return view('Accountants.income-records.index', compact('incomeRecords', 'incomeHeads'));
    }

    public function create()
    {
        $incomeHeads = IncomeHead::where('tenant_id', auth()->user()->tenant_id)->get();
        return view('Accountants.income-records.form', compact('incomeHeads'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'income_head_id' => 'required|exists:income_heads,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'description' => 'nullable|string|max:500',
        ], [
            'income_head_id.required' => 'Please select income head!',
            'amount.required' => 'Please enter amount!',
            'date.required' => 'Please select date!',
        ]);
        $tenantId = auth()->user()->tenant_id;
        $paymentMode = $request->payment_mode ?? 'cash';
        $ledgerService = new LedgerService();

        DB::transaction(function () use ($request, $tenantId, $paymentMode, $ledgerService) {
            $incomeRecord = IncomeRecord::create([
                'tenant_id' => $tenantId,
                'income_head_id' => $request->income_head_id,
                'amount' => $request->amount,
                'date' => $request->date,
                'description' => $request->description,
            ]);

            $ledgerService->recordIncome($incomeRecord, $paymentMode);
        });

        return redirect()->route($this->userListRoute)
            ->with('success', 'Income record created successfully');
    }

    public function edit($id)
    {
        $incomeRecord = IncomeRecord::where('tenant_id', auth()->user()->tenant_id)->findOrFail($id);
        $incomeHeads = IncomeHead::where('tenant_id', auth()->user()->tenant_id)->get();
        return view('Accountants.income-records.form', compact('incomeRecord', 'incomeHeads'));
    }

    public function update(Request $request, $id)
    {
        $incomeRecord = IncomeRecord::where('tenant_id', auth()->user()->tenant_id)->findOrFail($id);
        $paymentMode = $request->payment_mode ?? 'cash';
        $ledgerService = new LedgerService();

        $request->validate([
            'income_head_id' => 'required|exists:income_heads,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'description' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($request, $incomeRecord, $paymentMode, $ledgerService) {
            $incomeRecord->update($request->only(['income_head_id', 'amount', 'date', 'description']));
            $incomeRecord->load('incomeHead');

            $ledgerService->repostIncome($incomeRecord, $paymentMode);
        });

        return redirect()->route($this->userListRoute)
            ->with('success', 'Income record updated successfully');
    }

    public function destroy($id)
    {
        $incomeRecord = IncomeRecord::where('tenant_id', auth()->user()->tenant_id)->findOrFail($id);
        $ledgerService = new LedgerService();

        DB::transaction(function () use ($incomeRecord, $ledgerService) {
            $ledgerService->forgetReference(IncomeRecord::class, $incomeRecord->id, $incomeRecord->tenant_id);
            $incomeRecord->delete();
        });

        return redirect()->route($this->userListRoute)
            ->with('success', 'Income record deleted successfully');
    }

    // Export Single Record as PDF Invoice
    public function invoice($id)
    {
        $incomeRecord = IncomeRecord::with('incomeHead')->where('tenant_id', auth()->user()->tenant_id)->findOrFail($id);

        $pdf = Pdf::loadView('Accountants.income-records.invoice', compact('incomeRecord'));
        return $pdf->download('income-receipt-' . $incomeRecord->id . '.pdf');
    }

    // Export Multiple Records (Excel)
    public function export(Request $request)
    {
        $query = IncomeRecord::with('incomeHead')->where('tenant_id', auth()->user()->tenant_id);

        if ($request->filled('ids')) {
            $query->whereIn('id', explode(',', $request->ids));
        }

        $records = $query->get();
        return Excel::download(new IncomeRecordsExport($records), 'income-records.xlsx');
    }
}
