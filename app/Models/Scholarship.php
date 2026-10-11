<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scholarship extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'type',
        'value',
        'source',
        'caste_category',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'value' => 'decimal:2',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
