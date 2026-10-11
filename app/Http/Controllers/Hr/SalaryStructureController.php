<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\SalaryStructure;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SalaryStructureController extends Controller
{
    public function index(string $tenantKey)
    {
        $tenant = $this->resolveTenant($tenantKey);

        $salaryStructures = SalaryStructure::where('tenant_id', $tenant->id)
            ->latest('id')
            ->paginate(10);

        return view('hr.salary-structures.index', compact('tenant', 'tenantKey', 'salaryStructures'));
    }

    public function create(string $tenantKey)
    {
        $tenant = $this->resolveTenant($tenantKey);
        $salaryStructure = new SalaryStructure();

        return view('hr.salary-structures.form', compact('tenant', 'tenantKey', 'salaryStructure'));
    }

    public function store(Request $request, string $tenantKey)
    {
        $tenant = $this->resolveTenant($tenantKey);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'components_json' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['tenant_id'] = $tenant->id;
        $data['is_active'] = $request->boolean('is_active');
        $data['components'] = $this->parseComponents($request->input('components_json'));

        SalaryStructure::create($data);

        return redirect()
            ->route('hr.salary-structures.index', $tenantKey)
            ->with('success', 'Salary structure created successfully.');
    }

    public function edit(string $tenantKey, SalaryStructure $salaryStructure)
    {
        $tenant = $this->resolveTenant($tenantKey);
        $this->authorizeTenant($tenant->id, $salaryStructure);

        return view('hr.salary-structures.form', compact('tenant', 'tenantKey', 'salaryStructure'));
    }

    public function update(Request $request, string $tenantKey, SalaryStructure $salaryStructure)
    {
        $tenant = $this->resolveTenant($tenantKey);
        $this->authorizeTenant($tenant->id, $salaryStructure);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'components_json' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['components'] = $this->parseComponents($request->input('components_json'));

        $salaryStructure->update($data);

        return redirect()
            ->route('hr.salary-structures.index', $tenantKey)
            ->with('success', 'Salary structure updated successfully.');
    }

    public function destroy(string $tenantKey, SalaryStructure $salaryStructure)
    {
        $tenant = $this->resolveTenant($tenantKey);
        $this->authorizeTenant($tenant->id, $salaryStructure);

        $salaryStructure->delete();

        return redirect()
            ->route('hr.salary-structures.index', $tenantKey)
            ->with('success', 'Salary structure deleted successfully.');
    }

    private function resolveTenant(string $tenantKey): Tenant
    {
        if (preg_match('/-(\d+)(?:-|$)/', $tenantKey, $matches)) {
            return Tenant::where('status', 'active')->findOrFail((int) $matches[1]);
        }

        abort(404, 'Invalid tenant link');
    }

    private function authorizeTenant(int $tenantId, SalaryStructure $salaryStructure): void
    {
        abort_unless($salaryStructure->tenant_id === $tenantId, 403);
    }

    private function parseComponents(?string $json): ?array
    {
        if (! $json || trim($json) === '') {
            return null;
        }

        $decoded = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
            throw ValidationException::withMessages([
                'components_json' => 'Components must be valid JSON.',
            ]);
        }

        return $decoded;
    }
}
