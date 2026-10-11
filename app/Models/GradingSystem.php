<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradingSystem extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'description',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
    public function gradeLevels()
    {
        return $this->hasMany(GradeLevel::class);
    }
}
