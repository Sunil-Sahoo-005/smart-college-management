<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeInstallment extends Model
{
    //
    protected $fillable = [
        'tenant_id',
        'student_id',
        'student_fee_structure_id',
        'fee_category_id',
        'amount',
        'due_date',
        'status',
        'reason',       
        'description',
    ];

    protected $casts = [
        'amount'   => 'decimal:2',
        'due_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function studentFeeStructure()
    {
        return $this->belongsTo(StudentFeeStructure::class, 'student_fee_structure_id');
    }

    public function feeCategory()
    {
        return $this->belongsTo(FeeCategory::class);
    }
     public function payment()
    {
        return $this->hasMany(FeePayment::class, 'fee_installment_id');
    }

    // ✅ For receipts: Get the latest payment for this installment
    public function latestPayment()
    {
        return $this->hasOne(FeePayment::class, 'fee_installment_id')->latestOfMany();
    }
}
