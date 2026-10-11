<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = [
        'tenant_id',
        'exam_code',
        'exam_type_id',
        'name',
        'class_id',
        'section_id',
        'subject_id',
        'academic_year_id',
        'term_id',
        'exam_date',
        'start_time',
        'end_time',
        'total_marks',
        'pass_marks',
        'stream_id',
        'course_id',
        'question_bank_id',
        'grading_system_id',
        'exam_mode',
    ];
protected $casts = [
    'exam_date' => 'date',
];
    public function stream()
    {
        return $this->belongsTo(Stream::class, 'stream_id');
    }
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function examType()
    {
        return $this->belongsTo(ExamType::class);
    }

    public function class()
    {
        return $this->belongsTo(Classe::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function gradeLevels()
    {
        return $this->hasMany(\App\Models\GradeLevel::class, 'grading_system_id', 'grading_system_id');
    }

    public function gradingSystem()
    {
        return $this->belongsTo(GradingSystem::class, 'grading_system_id');
    }
    public function centerAssignments()
{
    return $this->hasMany(
        ExamCenterAssignment::class
    );
}

public function centers()
{
    return $this->belongsToMany(
        ExamCenter::class,
        'exam_center_assignments',
        'exam_id',
        'center_id'
    );
}
}