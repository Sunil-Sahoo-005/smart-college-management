@extends('admin_layout')
@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap');

    .lib-dash * { box-sizing: border-box; }
    .lib-dash { font-family: 'Sora', sans-serif; padding: 0 0 2rem; }

    /* ── Topbar ── */
    .lib-topbar {
        display: flex; align-items: center; justify-content: space-between;
        padding: 1.25rem 0 0.5rem;
        border-bottom: 1px solid #e5e7eb;
        margin-bottom: 1.5rem;
    }
    .lib-topbar h1 { font-size: 20px; font-weight: 600; color: #111827; letter-spacing: -0.3px; }
    .lib-topbar p  { font-size: 12px; color: #6b7280; font-family: 'JetBrains Mono', monospace; margin-top: 2px; }
    .lib-topbar-actions { display: flex; gap: 8px; flex-wrap: wrap; }
    .lib-btn {
        font-family: 'Sora', sans-serif; font-size: 12px; font-weight: 500;
        padding: 7px 14px; border-radius: 6px; border: 1px solid #d1d5db;
        background: #fff; color: #374151; cursor: pointer;
        display: inline-flex; align-items: center; gap: 6px;
        text-decoration: none; transition: background 0.15s, border-color 0.15s;
    }
    .lib-btn:hover { background: #f9fafb; }
    .lib-btn-primary { background: #1a56db; color: #fff; border-color: #1a56db; }
    .lib-btn-primary:hover { background: #1748c7; color: #fff; }
    .lib-btn-success { background: #0e7c50; color: #fff; border-color: #0e7c50; }
    .lib-btn-success:hover { background: #0b6842; color: #fff; }
    .lib-btn-outline { background: transparent; color: #374151; border-color: #d1d5db; }
    .lib-btn-outline:hover { background: #f3f4f6; }

    /* ── Metrics ── */
    .lib-metrics {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 1.5rem;
    }
    .lib-metric {
        background: #f9fafb; border: 1px solid #e5e7eb;
        border-radius: 12px; padding: 1.1rem 1.25rem;
        position: relative; overflow: hidden;
    }
    .lib-metric-icon {
        width: 34px; height: 34px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px; margin-bottom: 10px;
    }
    .lib-metric-label {
        font-size: 11px; color: #6b7280;
        font-family: 'JetBrains Mono', monospace;
        letter-spacing: 0.4px; text-transform: uppercase; margin-bottom: 4px;
    }
    .lib-metric-value {
        font-size: 30px; font-weight: 600; color: #111827;
        font-family: 'JetBrains Mono', monospace; letter-spacing: -1.5px; line-height: 1;
    }
    .lib-metric-sub { font-size: 11px; color: #9ca3af; margin-top: 4px; }
    .lib-metric-bar {
        position: absolute; bottom: 0; left: 0; height: 3px; border-radius: 0 2px 0 0;
        transition: width 0.8s ease;
    }

    /* ── Mid row ── */
    .lib-mid {
        display: grid;
        grid-template-columns: 1fr 290px;
        gap: 12px;
        margin-bottom: 1.5rem;
    }

    /* ── Card ── */
    .lib-card {
        background: #fff; border: 1px solid #e5e7eb;
        border-radius: 12px; overflow: hidden;
    }
    .lib-card-header {
        padding: 14px 16px 10px;
        display: flex; align-items: center; justify-content: space-between;
        border-bottom: 1px solid #f3f4f6;
    }
    .lib-card-header h2 { font-size: 13px; font-weight: 600; color: #111827; }
    .lib-see-all {
        font-size: 11px; color: #1a56db;
        font-family: 'JetBrains Mono', monospace; font-weight: 500;
        text-decoration: none;
    }
    .lib-see-all:hover { text-decoration: underline; }

    /* ── Table ── */
    .lib-table-wrap { overflow-x: auto; }
    .lib-table { width: 100%; border-collapse: collapse; font-size: 12px; }
    .lib-table thead th {
        padding: 9px 16px; text-align: left; font-weight: 500;
        color: #6b7280; font-size: 11px; font-family: 'JetBrains Mono', monospace;
        text-transform: uppercase; letter-spacing: 0.3px;
        border-bottom: 1px solid #f3f4f6; background: #fafafa; white-space: nowrap;
    }
    .lib-table tbody tr { border-bottom: 1px solid #f9fafb; transition: background 0.1s; }
    .lib-table tbody tr:last-child { border-bottom: none; }
    .lib-table tbody tr:hover { background: #fafafa; }
    .lib-table tbody td { padding: 10px 16px; color: #374151; vertical-align: middle; }
    .lib-book-title { font-weight: 500; font-size: 12px; color: #111827; max-width: 160px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .lib-book-author { font-size: 10px; color: #9ca3af; margin-top: 1px; }
    .lib-avatar {
        width: 26px; height: 26px; border-radius: 50%;
        background: #e6f1fb; color: #1a56db;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 9px; font-weight: 600; flex-shrink: 0;
    }
    .lib-student { display: flex; align-items: center; gap: 7px; }
    .lib-mono { font-family: 'JetBrains Mono', monospace; font-size: 11px; color: #6b7280; }
    .lib-badge {
        font-size: 10px; font-weight: 500; padding: 3px 8px;
        border-radius: 4px; font-family: 'JetBrains Mono', monospace;
        display: inline-block;
    }
    .lib-badge-issued   { background: #e6f1fb; color: #1a56db; }
    .lib-badge-returned { background: #ecfdf5; color: #065f46; }
    .lib-badge-overdue  { background: #fef2f2; color: #991b1b; }

    /* ── Side column ── */
    .lib-side { display: flex; flex-direction: column; gap: 12px; }
    .lib-mini {
        background: #fff; border: 1px solid #e5e7eb;
        border-radius: 12px; padding: 14px 16px;
    }
    .lib-mini-label {
        font-size: 11px; font-family: 'JetBrains Mono', monospace;
        color: #6b7280; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 6px;
    }
    .lib-mini-value {
        font-size: 24px; font-weight: 600; font-family: 'JetBrains Mono', monospace;
        color: #111827; letter-spacing: -0.8px;
    }
    .lib-mini-sub { font-size: 11px; color: #9ca3af; margin-top: 3px; }

    /* ── Health bars ── */
    .lib-cat-row { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; }
    .lib-cat-row:last-child { margin-bottom: 0; }
    .lib-cat-name { font-size: 11px; color: #6b7280; font-family: 'JetBrains Mono', monospace; width: 70px; flex-shrink: 0; }
    .lib-cat-bg { flex: 1; height: 5px; background: #f3f4f6; border-radius: 3px; overflow: hidden; }
    .lib-cat-fill { height: 100%; border-radius: 3px; transition: width 0.8s ease; }
    .lib-cat-count { font-size: 10px; font-family: 'JetBrains Mono', monospace; color: #9ca3af; min-width: 24px; text-align: right; }

    /* ── Chart section ── */
    .lib-chart-section {
        background: #fff; border: 1px solid #e5e7eb;
        border-radius: 12px; overflow: hidden; margin-bottom: 12px;
    }
    .lib-chart-header {
        padding: 14px 16px 10px;
        display: flex; align-items: center; justify-content: space-between;
        border-bottom: 1px solid #f3f4f6;
    }
    .lib-chart-header h2 { font-size: 13px; font-weight: 600; color: #111827; }
    .lib-legend { display: flex; gap: 14px; }
    .lib-leg { display: flex; align-items: center; gap: 5px; font-size: 10px; font-family: 'JetBrains Mono', monospace; color: #6b7280; }
    .lib-leg-sq { width: 8px; height: 8px; border-radius: 2px; display: inline-block; }
    .lib-chart-wrap { padding: 12px 16px 16px; position: relative; height: 180px; }

    /* ── Bottom row ── */
    .lib-bottom {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }
    .lib-bottom-card {
        background: #fff; border: 1px solid #e5e7eb;
        border-radius: 12px; padding: 14px 16px;
    }
    .lib-bc-label {
        font-size: 11px; font-family: 'JetBrains Mono', monospace;
        color: #6b7280; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 10px;
    }
    .lib-bc-value { font-size: 22px; font-weight: 600; font-family: 'JetBrains Mono', monospace; color: #111827; }
    .lib-bc-unit { font-size: 13px; color: #9ca3af; margin-left: 4px; }
    .lib-progress-bg { width: 100%; height: 5px; background: #f3f4f6; border-radius: 3px; overflow: hidden; margin-top: 10px; }
    .lib-progress-fill { height: 100%; border-radius: 3px; transition: width 0.8s ease; }

    @media (max-width: 900px) {
        .lib-metrics { grid-template-columns: repeat(2, 1fr); }
        .lib-mid { grid-template-columns: 1fr; }
        .lib-bottom { grid-template-columns: 1fr; }
    }
    @media (max-width: 480px) {
        .lib-metrics { grid-template-columns: 1fr 1fr; }
        .lib-topbar-actions .lib-btn span { display: none; }
    }
</style>

<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Library.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">
            <div class="content-area-body p-3">
                <div class="lib-dash">

                    {{-- ── Topbar ── --}}
                    <div class="lib-topbar">
                        <div>
                            <h1>Library dashboard</h1>
                            <p id="lib-date-str">loading...</p>
                        </div>
                        <div class="lib-topbar-actions">
                            <a href="{{ route('admin.books.create') }}" class="lib-btn lib-btn-primary">
                                <i class="feather-plus" style="font-size:13px"></i>
                                <span>Add book</span>
                            </a>
                            <a href="{{ route('admin.book_issues.create') }}" class="lib-btn lib-btn-outline">
                                <i class="feather-book-open" style="font-size:13px"></i>
                                <span>Issue book</span>
                            </a>
                            <a href="{{ route('admin.book_issues.return') }}" class="lib-btn lib-btn-success">
                                <i class="feather-corner-up-left" style="font-size:13px"></i>
                                <span>Return book</span>
                            </a>
                        </div>
                    </div>

                    {{-- ── Metric Cards ── --}}
                    <div class="lib-metrics">
                        <div class="lib-metric">
                            <div class="lib-metric-icon" style="background:#e6f1fb;color:#1a56db">
                                <i class="feather-book"></i>
                            </div>
                            <div class="lib-metric-label">Total books</div>
                            <div class="lib-metric-value">{{ $totalBooks }}</div>
                            <div class="lib-metric-sub">across all categories</div>
                            <div class="lib-metric-bar" style="width:100%;background:#378add"></div>
                        </div>
                        <div class="lib-metric">
                            <div class="lib-metric-icon" style="background:#ecfdf5;color:#065f46">
                                <i class="feather-check-circle"></i>
                            </div>
                            <div class="lib-metric-label">Available</div>
                            <div class="lib-metric-value">{{ $availableCopies }}</div>
                            <div class="lib-metric-sub">
                                @if($totalBooks > 0)
                                    {{ round($availableCopies / $totalBooks * 100) }}% of collection
                                @else
                                    on shelf
                                @endif
                            </div>
                            <div class="lib-metric-bar"
                                 style="width:{{ $totalBooks > 0 ? round($availableCopies / $totalBooks * 100) : 0 }}%;background:#10b981">
                            </div>
                        </div>
                        <div class="lib-metric">
                            <div class="lib-metric-icon" style="background:#fef3c7;color:#92400e">
                                <i class="feather-arrow-up-right"></i>
                            </div>
                            <div class="lib-metric-label">Issued</div>
                            <div class="lib-metric-value">{{ $issuedBooks }}</div>
                            <div class="lib-metric-sub">
                                @if($totalBooks > 0)
                                    {{ round($issuedBooks / $totalBooks * 100) }}% of collection
                                @else
                                    currently out
                                @endif
                            </div>
                            <div class="lib-metric-bar"
                                 style="width:{{ $totalBooks > 0 ? round($issuedBooks / $totalBooks * 100) : 0 }}%;background:#f59e0b">
                            </div>
                        </div>
                        <div class="lib-metric">
                            <div class="lib-metric-icon" style="background:#fef2f2;color:#991b1b">
                                <i class="feather-alert-triangle"></i>
                            </div>
                            <div class="lib-metric-label">Overdue</div>
                            <div class="lib-metric-value" style="color:#dc2626">{{ $overdueBooks }}</div>
                            <div class="lib-metric-sub">past due date</div>
                            <div class="lib-metric-bar"
                                 style="width:{{ $issuedBooks > 0 ? min(round($overdueBooks / $issuedBooks * 100), 100) : 0 }}%;background:#ef4444">
                            </div>
                        </div>
                    </div>

                    {{-- ── Mid Row: Table + Sidebar ── --}}
                    <div class="lib-mid">

                        {{-- Recent Issues Table --}}
                        <div class="lib-card">
                            <div class="lib-card-header">
                                <h2>Recent issues</h2>
                                <a href="{{ route('admin.book_issues.index') }}" class="lib-see-all">view all →</a>
                            </div>
                            <div class="lib-table-wrap">
                                <table class="lib-table">
                                    <thead>
                                        <tr>
                                            <th>Book</th>
                                            <th>Borrower</th>
                                            <th>Issue date</th>
                                            <th>Due date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($recentIssues as $issue)
                                            @php
                                                // ✅ FIX: first_name + last_name use karo, name column nahi hota
                                                $borrower = $issue->student ?? $issue->staff;
                                                $name     = $borrower
                                                    ? trim(($borrower->first_name ?? '') . ' ' . ($borrower->last_name ?? ''))
                                                    : '-';
                                                $isStaff  = is_null($issue->student_id);

                                                // Initials from first + last name
                                                $initials = collect(explode(' ', $name))
                                                    ->filter()
                                                    ->map(fn($w) => strtoupper($w[0]))
                                                    ->take(2)
                                                    ->join('');

                                                $badgeClass = match($issue->status) {
                                                    'issued'   => 'lib-badge-issued',
                                                    'returned' => 'lib-badge-returned',
                                                    'overdue'  => 'lib-badge-overdue',
                                                    default    => 'lib-badge-issued',
                                                };

                                                $avatarBg    = $isStaff ? '#EAF3DE' : '#e6f1fb';
                                                $avatarColor = $isStaff ? '#27500A' : '#1a56db';
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div class="lib-book-title">{{ $issue->book->title ?? '-' }}</div>
                                                    <div class="lib-book-author">{{ $issue->book->author ?? '' }}</div>
                                                </td>
                                                <td>
                                                    <div class="lib-student">
                                                        <div class="lib-avatar"
                                                             style="background:{{ $avatarBg }};color:{{ $avatarColor }};">
                                                            {{ $initials ?: '?' }}
                                                        </div>
                                                        <div>
                                                            <div style="font-size:12px;font-weight:500;color:#111827;">
                                                                {{ $name }}
                                                            </div>
                                                            <div style="font-size:10px;color:#9ca3af;">
                                                                {{ $isStaff ? 'Staff' : 'Student' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="lib-mono">
                                                    {{ \Carbon\Carbon::parse($issue->issue_date)->format('d M Y') }}
                                                </td>
                                                <td class="lib-mono">
                                                    @php
                                                        $due    = \Carbon\Carbon::parse($issue->due_date);
                                                        $isPast = $due->isPast() && $issue->status === 'issued';
                                                    @endphp
                                                    <span style="{{ $isPast ? 'color:#dc2626;font-weight:500' : '' }}">
                                                        {{ $due->format('d M Y') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="lib-badge {{ $badgeClass }}">
                                                        {{ ucfirst($issue->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" style="text-align:center;padding:24px;color:#9ca3af;font-size:12px">
                                                    No issues yet.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Side column --}}
                        <div class="lib-side">
                            <div class="lib-mini">
                                <div class="lib-mini-label">Active library cards</div>
                                <div class="lib-mini-value">{{ $activeCards }}</div>
                                <div class="lib-mini-sub">valid memberships</div>
                            </div>
                            <div class="lib-mini">
                                <div class="lib-mini-label">Collection health</div>
                                <div style="margin-top:8px">
                                    @php
                                        $total = max($totalBooks, 1);
                                        $healthItems = [
                                            ['Available', $availableCopies, '#10b981'],
                                            ['Issued',    $issuedBooks,     '#f59e0b'],
                                            ['Overdue',   $overdueBooks,    '#ef4444'],
                                        ];
                                    @endphp
                                    @foreach ($healthItems as [$label, $val, $color])
                                        <div class="lib-cat-row">
                                            <div class="lib-cat-name">{{ $label }}</div>
                                            <div class="lib-cat-bg">
                                                <div class="lib-cat-fill"
                                                     style="width:{{ round($val / $total * 100) }}%;background:{{ $color }}">
                                                </div>
                                            </div>
                                            <div class="lib-cat-count">{{ $val }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Monthly Trend Chart ── --}}
                    <div class="lib-chart-section">
                        <div class="lib-chart-header">
                            <h2>Monthly issue trend — {{ now()->year }}</h2>
                            <div class="lib-legend">
                                <div class="lib-leg">
                                    <span class="lib-leg-sq" style="background:#1a56db"></span> Issues
                                </div>
                            </div>
                        </div>
                        <div class="lib-chart-wrap">
                            <canvas id="libTrendChart"></canvas>
                        </div>
                    </div>

                    {{-- ── Bottom Row ── --}}
                    <div class="lib-bottom">
                        <div class="lib-bottom-card">
                            <div class="lib-bc-label">Status breakdown</div>
                            @php
                                $statusItems = [
                                    ['Available', $availableCopies, '#1a56db'],
                                    ['Issued',    $issuedBooks,     '#f59e0b'],
                                    ['Overdue',   $overdueBooks,    '#ef4444'],
                                ];
                            @endphp
                            @foreach ($statusItems as [$lbl, $v, $c])
                                <div class="lib-cat-row">
                                    <div class="lib-cat-name">{{ $lbl }}</div>
                                    <div class="lib-cat-bg">
                                        <div class="lib-cat-fill" style="width:{{ round($v / max($totalBooks,1) * 100) }}%;background:{{ $c }}"></div>
                                    </div>
                                    <div class="lib-cat-count">{{ $v }}</div>
                                </div>
                            @endforeach
                        </div>

                        <div class="lib-bottom-card">
                            @php
                                $returned = max($totalBooks - $issuedBooks - $overdueBooks, 0);
                                $retRate  = $totalBooks > 0 ? round($returned / $totalBooks * 100) : 0;
                            @endphp
                            <div class="lib-bc-label">Return rate</div>
                            <div>
                                <span class="lib-bc-value">{{ $retRate }}</span>
                                <span class="lib-bc-unit">%</span>
                            </div>
                            <div class="lib-progress-bg">
                                <div class="lib-progress-fill" style="width:{{ $retRate }}%;background:#10b981"></div>
                            </div>
                            <div class="lib-mini-sub" style="margin-top:8px">{{ $returned }} copies returned</div>
                        </div>

                        <div class="lib-bottom-card">
                            @php
                                $peakMonth = 0; $peakCount = 0;
                                foreach ($monthlyIssues as $m => $c) {
                                    if ($c > $peakCount) { $peakCount = $c; $peakMonth = $m; }
                                }
                                $monthNames = ['','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                                $avgIssues  = count($monthlyIssues) ? round(array_sum($monthlyIssues) / count($monthlyIssues)) : 0;
                            @endphp
                            <div class="lib-bc-label">Peak month</div>
                            <div class="lib-bc-value">{{ $monthNames[$peakMonth] ?? '—' }}</div>
                            <div class="lib-mini-sub" style="margin-top:4px">{{ $peakCount }} issues</div>
                            <div style="margin-top:10px;font-size:11px;font-family:'JetBrains Mono',monospace;color:#9ca3af">
                                avg {{ $avgIssues }} issues / month
                            </div>
                        </div>
                    </div>

                </div>{{-- .lib-dash --}}
            </div>{{-- .content-area-body --}}
        </div>{{-- .content-area --}}
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<script>
(function () {
    // Date string
    var d  = new Date();
    var el = document.getElementById('lib-date-str');
    if (el) {
        el.textContent = d.toLocaleDateString('en-IN', {
            weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
        }).toLowerCase();
    }

    // Monthly data from controller
    var rawMonthly = @json($monthlyIssues);
    var months     = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

    // Build 12-element array, find peak
    var data  = [];
    var peak  = 0;
    var peakV = 0;
    for (var i = 1; i <= 12; i++) {
        var v = rawMonthly[i] || 0;
        data.push(v);
        if (v > peakV) { peakV = v; peak = i - 1; }
    }

    var colors = data.map(function (_, i) {
        return i === peak ? '#1a56db' : '#b5d4f4';
    });

    new Chart(document.getElementById('libTrendChart'), {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Issues',
                data: data,
                backgroundColor: colors,
                borderRadius: 4,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        title: function (c) { return months[c[0].dataIndex]; },
                        label: function (c) { return c.raw + ' issues'; }
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: { color: '#9ca3af', font: { size: 10, family: 'JetBrains Mono' } },
                    border: { display: false }
                },
                y: {
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: { color: '#9ca3af', font: { size: 10, family: 'JetBrains Mono' }, stepSize: 5 },
                    border: { display: false }
                }
            }
        }
    });
})();
</script>
@endsection