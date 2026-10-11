<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\ExpenseHead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ExpenseHeadController extends Controller
{
    //
     protected $userListRoute = 'expense-heads.index';

    public function index()
    {
        $expenseHeads = ExpenseHead::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->paginate(10);

        return view('Accountants.expense_heads.index', compact('expenseHeads'));
    }

    public function create()
    {
        return view('Accountants.expense_heads.form');
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255|unique:expense_heads,name,' . $request->id,
        ];

        $messages = [
            'name.required' => 'Please enter expense head name!',
            'name.unique' => 'Expense head name already exists!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

       $expenseHead = ExpenseHead::updateOrCreate(
            ['id' => $request->id],
            [
                'tenant_id' => auth()->user()->tenant_id,
                'name' => trim($request->name),
            ]
        );
$tenantId = auth()->user()->tenant_id;
    $ledgerName = trim($expenseHead->name);

    $existingLedger = DB::table('ledgers')
        ->where('tenant_id', $tenantId)
        ->where('name', $ledgerName)
        ->first();

    if (!$existingLedger) {
        DB::table('ledgers')->insert([
            'tenant_id' => $tenantId,
            'name' => $ledgerName,
            'balance_type' => 'debit',  // ← EXPENSE = DEBIT
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
        $msg = $request->id ? 'Expense head updated successfully' : 'Expense head created successfully';
        
        return redirect()->route($this->userListRoute)
            ->with('success', $msg);
    }

    public function edit($id)
    {
        $expenseHead = ExpenseHead::where('tenant_id', auth()->user()->tenant_id)
            ->findOrFail($id);

        return view('Accountants.expense_heads.form', compact('expenseHead'));
    }

    public function destroy($id)
    {
        $expenseHead = ExpenseHead::where('tenant_id', auth()->user()->tenant_id)
            ->findOrFail($id);

        $expenseHead->delete();

        return redirect()->route($this->userListRoute)
            ->with('success', 'Expense head deleted successfully');
    }
}
