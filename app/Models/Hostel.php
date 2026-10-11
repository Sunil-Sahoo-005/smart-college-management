<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hostel extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'type',
        'address',
        'contact_phone',
        'caretaker',          
        'caretaker_phone',    
        'code_of_conduct',    
    ];
    
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
    
    public function getFormattedNameAttribute()
    {
        $typeText = '';
        if ($this->type === 'boys') {
            $typeText = ' Boys Hostel';
        } elseif ($this->type === 'girls') {
            $typeText = ' Girls Hostel';
        } elseif ($this->type === 'mixed') {
            $typeText = '👥 Mixed Hostel';
        } else {
            $typeText = ucfirst($this->type ?? 'Hostel');
        }
        
        return $this->name . ' (' . $typeText . ')';
    }
     public function rooms()
    {
        return $this->hasMany(HostelRoom::class);
    }
    public function beds()
{
    return $this->hasManyThrough(HostelBed::class, HostelRoom::class);
}
}