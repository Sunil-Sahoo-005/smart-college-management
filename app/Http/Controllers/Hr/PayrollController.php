<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Holidays;
use App\Models\LeaveRequest;
use App\Models\PayrollRun;
use App\Models\Payslip;
use App\Models\SalaryPayment;
use App\Models\SalaryStructure;
use App\Models\Staff;
use App\Models\StaffSalaryAssignment;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    public function index(string $tenantKey)
    {
        $tenant = $this->resolveTenant($tenantKey);

        $payrollRuns = PayrollRun::where('tenant_id', $tenant->id)
            ->latest('year')
            ->latest('month')
            ->paginate(10);

        return view('hr.payroll.index', compact('tenant', 'tenantKey', 'payrollRuns'));
    }

    public function create(string $tenantKey)
    {
        $tenant = $this->resolveTenant($tenantKey);
        $salaryStructures = SalaryStructure::where('tenant_id', $tenant->id)->where('is_active', true)->orderBy('name')->get();

        return view('hr.payroll.create', compact('tenant', 'tenantKey', 'salaryStructures'));
    }

    public function preview(Request $request, string $tenantKey)
    {
        $tenant = $this->resolveTenant($tenantKey);

        $data = $request->validate([
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
        ]);

        $run = (object) [
            'month' => (int) $data['month'],
            'year' => (int) $data['year'],
            'status' => 'preview',
        ];

        $previewRows = $this->buildPayrollRows($tenant->id, $run, false);
        $summary = $this->summarizeRows($previewRows);

        return view('hr.payroll.preview', compact('tenant', 'tenantKey', 'run', 'previewRows', 'summary'));
    }

    public function store(Request $request, string $tenantKey)
    {
        $tenant = $this->resolveTenant($tenantKey);

        $data = $request->validate([
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
        ]);

        $run = DB::transaction(function () use ($tenant, $data) {
            $run = PayrollRun::updateOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'month' => $data['month'],
                    'year' => $data['year'],
                ],
                [
                    'status' => 'processed',
                    'processed_by' => auth()->id(),
                    'processed_at' => now(),
                ]
            );

            Payslip::where('payroll_run_id', $run->id)->delete();
            $this->buildPayrollRows($tenant->id, $run, true);

            return $run;
        });

        return redirect()->route('hr.payrolls.show', [$tenantKey, $run])
            ->with('success', 'Payroll processed successfully.');
    }

    public function show(string $tenantKey, PayrollRun $payrollRun)
    {
        $tenant = $this->resolveTenant($tenantKey);
        $this->authorizeTenant($tenant->id, $payrollRun);

        $payrollRun->load(['payslips.staff.designation', 'payslips.staff.department', 'payslips.salaryPayment', 'processedBy']);

        return view('hr.payroll.show', compact('tenant', 'tenantKey', 'payrollRun'));
    }

    public function publishPayslip(string $tenantKey, Payslip $payslip)
    {
        $tenant = $this->resolveTenant($tenantKey);
        $this->authorizePayslipTenant($tenant->id, $payslip);

        $payslip->update([
            'is_published' => true,
            'published_at' => now(),
        ]);

        return back()->with('success', 'Payslip published.');
    }

    public function recordPayment(Request $request, string $tenantKey, Payslip $payslip)
    {
        $tenant = $this->resolveTenant($tenantKey);
        $this->authorizePayslipTenant($tenant->id, $payslip);

        $data = $request->validate([
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_mode' => ['required', 'in:bank_transfer,cash,cheque,upi'],
            'transaction_reference' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:pending,paid,failed'],
            'remarks' => ['nullable', 'string'],
        ]);

        SalaryPayment::updateOrCreate(
            ['payslip_id' => $payslip->id],
            array_merge($data, ['tenant_id' => $tenant->id, 'payslip_id' => $payslip->id])
        );

        return back()->with('success', 'Payment updated.');
    }

    private function buildPayrollRows(int $tenantId, object $run, bool $persist = false): array
    {
        $staffList = Staff::where('tenant_id', $tenantId)
            ->with(['designation', 'department'])
            ->get();

        $rows = [];

        foreach ($staffList as $staff) {
            $assignment = StaffSalaryAssignment::where('tenant_id', $tenantId)
                ->where('staff_id', $staff->id)
                ->where('is_active', true)
                ->with('salaryStructure')
                ->latest('effective_from')
                ->first();

            if (! $assignment) {
                continue;
            }

            $periodStart = Carbon::create($run->year, $run->month, 1)->startOfMonth();
            $periodEnd = Carbon::create($run->year, $run->month, 1)->endOfMonth();

            $holidayDays = Holidays::where('tenant_id', $tenantId)
                ->whereBetween('date', [$periodStart->toDateString(), $periodEnd->toDateString()])
                ->count();

            $attendanceRows = Attendance::where('tenant_id', $tenantId)
                ->where('staff_id', $staff->id)
                ->whereBetween('attendance_date', [$periodStart->toDateString(), $periodEnd->toDateString()])
                ->get();

            $presentDays = $attendanceRows->where('status', 'present')->count();
            $approvedLeaveDays = $this->countApprovedLeaveDays($staff->id, $periodStart, $periodEnd);
            $leaveDays = $attendanceRows->where('status', 'leave')->count() + $approvedLeaveDays;
            $halfDays = $attendanceRows->where('status', 'half_day')->count();
            $absentDays = $attendanceRows->where('status', 'absent')->count();
            $workingDays = max(0, $periodStart->daysInMonth - $holidayDays);

            $gross = (float) $assignment->gross_salary;
            $componentBreakdown = $this->calculateComponentBreakdown($assignment->salaryStructure?->components ?? [], $gross);
            $earningsBreakdown = $componentBreakdown['earnings'];
            $deductionsBreakdown = $componentBreakdown['deductions'];

            $bonus = 0;
            $arrears = 0;
            $overtime = 0;
            $totalEarnings = $gross + $bonus + $arrears + $overtime;
            $totalComponentDeductions = collect($deductionsBreakdown)->sum('amount');
            $lopAmount = round(($gross / max(1, $workingDays)) * $absentDays, 2);
            $totalDeductions = $totalComponentDeductions + $lopAmount;

            $net = max(0, $totalEarnings - $totalDeductions);

            $row = [
                'staff' => $staff,
                'assignment' => $assignment,
                'gross_salary' => $gross,
                'total_earnings' => $totalEarnings,
                'total_deductions' => $totalDeductions,
                'lop_amount' => $lopAmount,
                'bonus_amount' => $bonus,
                'arrear_amount' => $arrears,
                'overtime_amount' => $overtime,
                'net_salary' => $net,
                'working_days' => $workingDays,
                'present_days' => $presentDays,
                'leave_days' => $leaveDays,
                'absent_days' => $absentDays,
                'half_days' => $halfDays,
                'late_marks' => $attendanceRows->where('is_late', true)->count(),
                'earnings_breakdown' => $earningsBreakdown,
                'deductions_breakdown' => array_merge($deductionsBreakdown, [
                    [
                        'key' => 'lop',
                        'label' => 'Loss of Pay',
                        'amount' => $lopAmount,
                    ],
                ]),
            ];

            $rows[] = $row;

            if ($persist) {
                Payslip::create([
                    'tenant_id' => $tenantId,
                    'payroll_run_id' => $run->id,
                    'staff_id' => $staff->id,
                    'gross_salary' => $gross,
                    'total_earnings' => $totalEarnings,
                    'total_deductions' => $totalDeductions,
                    'lop_amount' => $lopAmount,
                    'bonus_amount' => $bonus,
                    'arrear_amount' => $arrears,
                    'overtime_amount' => $overtime,
                    'net_salary' => $net,
                    'working_days' => $workingDays,
                    'present_days' => $presentDays,
                    'leave_days' => $leaveDays,
                    'absent_days' => $absentDays,
                    'half_days' => $halfDays,
                    'late_marks' => $attendanceRows->where('is_late', true)->count(),
                    'earnings_breakdown' => $earningsBreakdown,
                    'deductions_breakdown' => array_merge($deductionsBreakdown, [
                        [
                            'key' => 'lop',
                            'label' => 'Loss of Pay',
                            'amount' => $lopAmount,
                        ],
                    ]),
                    'is_published' => false,
                ]);
            }
        }

        return $rows;
    }

    private function summarizeRows(array $rows): array
    {
        $collection = collect($rows);

        return [
            'employees' => $collection->count(),
            'gross_salary' => $collection->sum('gross_salary'),
            'total_deductions' => $collection->sum('total_deductions'),
            'net_salary' => $collection->sum('net_salary'),
        ];
    }

    private function calculateComponentBreakdown(array $components, float $grossSalary): array
    {
        $earnings = [];
        $deductions = [];
        $basicAmount = null;

        foreach ($components as $component) {
            if (!is_array($component)) {
                continue;
            }

            $type = strtolower($component['type'] ?? 'earning');
            $calc = strtolower($component['calc'] ?? 'fixed');
            $value = (float) ($component['value'] ?? 0);
            $label = $component['label'] ?? ($component['key'] ?? 'Component');
            $key = $component['key'] ?? str()->slug($label);

            $base = $grossSalary;
            if ($calc === 'percent_of_basic') {
                $base = $basicAmount ?? $grossSalary;
            }

            $amount = match ($calc) {
                'percent_of_gross', 'percent_of_basic' => round($base * ($value / 100), 2),
                default => round($value, 2),
            };

            if ($type === 'earning') {
                $earnings[] = [
                    'key' => $key,
                    'label' => $label,
                    'amount' => $amount,
                    'type' => 'earning',
                    'calc' => $calc,
                    'value' => $value,
                ];

                if ($basicAmount === null && in_array(strtolower($key), ['basic', 'basic_salary'], true)) {
                    $basicAmount = $amount;
                }
            } else {
                $deductions[] = [
                    'key' => $key,
                    'label' => $label,
                    'amount' => $amount,
                    'type' => 'deduction',
                    'calc' => $calc,
                    'value' => $value,
                ];
            }
        }

        if ($basicAmount === null) {
            $basicAmount = $grossSalary;
        }

        if (empty($earnings)) {
            $earnings[] = [
                'key' => 'basic',
                'label' => 'Basic Salary',
                'amount' => $grossSalary,
                'type' => 'earning',
                'calc' => 'fixed',
                'value' => $grossSalary,
            ];
        }

        return compact('earnings', 'deductions');
    }

    private function countApprovedLeaveDays(int $staffId, Carbon $periodStart, Carbon $periodEnd): int
    {
        $leaves = LeaveRequest::where('staff_id', $staffId)
            ->where('status', 'approved')
            ->whereDate('from_date', '<=', $periodEnd->toDateString())
            ->whereDate('to_date', '>=', $periodStart->toDateString())
            ->get();

        $days = 0;

        foreach ($leaves as $leave) {
            $start = Carbon::parse($leave->from_date)->max($periodStart);
            $end = Carbon::parse($leave->to_date)->min($periodEnd);
            $days += $start->diffInDays($end) + 1;
        }

        return $days;
    }

    private function resolveTenant(string $tenantKey): Tenant
    {
        if (preg_match('/-(\d+)(?:-|$)/', $tenantKey, $matches)) {
            return Tenant::where('status', 'active')->findOrFail((int) $matches[1]);
        }

        abort(404, 'Invalid tenant link');
    }

    private function authorizeTenant(int $tenantId, PayrollRun $payrollRun): void
    {
        abort_unless($payrollRun->tenant_id === $tenantId, 403);
    }

    private function authorizePayslipTenant(int $tenantId, Payslip $payslip): void
    {
        abort_unless($payslip->tenant_id === $tenantId, 403);
    }

    public function showPayslip(string $tenantKey, Payslip $payslip)
    {
        $tenant = $this->resolveTenant($tenantKey);
        $this->authorizePayslipTenant($tenant->id, $payslip);

        $payslip->load(['staff.designation', 'staff.department', 'payrollRun']);

        return view('hr.payroll.payslip-show', compact('tenant', 'tenantKey', 'payslip'));
    }

    public function editPayslip(string $tenantKey, Payslip $payslip)
    {
        $tenant = $this->resolveTenant($tenantKey);
        $this->authorizePayslipTenant($tenant->id, $payslip);

        $payslip->load(['staff.designation', 'staff.department', 'payrollRun']);

        return view('hr.payroll.payslip-edit', compact('tenant', 'tenantKey', 'payslip'));
    }

    public function updatePayslip(Request $request, string $tenantKey, Payslip $payslip)
    {
        $tenant = $this->resolveTenant($tenantKey);
        $this->authorizePayslipTenant($tenant->id, $payslip);

        $data = $request->validate([
            'working_days' => ['required', 'integer', 'min:0'],
            'present_days' => ['required', 'integer', 'min:0'],
            'leave_days' => ['required', 'integer', 'min:0'],
            'absent_days' => ['required', 'integer', 'min:0'],
            'half_days' => ['required', 'integer', 'min:0'],
            'late_marks' => ['required', 'integer', 'min:0'],
            'bonus_amount' => ['required', 'numeric', 'min:0'],
            'arrear_amount' => ['required', 'numeric', 'min:0'],
            'overtime_amount' => ['required', 'numeric', 'min:0'],
            'lop_amount' => ['required', 'numeric', 'min:0'],
        ]);

        $gross = (float) $payslip->gross_salary;
        $totalEarnings = $gross + (float)$data['bonus_amount'] + (float)$data['arrear_amount'] + (float)$data['overtime_amount'];

        $deductionsBreakdown = $payslip->deductions_breakdown ?? [];
        $newDeductionsBreakdown = [];
        $totalComponentDeductions = 0;

        foreach ($deductionsBreakdown as $comp) {
            if (isset($comp['key']) && $comp['key'] === 'lop') {
                continue;
            }
            $newDeductionsBreakdown[] = $comp;
            $totalComponentDeductions += (float) ($comp['amount'] ?? 0);
        }

        $lop = (float)$data['lop_amount'];
        $newDeductionsBreakdown[] = [
            'key' => 'lop',
            'label' => 'Loss of Pay',
            'amount' => $lop,
        ];

        $totalDeductions = $totalComponentDeductions + $lop;
        $net = max(0, $totalEarnings - $totalDeductions);

        $payslip->update(array_merge($data, [
            'total_earnings' => $totalEarnings,
            'total_deductions' => $totalDeductions,
            'net_salary' => $net,
            'deductions_breakdown' => $newDeductionsBreakdown,
        ]));

        return redirect()->route('hr.payrolls.show', [$tenantKey, $payslip->payroll_run_id])
            ->with('success', 'Payslip adjusted successfully.');
    }
}
