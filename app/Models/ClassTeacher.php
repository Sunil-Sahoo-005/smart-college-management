<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassTeacher extends Model
{
    protected $fillable = [
        'tenant_id',
        'class_id',
        'section_id',
        'staff_id',
        'academic_year_id',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function class()
    {
        return $this->belongsTo(Classe::class, 'class_id');
    }
    public function stream()
    {
        return $this->belongsTo(Stream::class, 'stream_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }
}
