<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classe extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'order_index',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }


    public function streams()
    {
        return $this->belongsToMany(
            Stream::class,
            'class_stream_mappings',
            'class_id',
            'stream_id'
        )->withTimestamps();
    }


    public function subjects()
    {
        return $this->belongsToMany(
            Subject::class,
            'class_subject_mappings',
            'class_id',
            'subject_id'
        )
            ->withPivot('is_compulsory')
            ->withTimestamps();
    }
    public function sections()
    {
        return $this->hasMany(Section::class);
    }
    // Add this to your existing Classe model
public function enrollments()
{
    return $this->hasMany(StudentClassEnrollment::class, 'class_id');
}
public function streamMappings()
{
    return $this->hasMany(ClassStreamMapping::class, 'class_id');
}
public function classStreamMappings()
    {
        return $this->hasMany(ClassStreamMapping::class, 'class_id');
    }

}
