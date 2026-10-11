<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassStreamMapping extends Model
{
    protected $fillable = [
        'tenant_id',
        'class_id',
        'course_stream_id',
        'stream_id',
    ];
public function courseStream()
    {
        return $this->belongsTo(CourseStream::class, 'course_stream_id');
    }
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function class()
    {
        return $this->belongsTo(Classe::class, 'class_id');
    }

    public function stream()
    {
        return $this->belongsTo(Stream::class, 'stream_id');
    }

    public function sections()
    {
        return $this->hasMany(Section::class, 'class_stream_id');
    }

    // public function classSubjectMappings()
    // {
    //     return $this->hasMany(ClassSubjectMapping::class, 'class_id');
    // }
     public function classSubjectMappings()
    {
        return $this->hasMany(ClassSubjectMapping::class, 'class_stream_id');
    }
}
