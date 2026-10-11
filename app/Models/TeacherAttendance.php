<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherAttendance extends Model
{
    protected $table = 'teacher_attendance';

    protected $fillable = [
        'tenant_id',
        'staff_id',
        'date',
        'period_id',
        'status',
        'remarks',
        'marked_by_user_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function period()
    {
        return $this->belongsTo(AttendancePeriod::class, 'period_id');
    }

    public function markedBy()
    {
        return $this->belongsTo(User::class, 'marked_by_user_id');
    }
}
