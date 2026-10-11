<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'start_year',
        'end_year',
        'is_current',
    ];

    protected $casts = [
        'start_year' => 'integer',
        'end_year'   => 'integer',
        'is_current' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
