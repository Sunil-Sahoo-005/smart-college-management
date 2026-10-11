<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    // protected $table = 'vehicles';

    protected $fillable = [
        'tenant_id',
        'registration_number',
        'vehicle_number',
        'type',
        'capacity',
        'driver_id',
    ];
    
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
