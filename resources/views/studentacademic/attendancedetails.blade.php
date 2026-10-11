@extends('studentadmin_layout')
@section('content')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    @php
        $studentName = trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''));
        $studentName = $studentName !== '' ? $studentName : 'Student';
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

        .attendance-shell {
            padding: 26px;
            display: flex;
            flex-direction: column;
            gap: 22px;
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
            grid-template-columns: minmax(0, 1.55fr) minmax(280px, 0.95fr);
            gap: 18px;
            align-items: stretch;
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
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .hero-title {
            margin: 16px 0 8px;
            font-size: clamp(28px, 4vw, 40px);
            line-height: 1.06;
            font-weight: 800;
            letter-spacing: -0.04em;
            color: #fffaf3;
        }

        .hero-copy {
            max-width: 700px;
            margin: 0;
            color: rgba(247, 243, 234, 0.82);
            font-size: 14px;
            line-height: 1.7;
        }

        .hero-chips {
            margin-top: 18px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .hero-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.11);
            border: 1px solid rgba(255, 255, 255, 0.14);
            color: #f7f3ea;
            font-size: 13px;
            font-weight: 700;
        }

        .hero-side {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .overall-panel {
            border-radius: 22px;
            padding: 24px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.14);
            backdrop-filter: blur(10px);
            text-align: center;
        }

        .overall-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: rgba(247, 243, 234, 0.7);
            margin-bottom: 8px;
            font-weight: 700;
        }

        .overall-value {
            font-size: 52px;
            font-weight: 800;
            color: #fffaf3;
            line-height: 1;
            letter-spacing: -0.04em;
        }

        .overall-meta {
            margin-top: 10px;
            font-size: 13px;
            color: rgba(247, 243, 234, 0.8);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
        }

        .stat-card,
        .panel-card {
            background: rgba(255, 255, 255, 0.88);
            border: 1px solid rgba(24, 53, 45, 0.08);
            box-shadow: 0 16px 45px rgba(28, 49, 43, 0.08);
            backdrop-filter: blur(14px);
        }

        .stat-card {
            border-radius: 22px;
            padding: 18px;
        }

        .stat-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px;
        }

        .stat-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6b7f77;
            font-weight: 700;
        }

        .stat-icon {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #edf5ef;
            color: #12342b;
            font-size: 18px;
        }

        .stat-value {
            font-size: 30px;
            line-height: 1;
            font-weight: 800;
            letter-spacing: -0.04em;
            color: #18352d;
            margin-bottom: 8px;
        }

        .stat-copy {
            font-size: 13px;
            line-height: 1.6;
            color: #687872;
        }

        .panel-card {
            border-radius: 26px;
            padding: 22px;
        }

        .panel-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 18px;
        }

        .panel-kicker {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #8a7c64;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .panel-title {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: #16342d;
        }

        .panel-copy {
            margin: 6px 0 0;
            color: #667871;
            font-size: 13px;
            line-height: 1.6;
        }

        .badge-soft {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 12px;
            border-radius: 999px;
            background: #f5efe2;
            color: #85551a;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .badge-soft.good {
            background: #eef5ef;
            color: #21493d;
        }

        .badge-soft.watch {
            background: #fff3dd;
            color: #8a5f17;
        }

        .badge-soft.alert {
            background: #fde9e5;
            color: #a04231;
        }

        .badge-soft::before {
            content: '';
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: currentColor;
            opacity: 0.7;
        }

        .table-wrap {
            overflow-x: auto;
            border-radius: 18px;
            border: 1px solid rgba(24, 53, 45, 0.08);
        }

        .premium-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        .premium-table thead th {
            background: #f7f0e2;
            padding: 14px 18px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #7b8b85;
            text-align: left;
            border-bottom: 1px solid rgba(24, 53, 45, 0.08);
        }

        .premium-table tbody tr {
            border-bottom: 1px solid rgba(24, 53, 45, 0.06);
            transition: background 0.1s ease;
        }

        .premium-table tbody tr:hover {
            background: #fbf8f2;
        }

        .premium-table tbody tr:last-child {
            border-bottom: none;
        }

        .premium-table td {
            padding: 16px 18px;
            font-size: 14px;
            color: #18352d;
            vertical-align: middle;
        }

        .subject-name {
            font-weight: 700;
            color: #12342b;
        }

        .subject-code {
            font-size: 11px;
            color: #7b8b85;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            text-transform: capitalize;
        }

        .status-pill.present {
            background: #eef5ef;
            color: #21493d;
        }

        .status-pill.absent {
            background: #fde9e5;
            color: #a04231;
        }

        .status-pill.late {
            background: #fff3dd;
            color: #8a5f17;
        }

        .status-pill.half_day {
            background: #f1f4f9;
            color: #3e5b8a;
        }

        .progress-mini {
            height: 8px;
            background: #e8ecdf;
            border-radius: 999px;
            overflow: hidden;
            width: 100px;
        }

        .progress-mini-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #c68b3c 0%, #12342b 100%);
        }

        .empty-state {
            padding: 48px 24px;
            text-align: center;
            background: linear-gradient(180deg, #faf7f1 0%, #f1f5ef 100%);
            border: 1px dashed rgba(24, 53, 45, 0.12);
            border-radius: 22px;
        }

        .empty-icon {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #eaf5ee;
            color: #1f5d46;
            font-size: 24px;
            margin-bottom: 16px;
        }

        @media (max-width: 1100px) {
            .hero-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            .attendance-shell {
                padding: 16px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .panel-head {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>

    <div class="nxl-content">
        <div class="attendance-shell">
            <section class="hero-panel">
                <div class="hero-grid">
                    <div>
                        <span class="eyebrow">
                            <i class="feather-bar-chart-2 me-2"></i>Academic Analytics
                        </span>
                        <h1 class="hero-title">Attendance Insights</h1>
                        <p class="hero-copy">
                            Monitor your lecture presence and subject-wise trends. Maintaining a healthy attendance rate is
                            crucial for academic success and meeting institutional requirements.
                        </p>
                        <div class="hero-chips">
                            <span class="hero-chip">
                                <i class="feather-user me-2"></i>{{ $studentName }}
                            </span>
                            <span class="hero-chip">
                                <i class="feather-hash me-2"></i>Reg: {{ $student->admission_number ?? 'N/A' }}
                            </span>
                            <span class="hero-chip">
                                <i class="feather-layers me-2"></i>{{ $student->class_name ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                    <div class="hero-side">
                        <div class="overall-panel">
                            <div class="overall-label">Overall Rate</div>
                            <div class="overall-value">{{ $attendancePercentage }}%</div>
                            <div class="overall-meta">
                                Based on {{ $totalClasses }} total sessions
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="stats-grid">
                <div class="stat-card">
                    <div class="stat-top">
                        <div class="stat-label">Present</div>
                        <div class="stat-icon"><i class="feather-check-circle"></i></div>
                    </div>
                    <div class="stat-value">{{ $presentClasses }}</div>
                    <div class="stat-copy">Successfully attended sessions.</div>
                </div>
                <div class="stat-card">
                    <div class="stat-top">
                        <div class="stat-label">Absent</div>
                        <div class="stat-icon" style="background:#fde9e5; color:#a04231;"><i class="feather-x-circle"></i></div>
                    </div>
                    <div class="stat-value">{{ $absentClasses }}</div>
                    <div class="stat-copy">Missed academic sessions.</div>
                </div>
                <div class="stat-card">
                    <div class="stat-top">
                        <div class="stat-label">Late</div>
                        <div class="stat-icon" style="background:#fff3dd; color:#8a5f17;"><i class="feather-clock"></i></div>
                    </div>
                    <div class="stat-value">{{ $lateClasses }}</div>
                    <div class="stat-copy">Sessions with delayed entry.</div>
                </div>
                <div class="stat-card">
                    <div class="stat-top">
                        <div class="stat-label">Monthly</div>
                        <div class="stat-icon" style="background:#f1f4f9; color:#3e5b8a;"><i class="feather-calendar"></i></div>
                    </div>
                    <div class="stat-value">{{ $monthlyAttendancePercentage ?? 'N/A' }}%</div>
                    <div class="stat-copy">Attendance for the current month.</div>
                </div>
            </section>

            {{-- Subject Wise Breakdown --}}
            <section class="panel-card">
                <div class="panel-head">
                    <div>
                        <div class="panel-kicker">Course Performance</div>
                        <h2 class="panel-title">Subject-wise Attendance</h2>
                        <p class="panel-copy">Detailed breakdown of your presence in each registered course.</p>
                    </div>
                    <span class="badge-soft {{ $attendanceTone }}">
                        {{ $attendancePercentage >= 75 ? 'Excellent' : ($attendancePercentage >= 60 ? 'Satisfactory' : 'Action Required') }}
                    </span>
                </div>

                @if ($subjectAttendance->count() > 0)
                    <div class="table-wrap">
                        <table class="premium-table">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>Total</th>
                                    <th>Present</th>
                                    <th>Absent</th>
                                    <th>Late</th>
                                    <th>Percentage</th>
                                    <th>Trend</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($subjectAttendance as $subject)
                                    <tr>
                                        <td>
                                            <div class="subject-name">{{ $subject->subject_name }}</div>
                                            <div class="subject-code">{{ $subject->subject_code }}</div>
                                        </td>
                                        <td class="fw-bold">{{ $subject->total }}</td>
                                        <td class="text-success fw-bold">{{ $subject->present }}</td>
                                        <td class="text-danger fw-bold">{{ $subject->absent }}</td>
                                        <td class="text-warning fw-bold">{{ $subject->late }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <span class="fw-bold">{{ $subject->percentage }}%</span>
                                                <div class="progress-mini">
                                                    <div class="progress-mini-fill" style="width: {{ $subject->percentage }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($subject->percentage >= 75)
                                                <span class="text-success"><i class="feather-trending-up"></i> High</span>
                                            @elseif($subject->percentage >= 60)
                                                <span class="text-warning"><i class="feather-minus"></i> Stable</span>
                                            @else
                                                <span class="text-danger"><i class="feather-trending-down"></i> Low</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon"><i class="feather-book-open"></i></div>
                        <h3 class="panel-title">No subject data found</h3>
                        <p class="panel-copy">Attendance mapping will appear once timetable and records are synced.</p>
                    </div>
                @endif
            </section>

            {{-- Recent Records --}}
            <section class="panel-card">
                <div class="panel-head">
                    <div>
                        <div class="panel-kicker">Recent Activity</div>
                        <h2 class="panel-title">Daily Attendance Logs</h2>
                        <p class="panel-copy">Your last 12 recorded sessions across all subjects.</p>
                    </div>
                </div>

                @if ($recentAttendance->count() > 0)
                    <div class="table-wrap">
                        <table class="premium-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Period / Time</th>
                                    <th>Status</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentAttendance as $record)
                                    <tr>
                                        <td class="fw-bold">{{ \Carbon\Carbon::parse($record->date)->format('d M Y') }}</td>
                                        <td>
                                            @if ($record->period)
                                                <div class="fw-bold text-dark">{{ $record->period->name }}</div>
                                                <div class="small text-muted">{{ $record->period->start_time }} - {{ $record->period->end_time }}</div>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="status-pill {{ $record->status }}">
                                                {{ str_replace('_', ' ', $record->status) }}
                                            </span>
                                        </td>
                                        <td class="text-muted small">
                                            {{ $record->remarks ?: 'No remarks' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon"><i class="feather-clock"></i></div>
                        <h3 class="panel-title">No recent logs</h3>
                        <p class="panel-copy">Your daily attendance history will be listed here.</p>
                    </div>
                @endif
            </section>
        </div>
    </div>
@endsection
