<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    protected $fillable = [
        'tenant_id',
        'course_id',
        'stream_id',
        'class_id',
        'section_id',
        'academic_year_id',
        'entry_type',
        'name',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function stream()
    {
        return $this->belongsTo(Stream::class, 'stream_id');
    }
    public function class()
    {
        return $this->belongsTo(Classe::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function academicYears()
    {
        return $this->belongsToMany(AcademicYear::class, 'timetable_academic_years');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function slots()
    {
        return $this->hasMany(TimetableSlot::class);
    }
}
