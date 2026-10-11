<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    //
    protected $fillable = [
        'tenant_id',
        'course_id',
        'stream_id',
        'name',
        'year_number',
        'class_id',
        'academic_year_id',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function stream()
    {
        return $this->belongsTo(Stream::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function class()
    {
        return $this->belongsTo(Classe::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function items()
    {
        return $this->hasMany(FeeStructureItem::class);
    }
 // FeeStructure.php
public function courseStage()
{
    return $this->belongsTo(CourseStage::class);
}

    public function getYearLabelAttribute(): string
    {
        return $this->year_number ? 'Year ' . $this->year_number : 'General';
    }

    public function studentFeeAssignments()
    {
        return $this->hasMany(StudentFeeStructure::class, 'fee_structure_id');
    }
}
