@extends('hr.layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('hr.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">
            

            <div class="content-area-body px-4 py-4">
                <div class="row g-3 mb-4">
                    <div class="col-md-3 col-sm-6">
                        <div class="card border-0 shadow-sm h-100"><div class="card-body">
                            <div class="fs-12 text-muted text-uppercase fw-bold mb-2">Staff</div>
                            <h2 class="mb-0 fw-bold">{{ $totalStaff }}</h2>
                            <div class="fs-12 text-muted mt-2">{{ $activeStaff }} active</div>
                        </div></div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card border-0 shadow-sm h-100"><div class="card-body">
                            <div class="fs-12 text-muted text-uppercase fw-bold mb-2">Students</div>
                            <h2 class="mb-0 fw-bold">{{ $totalStudents }}</h2>
                            <div class="fs-12 text-muted mt-2">{{ $activeEnrollments }} active enrollments</div>
                        </div></div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card border-0 shadow-sm h-100"><div class="card-body">
                            <div class="fs-12 text-muted text-uppercase fw-bold mb-2">New Hires</div>
                            <h2 class="mb-0 fw-bold">{{ $newHiresThisMonth }}</h2>
                            <div class="fs-12 text-muted mt-2">This month</div>
                        </div></div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card border-0 shadow-sm h-100"><div class="card-body">
                            <div class="fs-12 text-muted text-uppercase fw-bold mb-2">Leaves Today</div>
                            {{-- <h2 class="mb-0 fw-bold">{{ $leaveToday }}</h2> --}}
                            <div class="fs-12 text-muted mt-2">Active leave activity</div>
                        </div></div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-xl-7">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-0 pb-0">
                                <h5 class="mb-0 fw-bold">Recent Staff</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    @forelse($recentStaff as $staff)
                                        <div class="list-group-item d-flex align-items-center justify-content-between px-0">
                                            <div>
                                                <div class="fw-semibold">{{ $staff->first_name }} {{ $staff->last_name }}</div>
                                                <div class="fs-12 text-muted">
                                                    {{ $staff->designation->name ?? 'Designation' }} · {{ $staff->department->name ?? 'Department' }}
                                                </div>
                                            </div>
                                            <span class="badge bg-soft-success text-success">{{ ucfirst($staff->status) }}</span>
                                        </div>
                                    @empty
                                        <div class="text-center text-muted py-4">No staff available.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-5">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-0 pb-0">
                                <h5 class="mb-0 fw-bold">Leave Activity</h5>
                            </div>
                            <div class="card-body">
                                {{-- @forelse($leaveRequests as $leave)
                                    <div class="d-flex align-items-start justify-content-between py-3 border-bottom">
                                        <div>
                                            <div class="fw-semibold">{{ $leave->title ?? 'Leave Request' }}</div>
                                            <div class="fs-12 text-muted">
                                                {{ \Illuminate\Support\Carbon::parse($leave->from_date)->format('d M') }}
                                                -
                                                {{ \Illuminate\Support\Carbon::parse($leave->to_date)->format('d M Y') }}
                                            </div>
                                        </div>
                                        <span class="badge bg-soft-secondary text-secondary">{{ ucfirst($leave->status ?? 'pending') }}</span>
                                    </div>
                                @empty
                                    <div class="text-center text-muted py-4">No leave records found.</div>
                                @endforelse --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
