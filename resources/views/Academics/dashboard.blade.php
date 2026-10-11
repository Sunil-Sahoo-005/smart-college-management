@extends('admin_layout')

@section('content')
    @php
        $tone = is_null($attendanceRate)
            ? ['label' => 'No attendance yet', 'class' => 'secondary']
            : ($attendanceRate >= 85
                ? ['label' => 'Strong attendance', 'class' => 'success']
                : ($attendanceRate >= 70
                    ? ['label' => 'Attendance slipping', 'class' => 'warning']
                    : ['label' => 'Attendance critical', 'class' => 'danger']));
    @endphp

    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header academic-dashboard-header">
                    <div>
                        <h2 class="fw-bold mb-1">Dashboard</h2>
                        <!-- <p class="text-muted mb-0">Track student load, admissions, attendance, and exam activity from one view.</p> -->
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <!-- <span class="badge bg-soft-{{ $tone['class'] }} text-{{ $tone['class'] }}">{{ $tone['label'] }}</span> -->
                        <!-- <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
                            <i class="feather-user-plus me-2"></i>Create Student
                        </a>
                        <a href="{{ route('admin.student_attendance.date') }}" class="btn btn-light-brand">
                            <i class="feather-check-circle me-2"></i>Attendance
                        </a> -->
                    </div>
                </div>

                <div class="content-area-body academic-dashboard-body">
                    <!-- Hero Section (unchanged) -->
                    <div class="card border-0 shadow-sm overflow-hidden mb-4 academy-hero">
                        <div class="card-body p-4 p-lg-5">
                            <div class="row g-4 align-items-center">
                                <div class="col-xl-8">
                                    <span class="academy-hero-label">Today at a glance</span>
                                    <h3 class="academy-hero-title">A cleaner command center for academics.</h3>
                                    <p class="academy-hero-copy">See how enrollment, attendance, and assessments are moving without jumping between modules.</p>
                                    <div class="academy-hero-stats">
                                        <div class="academy-hero-stat">
                                            <span>Students</span>
                                            <strong>{{ number_format($totalStudents ?? 0) }}</strong>
                                            <small>{{ number_format($activeEnrollments ?? 0) }} active enrollments</small>
                                        </div>
                                        <div class="academy-hero-stat">
                                            <span>Attendance Today</span>
                                            <strong>{{ is_null($attendanceRate) ? 'N/A' : $attendanceRate . '%' }}</strong>
                                            <small>{{ number_format($presentToday ?? 0) }} present / {{ number_format($absentToday ?? 0) }} absent</small>
                                        </div>
                                        <div class="academy-hero-stat">
                                            <span>Upcoming Exams</span>
                                            <strong>{{ number_format($upcomingExams ?? 0) }}</strong>
                                            <small>{{ number_format($examsThisMonth ?? 0) }} this month</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="academy-side-panel">
                                        <div class="academy-side-item">
                                            <span>New admissions this month</span>
                                            <strong>{{ number_format($newAdmissionsThisMonth ?? 0) }}</strong>
                                        </div>
                                        <div class="academy-side-item">
                                            <span>Marks recorded</span>
                                            <strong>{{ number_format($marksEntered ?? 0) }}</strong>
                                        </div>
                                        <div class="academy-side-item">
                                            <span>Attendance rows today</span>
                                            <strong>{{ number_format($todayAttendanceRecords ?? 0) }}</strong>
                                        </div>
                                        <div class="academy-side-item">
                                            <span>Branches / semesters</span>
                                            <strong>{{ number_format($totalBranches ?? 0) }} / {{ number_format($totalClasses ?? 0) }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4 Stat Cards (unchanged) -->
                    <div class="row g-4 mb-4">
                        <div class="col-xl-3 col-md-6">
                            <div class="card border-0 shadow-sm h-100 stat-card">
                                <div class="card-body">
                                    <div class="stat-icon bg-primary-subtle text-primary"><i class="feather-users"></i></div>
                                    <span>Total Students</span>
                                    <h3>{{ number_format($totalStudents ?? 0) }}</h3>
                                    <p>Complete student base.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card border-0 shadow-sm h-100 stat-card">
                                <div class="card-body">
                                    <div class="stat-icon bg-success-subtle text-success"><i class="feather-user-check"></i></div>
                                    <span>Active Enrollments</span>
                                    <h3>{{ number_format($activeEnrollments ?? 0) }}</h3>
                                    <p>Students in active sections.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card border-0 shadow-sm h-100 stat-card">
                                <div class="card-body">
                                    <div class="stat-icon bg-warning-subtle text-warning"><i class="feather-book-open"></i></div>
                                    <span>Branches & Semesters</span>
                                    <h3>{{ number_format($totalBranches ?? 0) }} / {{ number_format($totalClasses ?? 0) }}</h3>
                                    <p>Current academic setup.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card border-0 shadow-sm h-100 stat-card">
                                <div class="card-body">
                                    <div class="stat-icon bg-info-subtle text-info"><i class="feather-award"></i></div>
                                    <span>Marks Recorded</span>
                                    <h3>{{ number_format($marksEntered ?? 0) }}</h3>
                                    <p>Assessment entries captured.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Admissions Trend + Branch Strength (unchanged) -->
                    <div class="row g-4 mb-4">
                        <div class="col-xl-8">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-transparent border-0 pt-4 px-4">
                                    <h5 class="mb-1">Admissions Trend</h5>
                                    <p class="text-muted mb-0">Last six completed months of admissions.</p>
                                </div>
                                <div class="card-body pt-2 px-3 px-md-4">
                                    <div id="academicAdmissionsChart" style="height: 320px;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-transparent border-0 pt-4 px-4">
                                    <h5 class="mb-1">Branch Strength</h5>
                                    <p class="text-muted mb-0">Active students by branch.</p>
                                </div>
                                <div class="card-body pt-2 px-4">
                                    @forelse ($topBranches ?? [] as $branch)
                                        @php
                                            $maxBranchTotal = max(1, collect($topBranches)->max('total'));
                                            $width = round(($branch->total / $maxBranchTotal) * 100);
                                        @endphp
                                        <div class="branch-item">
                                            <div class="d-flex justify-content-between mb-2">
                                                <div>
                                                    <h6 class="mb-0">{{ $branch->stream?->name ?? 'Unassigned' }}</h6>
                                                    <small class="text-muted">Active students</small>
                                                </div>
                                                <strong>{{ number_format($branch->total) }}</strong>
                                            </div>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar" role="progressbar" style="width: {{ $width }}%"></div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="empty-mini">No branch data yet.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attendance, Upcoming Exams, Quick Actions (unchanged) -->
                    <div class="row g-4 mb-4">
                        <div class="col-xl-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-transparent border-0 pt-4 px-4">
                                    <h5 class="mb-1">Attendance Snapshot</h5>
                                    <p class="text-muted mb-0">Daily attendance capture.</p>
                                </div>
                                <div class="card-body px-4">
                                    <div class="attendance-boxes">
                                        <div class="attendance-box success">
                                            <span>Present</span>
                                            <strong>{{ number_format($presentToday ?? 0) }}</strong>
                                        </div>
                                        <div class="attendance-box danger">
                                            <span>Absent</span>
                                            <strong>{{ number_format($absentToday ?? 0) }}</strong>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Attendance rate</span>
                                            <span class="fw-semibold">{{ is_null($attendanceRate) ? 'N/A' : $attendanceRate . '%' }}</span>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar bg-{{ $tone['class'] }}" role="progressbar" style="width: {{ is_null($attendanceRate) ? 0 : min(100, $attendanceRate) }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-transparent border-0 pt-4 px-4">
                                    <h5 class="mb-1">Upcoming Exams</h5>
                                    <p class="text-muted mb-0">Next scheduled assessments.</p>
                                </div>
                                <div class="card-body px-4 pt-2">
                                    @forelse ($upcomingExamList ?? [] as $exam)
                                        <div class="timeline-item">
                                            <span class="timeline-dot"></span>
                                            <div>
                                                <h6 class="mb-1">{{ $exam->name }}</h6>
                                                <p class="mb-1 text-muted">{{ $exam->subject?->name ?? ($exam->examType?->name ?? 'General Exam') }}</p>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($exam->exam_date)->format('d M, Y') }}</small>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="empty-mini">No upcoming exams scheduled.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-transparent border-0 pt-4 px-4">
                                    <h5 class="mb-1">Quick Actions</h5>
                                    <p class="text-muted mb-0">Move faster through academic work.</p>
                                </div>
                                <div class="card-body px-4">
                                    <div class="action-grid">
                                        <a href="{{ route('admin.students.index') }}" class="action-card"><i class="feather-users text-primary"></i><span>Students</span></a>
                                        <a href="{{ route('admin.student_attendance.date') }}" class="action-card"><i class="feather-check-circle text-success"></i><span>Attendance</span></a>
                                        <a href="{{ route('admin.timetables.index') }}" class="action-card"><i class="feather-clock text-info"></i><span>Timetable</span></a>
                                        <a href="{{ route('admin.exams.index') }}" class="action-card"><i class="feather-file-text text-warning"></i><span>Exams</span></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Admissions & Recent Marks (unchanged) -->
                    <div class="row g-4">
                        <div class="col-xl-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-transparent border-0 pt-4 px-4">
                                    <h5 class="mb-1">Recent Admissions</h5>
                                    <p class="text-muted mb-0">Latest students entering academic flow.</p>
                                </div>
                                <div class="card-body px-0 pt-2">
                                    <div class="table-responsive">
                                        <table class="table table-modern mb-0">
                                            <thead>
                                                <tr><th>Student</th><th>Branch</th><th>Semester / Section</th><th>Admission</th></tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($recentAdmissions ?? [] as $enrollment)
                                                    <tr>
                                                        <td>{{ $enrollment->student?->first_name }} {{ $enrollment->student?->last_name }}</td>
                                                        <td>{{ $enrollment->stream?->name ?? 'Unassigned' }}</td>
                                                        <td>{{ $enrollment->class?->name ?? 'N/A' }}{{ $enrollment->section ? ' / ' . $enrollment->section->name : '' }}</td>
                                                        <td>{{ optional($enrollment->dateof_admission)->format('d M, Y') ?? 'N/A' }}</td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="4" class="text-center text-muted py-4">No recent admissions found.</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-transparent border-0 pt-4 px-4">
                                    <h5 class="mb-1">Recent Marks</h5>
                                    <p class="text-muted mb-0">Latest assessment updates.</p>
                                </div>
                                <div class="card-body px-0 pt-2">
                                    <div class="table-responsive">
                                        <table class="table table-modern mb-0">
                                            <thead>
                                                <tr><th>Student</th><th>Exam</th><th>Marks</th><th>Grade</th><th>Date</th></tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($recentMarks ?? [] as $mark)
                                                    <tr>
                                                        <td>{{ $mark->student?->first_name }} {{ $mark->student?->last_name }}</td>
                                                        <td>{{ $mark->exam?->name ?? 'N/A' }}</td>
                                                        <td>{{ number_format($mark->marks_obtained, 2) }}</td>
                                                        <td>{{ $mark->grade_letter ?? 'N/A' }}</td>
                                                        <td>{{ optional($mark->created_at)->format('d M, Y') }}</td>
                                                    </tr>
                                                @empty
                                                    <tr><td colspan="5" class="text-center text-muted py-4">No recent marks found.</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ========== NEW WIDGETS SECTION (ADDED) ========== -->

                    <!-- Row 1: Gender Ratio + Room Utilization -->
                    <div class="row g-4 mt-4">
                        <div class="col-xl-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-transparent border-0 pt-4 px-4">
                                    <h5 class="mb-1">Student Gender Ratio</h5>
                                    <p class="text-muted mb-0">Active students by gender</p>
                                </div>
                                <div class="card-body">
                                    <div id="genderRatioChart" style="height: 280px;"></div>
                                    <div class="mt-3 text-center">
                                        <span class="badge bg-primary">Male {{ number_format($maleCount ?? 0) }}</span>
                                        <span class="badge bg-danger">Female {{ number_format($femaleCount ?? 0) }}</span>
                                        <span class="badge bg-secondary">Other {{ number_format($otherCount ?? 0) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-transparent border-0 pt-4 px-4">
                                    <h5 class="mb-1">Room Utilization Today</h5>
                                    <p class="text-muted mb-0">Rooms occupied vs available</p>
                                </div>
                                <div class="card-body d-flex flex-column justify-content-center">
                                    <div class="text-center mb-3">
                                        <h1 class="display-4 fw-bold">{{ $roomUtilizationPercent ?? 0 }}%</h1>
                                        <p class="text-muted">utilization rate</p>
                                    </div>
                                    <div class="progress" style="height: 12px;">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ min(100, $roomUtilizationPercent ?? 0) }}%"></div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-3">
                                        <div><strong>{{ number_format($usedRoomsToday ?? 0) }}</strong> occupied</div>
                                        <div><strong>{{ number_format(($totalRooms ?? 0) - ($usedRoomsToday ?? 0)) }}</strong> free</div>
                                    </div>
                                    <div class="mt-3 text-muted small">
                                        <i class="feather-info"></i> Based on today's timetable
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Fee Trend Chart + Upcoming Holidays -->
                    <div class="row g-4 mt-4">
                        <div class="col-xl-8">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-transparent border-0 pt-4 px-4">
                                    <h5 class="mb-1">Monthly Fee Collection Trend</h5>
                                    <p class="text-muted mb-0">Last 6 months fee income</p>
                                </div>
                                <div class="card-body pt-2 px-3">
                                    <div id="feeTrendChart" style="height: 300px;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-transparent border-0 pt-4 px-4">
                                    <h5 class="mb-1">Upcoming Holidays</h5>
                                    <p class="text-muted mb-0">Next 30 days</p>
                                </div>
                                <div class="card-body px-4">
                                    @forelse ($upcomingHolidays ?? [] as $holiday)
                                        <div class="timeline-item">
                                            <span class="timeline-dot bg-warning"></span>
                                            <div>
                                                <h6 class="mb-1">{{ $holiday->title }}</h6>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($holiday->date)->format('d M Y') }}</small>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="empty-mini">No holidays in next 30 days.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 3: Recent Fee Payments + Student-Teacher Ratio -->
                    <div class="row g-4 mt-4">
                        <div class="col-xl-7">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-transparent border-0 pt-4 px-4">
                                    <h5 class="mb-1">Recent Fee Payments</h5>
                                    <p class="text-muted mb-0">Latest fee transactions</p>
                                </div>
                                <div class="card-body px-0 pt-2">
                                    <div class="table-responsive">
                                        <table class="table table-modern mb-0">
                                            <thead>
                                                <td><th>Student</th><th>Amount</th><th>Date</th><th>Receipt</th></tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($recentFeePayments ?? [] as $payment)
                                                    <tr>
                                                        <td>{{ $payment['student_name'] }}</td>
                                                        <td><strong>{{ number_format($payment['amount']) }}</strong></td>
                                                        <td>{{ $payment['date'] }}</td>
                                                        <td>{{ $payment['receipt'] }}</td>
                                                    </tr>
                                                @empty
                                                    <td><td colspan="4" class="text-center text-muted">No recent payments</td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-5">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-transparent border-0 pt-4 px-4">
                                    <h5 class="mb-1">Student-Teacher Ratio</h5>
                                    <p class="text-muted mb-0">Overall academic load</p>
                                </div>
                                <div class="card-body d-flex flex-column justify-content-center text-center">
                                    <h1 class="display-3 fw-bold">{{ $studentTeacherRatio ?? 0 }}:1</h1>
                                    <p class="text-muted">{{ number_format($totalStudents ?? 0) }} students : {{ number_format($totalTeachers ?? 0) }} teachers</p>
                                    <div class="mt-3">
                                        <span class="badge bg-light text-dark">Ideal ratio 30:1</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 4: Monthly Attendance Trend (Line Chart) -->
                    <div class="row g-4 mt-4">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-transparent border-0 pt-4 px-4">
                                    <h5 class="mb-1">Monthly Attendance Trend</h5>
                                    <p class="text-muted mb-0">Average attendance rate over last 6 months</p>
                                </div>
                                <div class="card-body pt-2 px-3">
                                    <div id="attendanceTrendChart" style="height: 300px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div> <!-- end content-area-body -->
            </div> <!-- end content-area -->
        </div> <!-- end main-content -->
    </div> <!-- end nxl-content -->

    <!-- ========== STYLES (unchanged) ========== -->
    <style>
        /* ... your original styles remain exactly as they were ... */
        .academic-dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            padding: 1.5rem 1.75rem 1rem;
            border-bottom: 1px solid rgba(15, 23, 42, .06);
            background: radial-gradient(circle at top right, rgba(13, 110, 253, .08), transparent 34%), linear-gradient(180deg, #fff 0%, #fbfcff 100%)
        }
        .academic-dashboard-body {
            padding: 1.5rem;
            background: linear-gradient(180deg, #f6f8fc 0%, #eef3f9 100%)
        }
        .academic-kicker {
            letter-spacing: .12em
        }
        .bg-soft-primary {
            background: rgba(13, 110, 253, .12)
        }
        .bg-soft-success {
            background: rgba(25, 135, 84, .12)
        }
        .bg-soft-warning {
            background: rgba(255, 193, 7, .18)
        }
        .bg-soft-danger {
            background: rgba(220, 53, 69, .12)
        }
        .bg-soft-secondary {
            background: rgba(108, 117, 125, .12)
        }
        .academy-hero {
            background: radial-gradient(circle at top left, rgba(13, 110, 253, .16), transparent 28%), linear-gradient(135deg, #fff 0%, #f8fbff 48%, #f4f8ff 100%)
        }
        .academy-hero-label {
            display: inline-flex;
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .12em;
            color: #0d6efd;
            font-weight: 700;
            margin-bottom: .75rem
        }
        .academy-hero-title {
            font-size: clamp(1.75rem, 2vw, 2.35rem);
            font-weight: 800;
            color: #132238;
            margin-bottom: .85rem
        }
        .academy-hero-copy {
            color: #5f6b7a;
            max-width: 640px;
            margin-bottom: 1.5rem;
            line-height: 1.7
        }
        .academy-hero-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem
        }
        .academy-hero-stat,
        .academy-side-panel,
        .stat-card,
        .action-card,
        .attendance-box {
            border-radius: 1rem
        }
        .academy-hero-stat {
            padding: 1rem 1.1rem;
            background: rgba(255, 255, 255, .84);
            border: 1px solid rgba(15, 23, 42, .06);
            box-shadow: 0 12px 32px rgba(15, 23, 42, .05)
        }
        .academy-hero-stat span,
        .stat-card span,
        .attendance-box span {
            display: block;
            color: #64748b;
            font-size: .84rem;
            font-weight: 600
        }
        .academy-hero-stat strong,
        .stat-card h3,
        .attendance-box strong {
            display: block;
            color: #132238;
            font-weight: 800
        }
        .academy-hero-stat strong {
            font-size: 1.5rem;
            margin: .15rem 0
        }
        .academy-hero-stat small,
        .stat-card p {
            color: #7b8794
        }
        .academy-side-panel {
            padding: 1.25rem;
            background: #132238;
            color: #fff;
            box-shadow: 0 20px 45px rgba(19, 34, 56, .2);
            display: grid;
            gap: 1rem
        }
        .academy-side-item span {
            display: block;
            color: rgba(255, 255, 255, .7);
            font-size: .84rem
        }
        .academy-side-item strong {
            font-size: 1.35rem
        }
        .stat-card .card-body {
            padding: 1.25rem
        }
        .stat-icon {
            width: 3.1rem;
            height: 3.1rem;
            border-radius: .95rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-bottom: 1rem
        }
        .branch-item {
            padding: .9rem 1rem;
            border: 1px solid rgba(15, 23, 42, .06);
            border-radius: .95rem;
            background: #f8fbff
        }
        .branch-item+.branch-item,
        .timeline-item+.timeline-item {
            margin-top: 1rem
        }
        .progress-sm {
            height: .5rem
        }
        .attendance-boxes,
        .action-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .9rem
        }
        .attendance-box {
            padding: 1rem;
            background: #f8fafc;
            border: 1px solid rgba(15, 23, 42, .06)
        }
        .attendance-box.success {
            background: rgba(25, 135, 84, .08)
        }
        .attendance-box.danger {
            background: rgba(220, 53, 69, .08)
        }
        .timeline-item {
            display: grid;
            grid-template-columns: 14px 1fr;
            gap: .9rem
        }
        .timeline-dot {
            width: 14px;
            height: 14px;
            border-radius: 999px;
            background: linear-gradient(135deg, #0d6efd, #6ea8fe);
            margin-top: .35rem;
            box-shadow: 0 0 0 6px rgba(13, 110, 253, .12)
        }
        .action-card {
            display: flex;
            flex-direction: column;
            gap: .8rem;
            padding: 1rem;
            background: linear-gradient(180deg, #fff 0%, #f8fbff 100%);
            border: 1px solid rgba(15, 23, 42, .08);
            text-decoration: none;
            color: #172b44;
            font-weight: 700;
            transition: transform .2s ease, box-shadow .2s ease
        }
        .action-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 32px rgba(15, 23, 42, .08)
        }
        .table-modern thead th {
            font-size: .76rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #64748b;
            background: #f8fafc;
            border-bottom: 1px solid rgba(15, 23, 42, .08)
        }
        .table-modern>:not(caption)>*>* {
            padding: .95rem 1.2rem
        }
        .table-modern tbody tr:hover {
            background: rgba(13, 110, 253, .03)
        }
        .empty-mini {
            min-height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #7b8794;
            text-align: center
        }
        @media (max-width:1199.98px) {
            .academic-dashboard-header {
                flex-direction: column;
                align-items: stretch
            }
        }
        @media (max-width:991.98px) {
            .academy-hero-stats,
            .attendance-boxes,
            .action-grid {
                grid-template-columns: 1fr
            }
        }
        @media (max-width:767.98px) {
            .academic-dashboard-body {
                padding: 1rem
            }
            .academic-dashboard-header {
                padding: 1.1rem 1rem .9rem
            }
        }
    </style>

    <!-- ========== SCRIPTS (original + new charts) ========== -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Original Admissions Chart
            const target = document.querySelector('#academicAdmissionsChart');
            if (target && typeof ApexCharts !== 'undefined') {
                const totals = @json(($monthlyAdmissions ?? collect())->pluck('total')->values());
                const labels = @json(($monthlyAdmissions ?? collect())->pluck('label')->values());
                new ApexCharts(target, {
                    chart: {
                        type: 'area',
                        height: 320,
                        toolbar: { show: false },
                        fontFamily: 'Inter, sans-serif'
                    },
                    series: [{ name: 'Admissions', data: totals }],
                    colors: ['#0d6efd'],
                    stroke: { curve: 'smooth', width: 3 },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            opacityFrom: .34,
                            opacityTo: .04,
                            stops: [0, 95, 100]
                        }
                    },
                    dataLabels: { enabled: false },
                    grid: { borderColor: 'rgba(148,163,184,.18)', strokeDashArray: 4 },
                    xaxis: {
                        categories: labels,
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                        labels: { style: { colors: '#64748b' } }
                    },
                    yaxis: { labels: { style: { colors: '#64748b' } } },
                    legend: { show: false },
                    tooltip: { theme: 'light' }
                }).render();
            }

            // New: Fee Trend Chart (bar)
            const feeCtx = document.querySelector('#feeTrendChart');
            if (feeCtx && typeof ApexCharts !== 'undefined') {
                const feeData = @json($monthlyFeeTrend ?? []);
                new ApexCharts(feeCtx, {
                    chart: { type: 'bar', height: 300, toolbar: { show: false } },
                    series: [{ name: 'Fee Collected', data: feeData.map(i => i.total) }],
                    colors: ['#28a745'],
                    xaxis: { categories: feeData.map(i => i.label) },
                    dataLabels: { enabled: false },
                    plotOptions: { bar: { borderRadius: 6, horizontal: false } }
                }).render();
            }

            // New: Gender Ratio Donut Chart
            const genderCtx = document.querySelector('#genderRatioChart');
            if (genderCtx && typeof ApexCharts !== 'undefined') {
                new ApexCharts(genderCtx, {
                    chart: { type: 'donut', height: 280 },
                    series: [{{ $maleCount ?? 0 }}, {{ $femaleCount ?? 0 }}, {{ $otherCount ?? 0 }}],
                    labels: ['Male', 'Female', 'Other'],
                    colors: ['#0d6efd', '#dc3545', '#6c757d'],
                    legend: { position: 'bottom' },
                    dataLabels: { enabled: false }
                }).render();
            }

            // New: Monthly Attendance Trend (line)
            const attCtx = document.querySelector('#attendanceTrendChart');
            if (attCtx && typeof ApexCharts !== 'undefined') {
                const attData = @json($monthlyAttendanceTrend ?? []);
                new ApexCharts(attCtx, {
                    chart: { type: 'line', height: 300, toolbar: { show: false }, zoom: { enabled: false } },
                    series: [{ name: 'Attendance %', data: attData.map(i => i.rate) }],
                    colors: ['#ffc107'],
                    stroke: { curve: 'smooth', width: 3 },
                    markers: { size: 5 },
                    xaxis: { categories: attData.map(i => i.label) },
                    yaxis: { min: 0, max: 100, labels: { formatter: (val) => val + '%' } },
                    tooltip: { y: { formatter: (val) => val + '%' } }
                }).render();
            }
        });
    </script>
@endsection