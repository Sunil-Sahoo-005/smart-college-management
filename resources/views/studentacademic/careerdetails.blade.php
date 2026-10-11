@extends('studentadmin_layout')
@section('content')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    @php
        $studentName = trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''));
        $studentName = $studentName !== '' ? $studentName : 'Student';
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

        .career-shell {
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
            grid-template-columns: minmax(0, 1.55fr) minmax(300px, 0.95fr);
            gap: 20px;
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
            display: grid;
            gap: 16px;
        }

        .summary-panel {
            border-radius: 22px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.14);
            backdrop-filter: blur(10px);
        }

        .summary-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .summary-item {
            border-radius: 16px;
            padding: 14px;
            background: rgba(7, 20, 16, 0.16);
        }

        .summary-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: rgba(247, 243, 234, 0.68);
            font-weight: 700;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 19px;
            font-weight: 800;
            color: #fffaf3;
            line-height: 1.35;
        }

        .panel-card {
            background: rgba(255, 255, 255, 0.88);
            border: 1px solid rgba(24, 53, 45, 0.08);
            box-shadow: 0 16px 45px rgba(28, 49, 43, 0.08);
            backdrop-filter: blur(14px);
            border-radius: 26px;
            padding: 24px;
        }

        .panel-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 20px;
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

        .info-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .info-item {
            border-radius: 18px;
            padding: 16px;
            background: linear-gradient(180deg, #faf7f1 0%, #edf4ee 100%);
            border: 1px solid rgba(24, 53, 45, 0.07);
        }

        .info-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #7b8b85;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .info-value {
            font-size: 15px;
            font-weight: 700;
            color: #18352d;
            line-height: 1.45;
        }

        .career-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .career-card {
            border-radius: 22px;
            padding: 20px;
            background: #fbf8f2;
            border: 1px solid rgba(24, 53, 45, 0.07);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }

        .career-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 30px rgba(24, 53, 45, 0.08);
        }

        .career-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: #f7e7ce;
            color: #12342b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .career-value-big {
            font-size: 24px;
            font-weight: 800;
            color: #18352d;
            letter-spacing: -0.04em;
        }

        .career-subtext {
            font-size: 12px;
            color: #6b7f77;
            font-weight: 600;
        }

        .table-wrap {
            overflow-x: auto;
            border-radius: 18px;
            border: 1px solid rgba(24, 53, 45, 0.08);
        }

        .premium-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        .premium-table thead th {
            background: #f7f0e2;
            padding: 12px 18px;
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
        }

        .pct-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 999px;
            background: #eef5ef;
            color: #21493d;
            font-size: 13px;
            font-weight: 800;
        }

        .notice-info {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 20px;
            background: #fdf6ee;
            border-radius: 14px;
            border-left: 4px solid #12342b;
            font-size: 13px;
            color: #6b7f77;
            font-weight: 600;
            margin-top: 20px;
        }

        .notice-info i {
            font-size: 18px;
            color: #12342b;
        }

        .empty-state {
            padding: 40px 20px;
            text-align: center;
            background: #faf7f1;
            border-radius: 18px;
            border: 1px dashed rgba(24, 53, 45, 0.1);
        }

        @media (max-width: 1100px) {
            .hero-grid {
                grid-template-columns: 1fr;
            }

            .info-grid,
            .career-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            .career-shell {
                padding: 16px;
            }

            .info-grid,
            .career-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="nxl-content">
        <div class="career-shell">
            <section class="hero-panel">
                <div class="hero-grid">
                    <div>
                        <span class="eyebrow">
                            <i class="feather-award me-2"></i>Academic Profile
                        </span>
                        <h1 class="hero-title">Career Records</h1>
                        <p class="hero-copy">
                            Review your educational history, past achievements, and internal examination results. This
                            comprehensive record tracks your academic growth across various levels.
                        </p>
                        <div class="hero-chips">
                            <span class="hero-chip">
                                <i class="feather-user me-2"></i>{{ $studentName }}
                            </span>
                            <span class="hero-chip">
                                <i class="feather-hash me-2"></i>Reg: {{ $student->admission_number ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                    <div class="hero-side">
                        <div class="summary-panel">
                            <div class="summary-grid">
                                <div class="summary-item">
                                    <div class="summary-label">Class Roll</div>
                                    <div class="summary-value">{{ $student->roll_number ?? 'N/A' }}</div>
                                </div>
                                <div class="summary-item">
                                    <div class="summary-label">Semester</div>
                                    <div class="summary-value">{{ $student->class_name ?? 'N/A' }}</div>
                                </div>
                                <div class="summary-item">
                                    <div class="summary-label">Course</div>
                                    <div class="summary-value">{{ $student->stream_name ?? 'N/A' }}</div>
                                </div>
                                <div class="summary-item">
                                    <div class="summary-label">Batch</div>
                                    <div class="summary-value">{{ $student->academic_name ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Basic Identity --}}
            <article class="panel-card">
                <div class="panel-head">
                    <div>
                        <div class="panel-kicker">Academic Identity</div>
                        <h2 class="panel-title">Personal Information</h2>
                        <p class="panel-copy">Current registration and enrollment details.</p>
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Full Name</div>
                        <div class="info-value">{{ $studentName }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Admission Number</div>
                        <div class="info-value">{{ $student->admission_number ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Course / Stream</div>
                        <div class="info-value">{{ $student->stream_name ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Session</div>
                        <div class="info-value">{{ $student->academic_name ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Current Semester</div>
                        <div class="info-value">{{ $student->class_name ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Section</div>
                        <div class="info-value">{{ $student->section_name ?? 'Not assigned' }}</div>
                    </div>
                </div>
            </article>

            {{-- Career Stats --}}
            <article class="panel-card">
                <div class="panel-head">
                    <div>
                        <div class="panel-kicker">Educational Background</div>
                        <h2 class="panel-title">Prior Qualifications</h2>
                        <p class="panel-copy">Your secondary and higher secondary performance records.</p>
                    </div>
                </div>

                <div class="career-grid">
                    <div class="career-card">
                        <div class="career-icon">🎓</div>
                        <div class="career-info">
                            <div class="career-label">10th Grade</div>
                            <div class="career-value-big">{{ $student->tenth ?? '0' }}%</div>
                            <div class="career-subtext">Passing Year: {{ $student->tenth_yop ?? 'N/A' }}</div>
                        </div>
                    </div>
                    <div class="career-card">
                        <div class="career-icon">📘</div>
                        <div class="career-info">
                            <div class="career-label">12th Grade</div>
                            <div class="career-value-big">{{ $student->twelfth ?? '0' }}%</div>
                            <div class="career-subtext">Passing Year: {{ $student->twelfth_yop ?? 'N/A' }}</div>
                        </div>
                    </div>
                    <div class="career-card">
                        <div class="career-icon">🏛️</div>
                        <div class="career-info">
                            <div class="career-label">Graduation</div>
                            <div class="career-value-big">{{ $student->grad ?? '0' }}%</div>
                            <div class="career-subtext">Passing Year: {{ $student->grad_yop ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </article>

            {{-- Exam Results --}}
            <article class="panel-card">
                <div class="panel-head">
                    <div>
                        <div class="panel-kicker">Internal Performance</div>
                        <h2 class="panel-title">Internal Examination Results</h2>
                        <p class="panel-copy">Summary of internal theory and practical marks obtained.</p>
                    </div>
                </div>

                @if($results->count() > 0)
                    <div class="table-wrap">
                        <table class="premium-table">
                            <thead>
                                <tr>
                                    <th>Exam Name</th>
                                    <th>Total Marks</th>
                                    <th>Marks Obtained</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results as $row)
                                    @php
                                        $percentage = ($row->marks_obtained / $row->total_marks) * 100;
                                    @endphp
                                    <tr>
                                        <td class="fw-bold">{{ $row->exam_name }}</td>
                                        <td>{{ $row->total_marks }}</td>
                                        <td class="fw-bold">{{ $row->marks_obtained }}</td>
                                        <td>
                                            <span class="pct-badge">
                                                <i class="feather-trending-up me-1"></i>
                                                {{ number_format($percentage, 1) }}%
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <p class="panel-copy mb-0">No exam results recorded for the current semester.</p>
                    </div>
                @endif

                <div class="notice-info">
                    <i class="feather-info"></i>
                    <span>Please contact the academic office if any corrections are required regarding your career data.</span>
                </div>
            </article>
        </div>
    </div>
@endsection