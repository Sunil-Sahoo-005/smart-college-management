<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentScholarship extends Model
{
    protected $fillable = [
        'tenant_id',
        'student_id',
        'scholarship_id',
        'approved_amount',
        'used_amount',
        'balance_amount',
        'applied_on',
        'status',
        'remarks',
    ];

    protected $casts = [
        'approved_amount' => 'decimal:2',
        'used_amount'     => 'decimal:2',
        'balance'         => 'decimal:2',
        'applied_on'      => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function scholarship()
    {
        return $this->belongsTo(Scholarship::class);
    }

    public function transactions()
    {
        return $this->hasMany(ScholarshipTransaction::class);
    }
}
