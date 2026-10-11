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
    .lv-shell { padding: 26px; display: flex; flex-direction: column; gap: 22px; }

    /* Hero */
    .lv-hero {
        position: relative; overflow: hidden; border-radius: 28px; padding: 28px;
        background: radial-gradient(circle at top left, rgba(255,255,255,0.14), transparent 24%),
                    linear-gradient(135deg, #12342b 0%, #1d5548 58%, #c68b3c 160%);
        color: #f7f3ea; box-shadow: 0 20px 55px rgba(19,52,43,0.18);
    }
    .lv-hero::after {
        content:''; position:absolute; inset:auto -70px -90px auto;
        width:240px; height:240px; border-radius:50%; background:rgba(255,255,255,0.07);
    }
    .lv-hero-inner { position:relative; z-index:1; display:flex; align-items:center; justify-content:space-between; gap:18px; flex-wrap:wrap; }
    .eyebrow {
        display:inline-flex; align-items:center; gap:8px; padding:7px 12px; border-radius:999px;
        background:rgba(255,255,255,0.12); color:#f6e7ce; font-size:12px; font-weight:700;
        text-transform:uppercase; letter-spacing:.08em; margin-bottom:14px;
    }
    .lv-title { font-size:clamp(22px,3vw,34px); font-weight:800; letter-spacing:-.04em; margin:0 0 8px; }
    .lv-copy  { color:rgba(247,243,234,.82); font-size:14px; line-height:1.7; margin:0; max-width:600px; }
    .hero-btn {
        display:inline-flex; align-items:center; gap:8px; padding:14px 22px; border-radius:16px;
        background:#c68b3c; color:#fff; font-family:inherit; font-size:14px; font-weight:700;
        border:none; cursor:pointer; text-decoration:none; white-space:nowrap; transition:.2s;
    }
    .hero-btn:hover { background:#b07630; color:#fff; transform:translateY(-1px); }

    /* Stats */
    .stats-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:16px; }
    .stat-card {
        border-radius:22px; padding:18px;
        background:rgba(255,255,255,.88); border:1px solid rgba(24,53,45,.08);
        box-shadow:0 16px 45px rgba(28,49,43,.08); backdrop-filter:blur(14px);
    }
    .stat-top { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:14px; }
    .stat-label { font-size:11px; text-transform:uppercase; letter-spacing:.08em; color:#6b7f77; font-weight:700; }
    .stat-icon { width:40px; height:40px; border-radius:14px; display:inline-flex; align-items:center; justify-content:center; font-size:17px; }
    .stat-value { font-size:28px; font-weight:800; letter-spacing:-.04em; }

    /* Panel */
    .panel-card {
        border-radius:26px; padding:22px;
        background:rgba(255,255,255,.88); border:1px solid rgba(24,53,45,.08);
        box-shadow:0 16px 45px rgba(28,49,43,.08);
    }
    .panel-head { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom:18px; }
    .panel-kicker { font-size:11px; text-transform:uppercase; letter-spacing:.08em; color:#8a7c64; font-weight:700; margin-bottom:6px; }
    .panel-title  { margin:0; font-size:20px; font-weight:800; letter-spacing:-.03em; color:#16342d; }

    /* Table */
    .lv-table { width:100%; border-collapse:collapse; }
    .lv-table th {
        font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.07em;
        color:#687872; padding:10px 14px; border-bottom:1px solid rgba(24,53,45,.08); text-align:left;
    }
    .lv-table td { padding:13px 14px; border-bottom:1px solid rgba(24,53,45,.05); font-size:13px; color:#2a3f38; }
    .lv-table tr:last-child td { border-bottom:none; }
    .lv-table tr:hover td { background:rgba(18,52,43,.03); }

    .badge {
        display:inline-flex; align-items:center; gap:5px; padding:5px 10px;
        border-radius:999px; font-size:11px; font-weight:700; text-transform:capitalize;
    }
    .badge.pending  { background:#fef9c3; color:#854d0e; }
    .badge.approved { background:#dcfce7; color:#166534; }
    .badge.rejected { background:#fee2e2; color:#991b1b; }
    .badge.cancelled{ background:#f1f5f9; color:#475569; }

    .reason-snippet { max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; color:#687872; }

    .empty-state { text-align:center; padding:48px 24px; color:#687872; }
    .empty-state i { font-size:36px; display:block; margin-bottom:14px; color:#9caea7; }
    .empty-btn {
        display:inline-flex; align-items:center; gap:8px; padding:12px 20px; border-radius:14px;
        background:#12342b; color:#f7f3ea; font-family:inherit; font-size:13px; font-weight:700;
        border:none; cursor:pointer; text-decoration:none; margin-top:14px; transition:.2s;
    }
    .empty-btn:hover { background:#1d5548; color:#f7f3ea; }

    @media(max-width:768px){ .lv-shell{padding:16px;} .stats-grid{grid-template-columns:1fr;} }
</style>

<div class="nxl-content">
<div class="lv-shell">

    {{-- Flash --}}
    @if(session('success'))
        <div style="background:#dcfce7;color:#166534;padding:14px 18px;border-radius:14px;font-weight:600;font-size:14px;">
            {{ session('success') }}
        </div>
    @endif

    {{-- Hero --}}
    <section class="lv-hero">
        <div class="lv-hero-inner">
            <div>
                <span class="eyebrow"><i class="feather-calendar"></i> Leave Management</span>
                <h1 class="lv-title">My Leave Requests</h1>
                <p class="lv-copy">Submit and track your leave applications. Approved leaves are reflected in your attendance and payslip automatically.</p>
            </div>
            <a href="{{ route('teacher.my_leave.create') }}" class="hero-btn">
                <i class="feather-plus"></i> New Request
            </a>
        </div>
    </section>

    {{-- Stats --}}
    <section class="stats-grid">
        <article class="stat-card">
            <div class="stat-top">
                <div class="stat-label">Pending</div>
                <div class="stat-icon" style="background:#fef9c3;color:#854d0e;"><i class="feather-clock"></i></div>
            </div>
            <div class="stat-value" style="color:#854d0e;">{{ $stats['pending'] }}</div>
        </article>
        <article class="stat-card">
            <div class="stat-top">
                <div class="stat-label">Approved</div>
                <div class="stat-icon" style="background:#dcfce7;color:#166534;"><i class="feather-check-circle"></i></div>
            </div>
            <div class="stat-value" style="color:#166534;">{{ $stats['approved'] }}</div>
        </article>
        <article class="stat-card">
            <div class="stat-top">
                <div class="stat-label">Rejected</div>
                <div class="stat-icon" style="background:#fee2e2;color:#991b1b;"><i class="feather-x-circle"></i></div>
            </div>
            <div class="stat-value" style="color:#991b1b;">{{ $stats['rejected'] }}</div>
        </article>
    </section>

    {{-- Table --}}
    <section class="panel-card">
        <div class="panel-head">
            <div>
                <div class="panel-kicker">All Requests</div>
                <h2 class="panel-title">Leave History</h2>
            </div>
            <a href="{{ route('teacher.my_leave.create') }}" class="hero-btn" style="padding:10px 18px;font-size:13px;">
                <i class="feather-plus"></i> Apply Leave
            </a>
        </div>

        @if($leaveRequests->isNotEmpty())
            <div style="overflow-x:auto;">
                <table class="lv-table">
                    <thead>
                        <tr>
                            <th>Leave Type</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Days</th>
                            <th>Half Day</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Applied On</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leaveRequests as $req)
                            <tr>
                                <td><strong>{{ $req->leaveType?->name ?? 'N/A' }}</strong></td>
                                <td>{{ \Carbon\Carbon::parse($req->from_date)->format('d M Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($req->to_date)->format('d M Y') }}</td>
                                <td>
                                    <span style="font-weight:700;color:#12342b;">{{ $req->total_days }}</span>
                                    <span style="color:#687872;font-size:12px;">day{{ $req->total_days != 1 ? 's' : '' }}</span>
                                </td>
                                <td>
                                    @if($req->half_day !== 'none')
                                        <span style="font-size:12px;color:#854d0e;font-weight:600;">{{ ucfirst($req->half_day) }} half</span>
                                    @else
                                        <span style="color:#9caea7;font-size:12px;">—</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="reason-snippet" title="{{ $req->reason }}">
                                        {{ $req->reason ?: '—' }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ $req->status }}">
                                        @if($req->status === 'pending') <i class="feather-clock"></i>
                                        @elseif($req->status === 'approved') <i class="feather-check"></i>
                                        @elseif($req->status === 'rejected') <i class="feather-x"></i>
                                        @endif
                                        {{ ucfirst($req->status) }}
                                    </span>
                                </td>
                                <td style="color:#687872;font-size:12px;">
                                    {{ $req->created_at?->format('d M Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="padding-top:16px;">
                {{ $leaveRequests->links() }}
            </div>
        @else
            <div class="empty-state">
                <i class="feather-calendar"></i>
                <div style="font-size:17px;font-weight:700;color:#18352d;margin-bottom:6px;">No Leave Requests Yet</div>
                <div>You haven't submitted any leave requests. Click below to apply.</div>
                <a href="{{ route('teacher.my_leave.create') }}" class="empty-btn">
                    <i class="feather-plus"></i> Apply for Leave
                </a>
            </div>
        @endif
    </section>

</div>
</div>
@endsection
