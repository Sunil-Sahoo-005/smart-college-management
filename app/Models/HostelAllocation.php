<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HostelAllocation extends Model
{
    protected $table = 'hostel_allocations';

    protected $fillable = [
        'tenant_id',
        'student_id',
        'hostel_id',
        'room_id',
        'bed_id',
        'from_date',
        'to_date'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function room()
    {
        return $this->belongsTo(Hostelroom::class, 'room_id');
    }

    public function bed()
    {
        return $this->belongsTo(Hostelbed::class, 'bed_id');
    }
    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

}
