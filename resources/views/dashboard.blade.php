@extends('admin_layout')

@section('content')
    <div class="nxl-content">

        {{-- Page header --}}
        <div class="page-header mb-1">
            <div class="page-header-left">
                <h5 class="m-b-4 fw-semibold">Dashboard</h5>
                <span class="fs-12 text-muted">
                    {{ optional($currentYear)->name ?? 'No active academic year' }} &nbsp;&bull;&nbsp;
                    Quick access to all modules
                </span>
            </div>
        </div>

        {{-- ── Stat cards ─────────────────────────────────────────────────── --}}
        <div class="main-content">

        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 stat-card h-100">
                    <div class="card-body p-3">
                        <p class="text-muted fs-12 mb-1">Total students</p>
                        <h4 class="fw-semibold mb-1">{{ number_format($totalStudents) }}</h4>
                        <span class="fs-12 text-success">↑ {{ $newStudentsThisMonth }} this month</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 stat-card h-100">
                    <div class="card-body p-3">
                        <p class="text-muted fs-12 mb-1">Active staff</p>
                        <h4 class="fw-semibold mb-1">{{ number_format($totalStaff) }}</h4>
                        <span class="fs-12 text-success">↑ {{ $newStaffThisMonth }} joined</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 stat-card h-100">
                    <div class="card-body p-3">
                        <p class="text-muted fs-12 mb-1">Fee collected ({{ now()->year }})</p>
                        <h4 class="fw-semibold mb-1">₹{{ number_format($feeCollected / 100000, 1) }}L</h4>
                        <span class="fs-12 text-success">↑ This academic year</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 stat-card h-100">
                    <div class="card-body p-3">
                        <p class="text-muted fs-12 mb-1">Pending dues</p>
                        <h4 class="fw-semibold mb-1">₹{{ number_format($pendingDues / 100000, 1) }}L</h4>
                        <span class="fs-12 text-danger">{{ $studentsWithDues }} students</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Module cards ─────────────────────────────────────────────────── --}}
        <div class="row g-3 mb-4">
           
        </div>

        {{-- ── Bottom panels ────────────────────────────────────────────────── --}}
        <div class="row g-3">

            {{-- Recent fee payments --}}
            <div class="col-xl-6">
                <div class="card border-0 h-100">
                    <div
                        class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between py-3">
                        <h6 class="fw-semibold mb-0 fs-13">Recent fee payments</h6>
                        <a href="{{ route('admin.fee-payments.index') }}" class="fs-12 text-primary">View all</a>
                    </div>
                    <div class="card-body p-0">
                        @forelse ($recentPayments as $payment)
                            <div class="d-flex align-items-center gap-3 px-4 py-3 panel-row">
                                <div class="initials-avatar bg-primary bg-opacity-10 text-primary">
                                    {{ $payment['initials'] }}
                                </div>
                                <div class="flex-grow-1">
                                    <p class="fw-semibold fs-13 mb-0">{{ $payment['name'] }}</p>
                                    <span class="text-muted fs-12">{{ $payment['time'] }}</span>
                                </div>
                                <span class="fw-semibold fs-13">{{ $payment['amount'] }}</span>
                            </div>
                        @empty
                            <div class="px-4 py-4 text-muted fs-13">No payments today.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Today's attendance --}}
            <div class="col-xl-6">
                <div class="card border-0 h-100">
                    <div
                        class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between py-3">
                        <h6 class="fw-semibold mb-0 fs-13">Today's attendance by section</h6>
                        <a href="{{ route('admin.student_attendance.date') }}" class="fs-12 text-primary">View all</a>
                    </div>
                    <div class="card-body">
                        @forelse ($attendanceBySection as $att)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fs-12 text-muted">{{ $att['label'] }}</span>
                                    <span class="fs-12 fw-semibold">{{ $att['pct'] }}%</span>
                                </div>
                                <div class="progress" style="height:6px;border-radius:4px;background:#f0f0f0">
                                    <div class="progress-bar" role="progressbar"
                                        style="width:{{ $att['pct'] }}%;background:{{ $att['color'] }};border-radius:4px">
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted fs-13">No attendance data for today.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Notice board --}}
            <div class="col-xl-6">
                <div class="card border-0 h-100">
                    <div
                        class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between py-3">
                        <h6 class="fw-semibold mb-0 fs-13">Notice board</h6>
                        <a href="" class="fs-12 text-primary">+ Add notice</a>
                    </div>
                    <div class="card-body p-0">
                        @forelse ($notices as $notice)
                            <div class="d-flex align-items-start gap-3 px-4 py-3 panel-row">
                                <div class="notice-dot mt-1"></div>
                                <div>
                                    <p class="fs-13 mb-0">{{ $notice['title'] }}</p>
                                    <span class="text-muted fs-12">{{ $notice['date'] }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-4 text-muted fs-13">No notices yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Pending leaves --}}
            <div class="col-xl-6">
                <div class="card border-0 h-100">
                    <div
                        class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between py-3">
                        <h6 class="fw-semibold mb-0 fs-13">Pending leave requests</h6>
                        <a href="" class="fs-12 text-primary">View all</a>
                    </div>
                    <div class="card-body p-0">
                        {{-- @forelse ($pendingLeaves as $leave)
                        <div class="d-flex align-items-center gap-3 px-4 py-3 panel-row">
                            <div class="flex-grow-1">
                                <p class="fw-semibold fs-13 mb-0">{{ $leave['name'] }}</p>
                                <span class="text-muted fs-12">{{ $leave['leave_type'] }} &bull; {{ $leave['from'] }} – {{ $leave['to'] }}</span>
                            </div>
                            @if ($leave['status'] === 'pending')
                                <span class="badge bg-warning text-dark fs-11">Pending</span>
                                <a href="{{ route('hrms.leaves.show', $leave['id']) }}"
                                   class="btn btn-sm btn-outline-secondary fs-12 py-1 px-2">Review</a>
                            @else
                                <span class="badge bg-success fs-11">Approved</span>
                            @endif
                        </div>
                    @empty
                        <div class="px-4 py-4 text-muted fs-13">No pending leave requests.</div>
                    @endforelse --}}
                    </div>
                </div>
            </div>

        </div>{{-- end row --}}
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .stat-card {
            background: #f8f9fa;
            border-radius: 12px;
        }

        .hover-card {
            border-radius: 12px;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .hover-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, .08) !important;
        }

        .mod-icon-wrap {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .initials-avatar {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
            flex-shrink: 0;
        }

        .panel-row {
            border-bottom: 1px solid #f0f0f0;
        }

        .panel-row:last-child {
            border-bottom: none;
        }

        .notice-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #0d6efd;
            flex-shrink: 0;
        }

        .fs-11 {
            font-size: 11px !important;
        }

        .fs-12 {
            font-size: 12px !important;
        }

        .fs-13 {
            font-size: 13px !important;
        }
    </style>
@endpush
