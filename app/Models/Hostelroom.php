<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hostelroom extends Model
{
    use HasFactory;
    protected $table = 'hostel_rooms';
    protected $fillable = [
        'tenant_id',
        'hostel_id',
        'room_number',
        'type',
        'max_beds',
    ];

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }
    
    public function beds()
    {
        return $this->hasMany(Hostelbed::class, 'room_id');
    }
    
    // Add this method to get occupied beds
    public function occupiedBeds()
    {
        return $this->hasMany(Hostelbed::class, 'room_id')->where('status', 'occupied');
    }
    
    // Add this method to get available beds
    public function availableBeds()
    {
        return $this->hasMany(Hostelbed::class, 'room_id')->where('status', 'available');
    }
    // Hostelbed.php model mein
public function currentStudent()
{
    return $this->hasOne(HostelAllocation::class, 'bed_id')
                ->where('status', 'active')
                ->with('student');
}
}