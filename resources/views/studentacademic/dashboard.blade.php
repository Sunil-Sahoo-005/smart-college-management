@extends('studentadmin_layout')
@section('content')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    @php
        $studentName = trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''));
        $studentName = $studentName !== '' ? $studentName : 'Student';
        $studentInitials = strtoupper(
            substr($student->first_name ?? 'S', 0, 1) . substr($student->last_name ?? 'T', 0, 1),
        );
        $studentInitials = trim($studentInitials) !== '' ? $studentInitials : 'ST';
        $feeProgress = $totalFees > 0 ? round(($paidFees / $totalFees) * 100) : 0;
        $attendanceTone = $attendancePercentage >= 75 ? 'good' : ($attendancePercentage >= 60 ? 'watch' : 'alert');
    @endphp

    <style>
        * {
            box-sizing: border-box;
        }

        body,
        .nxl-content {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background:
                radial-gradient(circle at top right, rgba(190, 217, 193, 0.46), transparent 28%),
                linear-gradient(180deg, #f5efe3 0%, #eef3ea 100%);
            color: #18352d;
        }

        .portal-shell {
            padding: 26px;
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        .portal-grid {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 18px;
        }

        .hero-panel {
            position: relative;
            overflow: hidden;
            border-radius: 28px;
            padding: 28px;
            background:
                radial-gradient(circle at top left, rgba(255, 255, 255, 0.14), transparent 24%),
                linear-gradient(135deg, #12342b 0%, #1d5548 58%, #c68b3c 160%);
            color: #f7f3ea;
            box-shadow: 0 20px 55px rgba(19, 52, 43, 0.18);
        }

        .hero-panel::after {
            content: '';
            position: absolute;
            inset: auto -70px -90px auto;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.07);
        }

        .hero-grid {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: minmax(0, 1.55fr) minmax(320px, 0.95fr);
            gap: 20px;
            align-items: stretch;
        }

        .eyebrow,
        .panel-kicker,
        .stat-label,
        .profile-label,
        .support-label,
        .fee-tile-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            color: #f6e7ce;
            font-size: 12px;
        }

        .hero-title {
            margin: 16px 0 8px;
            font-size: clamp(28px, 4vw, 42px);
            line-height: 1.06;
            font-weight: 800;
            letter-spacing: -0.04em;
            color: #fffaf3;
        }

        .hero-copy,
        .panel-copy,
        .quick-copy,
        .fee-meta,
        .notice-copy,
        .holiday-copy,
        .empty-copy,
        .stat-subtext {
            font-size: 13px;
            line-height: 1.6;
            color: #6b7f77;
        }

        .hero-copy {
            color: rgba(247, 243, 234, 0.82);
            max-width: 680px;
            margin: 0;
            line-height: 1.7;
        }

        .hero-meta {
            margin-top: 18px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .hero-chip,
        .panel-tag,
        .badge-inline {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .hero-chip {
            padding: 10px 14px;
            background: rgba(255, 255, 255, 0.11);
            border: 1px solid rgba(255, 255, 255, 0.14);
            color: #f7f3ea;
            font-size: 13px;
            font-weight: 600;
        }

        .hero-side {
            display: grid;
            gap: 16px;
        }

        .student-spotlight,
        .due-spotlight,
        .stat-card,
        .panel-card {
            border: 1px solid rgba(24, 53, 45, 0.08);
            box-shadow: 0 16px 45px rgba(28, 49, 43, 0.08);
        }

        .student-spotlight,
        .due-spotlight {
            border-radius: 22px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.14);
            backdrop-filter: blur(10px);
            box-shadow: none;
        }

        .student-identity {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .avatar,
        .avatar-fallback {
            width: 64px;
            height: 64px;
            border-radius: 18px;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.22);
            box-shadow: 0 14px 30px rgba(7, 21, 17, 0.22);
        }

        .avatar-fallback {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f6e7ce 0%, #d59f57 100%);
            color: #12342b;
            font-size: 22px;
            font-weight: 800;
        }

        .identity-label,
        .due-label,
        .side-stat-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: rgba(247, 243, 234, 0.72);
        }

        .identity-name {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .identity-sub,
        .due-detail,
        .side-stat-sub {
            font-size: 13px;
            color: rgba(247, 243, 234, 0.78);
        }

        .side-stat-grid {
            margin-top: 16px;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .side-stat {
            border-radius: 16px;
            padding: 14px;
            background: rgba(7, 20, 16, 0.16);
        }

        .side-stat-value {
            font-size: 19px;
            font-weight: 800;
            color: #fffaf3;
        }

        .due-amount {
            margin: 8px 0 6px;
            font-size: 30px;
            font-weight: 800;
            letter-spacing: -0.04em;
        }

        .stat-card,
        .panel-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(14px);
        }

        .stat-card {
            grid-column: span 3;
            border-radius: 22px;
            padding: 18px;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            inset: auto -12px -22px auto;
            width: 86px;
            height: 86px;
            border-radius: 50%;
            background: rgba(198, 139, 60, 0.1);
        }

        .stat-head,
        .panel-head,
        .fee-row-top,
        .notice-top,
        .holiday-top,
        .progress-topline {
            display: flex;
            justify-content: space-between;
            gap: 12px;
        }

        .stat-head {
            align-items: center;
            margin-bottom: 22px;
        }

        .stat-label {
            color: #6b7f77;
        }

        .stat-icon,
        .quick-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .stat-icon {
            background: #edf5ef;
            color: #12342b;
        }

        .quick-icon {
            background: #12342b;
            color: #f7f3ea;
            margin-bottom: 14px;
        }

        .stat-value,
        .fee-tile-value {
            font-weight: 800;
            letter-spacing: -0.04em;
            color: #18352d;
        }

        .stat-value {
            font-size: 30px;
            line-height: 1;
            margin-bottom: 8px;
        }

        .fee-tile-value {
            font-size: 24px;
        }

        .panel-card {
            border-radius: 26px;
            padding: 22px;
        }

        .panel-card.panel-span-4 {
            grid-column: span 4;
        }

        .panel-card.panel-span-5 {
            grid-column: span 5;
        }

        .panel-card.panel-span-6 {
            grid-column: span 6;
        }

        .panel-card.panel-span-7 {
            grid-column: span 7;
        }

        .panel-card.panel-span-8 {
            grid-column: span 8;
        }

        .panel-head {
            align-items: flex-start;
            margin-bottom: 18px;
        }

        .panel-kicker,
        .profile-label,
        .support-label,
        .fee-tile-label {
            color: #8a7c64;
            margin-bottom: 6px;
        }

        .panel-title {
            margin: 0;
            font-size: 21px;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: #16342d;
        }

        .panel-tag {
            padding: 10px 12px;
            background: #f5efe2;
            color: #85551a;
            white-space: nowrap;
        }

        .quick-grid,
        .profile-grid,
        .support-grid,
        .fee-summary {
            display: grid;
            gap: 12px;
        }

        .quick-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .profile-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .fee-summary {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            margin-bottom: 18px;
        }

        .quick-link,
        .profile-item,
        .support-item,
        .fee-tile,
        .fee-row,
        .notice-item,
        .holiday-item,
        .empty-state {
            border-radius: 18px;
            border: 1px solid rgba(24, 53, 45, 0.07);
        }

        .quick-link {
            display: block;
            text-decoration: none;
            color: inherit;
            padding: 16px;
            background: linear-gradient(180deg, #f9f6ef 0%, #edf4ee 100%);
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }

        .quick-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 30px rgba(24, 53, 45, 0.09);
            color: inherit;
        }

        .quick-title,
        .profile-value,
        .support-value,
        .fee-name,
        .notice-title,
        .holiday-name,
        .empty-title {
            font-size: 15px;
            font-weight: 700;
            color: #18352d;
        }

        .profile-item,
        .support-item,
        .fee-tile,
        .fee-row,
        .notice-item,
        .holiday-item {
            padding: 16px;
            background: #faf7f1;
        }

        .support-value.muted {
            color: #6b7f77;
            font-weight: 600;
        }

        .progress-shell {
            margin: 14px 0 18px;
        }

        .progress-topline {
            align-items: center;
            font-size: 13px;
            font-weight: 700;
            color: #506660;
            margin-bottom: 10px;
        }

        .progress-track,
        .mini-track {
            border-radius: 999px;
            overflow: hidden;
            background: #e8ecdf;
        }

        .progress-track {
            height: 12px;
        }

        .mini-track {
            height: 8px;
            background: #e8ece1;
        }

        .progress-fill,
        .mini-fill {
            height: 100%;
            border-radius: 999px;
        }

        .progress-fill {
            background: linear-gradient(90deg, #12342b 0%, #3f7d69 100%);
        }

        .mini-fill {
            background: linear-gradient(90deg, #c68b3c 0%, #12342b 100%);
        }

        .fee-category-list,
        .notice-list,
        .holiday-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .fee-amount,
        .notice-date,
        .holiday-date {
            font-size: 12px;
            font-weight: 700;
            color: #85551a;
            background: #f6e7ce;
            border-radius: 999px;
            padding: 7px 10px;
            white-space: nowrap;
        }

        .badge-inline {
            padding: 7px 10px;
            background: #eef5ef;
            color: #21493d;
        }

        .badge-inline.watch {
            background: #fff3dd;
            color: #8a5f17;
        }

        .badge-inline.alert {
            background: #fde9e5;
            color: #a04231;
        }

        .badge-inline::before {
            content: '';
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: currentColor;
            opacity: 0.65;
        }

        .empty-state {
            padding: 28px 20px;
            text-align: center;
            background: linear-gradient(180deg, #faf7f1 0%, #f1f5ef 100%);
            border-style: dashed;
            border-color: rgba(24, 53, 45, 0.12);
        }

        .empty-title {
            margin-bottom: 6px;
            font-size: 16px;
        }

        .empty-copy {
            margin: 0;
        }

        .action-link {
            color: #12342b;
            font-weight: 700;
            text-decoration: none;
        }

        .action-link:hover {
            color: #1d5548;
        }

        /* ── Modern Table Improvements ── */
        .premium-table-wrap {
            overflow-x: auto;
            border-radius: 18px;
            border: 1px solid rgba(24, 53, 45, 0.08);
        }

        .premium-table {
            width: 100%;
            border-collapse: collapse;
        }

        .premium-table th {
            background: #f7f0e2;
            padding: 12px 16px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #7b8b85;
            text-align: left;
        }

        .premium-table td {
            padding: 14px 16px;
            font-size: 13px;
            border-bottom: 1px solid rgba(24, 53, 45, 0.06);
        }

        .premium-table tr:last-child td {
            border-bottom: none;
        }

        @media (max-width: 1200px) {
            .hero-grid {
                grid-template-columns: 1fr;
            }

            .stat-card {
                grid-column: span 6;
            }

            .panel-card.panel-span-4,
            .panel-card.panel-span-5,
            .panel-card.panel-span-6,
            .panel-card.panel-span-7,
            .panel-card.panel-span-8 {
                grid-column: span 12;
            }
        }

        @media (max-width: 768px) {
            .portal-shell {
                padding: 16px;
            }

            .hero-panel,
            .panel-card,
            .stat-card {
                border-radius: 20px;
            }

            .stat-card,
            .quick-grid a {
                grid-column: span 12;
            }

            .fee-summary,
            .profile-grid,
            .support-grid,
            .quick-grid,
            .side-stat-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="nxl-content">
        <div class="portal-shell">
            <section class="hero-panel">
                <div class="hero-grid">
                    <div>
                        <span class="eyebrow">
                            <i class="feather-star me-2"></i>Student Portal
                        </span>
                        <h1 class="hero-title">Welcome back, {{ $student->first_name ?? 'Student' }}.</h1>
                        <p class="hero-copy">
                            Your academic, fee, library, and campus updates are now in one place. This dashboard is designed
                            to help you quickly see what matters most today and move straight to the right student service.
                        </p>

                        <div class="hero-meta">
                            <span class="hero-chip">
                                <i class="feather-hash me-2"></i>Reg: {{ $student->admission_number ?? 'N/A' }}
                            </span>
                            <span class="hero-chip">
                                <i class="feather-book-open me-2"></i>{{ $student->stream_name ?? 'Not assigned' }}
                            </span>
                            <span class="hero-chip">
                                <i class="feather-layers me-2"></i>{{ $student->class_name ?? 'Not assigned' }}
                            </span>
                            <span class="hero-chip">
                                <i class="feather-calendar me-2"></i>{{ $student->academic_name ?? 'N/A' }}
                            </span>
                        </div>
                    </div>

                    <div class="hero-side">
                        <div class="student-spotlight">
                            <div class="student-identity">
                                @if (!empty($student->photo_url))
                                    <img src="{{ asset('storage/' . $student->photo_url) }}" alt="{{ $studentName }}"
                                        class="avatar"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                                    <span class="avatar-fallback" style="display:none;">{{ $studentInitials }}</span>
                                @else
                                    <span class="avatar-fallback">{{ $studentInitials }}</span>
                                @endif

                                <div>
                                    <div class="identity-label">Student Profile</div>
                                    <div class="identity-name">{{ $studentName }}</div>
                                    <div class="identity-sub">
                                        {{ $student->stream_name ?? 'Program pending' }}
                                        @if (!empty($student->class_name))
                                            · {{ $student->class_name }}
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="side-stat-grid">
                                <div class="side-stat">
                                    <div class="side-stat-label">Attendance</div>
                                    <div class="side-stat-value">{{ $attendancePercentage }}%</div>
                                    <div class="side-stat-sub">
                                        {{ $monthlyAttendancePercentage !== null ? 'This month: ' . $monthlyAttendancePercentage . '%' : 'Monthly data not available' }}
                                    </div>
                                </div>
                                <div class="side-stat">
                                    <div class="side-stat-label">Library</div>
                                    <div class="side-stat-value">{{ $issuedBooksCount }}</div>
                                    <div class="side-stat-sub">{{ $overdueBooksCount }} overdue</div>
                                </div>
                            </div>
                        </div>

                        <div class="due-spotlight">
                            <div class="due-label">Fee Position</div>
                            <div class="due-amount">₹{{ number_format($dueFees, 0) }}</div>
                            <div class="due-detail">
                                @if ($nextFeeDue)
                                    Next: {{ $nextFeeDue->category_name }} by
                                    {{ \Carbon\Carbon::parse($nextFeeDue->due_date)->format('d M Y') }}
                                @else
                                    Your account looks clear right now.
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="portal-grid">
                {{-- Quick Actions --}}
                <article class="panel-card panel-span-4">
                    <div class="panel-head">
                        <div>
                            <div class="panel-kicker">Navigation</div>
                            <h2 class="panel-title">Quick Actions</h2>
                            <p class="panel-copy">Jump directly to student services.</p>
                        </div>
                    </div>

                    <div class="quick-grid">
                        <a href="{{ route('student.studentsacademic.timetable') }}" class="quick-link">
                            <span class="quick-icon"><i class="feather-calendar"></i></span>
                            <div class="quick-title">Timetable</div>
                            <div class="quick-copy">Check your schedule.</div>
                        </a>

                        <a href="{{ route('student.studentsacademic.accountdetails') }}" class="quick-link">
                            <span class="quick-icon"><i class="feather-dollar-sign"></i></span>
                            <div class="quick-title">Fee Account</div>
                            <div class="quick-copy">Review dues & payments.</div>
                        </a>

                        <a href="{{ route('student.studentsacademic.careerdetails') }}" class="quick-link">
                            <span class="quick-icon"><i class="feather-trending-up"></i></span>
                            <div class="quick-title">Career</div>
                            <div class="quick-copy">Exam performance.</div>
                        </a>

                        <a href="{{ route('student.studentsacademic.attendancedetails') }}" class="quick-link">
                            <span class="quick-icon"><i class="feather-bar-chart-2"></i></span>
                            <div class="quick-title">Attendance</div>
                            <div class="quick-copy">Subject-wise details.</div>
                        </a>
                    </div>
                </article>

                {{-- Profile Details --}}
                <article class="panel-card panel-span-8">
                    <div class="panel-head">
                        <div>
                            <div class="panel-kicker">Academic Identity</div>
                            <h2 class="panel-title">Student Profile</h2>
                            <p class="panel-copy">Registered academic details summary.</p>
                        </div>
                        <span class="badge-inline {{ $attendanceTone }}">
                            {{ $attendancePercentage >= 75 ? 'On Track' : ($attendancePercentage >= 60 ? 'Needs Attention' : 'Critical') }}
                        </span>
                    </div>

                    <div class="profile-grid">
                        <div class="profile-item">
                            <div class="profile-label">Registration Number</div>
                            <div class="profile-value">{{ $student->admission_number ?? 'N/A' }}</div>
                        </div>
                        <div class="profile-item">
                            <div class="profile-label">Roll Number</div>
                            <div class="profile-value">{{ $student->roll_number ?? 'N/A' }}</div>
                        </div>
                        <div class="profile-item">
                            <div class="profile-label">Batch</div>
                            <div class="profile-value">{{ $student->academic_name ?? 'N/A' }}</div>
                        </div>
                        <div class="profile-item">
                            <div class="profile-label">Section</div>
                            <div class="profile-value">{{ $student->section_name ?? 'Not assigned' }}</div>
                        </div>
                        <div class="profile-item">
                            <div class="profile-label">Mobile</div>
                            <div class="profile-value">{{ $student->phone ?? 'Not available' }}</div>
                        </div>
                        <div class="profile-item">
                            <div class="profile-label">Email</div>
                            <div class="profile-value">{{ $student->email ?? 'Not available' }}</div>
                        </div>
                    </div>
                </article>

                {{-- Notices & Holidays --}}
                <article class="panel-card panel-span-6">
                    <div class="panel-head">
                        <div>
                            <div class="panel-kicker">Campus Updates</div>
                            <h2 class="panel-title">Notice Board</h2>
                        </div>
                    </div>
                    @if ($notices->count() > 0)
                        <div class="notice-list">
                            @foreach ($notices as $notice)
                                <div class="notice-item">
                                    <div class="notice-top">
                                        <div class="notice-title">{{ $notice->title }}</div>
                                        <div class="notice-date">{{ \Carbon\Carbon::parse($notice->notice_date)->format('d M') }}</div>
                                    </div>
                                    <p class="notice-copy mt-2 mb-0">{{ Str::limit($notice->description, 100) }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-title">No notices today</div>
                        </div>
                    @endif
                </article>

                <article class="panel-card panel-span-6">
                    <div class="panel-head">
                        <div>
                            <div class="panel-kicker">Upcoming</div>
                            <h2 class="panel-title">Holidays</h2>
                        </div>
                    </div>
                    @if ($holidays->count() > 0)
                        <div class="holiday-list">
                            @foreach ($holidays as $holiday)
                                <div class="holiday-item">
                                    <div class="holiday-top">
                                        <div class="holiday-name">{{ $holiday->name }}</div>
                                        <div class="holiday-date">{{ \Carbon\Carbon::parse($holiday->date)->format('d M Y') }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-title">No upcoming holidays</div>
                        </div>
                    @endif
                </article>

                {{-- Recent Payments --}}
               
            </section>
             <article class="panel-card panel-span-12">
                    <div class="panel-head">
                        <div>
                            <div class="panel-kicker">Financial History</div>
                            <h2 class="panel-title">Recent Payments</h2>
                            <p class="panel-copy">Your last 5 fee transactions recorded in the system.</p>
                        </div>
                        <a href="{{ route('student.studentsacademic.accountdetails') }}" class="action-link">View All <i class="feather-arrow-right"></i></a>
                    </div>

                    @if ($recentPayments->count() > 0)
                        <div class="premium-table-wrap">
                            <table class="premium-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Receipt No</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentPayments as $payment)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($payment->paid_at)->format('d M Y') }}</td>
                                            <td class="fw-bold">{{ $payment->receipt_number }}</td>
                                            <td class="fw-bold text-success">₹{{ number_format($payment->amount_paid, 0) }}</td>
                                            <td><span class="text-uppercase small fw-bold text-muted">{{ $payment->payment_method }}</span></td>
                                            <td>
                                                <span class="badge bg-success text-white">Completed</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-title">No recent payments found</div>
                            <p class="empty-copy">Previous transaction history will appear here once recorded.</p>
                        </div>
                    @endif
                </article>
        </div>
    </div>
@endsection
