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

        .exam-shell {
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
            margin-bottom: 24px;
        }

        .stat-card {
            border-radius: 22px;
            padding: 24px;
            background: #fbf8f2;
            border: 1px solid rgba(24, 53, 45, 0.07);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 14px;
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 30px rgba(24, 53, 45, 0.08);
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            background: #f7e7ce;
            color: #12342b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .stat-value-big {
            font-size: 32px;
            font-weight: 800;
            color: #18352d;
            letter-spacing: -0.04em;
        }

        .stat-label {
            font-size: 13px;
            font-weight: 600;
            color: #667871;
            text-align: center;
        }

        .exam-type-section {
            margin-top: 28px;
        }

        .exam-type-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid rgba(24, 53, 45, 0.08);
        }

        .exam-type-badge {
            padding: 6px 14px;
            border-radius: 999px;
            background: linear-gradient(135deg, #1d5548 0%, #2d8a74 100%);
            color: white;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .exam-type-title {
            font-size: 18px;
            font-weight: 700;
            color: #16342d;
            margin: 0;
        }

        .results-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .results-table thead th {
            background: linear-gradient(180deg, #faf7f1 0%, #edf4ee 100%);
            padding: 14px 16px;
            text-align: left;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #7b8b85;
            border-bottom: 1px solid rgba(24, 53, 45, 0.08);
        }

        .results-table thead th:first-child {
            border-radius: 12px 0 0 0;
        }

        .results-table thead th:last-child {
            border-radius: 0 12px 0 0;
        }

        .results-table tbody td {
            padding: 14px 16px;
            border-bottom: 1px solid rgba(24, 53, 45, 0.05);
            font-size: 14px;
            color: #18352d;
        }

        .results-table tbody tr:last-child td {
            border-bottom: none;
        }

        .results-table tbody tr:hover {
            background: rgba(29, 85, 72, 0.03);
        }

        .marks-cell {
            font-weight: 700;
            font-size: 16px;
        }

        .grade-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            min-width: 40px;
        }

        .grade-a {
            background: #d4edda;
            color: #155724;
        }

        .grade-b {
            background: #cce5ff;
            color: #004085;
        }

        .grade-c {
            background: #fff3cd;
            color: #856404;
        }

        .grade-d {
            background: #f8d7da;
            color: #721c24;
        }

        .grade-f {
            background: #f5c6cb;
            color: #721c24;
        }

        .grade-na {
            background: #e2e3e5;
            color: #383d41;
        }

        .pass-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .pass-badge.pass {
            background: #d4edda;
            color: #155724;
        }

        .pass-badge.fail {
            background: #f8d7da;
            color: #721c24;
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
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state i {
            font-size: 48px;
            color: #a0aec0;
            margin-bottom: 16px;
        }

        .empty-state h5 {
            color: #4a5568;
            margin-bottom: 8px;
        }

        .empty-state p {
            color: #718096;
            font-size: 14px;
        }

        @media (max-width: 1100px) {
            .hero-grid {
                grid-template-columns: 1fr;
            }

            .info-grid,
            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            .exam-shell {
                padding: 16px;
            }

            .info-grid,
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .results-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>

    <div class="nxl-content">
        <div class="exam-shell">
            <section class="hero-panel">
                <div class="hero-grid">
                    <div>
                        <span class="eyebrow">
                            <i class="feather-trophy me-2"></i>Exam Results
                        </span>
                        <h1 class="hero-title">Academic Performance</h1>
                        <p class="hero-copy">
                            View your complete exam history across all exam types. Track your performance, grades, and 
                            academic progress throughout your enrollment.
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
                                    <div class="summary-label">Active Class</div>
                                    <div class="summary-value">{{ $student->class_name ?? 'N/A' }}</div>
                                </div>
                                <div class="summary-item">
                                    <div class="summary-label">Section</div>
                                    <div class="summary-value">{{ $student->section_name ?? 'N/A' }}</div>
                                </div>
                                <div class="summary-item">
                                    <div class="summary-label">Session</div>
                                    <div class="summary-value">{{ $student->academic_name ?? 'N/A' }}</div>
                                </div>
                                <div class="summary-item">
                                    <div class="summary-label">Program</div>
                                    <div class="summary-value">{{ $student->stream_name ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

          
            {{-- Overall Statistics --}}
            @if($totalExams > 0)
            <article class="panel-card">
                <div class="panel-head">
                    <div>
                        <div class="panel-kicker">Performance Metrics</div>
                        <h2 class="panel-title">Overall Statistics</h2>
                        <p class="panel-copy">Summary of your exam performance across all exam types.</p>
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">📝</div>
                        <div class="stat-value-big">{{ $totalExams }}</div>
                        <div class="stat-label">Total Exams</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">📊</div>
                        <div class="stat-value-big">{{ number_format($overallPercentage, 1) }}%</div>
                        <div class="stat-label">Overall Percentage</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">✅</div>
                        <div class="stat-value-big">{{ $passedExams }}</div>
                        <div class="stat-label">Exams Passed</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">❌</div>
                        <div class="stat-value-big">{{ $failedExams }}</div>
                        <div class="stat-label">Exams Failed</div>
                    </div>
                </div>
            </article>

            {{-- Exam Results by Type --}}
            @foreach($groupedByExamType as $examTypeId => $results)
                @php
                    $examTypeName = $results->first()->exam_type_name ?? 'General';
                    $firstResult = $results->first();
                @endphp
            <article class="panel-card">
                <div class="exam-type-section">
                    <div class="exam-type-header">
                        <span class="exam-type-badge">{{ $examTypeName }}</span>
                        <h3 class="exam-type-title mb-0">{{ $firstResult->academic_year_name ?? '' }}</h3>
                    </div>

                    <div class="table-responsive">
                        <table class="results-table">
                            <thead>
                                <tr>
                                    <th>Exam Name</th>
                                    <th>Subject</th>
                                    <th>Term</th>
                                    <th>Date</th>
                                    <th class="text-center">Marks</th>
                                    <th class="text-center">Grade</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results as $result)
                                    @php
                                        $percentage = $result->total_marks > 0 
                                            ? ($result->marks_obtained / $result->total_marks) * 100 
                                            : 0;
                                        $isPass = $result->marks_obtained >= $result->pass_marks;
                                        $gradeClass = '';
                                        if ($result->grade_letter) {
                                            $gradeUpper = strtoupper($result->grade_letter);
                                            if ($gradeUpper == 'A+' || $gradeUpper == 'A') $gradeClass = 'grade-a';
                                            else if ($gradeUpper == 'B+' || $gradeUpper == 'B') $gradeClass = 'grade-b';
                                            else if ($gradeUpper == 'C+' || $gradeUpper == 'C') $gradeClass = 'grade-c';
                                            else if ($gradeUpper == 'D') $gradeClass = 'grade-d';
                                            else $gradeClass = 'grade-f';
                                        } else {
                                            $gradeClass = 'grade-na';
                                        }
                                    @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $result->exam_name }}</div>
                                    </td>
                                    <td>
                                        <div>{{ $result->subject_name }}</div>
                                        @if($result->subject_code)
                                            <small class="text-muted">{{ $result->subject_code }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $result->term_name ?? 'N/A' }}</td>
                                    <td>{{ $result->exam_date ? \Carbon\Carbon::parse($result->exam_date)->format('d M Y') : 'N/A' }}</td>
                                    <td class="text-center">
                                        <div class="marks-cell">{{ $result->marks_obtained }} / {{ $result->total_marks }}</div>
                                        <small class="text-muted">{{ number_format($percentage, 1) }}%</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="grade-badge {{ $gradeClass }}">
                                            {{ $result->grade_letter ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="pass-badge {{ $isPass ? 'pass' : 'fail' }}">
                                            @if($isPass)
                                                <i class="feather-check" style="font-size:12px;"></i> Pass
                                            @else
                                                <i class="feather-x" style="font-size:12px;"></i> Fail
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </article>
            @endforeach
            @else
            {{-- Empty State --}}
            <article class="panel-card">
                <div class="empty-state">
                    <i class="feather-file-text"></i>
                    <h5>No Exam Results Found</h5>
                    <p>Your exam results will appear here once they are published by the administration.</p>
                </div>
            </article>
            @endif

            <div class="notice-info">
                <i class="feather-info"></i>
                <span>If you have any questions or concerns about your exam results, please contact the examination cell or your academic advisor.</span>
            </div>
        </div>
    </div>
@endsection