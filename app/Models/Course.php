<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'duration_years',
        'course_type',
        'strength',
        'is_lateral_entry',
        'lateral_seat_percentage',
    ];

    protected $casts = [
        'is_lateral_entry' => 'boolean',
        'lateral_seat_percentage' => 'decimal:2',
    ];
public function courseStreams()
    {
        return $this->hasMany(CourseStream::class);
    }
    public function streams()
    {
        return $this->belongsToMany(Stream::class, 'course_stream')
            ->withTimestamps()
            ->withPivot('tenant_id');
    }
}
