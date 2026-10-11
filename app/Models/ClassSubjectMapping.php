<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSubjectMapping extends Model
{
    protected $fillable = [
        'tenant_id',
        'class_id',
        'class_stream_id',
        'subject_id',
        'is_compulsory',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function classStream()
    {
        return $this->belongsTo(ClassStreamMapping::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}
