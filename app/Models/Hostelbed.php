<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hostelbed extends Model
{
    protected $table = 'hostel_beds';
    protected $fillable = [
        'tenant_id',
        'room_id',
        'bed_number',
        'amount',
        'status', // available, occupied, maintenance
        'current_student_id',
        'allocation_date',
    ];
    
    public function currentStudent()
{
    return $this->hasOne(HostelAllocation::class, 'bed_id')
                ->where(function ($query) {
                    $query->whereNull('to_date')
                          ->orWhere('to_date', '>=', now()->toDateString());
                })
                ->latest('from_date');
}
    public function room()
    {
        return $this->belongsTo(Hostelroom::class, 'room_id');
    }
    
    
    // public function currentStudent()
    // {
    //     return $this->belongsTo(Student::class, 'current_student_id');
    // }
    
}
