<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookFines extends Model
{
    protected $table = 'book_fines';

    protected $fillable = [
        'tenant_id',
        'student_id',
        'amount',
        'reason',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
