<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentRecord extends Model
{
    protected $fillable = [
        'tenant_id',
        'student_id',
        'message',
        'file_path',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}