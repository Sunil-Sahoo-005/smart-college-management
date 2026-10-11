<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CenterDevice;
use App\Models\ExamCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CenterDeviceController extends Controller
{
    protected $listRoute = 'admin.center_devices.index';

    // ══════════════════════════════════════
    // INDEX
    // ══════════════════════════════════════
    public function index(Request $request)
    {
        $centerId = $request->get('center_id');

        $query = CenterDevice::with('center')
            ->whereHas('center', fn($q) => $q->where('tenant_id', Auth::user()->tenant_id));

        if ($centerId) {
            $query->where('center_id', $centerId);
        }

        $devices = $query->latest()->get();

        $centers = ExamCenter::where('tenant_id', Auth::user()->tenant_id)
            ->orderBy('name')
            ->get();

        return view('Academics.center_devices.index', compact('devices', 'centers', 'centerId'));
    }

    // ══════════════════════════════════════
    // CREATE
    // ══════════════════════════════════════
    public function create(Request $request)
    {
        $device  = new CenterDevice;
        $centers = ExamCenter::where('tenant_id', Auth::user()->tenant_id)
            ->active()
            ->orderBy('name')
            ->get();

        $selectedCenterId = $request->get('center_id');

        return view('Academics.center_devices.form', compact('device', 'centers', 'selectedCenterId'));
    }

    // ══════════════════════════════════════
    // EDIT
    // ══════════════════════════════════════
    public function edit($id)
    {
        $device = CenterDevice::with('center')
            ->whereHas('center', fn($q) => $q->where('tenant_id', Auth::user()->tenant_id))
            ->find($id);

        if (empty($device)) abort(404);

        $centers = ExamCenter::where('tenant_id', Auth::user()->tenant_id)
            ->orderBy('name')
            ->get();

        $selectedCenterId = $device->center_id;

        return view('Academics.center_devices.form', compact('device', 'centers', 'selectedCenterId'));
    }

    // ══════════════════════════════════════
    // STORE / UPDATE
    // ══════════════════════════════════════
   public function store(Request $request)
    {
        $isEdit   = !empty($request->get('id'));
        $deviceId = $request->get('id');

        $rules = [
            'center_id'         => 'required|exists:exam_centers,id',
            'device_code'       => 'required|string|max:100|unique:center_devices,device_code' . ($isEdit ? ",{$deviceId}" : ''),
            'device_name'       => 'required|string|max:191',
            'device_identifier' => 'required|string|max:191|unique:center_devices,device_identifier' . ($isEdit ? ",{$deviceId}" : ''),
            'mac_address'       => 'nullable|string|max:100',
            'status'            => 'required|in:active,inactive',
        ];

        $messages = [
            'center_id.required'         => 'Please select a center!',
            'device_code.required'       => 'Device code is required!',
            'device_code.unique'         => 'This device code is already taken!',
            'device_name.required'       => 'Device name is required!',
            'device_identifier.required' => 'Device identifier is required!',
            'device_identifier.unique'   => 'This device identifier is already registered!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($isEdit) {
            $device = CenterDevice::whereHas('center', fn($q) => $q->where('tenant_id', Auth::user()->tenant_id))
                ->find($deviceId);
            if (empty($device)) abort(404);
            $redirect = ['msg' => 'Device updated successfully'];
        } else {
            $device   = new CenterDevice;
            $redirect = ['msg' => 'Device registered successfully'];
        }

        $device->center_id         = $request->center_id;
        $device->device_code       = strtoupper(trim($request->device_code));
        $device->device_name       = trim($request->device_name);
        $device->device_identifier = trim($request->device_identifier);
        $device->mac_address       = $request->mac_address ? trim($request->mac_address) : null;
        $device->status            = $request->status;

        try {
            $device->save();
            return redirect()
                ->route('admin.exam_center.devices', $device->center_id)
                ->with('success', $redirect['msg']);
        } catch (\Exception $e) {
            \Log::error('CenterDevice save error: ' . $e->getMessage());
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
    $device = CenterDevice::whereHas('center', fn($q) => $q->where('tenant_id', Auth::user()->tenant_id))
        ->find($id);
    if (empty($device)) abort(404);

    $centerId = $device->center_id;   
    $device->delete();

    
    return redirect()->route('admin.exam_center.devices', $centerId)
        ->with('success', 'Device deleted successfully.');
}


    // ══════════════════════════════════════
    // TOGGLE STATUS (AJAX)
    // ══════════════════════════════════════
    public function toggleStatus(Request $request, $id)
    {
        $device = CenterDevice::whereHas('center', fn($q) => $q->where('tenant_id', Auth::user()->tenant_id))
            ->find($id);

        if (empty($device)) {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        $device->status = $device->status === 'active' ? 'inactive' : 'active';
        $device->save();

        return response()->json([
            'success' => true,
            'status'  => $device->status,
            'message' => 'Status updated successfully.',
        ]);
    }
}