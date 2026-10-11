<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CenterDevice;
use App\Models\ExamCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ExamCenterController extends Controller
{
    protected $listRoute = 'admin.exam_center.index';

    // ══════════════════════════════════════
    // INDEX — center cards
    // ══════════════════════════════════════
    public function index()
    {
        $centers = ExamCenter::withCount('devices')
            ->where('tenant_id', Auth::user()->tenant_id)
            ->latest()
            ->get();

        return view('Academics.exam_center.index', compact('centers'));
    }

    // ══════════════════════════════════════
    // DEVICES — device cards for one center
    // ══════════════════════════════════════
    public function devices($centerId)
    {
        $center = ExamCenter::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($centerId);

        $devices = CenterDevice::where('center_id', $centerId)
            ->latest()
            ->get();

        return view('Academics.exam_center.devices', compact('center', 'devices'));
    }

    // ══════════════════════════════════════
    // CREATE
    // ══════════════════════════════════════
    public function create()
    {
        $center = new ExamCenter;
        return view('Academics.exam_center.form', compact('center'));
    }

    // ══════════════════════════════════════
    // EDIT
    // ══════════════════════════════════════
    public function edit($id)
    {
        $center = ExamCenter::where('tenant_id', Auth::user()->tenant_id)->findOrFail($id);
        return view('Academics.exam_center.form', compact('center'));
    }

    // ══════════════════════════════════════
    // STORE / UPDATE
    // ══════════════════════════════════════
    public function store(Request $request)
    {
        $isEdit   = !empty($request->get('id'));
        $centerId = $request->get('id');

        $rules = [
            'code'        => 'required|string|max:50|unique:exam_centers,code' . ($isEdit ? ",{$centerId}" : ''),
            'name'        => 'required|string|max:191',
            'username'    => 'required|string|max:100|unique:exam_centers,username' . ($isEdit ? ",{$centerId}" : ''),
            'description' => 'nullable|string',
            'status'      => 'required|in:active,inactive',
        ];

        if (!$isEdit) {
            $rules['password'] = 'required|string|min:6|confirmed';
        } else {
            $rules['password'] = 'nullable|string|min:6|confirmed';
        }

        $messages = [
            'code.required'      => 'Center code is required!',
            'code.unique'        => 'This code is already taken!',
            'name.required'      => 'Center name is required!',
            'username.required'  => 'Username is required!',
            'username.unique'    => 'This username is already taken!',
            'password.required'  => 'Password is required!',
            'password.min'       => 'Password must be at least 6 characters!',
            'password.confirmed' => 'Passwords do not match!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($isEdit) {
            $center = ExamCenter::where('tenant_id', Auth::user()->tenant_id)->findOrFail($centerId);
            $redirect = ['url' => redirect()->back(), 'msg' => 'Exam center updated successfully'];
        } else {
            $center   = new ExamCenter;
            $redirect = ['url' => redirect()->route($this->listRoute), 'msg' => 'Exam center created successfully'];
        }

        $center->tenant_id   = Auth::user()->tenant_id;
        $center->code        = strtoupper(trim($request->code));
        $center->name        = trim($request->name);
        $center->username    = trim($request->username);
        $center->description = $request->description;
        $center->status      = $request->status;

        if ($request->filled('password')) {
            $center->password = Hash::make($request->password);
        }

        try {
            $center->save();
            return $redirect['url']->with('success', $redirect['msg']);
        } catch (\Exception $e) {
            \Log::error('ExamCenter save error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Something went wrong: ' . $e->getMessage())
                ->withInput();
        }
    }

    // ══════════════════════════════════════
    // DESTROY
    // ══════════════════════════════════════
    public function destroy($id)
    {
        $center = ExamCenter::where('tenant_id', Auth::user()->tenant_id)->findOrFail($id);
        $center->delete();
        return redirect()->route($this->listRoute)->with('success', 'Exam center deleted successfully.');
    }

    // ══════════════════════════════════════
    // TOGGLE STATUS (AJAX)
    // ══════════════════════════════════════
    public function toggleStatus(Request $request, $id)
    {
        $center = ExamCenter::where('tenant_id', Auth::user()->tenant_id)->find($id);
        if (!$center) {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        $center->status = $center->status === 'active' ? 'inactive' : 'active';
        $center->save();

        return response()->json([
            'success' => true,
            'status'  => $center->status,
            'message' => 'Status updated successfully.',
        ]);
    }
}