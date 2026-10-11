<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PracticalTimetableSlot extends Model
{
    protected $fillable = [
        'tenant_id',
        'practical_timetable_id',
        'attendance_period_id',
        'room_id',
        'staff_id',
        'subject_id',
        'day_of_week',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
    ];

    public function timetable()
    {
        return $this->belongsTo(PracticalTimetable::class, 'practical_timetable_id');
    }

    public function period()
    {
        return $this->belongsTo(AttendancePeriod::class, 'attendance_period_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
