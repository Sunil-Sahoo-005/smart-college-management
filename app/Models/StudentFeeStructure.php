<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentFeeStructure extends Model
{
    
    protected $fillable = [
        'tenant_id',
        'student_id',
        'fee_structure_id',
        'academic_year_id',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class);
    }
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
