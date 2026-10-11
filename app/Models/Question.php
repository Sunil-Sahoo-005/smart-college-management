<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_bank_id',
        'type',
        'question',
        'mark',
        'negative_marks',
        'explanation',
        'is_active',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'mark'           => 'decimal:2',
        'negative_marks' => 'decimal:2',
    ];

    public function questionBank()
    {
        return $this->belongsTo(QuestionBank::class);
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class);
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'exam_questions', 'question_id', 'exam_id');
    }
}
