@extends('studentadmin_layout')


@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
           

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="nav-tabs-wrapper page-content-left-sidebar-wrapper">
                            <ul class="nav nav-tabs nav-tabs-custom-style" id="staffTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#overviewTab">
                                        Overview
                                    </button>
                                </li>

                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#salaryTab">
                                        <i class="feather-dollar-sign me-1"></i>Salary
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#attendanceTab">
                                        Attendance
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#timetableTab">
                                        <i class="feather-calendar me-1"></i>Class Schedule
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="content-area-body">
                    <div class="main-content">
                        <div class="tab-content">
                            {{-- 🔥 OVERVIEW TAB --}}
                            <div class="tab-pane fade active show" id="overviewTab">
                                <div class="row">
                                    {{-- MAIN HEADER --}}
                                    <div class="col-lg-12">
                                        <div class="card stretch stretch-full">
                                            <div
                                                class="card-body task-header d-md-flex align-items-center justify-content-between">
                                                <div class="me-4">
                                                    <h4 class="mb-4 fw-bold d-flex align-items-center">
                                                        <span class="text-truncate-1-line me-3">
                                                            {{ $staff->first_name }} {{ $staff->last_name }}
                                                        </span>
                                                        <span
                                                            class="badge bg-soft-{{ $staff->status == 'active' ? 'success' : 'warning' }} 
                                                                text-{{ $staff->status == 'active' ? 'success' : 'warning' }} px-3 py-2">
                                                            {{ ucfirst($staff->status) }}
                                                        </span>
                                                    </h4>
                                                    <div class="d-flex align-items-center gap-3 flex-wrap">
                                                        <span class="text-muted fs-12">
                                                            <i class="feather-id me-1"></i>Staff ID: {{ $staff->id }}
                                                        </span>
                                                        <span class="text-muted fs-12">
                                                            <i
                                                                class="feather-briefcase me-1"></i>{{ $staff->designation->name ?? 'N/A' }}
                                                        </span>
                                                        <span class="text-muted fs-12">
                                                            <i
                                                                class="feather-hash me-1"></i>{{ $staff->department->name ?? 'N/A' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- DETAILS CARDS --}}
                                    <div class="col-xl-8">
                                        <div class="card stretch stretch-full">
                                            <div class="card-header">
                                                <h6 class="card-title">Staff Details</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row g-4">
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Email</label>
                                                        <p class="mb-0">{{ $staff->email ?? 'N/A' }}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Phone</label>
                                                        <p class="mb-0">{{ $staff->phone ?? 'N/A' }}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Gender</label>
                                                        <p class="mb-0">{{ ucfirst($staff->gender ?? 'N/A') }}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Date of Birth</label>
                                                        <p class="mb-0">
                                                            {{ $staff->dob ? $staff->dob->format('d M, Y') : 'N/A' }}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Joining Date</label>
                                                        <p class="mb-0">
                                                            {{ $staff->joining_date ? $staff->joining_date->format('d M, Y') : 'N/A' }}
                                                        </p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Blood Group</label>
                                                        <p class="mb-0">{{ $staff->blood_group ?? 'N/A' }}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Nationality</label>
                                                        <p class="mb-0">{{ $staff->nationality ?? 'N/A' }}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">PAN</label>
                                                        <p class="mb-0">{{ $staff->pan ?? 'N/A' }}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Aadhaar</label>
                                                        <p class="mb-0">{{ $staff->aadhaar ?? 'N/A' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- BANK DETAILS --}}
                                        @if ($staff->bank_account_name || $staff->bank_account_number)
                                            <div class="card stretch stretch-full mt-4">
                                                <div class="card-header">
                                                    <h6 class="card-title">
                                                        <i class="feather-credit-card me-2"></i>Bank Details
                                                    </h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-3">
                                                        <div class="col-md-4">
                                                            <label class="form-label small text-muted">Account Name</label>
                                                            <p class="fw-semibold">{{ $staff->bank_account_name ?? 'N/A' }}
                                                            </p>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label small text-muted">Account
                                                                Number</label>
                                                            <p class="fw-semibold">
                                                                {{ $staff->bank_account_number ?? 'N/A' }}</p>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label small text-muted">IFSC</label>
                                                            <p class="fw-semibold">{{ $staff->ifsc ?? 'N/A' }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- PROFILE PHOTO --}}
                                    <div class="col-xl-4">
                                        <div class="card stretch stretch-full">
                                            <div class="card-body text-center p-4">
                                                @if ($staff->photo_url)
                                                    <img src="{{ $staff->photo_url }}"
                                                        class="img-fluid rounded-circle mb-3 shadow-sm"
                                                        style="width: 150px; height: 150px; object-fit: cover;"
                                                        alt="Staff Photo">
                                                @else
                                                    <div
                                                        class="avatar-text avatar-xl bg-soft-primary text-primary mb-3 mx-auto">
                                                        <i class="feather-user fs-1"></i>
                                                    </div>
                                                @endif
                                                <h5 class="mb-1">{{ $staff->first_name }} {{ $staff->last_name }}</h5>
                                                <p class="text-muted mb-2">{{ $staff->designation->name ?? 'Staff' }}</p>
                                                <div
                                                    class="d-flex align-items-center justify-content-center gap-2 text-muted fs-12">
                                                    <i class="feather-clock me-1"></i>
                                                    Joined: {{ $staff->joining_date?->format('d M, Y') ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- SALARY TAB --}}
                            <div class="tab-pane fade" id="salaryTab">
                                <div class="card stretch stretch-full">
                                    <div class="card-body">
                                        <div class="text-center py-5">
                                            <i class="feather-dollar-sign fs-1 text-success mb-3 d-block"></i>
                                            <h5 class="text-muted">Salary records coming soon</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ATTENDANCE TAB --}}
                            <div class="tab-pane fade" id="attendanceTab">
                                <div class="card stretch stretch-full">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-center"
                                            style="height: calc(100vh - 315px)">
                                            <div class="text-center">
                                                <i class="feather-calendar fs-2 text-muted mb-3 d-block"></i>
                                                <h5 class="text-muted">No attendance data</h5>
                                                <p class="text-muted fs-12">Attendance records will be available here</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- TIMETABLE TAB --}}
                            {{-- <div class="tab-pane fade" id="timetableTab">
                                <div class="card stretch stretch-full">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 class="card-title mb-0">
                                            <i class="feather-calendar me-2 text-primary"></i>Class Schedule
                                        </h6>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="text-center p-5">
                                            <i class="feather-calendar fs-1 text-muted mb-3 d-block"></i>
                                            <h5 class="text-muted">Schedule Loading...</h5>
                                            <p class="text-muted">Staff class schedule will appear here</p>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}
                            {{-- 🔥 STAFF TIMETABLE TAB --}}
                           @include('Academics.staff.timetable')

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- STYLES --}}
    <style>
        .nav-tabs-custom-style .nav-link {
            padding: 12px 20px;
            font-weight: 500;
        }

        .weekday-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
        }
    </style>

    <a href="{{ route('admin.staff.index') }}"
        class="btn btn-secondary position-fixed bottom-0 end-0 m-4 shadow-lg no-print" style="z-index: 1050;">
        <i class="feather-list me-1"></i>Back to List
    </a>
@endsection
