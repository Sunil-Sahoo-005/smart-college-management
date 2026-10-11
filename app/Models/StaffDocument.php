<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffDocument extends Model
{
    protected $table = 'staff_documents';

    protected $fillable = [
        'staff_id',
        'doc_type',
        'file_path',
        'note',
        'uploaded_by_user_id',
    ];
}