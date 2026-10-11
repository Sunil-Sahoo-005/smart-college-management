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

        .transport-shell {
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

        .summary-panel,
        .route-panel {
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
        .route-label {
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

        .route-value {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.04em;
            margin: 8px 0 6px;
            color: #fffaf3;
        }

        .route-copy {
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

        .profile-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .profile-card,
        .transport-card,
        .note-card {
            border-radius: 18px;
            padding: 16px;
            background: linear-gradient(180deg, #faf7f1 0%, #edf4ee 100%);
            border: 1px solid rgba(24, 53, 45, 0.07);
        }

        .profile-label,
        .transport-label,
        .note-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #7b8b85;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .profile-value,
        .transport-value,
        .note-title {
            font-size: 15px;
            font-weight: 700;
            color: #18352d;
            line-height: 1.45;
        }

        .transport-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .transport-card {
            background: #fbf8f2;
        }

        .transport-big {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.04em;
            color: #18352d;
            margin-bottom: 6px;
        }

        .transport-copy,
        .note-copy {
            margin: 0;
            font-size: 13px;
            color: #677973;
            line-height: 1.7;
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

            .hero-grid,
            .stats-grid,
            .profile-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            .transport-shell {
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
            .profile-grid,
            .transport-grid {
                grid-template-columns: 1fr;
            }

            .panel-head {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>

    <div class="nxl-content">
        <div class="transport-shell">
            <section class="hero-panel">
                <div class="hero-grid">
                    <div>
                        <span class="eyebrow">Student Transport</span>
                        <h1 class="hero-title">Travel details made simpler, {{ $student->first_name ?? 'Student' }}.</h1>
                        <p class="hero-copy">
                            See your assigned route, stop, arrival timing, and transport fee in one clean student-friendly
                            view.
                        </p>

                        <div class="hero-chips">
                            <span class="hero-chip">Student: {{ $studentName }}</span>
                            <span class="hero-chip">Reg No: {{ $student->admission_number ?? 'N/A' }}</span>
                            <span class="hero-chip">Program: {{ $student->stream_name ?? 'Not assigned' }}</span>
                            <span class="hero-chip">Semester: {{ $student->class_name ?? 'Not assigned' }}</span>
                        </div>
                    </div>

                    <div class="hero-side">
                        <div class="summary-panel">
                            <div class="summary-grid">
                                <div class="summary-item">
                                    <div class="summary-label">Route Status</div>
                                    <div class="summary-value">{{ $transport ? 'Assigned' : 'Not Assigned' }}</div>
                                </div>
                                <div class="summary-item">
                                    <div class="summary-label">Stop Name</div>
                                    <div class="summary-value">{{ $transport->stop_name ?? 'N/A' }}</div>
                                </div>
                                <div class="summary-item">
                                    <div class="summary-label">Arrival Time</div>
                                    <div class="summary-value">
                                        {{ $transport && $transport->arrival_time ? \Carbon\Carbon::parse($transport->arrival_time)->format('h:i A') : 'N/A' }}
                                    </div>
                                </div>
                                <div class="summary-item">
                                    <div class="summary-label">Transport Fee</div>
                                    <div class="summary-value">
                                        {{ $transport ? 'Rs ' . number_format($transport->transport_fee, 2) : 'N/A' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="route-panel">
                            <div class="route-label">Assigned Route</div>
                            <div class="route-value">{{ $transport->route_name ?? 'No route assigned' }}</div>
                            <div class="route-copy">
                                @if ($transport)
                                    Travel from {{ $transport->start_point }} to {{ $transport->end_point }} with pickup
                                    at {{ $transport->stop_name }}.
                                @else
                                    No transport mapping is currently configured for this student account.
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="panel-card">
                <div class="panel-head">
                    <div>
                        <div class="panel-kicker">Route Details</div>
                        <h2 class="panel-title">Transport Information</h2>
                        <p class="panel-copy">A clear breakdown of route, stop, travel timing, and transport fee
                            information.</p>
                    </div>
                </div>

                @if ($transport)
                    <div class="transport-grid">
                        <div class="transport-card">
                            <div class="transport-label">Route Name</div>
                            <div class="transport-big">{{ $transport->route_name }}</div>
                            <p class="transport-copy">This is the active route assigned to the student transport profile.
                            </p>
                        </div>

                        <div class="transport-card">
                            <div class="transport-label">Journey Path</div>
                            <div class="transport-value">{{ $transport->start_point }} to {{ $transport->end_point }}
                            </div>
                            <p class="transport-copy">Travel starts at the route’s origin and ends at the final mapped
                                destination.</p>
                        </div>

                        <div class="transport-card">
                            <div class="transport-label">Assigned Stop</div>
                            <div class="transport-value">{{ $transport->stop_name }}</div>
                            <p class="transport-copy">This is the stop currently mapped for pickup or arrival.</p>
                        </div>

                        <div class="transport-card">
                            <div class="transport-label">Arrival Time</div>
                            <div class="transport-value">
                                {{ $transport->arrival_time ? \Carbon\Carbon::parse($transport->arrival_time)->format('h:i A') : 'N/A' }}
                            </div>
                            <p class="transport-copy">Students should be ready at the stop before the scheduled time.</p>
                        </div>

                        <div class="transport-card">
                            <div class="transport-label">Transport Fee</div>
                            <div class="transport-value">Rs {{ number_format($transport->transport_fee, 2) }}</div>
                            <p class="transport-copy">Fee amount currently associated with the mapped route and stop.</p>
                        </div>

                        <div class="transport-card">
                            <div class="transport-label">Travel Status</div>
                            <div class="transport-value">Active Route Mapping</div>
                            <p class="transport-copy">This student has an active transport assignment in the portal.</p>
                        </div>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="feather-truck"></i>
                        <div class="empty-title">No transport record found</div>
                        <p class="empty-copy">A route or stop has not yet been assigned to this student in the transport
                            module.</p>
                    </div>
                @endif
            </section>

            <section class="panel-card">
                <div class="panel-head">
                    <div>
                        <div class="panel-kicker">Important Note</div>
                        <h2 class="panel-title">Travel Guidance</h2>
                        <p class="panel-copy">A quick reminder section to make the transport page more useful for students.
                        </p>
                    </div>
                </div>

                <div class="note-card">
                    <div class="note-label">Before you travel</div>
                    <div class="note-title">Reach the assigned stop a few minutes early</div>
                    <p class="note-copy">
                        The arrival time shown here is the scheduled stop timing in the system. Students should arrive a
                        little early
                        and contact the office or transport desk if the mapped route, stop, or fee details look incorrect.
                    </p>
                </div>
            </section>
        </div>
    </div>
@endsection
