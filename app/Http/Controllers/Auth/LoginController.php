<?php
// app/Http/Controllers/Auth/LoginController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OtpVerification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Staff;
use App\Models\FeePayment;
use App\Models\FeeInstallment;
use App\Models\StudentClassEnrollment;
use App\Models\StudentAttendance;
use App\Models\Leave;
use App\Models\NoticeBoard;
use App\Models\AcademicYear;
use App\Models\Section;
use Carbon\Carbon;

class LoginController extends Controller
{
    // ─── Step 1: Email/Password check → OTP bhejo ────────────────────────
    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'Please enter your password.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('email', 'password');

        // Sirf validate karo, login mat karo abhi
        if (!Auth::validate($credentials)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        // OTP generate karo
        $otp = '000000';
        // $otp = rand(100000, 999999);

        // Purane OTP delete karo
        OtpVerification::where('email', $request->email)->delete();

        // Naya OTP save karo — 5 minute valid
        OtpVerification::create([
            'email'      => $request->email,
            'otp'        => $otp,
            'expires_at' => Carbon::now()->addMinutes(5),
            'used'       => false,
        ]);

        // Mail bhejo
        // Mail::send('emails.otp', ['otp' => $otp], function ($mail) use ($request) {
        //     $mail->to($request->email)
        //          ->subject('Your Login OTP - ' . config('app.name'));
        // });

        // Email + remember session mein store karo
        session([
            'otp_email'    => $request->email,
            'otp_remember' => $request->has('remember'),
        ]);

        return redirect()->route('otp.verify.form');
    }

    // ─── Step 2: OTP form dikhao ─────────────────────────────────────────
    public function showOtpForm()
    {
        if (!session('otp_email')) {
            return redirect()->route('login');
        }
        return view('auth.otp');
    }

    // ─── Step 3: OTP verify → login karo ─────────────────────────────────
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $email = session('otp_email');

        if (!$email) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Session expired. Please login again.']);
        }

        $otpRecord = OtpVerification::where('email', $email)
            ->where('otp', $request->otp)
            ->where('used', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$otpRecord) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP. Please try again.']);
        }

        // OTP use hua mark karo
        $otpRecord->update(['used' => true]);

        // Ab login karo
        $remember = session('otp_remember', false);
        $user     = \App\Models\User::where('email', $email)->first();
        Auth::login($user, $remember);

        session()->forget(['otp_email', 'otp_remember']);
        $request->session()->regenerate();

        // Role ke hisaab se redirect karo
        $roleRoutes = [
            'super_admin' => 'super_admin.dashboard',
            'admin'       => 'admin.dashboard',
            'student'     => 'student.dashboard',
            'teacher'     => 'teacher.dashboard',
            'hr'          => 'hr.dashboard',
            'Accountant'  => 'accountant.dashboard',
            'librarian'   => 'library.dashboard',
            'hostel'      => 'hostel.dashboard',
            'travels'     => 'travels.dashboard',
            'academic'    => 'academic.dashboard',
        ];

        foreach ($roleRoutes as $role => $route) {
            if ($user->role === $role) {
                return redirect()->intended(route($route));
            }
        }

        return redirect()->intended(route('login'));
    }

    // ─── OTP Resend ───────────────────────────────────────────────────────
    public function resendOtp()
    {
        $email = session('otp_email');

        if (!$email) {
            return redirect()->route('login');
        }

        $otp = '000000';
        // $otp = rand(100000, 999999);

        OtpVerification::where('email', $email)->delete();

        OtpVerification::create([
            'email'      => $email,
            'otp'        => $otp,
            'expires_at' => Carbon::now()->addMinutes(5),
            'used'       => false,
        ]);

        // Mail::send('emails.otp', ['otp' => $otp], function ($mail) use ($email) {
        //     $mail->to($email)
        //          ->subject('Your Login OTP - ' . config('app.name'));
        // });

        return back()->with('success', 'OTP resent successfully! Check your email.');
    }

    // ─── Logout ───────────────────────────────────────────────────────────
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    // ─── Dashboard ────────────────────────────────────────────────────────
    public function dashboard()
    {
        $tenantId = Auth::user()->tenant_id;
        $today    = Carbon::today();

        $currentYear = AcademicYear::where('tenant_id', $tenantId)
                           ->where('is_current', true)
                           ->first();

        $totalStudents = Student::where('tenant_id', $tenantId)
                             ->where('status', 'active')->count();

        $newStudentsThisMonth = Student::where('tenant_id', $tenantId)
                             ->where('status', 'active')
                             ->whereMonth('created_at', $today->month)
                             ->whereYear('created_at',  $today->year)->count();

        $totalStaff = Staff::where('tenant_id', $tenantId)
                         ->where('status', 'active')->count();

        $newStaffThisMonth = Staff::where('tenant_id', $tenantId)
                         ->where('status', 'active')
                         ->whereMonth('joining_date', $today->month)
                         ->whereYear('joining_date',  $today->year)->count();

        $feeCollected = FeePayment::where('tenant_id', $tenantId)
                            ->whereYear('payment_date', $today->year)
                            ->sum('amount_paid');

        $pendingDues = FeeInstallment::where('tenant_id', $tenantId)
                           ->whereIn('status', ['unpaid', 'partial'])
                           ->sum('amount');

        $studentsWithDues = FeeInstallment::where('tenant_id', $tenantId)
                                ->whereIn('status', ['unpaid', 'partial'])
                                ->distinct('student_id')->count('student_id');

        $recentPayments = FeePayment::with(['student'])
                              ->where('tenant_id', $tenantId)
                              ->latest('paid_at')->limit(5)->get()
                              ->filter(fn($p) => $p->student !== null)
                              ->map(fn($p) => [
                                  'name'     => $p->student->first_name . ' ' . $p->student->last_name,
                                  'initials' => strtoupper(substr($p->student->first_name, 0, 1) . substr($p->student->last_name, 0, 1)),
                                  'amount'   => '₹' . number_format($p->amount_paid, 0),
                                  'time'     => $p->paid_at ? $p->paid_at->diffForHumans() : '—',
                              ]);

        $sections = Section::where('tenant_id', $tenantId)->with('class')->limit(6)->get();

        $attendanceBySection = $sections->map(function ($section) use ($tenantId, $today, $currentYear) {
            $enrollments = StudentClassEnrollment::where('tenant_id', $tenantId)
                               ->where('section_id', $section->id)
                               ->where('academic_year_id', optional($currentYear)->id)
                               ->pluck('id');

            $total   = $enrollments->count();
            $present = StudentAttendance::where('tenant_id', $tenantId)
                           ->whereIn('enrollment_id', $enrollments)
                           ->where('date', $today)
                           ->whereIn('status', ['present', 'late'])->count();

            $pct = $total > 0 ? round(($present / $total) * 100) : 0;

            return [
                'label' => optional($section->class)->name . ' ' . $section->name,
                'pct'   => $pct,
                'color' => $pct >= 90 ? '#3B6D11' : ($pct >= 75 ? '#854F0B' : '#A32D2D'),
            ];
        });

        $notices = NoticeBoard::where('tenant_id', $tenantId)
                       ->latest('notice_date')->limit(4)->get()
                       ->map(fn($n) => [
                           'title' => $n->title,
                           'date'  => Carbon::parse($n->notice_date)->diffForHumans(),
                       ]);

        return view('dashboard', compact(
            'totalStudents', 'newStudentsThisMonth',
            'totalStaff', 'newStaffThisMonth',
            'feeCollected', 'pendingDues', 'studentsWithDues',
            'recentPayments', 'attendanceBySection',
            'notices', 'currentYear'
        ));
    }
}
