<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RouteStop extends Model
{
    protected $fillable = [
        'route_id',
        'stop_name',
        'lat',        // ✅ NEW
        'lng',        // ✅ NEW
        'order',      // ✅ NEW
        'arrival_time',
    ];

    public function route()
    {
        return $this->belongsTo(Routes::class);
    }
}