<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'allowed_days',
        'carry_forward',
        'is_paid',
        'applicable_to',
    ];

    protected $casts = [
        'carry_forward' => 'boolean',
        'is_paid' => 'boolean',
        'allowed_days' => 'integer',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
