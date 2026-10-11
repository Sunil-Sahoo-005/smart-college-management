<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LedgerEntry extends Model
{
    //
    // protected $fillable = [
    //     'tenant_id',
    //     'student_id',
    //     'fee_installment_id',
    //     'amount',
    //     'entry_type', // debit or credit
    //     'description',
    // ];
     protected $fillable = [
        'tenant_id', 'ledger_id', 'amount', 'type',
        'reference_type', 'reference_id', 'description', 'entry_date'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'entry_date' => 'date',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function ledger()
    {
        return $this->belongsTo(Ledger::class);
    }

    public function reference()
    {
        return $this->morphTo(__FUNCTION__, 'reference_type', 'reference_id');
    }  
}
