<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveApprovalLog extends Model
{
    protected $fillable = [
        'leave_request_id',
        'actioned_by',
        'action',
        'remarks',
    ];

    public function leaveRequest()
    {
        return $this->belongsTo(LeaveRequest::class);
    }

    public function actionedBy()
    {
        return $this->belongsTo(User::class, 'actioned_by');
    }
}
