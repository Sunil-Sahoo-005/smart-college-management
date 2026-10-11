<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamMark extends Model
{
    protected $fillable = [
        'tenant_id',
        'exam_id',
        'student_id',
        'marks_obtained',
        'grade_letter',
        'remarks',
        'mark_date',
        'entered_by_user_id',
    ];

    protected $casts = [
        'marks_obtained' => 'decimal:2',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function enteredBy()
    {
        return $this->belongsTo(User::class, 'entered_by_user_id');
    }
}