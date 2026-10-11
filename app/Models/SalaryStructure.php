<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryStructure extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'components',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'components' => 'array',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
