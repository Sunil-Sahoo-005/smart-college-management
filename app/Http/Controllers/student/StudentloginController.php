<?php

namespace App\Http\Controllers\student;

use App\Http\Controllers\Controller;
use App\Models\BookIssues;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\ExamType;
use App\Models\Holidays;
use App\Models\MergedClassSchedule;
use App\Models\NoticeBoard;
use App\Models\StudentAttendance;
use App\Models\StudentClassEnrollment;
use App\Models\Term;
use App\Models\Timetable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class StudentloginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required',
        ], [
            'username.required' => 'Username or email is required',
            'password.required' => 'Password is required',
        ]);

        $loginValue = trim((string) $request->username);

        $user = DB::table('students')
            ->where(function ($query) use ($loginValue) {
                $query->where('username', $loginValue)
                    ->orWhere('email', $loginValue)
                    ->orWhere('admission_number', $loginValue);
            })
            ->first();

        // Username not found
        if (!$user) {
            return back()->withErrors([
                'username' => 'This student login was not found.'
            ])->withInput();
        }

        // Password incorrect
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'Incorrect password.'
            ])->withInput();
        }

        // Login success
        $request->session()->regenerate();

        $request->session()->put([
            'user_id' => $user->id,
            'username' => $user->username,
            'tenant_id' => $user->tenant_id,
            'portal_type' => 'student'
        ]);

        return redirect()->route('student.studentsacademic.dashboard')
            ->with('success', 'Logged in successfully');
    }
    public function logout(Request $request)
    {
        // remove specific session
        $request->session()->forget(['user_id', 'username', 'tenant_id']);

        // OR destroy all session
        $request->session()->flush();

        return redirect('/studentsacademic')->with('success', 'Logged out successfully');
    }
    public function dashboard()
    {
        $studentId = session('user_id');

        // 👇 Student Info
        $student = DB::table('students')
            ->leftJoin('student_class_enrollments', 'students.id', '=', 'student_class_enrollments.student_id')
            ->leftJoin('streams', 'student_class_enrollments.stream_id', '=', 'streams.id')
            ->leftJoin('academic_years', 'student_class_enrollments.academic_year_id', '=', 'academic_years.id')
            ->leftJoin('classes', 'student_class_enrollments.class_id', '=', 'classes.id')
            ->select(
                'students.*',
                'streams.name as stream_name',
                'academic_years.name as academic_name',
                'classes.name as class_name'
            )
            ->where('students.id', $studentId)
            ->first();

        // 👇 Category-wise Fees
        $categoryFees = DB::table('fee_installments')
            ->join('fee_categories', 'fee_installments.fee_category_id', '=', 'fee_categories.id')
            ->where('fee_installments.student_id', $studentId)
            ->select(
                'fee_categories.name as category_name',
                DB::raw('SUM(fee_installments.amount) as total_amount'),
                DB::raw("SUM(CASE WHEN fee_installments.status = 'paid' THEN fee_installments.amount ELSE 0 END) as paid_amount"),
                DB::raw("SUM(CASE WHEN fee_installments.status = 'unpaid' THEN fee_installments.amount ELSE 0 END) as due_amount")
            )
            ->groupBy('fee_categories.name')
            ->get();

        // 👇 Total Fees
        $totalFees = DB::table('fee_installments')
            ->where('student_id', $studentId)
            ->sum('amount');

        // 👇 Paid Fees
        $paidFees = DB::table('fee_installments')
            ->where('student_id', $studentId)
            ->where('status', 'paid')
            ->sum('amount');

        // 👇 Due Fees
        $dueFees = DB::table('fee_installments')
            ->where('student_id', $studentId)
            ->where('status', 'unpaid')
            ->sum('amount');

        return view('studentacademic.dashboard', compact(
            'student',
            'categoryFees',
            'totalFees',
            'paidFees',
            'dueFees'
        ));
    }


    public function portalDashboard()
    {
        $studentId = session('user_id');
        $today = Carbon::today();

        if (!$studentId) {
            return redirect('/studentsacademic')->withErrors([
                'username' => 'Please log in to continue.'
            ]);
        }

        $student = DB::table('students')
            ->leftJoin('student_class_enrollments', 'students.id', '=', 'student_class_enrollments.student_id')
            ->leftJoin('streams', 'student_class_enrollments.stream_id', '=', 'streams.id')
            ->leftJoin('academic_years', 'student_class_enrollments.academic_year_id', '=', 'academic_years.id')
            ->leftJoin('classes', 'student_class_enrollments.class_id', '=', 'classes.id')
            ->leftJoin('sections', 'student_class_enrollments.section_id', '=', 'sections.id')
            ->select(
                'students.*',
                'student_class_enrollments.id as enrollment_id',
                'streams.name as stream_name',
                'academic_years.name as academic_name',
                'classes.name as class_name',
                'sections.name as section_name'
            )
            ->where('students.id', $studentId)
            ->first();

        if (!$student) {
            return redirect('/studentsacademic')->withErrors([
                'username' => 'Student account could not be found.'
            ]);
        }

        $tenantId = $student->tenant_id;

        $categoryFees = DB::table('fee_installments')
            ->join('fee_categories', 'fee_installments.fee_category_id', '=', 'fee_categories.id')
            ->where('fee_installments.student_id', $studentId)
            ->select(
                'fee_categories.name as category_name',
                DB::raw('SUM(fee_installments.amount) as total_amount'),
                DB::raw("SUM(CASE WHEN fee_installments.status = 'paid' THEN fee_installments.amount ELSE 0 END) as paid_amount"),
                DB::raw("SUM(CASE WHEN fee_installments.status IN ('unpaid', 'partial') THEN fee_installments.amount ELSE 0 END) as due_amount")
            )
            ->groupBy('fee_categories.name')
            ->get();

        $totalFees = DB::table('fee_installments')
            ->where('student_id', $studentId)
            ->sum('amount');

        $paidFees = DB::table('fee_installments')
            ->where('student_id', $studentId)
            ->where('status', 'paid')
            ->sum('amount');

        $dueFees = DB::table('fee_installments')
            ->where('student_id', $studentId)
            ->whereIn('status', ['unpaid', 'partial'])
            ->sum('amount');

        $attendanceEnrollmentId = $student->enrollment_id ?? null;

        $attendanceBaseQuery = StudentAttendance::query()
            ->when($attendanceEnrollmentId, fn ($query) => $query->where('enrollment_id', $attendanceEnrollmentId))
            ->when(!$attendanceEnrollmentId, fn ($query) => $query->whereRaw('1 = 0'))
            ->when($tenantId, fn ($query) => $query->where('tenant_id', $tenantId));

        $totalAttendanceRecords = (clone $attendanceBaseQuery)->count();
        $presentAttendanceRecords = (clone $attendanceBaseQuery)
            ->whereIn('status', ['present', 'late'])
            ->count();
        $attendancePercentage = $totalAttendanceRecords > 0
            ? round(($presentAttendanceRecords / $totalAttendanceRecords) * 100)
            : 0;

        $monthlyAttendanceQuery = StudentAttendance::query()
            ->when($attendanceEnrollmentId, fn ($query) => $query->where('enrollment_id', $attendanceEnrollmentId))
            ->when(!$attendanceEnrollmentId, fn ($query) => $query->whereRaw('1 = 0'))
            ->when($tenantId, fn ($query) => $query->where('tenant_id', $tenantId))
            ->whereMonth('date', $today->month)
            ->whereYear('date', $today->year);

        $monthlyAttendanceTotal = (clone $monthlyAttendanceQuery)->count();
        $monthlyAttendancePresent = (clone $monthlyAttendanceQuery)
            ->whereIn('status', ['present', 'late'])
            ->count();
        $monthlyAttendancePercentage = $monthlyAttendanceTotal > 0
            ? round(($monthlyAttendancePresent / $monthlyAttendanceTotal) * 100)
            : null;

        $bookIssueQuery = BookIssues::query()
            ->where('student_id', $studentId)
            ->when($tenantId, fn ($query) => $query->where('tenant_id', $tenantId));

        $issuedBooksCount = (clone $bookIssueQuery)
            ->where('status', 'issued')
            ->count();

        $overdueBooksCount = (clone $bookIssueQuery)
            ->where('status', 'issued')
            ->whereDate('due_date', '<', $today)
            ->count();

        $nextBookDue = (clone $bookIssueQuery)
            ->where('status', 'issued')
            ->whereDate('due_date', '>=', $today)
            ->orderBy('due_date')
            ->first();

        $transport = DB::table('student_route_mappings')
            ->join('routes', 'student_route_mappings.route_id', '=', 'routes.id')
            ->join('route_stops', 'student_route_mappings.stop_id', '=', 'route_stops.id')
            ->where('student_route_mappings.student_id', $studentId)
            ->select(
                'routes.name as route_name',
                'routes.start_point',
                'routes.end_point',
                'route_stops.stop_name',
                'route_stops.arrival_time',
                'student_route_mappings.transport_fee'
            )
            ->first();

        $notices = NoticeBoard::query()
            ->when($tenantId, fn ($query) => $query->where('tenant_id', $tenantId))
            ->latest('notice_date')
            ->limit(4)
            ->get();

        $holidays = Holidays::query()
            ->when($tenantId, fn ($query) => $query->where('tenant_id', $tenantId))
            ->whereDate('date', '>=', $today)
            ->orderBy('date')
            ->limit(5)
            ->get();

        $results = DB::table('exam_marks')
            ->join('exams', 'exam_marks.exam_id', '=', 'exams.id')
            ->where('exam_marks.student_id', $studentId)
            ->select(
                'exams.name as exam_name',
                'exams.total_marks',
                'exam_marks.marks_obtained'
            )
            ->get();

        $examAverage = $results->count() > 0
            ? round($results->avg(function ($result) {
                return $result->total_marks > 0
                    ? ($result->marks_obtained / $result->total_marks) * 100
                    : 0;
            }), 1)
            : null;

        $latestExam = $results->last();

        $nextFeeDue = DB::table('fee_installments')
            ->join('fee_categories', 'fee_installments.fee_category_id', '=', 'fee_categories.id')
            ->where('fee_installments.student_id', $studentId)
            ->whereIn('fee_installments.status', ['unpaid', 'partial'])
            ->orderBy('fee_installments.due_date')
            ->select(
                'fee_categories.name as category_name',
                'fee_installments.amount',
                'fee_installments.due_date',
                'fee_installments.status'
            )
            ->first();

        // 👇 Recent Payments
        $recentPayments = DB::table('fee_payments')
            ->where('student_id', $studentId)
            ->latest('paid_at')
            ->limit(5)
            ->get();

        return view('studentacademic.dashboard', compact(
            'student',
            'categoryFees',
            'totalFees',
            'paidFees',
            'dueFees',
            'attendancePercentage',
            'monthlyAttendancePercentage',
            'issuedBooksCount',
            'overdueBooksCount',
            'nextBookDue',
            'transport',
            'notices',
            'holidays',
            'examAverage',
            'latestExam',
            'nextFeeDue',
            'recentPayments'
        ));
    }

    public function timetable(Request $request)
    {
        $studentId = session('user_id');

        if (!$studentId) {
            return redirect('/studentsacademic')->withErrors([
                'username' => 'Please log in to continue.'
            ]);
        }

        // 👇 Student Info
        $student = DB::table('students')
            ->leftJoin('student_class_enrollments', 'students.id', '=', 'student_class_enrollments.student_id')
            ->leftJoin('streams', 'student_class_enrollments.stream_id', '=', 'streams.id')
            // ->leftJoin('academic_years', 'student_class_enrollments.academic_year_id', '=', 'academic_years.id')
            ->leftJoin('classes', 'student_class_enrollments.class_id', '=', 'classes.id')
            ->leftJoin('sections', 'student_class_enrollments.section_id', '=', 'sections.id')
            ->select(
                'students.*',
                'streams.name as stream_name',
                // 'academic_years.name as academic_name',
                'classes.name as class_name',
                'sections.name as section_name'
            )
            ->where('students.id', $studentId)
            ->first();

        if (!$student) {
            return redirect('/studentsacademic')->withErrors([
                'username' => 'Student account could not be found.'
            ]);
        }

        $tenantId = $student->tenant_id;

        // 👇 Get active enrollments for the student
        $enrollments = StudentClassEnrollment::where('tenant_id', $tenantId)
            ->where('student_id', $student->id)
            ->where('status', 'active')
            ->with(['class', 'section', 'stream', 'academicYear'])
            ->get();

        // 👇 Load timetables for each enrollment
        $timetables = collect();
        foreach ($enrollments as $enrollment) {
            $timetablesForEnrollment = Timetable::where('tenant_id', $tenantId)
                ->where('class_id', $enrollment->class_id)
                ->where('section_id', $enrollment->section_id)
                ->where('stream_id', $enrollment->stream_id)
                // ->where('academic_year_id', $enrollment->academic_year_id)
                ->with(['slots.period', 'slots.subject', 'slots.staff', 'slots.room'])
                ->get();

            $timetables = $timetables->concat($timetablesForEnrollment);
        }

        // 👇 Load merged schedules for current week
        $startOfWeek = now()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = now()->endOfWeek(Carbon::SATURDAY);

        $mergedSchedules = MergedClassSchedule::where('tenant_id', $tenantId)
            ->whereBetween('schedule_date', [$startOfWeek, $endOfWeek])
            ->with(['subject', 'room', 'period', 'staff'])
            ->get()
            ->filter(function ($schedule) use ($enrollments) {
                $combinations = collect($schedule->class_combinations ?? []);

                return $enrollments->contains(function ($enrollment) use ($combinations) {
                    return $combinations->contains(function ($combo) use ($enrollment) {
                        return (int) ($combo['stream_id'] ?? 0) === (int) $enrollment->stream_id
                            && (int) ($combo['class_id'] ?? 0) === (int) $enrollment->class_id
                            && (int) ($combo['section_id'] ?? 0) === (int) $enrollment->section_id;
                    });
                });
            })
            ->values();

        $mergedByPeriodAndDay = $mergedSchedules->groupBy('period_id')->map(function ($periodGroup) {
            return $periodGroup->keyBy(fn ($schedule) => Carbon::parse($schedule->schedule_date)->dayOfWeekIso);
        });

        $mergedPeriods = $mergedSchedules->pluck('period')->filter()->unique('id')->sortBy('id')->values();

        return view('studentacademic.timetable', compact(
            'student', 'enrollments', 'timetables',
            'mergedSchedules', 'mergedByPeriodAndDay', 'mergedPeriods', 'startOfWeek', 'endOfWeek'
        ));
    }
    public function career()
    {
        $studentId = session('user_id');

        // 👇 Student Info
        $student = DB::table('students')
            ->leftJoin('student_class_enrollments', 'students.id', '=', 'student_class_enrollments.student_id')
            ->leftJoin('streams', 'student_class_enrollments.stream_id', '=', 'streams.id')
            ->leftJoin('academic_years', 'student_class_enrollments.academic_year_id', '=', 'academic_years.id')
            ->leftJoin('classes', 'student_class_enrollments.class_id', '=', 'classes.id')
            ->select(
                'students.*',
                'streams.name as stream_name',
                'academic_years.name as academic_name',
                'classes.name as class_name'
            )
            ->where('students.id', $studentId)
            ->first();

        // 👇 Exam + Result Join
        $results = DB::table('exam_marks')
            ->join('exams', 'exam_marks.exam_id', '=', 'exams.id')
            ->where('exam_marks.student_id', $studentId)
            ->select(
                'exams.name as exam_name',
                'exams.total_marks',
                'exam_marks.marks_obtained'
            )
            ->get();

        return view('studentacademic.careerdetails', compact('student', 'results'));
    }
    public function myIssuedBooks()
    {
        $studentId = session('user_id'); // login user id


        $student = DB::table('students')
            ->leftJoin('student_class_enrollments', 'students.id', '=', 'student_class_enrollments.student_id')
            ->leftJoin('streams', 'student_class_enrollments.stream_id', '=', 'streams.id')
            ->leftJoin('academic_years', 'student_class_enrollments.academic_year_id', '=', 'academic_years.id')
            ->leftJoin('classes', 'student_class_enrollments.class_id', '=', 'classes.id')
            ->select(
                'students.*',
                'streams.name as stream_name',
                'academic_years.name as academic_name',
                'classes.name as class_name'
            )
            ->where('students.id', $studentId)
            ->first();

        $bookIssues = DB::table('book_issues')
            ->join('books', 'book_issues.book_id', '=', 'books.id')
            ->where('book_issues.student_id', $studentId)
            ->select(
                'book_issues.id',
                'books.title',
                'books.author',
                'books.isbn',
                'book_issues.issue_date',
                'book_issues.due_date',
                'book_issues.return_date',
                'book_issues.fine_amount',
                'book_issues.status'
            )
            ->get();

        return view('studentacademic.librarydetails', compact('bookIssues', 'student'));
    }
    public function myTransport()
    {
        $studentId = session('user_id'); // login student id


        $student = DB::table('students')
            ->leftJoin('student_class_enrollments', 'students.id', '=', 'student_class_enrollments.student_id')
            ->leftJoin('streams', 'student_class_enrollments.stream_id', '=', 'streams.id')
            ->leftJoin('academic_years', 'student_class_enrollments.academic_year_id', '=', 'academic_years.id')
            ->leftJoin('classes', 'student_class_enrollments.class_id', '=', 'classes.id')
            ->select(
                'students.*',
                'streams.name as stream_name',
                'academic_years.name as academic_name',
                'classes.name as class_name'
            )
            ->where('students.id', $studentId)
            ->first();


        $transport = DB::table('student_route_mappings')
            ->join('routes', 'student_route_mappings.route_id', '=', 'routes.id')
            ->join('route_stops', 'student_route_mappings.stop_id', '=', 'route_stops.id')
            ->where('student_route_mappings.student_id', $studentId)
            ->select(
                'routes.name as route_name',
                'routes.start_point',
                'routes.end_point',
                'route_stops.stop_name',
                'route_stops.arrival_time',
                'student_route_mappings.transport_fee'
            )
            ->first();   // ek student ka usually ek route hota hai

        return view('studentacademic.hostel', compact('transport', 'student'));
    }
    public function account()
    {
        $studentId = session('user_id');
        $tenantId = session('tenant_id'); // Assuming you store tenant_id in session

        // Student Info
        $student = DB::table('students')
            ->leftJoin('student_class_enrollments', 'students.id', '=', 'student_class_enrollments.student_id')
            ->leftJoin('streams', 'student_class_enrollments.stream_id', '=', 'streams.id')
            ->leftJoin('academic_years', 'student_class_enrollments.academic_year_id', '=', 'academic_years.id')
            ->leftJoin('classes', 'student_class_enrollments.class_id', '=', 'classes.id')
            ->select(
                'students.*',
                'streams.name as stream_name',
                'academic_years.name as academic_name',
                'classes.name as class_name'
            )
            ->where('students.id', $studentId)
            ->first();

        // 🔥 UNPAID INSTALLMENTS ONLY (for payment)
        $unpaidInstallments = DB::table('fee_installments as fi')
            ->join('fee_categories as fc', 'fi.fee_category_id', '=', 'fc.id')
            ->leftJoin('fee_payments as fp', function ($join) {
                $join->on('fi.id', 'fp.fee_installment_id');
                //  ->where('fp.status', 'completed');
            })
            ->where('fi.student_id', $studentId)
            ->where('fi.status', '!=', 'paid')
            ->select(
                'fi.id',
                'fi.amount',
                'fi.due_date',
                'fc.name as category_name',
                DB::raw('COALESCE(fp.amount_paid, 0) as already_paid'),
                DB::raw('(fi.amount - COALESCE(fp.amount_paid, 0)) as remaining_amount')
            )
            ->havingRaw('remaining_amount > 0')
            ->get();

        // Category-wise summary
        $categoryFees = DB::table('fee_installments')
            ->join('fee_categories', 'fee_installments.fee_category_id', '=', 'fee_categories.id')
            ->where('fee_installments.student_id', $studentId)
            ->select(
                'fee_categories.name as category_name',
                DB::raw('SUM(fee_installments.amount) as total_amount'),
                DB::raw("SUM(CASE WHEN fee_installments.status = 'paid' THEN fee_installments.amount ELSE 0 END) as paid_amount"),
                DB::raw("SUM(CASE WHEN fee_installments.status != 'paid' THEN fee_installments.amount ELSE 0 END) as due_amount")
            )
            ->groupBy('fee_categories.name')
            ->get();

        // Totals
        $totalFees = DB::table('fee_installments')->where('student_id', $studentId)->sum('amount');
        $paidFees = DB::table('fee_installments')->where('student_id', $studentId)->where('status', 'paid')->sum('amount');
        $dueFees = $totalFees - $paidFees;

        // 🔥 Payment History
        $paymentHistory = DB::table('fee_payments')
            ->where('student_id', $studentId)
            ->orderByDesc('paid_at')
            ->get();

        return view('studentacademic.accountdetails', compact(
            'student',
            'categoryFees',
            'totalFees',
            'paidFees',
            'dueFees',
            'unpaidInstallments',
            'paymentHistory'
        ));
    }

    // 🔥 Updated Payment Methods
    public function createPaymentOrder(Request $request)
    {
        $request->validate([
            'installment_id' => 'required|exists:fee_installments,id',
            'amount' => 'required|numeric|min:1'
        ]);

        $studentId = session('user_id');
        $tenantId = session('tenant_id');

        $installment = DB::table('fee_installments')
            ->where('id', $request->installment_id)
            ->where('student_id', $studentId)
            ->where('status', '!=', 'paid')
            ->first();

        if (!$installment) {
            return response()->json(['error' => 'Invalid installment'], 400);
        }

        // Razorpay Order
        // $api = new \Razorpay\Api\Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));
        $api = new \Razorpay\Api\Api('rzp_test_Rv23F2PMj0bMq4', 'HsLe9f9nEcRmLvU0Xo2NxYRa');

        $orderData = [
            'receipt' => 'student_' . $studentId . '_fee_' . $request->installment_id,
            'amount' => $request->amount * 100,
            'currency' => 'INR',
            'notes' => [
                'student_id' => $studentId,
                'tenant_id' => $tenantId,
                'installment_id' => $request->installment_id,
                'category' => $installment->fee_category_name ?? 'Fee'
            ]
        ];

        $razorpayOrder = $api->order->create($orderData);

        return response()->json([
            'order_id' => $razorpayOrder->id,
            'amount' => $request->amount,
            'installment_id' => $request->installment_id,
            // 'key' => env('RAZORPAY_KEY_ID')
            'key' => 'rzp_test_Rv23F2PMj0bMq4'
        ]);
    }



    public function verifyPayment(Request $request)
    {
        // 🔥 Validation
        $request->validate([
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
            'installment_id' => 'required|exists:fee_installments,id',
            'amount' => 'required|numeric|min:1',
            'selected_installments' => 'nullable|json'
        ]);

        $studentId = session('user_id');
        $tenantId = session('tenant_id');

        try {
            // 🔥 Initialize Razorpay API
            $api = new \Razorpay\Api\Api(
                env('RAZORPAY_KEY_ID'),
                env('RAZORPAY_KEY_SECRET')
            );

            // 🔥 Verify Payment Signature (CRITICAL SECURITY)
            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            ];

            // Fetch and verify payment
            $payment = $api->payment->fetch($request->razorpay_payment_id);
            $payment->verify($attributes);

            // 🔥 Double-check payment status
            if (!$payment->captured) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Payment not captured. Please contact support.'
                ], 400);
            }

            // 🔥 Verify amount matches
            if ($payment->amount / 100 != $request->amount) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Payment amount mismatch.'
                ], 400);
            }

            // 🔥 Verify belongs to correct student
            $installment = DB::table('fee_installments')
                ->where('id', $request->installment_id)
                ->where('student_id', $studentId)
                ->where('status', '!=', 'paid')
                ->first();

            if (!$installment) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid installment or already paid.'
                ], 400);
            }

            // 🔥 Start Database Transaction
            DB::beginTransaction();

            try {
                // Generate unique receipt number
                $receiptNumber = 'STU-' . $studentId . '-' .
                    now()->format('Y-m-d') . '-' .
                    strtoupper(substr(uniqid(), -4));

                // 🔥 Create Fee Payment Record
                $paymentData = [
                    'tenant_id' => $tenantId,
                    'student_id' => $studentId,
                    'fee_installment_id' => $request->installment_id, // If column exists
                    'payment_method' => 'online',
                    'transaction_id' => $request->razorpay_payment_id,
                    'gateway_name' => 'Razorpay',
                    'amount_paid' => $request->amount,
                    'paid_at' => now(),
                    'payment_date' => now()->format('Y-m-d'),
                    'receipt_number' => $receiptNumber,
                    'status' => 'completed',
                    'notes' => $request->selected_installments
                        ? $request->selected_installments
                        : json_encode(['category' => $installment->fee_category_name]),
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                // Insert payment record (ignore fee_installment_id if column doesn't exist)
                DB::table('fee_payments')->insert($paymentData);

                // 🔥 Update Installment Status
                $newStatus = 'paid'; // Default to paid

                // Handle multiple installments if selected
                if ($request->selected_installments) {
                    $selectedInstallments = json_decode($request->selected_installments, true);

                    foreach ($selectedInstallments as $installmentData) {
                        $installmentAmount = DB::table('fee_installments')
                            ->where('id', $installmentData['id'])
                            ->where('student_id', $studentId)
                            ->value('amount');

                        // Mark as paid if payment covers full amount
                        if ($request->amount >= $installmentAmount) {
                            DB::table('fee_installments')
                                ->where('id', $installmentData['id'])
                                ->update([
                                    'status' => 'paid',
                                    'updated_at' => now()
                                ]);
                        } else {
                            // Partial payment
                            DB::table('fee_installments')
                                ->where('id', $installmentData['id'])
                                ->update([
                                    'status' => 'partial',
                                    'updated_at' => now()
                                ]);
                        }
                    }
                } else {
                    // Single installment
                    if ($request->amount >= $installment->amount) {
                        DB::table('fee_installments')
                            ->where('id', $request->installment_id)
                            ->update([
                                'status' => 'paid',
                                'updated_at' => now()
                            ]);
                    } else {
                        DB::table('fee_installments')
                            ->where('id', $request->installment_id)
                            ->update([
                                'status' => 'partial',
                                'updated_at' => now()
                            ]);
                    }
                }

                // 🔥 Commit Transaction
                DB::commit();

                // 🔥 Log successful payment (Optional)
                Log::info('Student Fee Payment Success', [
                    'student_id' => $studentId,
                    'payment_id' => $request->razorpay_payment_id,
                    'amount' => $request->amount,
                    'receipt' => $receiptNumber
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Payment completed successfully!',
                    'receipt_number' => $receiptNumber,
                    'amount' => $request->amount,
                    'payment_id' => $request->razorpay_payment_id
                ]);
            } catch (\Exception $dbError) {
                DB::rollBack();

                Log::error('Student Fee Payment DB Error', [
                    'student_id' => $studentId,
                    'error' => $dbError->getMessage()
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Payment recorded but database update failed. Please contact admin.'
                ], 500);
            }
        } catch (\Razorpay\Api\Errors\SignatureVerificationError $e) {
            Log::error('Razorpay Signature Verification Failed', [
                'student_id' => $studentId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Payment signature verification failed.'
            ], 400);
        } catch (\Razorpay\Api\Errors\BadRequestError $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid payment details.'
            ], 400);
        } catch (\Exception $e) {
            Log::error('Razorpay Payment Verification Error', [
                'student_id' => $studentId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Payment verification failed. Please try again.'
            ], 500);
        }
    }

    public function examResults()
    {
        $studentId = session('user_id');

        // 👇 Student Info
        $student = DB::table('students')
            ->leftJoin('student_class_enrollments', 'students.id', '=', 'student_class_enrollments.student_id')
            ->leftJoin('streams', 'student_class_enrollments.stream_id', '=', 'streams.id')
            ->leftJoin('academic_years', 'student_class_enrollments.academic_year_id', '=', 'academic_years.id')
            ->leftJoin('classes', 'student_class_enrollments.class_id', '=', 'classes.id')
            ->leftJoin('sections', 'student_class_enrollments.section_id', '=', 'sections.id')
            ->select(
                'students.*',
                'student_class_enrollments.id as enrollment_id',
                'streams.name as stream_name',
                'academic_years.name as academic_name',
                'classes.name as class_name',
                'sections.name as section_name'
            )
            ->where('students.id', $studentId)
            ->first();

        if (!$student) {
            return redirect('/studentsacademic')->withErrors([
                'username' => 'Student account could not be found.'
            ]);
        }

        $tenantId = $student->tenant_id;

        // 👇 Fetch all exam results grouped by exam type
        $examResults = DB::table('exam_marks as em')
            ->join('exams as e', 'em.exam_id', '=', 'e.id')
            ->leftJoin('exam_types as et', 'e.exam_type_id', '=', 'et.id')
            ->leftJoin('subjects as s', 'e.subject_id', '=', 's.id')
            ->leftJoin('terms as t', 'e.term_id', '=', 't.id')
            ->leftJoin('classes as c', 'e.class_id', '=', 'c.id')
            ->leftJoin('academic_years as ay', 'e.academic_year_id', '=', 'ay.id')
            ->where('em.student_id', $studentId)
            ->where('e.tenant_id', $tenantId)
            ->select(
                'em.id as mark_id',
                'em.marks_obtained',
                'em.grade_letter',
                'em.remarks',
                'e.id as exam_id',
                'e.name as exam_name',
                'e.total_marks',
                'e.pass_marks',
                'e.exam_date',
                'et.id as exam_type_id',
                'et.name as exam_type_name',
                's.id as subject_id',
                's.name as subject_name',
                's.code as subject_code',
                't.id as term_id',
                't.name as term_name',
                'c.id as class_id',
                'c.name as class_name',
                'ay.id as academic_year_id',
                'ay.name as academic_year_name'
            )
            ->orderBy('et.id', 'desc')
            ->orderBy('e.exam_date', 'desc')
            ->get();

        // 👇 Group results by exam type
        $groupedByExamType = $examResults->groupBy('exam_type_id');

        // 👇 Calculate overall statistics
        $totalExams = $examResults->count();
        $totalMarksObtained = $examResults->sum('marks_obtained');
        $totalMaxMarks = $examResults->sum('total_marks');
        $overallPercentage = $totalMaxMarks > 0 ? round(($totalMarksObtained / $totalMaxMarks) * 100, 2) : 0;

        // 👇 Count passed/failed exams
        $passedExams = $examResults->filter(function($result) {
            return $result->marks_obtained >= $result->pass_marks;
        })->count();

        $failedExams = $totalExams - $passedExams;

        return view('studentacademic.examresults', compact(
            'student',
            'examResults',
            'groupedByExamType',
            'totalExams',
            'totalMarksObtained',
            'totalMaxMarks',
            'overallPercentage',
            'passedExams',
            'failedExams'
        ));
    }

    public function attendance()
    {
        $studentId = session('user_id');

        // 👇 Student Info
        $student = DB::table('students')
            ->leftJoin('student_class_enrollments', 'students.id', '=', 'student_class_enrollments.student_id')
            ->leftJoin('streams', 'student_class_enrollments.stream_id', '=', 'streams.id')
            ->leftJoin('academic_years', 'student_class_enrollments.academic_year_id', '=', 'academic_years.id')
            ->leftJoin('classes', 'student_class_enrollments.class_id', '=', 'classes.id')
            ->select(
                'students.*',
                'streams.name as stream_name',
                'academic_years.name as academic_name',
                'classes.name as class_name'
            )
            ->where('students.id', $studentId)
            ->first();

        // 👇 Category-wise Fees


        return view('studentacademic.attendancedetails', compact(
            'student'
        ));
    }

    public function profile()
    {
        $studentId = session('user_id');

        // 👇 Student Info with full details
        $student = DB::table('students')
            ->leftJoin('student_class_enrollments', 'students.id', '=', 'student_class_enrollments.student_id')
            ->leftJoin('streams', 'student_class_enrollments.stream_id', '=', 'streams.id')
            ->leftJoin('academic_years', 'student_class_enrollments.academic_year_id', '=', 'academic_years.id')
            ->leftJoin('classes', 'student_class_enrollments.class_id', '=', 'classes.id')
            ->leftJoin('sections', 'student_class_enrollments.section_id', '=', 'sections.id')
            ->select(
                'students.*',
                'streams.name as stream_name',
                'academic_years.name as academic_name',
                'classes.name as class_name',
                'sections.name as section_name',
                // 'student_class_enrollments.admission_number',
                'student_class_enrollments.status as enrollment_status'
            )
            ->where('students.id', $studentId)
            ->first();

        if (!$student) {
            return redirect('/studentsacademic')->withErrors([
                'username' => 'Student account could not be found.'
            ]);
        }

        return view('studentacademic.profiledetails', compact('student'));
    }

    public function attendancePortal()
    {
        $studentId = session('user_id');
        $today = Carbon::today();

        $student = DB::table('students')
            ->leftJoin('student_class_enrollments', 'students.id', '=', 'student_class_enrollments.student_id')
            ->leftJoin('streams', 'student_class_enrollments.stream_id', '=', 'streams.id')
            ->leftJoin('academic_years', 'student_class_enrollments.academic_year_id', '=', 'academic_years.id')
            ->leftJoin('classes', 'student_class_enrollments.class_id', '=', 'classes.id')
            ->leftJoin('sections', 'student_class_enrollments.section_id', '=', 'sections.id')
            ->select(
                'students.*',
                'student_class_enrollments.id as enrollment_id',
                'streams.name as stream_name',
                'academic_years.name as academic_name',
                'classes.name as class_name',
                'sections.name as section_name'
            )
            ->where('students.id', $studentId)
            ->first();

        if (!$student) {
            return redirect('/studentsacademic')->withErrors([
                'username' => 'Student account could not be found.'
            ]);
        }

        $tenantId = $student->tenant_id ?? null;

        // 👇 Get ALL enrollments (current and past) for class-wise attendance
        $allEnrollments = StudentClassEnrollment::where('tenant_id', $tenantId)
            ->where('student_id', $studentId)
            ->with(['class', 'section', 'stream', 'academicYear'])
            ->orderBy('academic_year_id', 'desc')
            ->get();

        // 👇 Current active enrollment
        $currentEnrollment = $allEnrollments->where('status', 'active')->first();
        $attendanceEnrollmentId = $currentEnrollment?->id;

        // 👇 Overall attendance across ALL enrollments
        $overallAttendanceQuery = StudentAttendance::query()
            ->when($tenantId, fn ($query) => $query->where('tenant_id', $tenantId))
            ->whereIn('enrollment_id', $allEnrollments->pluck('id'));

        $totalClasses = (clone $overallAttendanceQuery)->count();
        $presentClasses = (clone $overallAttendanceQuery)->whereIn('status', ['present', 'late'])->count();
        $absentClasses = (clone $overallAttendanceQuery)->where('status', 'absent')->count();
        $lateClasses = (clone $overallAttendanceQuery)->where('status', 'late')->count();
        $halfDayClasses = (clone $overallAttendanceQuery)->where('status', 'half_day')->count();

        $attendancePercentage = $totalClasses > 0
            ? round(($presentClasses / $totalClasses) * 100)
            : 0;

        // 👇 Monthly attendance for current enrollment
        $thisMonthQuery = StudentAttendance::query()
            ->when($attendanceEnrollmentId, fn ($query) => $query->where('enrollment_id', $attendanceEnrollmentId))
            ->when(!$attendanceEnrollmentId, fn ($query) => $query->whereRaw('1 = 0'))
            ->when($tenantId, fn ($query) => $query->where('tenant_id', $tenantId))
            ->whereMonth('date', $today->month)
            ->whereYear('date', $today->year);

        $thisMonthTotal = (clone $thisMonthQuery)->count();
        $thisMonthPresent = (clone $thisMonthQuery)->whereIn('status', ['present', 'late'])->count();
        $monthlyAttendancePercentage = $thisMonthTotal > 0
            ? round(($thisMonthPresent / $thisMonthTotal) * 100)
            : null;

        // 👇 Recent attendance (from all enrollments)
        $recentAttendance = StudentAttendance::query()
            ->with(['period'])
            ->when($tenantId, fn ($query) => $query->where('tenant_id', $tenantId))
            ->whereIn('enrollment_id', $allEnrollments->pluck('id'))
            ->orderByDesc('date')
            ->orderByDesc('period_id')
            ->limit(12)
            ->get();

        // 👇 Get timetables for current enrollment (for subject/term mapping)
        $timetables = collect();
        if ($currentEnrollment) {
            $timetables = Timetable::where('tenant_id', $tenantId)
                ->where('class_id', $currentEnrollment->class_id)
                ->where('section_id', $currentEnrollment->section_id)
                ->where('stream_id', $currentEnrollment->stream_id)
                ->where('academic_year_id', $currentEnrollment->academic_year_id)
                ->with(['slots.subject', 'slots.period'])
                ->get();
        }

        $attendanceRecordsForMapping = StudentAttendance::query()
            ->with(['period'])
            ->when($attendanceEnrollmentId, fn ($query) => $query->where('enrollment_id', $attendanceEnrollmentId))
            ->when(!$attendanceEnrollmentId, fn ($query) => $query->whereRaw('1 = 0'))
            ->when($tenantId, fn ($query) => $query->where('tenant_id', $tenantId))
            ->orderBy('date')
            ->get();

        $subjectAttendanceMap = [];
        $termAttendanceMap = [];

        foreach ($attendanceRecordsForMapping as $record) {
            $recordDate = $record->date ? Carbon::parse($record->date) : null;
            if (!$recordDate) {
                continue;
            }

            $dayOfWeek = $recordDate->dayOfWeek;

            $matchedTimetable = $timetables->first(function ($timetable) use ($recordDate) {
                if (!$timetable->term || !$timetable->term->start_date || !$timetable->term->end_date) {
                    return true;
                }

                return $recordDate->between(
                    Carbon::parse($timetable->term->start_date),
                    Carbon::parse($timetable->term->end_date)
                );
            });

            if (!$matchedTimetable) {
                continue;
            }

            $matchedSlot = $matchedTimetable->slots->first(function ($slot) use ($record, $dayOfWeek) {
                return (int) $slot->period_id === (int) $record->period_id
                    && (int) $slot->day_of_week === (int) $dayOfWeek;
            });

            $termKey = (string) ($matchedTimetable->term_id ?? 'general');
            $termName = $matchedTimetable->term->name ?? 'General';

            if (!isset($termAttendanceMap[$termKey])) {
                $termAttendanceMap[$termKey] = [
                    'term_name' => $termName,
                    'total' => 0,
                    'present' => 0,
                    'absent' => 0,
                    'late' => 0,
                    'half_day' => 0,
                ];
            }

            $termAttendanceMap[$termKey]['total']++;
            if (isset($termAttendanceMap[$termKey][$record->status])) {
                $termAttendanceMap[$termKey][$record->status]++;
            }
            if (in_array($record->status, ['present', 'late'], true)) {
                $termAttendanceMap[$termKey]['present']++;
            }

            if (!$matchedSlot || !$matchedSlot->subject) {
                continue;
            }

            $subjectKey = (string) $matchedSlot->subject_id;
            if (!isset($subjectAttendanceMap[$subjectKey])) {
                $subjectAttendanceMap[$subjectKey] = [
                    'subject_name' => $matchedSlot->subject->name ?? 'Subject',
                    'subject_code' => $matchedSlot->subject->code ?? 'N/A',
                    'term_name' => $termName,
                    'total' => 0,
                    'present' => 0,
                    'absent' => 0,
                    'late' => 0,
                    'half_day' => 0,
                ];
            }

            $subjectAttendanceMap[$subjectKey]['total']++;
            if (isset($subjectAttendanceMap[$subjectKey][$record->status])) {
                $subjectAttendanceMap[$subjectKey][$record->status]++;
            }
            if (in_array($record->status, ['present', 'late'], true)) {
                $subjectAttendanceMap[$subjectKey]['present']++;
            }
        }

        $semesterAttendance = collect($termAttendanceMap)
            ->map(function ($term) {
                $term['percentage'] = $term['total'] > 0
                    ? round(($term['present'] / $term['total']) * 100, 1)
                    : 0;
                return (object) $term;
            })
            ->sortBy('term_name')
            ->values();

        $subjectAttendance = collect($subjectAttendanceMap)
            ->map(function ($subject) {
                $subject['percentage'] = $subject['total'] > 0
                    ? round(($subject['present'] / $subject['total']) * 100, 1)
                    : 0;
                return (object) $subject;
            })
            ->sortBy('subject_name')
            ->values();

        return view('studentacademic.attendancedetails', compact(
            'student',
            'totalClasses',
            'presentClasses',
            'absentClasses',
            'lateClasses',
            'halfDayClasses',
            'attendancePercentage',
            'monthlyAttendancePercentage',
            'recentAttendance',
            'semesterAttendance',
            'subjectAttendance'
        ));
    }
}
