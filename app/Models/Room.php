<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
     protected $fillable = [
        'tenant_id',
        'name',
        'type',
        'capacity',
    ];

    protected $casts = [
        'capacity' => 'integer',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
