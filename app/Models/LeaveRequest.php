<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    protected $fillable = [
        'staff_id',
        'leave_type_id',
        'from_date',
        'to_date',
        'total_days',
        'half_day',
        'reason',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'approved_at' => 'datetime',
        'total_days' => 'decimal:2',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function approvalLogs()
    {
        return $this->hasMany(LeaveApprovalLog::class);
    }
}
