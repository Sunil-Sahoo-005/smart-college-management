<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Staff;
use App\Models\TimetableSlot;
use App\Models\MergedClassSchedule;
use App\Models\TeacherAttendance;
use App\Models\BookIssues;
use App\Models\StaffSalaryAssignment;
use App\Models\Payslip;
use App\Models\LeaveRequest;
use App\Models\LeaveBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class StaffController extends Controller
{
    public function index(string $tenantKey)
    {
        $tenantId = $this->resolveTenantId($tenantKey);

        $staff = Staff::where('tenant_id', $tenantId)
            ->with(['designation', 'department'])
            ->orderBy('first_name')
            ->paginate(10);

        $departments = Department::where('tenant_id', $tenantId)->orderBy('name')->get();
        $designations = Designation::where('tenant_id', $tenantId)->orderBy('name')->get();

        return view('hr.staff.index', compact('staff', 'departments', 'designations', 'tenantKey'));
    }

    public function create(string $tenantKey)
    {
        $tenantId = $this->resolveTenantId($tenantKey);
        $staff = new Staff();
        $designations = Designation::where('tenant_id', $tenantId)->orderBy('name')->get();
        $departments = Department::where('tenant_id', $tenantId)->orderBy('name')->get();

        return view('hr.staff.form', compact('staff', 'designations', 'departments', 'tenantKey'));
    }

    public function edit(string $tenantKey, Staff $staff)
    {
        $this->authorizeTenant($staff);

        $tenantId = $this->resolveTenantId($tenantKey);
        $designations = Designation::where('tenant_id', $tenantId)->orderBy('name')->get();
        $departments = Department::where('tenant_id', $tenantId)->orderBy('name')->get();

        return view('hr.staff.form', compact('staff', 'designations', 'departments', 'tenantKey'));
    }

    public function store(Request $request, string $tenantKey)
    {
        $tenantId = $this->resolveTenantId($tenantKey);

        $data = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'nullable|email|max:100',
            'phone' => 'required|string|min:10|max:15',
            'gender' => 'required|in:male,female,other',
            'dob' => 'required|date',
            'blood_group' => 'required|max:5',
            'religion' => 'required|max:50',
            'nationality' => 'required|max:50',
            'designation_id' => 'required|exists:designations,id',
            'department_id' => 'required|exists:departments,id',
            'joining_date' => 'required|date',
            'resignation_date' => 'nullable|date|after_or_equal:joining_date',
            'status' => 'required|in:active,resigned,retired,on_leave',
            'pan' => 'required|max:10',
            'aadhaar' => 'required|max:12',
            'bank_account_name' => 'required|max:100',
            'bank_account_number' => 'required|max:50',
            'ifsc' => 'required|max:11',
            'staff_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'alternate_phone' => 'nullable|max:15',
            'marital_status' => 'required|in:Single,Married,Divorced,Widowed',
            'permanent_address' => 'required|max:500',
            'address' => 'required|max:500',
            'employee_id' => 'required|max:50',
            'bput_id' => 'required|max:50',
            'employment_type' => 'required|in:Full-Time,Part-Time,Contract,Guest,Visiting',
            'leaving_date' => 'nullable|date',
            'experience' => 'required|integer|min:0|max:40',
            'qualification' => 'required|max:50',
            'category' => 'required|in:General,OBC,SC,ST,EWS',
            'pf_number' => 'required|max:50',
            'bank_name' => 'required|max:100',
            'branch_name' => 'required|max:255',
            'account_type' => 'required|in:Savings,Current',
        ]);

        $staff = !empty($request->id)
            ? Staff::where('id', $request->id)->where('tenant_id', $tenantId)->firstOrFail()
            : new Staff(['tenant_id' => $tenantId]);

        $staff->fill($data);

        if ($request->hasFile('staff_photo')) {
            if ($staff->photo_url) {
                Storage::disk('public')->delete($staff->photo_url);
            }
            $file = $request->file('staff_photo');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $staff->photo_url = $file->storeAs('staff/photos', $filename, 'public');
        }

        $staff->save();

        return redirect()
            ->route('hr.staff.index', $tenantKey)
            ->with('success', 'Staff saved successfully.');
    }

    public function show(string $tenantKey, Staff $staff)
    {
        $this->authorizeTenant($staff);

        $staff->load(['designation', 'department', 'tenant', 'documents', 'scheduleDocuments']);

        // 1. Timetable/Schedule
        $regularSlots = TimetableSlot::where('tenant_id', $staff->tenant_id)
            ->where('staff_id', $staff->id)
            ->with(['timetable.class', 'timetable.section', 'timetable.stream', 'period', 'subject', 'room'])
            ->get();

        $unifiedGrid = $regularSlots->groupBy('period_id')->map(function ($periodGroup) {
            return $periodGroup->groupBy('day_of_week');
        });

        $regularPeriods = $regularSlots->pluck('period')->unique('id')->sortBy('id');

        $startOfWeek = now()->startOfWeek(Carbon::MONDAY);
        $endOfWeek   = now()->endOfWeek(Carbon::SATURDAY);

        $mergedSchedules = MergedClassSchedule::where('tenant_id', $staff->tenant_id)
            ->where('staff_id', $staff->id)
            ->whereBetween('schedule_date', [$startOfWeek, $endOfWeek])
            ->with(['subject', 'room', 'period'])
            ->get();

        $mergedByPeriodAndDay = $mergedSchedules->groupBy('period_id')->map(function ($periodGroup) {
            return $periodGroup->keyBy(fn($s) => Carbon::parse($s->schedule_date)->dayOfWeekIso);
        });

        $mergedPeriods = $mergedSchedules->pluck('period')->unique('id')->sortBy('id');

        // 2. Library
        $bookIssues = BookIssues::with('book')
            ->where('staff_id', $staff->id)
            ->orderByDesc('issue_date')
            ->get();

        // 3. Attendance Details
        $attendanceRecords = TeacherAttendance::where('staff_id', $staff->id)
            ->where('tenant_id', $staff->tenant_id)
            ->with(['period'])
            ->orderBy('date', 'desc')
            ->get();

        $attendanceStats = [
            'total' => $attendanceRecords->count(),
            'present' => $attendanceRecords->where('status', 'present')->count(),
            'absent' => $attendanceRecords->where('status', 'absent')->count(),
            'late' => $attendanceRecords->where('status', 'late')->count(),
            'half_day' => $attendanceRecords->where('status', 'half_day')->count(),
        ];
        $attendanceStats['rate'] = $attendanceStats['total'] > 0 
            ? round(($attendanceStats['present'] / $attendanceStats['total']) * 100, 1) 
            : 100;

        // 4. Salary/Payroll Details
        $activeSalaryAssignment = StaffSalaryAssignment::where('staff_id', $staff->id)
            ->where('is_active', true)
            ->with('salaryStructure')
            ->latest('effective_from')
            ->first();

        $salaryAssignments = StaffSalaryAssignment::where('staff_id', $staff->id)
            ->with('salaryStructure')
            ->latest('effective_from')
            ->get();

        $payslips = Payslip::where('staff_id', $staff->id)
            ->with('payrollRun')
            ->latest('created_at')
            ->get();

        // 5. Leave Details
        $leaveRequests = LeaveRequest::where('staff_id', $staff->id)
            ->with(['leaveType', 'approver'])
            ->latest('from_date')
            ->get();

        $leaveBalances = LeaveBalance::where('staff_id', $staff->id)
            ->with('leaveType')
            ->where('year', now()->year)
            ->get();

        return view('hr.staff.show', compact(
            'staff',
            'tenantKey',
            'unifiedGrid',
            'regularPeriods',
            'mergedByPeriodAndDay',
            'mergedPeriods',
            'mergedSchedules',
            'startOfWeek',
            'endOfWeek',
            'bookIssues',
            'attendanceRecords',
            'attendanceStats',
            'activeSalaryAssignment',
            'salaryAssignments',
            'payslips',
            'leaveRequests',
            'leaveBalances'
        ));
    }

    public function destroy(string $tenantKey, Staff $staff)
    {
        $this->authorizeTenant($staff);
        $staff->delete();

        return redirect()
            ->route('hr.staff.index', $tenantKey)
            ->with('success', 'Staff deleted successfully.');
    }

    private function authorizeTenant(Staff $staff): void
    {
        abort_unless($staff->tenant_id === auth()->user()->tenant_id, 403);
    }

    private function resolveTenantId(string $tenantKey): int
    {
        if (preg_match('/-(\d+)(?:-|$)/', $tenantKey, $matches)) {
            return (int) $matches[1];
        }

        abort(404, 'Invalid tenant link');
    }
}
