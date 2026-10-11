<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffSalaryAssignment extends Model
{
    protected $fillable = [
        'tenant_id',
        'staff_id',
        'salary_structure_id',
        'gross_salary',
        'effective_from',
        'effective_to',
        'is_active',
    ];

    protected $casts = [
        'gross_salary' => 'decimal:2',
        'effective_from' => 'date',
        'effective_to' => 'date',
        'is_active' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function salaryStructure()
    {
        return $this->belongsTo(SalaryStructure::class);
    }
}
