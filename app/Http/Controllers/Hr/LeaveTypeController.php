<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\LeaveType;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveTypeController extends Controller
{
    public function index(string $tenantKey)
    {
        $tenant = $this->resolveTenant($tenantKey);

        $leaveTypes = LeaveType::where('tenant_id', $tenant->id)
            ->latest('id')
            ->paginate(10);

        return view('hr.leave-types.index', compact('tenant', 'leaveTypes', 'tenantKey'));
    }

    public function create(string $tenantKey)
    {
        $tenant = $this->resolveTenant($tenantKey);
        $leaveType = new LeaveType();

        return view('hr.leave-types.form', compact('tenant', 'leaveType', 'tenantKey'));
    }

    public function store(Request $request, string $tenantKey)
    {
        $tenant = $this->resolveTenant($tenantKey);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'allowed_days' => ['required', 'integer', 'min:0'],
            'carry_forward' => ['nullable', 'boolean'],
            'is_paid' => ['nullable', 'boolean'],
            'applicable_to' => ['required', 'in:all,teaching,non_teaching'],
        ]);

        $data['tenant_id'] = $tenant->id;
        $data['carry_forward'] = $request->boolean('carry_forward');
        $data['is_paid'] = $request->boolean('is_paid', true);

        LeaveType::create($data);

        return redirect()
            ->route('hr.leave-types.index', $tenantKey)
            ->with('success', 'Leave type created successfully.');
    }

    public function edit(string $tenantKey, LeaveType $leaveType)
    {
        $tenant = $this->resolveTenant($tenantKey);
        $this->ensureTenantOwnership($tenant->id, $leaveType);

        return view('hr.leave-types.form', compact('tenant', 'leaveType', 'tenantKey'));
    }

    public function update(Request $request, string $tenantKey, LeaveType $leaveType)
    {
        $tenant = $this->resolveTenant($tenantKey);
        $this->ensureTenantOwnership($tenant->id, $leaveType);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'allowed_days' => ['required', 'integer', 'min:0'],
            'carry_forward' => ['nullable', 'boolean'],
            'is_paid' => ['nullable', 'boolean'],
            'applicable_to' => ['required', 'in:all,teaching,non_teaching'],
        ]);

        $data['carry_forward'] = $request->boolean('carry_forward');
        $data['is_paid'] = $request->boolean('is_paid', true);

        $leaveType->update($data);

        return redirect()
            ->route('hr.leave-types.index', $tenantKey)
            ->with('success', 'Leave type updated successfully.');
    }

    public function destroy(string $tenantKey, LeaveType $leaveType)
    {
        $tenant = $this->resolveTenant($tenantKey);
        $this->ensureTenantOwnership($tenant->id, $leaveType);

        $leaveType->delete();

        return redirect()
            ->route('hr.leave-types.index', $tenantKey)
            ->with('success', 'Leave type deleted successfully.');
    }

    private function resolveTenant(string $tenantKey): Tenant
    {
        $tenantId = $this->extractTenantId($tenantKey);

        return Tenant::where('status', 'active')->findOrFail($tenantId);
    }

    private function ensureTenantOwnership(int $tenantId, LeaveType $leaveType): void
    {
        abort_unless($leaveType->tenant_id === $tenantId, 404, 'Leave type not found.');
    }

    private function extractTenantId(string $tenantKey): int
    {
        if (preg_match('/-(\d+)(?:-|$)/', $tenantKey, $matches)) {
            return (int) $matches[1];
        }

        abort(404, 'Invalid tenant link');
    }
}
