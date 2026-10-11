<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamAttempt extends Model
{
    protected $fillable = [
        'tenant_id',
        'exam_id',
        'student_id',
        'center_id',
        'device_id',
        'started_at',
        'submitted_at',
        'status',
        'score',
        'ip_address',
        'device_info',
    ];

    protected $casts = [
        'started_at'   => 'datetime',
        'submitted_at' => 'datetime',
        'score'        => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function center()
    {
        return $this->belongsTo(
            ExamCenter::class,
            'center_id'
        );
    }

    public function device()
    {
        return $this->belongsTo(
            CenterDevice::class,
            'device_id'
        );
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function answers()
    {
        return $this->hasMany(
            ExamAnswer::class,
            'attempt_id'
        );
    }

    public function violations()
    {
        return $this->hasMany(
            ExamViolation::class,
            'attempt_id'
        );
    }
}
