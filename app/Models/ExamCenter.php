<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ExamCenter extends Authenticatable
{
    use HasApiTokens, Notifiable;

     protected $fillable = [
        'tenant_id',
        'code',
        'name',
        'username',
        'password',
        'description',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
    public function assignments()
{
    return $this->hasMany(
        ExamCenterAssignment::class,
        'center_id'
    );
}

public function exams()
{
    return $this->belongsToMany(
        Exam::class,
        'exam_center_assignments',
        'center_id',
        'exam_id'
    );
}
public function scopeActive($query)
{
    return $query->where('status', 'active');
}
 public function devices()
    {
        return $this->hasMany(CenterDevice::class, 'center_id');
    }
}
