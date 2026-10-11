<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $fillable = [
        'tenant_id',
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'alternate_phone',
        'gender',
        'marital_status',
        'dob',
        'blood_group',
        'religion',
        'nationality',
        'permanent_address',
        'address',
        'employee_id',
        'bput_id',
        'employment_type',
        'designation_id',
        'department_id',
        'joining_date',
        'leaving_date',
        'resignation_date',
        'status',
        'experience',
        'qualification',
        'category',
        'pan',
        'aadhaar',
        'pf_number',
        'bank_account_name',
        'bank_account_number',
        'ifsc',
        'bank_name',
        'branch_name',
        'account_type',
        'photo_url',
    ];

    protected $casts = [
        'dob'              => 'date',
        'joining_date'     => 'date',
        'leaving_date'     => 'date',
        'resignation_date' => 'date',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

   public function documents()
{
    return $this->hasMany(StaffDocument::class)
                ->where('doc_type', 'not like', 'SCHEDULE:%')
                ->orderBy('created_at', 'desc');
}

public function scheduleDocuments()
{
    return $this->hasMany(StaffDocument::class)
                ->where('doc_type', 'like', 'SCHEDULE:%')
                ->orderBy('created_at', 'desc');
}

    public function attendance()
    {
        return $this->hasMany(TeacherAttendance::class);
    }
    public function bookIssues()
{
    return $this->hasMany(\App\Models\BookIssue::class, 'staff_id')
                ->with('book')
                ->orderByDesc('issue_date');
}
}