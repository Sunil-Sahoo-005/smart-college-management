<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentClassEnrollment extends Model
{
    protected $fillable = [
        'tenant_id',
        'academic_year_id',
        'course_id',
        'student_id',
        'class_id',
        'class_stream_id',
        'section_id',
        'stream_id',
        'student_type',
        'residence_type',
        'dateof_admission',
        'date_of_leaving', 
        'status',
        
    ];

    protected $casts = [
        'dateof_admission' => 'date',
        'date_of_leaving' => 'date', 
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function class()
    {
        return $this->belongsTo(Classe::class, 'class_id');
    }

    public function classStream()
    {
        return $this->belongsTo(ClassStreamMapping::class, 'class_stream_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function stream()
    {
        return $this->belongsTo(Stream::class, 'stream_id');
    }

    // ✅ Add these accessor methods for better display in views

    /**
     * Get student type label
     */
    public function getStudentTypeLabelAttribute()
    {
        $types = [
            'regular' => 'Regular',
            'lateral' => 'Lateral Entry',
        ];
        
        return $types[$this->student_type] ?? 'Not Set';
    }

    /**
     * Get residence type label
     */
    public function getResidenceTypeLabelAttribute()
    {
        $types = [
            'day_scholar' => 'Day Scholar',
            'hosteller' => 'Hosteller',
        ];
        
        return $types[$this->residence_type] ?? 'Not Set';
    }

    /**
     * Get student type badge HTML
     */
    public function getStudentTypeBadgeAttribute()
    {
        $colors = [
            'regular' => 'secondary',
            'lateral' => 'warning',
        ];
        
        $color = $colors[$this->student_type] ?? 'secondary';
        $label = $this->student_type_label;
        
        return "<span class='badge bg-{$color}'>{$label}</span>";
    }

    /**
     * Get residence type badge HTML
     */
    public function getResidenceTypeBadgeAttribute()
    {
        $colors = [
            'day_scholar' => 'info',
            'hosteller' => 'success',
        ];
        
        $color = $colors[$this->residence_type] ?? 'secondary';
        $label = $this->residence_type_label;
        
        return "<span class='badge bg-{$color}'>{$label}</span>";
    }

    /**
     * Check if student is a hosteller
     */
    public function isHosteller()
    {
        return $this->residence_type === 'hosteller';
    }

    /**
     * Check if student is a day scholar
     */
    public function isDayScholar()
    {
        return $this->residence_type === 'day_scholar';
    }

    /**
     * Check if student is lateral entry
     */
    public function isLateralEntry()
    {
        return $this->student_type === 'lateral';
    }

    /**
     * Check if student is regular
     */
    public function isRegular()
    {
        return $this->student_type === 'regular';
    }

    /**
     * Get enrollment status label
     */
    public function getStatusLabelAttribute()
    {
        $statuses = [
            'active' => 'Active',
            'promoted' => 'Promoted',
            'transferred' => 'Transferred',
            'completed' => 'Completed',
        ];
        
        return $statuses[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Get enrollment status badge
     */
    public function getStatusBadgeAttribute()
    {
        $colors = [
            'active' => 'success',
            'promoted' => 'primary',
            'transferred' => 'warning',
            'completed' => 'info',
        ];
        
        $color = $colors[$this->status] ?? 'secondary';
        
        return "<span class='badge bg-{$color}'>{$this->status_label}</span>";
    }
}
