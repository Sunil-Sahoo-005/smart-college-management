<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollRun extends Model
{
    protected $fillable = [
        'tenant_id',
        'month',
        'year',
        'status',
        'processed_by',
        'processed_at',
        'finalized_at',
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'processed_at' => 'datetime',
        'finalized_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function payslips()
    {
        return $this->hasMany(Payslip::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
