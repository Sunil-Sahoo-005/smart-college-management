<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payslip extends Model
{
    protected $fillable = [
        'tenant_id',
        'payroll_run_id',
        'staff_id',
        'gross_salary',
        'total_earnings',
        'total_deductions',
        'lop_amount',
        'bonus_amount',
        'arrear_amount',
        'overtime_amount',
        'net_salary',
        'working_days',
        'present_days',
        'leave_days',
        'absent_days',
        'half_days',
        'late_marks',
        'earnings_breakdown',
        'deductions_breakdown',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'gross_salary' => 'decimal:2',
        'total_earnings' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'lop_amount' => 'decimal:2',
        'bonus_amount' => 'decimal:2',
        'arrear_amount' => 'decimal:2',
        'overtime_amount' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'earnings_breakdown' => 'array',
        'deductions_breakdown' => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function payrollRun()
    {
        return $this->belongsTo(PayrollRun::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function salaryPayment()
    {
        return $this->hasOne(SalaryPayment::class);
    }
}
