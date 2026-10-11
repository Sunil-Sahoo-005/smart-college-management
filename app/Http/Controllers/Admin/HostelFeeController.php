<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HostelFee;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HostelFeeController extends Controller
{
    protected $listRoute = 'admin.hostelfees.index';

    // 🔹 INDEX
    public function index()
    {
        $fees = HostelFee::with('student')
            ->where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('id','desc')
            ->get();

        return view('hostel.hostelfees.index', compact('fees'));
    }

    // 🔹 CREATE
    public function create()
    {
        $hostelfee = null;
        $students = Student::where('tenant_id', auth()->user()->tenant_id)->get();

        return view('hostel.hostelfees.form', compact('hostelfee','students'));
    }

    // 🔹 EDIT
    public function edit($id)
    {
        $hostelfee = HostelFee::where('id', $id)
            ->where('tenant_id', auth()->user()->tenant_id)
            ->firstOrFail();

        $students = Student::where('tenant_id', auth()->user()->tenant_id)->get();

        return view('hostel.hostelfees.form', compact('hostelfee','students'));
    }

    // 🔹 STORE (CREATE + UPDATE)
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'amount'     => 'required|numeric|min:0',
            'due_date'   => 'required|date',
            'status'     => 'required|in:paid,unpaid',
        ]);

        DB::transaction(function () use ($request) {

            if ($request->id) {
                $fee = HostelFee::where('id', $request->id)
                    ->where('tenant_id', auth()->user()->tenant_id)
                    ->firstOrFail();
            } else {
                $fee = new HostelFee();
                $fee->tenant_id = auth()->user()->tenant_id;
            }

            $fee->student_id = $request->student_id;
            $fee->amount     = $request->amount;
            $fee->due_date   = $request->due_date;
            $fee->status     = $request->status;

            $fee->save();
        });

        return redirect()->route($this->listRoute)
            ->with('success','Hostel fee saved successfully');
    }

    // 🔹 DELETE
    public function destroy($id)
    {
        HostelFee::where('id', $id)
            ->where('tenant_id', auth()->user()->tenant_id)
            ->firstOrFail()
            ->delete();

        return back()->with('success','Hostel fee deleted successfully');
    }
}
