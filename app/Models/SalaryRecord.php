<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryRecord extends Model
{
    //
     protected $fillable = [
        'tenant_id', 'staff_id', 'month', 'year', 'basic_salary', 
        'allowances', 'deductions', 'net_pay', 'status'
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'allowances' => 'decimal:2',
        'deductions' => 'decimal:2',
        'net_pay' => 'decimal:2',
        'month' => 'integer',
        'year' => 'integer'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function ledgerEntries()
    {
        return $this->morphMany(LedgerEntry::class, null, 'reference_type', 'reference_id');
    }
}
