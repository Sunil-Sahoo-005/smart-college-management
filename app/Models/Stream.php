<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stream extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'code',          
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function courseStreams()
    {
        return $this->hasMany(CourseStream::class);
    }

    public function classes()
    {
        return $this->belongsToMany(
            Classe::class,
            'class_stream_mappings',
            'stream_id',
            'class_id'
        )->withTimestamps();
    }

    public function feeStructures()
    {
        return $this->hasMany(FeeStructure::class, 'stream_id');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_stream')
            ->withTimestamps()
            ->withPivot('tenant_id');
    }
}