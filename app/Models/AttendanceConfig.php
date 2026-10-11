<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceConfig extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'late_after_minutes',
        'early_exit_minutes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'late_after_minutes' => 'integer',
        'early_exit_minutes' => 'integer',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function days()
    {
        return $this->hasMany(AttendanceConfigDay::class)->orderBy('day_of_week');
    }
}
