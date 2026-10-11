<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentDocument extends Model
{
    protected $fillable = [
        'student_id',
        'doc_type',
        'file_path',
        'uploaded_by_user_id',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }
}
