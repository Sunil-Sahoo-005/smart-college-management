<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseRecord extends Model
{
    //
    protected $fillable = ['tenant_id', 'expense_head_id', 'amount', 'date', 'description'];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function expenseHead()
    {
        return $this->belongsTo(ExpenseHead::class);
    }
    public function ledgerEntries()
    {
        return $this->morphMany(LedgerEntry::class, null, 'reference_type', 'reference_id');
    }
}
