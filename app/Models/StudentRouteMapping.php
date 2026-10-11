<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentRouteMapping extends Model
{
    protected $fillable = [
        'tenant_id',
        'student_id',
        'route_id',
        'stop_id',
        'transport_fee',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function route()
    {
        return $this->belongsTo(Routes::class);
    }

    public function stop()
    {
        return $this->belongsTo(RouteStop::class);
    }
    public function getStops($route_id)
    {
    $route = Route::with('stops')->find($route_id);
    return response()->json($route->stops);
    }

}
