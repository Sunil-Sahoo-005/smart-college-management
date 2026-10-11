<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Student extends Authenticatable
{
    use SoftDeletes, HasApiTokens;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'admission_number',
        'roll_number',
        'first_name',
        'last_name',
        'gender',
        'dob',
        'blood_group',
        'religion',
        'nationality',
        'aadhaar',
        'emergency_contact_name',
        'emergency_contact_phone',
        'medical_conditions',
        'allergies',
        'photo_url',
        'status',
        // Bank Details
        'account_holder_name',
        'bank_name',
        'branch_name',
        'account_type',
        'account_number',
        'ifsc_code',
        'category',
        'transfer_out_reason',
        'transfer_out_date',
        'physically_handicapped',
        'handicap_details',
        'email',
        'sub_caste',
    ];

    protected $casts = [
        'dob' => 'date',
        'status' => 'string',
        'deleted_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function parents()
    {
        return $this->hasMany(StudentParent::class, 'student_id');
    }
    public function addresses()
    {
        return $this->hasMany(StudentAddress::class, 'student_id');
    }
    public function documents()
    {
        return $this->hasMany(StudentDocument::class, 'student_id');
    }
    public function transfer()
    {
        return $this->hasOne(StudentTransfer::class, 'student_id');
    }
    public function feeInstallments()
    {
        return $this->hasMany(\App\Models\FeeInstallment::class, 'student_id');
    }
    public function enrollments()
    {
        return $this->hasMany(StudentClassEnrollment::class, 'student_id');
    }
    public function installments()
    {
        return $this->hasMany(FeeInstallment::class, 'student_id');
    }
    public function routeMappings()
    {
        return $this->hasMany(StudentRouteMapping::class);
    }
    public function hostelAllocations()
    {
        return $this->hasMany(HostelAllocation::class);
    }

    public function hostelAllocation()
    {
        return $this->hasOne(HostelAllocation::class)->latest();
    }
    public function libraryCard()
    {
        return $this->hasOne(LibraryCards::class, 'student_id');
    }
    public function records()
    {
        return $this->hasMany(StudentRecord::class);
    }
    // ✅ Yahan add karo
    public function bookFines()
    {
        return $this->hasMany(\App\Models\BookFines::class, 'student_id');
    }
    public function activeEnrollment()
    {
        return $this->hasOne(StudentClassEnrollment::class)
            ->where('status', 'active');
    }
        public function scopeForTenant($query, $tenantId)
        {
            return $query->where('tenant_id', $tenantId);
        }
public function academicQualifications()
{
    return $this->hasMany(StudentAcademicQualification::class)->orderByRaw("FIELD(qualification_level, '10th', '12th', 'diploma', 'graduation', 'post_graduation', 'other')");
}
    }
