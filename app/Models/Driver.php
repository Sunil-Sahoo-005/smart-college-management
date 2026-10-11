<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $fillable = [
        'tenant_id',
        'first_name',
        'last_name',
        'phone',
        'license_number',
    ];
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

}
