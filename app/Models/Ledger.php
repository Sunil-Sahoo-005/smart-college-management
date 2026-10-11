<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
    //
protected $fillable = ['tenant_id', 'name', 'balance_type'];

    protected $casts = [
        'balance_type' => 'string',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function entries()
    {
        return $this->hasMany(LedgerEntry::class);
    }
    // app/Models/Ledger.php
public function getBalanceAttribute()
{
    $debit = $this->entries()->where('type', 'debit')->sum('amount');
    $credit = $this->entries()->where('type', 'credit')->sum('amount');
    
    return $this->balance_type == 'debit' 
        ? $debit - $credit 
        : $credit - $debit;
}

}
