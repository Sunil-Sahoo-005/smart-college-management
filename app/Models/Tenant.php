<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = [
        'uuid',
        'name',
        'logo_url',
        'address',
        'phone',
        'email',
        'timezone',
        'currency',
        'academic_year_start_month',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function departments()
    {
        return $this->hasMany(Department::class);
    }
    public function students()
    {
        return $this->hasMany(Student::class);
    }
    public function users()
    {
        return $this->hasMany(\App\Models\User::class, 'tenant_id');
    }
    // public function users()
    // {
    //     return $this->hasMany(User::class);
    // }

    // public function students()
    // {
    //     return $this->hasMany(Student::class);
    // }

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }

    public function academicYears()
    {
        return $this->hasMany(AcademicYear::class);
    }

    public function currentAcademicYear()
    {
        return $this->hasOne(AcademicYear::class)->where('is_current', true);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function dataBackups()
    {
        return $this->hasMany(DataBackup::class);
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }
}
