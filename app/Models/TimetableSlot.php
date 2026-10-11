<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimetableSlot extends Model
{
    protected $fillable = [
         'tenant_id', 
        'timetable_id',
        'day_of_week',
        'period_id',
        'subject_id',
        'staff_id',
        'room_id',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
    ];

    public function timetable()
    {
        return $this->belongsTo(Timetable::class);
    }

    public function period()
    {
        return $this->belongsTo(AttendancePeriod::class, 'period_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
}
