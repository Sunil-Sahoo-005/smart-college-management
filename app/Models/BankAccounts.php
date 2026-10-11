<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccounts extends Model
{
    protected $fillable = [
        'tenant_id',
        'bank_name',
        'account_number',
        'ifsc_code',
        'status',
    ];

    // Optional casting (agar future me use ho)
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}