<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibraryCards extends Model
{
    protected $fillable = [
        'tenant_id',
        'student_id',
        'card_number',
        'card_serial_number',
        'issue_date',
        'expiry_date',
        'status',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];
}
