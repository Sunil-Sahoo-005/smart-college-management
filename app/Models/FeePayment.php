<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeePayment extends Model
{
    //
    protected $fillable = [
        'tenant_id',
        'student_id',
        'fee_installment_id',
        'payment_method',
        'transaction_id',
        'gateway_name',
        'amount_paid',
        'paid_at',
        'payment_date',
        'receipt_number',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'paid_at'     => 'datetime',
        'payment_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function installment()
    {
        return $this->belongsTo(FeeInstallment::class, 'fee_installment_id');
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccounts::class);
    }
    public function ledgerEntries()
    {
        return $this->morphMany(LedgerEntry::class, null, 'reference_type', 'reference_id');
    }
}
