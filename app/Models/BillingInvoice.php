<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingInvoice extends Model
{
    protected $fillable = [
        'tenant_id',
        'invoice_number',
        'amount',
        'currency',
        'due_date',
        'paid_at',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'status' => 'string',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
