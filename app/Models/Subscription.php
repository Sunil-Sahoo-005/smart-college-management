<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'tenant_id',
        'plan_id',
        'starts_at',
        'expires_at',
        'status',
        'payment_method',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'expires_at' => 'date',
        'status' => 'string',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }
}
