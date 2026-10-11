<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\Staff;
use App\Models\Student;
use App\Models\StudentClassEnrollment;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class HrDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $tenants = Tenant::where('status', 'active')
            ->withCount(['staff', 'students', 'academicYears'])
            ->orderBy('name')
            ->get();

        return view('hr.dashboard', compact('today', 'tenants'));
    }

    public function tenantDashboard($tenantKey)
    {
        $tenantId = $this->extractTenantId($tenantKey);

        $tenant = Tenant::with(['currentAcademicYear'])
            ->where('status', 'active')
            ->findOrFail($tenantId);

        $today = Carbon::today();

        $totalStaff = Staff::where('tenant_id', $tenant->id)->count();
        $activeStaff = Staff::where('tenant_id', $tenant->id)->where('status', 'active')->count();
        $newHiresThisMonth = Staff::where('tenant_id', $tenant->id)
            ->whereMonth('joining_date', $today->month)
            ->whereYear('joining_date', $today->year)
            ->count();

        $totalStudents = Student::where('tenant_id', $tenant->id)->count();
        $activeEnrollments = StudentClassEnrollment::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->count();

        // $leaveToday = Leave::where('tenant_id', $tenant->id)
        //     ->whereDate('from_date', '<=', $today)
        //     ->whereDate('to_date', '>=', $today)
        //     ->count();

        // $leaveRequests = Leave::where('tenant_id', $tenant->id)
        //     ->latest()
        //     ->limit(5)
        //     ->get();

        $recentStaff = Staff::where('tenant_id', $tenant->id)
            ->with(['designation', 'department'])
            ->latest('id')
            ->limit(6)
            ->get();

        return view('hr.tenant_dashboard', compact(
            'tenant',
            'today',
            'totalStaff',
            'activeStaff',
            'newHiresThisMonth',
            'totalStudents',
            'activeEnrollments',
            // 'leaveToday',
            // 'leaveRequests',
            'recentStaff'
        ));
    }

    private function extractTenantId(string $tenantKey): int
    {
        if (preg_match('/-(\d+)(?:-|$)/', $tenantKey, $matches)) {
            return (int) $matches[1];
        }

        abort(404, 'Invalid tenant link');
    }
}
