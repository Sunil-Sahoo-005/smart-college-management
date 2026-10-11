<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAddress extends Model
{
    protected $fillable = [
        'student_id',
        'type',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'pincode',
        'country',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
