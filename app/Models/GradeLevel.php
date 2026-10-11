<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeLevel extends Model
{
    protected $fillable = [
        'grading_system_id',
        'min_score',
        'max_score',
        'grade_letter',
        'remarks',
    ];

    public function gradingSystem()
    {
        return $this->belongsTo(GradingSystem::class);
    }
}
