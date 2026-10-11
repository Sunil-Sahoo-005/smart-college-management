@extends('teacher_layout')
@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    *, *::before, *::after { box-sizing: border-box; }
    body, .nxl-content {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background: radial-gradient(circle at top right, rgba(190,217,193,0.46), transparent 28%),
                    linear-gradient(180deg, #f5efe3 0%, #eef3ea 100%);
        color: #18352d;
    }
    .sl-shell { padding: 26px; display: flex; flex-direction: column; gap: 22px; }

    /* Hero */
    .sl-hero {
        position: relative; overflow: hidden; border-radius: 28px; padding: 28px;
        background: radial-gradient(circle at top left, rgba(255,255,255,0.14), transparent 24%),
                    linear-gradient(135deg, #12342b 0%, #1d5548 58%, #c68b3c 160%);
        color: #f7f3ea; box-shadow: 0 20px 55px rgba(19,52,43,0.18);
    }
    .sl-hero::after {
        content:''; position:absolute; inset:auto -70px -90px auto;
        width:240px; height:240px; border-radius:50%; background:rgba(255,255,255,0.07);
    }
    .sl-hero-inner { position:relative; z-index:1; }
    .eyebrow {
        display:inline-flex; align-items:center; gap:8px; padding:7px 12px; border-radius:999px;
        background:rgba(255,255,255,0.12); color:#f6e7ce; font-size:12px; font-weight:700;
        text-transform:uppercase; letter-spacing:.08em; margin-bottom:14px;
    }
    .sl-title { font-size:clamp(22px,3.5vw,36px); font-weight:800; letter-spacing:-.04em; margin:0 0 8px; }
    .sl-copy  { color:rgba(247,243,234,.82); font-size:14px; line-height:1.7; margin:0; max-width:680px; }

    /* Cards grid */
    .payslip-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:18px; }

    .payslip-card {
        border-radius:22px; padding:22px;
        background:rgba(255,255,255,.92); border:1px solid rgba(24,53,45,.08);
        box-shadow:0 10px 30px rgba(28,49,43,.07);
        transition:.22s;
    }
    .payslip-card:hover { transform:translateY(-3px); box-shadow:0 18px 45px rgba(28,49,43,.12); }

    .pc-top { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom:18px; }
    .pc-month {
        font-size:18px; font-weight:800; letter-spacing:-.03em; color:#12342b;
    }
    .pc-year { font-size:13px; color:#687872; font-weight:500; margin-top:2px; }

    .published-badge {
        display:inline-flex; align-items:center; gap:5px; padding:6px 12px;
        border-radius:999px; background:#dcfce7; color:#166534; font-size:11px; font-weight:700;
    }

    .pc-divider { border:none; border-top:1px solid rgba(24,53,45,.07); margin:16px 0; }

    .pc-amounts { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px; }
    .pc-amount-item { }
    .pc-amount-label { font-size:11px; text-transform:uppercase; letter-spacing:.07em; color:#8a9b94; font-weight:700; margin-bottom:4px; }
    .pc-amount-value { font-size:18px; font-weight:800; color:#18352d; letter-spacing:-.02em; }
    .pc-amount-value.net { color:#12342b; font-size:22px; }
    .pc-amount-value.deduct { color:#991b1b; }

    .view-btn {
        display:flex; align-items:center; justify-content:center; gap:8px; padding:12px;
        border-radius:14px; background:linear-gradient(135deg,#12342b,#1d5548);
        color:#f7f3ea; font-family:inherit; font-size:13px; font-weight:700;
        text-decoration:none; width:100%; transition:.2s;
    }
    .view-btn:hover { opacity:.88; color:#f7f3ea; }

    /* Empty */
    .panel-card { border-radius:26px; padding:22px; background:rgba(255,255,255,.88); border:1px solid rgba(24,53,45,.08); box-shadow:0 16px 45px rgba(28,49,43,.08); }
    .empty-state { text-align:center; padding:56px 24px; color:#687872; }
    .empty-state i { font-size:40px; display:block; margin-bottom:16px; color:#9caea7; }
    .empty-title { font-size:18px; font-weight:800; color:#18352d; margin-bottom:6px; }

    @media(max-width:768px){ .sl-shell{padding:16px;} }
</style>

<div class="nxl-content">
<div class="sl-shell">

    {{-- Hero --}}
    <section class="sl-hero">
        <div class="sl-hero-inner">
            <span class="eyebrow"><i class="feather-dollar-sign"></i> Salary</span>
            <h1 class="sl-title">My Payslips</h1>
            <p class="sl-copy">View all your published monthly payslips. Click on any month to see the full earnings and deductions breakdown.</p>
        </div>
    </section>

    {{-- Payslips --}}
    @if($payslips->isNotEmpty())
        <div class="payslip-grid">
            @foreach($payslips as $payslip)
                @php
                    $run = $payslip->payrollRun;
                    $monthName = $run ? \Carbon\Carbon::createFromDate($run->year, $run->month, 1)->format('F') : '—';
                    $year = $run?->year ?? '—';
                @endphp
                <div class="payslip-card">
                    <div class="pc-top">
                        <div>
                            <div class="pc-month">{{ $monthName }}</div>
                            <div class="pc-year">{{ $year }}</div>
                        </div>
                        <span class="published-badge"><i class="feather-check-circle"></i> Published</span>
                    </div>

                    <hr class="pc-divider">

                    <div class="pc-amounts">
                        <div class="pc-amount-item">
                            <div class="pc-amount-label">Gross Salary</div>
                            <div class="pc-amount-value">₹{{ number_format($payslip->gross_salary, 2) }}</div>
                        </div>
                        <div class="pc-amount-item">
                            <div class="pc-amount-label">Deductions</div>
                            <div class="pc-amount-value deduct">₹{{ number_format($payslip->total_deductions, 2) }}</div>
                        </div>
                    </div>

                    <div style="background:#edf5ef;border-radius:14px;padding:14px;margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;">
                        <div>
                            <div class="pc-amount-label">Net Salary</div>
                            <div class="pc-amount-value net">₹{{ number_format($payslip->net_salary, 2) }}</div>
                        </div>
                        <i class="feather-trending-up" style="font-size:28px;color:#12342b;opacity:.5;"></i>
                    </div>

                    <div style="display:flex;gap:10px;font-size:12px;color:#687872;margin-bottom:16px;flex-wrap:wrap;">
                        <span><i class="feather-calendar" style="font-size:11px;"></i> {{ $payslip->working_days ?? '—' }} working days</span>
                        <span><i class="feather-check" style="font-size:11px;"></i> {{ $payslip->present_days ?? '—' }} present</span>
                        @if(($payslip->absent_days ?? 0) > 0)
                            <span style="color:#991b1b;"><i class="feather-x" style="font-size:11px;"></i> {{ $payslip->absent_days }} absent</span>
                        @endif
                    </div>

                    <a href="{{ route('teacher.my_salary.show', $payslip) }}" class="view-btn">
                        <i class="feather-eye"></i> View Full Payslip
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div class="panel-card">
            <div class="empty-state">
                <i class="feather-dollar-sign"></i>
                <div class="empty-title">No Payslips Available</div>
                <div>Your published payslips will appear here once the HR team processes payroll for your account.</div>
            </div>
        </div>
    @endif

</div>
</div>
@endsection
