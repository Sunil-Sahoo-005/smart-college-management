@extends('studentadmin_layout')
@section('title', 'My Timetable')

@section('content')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    @php
        $studentName = trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''));
        $studentName = $studentName !== '' ? $studentName : 'Student';
        $activeEnrollment = $enrollments->first();
        $days = [
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];
        $totalSlots = 0;
        foreach ($timetables as $t) {
            $totalSlots += $t->slots->count();
        }
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

        .timetable-shell {
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

        .hero-grid {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: minmax(0, 1.55fr) minmax(280px, 0.95fr);
            gap: 18px;
            align-items: stretch;
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

        .hero-chip,
        .hero-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 14px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
        }

        .hero-chip {
            background: rgba(255, 255, 255, 0.11);
            border: 1px solid rgba(255, 255, 255, 0.14);
            color: #f7f3ea;
        }

        .hero-button {
            justify-content: center;
            background: #f6e7ce;
            color: #12342b;
            border: none;
            cursor: pointer;
            transition: transform 0.16s ease, box-shadow 0.16s ease;
        }

        .hero-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 24px rgba(18, 52, 43, 0.2);
            color: #12342b;
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

        .schedule-card {
            border-radius: 22px;
            overflow: hidden;
            border: 1px solid rgba(24, 53, 45, 0.07);
            background: #fcfaf6;
        }

        .schedule-top {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
            padding: 18px 20px;
            background: linear-gradient(180deg, #f7f0e2 0%, #eef4ee 100%);
            border-bottom: 1px solid rgba(24, 53, 45, 0.08);
        }

        .schedule-title {
            font-size: 17px;
            font-weight: 800;
            color: #16342d;
            margin: 0 0 4px;
        }

        .schedule-subtitle {
            font-size: 12.5px;
            color: #687872;
            margin: 0;
        }

        .schedule-updated {
            font-size: 12px;
            font-weight: 700;
            color: #85551a;
            background: #f6e7ce;
            padding: 8px 10px;
            border-radius: 999px;
            white-space: nowrap;
        }

        .table-wrap {
            overflow-x: auto;
        }

        .timetable-grid {
            width: 100%;
            min-width: 880px;
            border-collapse: separate;
            border-spacing: 12px;
            margin: 0;
            background: transparent;
        }

        .timetable-grid th,
        .timetable-grid td {
            vertical-align: top;
        }

        .period-head {
            min-width: 130px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #687872;
            font-weight: 800;
            padding: 8px;
        }

        .day-head {
            min-width: 140px;
            padding: 0 0 8px;
        }

        .day-pill {
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            min-height: 52px;
            padding: 12px;
            background: linear-gradient(135deg, #12342b 0%, #2f6958 100%);
            color: #fffaf3;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .period-cell {
            padding: 0;
        }

        .period-card {
            border-radius: 18px;
            padding: 16px;
            background: #f1f5ee;
            border: 1px solid rgba(24, 53, 45, 0.06);
            min-height: 112px;
        }

        .period-name {
            font-size: 15px;
            font-weight: 800;
            color: #18352d;
            margin-bottom: 8px;
        }

        .period-meta {
            font-size: 12px;
            color: #667871;
            line-height: 1.55;
        }

        .slot-card {
            min-height: 112px;
            border-radius: 18px;
            padding: 16px;
            border: 1px solid rgba(24, 53, 45, 0.08);
            background: #fff;
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
        }

        .slot-card.has-class {
            background: linear-gradient(180deg, #fffaf2 0%, #edf4ee 100%);
        }

        .slot-card.has-class:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 28px rgba(24, 53, 45, 0.1);
            border-color: rgba(29, 85, 72, 0.22);
        }

        .slot-card.empty {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f9f7f2;
            color: #94a29d;
            font-size: 13px;
            font-weight: 700;
        }

        .slot-subject {
            font-size: 15px;
            font-weight: 800;
            color: #18352d;
            line-height: 1.4;
            margin-bottom: 10px;
        }

        .slot-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(18, 52, 43, 0.08);
            color: #12342b;
            font-size: 11px;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .slot-row {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: #61736c;
            line-height: 1.5;
            margin-top: 6px;
        }

        .slot-row i {
            color: #1d5548;
        }

        .empty-state {
            border-radius: 22px;
            padding: 44px 24px;
            text-align: center;
            background: linear-gradient(180deg, #faf7f1 0%, #f1f5ef 100%);
            border: 1px dashed rgba(24, 53, 45, 0.12);
        }

        .empty-state i {
            font-size: 34px;
            color: #8fa49b;
            margin-bottom: 14px;
        }

        .empty-title {
            font-size: 19px;
            font-weight: 800;
            color: #18352d;
            margin-bottom: 6px;
        }

        .empty-copy {
            margin: 0;
            color: #677973;
            font-size: 13px;
            line-height: 1.7;
        }

        @media (max-width: 1100px) {
            .hero-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .timetable-shell {
                padding: 16px;
            }

            .hero-panel,
            .panel-card,
            .schedule-card {
                border-radius: 20px;
            }

            .summary-grid {
                grid-template-columns: 1fr;
            }

            .schedule-top {
                flex-direction: column;
                align-items: flex-start;
            }

            .timetable-grid {
                min-width: 760px;
                border-spacing: 10px;
            }
        }

        @media print {

            .hero-button,
            .badge-soft {
                display: none !important;
            }

            body,
            .nxl-content {
                background: #ffffff !important;
            }

            .hero-panel,
            .panel-card,
            .schedule-card,
            .slot-card,
            .period-card {
                box-shadow: none !important;
            }
        }
    </style>

    <div class="nxl-content">
        <div class="timetable-shell">
            <section class="hero-panel">
                <div class="hero-grid">
                    <div>
                        <span class="eyebrow">Student Timetable</span>
                        <h1 class="hero-title">Your weekly class plan, {{ $student->first_name ?? 'Student' }}.</h1>
                        <p class="hero-copy">
                            View your timetable in a cleaner format with clear subject, faculty, and room details so you can
                            plan each day faster.
                        </p>

                        <div class="hero-chips">
                            <span class="hero-chip">Student: {{ $studentName }}</span>
                            <span class="hero-chip">Program:
                                {{ $activeEnrollment?->stream?->name ?? ($student->stream_name ?? 'Not assigned') }}</span>
                            <span class="hero-chip">Class:
                                {{ $activeEnrollment?->class?->name ?? ($student->class_name ?? 'Not assigned') }}</span>
                            <span class="hero-chip">Section: {{ $activeEnrollment?->section?->name ?? 'Not assigned' }}</span>
                            <button onclick="window.print()" class="hero-button" type="button">
                                <i class="feather-printer"></i>
                                <span>Print Timetable</span>
                            </button>
                        </div>
                    </div>

                    <div class="summary-panel">
                        <div class="summary-grid">
                            <div class="summary-item">
                                <div class="summary-label">Academic Year</div>
                                <div class="summary-value">
                                    {{ $activeEnrollment?->academicYear->name ?? ($student->academic_name ?? 'Current year') }}
                                </div>
                            </div>
                            <div class="summary-item">
                                <div class="summary-label">Active Timetables</div>
                                <div class="summary-value">{{ $timetables->count() }}</div>
                            </div>
                            <div class="summary-item">
                                <div class="summary-label">Enrollment Status</div>
                                <div class="summary-value">{{ ucfirst($activeEnrollment?->status ?? 'N/A') }}</div>
                            </div>
                            <div class="summary-item">
                                <div class="summary-label">Registration Number</div>
                                <div class="summary-value">{{ $student->admission_number ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="panel-card">
                <div class="panel-head">
                    <div>
                        <div class="panel-kicker">Weekly Planner</div>
                        <h2 class="panel-title">My Timetable</h2>
                        <p class="panel-copy">A student-friendly schedule view with all subjects grouped by period and day.
                        </p>
                    </div>
                    <span class="badge-soft">{{ $totalSlots }} total slots</span>
                </div>

                @forelse($timetables as $timetable)
                    <div class="schedule-card {{ !$loop->last ? 'mb-4' : '' }}">
                        <div class="schedule-top">
                            <div>
                                <h3 class="schedule-title">
                                    {{ $timetable->name ?? ($timetable->class->name ?? 'Timetable') }}</h3>
                                <p class="schedule-subtitle">
                                    {{ $timetable->term->name ?? 'Current Term' }}
                                    @if ($activeEnrollment && $activeEnrollment->section)
                                        · Section {{ $activeEnrollment->section->name }}
                                    @endif
                                </p>
                            </div>
                            <div class="schedule-updated">
                                Updated {{ optional($timetable->updated_at)->format('d M Y') }}
                            </div>
                        </div>

                        <div class="table-wrap">
                            <table class="timetable-grid">
                                <thead>
                                    <tr>
                                        <th class="period-head">Period</th>
                                        @foreach ($days as $day)
                                            <th class="day-head">
                                                <div class="day-pill">{{ $day }}</div>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($timetable->slots->groupBy('period_id') as $periodId => $slots)
                                        <tr>
                                            <td class="period-cell">
                                                <div class="period-card">
                                                    <div class="period-name">
                                                        {{ $slots->first()->period->name ?? 'Period ' . $periodId }}
                                                    </div>
                                                    <div class="period-meta">
                                                        {{ $slots->count() }} scheduled
                                                        day{{ $slots->count() === 1 ? '' : 's' }} this week
                                                    </div>
                                                </div>
                                            </td>

                                            @foreach ($days as $dayOfWeek => $dayName)
                                                @php
                                                    $slot = $slots->where('day_of_week', $dayOfWeek)->first();
                                                @endphp
                                                <td class="slot-cell">
                                                    @if ($slot)
                                                        <div class="slot-card has-class">
                                                            <div class="slot-subject">
                                                                {{ $slot->subject->name ?? 'Subject' }}</div>

                                                            @if ($slot->staff)
                                                                <div class="slot-row">
                                                                    <i class="feather-user"></i>
                                                                    <span>{{ trim(($slot->staff->first_name ?? '') . ' ' . ($slot->staff->last_name ?? '')) ?: $slot->staff->name ?? 'Faculty' }}</span>
                                                                </div>
                                                            @endif

                                                            @if ($slot->room)
                                                                <div class="slot-row">
                                                                    <i class="feather-map-pin"></i>
                                                                    <span>{{ $slot->room->name }}</span>
                                                                </div>
                                                            @endif

                                                            <div class="slot-row">
                                                                <i class="feather-calendar"></i>
                                                                <span>{{ $dayName }}</span>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="slot-card empty">No Class</div>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="feather-calendar"></i>
                        <div class="empty-title">No timetable assigned yet</div>
                        <p class="empty-copy">Your schedule has not been published for the current enrollment. Please contact the academic office for more information.</p>
                    </div>
                @endforelse
            </section>
        </div>
    </div>
@endsection
