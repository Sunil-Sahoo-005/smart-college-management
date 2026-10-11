<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseStream extends Model
{
    protected $table = 'course_stream';
 
    protected $fillable = [
        'tenant_id',
        'course_id',
        'stream_id',
    ];
 
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
 
    public function stream()
    {
        return $this->belongsTo(Stream::class);
    }
 
    public function classStreamMappings()
    {
        return $this->hasMany(ClassStreamMapping::class, 'course_stream_id');
    }
}
