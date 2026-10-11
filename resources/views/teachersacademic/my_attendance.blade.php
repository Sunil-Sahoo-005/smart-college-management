@extends('teacher_layout')
@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    *, *::before, *::after { box-sizing: border-box; }

    body, .nxl-content {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background:
            radial-gradient(circle at top right, rgba(190,217,193,0.46), transparent 28%),
            linear-gradient(180deg, #f5efe3 0%, #eef3ea 100%);
        color: #18352d;
    }

    .ta-shell { padding: 26px; display: flex; flex-direction: column; gap: 22px; }

    /* Hero */
    .ta-hero {
        position: relative; overflow: hidden; border-radius: 28px; padding: 28px;
        background: radial-gradient(circle at top left, rgba(255,255,255,0.14), transparent 24%),
                    linear-gradient(135deg, #12342b 0%, #1d5548 58%, #c68b3c 160%);
        color: #f7f3ea;
        box-shadow: 0 20px 55px rgba(19,52,43,0.18);
    }
    .ta-hero::after {
        content:''; position:absolute; inset:auto -70px -90px auto;
        width:240px; height:240px; border-radius:50%; background:rgba(255,255,255,0.07);
    }
    .ta-hero-grid { position:relative; z-index:1; display:grid; grid-template-columns:1fr auto; gap:24px; align-items:center; }
    .eyebrow {
        display:inline-flex; align-items:center; gap:8px; padding:7px 12px; border-radius:999px;
        background:rgba(255,255,255,0.12); color:#f6e7ce; font-size:12px; font-weight:700;
        text-transform:uppercase; letter-spacing:.08em; margin-bottom:14px;
    }
    .ta-title { font-size:clamp(24px,3.5vw,36px); font-weight:800; letter-spacing:-.04em; margin:0 0 8px; }
    .ta-copy  { color:rgba(247,243,234,.82); font-size:14px; line-height:1.7; margin:0; }

    /* Punch card */
    .punch-card {
        background:rgba(255,255,255,.13); border:1px solid rgba(255,255,255,.18);
        backdrop-filter:blur(14px); border-radius:22px; padding:22px; min-width:260px;
    }
    .punch-label { font-size:11px; text-transform:uppercase; letter-spacing:.08em; color:rgba(247,243,234,.7); font-weight:700; margin-bottom:6px; }
    .punch-time  { font-size:36px; font-weight:800; letter-spacing:-.04em; color:#fffaf3; margin:6px 0 16px; }
    .punch-btn {
        display:block; width:100%; padding:14px; border:none; border-radius:16px;
        font-family:inherit; font-size:14px; font-weight:700; cursor:pointer; transition:.2s;
        margin-bottom:8px;
    }
    .punch-btn.in   { background:#22c55e; color:#fff; }
    .punch-btn.out  { background:#f97316; color:#fff; }
    .punch-btn:hover{ opacity:.88; transform:translateY(-1px); }
    .punch-btn:disabled { opacity:.45; cursor:not-allowed; transform:none; }
    .punch-status { font-size:12px; text-align:center; color:rgba(247,243,234,.75); margin-top:4px; }

    /* Stats */
    .stats-grid { display:grid; grid-template-columns:repeat(5,minmax(0,1fr)); gap:16px; }
    .stat-card {
        border-radius:22px; padding:18px;
        background:rgba(255,255,255,.88); border:1px solid rgba(24,53,45,.08);
        box-shadow:0 16px 45px rgba(28,49,43,.08); backdrop-filter:blur(14px);
    }
    .stat-top { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:14px; }
    .stat-label { font-size:11px; text-transform:uppercase; letter-spacing:.08em; color:#6b7f77; font-weight:700; }
    .stat-icon { width:40px; height:40px; border-radius:14px; display:inline-flex; align-items:center; justify-content:center; background:#edf5ef; color:#12342b; font-size:17px; }
    .stat-value { font-size:28px; font-weight:800; letter-spacing:-.04em; color:#18352d; }

    /* Panel */
    .panel-card { border-radius:26px; padding:22px; background:rgba(255,255,255,.88); border:1px solid rgba(24,53,45,.08); box-shadow:0 16px 45px rgba(28,49,43,.08); }
    .panel-head { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom:18px; flex-wrap:wrap; }
    .panel-kicker { font-size:11px; text-transform:uppercase; letter-spacing:.08em; color:#8a7c64; font-weight:700; margin-bottom:6px; }
    .panel-title  { margin:0; font-size:20px; font-weight:800; letter-spacing:-.03em; color:#16342d; }

    /* Filter */
    .filter-row { display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap; }
    .filter-row select, .filter-row button {
        padding:9px 14px; border-radius:12px; border:1px solid rgba(24,53,45,.14);
        font-family:inherit; font-size:13px; background:#fff; color:#18352d;
    }
    .filter-row button { background:#12342b; color:#f7f3ea; font-weight:700; cursor:pointer; border:none; }

    /* Table */
    .att-table { width:100%; border-collapse:collapse; }
    .att-table th {
        font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.07em;
        color:#687872; padding:10px 14px; border-bottom:1px solid rgba(24,53,45,.08); text-align:left;
    }
    .att-table td { padding:12px 14px; border-bottom:1px solid rgba(24,53,45,.05); font-size:13px; color:#2a3f38; }
    .att-table tr:last-child td { border-bottom:none; }
    .att-table tr:hover td { background:rgba(18,52,43,.03); }

    .badge {
        display:inline-flex; align-items:center; gap:6px; padding:5px 10px; border-radius:999px;
        font-size:11px; font-weight:700; text-transform:capitalize;
    }
    .badge.present  { background:#dcfce7; color:#166534; }
    .badge.absent   { background:#fee2e2; color:#991b1b; }
    .badge.half_day { background:#fef9c3; color:#854d0e; }
    .badge.leave    { background:#dbeafe; color:#1e40af; }
    .badge.late     { background:#fff7ed; color:#9a3412; }
    .badge.holiday  { background:#f3e8ff; color:#6b21a8; }
    .badge.week_off { background:#f1f5f9; color:#475569; }

    .empty-state { text-align:center; padding:48px 24px; color:#687872; }
    .empty-state i { font-size:36px; display:block; margin-bottom:14px; color:#9caea7; }

    @media(max-width:1100px){ .stats-grid{ grid-template-columns:repeat(3,1fr); } }
    @media(max-width:768px){
        .ta-shell{ padding:16px; }
        .ta-hero-grid{ grid-template-columns:1fr; }
        .punch-card{ min-width:unset; }
        .stats-grid{ grid-template-columns:repeat(2,1fr); }
    }
</style>

<div class="nxl-content">
<div class="ta-shell">

    {{-- Flash --}}
    @if(session('success'))
        <div style="background:#dcfce7;color:#166534;padding:14px 18px;border-radius:14px;font-weight:600;font-size:14px;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('info'))
        <div style="background:#dbeafe;color:#1e40af;padding:14px 18px;border-radius:14px;font-weight:600;font-size:14px;">
            {{ session('info') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background:#fee2e2;color:#991b1b;padding:14px 18px;border-radius:14px;font-weight:600;font-size:14px;">
            {{ session('error') }}
        </div>
    @endif

    {{-- Hero --}}
    <section class="ta-hero">
        <div class="ta-hero-grid">
            <div>
                <span class="eyebrow"><i class="feather-clock"></i> My Attendance</span>
                <h1 class="ta-title">Staff Attendance Portal</h1>
                <p class="ta-copy">Track your daily check-ins, view monthly attendance history, and manage your working hours all in one place.</p>
                <div style="margin-top:16px;display:flex;gap:10px;flex-wrap:wrap;">
                    <span style="display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:14px;background:rgba(255,255,255,.11);border:1px solid rgba(255,255,255,.14);color:#f7f3ea;font-size:13px;font-weight:700;">
                        <i class="feather-user"></i>
                        {{ optional($staff)->first_name }} {{ optional($staff)->last_name }}
                    </span>
                    <span style="display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:14px;background:rgba(255,255,255,.11);border:1px solid rgba(255,255,255,.14);color:#f7f3ea;font-size:13px;font-weight:700;">
                        <i class="feather-calendar"></i> {{ \Carbon\Carbon::today()->format('l, d M Y') }}
                    </span>
                </div>
            </div>

            {{-- Punch Card --}}
            <div class="punch-card">
                <div class="punch-label">Current Time</div>
                <div class="punch-time" id="liveClock">--:--</div>

                @php
                    $hasPunchedIn  = $todayRecord && $todayRecord->check_in;
                    $hasPunchedOut = $todayRecord && $todayRecord->check_out;
                @endphp

                <form method="POST" action="{{ route('teacher.my_attendance.punch') }}">
                    @csrf
                    @if(!$hasPunchedIn)
                        <button type="submit" class="punch-btn in"><i class="feather-log-in"></i> &nbsp; Punch In</button>
                        <p class="punch-status">Not yet checked in today</p>
                    @elseif($hasPunchedIn && !$hasPunchedOut)
                        <button type="button" class="punch-btn in" disabled>
                            ✅ In at {{ \Carbon\Carbon::parse($todayRecord->check_in)->format('h:i A') }}
                        </button>
                        <button type="submit" class="punch-btn out"><i class="feather-log-out"></i> &nbsp; Punch Out</button>
                        <p class="punch-status">Currently working</p>
                    @else
                        <button type="button" class="punch-btn in" disabled>
                            ✅ In: {{ \Carbon\Carbon::parse($todayRecord->check_in)->format('h:i A') }}
                        </button>
                        <button type="button" class="punch-btn out" disabled>
                            ✅ Out: {{ \Carbon\Carbon::parse($todayRecord->check_out)->format('h:i A') }}
                        </button>
                        @php $mins = $todayRecord->working_minutes ?? 0; @endphp
                        <p class="punch-status">
                            {{ floor($mins/60) }}h {{ $mins%60 }}m worked today
                        </p>
                    @endif
                </form>
            </div>
        </div>
    </section>

    {{-- Stats --}}
    <section class="stats-grid">
        <article class="stat-card">
            <div class="stat-top">
                <div class="stat-label">Present</div>
                <div class="stat-icon" style="background:#dcfce7;color:#166534;"><i class="feather-check-circle"></i></div>
            </div>
            <div class="stat-value" style="color:#166534;">{{ $stats['present'] }}</div>
        </article>
        <article class="stat-card">
            <div class="stat-top">
                <div class="stat-label">Absent</div>
                <div class="stat-icon" style="background:#fee2e2;color:#991b1b;"><i class="feather-x-circle"></i></div>
            </div>
            <div class="stat-value" style="color:#991b1b;">{{ $stats['absent'] }}</div>
        </article>
        <article class="stat-card">
            <div class="stat-top">
                <div class="stat-label">Half Day</div>
                <div class="stat-icon" style="background:#fef9c3;color:#854d0e;"><i class="feather-sun"></i></div>
            </div>
            <div class="stat-value" style="color:#854d0e;">{{ $stats['half_day'] }}</div>
        </article>
        <article class="stat-card">
            <div class="stat-top">
                <div class="stat-label">On Leave</div>
                <div class="stat-icon" style="background:#dbeafe;color:#1e40af;"><i class="feather-umbrella"></i></div>
            </div>
            <div class="stat-value" style="color:#1e40af;">{{ $stats['leave'] }}</div>
        </article>
        <article class="stat-card">
            <div class="stat-top">
                <div class="stat-label">Late Marks</div>
                <div class="stat-icon" style="background:#fff7ed;color:#9a3412;"><i class="feather-alert-circle"></i></div>
            </div>
            <div class="stat-value" style="color:#9a3412;">{{ $stats['late'] }}</div>
        </article>
    </section>

    {{-- Monthly Records --}}
    <section class="panel-card">
        <div class="panel-head">
            <div>
                <div class="panel-kicker">Monthly Report</div>
                <h2 class="panel-title">Attendance History</h2>
            </div>
            <form method="GET" action="{{ route('teacher.my_attendance.index') }}" class="filter-row">
                <select name="month">
                    @foreach(range(1,12) as $m)
                        <option value="{{ $m }}" @selected($m == $month)>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                    @endforeach
                </select>
                <select name="year">
                    @foreach(range(\Carbon\Carbon::now()->year - 2, \Carbon\Carbon::now()->year) as $y)
                        <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                    @endforeach
                </select>
                <button type="submit">Filter</button>
            </form>
        </div>

        @if($monthlyRecords->isNotEmpty())
            <div style="overflow-x:auto;">
                <table class="att-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Hours Worked</th>
                            <th>Status</th>
                            <th>Late</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthlyRecords as $rec)
                            @php
                                $mins = $rec->working_minutes ?? 0;
                                $statusClass = strtolower(str_replace(' ','_',$rec->status));
                            @endphp
                            <tr>
                                <td><strong>{{ \Carbon\Carbon::parse($rec->attendance_date)->format('d M Y') }}</strong></td>
                                <td style="color:#687872;">{{ \Carbon\Carbon::parse($rec->attendance_date)->format('l') }}</td>
                                <td>{{ $rec->check_in ? \Carbon\Carbon::parse($rec->check_in)->format('h:i A') : '—' }}</td>
                                <td>{{ $rec->check_out ? \Carbon\Carbon::parse($rec->check_out)->format('h:i A') : '—' }}</td>
                                <td>
                                    @if($mins > 0)
                                        {{ floor($mins/60) }}h {{ $mins%60 }}m
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $statusClass }}">
                                        {{ ucfirst(str_replace('_',' ',$rec->status)) }}
                                    </span>
                                </td>
                                <td>
                                    @if($rec->is_late)
                                        <span class="badge late">Late</span>
                                    @else
                                        <span style="color:#9caea7;font-size:12px;">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <i class="feather-calendar"></i>
                <div style="font-size:17px;font-weight:700;color:#18352d;margin-bottom:6px;">No Records Found</div>
                <div>No attendance records found for {{ \Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}.</div>
            </div>
        @endif
    </section>

</div>
</div>

<script>
    function updateClock() {
        const now = new Date();
        const h = String(now.getHours()).padStart(2,'0');
        const m = String(now.getMinutes()).padStart(2,'0');
        const s = String(now.getSeconds()).padStart(2,'0');
        document.getElementById('liveClock').textContent = h + ':' + m + ':' + s;
    }
    updateClock();
    setInterval(updateClock, 1000);
</script>

@endsection
