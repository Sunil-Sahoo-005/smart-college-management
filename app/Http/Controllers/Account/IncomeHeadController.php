<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;

use App\Models\IncomeHead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class IncomeHeadController extends Controller
{

    protected $userListRoute = 'income-heads.index';

    public function index()
    {
        $incomeHeads = IncomeHead::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->paginate(10);

        return view('Accountants.income_heads.index', compact('incomeHeads'));
    }

    public function create()
    {
        return view('Accountants.income_heads.form');
    }

    // public function store(Request $request)
    // {
    //     $rules = [
    //         'name' => 'required|string|max:255|unique:income_heads,name,' . $request->id,
    //         'type' => 'required|in:fee,other',
    //     ];

    //     $messages = [
    //         'name.required' => 'Please enter income head name!',
    //         'name.unique' => 'Income head name already exists!',
    //         'type.required' => 'Please select income type!',
    //     ];

    //     $validator = Validator::make($request->all(), $rules, $messages);

    //     if ($validator->fails()) {
    //         return redirect()->back()
    //             ->withErrors($validator)
    //             ->withInput();
    //     }

    //     IncomeHead::updateOrCreate(
    //         ['id' => $request->id],
    //         [
    //             'tenant_id' => auth()->user()->tenant_id,
    //             'name' => trim($request->name),
    //             'type' => $request->type,
    //         ]
    //     );

    //     $msg = $request->id ? 'Income head updated successfully' : 'Income head created successfully';

    //     return redirect()->route($this->userListRoute)
    //         ->with('success', $msg);
    // }
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255|unique:income_heads,name,' . $request->id,
            'type' => 'required|in:fee,other',
        ];

        $messages = [
            'name.required' => 'Please enter income head name!',
            'name.unique' => 'Income head name already exists!',
            'type.required' => 'Please select income type!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $incomeHead = IncomeHead::updateOrCreate(
            ['id' => $request->id],
            [
                'tenant_id' => auth()->user()->tenant_id,
                'name' => trim($request->name),
                'type' => $request->type,
            ]
        );

        // ✅ AUTO LEDGER (Method 1 - BULLETPROOF)
        $tenantId = auth()->user()->tenant_id;
        $ledgerName = trim($incomeHead->name);

        $existingLedger = DB::table('ledgers')
            ->where('tenant_id', $tenantId)
            ->where('name', $ledgerName)
            ->first();

        if (!$existingLedger) {
            DB::table('ledgers')->insert([
                'tenant_id' => $tenantId,
                'name' => $ledgerName,
                'balance_type' => 'credit',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $msg = $request->id ? 'Income head updated successfully' : 'Income head + ledger created automatically!';

        return redirect()->route($this->userListRoute)->with('success', $msg);
    }

    public function edit($id)
    {
        $incomeHead = IncomeHead::where('tenant_id', auth()->user()->tenant_id)
            ->findOrFail($id);

        return view('Accountants.income_heads.form', compact('incomeHead'));
    }

    public function destroy($id)
    {
        $incomeHead = IncomeHead::where('tenant_id', auth()->user()->tenant_id)
            ->findOrFail($id);

        $incomeHead->delete();

        return redirect()->route($this->userListRoute)
            ->with('success', 'Income head deleted successfully');
    }
}
