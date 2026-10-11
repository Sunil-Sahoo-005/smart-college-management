<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryPayment extends Model
{
    protected $fillable = [
        'tenant_id',
        'payslip_id',
        'payment_date',
        'amount',
        'payment_mode',
        'transaction_reference',
        'status',
        'remarks',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function payslip()
    {
        return $this->belongsTo(Payslip::class);
    }
}
