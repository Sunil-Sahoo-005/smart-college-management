<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\AttendanceConfig;
use App\Models\AttendanceConfigDay;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceConfigController extends Controller
{
    public function edit(string $tenantKey)
    {
        $tenant = $this->resolveTenant($tenantKey);
        $config = AttendanceConfig::firstOrCreate(
            ['tenant_id' => $tenant->id],
            ['name' => 'Default', 'late_after_minutes' => 15, 'early_exit_minutes' => 15, 'is_active' => true]
        );
        $config->load('days');

        $days = $this->dayDefinitions();

        return view('hr.attendance-config.edit', compact('tenant', 'tenantKey', 'config', 'days'));
    }

    public function update(Request $request, string $tenantKey)
    {
        $tenant = $this->resolveTenant($tenantKey);
        $config = AttendanceConfig::firstOrCreate(
            ['tenant_id' => $tenant->id],
            ['name' => 'Default', 'late_after_minutes' => 15, 'early_exit_minutes' => 15, 'is_active' => true]
        );

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'late_after_minutes' => ['required', 'integer', 'min:0'],
            'early_exit_minutes' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'days' => ['required', 'array', 'size:7'],
            'days.*.is_working_day' => ['nullable', 'boolean'],
            'days.*.office_start_time' => ['nullable', 'date_format:H:i'],
            'days.*.office_end_time' => ['nullable', 'date_format:H:i'],
            'days.*.full_day_minutes' => ['nullable', 'integer', 'min:1'],
            'days.*.half_day_minutes' => ['nullable', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($config, $request, $data) {
            $config->update([
                'name' => $data['name'],
                'late_after_minutes' => $data['late_after_minutes'],
                'early_exit_minutes' => $data['early_exit_minutes'],
                'is_active' => $request->boolean('is_active'),
            ]);

            foreach ($this->dayDefinitions() as $day) {
                $payload = $data['days'][$day['key']] ?? [];

                AttendanceConfigDay::updateOrCreate(
                    [
                        'attendance_config_id' => $config->id,
                        'day_of_week' => $day['index'],
                    ],
                    [
                        'is_working_day' => $request->boolean("days.{$day['key']}.is_working_day"),
                        'office_start_time' => $payload['office_start_time'] ?? null,
                        'office_end_time' => $payload['office_end_time'] ?? null,
                        'full_day_minutes' => $payload['full_day_minutes'] ?? null,
                        'half_day_minutes' => $payload['half_day_minutes'] ?? null,
                    ]
                );
            }
        });

        return redirect()
            ->route('hr.attendance-config.edit', $tenantKey)
            ->with('success', 'Attendance configuration updated successfully.');
    }

    private function resolveTenant(string $tenantKey): Tenant
    {
        if (preg_match('/-(\d+)(?:-|$)/', $tenantKey, $matches)) {
            return Tenant::where('status', 'active')->findOrFail((int) $matches[1]);
        }

        abort(404, 'Invalid tenant link');
    }

    private function dayDefinitions(): array
    {
        return [
            ['index' => 0, 'key' => 'sunday', 'label' => 'Sunday'],
            ['index' => 1, 'key' => 'monday', 'label' => 'Monday'],
            ['index' => 2, 'key' => 'tuesday', 'label' => 'Tuesday'],
            ['index' => 3, 'key' => 'wednesday', 'label' => 'Wednesday'],
            ['index' => 4, 'key' => 'thursday', 'label' => 'Thursday'],
            ['index' => 5, 'key' => 'friday', 'label' => 'Friday'],
            ['index' => 6, 'key' => 'saturday', 'label' => 'Saturday'],
        ];
    }
}
