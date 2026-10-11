<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentParent extends Model
{
    protected $fillable = [
        'student_id',
        'tenant_id',
        'parent_type',
        'first_name',
        'last_name',
        'phone',
        'email',
        'occupation',
        'address',
        'relationship',
        'is_emergency_contact',
    ];

    protected $casts = [
        'is_emergency_contact' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}