<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\ExpenseRecord;
use App\Models\FeePayment;
use App\Models\IncomeRecord;
use App\Models\Ledger;
use App\Models\LedgerEntry;
use App\Models\SalaryRecord;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LedgerController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        $search = $request->get('search');

        $query = Ledger::where('tenant_id', $tenantId)
            ->withSum(['entries as debit_total' => fn ($q) => $q->where('type', 'debit')], 'amount')
            ->withSum(['entries as credit_total' => fn ($q) => $q->where('type', 'credit')], 'amount');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('balance_type')) {
            $query->where('balance_type', $request->balance_type);
        }

        $ledgers = $query->orderBy('balance_type')->orderBy('name')->paginate(20)->appends($request->all());

        $summary = Ledger::where('tenant_id', $tenantId)
            ->selectRaw('
                SUM(CASE WHEN balance_type = "debit" THEN COALESCE((SELECT SUM(amount) FROM ledger_entries WHERE ledger_entries.ledger_id = ledgers.id AND type = "debit"), 0) ELSE 0 END) as total_assets,
                SUM(CASE WHEN balance_type = "credit" THEN COALESCE((SELECT SUM(amount) FROM ledger_entries WHERE ledger_entries.ledger_id = ledgers.id AND type = "credit"), 0) ELSE 0 END) as total_liabilities
            ')
            ->first();

        return view('Accountants.ledgers.index', compact('ledgers', 'search', 'summary'));
    }

    public function show(Request $request, $id)
    {
        $tenantId = auth()->user()->tenant_id;

        $ledger = Ledger::where('tenant_id', $tenantId)
            ->withSum(['entries as debit_total' => fn ($q) => $q->where('type', 'debit')], 'amount')
            ->withSum(['entries as credit_total' => fn ($q) => $q->where('type', 'credit')], 'amount')
            ->findOrFail($id);

        $entryDateExpression = DB::raw('COALESCE(entry_date, DATE(created_at))');

        $entryQuery = LedgerEntry::where('tenant_id', $tenantId)
            ->where('ledger_id', $id)
            ->with('ledger')
            ->orderBy('entry_date')
            ->orderBy('created_at');

        if ($request->filled('from')) {
            $entryQuery->whereDate($entryDateExpression, '>=', $request->from);
        }
        if ($request->filled('to')) {
            $entryQuery->whereDate($entryDateExpression, '<=', $request->to);
        }
        if ($request->filled('type')) {
            $entryQuery->where('type', $request->type);
        }

        $entries = $entryQuery->paginate(25)->appends($request->all());

        $openingBalance = 0;
        if ($request->filled('from')) {
            $openingBalance = LedgerEntry::where('tenant_id', $tenantId)
                ->where('ledger_id', $id)
                ->whereDate($entryDateExpression, '<', $request->from)
                ->selectRaw("
                    SUM(CASE WHEN type = 'debit' THEN amount ELSE 0 END) -
                    SUM(CASE WHEN type = 'credit' THEN amount ELSE 0 END) as balance
                ")
                ->value('balance') ?? 0;
        }

        return view('Accountants.ledgers.show', compact('ledger', 'entries', 'openingBalance'));
    }

    public function create()
    {
        return view('Accountants.ledgers.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'balance_type' => 'required|in:debit,credit',
        ]);

        $tenantId = auth()->user()->tenant_id;

        $exists = Ledger::where('tenant_id', $tenantId)
            ->where('name', $request->name)
            ->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'A ledger with this name already exists.'])->withInput();
        }

        Ledger::create([
            'tenant_id' => $tenantId,
            'name' => $request->name,
            'balance_type' => $request->balance_type,
        ]);

        return redirect()->route('ledgers.index')->with('success', 'Ledger account created.');
    }

    public function edit($id)
    {
        $ledger = Ledger::where('tenant_id', auth()->user()->tenant_id)->findOrFail($id);
        return view('Accountants.ledgers.form', compact('ledger'));
    }

    public function update(Request $request, $id)
    {
        $ledger = Ledger::where('tenant_id', auth()->user()->tenant_id)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'balance_type' => 'required|in:debit,credit',
        ]);

        $ledger->update([
            'name' => $request->name,
            'balance_type' => $request->balance_type,
        ]);

        return redirect()->route('ledgers.index')->with('success', 'Ledger updated.');
    }

    public function destroy($id)
    {
        $ledger = Ledger::where('tenant_id', auth()->user()->tenant_id)->findOrFail($id);

        if ($ledger->entries()->exists()) {
            return back()->with('error', 'Cannot delete a ledger that has entries.');
        }

        $ledger->delete();
        return redirect()->route('ledgers.index')->with('success', 'Ledger deleted.');
    }

    public function journalCreate()
    {
        $tenantId = auth()->user()->tenant_id;
        $ledgers = Ledger::where('tenant_id', $tenantId)->orderBy('name')->get();
        return view('Accountants.ledgers.journal', compact('ledgers'));
    }

    public function journalStore(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $request->validate([
            'description' => 'required|string|max:500',
            'entries' => 'required|array|min:2',
            'entries.*.ledger_id' => [
                'required',
                Rule::exists('ledgers', 'id')->where(fn ($query) => $query->where('tenant_id', $tenantId)),
            ],
            'entries.*.type' => 'required|in:debit,credit',
            'entries.*.amount' => 'required|numeric|min:0.01',
        ]);

        $totalDebit = collect($request->entries)->where('type', 'debit')->sum('amount');
        $totalCredit = collect($request->entries)->where('type', 'credit')->sum('amount');

        if (round($totalDebit, 2) !== round($totalCredit, 2)) {
            return back()
                ->withInput()
                ->with('error', "Journal entry is out of balance. Debits Rs {$totalDebit} != Credits Rs {$totalCredit}.");
        }

        DB::transaction(function () use ($request, $tenantId) {
            foreach ($request->entries as $entry) {
                LedgerEntry::create([
                    'tenant_id' => $tenantId,
                    'ledger_id' => $entry['ledger_id'],
                    'amount' => $entry['amount'],
                    'type' => $entry['type'],
                    'reference_type' => 'manual_journal',
                    'reference_id' => 0,
                    'description' => $request->description,
                    'entry_date' => now()->toDateString(),
                ]);
            }
        });

        return redirect()->route('ledgers.index')->with('success', 'Journal entry posted successfully.');
    }

    public function sync()
    {
        $tenantId = auth()->user()->tenant_id;
        $created = 0;
        $ledgerService = new LedgerService();

        DB::transaction(function () use ($tenantId, &$created, $ledgerService) {
            $payments = FeePayment::where('tenant_id', $tenantId)
                ->withCount('ledgerEntries')
                ->having('ledger_entries_count', '!=', 2)
                ->get();

            foreach ($payments as $payment) {
                $ledgerService->forgetReference(FeePayment::class, $payment->id, $tenantId);
                $ledgerService->recordFeePayment($payment);
                $created += 2;
            }

            $incomes = IncomeRecord::where('tenant_id', $tenantId)
                ->with('incomeHead')
                ->withCount('ledgerEntries')
                ->having('ledger_entries_count', '!=', 2)
                ->get();

            foreach ($incomes as $income) {
                $ledgerService->forgetReference(IncomeRecord::class, $income->id, $tenantId);
                $ledgerService->recordIncome($income, 'cash');
                $created += 2;
            }

            $expenses = ExpenseRecord::where('tenant_id', $tenantId)
                ->with('expenseHead')
                ->withCount('ledgerEntries')
                ->having('ledger_entries_count', '!=', 2)
                ->get();

            foreach ($expenses as $expense) {
                $ledgerService->forgetReference(ExpenseRecord::class, $expense->id, $tenantId);
                $ledgerService->recordExpense($expense, 'cash');
                $created += 2;
            }

            $salaries = SalaryRecord::where('tenant_id', $tenantId)
                ->where('status', 'paid')
                ->with('staff')
                ->withCount('ledgerEntries')
                ->having('ledger_entries_count', '!=', 2)
                ->get();

            foreach ($salaries as $salary) {
                $ledgerService->forgetReference(SalaryRecord::class, $salary->id, $tenantId);
                $ledgerService->recordSalary($salary);
                $created += 2;
            }
        });

        return redirect()->route('ledgers.index')
            ->with('success', "Sync complete - {$created} ledger entries created.");
    }
}
