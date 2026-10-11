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
    .ss-shell { padding: 26px; display: flex; flex-direction: column; gap: 20px; max-width: 920px; margin: 0 auto; }

    /* Header */
    .ss-header { display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; }
    .back-btn {
        display:inline-flex; align-items:center; gap:6px; padding:10px 16px; border-radius:12px;
        background:rgba(255,255,255,.88); border:1px solid rgba(24,53,45,.1);
        color:#18352d; font-family:inherit; font-size:13px; font-weight:600; text-decoration:none; transition:.2s;
    }
    .back-btn:hover { background:#12342b; color:#f7f3ea; }
    .print-btn {
        display:inline-flex; align-items:center; gap:6px; padding:10px 18px; border-radius:12px;
        background:#12342b; color:#f7f3ea; font-family:inherit; font-size:13px; font-weight:700;
        border:none; cursor:pointer; text-decoration:none; transition:.2s;
    }
    .print-btn:hover { background:#1d5548; }

    /* Payslip hero */
    .slip-hero {
        border-radius:28px; padding:28px;
        background: radial-gradient(circle at top left, rgba(255,255,255,0.14), transparent 24%),
                    linear-gradient(135deg, #12342b 0%, #1d5548 58%, #c68b3c 160%);
        color:#f7f3ea; box-shadow:0 20px 55px rgba(19,52,43,0.18);
        position:relative; overflow:hidden;
    }
    .slip-hero::after {
        content:''; position:absolute; inset:auto -70px -90px auto;
        width:220px; height:220px; border-radius:50%; background:rgba(255,255,255,0.07);
    }
    .slip-hero-inner { position:relative; z-index:1; display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:20px; }
    .slip-school { font-size:11px; text-transform:uppercase; letter-spacing:.08em; color:rgba(247,243,234,.7); margin-bottom:8px; }
    .slip-period { font-size:clamp(24px,4vw,38px); font-weight:800; letter-spacing:-.04em; margin:0 0 6px; }
    .slip-name   { font-size:15px; font-weight:600; color:rgba(247,243,234,.9); }
    .slip-meta   { font-size:13px; color:rgba(247,243,234,.7); margin-top:3px; }

    .net-box {
        background:rgba(255,255,255,.13); border:1px solid rgba(255,255,255,.18);
        border-radius:20px; padding:20px 24px; text-align:right; min-width:200px;
    }
    .net-label { font-size:11px; text-transform:uppercase; letter-spacing:.08em; color:rgba(247,243,234,.7); margin-bottom:6px; }
    .net-amount { font-size:36px; font-weight:800; letter-spacing:-.04em; color:#fffaf3; }

    /* Panel */
    .panel { border-radius:24px; padding:22px; background:rgba(255,255,255,.9); border:1px solid rgba(24,53,45,.08); box-shadow:0 12px 40px rgba(28,49,43,.07); }
    .panel-kicker { font-size:11px; text-transform:uppercase; letter-spacing:.08em; color:#8a7c64; font-weight:700; margin-bottom:6px; }
    .panel-title  { font-size:18px; font-weight:800; letter-spacing:-.03em; color:#16342d; margin:0 0 16px; }

    /* Attendance row */
    .att-stats { display:grid; grid-template-columns:repeat(5,minmax(0,1fr)); gap:12px; }
    .att-item { border-radius:14px; padding:14px; text-align:center; }
    .att-item.work  { background:#edf5ef; }
    .att-item.pres  { background:#dcfce7; }
    .att-item.abs   { background:#fee2e2; }
    .att-item.leave { background:#dbeafe; }
    .att-item.half  { background:#fef9c3; }
    .att-num { font-size:26px; font-weight:800; letter-spacing:-.04em; color:#12342b; }
    .att-num.red  { color:#991b1b; }
    .att-num.blue { color:#1e40af; }
    .att-num.gold { color:#854d0e; }
    .att-lbl { font-size:11px; text-transform:uppercase; letter-spacing:.07em; color:#687872; font-weight:700; margin-top:4px; }

    /* Breakdown tables */
    .bk-table { width:100%; border-collapse:collapse; }
    .bk-table th {
        font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.07em;
        color:#687872; padding:10px 14px; border-bottom:1px solid rgba(24,53,45,.08); text-align:left;
    }
    .bk-table th.right, .bk-table td.right { text-align:right; }
    .bk-table td { padding:11px 14px; border-bottom:1px solid rgba(24,53,45,.05); font-size:13px; color:#2a3f38; }
    .bk-table tr:last-child td { border-bottom:none; }
    .bk-table .total-row td { font-weight:800; font-size:14px; background:#f5f9f5; border-top:2px solid rgba(24,53,45,.1); }
    .bk-table .total-row td.green { color:#166534; }
    .bk-table .total-row td.red   { color:#991b1b; }

    /* Net summary bottom */
    .net-summary {
        border-radius:22px; padding:22px;
        background:linear-gradient(135deg,#12342b,#1d5548);
        color:#f7f3ea;
        display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:16px;
    }
    .ns-item { text-align:center; }
    .ns-label { font-size:11px; text-transform:uppercase; letter-spacing:.08em; color:rgba(247,243,234,.7); font-weight:700; margin-bottom:8px; }
    .ns-value { font-size:22px; font-weight:800; color:#fffaf3; letter-spacing:-.03em; }
    .ns-value.highlight { font-size:28px; color:#f6e7ce; }

    /* Two-col layout */
    .two-col { display:grid; grid-template-columns:1fr 1fr; gap:18px; }

    @media print {
        body { background:#fff !important; }
        .ss-header, .nxl-sidebar, .nxl-header, .teacher-shell { display:none !important; }
        .ss-shell { max-width:100%; padding:0; }
        .slip-hero { border-radius:12px; }
        .panel { box-shadow:none; border:1px solid #e5e7eb; }
    }
    @media(max-width:900px){
        .att-stats { grid-template-columns:repeat(3,1fr); }
        .two-col { grid-template-columns:1fr; }
    }
    @media(max-width:768px){ .ss-shell{padding:16px;} .slip-hero-inner{flex-direction:column;} .net-box{text-align:left;min-width:unset;width:100%;} }
    @media(max-width:560px){ .att-stats{grid-template-columns:repeat(2,1fr);} .net-summary{grid-template-columns:1fr;} }
</style>

<div class="nxl-content">
<div class="ss-shell">

    @php
        $run = $payslip->payrollRun;
        $staff = $payslip->staff;
        $monthName = $run ? \Carbon\Carbon::createFromDate($run->year, $run->month, 1)->format('F Y') : '—';
        $earnings  = $payslip->earnings_breakdown ?? [];
        $deductions = $payslip->deductions_breakdown ?? [];
    @endphp

    {{-- Header --}}
    <div class="ss-header">
        <a href="{{ route('teacher.my_salary.index') }}" class="back-btn">
            <i class="feather-arrow-left"></i> All Payslips
        </a>
        <button onclick="window.print()" class="print-btn">
            <i class="feather-printer"></i> Print Payslip
        </button>
    </div>

    {{-- Hero --}}
    <section class="slip-hero">
        <div class="slip-hero-inner">
            <div>
                <div class="slip-school">Official Payslip</div>
                <h1 class="slip-period">{{ $monthName }}</h1>
                <div class="slip-name">
                    {{ optional($staff)->first_name }} {{ optional($staff)->last_name }}
                </div>
                <div class="slip-meta">
                    {{ optional(optional($staff)->designation)->name ?? 'Staff' }}
                    @if(optional(optional($staff)->department)->name)
                        &nbsp;·&nbsp; {{ optional($staff)->department->name }}
                    @endif
                </div>
                @if($staff?->employee_id)
                    <div class="slip-meta" style="margin-top:4px;">Employee ID: {{ $staff->employee_id }}</div>
                @endif
            </div>
            <div class="net-box">
                <div class="net-label">Net Salary</div>
                <div class="net-amount">₹{{ number_format($payslip->net_salary, 2) }}</div>
                @if($payslip->published_at)
                    <div style="font-size:11px;color:rgba(247,243,234,.6);margin-top:8px;">
                        Published {{ \Carbon\Carbon::parse($payslip->published_at)->format('d M Y') }}
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Attendance Summary --}}
    <div class="panel">
        <div class="panel-kicker">Attendance</div>
        <h2 class="panel-title">Monthly Attendance Summary</h2>
        <div class="att-stats">
            <div class="att-item work">
                <div class="att-num">{{ $payslip->working_days ?? '—' }}</div>
                <div class="att-lbl">Working Days</div>
            </div>
            <div class="att-item pres">
                <div class="att-num">{{ $payslip->present_days ?? '—' }}</div>
                <div class="att-lbl">Present</div>
            </div>
            <div class="att-item abs">
                <div class="att-num red">{{ $payslip->absent_days ?? '—' }}</div>
                <div class="att-lbl">Absent</div>
            </div>
            <div class="att-item leave">
                <div class="att-num blue">{{ $payslip->leave_days ?? '—' }}</div>
                <div class="att-lbl">Leave</div>
            </div>
            <div class="att-item half">
                <div class="att-num gold">{{ $payslip->half_days ?? '—' }}</div>
                <div class="att-lbl">Half Day</div>
            </div>
        </div>
    </div>

    {{-- Earnings & Deductions --}}
    <div class="two-col">
        {{-- Earnings --}}
        <div class="panel">
            <div class="panel-kicker">Income</div>
            <h2 class="panel-title">Earnings Breakdown</h2>
            <table class="bk-table">
                <thead>
                    <tr>
                        <th>Component</th>
                        <th class="right">Amount (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($earnings as $item)
                        <tr>
                            <td>{{ $item['name'] ?? $item['component'] ?? '—' }}</td>
                            <td class="right">{{ number_format($item['amount'] ?? 0, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" style="text-align:center;color:#9caea7;padding:20px;">No earnings data</td>
                        </tr>
                    @endforelse
                    @if(($payslip->bonus_amount ?? 0) > 0)
                        <tr>
                            <td>Bonus</td>
                            <td class="right">{{ number_format($payslip->bonus_amount, 2) }}</td>
                        </tr>
                    @endif
                    @if(($payslip->arrear_amount ?? 0) > 0)
                        <tr>
                            <td>Arrears</td>
                            <td class="right">{{ number_format($payslip->arrear_amount, 2) }}</td>
                        </tr>
                    @endif
                    @if(($payslip->overtime_amount ?? 0) > 0)
                        <tr>
                            <td>Overtime</td>
                            <td class="right">{{ number_format($payslip->overtime_amount, 2) }}</td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td>Total Earnings</td>
                        <td class="right green">₹ {{ number_format($payslip->total_earnings, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Deductions --}}
        <div class="panel">
            <div class="panel-kicker">Deductions</div>
            <h2 class="panel-title">Deductions Breakdown</h2>
            <table class="bk-table">
                <thead>
                    <tr>
                        <th>Component</th>
                        <th class="right">Amount (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deductions as $item)
                        <tr>
                            <td>{{ $item['name'] ?? $item['component'] ?? '—' }}</td>
                            <td class="right">{{ number_format($item['amount'] ?? 0, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" style="text-align:center;color:#9caea7;padding:20px;">No deductions</td>
                        </tr>
                    @endforelse
                    @if(($payslip->lop_amount ?? 0) > 0)
                        <tr>
                            <td>Loss of Pay (LOP)</td>
                            <td class="right" style="color:#991b1b;">{{ number_format($payslip->lop_amount, 2) }}</td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td>Total Deductions</td>
                        <td class="right red">₹ {{ number_format($payslip->total_deductions, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Net Summary --}}
    <div class="net-summary">
        <div class="ns-item">
            <div class="ns-label">Gross Salary</div>
            <div class="ns-value">₹ {{ number_format($payslip->gross_salary, 2) }}</div>
        </div>
        <div class="ns-item">
            <div class="ns-label">Total Deductions</div>
            <div class="ns-value">₹ {{ number_format($payslip->total_deductions, 2) }}</div>
        </div>
        <div class="ns-item">
            <div class="ns-label">Net Take-Home</div>
            <div class="ns-value highlight">₹ {{ number_format($payslip->net_salary, 2) }}</div>
        </div>
    </div>

    {{-- Remarks --}}
    <div style="text-align:center;color:#9caea7;font-size:12px;padding:6px 0 10px;">
        This is a computer-generated payslip and does not require a physical signature.
    </div>

</div>
</div>
@endsection
