<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentTransfer extends Model
{
    protected $fillable = [
        'student_id',
        'from_class_id',
        'from_section_id',
        'to_school_name',
        'to_school_address',
        'tc_issued_date',
        'reason',
        'remarks',
        'issued_by_user_id',
    ];

    protected $casts = [
        'tc_issued_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function fromClass()
    {
        return $this->belongsTo(ClassModel::class, 'from_class_id');
    }

    public function fromSection()
    {
        return $this->belongsTo(Section::class, 'from_section_id');
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by_user_id');
    }
}
