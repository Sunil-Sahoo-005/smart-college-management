<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'type',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    // App\Models\Subject

public function classes()
{
    return $this->belongsToMany(
        Classe::class,
        'class_subject_mappings',
        'subject_id',
        'class_id'
    )
    ->withPivot('is_compulsory')
    ->withTimestamps();
}

}
