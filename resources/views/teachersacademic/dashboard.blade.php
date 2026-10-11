@extends('teacher_layout')
@section('content')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    @php
        $teacherName = trim(($teachers->first_name ?? '') . ' ' . ($teachers->last_name ?? ''));
        $teacherName = $teacherName !== '' ? $teacherName : 'Teacher';
        $todayLabel = $today->format('l, d M Y');
        $weekRange = $startOfWeek->format('d M') . ' - ' . $endOfWeek->format('d M Y');
        $regularCountToday = $todayRegularSlots->count();
        $mergedCountToday = $todayMergedSchedules->count();
        $todayLoad = $regularCountToday + $mergedCountToday;
        $classCohorts = $staffSlots
            ->map(function ($slot) {
                $streamName = optional(optional($slot->timetable)->stream)->name;
                $className = optional(optional($slot->timetable)->class)->name;
                $sectionName = optional(optional($slot->timetable)->section)->name;

                return collect([$streamName, $className, $sectionName ? 'Section ' . $sectionName : null])
                    ->filter()
                    ->implode(' ');
            })
            ->filter()
            ->unique()
            ->values();
        $subjectNames = $staffSlots->map(fn($slot) => optional($slot->subject)->name)->filter()->unique()->values();
        $weeklySchedule = $staffSlots->sortBy([['day_of_week', 'asc'], ['period_id', 'asc']])->groupBy('day_of_week');
        $weekdayNameMap = [
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday',
        ];
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

        .teacher-shell {
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

        .summary-panel,
        .today-panel {
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

        .summary-label,
        .today-label {
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

        .today-value {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.04em;
            margin: 8px 0 6px;
            color: #fffaf3;
        }

        .today-copy {
            font-size: 13px;
            line-height: 1.6;
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

        .quick-grid,
        .profile-grid,
        .schedule-list,
        .info-grid,
        .subject-pill-list,
        .weekly-board,
        .weekly-slots {
            display: grid;
            gap: 12px;
        }

        .quick-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .hr-quick-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .quick-link,
        .profile-card,
        .schedule-card,
        .note-card {
            border-radius: 18px;
            padding: 16px;
            background: linear-gradient(180deg, #faf7f1 0%, #edf4ee 100%);
            border: 1px solid rgba(24, 53, 45, 0.07);
        }

        .quick-link {
            display: block;
            text-decoration: none;
            color: inherit;
            transition: transform 0.16s ease, box-shadow 0.16s ease;
        }

        .quick-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 30px rgba(24, 53, 45, 0.09);
            color: inherit;
        }

        .quick-icon {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #12342b;
            color: #f7f3ea;
            font-size: 18px;
            margin-bottom: 14px;
        }

        .quick-title,
        .profile-value,
        .schedule-title,
        .note-title {
            font-size: 15px;
            font-weight: 700;
            color: #18352d;
        }

        .quick-copy,
        .schedule-copy,
        .note-copy {
            margin: 6px 0 0;
            font-size: 13px;
            color: #677973;
            line-height: 1.65;
        }

        .profile-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .info-grid,
        .weekly-board {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .profile-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #7b8b85;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .schedule-list {
            grid-template-columns: 1fr;
        }

        .schedule-card {
            background: #fbf8f2;
        }

        .info-card,
        .weekly-day,
        .weekly-slot {
            border-radius: 18px;
            padding: 16px;
            background: linear-gradient(180deg, #faf7f1 0%, #edf4ee 100%);
            border: 1px solid rgba(24, 53, 45, 0.07);
        }

        .info-label,
        .weekly-day-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #7b8b85;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .info-value,
        .weekly-slot-title {
            font-size: 15px;
            font-weight: 700;
            color: #18352d;
        }

        .info-copy,
        .weekly-slot-copy {
            margin: 6px 0 0;
            font-size: 13px;
            color: #677973;
            line-height: 1.65;
        }

        .subject-pill-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .subject-pill {
            display: inline-flex;
            align-items: center;
            padding: 9px 12px;
            border-radius: 999px;
            background: #f5efe2;
            color: #85551a;
            font-size: 12px;
            font-weight: 700;
        }

        .schedule-topline {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: flex-start;
            margin-bottom: 8px;
        }

        .schedule-meta {
            font-size: 12px;
            color: #687872;
            line-height: 1.55;
        }

        .schedule-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
        }

        .schedule-badge.regular {
            background: #eaf5ee;
            color: #1f5d46;
        }

        .schedule-badge.merged {
            background: #fff3dd;
            color: #8a5f17;
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

        @media (max-width: 1100px) {

            .hero-grid,
            .stats-grid,
            .quick-grid,
            .hr-quick-grid,
            .profile-grid,
            .info-grid,
            .weekly-board {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            .teacher-shell {
                padding: 16px;
            }

            .hero-panel,
            .panel-card,
            .stat-card {
                border-radius: 20px;
            }

            .hero-grid,
            .summary-grid,
            .stats-grid,
            .quick-grid,
            .profile-grid,
            .info-grid,
            .weekly-board {
                grid-template-columns: 1fr;
            }

            .panel-head,
            .schedule-topline {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>

    <div class="nxl-content">
        <div class="teacher-shell">
            <section class="hero-panel">
                <div class="hero-grid">
                    <div>
                        <span class="eyebrow">Teacher Portal</span>
                        <h1 class="hero-title">Teaching overview for {{ $teacherName }}.</h1>
                        <p class="hero-copy">
                            This dashboard brings together your teaching profile, active classes, today's schedule, weekly
                            teaching load, and merged class activity in one more professional teacher view.
                        </p>

                        <div class="hero-chips">
                            <span class="hero-chip">Teacher: {{ $teacherName }}</span>
                            <span class="hero-chip">Designation: {{ optional($teachers->designation)->name ?? 'Not assigned' }}</span>
                            <span class="hero-chip">Department: {{ optional($teachers->department)->name ?? 'Not assigned' }}</span>
                            <span class="hero-chip">Week: {{ $weekRange }}</span>
                            <span class="hero-chip">Today: {{ $todayLabel }}</span>
                        </div>
                    </div>

                    <div class="hero-side">
                        <div class="summary-panel">
                            <div class="summary-grid">
                                <div class="summary-item">
                                    <div class="summary-label">Active Timetables</div>
                                    <div class="summary-value">{{ $activeTimetables->count() }}</div>
                                </div>
                                <div class="summary-item">
                                    <div class="summary-label">Subjects</div>
                                    <div class="summary-value">{{ $subjectCount }}</div>
                                </div>
                                <div class="summary-item">
                                    <div class="summary-label">Today's Regular Classes</div>
                                    <div class="summary-value">{{ $regularCountToday }}</div>
                                </div>
                                <div class="summary-item">
                                    <div class="summary-label">Merged This Week</div>
                                    <div class="summary-value">{{ $mergedSchedules->count() }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="today-panel">
                            <div class="today-label">Teaching Reach</div>
                            <div class="today-value">{{ $studentReach }}</div>
                            <div class="today-copy">Estimated active students across your currently assigned class groups,
                                with {{ $todayLoad }} teaching sessions planned for today.</div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="stats-grid">
                <article class="stat-card">
                    <div class="stat-top">
                        <div class="stat-label">Class Load</div>
                        <div class="stat-icon"><i class="feather-layers"></i></div>
                    </div>
                    <div class="stat-value">{{ $activeTimetables->count() }}</div>
                    <div class="stat-copy">Distinct timetables currently assigned to this teacher account.</div>
                </article>

                <article class="stat-card">
                    <div class="stat-top">
                        <div class="stat-label">Weekly Slots</div>
                        <div class="stat-icon"><i class="feather-calendar"></i></div>
                    </div>
                    <div class="stat-value">{{ $staffSlots->count() }}</div>
                    <div class="stat-copy">Total regular timetable slots available in the current teaching load.</div>
                </article>

                <article class="stat-card">
                    <div class="stat-top">
                        <div class="stat-label">Today's Load</div>
                        <div class="stat-icon"><i class="feather-sun"></i></div>
                    </div>
                    <div class="stat-value">{{ $todayLoad }}</div>
                    <div class="stat-copy">Combined count of regular and merged classes scheduled for today.</div>
                </article>

                <article class="stat-card">
                    <div class="stat-top">
                        <div class="stat-label">Class Groups</div>
                        <div class="stat-icon"><i class="feather-git-merge"></i></div>
                    </div>
                    <div class="stat-value">{{ $classCohorts->count() }}</div>
                    <div class="stat-copy">Distinct stream, class, and section groups currently covered by your timetable
                        assignments.</div>
                </article>
            </section>

            <section class="panel-card">
                <div class="panel-head">
                    <div>
                        <div class="panel-kicker">Quick Actions</div>
                        <h2 class="panel-title">Teaching Tools</h2>
                        <p class="panel-copy">Jump directly to the teacher areas that are used most often during the day.
                        </p>
                    </div>
                </div>

                <div class="quick-grid">
                    <a href="{{ route('teacher.teachersacademic.show') }}" class="quick-link">
                        <span class="quick-icon"><i class="feather-calendar"></i></span>
                        <div class="quick-title">Full Timetable</div>
                        <div class="quick-copy">Open your full teaching timetable with assigned classes and sections.</div>
                    </a>

                    <a href="{{ route('teacher.exams.index') }}" class="quick-link">
                        <span class="quick-icon"><i class="feather-file-text"></i></span>
                        <div class="quick-title">Manage Exams</div>
                        <div class="quick-copy">Create, update, and review exams assigned under the teacher portal.</div>
                    </a>

                    <a href="{{ route('teacher.exam_marks.index') }}" class="quick-link">
                        <span class="quick-icon"><i class="feather-edit-3"></i></span>
                        <div class="quick-title">Exam Marks</div>
                        <div class="quick-copy">View and manage marks entry records from the teacher module.</div>
                    </a>
                </div>
            </section>

            {{-- Self-Service HR --}}
            <section class="panel-card">
                <div class="panel-head">
                    <div>
                        <div class="panel-kicker">Staff Self-Service</div>
                        <h2 class="panel-title">My HR Portal</h2>
                        <p class="panel-copy">Manage your attendance, apply for leave, and view salary payslips directly from the portal.</p>
                    </div>
                </div>

                <div class="hr-quick-grid">
                    <a href="{{ route('teacher.my_attendance.index') }}" class="quick-link">
                        <span class="quick-icon" style="background:#1d5548;"><i class="feather-clock"></i></span>
                        <div class="quick-title">My Attendance</div>
                        <div class="quick-copy">Punch in or out for today and view your monthly attendance history and stats.</div>
                    </a>

                    <a href="{{ route('teacher.my_leave.index') }}" class="quick-link">
                        <span class="quick-icon" style="background:#1d5548;"><i class="feather-umbrella"></i></span>
                        <div class="quick-title">Leave Requests</div>
                        <div class="quick-copy">Apply for leave and check the status of your pending and past leave applications.</div>
                    </a>

                    <a href="{{ route('teacher.my_salary.index') }}" class="quick-link">
                        <span class="quick-icon" style="background:#1d5548;"><i class="feather-dollar-sign"></i></span>
                        <div class="quick-title">My Salary</div>
                        <div class="quick-copy">View and print your published monthly payslips with full earnings breakdown.</div>
                    </a>
                </div>
            </section>

            <section class="panel-card">
                <div class="panel-head">
                    <div>
                        <div class="panel-kicker">Teacher Profile</div>
                        <h2 class="panel-title">Professional Information</h2>
                        <p class="panel-copy">A cleaner summary of personal and institutional details for this teacher
                            account.</p>
                    </div>
                    <span class="badge-soft">{{ optional($teachers->designation)->name ?? 'Teacher' }}</span>
                </div>

                <div class="profile-grid">
                    <div class="profile-card">
                        <div class="profile-label">Full Name</div>
                        <div class="profile-value">{{ $teacherName }}</div>
                    </div>
                    <div class="profile-card">
                        <div class="profile-label">Email</div>
                        <div class="profile-value">{{ $teachers->email ?? 'Not available' }}</div>
                    </div>
                    <div class="profile-card">
                        <div class="profile-label">Phone</div>
                        <div class="profile-value">{{ $teachers->phone ?? 'Not available' }}</div>
                    </div>
                    <div class="profile-card">
                        <div class="profile-label">Gender</div>
                        <div class="profile-value">{{ $teachers->gender ?? 'Not available' }}</div>
                    </div>
                    <div class="profile-card">
                        <div class="profile-label">Date of Birth</div>
                        <div class="profile-value">{{ $teachers->dob ?? 'Not available' }}</div>
                    </div>
                    <div class="profile-card">
                        <div class="profile-label">Blood Group</div>
                        <div class="profile-value">{{ $teachers->blood_group ?? 'Not available' }}</div>
                    </div>
                    <div class="profile-card">
                        <div class="profile-label">Joining Date</div>
                        <div class="profile-value">{{ $teachers->joining_date ?? 'Not available' }}</div>
                    </div>
                    <div class="profile-card">
                        <div class="profile-label">Department</div>
                        <div class="profile-value">{{ optional($teachers->department)->name ?? 'Not assigned' }}</div>
                    </div>
                    <div class="profile-card">
                        <div class="profile-label">Religion</div>
                        <div class="profile-value">{{ $teachers->religion ?? 'Not available' }}</div>
                    </div>
                </div>
            </section>

            <section class="panel-card">
                <div class="panel-head">
                    <div>
                        <div class="panel-kicker">Teaching Coverage</div>
                        <h2 class="panel-title">Class Responsibility</h2>
                        <p class="panel-copy">A simple overview of which groups and subjects are currently part of your
                            active teaching plan.</p>
                    </div>
                    <span class="badge-soft">{{ $weekRange }}</span>
                </div>

                <div class="info-grid">
                    <div class="info-card">
                        <div class="info-label">Assigned Class Groups</div>
                        <div class="info-value">{{ $classCohorts->count() }} groups</div>
                        <p class="info-copy">
                            @if ($classCohorts->isNotEmpty())
                                {{ $classCohorts->take(8)->implode(', ') }}@if ($classCohorts->count() > 8)
                                    , and {{ $classCohorts->count() - 8 }} more.
                                @endif
                            @else
                                No active class groups are assigned yet.
                            @endif
                        </p>
                    </div>

                    <div class="info-card">
                        <div class="info-label">Subject Rotation</div>
                        <div class="info-value">{{ $subjectNames->count() }} subjects</div>
                        @if ($subjectNames->isNotEmpty())
                            <div class="subject-pill-list" style="margin-top: 10px;">
                                @foreach ($subjectNames->take(8) as $subjectName)
                                    <span class="subject-pill">{{ $subjectName }}</span>
                                @endforeach
                            </div>
                        @else
                            <p class="info-copy">No timetable subjects are linked to this teacher yet.</p>
                        @endif
                    </div>
                </div>
            </section>

            <section class="panel-card">
                <div class="panel-head">
                    <div>
                        <div class="panel-kicker">Weekly Teaching Board</div>
                        <h2 class="panel-title">Teaching Rhythm</h2>
                        <p class="panel-copy">A quick weekly snapshot helps identify heavier teaching days without opening
                            the full timetable page.</p>
                    </div>
                    <span class="badge-soft">{{ $staffSlots->count() }} weekly slots</span>
                </div>

                @if ($weeklySchedule->isNotEmpty())
                    <div class="weekly-board">
                        @foreach ($weeklySchedule as $dayNumber => $slots)
                            <div class="weekly-day">
                                <div class="weekly-day-label">{{ $weekdayNameMap[$dayNumber] ?? 'Day' }}</div>
                                <div class="info-value">{{ $slots->count() }} class
                                    {{ $slots->count() === 1 ? 'slot' : 'slots' }}</div>
                                <div class="weekly-slots" style="margin-top: 12px;">
                                    @foreach ($slots->take(4) as $slot)
                                        <div class="weekly-slot">
                                            <div class="weekly-slot-title">{{ $slot->subject->name ?? 'Subject' }}</div>
                                            <div class="weekly-slot-copy">
                                                {{ $slot->period->name ?? 'Period ' . $slot->period_id }}
                                                · {{ $slot->timetable->class->name ?? 'Class' }}
                                                @if ($slot->timetable->section)
                                                    · Section {{ $slot->timetable->section->name }}
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                    @if ($slots->count() > 4)
                                        <p class="info-copy">+ {{ $slots->count() - 4 }} more slots on this day</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="feather-grid"></i>
                        <div class="empty-title">No weekly schedule available</div>
                        <p class="panel-copy">Once timetable slots are assigned, your weekly teaching board will appear
                            here automatically.</p>
                    </div>
                @endif
            </section>

            <section class="panel-card">
                <div class="panel-head">
                    <div>
                        <div class="panel-kicker">Today’s Schedule</div>
                        <h2 class="panel-title">Regular and Merged Classes</h2>
                        <p class="panel-copy">A compact view of what this teacher needs to handle today.</p>
                    </div>
                    <span class="badge-soft">{{ $todayLabel }}</span>
                </div>

                <div class="schedule-list">
                    @forelse($todayRegularSlots as $slot)
                        <div class="schedule-card">
                            <div class="schedule-topline">
                                <div>
                                    <div class="schedule-title">{{ $slot->subject->name ?? 'Subject' }}</div>
                                    <div class="schedule-meta">
                                        {{ $slot->timetable->stream->name ?? 'Stream' }}
                                        {{ $slot->timetable->class->name ?? 'Class' }}
                                        @if ($slot->timetable->section)
                                            · Section {{ $slot->timetable->section->name }}
                                        @endif
                                    </div>
                                </div>
                                <span
                                    class="schedule-badge regular">{{ $slot->period->name ?? 'Period ' . $slot->period_id }}</span>
                            </div>
                            <p class="schedule-copy">
                                @if ($slot->room)
                                    Room: {{ $slot->room->name }}.
                                @endif
                                {{ $slot->timetable->term->name ?? 'Current term' }}.
                            </p>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="feather-calendar"></i>
                            <div class="empty-title">No regular classes today</div>
                            <p class="panel-copy">There are no regular timetable slots assigned for
                                {{ $today->format('l') }}.</p>
                        </div>
                    @endforelse

                    @foreach ($todayMergedSchedules as $schedule)
                        <div class="schedule-card">
                            <div class="schedule-topline">
                                <div>
                                    <div class="schedule-title">{{ $schedule->subject->name ?? 'Merged Class' }}</div>
                                    <div class="schedule-meta">
                                        Special merged schedule · {{ optional($schedule->schedule_date)->format('d M Y') }}
                                    </div>
                                </div>
                                <span
                                    class="schedule-badge merged">{{ $schedule->period->name ?? 'Period ' . $schedule->period_id }}</span>
                            </div>
                            <p class="schedule-copy">
                                @if ($schedule->room)
                                    Room: {{ $schedule->room->name }}.
                                @endif
                                This merged class is scheduled specially for today.
                            </p>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
    </div>

@endsection
