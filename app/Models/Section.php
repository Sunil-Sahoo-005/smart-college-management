<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = [
        'tenant_id',
        'class_id',
        'class_stream_id',
        'name',
        'max_strength',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function classStream()
    {
        return $this->belongsTo(ClassStreamMapping::class, 'class_stream_id');
    }

    public function class()
    {
        return $this->belongsTo(Classe::class, 'class_id');
    }
}
