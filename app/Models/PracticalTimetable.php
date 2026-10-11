<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PracticalTimetable extends Model
{
    protected $fillable = [
        'tenant_id',
        'practical_group_id',
        'name',
    ];

    public function practicalGroup()
    {
        return $this->belongsTo(PracticalGroups::class, 'practical_group_id');
    }

    public function slots()
    {
        return $this->hasMany(PracticalTimetableSlot::class);
    }
}
