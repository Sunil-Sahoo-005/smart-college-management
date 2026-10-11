<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'description',
        'monthly_price',
        'annual_price',
        'max_users',
        'max_students',
        'max_staff',
        'is_active',
    ];

    protected $casts = [
        'monthly_price' => 'decimal:2',
        'annual_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
