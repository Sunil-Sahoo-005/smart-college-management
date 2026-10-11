<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CenterDevice extends Model
{
     protected $fillable = [
        'center_id',
        'device_code',
        'device_name',
        'device_identifier',
        'mac_address',
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function center()
    {
        return $this->belongsTo(ExamCenter::class, 'center_id');
    }
}
