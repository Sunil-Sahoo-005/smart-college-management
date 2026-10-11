<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\BookFines;
use App\Models\BookIssues;
use App\Models\Books;
use App\Models\Classe;
use App\Models\Driver;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\ExpenseHead;
use Illuminate\Support\Facades\Schema;
use App\Models\ExpenseRecord;
use App\Models\FeeInstallment;
use App\Models\FeePayment;
use App\Models\Hostel;
use App\Models\Holidays;
use App\Models\Staff;
use App\Models\Room;
use App\Models\Timetable;
use App\Models\HostelAllocation;
use App\Models\Hostelbed;
use App\Models\HostelFee;
use App\Models\Hostelroom;
use App\Models\IncomeHead;
use App\Models\IncomeRecord;
use App\Models\LibraryCards;
use App\Models\Routes;
use App\Models\RouteStop;
use App\Models\Stream;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentClassEnrollment;
use App\Models\StudentRouteMapping;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // public function superdashboard()
    // {
    //     $tenants = Tenant::withCount([
    //         'users as users_count',
    //         'students as students_count'  // adjust table name
    //     ])
    //         ->orderBy('name')
    //         ->get();
    //     return view('dashboard.super_admin', compact('tenants'));
    // }
    public function superdashboard()
    {
        $today = Carbon::today();

        // ── Top stat cards ─────────────────────────────────────────────────
        $totalTenants = Tenant::count();

        $newTenantsThisMonth = Tenant::whereMonth('created_at', $today->month)
            ->whereYear('created_at', $today->year)
            ->count();

        $totalStudents = Student::where('status', 'active')->count();

        $totalUsers = User::count();

        $newUsersThisWeek = User::where('created_at', '>=', $today->startOfWeek())->count();

        $totalFeeCollected = FeePayment::whereYear('payment_date', $today->year)
            ->sum('amount_paid');

        // ── Tenant list with student + staff counts ───────────────────────
        // Using manual subquery counts to avoid dependency on Tenant relationships
        $tenants = Tenant::select('tenants.*')
            ->selectSub(
                Student::selectRaw('count(*)')
                    ->whereColumn('tenant_id', 'tenants.id')
                    ->where('status', 'active'),
                'student_count'
            )
            ->selectSub(
                \App\Models\Staff::selectRaw('count(*)')
                    ->whereColumn('tenant_id', 'tenants.id')
                    ->where('status', 'active'),
                'staff_count'
            )
            ->orderByDesc('student_count')
            ->get()
            ->map(function ($t) {
                $initials = collect(explode(' ', $t->name))
                    ->take(2)
                    ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                    ->implode('');
                return [
                    'id'            => $t->id,
                    'name'          => $t->name,
                    'initials'      => $initials,
                    'city'          => $t->address ?? '—',
                    'status'        => $t->status,
                    'student_count' => $t->student_count,
                    'staff_count'   => $t->staff_count,
                    'currency'      => $t->currency,
                    'timezone'      => $t->timezone,
                    'created_at'    => $t->created_at->format('d M Y'),
                ];
            });

        // ── Tenants bar chart data (top 6) ────────────────────────────────
        $barColors = ['#185FA5', '#3B6D11', '#993C1D', '#854F0B', '#534AB7', '#A32D2D'];
        $tenantBars = $tenants->take(6)->values()->map(function ($t, $i) use ($barColors) {
            return array_merge($t, ['bar_color' => $barColors[$i] ?? '#888780']);
        });

        // ── Recent audit logs (last 8) ─────────────────────────────────────
        // $auditLogs = AuditLog::with(['user', 'tenant'])
        //                 ->latest()
        //                 ->limit(8)
        //                 ->get()
        //                 ->map(fn($a) => [
        //                     'action'      => $a->action,
        //                     'entity_type' => $a->entity_type,
        //                     'tenant_name' => optional($a->tenant)->name ?? 'Platform',
        //                     'user_name'   => optional($a->user)->first_name . ' ' . optional($a->user)->last_name,
        //                     'time'        => $a->created_at->diffForHumans(),
        //                     'type'        => str_contains($a->action, 'creat') ? 'add'
        //                                     : (str_contains($a->action, 'delet') ? 'del' : 'edit'),
        //                 ]);

        // ── Data backups (latest per tenant) ──────────────────────────────
        // $backups = DataBackup::with('tenant')
        //                 ->latest('started_at')
        //                 ->limit(6)
        //                 ->get()
        //                 ->map(fn($b) => [
        //                     'tenant_name' => optional($b->tenant)->name ?? '—',
        //                     'backup_type' => ucfirst($b->backup_type),
        //                     'size_mb'     => $b->size_mb ? $b->size_mb . ' MB' : '—',
        //                     'status'      => $b->status,
        //                     'time'        => Carbon::parse($b->started_at)->format('d M, h:i A'),
        //                 ]);

        // ── System users (super admins — no tenant_id) ────────────────────
        $systemUsers = User::whereNull('tenant_id')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn($u) => [
                'name'     => $u->first_name . ' ' . $u->last_name,
                'email'    => $u->email,
                'initials' => strtoupper(substr($u->first_name, 0, 1) . substr($u->last_name, 0, 1)),
                'last'     => $u->updated_at->diffForHumans(),
                'status'   => $u->status,
            ]);

        // ── Module health (static list — extend with real checks if needed)
        $moduleHealth = collect([
            'Students',
            'Attendance',
            'Fees',
            'Exams',
            'Library',
            'Hostel',
            'Transport',
            'Payroll',
            'Notices',
        ])->map(fn($m) => ['name' => $m, 'status' => 'online']);

        return view('dashboard.super_admin', compact(
            'totalTenants',
            'newTenantsThisMonth',
            'totalStudents',
            'totalUsers',
            'newUsersThisWeek',
            'totalFeeCollected',
            'tenants',
            'tenantBars',
            // 'auditLogs', 'backups',
            'systemUsers',
            'moduleHealth'
        ));
    }
    public function adminDashboard()
    {
        return view('auth.dashboard');
    }
