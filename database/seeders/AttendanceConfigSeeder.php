<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\AttendanceConfig;
use App\Models\AttendanceConfigDay;
use Illuminate\Database\Seeder;

class AttendanceConfigSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {

            $config = AttendanceConfig::updateOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'name' => 'Default Office Timing',
                ],
                [
                    'late_after_minutes' => 15,
                    'early_exit_minutes' => 15,
                    'is_active' => true,
                ]
            );

            $days = [
                [
                    'day_of_week' => 0, // Sunday
                    'is_working_day' => false,
                    'office_start_time' => null,
                    'office_end_time' => null,
                    'full_day_minutes' => null,
                    'half_day_minutes' => null,
                ],
                [
                    'day_of_week' => 1, // Monday
                    'is_working_day' => true,
                    'office_start_time' => '09:00:00',
                    'office_end_time' => '17:00:00',
                    'full_day_minutes' => 480,
                    'half_day_minutes' => 240,
                ],
                [
                    'day_of_week' => 2, // Tuesday
                    'is_working_day' => true,
                    'office_start_time' => '09:00:00',
                    'office_end_time' => '17:00:00',
                    'full_day_minutes' => 480,
                    'half_day_minutes' => 240,
                ],
                [
                    'day_of_week' => 3, // Wednesday
                    'is_working_day' => true,
                    'office_start_time' => '09:00:00',
                    'office_end_time' => '17:00:00',
                    'full_day_minutes' => 480,
                    'half_day_minutes' => 240,
                ],
                [
                    'day_of_week' => 4, // Thursday
                    'is_working_day' => true,
                    'office_start_time' => '09:00:00',
                    'office_end_time' => '17:00:00',
                    'full_day_minutes' => 480,
                    'half_day_minutes' => 240,
                ],
                [
                    'day_of_week' => 5, // Friday
                    'is_working_day' => true,
                    'office_start_time' => '09:00:00',
                    'office_end_time' => '17:00:00',
                    'full_day_minutes' => 480,
                    'half_day_minutes' => 240,
                ],
                [
                    'day_of_week' => 6, // Saturday
                    'is_working_day' => true,
                    'office_start_time' => '09:00:00',
                    'office_end_time' => '13:00:00',
                    'full_day_minutes' => 240,
                    'half_day_minutes' => 120,
                ],
            ];

            foreach ($days as $day) {

                AttendanceConfigDay::updateOrCreate(
                    [
                        'attendance_config_id' => $config->id,
                        'day_of_week' => $day['day_of_week'],
                    ],
                    [
                        'is_working_day' => $day['is_working_day'],
                        'office_start_time' => $day['office_start_time'],
                        'office_end_time' => $day['office_end_time'],
                        'full_day_minutes' => $day['full_day_minutes'],
                        'half_day_minutes' => $day['half_day_minutes'],
                    ]
                );
            }
        }
    }
}