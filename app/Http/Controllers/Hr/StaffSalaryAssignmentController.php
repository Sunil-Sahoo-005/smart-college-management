<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\SalaryStructure;
use App\Models\Staff;
use App\Models\StaffSalaryAssignment;
use App\Models\Tenant;
use Illuminate\Http\Request;

class StaffSalaryAssignmentController extends Controller
{
    public function index(string $tenantKey, Request $request)
    {
        $tenant = $this->resolveTenant($tenantKey);

        $assignments = StaffSalaryAssignment::where('tenant_id', $tenant->id)
            ->with(['staff.designation', 'staff.department', 'salaryStructure'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = trim($request->search);
                $q->whereHas('staff', function ($sq) use ($search) {
                    $sq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('employee_id', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('salary_structure_id'), fn ($q) => $q->where('salary_structure_id', $request->salary_structure_id))
            ->when($request->filled('status'), fn ($q) => $q->where('is_active', $request->status === 'active'))
            ->latest('effective_from')
            ->paginate(10)
            ->withQueryString();

        $salaryStructures = SalaryStructure::where('tenant_id', $tenant->id)->orderBy('name')->get();

        return view('hr.staff-salary-assignments.index', compact('tenant', 'tenantKey', 'assignments', 'salaryStructures'));
    }

    public function create(string $tenantKey)
    {
        $tenant = $this->resolveTenant($tenantKey);
        $assignment = new StaffSalaryAssignment(['effective_from' => now()->toDateString(), 'is_active' => true]);

        $salaryStructures = SalaryStructure::where('tenant_id', $tenant->id)->where('is_active', true)->orderBy('name')->get();

        return view('hr.staff-salary-assignments.form', compact('tenant', 'tenantKey', 'assignment', 'salaryStructures'));
    }

    public function edit(string $tenantKey, StaffSalaryAssignment $assignment)
    {
        $tenant = $this->resolveTenant($tenantKey);
        $this->authorizeTenant($tenant->id, $assignment);

        $assignment->load(['staff.designation', 'staff.department', 'salaryStructure']);
        $salaryStructures = SalaryStructure::where('tenant_id', $tenant->id)->where('is_active', true)->orderBy('name')->get();

        return view('hr.staff-salary-assignments.form', compact('tenant', 'tenantKey', 'assignment', 'salaryStructures'));
    }

    public function store(Request $request, string $tenantKey)
    {
        $tenant = $this->resolveTenant($tenantKey);
        $data = $this->validatePayload($request, $tenant->id);
        $data['tenant_id'] = $tenant->id;

        StaffSalaryAssignment::create($data);

        return redirect()->route('hr.staff-salary-assignments.index', $tenantKey)
            ->with('success', 'Salary assignment created successfully.');
    }

    public function update(Request $request, string $tenantKey, StaffSalaryAssignment $assignment)
    {
        $tenant = $this->resolveTenant($tenantKey);
        $this->authorizeTenant($tenant->id, $assignment);
        $data = $this->validatePayload($request, $tenant->id);
        $data['tenant_id'] = $tenant->id;

        $assignment->update($data);

        return redirect()->route('hr.staff-salary-assignments.index', $tenantKey)
            ->with('success', 'Salary assignment updated successfully.');
    }

    public function destroy(string $tenantKey, StaffSalaryAssignment $assignment)
    {
        $tenant = $this->resolveTenant($tenantKey);
        $this->authorizeTenant($tenant->id, $assignment);
        $assignment->delete();

        return redirect()->route('hr.staff-salary-assignments.index', $tenantKey)
            ->with('success', 'Salary assignment deleted successfully.');
    }

    public function lookup(string $tenantKey, Request $request)
    {
        $tenant = $this->resolveTenant($tenantKey);
        $search = trim($request->get('q', ''));

        if ($search === '') {
            return response()->json(['staff' => null]);
        }

        $staff = Staff::where('tenant_id', $tenant->id)
            ->where(function ($q) use ($search) {
                $q->where('employee_id', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
            })
            ->with(['designation', 'department'])
            ->first();

        if (! $staff) {
            return response()->json(['staff' => null]);
        }

        $assignment = StaffSalaryAssignment::where('tenant_id', $tenant->id)
            ->where('staff_id', $staff->id)
            ->where('is_active', true)
            ->with('salaryStructure')
            ->latest('effective_from')
            ->first();

        return response()->json([
            'staff' => [
                'id' => $staff->id,
                'employee_id' => $staff->employee_id,
                'name' => trim($staff->first_name . ' ' . $staff->last_name),
                'phone' => $staff->phone,
                'designation' => $staff->designation?->name,
                'department' => $staff->department?->name,
            ],
            'assignment' => $assignment ? [
                'id' => $assignment->id,
                'salary_structure_id' => $assignment->salary_structure_id,
                'salary_structure_name' => $assignment->salaryStructure?->name,
                'gross_salary' => $assignment->gross_salary,
                'effective_from' => $assignment->effective_from?->format('Y-m-d'),
                'effective_to' => $assignment->effective_to?->format('Y-m-d'),
                'is_active' => $assignment->is_active,
            ] : null,
        ]);
    }

    private function validatePayload(Request $request, int $tenantId): array
    {
        $data = $request->validate([
            'staff_id' => ['required', 'exists:staff,id'],
            'salary_structure_id' => ['required', 'exists:salary_structures,id'],
            'gross_salary' => ['required', 'numeric', 'min:0'],
            'effective_from' => ['required', 'date'],
            'effective_to' => ['nullable', 'date', 'after_or_equal:effective_from'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        abort_unless(Staff::where('tenant_id', $tenantId)->whereKey($data['staff_id'])->exists(), 403);
        abort_unless(SalaryStructure::where('tenant_id', $tenantId)->whereKey($data['salary_structure_id'])->exists(), 403);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function resolveTenant(string $tenantKey): Tenant
    {
        if (preg_match('/-(\d+)(?:-|$)/', $tenantKey, $matches)) {
            return Tenant::where('status', 'active')->findOrFail((int) $matches[1]);
        }

        abort(404, 'Invalid tenant link');
    }

    private function authorizeTenant(int $tenantId, StaffSalaryAssignment $assignment): void
    {
        abort_unless($assignment->tenant_id === $tenantId, 403);
    }
}
