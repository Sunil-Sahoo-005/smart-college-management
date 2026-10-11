<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    protected $fillable = [
        'tenant_id',
        'student_id',
        'enrollment_id',
        'class_id',
        'section_id',
        'date',
        'period_id',
        'status',
        'remarks',
        'marked_by_user_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function class()
    {
        return $this->belongsTo(Classe::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function period()
    {
        return $this->belongsTo(AttendancePeriod::class, 'period_id');
    }

    public function markedBy()
    {
        return $this->belongsTo(User::class, 'marked_by_user_id');
    }
    public function enrollment()
    {
        return $this->belongsTo(StudentClassEnrollment::class, 'enrollment_id');
    }
    public function timetableSlot()
    {
        return $this->hasOne(TimetableSlot::class, 'period_id', 'period_id')
            ->whereHas('timetable', function ($q) {
                $q->where('class_id', $this->class_id)
                  ->where('section_id', $this->section_id);
            });
    }
}
