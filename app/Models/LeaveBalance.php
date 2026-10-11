<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    protected $fillable = [
        'staff_id',
        'leave_type_id',
        'year',
        'allocated_days',
        'used_days',
        'carry_forward_days',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }
}
