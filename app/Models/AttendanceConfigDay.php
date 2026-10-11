<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceConfigDay extends Model
{
    protected $fillable = [
        'attendance_config_id',
        'day_of_week',
        'is_working_day',
        'office_start_time',
        'office_end_time',
        'full_day_minutes',
        'half_day_minutes',
    ];

    protected $casts = [
        'is_working_day' => 'boolean',
        'day_of_week' => 'integer',
        'full_day_minutes' => 'integer',
        'half_day_minutes' => 'integer',
    ];

    public function attendanceConfig()
    {
        return $this->belongsTo(AttendanceConfig::class);
    }


    const SUNDAY = 0;
    const MONDAY = 1;
    const TUESDAY = 2;
    const WEDNESDAY = 3;
    const THURSDAY = 4;
    const FRIDAY = 5;
    const SATURDAY = 6;

    public const DAYS = [
        0 => 'Sunday',
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
    ];
}
