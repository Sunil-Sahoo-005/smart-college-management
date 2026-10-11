<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'tenant_id',
        'staff_id',
        'attendance_date',
        'check_in',
        'check_out',
        'working_minutes',
        'is_late',
        'status',
        'source',
        'remark',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'is_late' => 'boolean',
        'working_minutes' => 'integer',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
