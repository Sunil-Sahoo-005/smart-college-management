<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    //
    protected $fillable = [
        'tenant_id', 'academic_year_id', 'student_id',
        'from_stream_name', 'from_class_name', 'from_section_name',
        'to_stream_name', 'to_class_name', 'to_section_name',
        'gpa_required', 'student_gpa', 'remarks', 'status'
    ];

    protected $casts = [
        'gpa_required' => 'decimal:2',
        'student_gpa' => 'decimal:2',
    ];
     public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    // Helper: Full from description
    public function getFromFullAttribute()
    {
        return trim("{$this->from_stream_name} {$this->from_class_name} {$this->from_section_name}");
    }

    // Helper: Full to description
    public function getToFullAttribute()
    {
        return trim("{$this->to_stream_name} {$this->to_class_name} {$this->to_section_name}");
    }
    public function class()
{
    return $this->belongsTo(Classe::class);
}

public function section()
{
    return $this->belongsTo(Section::class);
}

public function stream()
{
    return $this->belongsTo(Stream::class);
}

}
