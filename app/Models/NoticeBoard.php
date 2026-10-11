<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NoticeBoard extends Model
{
    use HasFactory;

    protected $table = 'notice_boards';

    protected $fillable = [
        'tenant_id',
        'title',
        'notice_date',
        'file_path',
    ];

    protected $casts = [
        'notice_date' => 'date',
    ];

    // Tenant Relationship
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}