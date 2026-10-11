<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamCenterAssignment extends Model
{
    protected $fillable = [
        'exam_id',
        'center_id',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function center()
    {
        return $this->belongsTo(ExamCenter::class, 'center_id');
    }
}