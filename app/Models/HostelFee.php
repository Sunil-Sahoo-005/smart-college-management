<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HostelFee extends Model
{
    protected $table = 'hostel_fees';

    protected $fillable = [
        'tenant_id',
        'student_id',
        'amount',
        'due_date',
        'status',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
