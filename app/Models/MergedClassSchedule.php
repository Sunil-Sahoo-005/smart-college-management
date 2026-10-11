<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MergedClassSchedule extends Model
{
    //
     protected $fillable = [
        'tenant_id',
        'academic_year_id',
        'schedule_date',
        'period_id',
        'class_combinations',
        'subject_id',
        'staff_id',
        'room_id',
    ];

    protected $casts = [
        'schedule_date' => 'date',
        'class_combinations' => 'array', // Auto JSON decode/encode
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function period()
    {
        return $this->belongsTo(AttendancePeriod::class, 'period_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
