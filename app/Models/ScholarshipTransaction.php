<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScholarshipTransaction extends Model
{
    protected $fillable = [
        'student_scholarship_id',
        'installment_id',
        'amount_used',
        'remarks',
    ];

    protected $casts = [
        'amount_used' => 'decimal:2',
    ];

    public function studentScholarship()
    {
        return $this->belongsTo(StudentScholarship::class);
    }
}
