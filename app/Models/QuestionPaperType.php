<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionPaperType extends Model
{
    use HasFactory;

    protected $table = 'question_paper_types';

    protected $fillable = [
        'tenant_id',
        'question_bank_id',
        'type_name',
        'total_marks',
        'duration',
        'total_questions',
        'description',
    ];

    public function questionBank()
    {
        return $this->belongsTo(QuestionBank::class);
    }
}