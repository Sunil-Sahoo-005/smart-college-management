<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Routes extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'start_point',
        'start_lat',  // ✅ add
        'start_lng',  // ✅ add
        'end_point',
        'end_lat',    // ✅ add
        'end_lng',    // ✅ add
        'vehicle_id',
        'route_distance',
        'route_duration',
    ];
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
 public function stops()
    {
        return $this->hasMany(RouteStop::class, 'route_id');
    }
    
}
