<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\LeaveApprovalLog;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Staff;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaveRequestController extends Controller
{
    public function index(string $tenantKey)
    {
        $tenant = $this->resolveTenant($tenantKey);

        $leaveRequests = LeaveRequest::whereHas('staff', fn ($q) => $q->where('tenant_id', $tenant->id))
            ->with(['staff.designation', 'leaveType', 'approver', 'approvalLogs.actionedBy'])
            ->latest('id')
            ->paginate(10);

        return view('hr.leave-requests.index', compact('tenant', 'tenantKey', 'leaveRequests'));
    }

    public function create(string $tenantKey)
    {
        $tenant = $this->resolveTenant($tenantKey);
        $staffList = Staff::where('tenant_id', $tenant->id)->orderBy('first_name')->get();
        $leaveTypes = LeaveType::where('tenant_id', $tenant->id)->orderBy('name')->get();
        $leaveRequest = new LeaveRequest(['half_day' => 'none', 'status' => 'pending']);

        return view('hr.leave-requests.form', compact('tenant', 'tenantKey', 'staffList', 'leaveTypes', 'leaveRequest'));
    }

    public function store(Request $request, string $tenantKey)
    {
        $tenant = $this->resolveTenant($tenantKey);
        $data = $this->validatePayload($request, $tenant->id);

        $leaveRequest = new LeaveRequest($data);
        $leaveRequest->save();

        return redirect()
            ->route('hr.leave-requests.index', $tenantKey)
            ->with('success', 'Leave request created successfully.');
    }

    public function show(string $tenantKey, LeaveRequest $leaveRequest)
    {
        $this->authorizeTenant($tenantKey, $leaveRequest);

        $leaveRequest->load(['staff.designation', 'leaveType', 'approver', 'approvalLogs.actionedBy']);

        return view('hr.leave-requests.show', compact('tenantKey', 'leaveRequest'));
    }

    public function edit(string $tenantKey, LeaveRequest $leaveRequest)
    {
        $tenant = $this->resolveTenant($tenantKey);
        $this->authorizeTenant($tenantKey, $leaveRequest);

        $staffList = Staff::where('tenant_id', $tenant->id)->orderBy('first_name')->get();
        $leaveTypes = LeaveType::where('tenant_id', $tenant->id)->orderBy('name')->get();

        return view('hr.leave-requests.form', compact('tenant', 'tenantKey', 'staffList', 'leaveTypes', 'leaveRequest'));
    }

    public function update(Request $request, string $tenantKey, LeaveRequest $leaveRequest)
    {
        $tenant = $this->resolveTenant($tenantKey);
        $this->authorizeTenant($tenantKey, $leaveRequest);
        $data = $this->validatePayload($request, $tenant->id);

        $leaveRequest->update($data);

        return redirect()
            ->route('hr.leave-requests.index', $tenantKey)
            ->with('success', 'Leave request updated successfully.');
    }

    public function destroy(string $tenantKey, LeaveRequest $leaveRequest)
    {
        $this->authorizeTenant($tenantKey, $leaveRequest);
        $leaveRequest->delete();

        return redirect()
            ->route('hr.leave-requests.index', $tenantKey)
            ->with('success', 'Leave request deleted successfully.');
    }

    public function approve(Request $request, string $tenantKey, LeaveRequest $leaveRequest)
    {
        $this->authorizeTenant($tenantKey, $leaveRequest);

        $request->validate(['remarks' => ['nullable', 'string', 'max:500']]);

        DB::transaction(function () use ($leaveRequest, $request) {
            $leaveRequest->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            LeaveApprovalLog::create([
                'leave_request_id' => $leaveRequest->id,
                'actioned_by' => auth()->id(),
                'action' => 'approved',
                'remarks' => $request->remarks,
            ]);
        });

        return back()->with('success', 'Leave request approved.');
    }

    public function reject(Request $request, string $tenantKey, LeaveRequest $leaveRequest)
    {
        $this->authorizeTenant($tenantKey, $leaveRequest);

        $request->validate(['remarks' => ['nullable', 'string', 'max:500']]);

        DB::transaction(function () use ($leaveRequest, $request) {
            $leaveRequest->update(['status' => 'rejected']);

            LeaveApprovalLog::create([
                'leave_request_id' => $leaveRequest->id,
                'actioned_by' => auth()->id(),
                'action' => 'rejected',
                'remarks' => $request->remarks,
            ]);
        });

        return back()->with('success', 'Leave request rejected.');
    }

    private function validatePayload(Request $request, int $tenantId): array
    {
        $data = $request->validate([
            'staff_id' => ['required', 'exists:staff,id'],
            'leave_type_id' => ['required', 'exists:leave_types,id'],
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date', 'after_or_equal:from_date'],
            'total_days' => ['required', 'numeric', 'min:0.5'],
            'half_day' => ['required', 'in:none,first,second'],
            'reason' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'in:pending,approved,rejected,cancelled'],
        ]);

        abort_unless(Staff::where('tenant_id', $tenantId)->whereKey($data['staff_id'])->exists(), 403);
        abort_unless(LeaveType::where('tenant_id', $tenantId)->whereKey($data['leave_type_id'])->exists(), 403);

        if ($data['status'] !== 'pending') {
            $data['approved_by'] = auth()->id();
            $data['approved_at'] = now();
        }

        return $data;
    }

    private function resolveTenant(string $tenantKey): Tenant
    {
        if (preg_match('/-(\d+)(?:-|$)/', $tenantKey, $matches)) {
            return Tenant::where('status', 'active')->findOrFail((int) $matches[1]);
        }

        abort(404, 'Invalid tenant link');
    }

    private function authorizeTenant(string $tenantKey, LeaveRequest $leaveRequest): void
    {
        $tenant = $this->resolveTenant($tenantKey);
        abort_unless($leaveRequest->staff()->where('tenant_id', $tenant->id)->exists(), 403);
    }
}
