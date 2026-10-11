<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccounts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankAccountsController extends Controller
{
    // ✅ Index
    public function index(Request $request)
    {
        $query = BankAccounts::where('tenant_id', Auth::user()->tenant_id);

        // 🔍 Filters
        if ($request->filled('bank_name')) {
            $query->where('bank_name', 'like', '%' . $request->bank_name . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $accounts = $query->latest()->paginate(20);

        return view('Accountants.bank_accounts.index', compact('accounts'));
    }

    // ✅ Create
    public function create()
    {
        return view('Accountants.bank_accounts.form');
    }

    // ✅ Store
   public function store(Request $request)
{
    $request->validate([
        'bank_name'      => 'required|string|max:255',
        'account_number' => 'nullable|numeric|digits_between:9,18|unique:bank_accounts,account_number',
        'ifsc_code'      => ['nullable', 'string', 'max:11', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
        'status'         => 'required|in:active,inactive',
    ], [
        'bank_name.required'      => 'Bank name is required.',
        'account_number.numeric'   => 'Account number must contain only digits.',
        'account_number.digits_between' => 'Account number must be between 9 and 18 digits.',
        'account_number.unique'   => 'This account number already exists.',
        'ifsc_code.regex'         => 'IFSC code format invalid. Example: SBIN0001234',
    ]);

    BankAccounts::create([
        'tenant_id'      => Auth::user()->tenant_id,
        'bank_name'      => $request->bank_name,
        'account_number' => $request->account_number,
        'ifsc_code'      => $request->ifsc_code ? strtoupper($request->ifsc_code) : null,
        'status'         => $request->status,
    ]);

    return redirect()->route('admin.bank_accounts.index')
        ->with('success', 'Bank account created successfully.');
}


    // ✅ Edit
    public function edit($id)
    {
        $account = BankAccounts::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        return view('Accountants.bank_accounts.form', compact('account'));
    }

    // ✅ Update
    public function update(Request $request, $id)
{
    $account = BankAccounts::where('tenant_id', Auth::user()->tenant_id)
        ->findOrFail($id);

    $request->validate([
        'bank_name'      => 'required|string|max:255',
        'account_number' => 'nullable|numeric|digits_between:9,18|unique:bank_accounts,account_number,' . $id,
        'ifsc_code'      => ['nullable', 'string', 'max:11', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
        'status'         => 'required|in:active,inactive',
    ], [
        'bank_name.required'      => 'Bank name is required.',
        'account_number.numeric'   => 'Account number must contain only digits.',
        'account_number.digits_between' => 'Account number must be between 9 and 18 digits.',
        'account_number.unique'   => 'This account number already exists.',
        'ifsc_code.regex'         => 'IFSC code format invalid. Example: SBIN0001234',
    ]);
    $account->update([
        'bank_name'      => $request->bank_name,
        'account_number' => $request->account_number,
        'ifsc_code'      => $request->ifsc_code ? strtoupper($request->ifsc_code) : null,
        'status'         => $request->status,
    ]);

    return redirect()->route('admin.bank_accounts.index')
        ->with('success', 'Bank account updated successfully.');
}
    // ✅ Destroy
    public function destroy($id)
    {
        $account = BankAccounts::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        $account->delete();

        return redirect()->route('admin.bank_accounts.index')
            ->with('success', 'Bank account deleted successfully.');
    }
}