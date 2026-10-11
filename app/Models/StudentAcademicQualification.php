<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAcademicQualification extends Model
{
    protected $table = 'student_academic_qualifications';

    protected $fillable = [
        'student_id',
        'qualification_level',
        'board_university',
        'school_college',
        'passing_year',
        'percentage',
        'grade',
        'stream',
        'roll_number',
        'uploaded_by',
        'certificate_path',
    ];

    protected $casts = [
        'passing_year' => 'integer',
        'percentage'   => 'decimal:2',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}