public function AcademicDashboard()
{
    $tenantId = Auth::user()->tenant_id;
    $today = Carbon::today();
    $monthStart = Carbon::now()->startOfMonth();

    // ----- Existing variables (unchanged) -----
    $totalStudents = Student::where('tenant_id', $tenantId)->count();
    $totalBranches = Stream::where('tenant_id', $tenantId)->count();
    $totalClasses = Classe::where('tenant_id', $tenantId)->count();
    $marksEntered = ExamMark::where('tenant_id', $tenantId)->count();
    $activeEnrollments = StudentClassEnrollment::where('tenant_id', $tenantId)->where('status', 'active')->count();
    $newAdmissionsThisMonth = StudentClassEnrollment::where('tenant_id', $tenantId)->whereDate('dateof_admission', '>=', $monthStart)->count();

    // Attendance today
    $todayAttendanceQuery = StudentAttendance::where('tenant_id', $tenantId)->whereDate('date', $today);
    $todayAttendanceRecords = (clone $todayAttendanceQuery)->count();
    $presentToday = (clone $todayAttendanceQuery)->where('status', 'present')->count();
    $absentToday = (clone $todayAttendanceQuery)->where('status', 'absent')->count();
    $attendanceRate = $todayAttendanceRecords > 0 ? round(($presentToday / $todayAttendanceRecords) * 100, 1) : null;

    // Exams
    $upcomingExams = Exam::where('tenant_id', $tenantId)->whereDate('exam_date', '>=', $today)->count();
    $examsThisMonth = Exam::where('tenant_id', $tenantId)->whereBetween('exam_date', [$monthStart, Carbon::now()->endOfMonth()])->count();
    $upcomingExamList = Exam::where('tenant_id', $tenantId)->with(['examType', 'subject'])->whereDate('exam_date', '>=', $today)->orderBy('exam_date')->limit(5)->get();

    // Recent marks & admissions
    $recentMarks = ExamMark::where('tenant_id', $tenantId)->with(['student', 'exam'])->latest()->limit(5)->get();
    $recentAdmissions = StudentClassEnrollment::where('tenant_id', $tenantId)->with(['student', 'class', 'section', 'stream'])->whereNotNull('dateof_admission')->latest('dateof_admission')->limit(5)->get();

    // Branch strength
    $topBranches = StudentClassEnrollment::where('tenant_id', $tenantId)->where('status', 'active')->selectRaw('stream_id, COUNT(*) as total')->with('stream')->groupBy('stream_id')->orderByDesc('total')->limit(5)->get();

    // Monthly admissions chart
    $monthlyAdmissions = collect(range(5, 0))->map(function ($monthsAgo) use ($tenantId) {
        $date = Carbon::now()->subMonths($monthsAgo);
        return [
            'label' => $date->format('M Y'),
            'total' => StudentClassEnrollment::where('tenant_id', $tenantId)->whereYear('dateof_admission', $date->year)->whereMonth('dateof_admission', $date->month)->count(),
        ];
    })->values();

    // ========== NEW WIDGETS (COMPLETELY SAFE) ==========

    // 1. Fee collection trend (safe – FeePayment exists)
    $monthlyFeeTrend = collect(range(5, 0))->map(function ($monthsAgo) use ($tenantId) {
        $date = Carbon::now()->subMonths($monthsAgo);
        return [
            'label' => $date->format('M Y'),
            'total' => (float) FeePayment::where('tenant_id', $tenantId)
                ->whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->sum('amount_paid'),
        ];
    })->values();

    // 2. Upcoming holidays (safe column detection)
    $upcomingHolidays = collect();
    if (Schema::hasTable('holidays')) {
        $dateColumn = Schema::hasColumn('holidays', 'date') ? 'date' : (Schema::hasColumn('holidays', 'holiday_date') ? 'holiday_date' : null);
        $titleColumn = Schema::hasColumn('holidays', 'title') ? 'title' : (Schema::hasColumn('holidays', 'name') ? 'name' : (Schema::hasColumn('holidays', 'holiday_name') ? 'holiday_name' : null));
        if ($dateColumn && $titleColumn) {
            $upcomingHolidays = \DB::table('holidays')
                ->where('tenant_id', $tenantId)
                ->where($dateColumn, '>=', $today)
                ->where($dateColumn, '<=', Carbon::now()->addDays(30))
                ->orderBy($dateColumn)
                ->limit(5)
                ->get([$titleColumn . ' as title', $dateColumn . ' as date']);
        }
    }

    // 3. Gender ratio (safe)
    $maleCount = Student::where('tenant_id', $tenantId)->where('gender', 'male')->count();
    $femaleCount = Student::where('tenant_id', $tenantId)->where('gender', 'female')->count();
    $otherCount = Student::where('tenant_id', $tenantId)->whereNotIn('gender', ['male', 'female'])->count();

    // 4. Monthly attendance trend (safe)
    $monthlyAttendanceTrend = collect(range(5, 0))->map(function ($monthsAgo) use ($tenantId) {
        $date = Carbon::now()->subMonths($monthsAgo);
        $start = $date->copy()->startOfMonth();
        $end = $date->copy()->endOfMonth();
        $attendanceRecords = StudentAttendance::where('tenant_id', $tenantId)
            ->whereBetween('date', [$start, $end])
            ->get();
        $total = $attendanceRecords->count();
        $present = $attendanceRecords->where('status', 'present')->count();
        $rate = $total > 0 ? round(($present / $total) * 100, 1) : 0;
        return ['label' => $date->format('M Y'), 'rate' => $rate];
    })->values();

    // 5. Recent fee payments (safe)
    $recentFeePayments = FeePayment::where('tenant_id', $tenantId)
        ->with('student')
        ->latest('payment_date')
        ->limit(5)
        ->get()
        ->map(fn($p) => [
            'student_name' => optional($p->student)->first_name . ' ' . optional($p->student)->last_name,
            'amount'       => $p->amount_paid,
            'date'         => optional($p->payment_date)->format('d M Y'),
            'receipt'      => $p->receipt_no ?? '—',
        ]);

    // 6. Student-Teacher Ratio (safe – no 'role' column, just count active staff)
    $totalTeachers = 0;
    if (class_exists(\App\Models\Staff::class) && Schema::hasTable('staff')) {
        if (Schema::hasColumn('staff', 'status')) {
            $totalTeachers = \App\Models\Staff::where('tenant_id', $tenantId)->where('status', 'active')->count();
        } else {
            $totalTeachers = \App\Models\Staff::where('tenant_id', $tenantId)->count();
        }
    }
    $studentTeacherRatio = $totalTeachers > 0 ? round($totalStudents / $totalTeachers, 1) : 0;

    // 7. Room utilization (completely safe – check columns first)
    $totalRooms = 0;
    $usedRoomsToday = 0;
    $roomUtilizationPercent = 0;
    
    // Check rooms table
    if (class_exists(\App\Models\Room::class) && Schema::hasTable('rooms')) {
        $totalRooms = \App\Models\Room::where('tenant_id', $tenantId)->count();
    }
    
    // Check timetables table and required columns
    if (class_exists(\App\Models\Timetable::class) && Schema::hasTable('timetables')) {
        // Check if room_id column exists
        if (Schema::hasColumn('timetables', 'room_id')) {
            // Check date column – could be 'date' or 'timetable_date'
            $dateColumn = Schema::hasColumn('timetables', 'date') ? 'date' : 
                         (Schema::hasColumn('timetables', 'timetable_date') ? 'timetable_date' : null);
            if ($dateColumn) {
                $usedRoomsToday = \App\Models\Timetable::where('tenant_id', $tenantId)
                    ->whereDate($dateColumn, $today)
                    ->distinct('room_id')
                    ->count('room_id');
            }
        }
    }
    
    if ($totalRooms > 0) {
        $roomUtilizationPercent = round(($usedRoomsToday / $totalRooms) * 100, 1);
    }

    return view('Academics.dashboard', compact(
        'totalStudents', 'totalBranches', 'totalClasses', 'marksEntered', 'activeEnrollments',
        'newAdmissionsThisMonth', 'todayAttendanceRecords', 'presentToday', 'absentToday',
        'attendanceRate', 'upcomingExams', 'examsThisMonth', 'upcomingExamList',
        'recentMarks', 'recentAdmissions', 'topBranches', 'monthlyAdmissions',
        'monthlyFeeTrend', 'upcomingHolidays', 'maleCount', 'femaleCount', 'otherCount',
        'monthlyAttendanceTrend', 'recentFeePayments', 'studentTeacherRatio', 'totalTeachers',
        'roomUtilizationPercent', 'totalRooms', 'usedRoomsToday'
    ));
}
    // public function AccountDashboard()
    // {
    //     $totalStudents = Student::where('tenant_id', Auth::user()->tenant_id)->count();
    //     $totalCollected = FeePayment::where('tenant_id', Auth::user()->tenant_id)->sum('amount_paid');
    //     $todayCollected = FeePayment::where('tenant_id', Auth::user()->tenant_id)
    //         ->whereDate('payment_date', today())
    //         ->sum('amount_paid');
    //     $totalOutstanding = FeeInstallment::where('tenant_id', Auth::user()->tenant_id)
    //         ->whereIn('status', ['unpaid', 'partial'])
    //         ->sum('amount');

    //     $recentPayments = FeePayment::where('tenant_id', Auth::user()->tenant_id)
    //         ->with('student')
    //         ->latest('paid_at')
    //         ->limit(5)
    //         ->get()
    //         ->map(fn($p) => [
    //             'student_name' => $p->student->first_name . ' ' . $p->student->last_name,
    //             'amount_paid' => $p->amount_paid,
    //             'payment_date' => $p->payment_date
    //         ]);

    //     $topOutstanding = Student::whereHas(
    //         'installments',
    //         fn($q) =>
    //         $q->whereIn('status', ['unpaid', 'partial'])
    //     )
    //         ->with(['installments' => fn($q) => $q->whereIn('status', ['unpaid', 'partial'])])
    //         ->get()
    //         ->map(fn($s) => [
    //             'name' => $s->first_name . ' ' . $s->last_name,
    //             'class_name' => $s->currentClass?->name ?? 'N/A',
    //             'outstanding' => $s->installments->sum('amount'),
    //             'latest_due_date' => $s->installments->sortByDesc('due_date')->first()?->due_date
    //         ])
    //         ->sortByDesc('outstanding')
    //         ->take(5);

    //     return view('Accountants.dashboard', compact(
    //         'totalStudents',
    //         'totalCollected',
    //         'todayCollected',
    //         'totalOutstanding',
    //         'recentPayments',
    //         'topOutstanding'
    //     ));
    //     // return view('Accountants.dashboard');
    // }
    // public function AccountDashboard()
    // {
    //     $tenantId = auth()->user()->tenant_id;

    //     // Income Stats
    //     $totalIncome = IncomeRecord::where('tenant_id', $tenantId)->sum('amount');
    //     $todayIncome = IncomeRecord::where('tenant_id', $tenantId)
    //         ->whereDate('date', today())->sum('amount');
    //     $thisMonthIncome = IncomeRecord::where('tenant_id', $tenantId)
    //         ->whereMonth('date', now()->month)->sum('amount');

    //     // Expense Stats  
    //     $totalExpense = ExpenseRecord::where('tenant_id', $tenantId)->sum('amount');
    //     $todayExpense = ExpenseRecord::where('tenant_id', $tenantId)
    //         ->whereDate('date', today())->sum('amount');
    //     $thisMonthExpense = ExpenseRecord::where('tenant_id', $tenantId)
    //         ->whereMonth('date', now()->month)->sum('amount');

    //     // Profit/Loss
    //     $netProfit = $totalIncome - $totalExpense;
    //     $profitPercent = $totalIncome > 0 ? ($netProfit / $totalIncome) * 100 : 0;

    //     // Recent Income (Last 5)
    //     $recentIncome = IncomeRecord::with('incomeHead')
    //         ->where('tenant_id', $tenantId)
    //         ->latest('date')
    //         ->limit(5)
    //         ->get()
    //         ->map(fn($r) => [
    //             'head' => $r->incomeHead->name,
    //             'amount' => $r->amount,
    //             'date' => $r->date->format('d M'),
    //             'id' => $r->id
    //         ]);

    //     // Recent Expenses (Last 5)
    //     $recentExpenses = ExpenseRecord::with('expenseHead')
    //         ->where('tenant_id', $tenantId)
    //         ->latest('date')
    //         ->limit(5)
    //         ->get()
    //         ->map(fn($r) => [
    //             'head' => $r->expenseHead->name,
    //             'amount' => $r->amount,
    //             'date' => $r->date->format('d M'),
    //             'id' => $r->id
    //         ]);

    //     // Monthly Data for Chart (Last 6 months)
    //     $monthlyIncome = [];
    //     $monthlyExpense = [];
    //     for ($i = 5; $i >= 0; $i--) {
    //         $month = now()->subMonths($i);
    //         $monthlyIncome[] = IncomeRecord::where('tenant_id', $tenantId)
    //             ->whereYear('date', $month->year)
    //             ->whereMonth('date', $month->month)
    //             ->sum('amount');
    //         $monthlyExpense[] = ExpenseRecord::where('tenant_id', $tenantId)
    //             ->whereYear('date', $month->year)
    //             ->whereMonth('date', $month->month)
    //             ->sum('amount');
    //     }
    //     $months = collect(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'])->reverse()->values()->toArray();

    //     return view('Accountants.dashboard', compact(
    //         'totalIncome',
    //         'totalExpense',
    //         'netProfit',
    //         'profitPercent',
    //         'todayIncome',
    //         'todayExpense',
    //         'thisMonthIncome',
    //         'thisMonthExpense',
    //         'recentIncome',
    //         'recentExpenses',
    //         'monthlyIncome',
    //         'monthlyExpense',
    //         'months'
    //     ));
    // }
    public function AccountDashboard()
    {
        $tenantId = auth()->user()->tenant_id;
        $today = now();

        $totalIncome = IncomeRecord::where('tenant_id', $tenantId)->sum('amount');
        $totalExpense = ExpenseRecord::where('tenant_id', $tenantId)->sum('amount');
        $todayIncome = IncomeRecord::where('tenant_id', $tenantId)
            ->whereDate('date', $today)
            ->sum('amount');
        $todayExpense = ExpenseRecord::where('tenant_id', $tenantId)
            ->whereDate('date', $today)
            ->sum('amount');
        $thisMonthIncome = IncomeRecord::where('tenant_id', $tenantId)
            ->whereYear('date', $today->year)
            ->whereMonth('date', $today->month)
            ->sum('amount');
        $thisMonthExpense = ExpenseRecord::where('tenant_id', $tenantId)
            ->whereYear('date', $today->year)
            ->whereMonth('date', $today->month)
            ->sum('amount');

        $stats = [
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'todayIncome' => $todayIncome,
            'todayExpense' => $todayExpense,
            'thisMonthIncome' => $thisMonthIncome,
            'thisMonthExpense' => $thisMonthExpense,
            'incomeHeadsCount' => IncomeHead::where('tenant_id', $tenantId)->count(),
            'expenseHeadsCount' => ExpenseHead::where('tenant_id', $tenantId)->count(),
            'netBalance' => $totalIncome - $totalExpense,
            'monthlyNet' => $thisMonthIncome - $thisMonthExpense,
            'transactionsThisMonth' => IncomeRecord::where('tenant_id', $tenantId)
                ->whereYear('date', $today->year)
                ->whereMonth('date', $today->month)
                ->count() +
                ExpenseRecord::where('tenant_id', $tenantId)
                ->whereYear('date', $today->year)
                ->whereMonth('date', $today->month)
                ->count(),
        ];

        $recentIncome = IncomeRecord::with('incomeHead')
            ->where('tenant_id', $tenantId)
            ->latest('date')
            ->limit(5)
            ->get();

        $recentExpenses = ExpenseRecord::with('expenseHead')
            ->where('tenant_id', $tenantId)
            ->latest('date')
            ->limit(5)
            ->get();

        $trendLabels = [];
        $incomeTrend = [];
        $expenseTrend = [];

        foreach (range(5, 0) as $offset) {
            $month = now()->subMonths($offset);
            $trendLabels[] = $month->format('M Y');
            $incomeTrend[] = (float) IncomeRecord::where('tenant_id', $tenantId)
                ->whereYear('date', $month->year)
                ->whereMonth('date', $month->month)
                ->sum('amount');
            $expenseTrend[] = (float) ExpenseRecord::where('tenant_id', $tenantId)
                ->whereYear('date', $month->year)
                ->whereMonth('date', $month->month)
                ->sum('amount');
        }

        $topIncomeHeads = IncomeRecord::with('incomeHead')
            ->where('tenant_id', $tenantId)
            ->selectRaw('income_head_id, SUM(amount) as total_amount')
            ->groupBy('income_head_id')
            ->orderByDesc('total_amount')
            ->limit(5)
            ->get();

        $topExpenseHeads = ExpenseRecord::with('expenseHead')
            ->where('tenant_id', $tenantId)
            ->selectRaw('expense_head_id, SUM(amount) as total_amount')
            ->groupBy('expense_head_id')
            ->orderByDesc('total_amount')
            ->limit(5)
            ->get();

        return view('Accountants.dashboard', compact(
            'stats',
            'recentIncome',
            'recentExpenses',
            'trendLabels',
            'incomeTrend',
            'expenseTrend',
            'topIncomeHeads',
            'topExpenseHeads'
        ));
    }

    // public function LibraryDashboard()
    // {
    //     $totalBooks       = Books::count();
    //     $availableCopies  = Books::sum('available_copies');
    //     $issuedBooks      = BookIssues::where('status', 'issued')->count();
    //     $overdueBooks     = BookIssues::where('status', 'overdue')->count();
    //     $totalFines       = BookIssues::sum('fine_amount') + BookFines::sum('amount');
    //     $activeCards      = LibraryCards::where(function ($q) {
    //         $q->whereNull('expiry_date')
    //             ->orWhere('expiry_date', '>=', now()->format('Y-m-d'));
    //     })->count();

    //     $recentIssues = BookIssues::with(['book', 'student'])
    //         ->orderBy('issue_date', 'desc')
    //         ->limit(10)
    //         ->get();

    //     return view('library.dashboard', compact(
    //         'totalBooks',
    //         'availableCopies',
    //         'issuedBooks',
    //         'overdueBooks',
    //         'totalFines',
    //         'activeCards',
    //         'recentIssues'
    //     ));
    // }
    public function LibraryDashboard()
    {
        $tenantId = auth()->user()->tenant_id;

        // ── Core counts ──────────────────────────────────────────────
        $totalBooks = Books::where('tenant_id', $tenantId)->count();

        $availableCopies = Books::where('tenant_id', $tenantId)
            ->where('status', 'available')
            ->count();

        $issuedBooks = BookIssues::where('tenant_id', $tenantId)
            ->where('status', 'issued')
            ->count();

        // Real overdue: issued books whose due_date has passed
        $overdueBooks = BookIssues::where('tenant_id', $tenantId)
            ->where('status', 'issued')
            ->where('due_date', '<', now()->toDateString())
            ->count();

        // ── Financials ───────────────────────────────────────────────
        // Sum fine_amount from returned/overdue records
        $totalFines = BookIssues::where('tenant_id', $tenantId)
            ->whereIn('status', ['returned', 'overdue'])
            ->sum('fine_amount');

        // ── Library cards ────────────────────────────────────────────
        $activeCards = LibraryCards::where('tenant_id', $tenantId)
            ->where(function ($q) {
                $q->whereNull('expiry_date')
                    ->orWhere('expiry_date', '>=', now()->toDateString());
            })
            ->count();

        // ── Recent issues (last 10) ───────────────────────────────────
        // Eager-load book, student and staff so the blade has no N+1 queries
        $recentIssues = BookIssues::with(['book', 'student', 'staff', 'libraryCard'])
            ->where('tenant_id', $tenantId)
            ->orderBy('issue_date', 'desc')
            ->limit(5)
            ->get();

        // ── Monthly trend for current year ───────────────────────────
        // Returns an array keyed by month number (1–12) with issue counts
        // e.g. [1 => 12, 3 => 25, ...]  — missing months simply won't appear
        $monthlyIssues = BookIssues::where('tenant_id', $tenantId)
            ->whereYear('issue_date', now()->year)
            ->selectRaw('MONTH(issue_date) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        return view('Library.dashboard', compact(
            'totalBooks',
            'availableCopies',
            'issuedBooks',
            'overdueBooks',
            'totalFines',
            'activeCards',
            'recentIssues',
            'monthlyIssues',
        ));
    }

    public function TravelsDashboard()
    {
        $totalVehicles          = Vehicle::count();
        $totalDrivers           = Driver::count();
        $totalRoutes            = Routes::count();
        $totalStops             = RouteStop::count();
        $totalTransportStudents = StudentRouteMapping::distinct('student_id')->count('student_id');
        $totalTransportFee      = StudentRouteMapping::sum('transport_fee');

        $recentRoutes = Routes::with(['vehicle', 'stops'])
            ->withCount('stops as stops_count')
            ->latest()
            ->limit(10)
            ->get();

        $recentMappings = StudentRouteMapping::with(['student', 'route', 'stop'])
            ->latest()
            ->limit(10)
            ->get();

        $recentVehicles = Vehicle::with('driver')->latest()->limit(10)->get();

        return view('Travels.dashboard', compact(
            'totalVehicles',
            'totalDrivers',
            'totalRoutes',
            'totalStops',
            'totalTransportStudents',
            'totalTransportFee',
            'recentRoutes',
            'recentMappings',
            'recentVehicles'
        ));
        // return view('Travels.dashboard');
    }

    public function CommunicationsDashboard()
    {
        return view('Communication.dashboard');
    }
    public function HostelDashboard()
    {
        $tenantId = auth()->user()->tenant_id;

        // ✅ Stats with change % (last 30 days vs previous)
        $totalHostels = Hostel::where('tenant_id', $tenantId)->count();
        $totalRooms = Hostelroom::where('tenant_id', $tenantId)->count();
        $totalBeds = Hostelbed::where('tenant_id', $tenantId)->count();
        $occupiedBeds = Hostelbed::where('tenant_id', $tenantId)
            ->where('status', 'occupied')->count();
        $vacancyPercent = $totalBeds > 0 ? round((($totalBeds - $occupiedBeds) / $totalBeds) * 100, 1) : 0;
        $totalResidents = HostelAllocation::where('tenant_id', $tenantId)
            ->whereNull('to_date') // Current residents only
            ->distinct('student_id')->count('student_id');
        $totalDueFees = 0; // Calculate from allocations/fees

        // Recent allocations (last 30 days)
        $recentAllocations = HostelAllocation::with(['student', 'hostel', 'room', 'bed'])
            ->where('tenant_id', $tenantId)
            ->where('from_date', '>=', now()->subDays(30))
            ->latest()
            ->limit(10)
            ->get();

        return view('hostel.dashboard', compact(
            'totalHostels',
            'totalRooms',
            'totalBeds',
            'occupiedBeds',
            'vacancyPercent',
            'totalResidents',
            'totalDueFees',
            'recentAllocations'
        ));
    }
}
