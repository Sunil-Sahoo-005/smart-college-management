@extends('hr.layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('hr.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <!-- HEADER BREADCRUMB / ACTION BAR -->
                <div class="content-area-header bg-white sticky-top px-4 py-3 border-bottom shadow-sm ">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 w-100">
                        <div>
                            <h4 class="fw-bold mb-1 text-dark">{{ $staff->first_name }} {{ $staff->last_name }}</h4>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="badge bg-soft-primary text-primary px-2 py-1 fs-11">
                                    <i class="feather-briefcase me-1"></i>{{ $staff->designation?->name ?? 'Faculty' }}
                                </span>
                                <span class="text-muted">·</span>
                                <span class="text-muted fs-12">{{ $staff->department?->name ?? 'Department' }}</span>
                                <span class="text-muted">·</span>
                               
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 d-flex align-items-center gap-2">
                        <a href="{{ route('hr.staff.index', request()->route('tenantKey')) }}" class="btn btn-light btn-sm">
                            <i class="feather-arrow-left me-1"></i> Back
                        </a>
                        <a href="{{ route('hr.staff.edit', [request()->route('tenantKey'), $staff->id]) }}"
                            class="btn btn-primary btn-sm">
                            <i class="feather-edit me-1"></i> Edit
                        </a>
                    </div>
                </div>

                <!-- TABS CONTENT AREA -->
                <div class="content-area-body px-4 pb-4">
                    <!-- NAVIGATION TABS CARD -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-3">
                            <ul class="nav nav-pills nav-pills-custom gap-1 m-0" id="staffShowTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="overview-tab" data-bs-toggle="tab"
                                        data-bs-target="#overviewTab" type="button" role="tab">
                                        <i class="feather-user me-1"></i>Overview
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="salary-tab" data-bs-toggle="tab"
                                        data-bs-target="#salaryTab" type="button" role="tab">
                                        <i class="feather-dollar-sign me-1"></i>Salary & Payroll
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="attendance-tab" data-bs-toggle="tab"
                                        data-bs-target="#attendanceTab" type="button" role="tab">
                                        <i class="feather-check-square me-1"></i>Attendance
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="leave-tab" data-bs-toggle="tab" data-bs-target="#leaveTab"
                                        type="button" role="tab">
                                        <i class="feather-calendar me-1"></i>Leaves
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="timetable-tab" data-bs-toggle="tab"
                                        data-bs-target="#timetableTab" type="button" role="tab">
                                        <i class="feather-clock me-1"></i>Class Schedule
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="library-tab" data-bs-toggle="tab"
                                        data-bs-target="#libraryTab" type="button" role="tab">
                                        <i class="feather-book-open me-1"></i>Library
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="documents-tab" data-bs-toggle="tab"
                                        data-bs-target="#documentsTab" type="button" role="tab">
                                        <i class="feather-file-text me-1"></i>Documents
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="tab-content" id="staffShowTabsContent">

                        <!-- ================= OVERVIEW TAB ================= -->
                        <div class="tab-pane fade show active" id="overviewTab" role="tabpanel">
                            <div class="row g-4">
                                <!-- Main Left (8 Cols) -->
                                <div class="col-xl-8">
                                    <!-- Banner & Photo Card -->
                                    <div class="card border-0 shadow-sm mb-4 overflow-hidden position-relative">
                                        <div class="bg-primary"
                                            style="height: 120px; background-image: linear-gradient(135deg, #1e3a8a, #3b82f6);">
                                        </div>
                                        <div class="card-body pt-0 px-4 pb-4">
                                            <div class="d-flex align-items-end mb-4" style="margin-top: -60px;">
                                                <div class="position-relative">
                                                    @if ($staff->photo_url)
                                                        <img src="{{ asset('storage/' . $staff->photo_url) }}"
                                                            class="rounded-circle border border-4 border-white shadow"
                                                            style="width: 130px; height: 130px; object-fit: cover; background: #fff;">
                                                    @else
                                                        <div class="avatar-text bg-soft-primary text-primary rounded-circle border border-4 border-white shadow d-flex align-items-center justify-content-center fw-bold"
                                                            style="width: 130px; height: 130px; font-size: 38px; background: #fff;">
                                                            {{ substr($staff->first_name, 0, 1) }}{{ substr($staff->last_name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                    <span
                                                        class="position-absolute bottom-0 end-0 mb-2 me-1 p-2 bg-success border border-2 border-white rounded-circle shadow-sm"
                                                        title="Active"></span>
                                                </div>
                                                <div class="ms-4 mb-2">
                                                    <h3 class="fw-bold text-dark mb-1">{{ $staff->first_name }}
                                                        {{ $staff->last_name }}</h3>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <span class="text-muted fs-13"><i
                                                                class="feather-id text-primary me-1"></i> ID:
                                                            {{ $staff->employee_id ?? $staff->id }}</span>
                                                        <span class="text-muted fs-13">|</span>
                                                        <span class="text-muted fs-13"><i
                                                                class="feather-shield text-info me-1"></i> BPUT ID:
                                                            {{ $staff->bput_id ?? 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr class="opacity-10 mb-4">

                                            <!-- Contact Grid -->
                                            <div class="row g-4">
                                                <div class="col-md-6">
                                                    <div class="d-flex align-items-start">
                                                        <div
                                                            class="avatar-text avatar-sm bg-soft-light text-primary rounded me-3">
                                                            <i class="feather-mail"></i></div>
                                                        <div>
                                                            <label
                                                                class="text-muted fs-10 mb-1 d-block text-uppercase fw-bold">Email
                                                                Address</label>
                                                            <span
                                                                class="fw-semibold text-dark">{{ $staff->email ?? 'N/A' }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="d-flex align-items-start">
                                                        <div
                                                            class="avatar-text avatar-sm bg-soft-light text-success rounded me-3">
                                                            <i class="feather-phone"></i></div>
                                                        <div>
                                                            <label
                                                                class="text-muted fs-10 mb-1 d-block text-uppercase fw-bold">Primary
                                                                Phone</label>
                                                            <span
                                                                class="fw-semibold text-dark">{{ $staff->phone ?? 'N/A' }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="d-flex align-items-start">
                                                        <div
                                                            class="avatar-text avatar-sm bg-soft-light text-warning rounded me-3">
                                                            <i class="feather-phone-call"></i></div>
                                                        <div>
                                                            <label
                                                                class="text-muted fs-10 mb-1 d-block text-uppercase fw-bold">Alternate
                                                                Phone</label>
                                                            <span
                                                                class="fw-semibold text-dark">{{ $staff->alternate_phone ?? 'N/A' }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="d-flex align-items-start">
                                                        <div
                                                            class="avatar-text avatar-sm bg-soft-light text-info rounded me-3">
                                                            <i class="feather-map-pin"></i></div>
                                                        <div>
                                                            <label
                                                                class="text-muted fs-10 mb-1 d-block text-uppercase fw-bold">Current
                                                                Address</label>
                                                            <span
                                                                class="text-dark fs-12">{{ $staff->address ?? 'N/A' }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Personal & Professional Cards -->
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="card border-0 shadow-sm h-100">
                                                <div class="card-header border-0 bg-transparent pt-4 px-4">
                                                    <h6 class="fw-bold text-dark mb-0"><i
                                                            class="feather-user me-2 text-primary"></i>Personal Details
                                                    </h6>
                                                </div>
                                                <div class="card-body p-4 pt-2">
                                                    <ul class="list-group list-group-flush">
                                                        <li
                                                            class="list-group-item px-0 py-2 border-0 d-flex justify-content-between">
                                                            <span class="text-muted fs-12">Gender</span>
                                                            <span
                                                                class="fw-semibold text-dark">{{ ucfirst($staff->gender ?? 'N/A') }}</span>
                                                        </li>
                                                        <li
                                                            class="list-group-item px-0 py-2 border-0 d-flex justify-content-between">
                                                            <span class="text-muted fs-12">Date of Birth</span>
                                                            <span
                                                                class="fw-semibold text-dark">{{ $staff->dob ? $staff->dob->format('d M, Y') : 'N/A' }}</span>
                                                        </li>
                                                        <li
                                                            class="list-group-item px-0 py-2 border-0 d-flex justify-content-between">
                                                            <span class="text-muted fs-12">Blood Group</span>
                                                            <span
                                                                class="badge bg-soft-danger text-danger border-0">{{ $staff->blood_group ?? 'N/A' }}</span>
                                                        </li>
                                                        <li
                                                            class="list-group-item px-0 py-2 border-0 d-flex justify-content-between">
                                                            <span class="text-muted fs-12">Marital Status</span>
                                                            <span
                                                                class="fw-semibold text-dark">{{ $staff->marital_status ?? 'N/A' }}</span>
                                                        </li>
                                                        <li
                                                            class="list-group-item px-0 py-2 border-0 d-flex justify-content-between">
                                                            <span class="text-muted fs-12">Nationality</span>
                                                            <span
                                                                class="fw-semibold text-dark">{{ $staff->nationality ?? 'N/A' }}</span>
                                                        </li>
                                                        <li
                                                            class="list-group-item px-0 py-2 border-0 d-flex justify-content-between">
                                                            <span class="text-muted fs-12">Religion</span>
                                                            <span
                                                                class="fw-semibold text-dark">{{ $staff->religion ?? 'N/A' }}</span>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="card border-0 shadow-sm h-100">
                                                <div class="card-header border-0 bg-transparent pt-4 px-4">
                                                    <h6 class="fw-bold text-dark mb-0"><i
                                                            class="feather-award me-2 text-info"></i>Professional Details
                                                    </h6>
                                                </div>
                                                <div class="card-body p-4 pt-2">
                                                    <ul class="list-group list-group-flush">
                                                        <li
                                                            class="list-group-item px-0 py-2 border-0 d-flex justify-content-between">
                                                            <span class="text-muted fs-12">Employment Type</span>
                                                            <span
                                                                class="badge bg-soft-info text-info border-0">{{ $staff->employment_type ?? 'N/A' }}</span>
                                                        </li>
                                                        <li
                                                            class="list-group-item px-0 py-2 border-0 d-flex justify-content-between">
                                                            <span class="text-muted fs-12">Qualification</span>
                                                            <span
                                                                class="fw-semibold text-dark">{{ $staff->qualification ?? 'N/A' }}</span>
                                                        </li>
                                                        <li
                                                            class="list-group-item px-0 py-2 border-0 d-flex justify-content-between">
                                                            <span class="text-muted fs-12">Experience</span>
                                                            <span
                                                                class="fw-bold text-dark">{{ $staff->experience ?? '0' }}+
                                                                Years</span>
                                                        </li>
                                                        <li
                                                            class="list-group-item px-0 py-2 border-0 d-flex justify-content-between">
                                                            <span class="text-muted fs-12">Category</span>
                                                            <span
                                                                class="fw-semibold text-dark">{{ $staff->category ?? 'N/A' }}</span>
                                                        </li>
                                                        <li
                                                            class="list-group-item px-0 py-2 border-0 d-flex justify-content-between">
                                                            <span class="text-muted fs-12">Joining Date</span>
                                                            <span
                                                                class="fw-semibold text-dark">{{ $staff->joining_date ? $staff->joining_date->format('d M, Y') : 'N/A' }}</span>
                                                        </li>
                                                        <li
                                                            class="list-group-item px-0 py-2 border-0 d-flex justify-content-between">
                                                            <span class="text-muted fs-12">Leaving / Resignation
                                                                Date</span>
                                                            <span class="fw-semibold text-dark">
                                                                {{ $staff->leaving_date ? $staff->leaving_date->format('d M, Y') : ($staff->resignation_date ? $staff->resignation_date->format('d M, Y') : 'N/A') }}
                                                            </span>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sidebar Right (4 Cols) -->
                                <div class="col-xl-4">
                                    <!-- Identities & Badges -->
                                    <div class="card border-0 shadow-sm mb-4">
                                        <div class="card-header border-0 bg-transparent pt-4 px-4">
                                            <h6 class="fw-bold text-dark mb-0"><i
                                                    class="feather-hash me-2 text-primary"></i>Identity Info</h6>
                                        </div>
                                        <div class="card-body p-4 pt-0">
                                            <div class="mt-3">
                                                <div class="p-3 bg-light rounded mb-3">
                                                    <label
                                                        class="text-muted fs-10 mb-1 d-block text-uppercase fw-bold">Aadhaar
                                                        Card</label>
                                                    <span class="fw-bold text-dark fs-14 letter-spacing-1">
                                                        {{ $staff->aadhaar ? chunk_split($staff->aadhaar, 4, ' ') : 'N/A' }}
                                                    </span>
                                                </div>
                                                <div class="p-3 bg-light rounded mb-3">
                                                    <label class="text-muted fs-10 mb-1 d-block text-uppercase fw-bold">PAN
                                                        Card</label>
                                                    <span
                                                        class="fw-bold text-dark fs-14 letter-spacing-1 text-uppercase">{{ $staff->pan ?? 'N/A' }}</span>
                                                </div>
                                                <div class="p-3 bg-light rounded">
                                                    <label class="text-muted fs-10 mb-1 d-block text-uppercase fw-bold">PF
                                                        Number</label>
                                                    <span
                                                        class="fw-bold text-dark fs-14">{{ $staff->pf_number ?? 'N/A' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Premium Banking Details Card -->
                                    <div class="card border-0 shadow-sm mb-4 bg-primary text-white overflow-hidden"
                                        style="background-image: linear-gradient(135deg, #1e3a8a, #2563eb);">
                                        <div class="card-body p-4">
                                            <div class="d-flex justify-content-between align-items-start mb-4">
                                                <i class="feather-credit-card fs-1 opacity-50"></i>
                                                <span
                                                    class="badge bg-white text-primary border-0 fs-10">{{ strtoupper($staff->account_type ?? 'Savings') }}</span>
                                            </div>
                                            <div class="mb-4">
                                                <label class="fs-9 opacity-75 mb-1 d-block">ACCOUNT NUMBER</label>
                                                <h4 class="fw-bold mb-0 letter-spacing-2">
                                                    {{ $staff->bank_account_number ?? '•••• •••• ••••' }}</h4>
                                            </div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <label class="fs-9 opacity-75 mb-1 d-block">IFSC CODE</label>
                                                    <span class="fw-semibold fs-12">{{ $staff->ifsc ?? 'N/A' }}</span>
                                                </div>
                                                <div class="col-6 text-end">
                                                    <label class="fs-9 opacity-75 mb-1 d-block">BANK NAME</label>
                                                    <span
                                                        class="fw-semibold fs-11 text-truncate d-block">{{ $staff->bank_name ?? 'N/A' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Addresses -->
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header border-0 bg-transparent pt-4 px-4">
                                            <h6 class="fw-bold text-dark mb-0"><i
                                                    class="feather-home me-2 text-success"></i>Permanent Address</h6>
                                        </div>
                                        <div class="card-body p-4 pt-2">
                                            <div class="d-flex align-items-start">
                                                <i class="feather-map fs-5 text-muted me-3 mt-1"></i>
                                                <p class="mb-0 text-muted fs-12 line-height-1-5">
                                                    {{ $staff->permanent_address ?? 'Not provided' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ================= SALARY DETAILS TAB ================= -->
                        <div class="tab-pane fade" id="salaryTab" role="tabpanel">
                            <div class="row g-4">
                                <!-- Current Assignment Banner -->
                                <div class="col-xl-4">
                                    <div class="card border-0 shadow-sm h-100 bg-soft-primary text-primary">
                                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                                            <div>
                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                    <span class="badge bg-primary text-white">Active Assignment</span>
                                                    <i class="feather-dollar-sign fs-4 text-primary"></i>
                                                </div>
                                                @if ($activeSalaryAssignment)
                                                    <h2 class="fw-bold text-dark mb-1">
                                                        ₹{{ number_format($activeSalaryAssignment->gross_salary, 2) }}</h2>
                                                    <p class="text-muted mb-4">Gross Salary (Monthly)</p>

                                                    <ul class="list-unstyled mb-0">
                                                        <li class="mb-2 d-flex justify-content-between fs-12">
                                                            <span class="text-muted">Salary Structure:</span>
                                                            <span
                                                                class="fw-semibold text-dark">{{ $activeSalaryAssignment->salaryStructure?->name ?? 'Custom' }}</span>
                                                        </li>
                                                        <li class="mb-2 d-flex justify-content-between fs-12">
                                                            <span class="text-muted">Effective From:</span>
                                                            <span
                                                                class="fw-semibold text-dark">{{ $activeSalaryAssignment->effective_from?->format('d M, Y') ?? 'N/A' }}</span>
                                                        </li>
                                                        <li class="d-flex justify-content-between fs-12">
                                                            <span class="text-muted">Effective To:</span>
                                                            <span
                                                                class="fw-semibold text-dark">{{ $activeSalaryAssignment->effective_to?->format('d M, Y') ?? 'Ongoing' }}</span>
                                                        </li>
                                                    </ul>
                                                @else
                                                    <div class="text-center py-4 text-muted">
                                                        <p class="mb-0">No active salary structure assigned to this staff
                                                            member.</p>
                                                        <a href="{{ route('hr.staff-salary-assignments.create', $tenantKey) }}"
                                                            class="btn btn-primary btn-sm mt-3">Assign Salary</a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Salary History & Breakdown -->
                                <div class="col-xl-8">
                                    <div class="card border-0 shadow-sm mb-4">
                                        <div
                                            class="card-header border-0 bg-transparent pt-4 px-4 d-flex align-items-center justify-content-between">
                                            <h6 class="fw-bold text-dark mb-0"><i
                                                    class="feather-clock me-2 text-info"></i>Salary Assignment History</h6>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-hover align-middle mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class="px-4">Structure</th>
                                                            <th>Gross Salary</th>
                                                            <th>Effective Dates</th>
                                                            <th class="text-center">Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($salaryAssignments as $assign)
                                                            <tr>
                                                                <td class="px-4 fw-semibold text-dark">
                                                                    {{ $assign->salaryStructure?->name ?? 'Custom' }}</td>
                                                                <td>₹{{ number_format($assign->gross_salary, 2) }}</td>
                                                                <td>{{ $assign->effective_from?->format('d M Y') }} -
                                                                    {{ $assign->effective_to?->format('d M Y') ?? 'Ongoing' }}
                                                                </td>
                                                                <td class="text-center">
                                                                    <span
                                                                        class="badge bg-{{ $assign->is_active ? 'success' : 'secondary' }}">
                                                                        {{ $assign->is_active ? 'Active' : 'Inactive' }}
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="4" class="text-center py-4 text-muted">No
                                                                    assignments recorded.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payslips History -->
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header border-0 bg-transparent pt-4 px-4">
                                            <h6 class="fw-bold text-dark mb-0"><i
                                                    class="feather-file-text me-2 text-warning"></i>Payslip Ledger</h6>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-hover align-middle mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class="px-4">Payroll Month</th>
                                                            <th>Gross Salary</th>
                                                            <th>Earnings</th>
                                                            <th>Deductions</th>
                                                            <th>LOP Amount</th>
                                                            <th>Net Salary</th>
                                                            <th class="text-center">Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($payslips as $slip)
                                                            <tr>
                                                                <td class="px-4 fw-semibold text-dark">
                                                                    {{ date('F Y', mktime(0, 0, 0, $slip->payrollRun->month ?? 1, 10, $slip->payrollRun->year ?? date('Y'))) }}
                                                                </td>
                                                                <td>₹{{ number_format($slip->gross_salary, 2) }}</td>
                                                                <td class="text-success">+
                                                                    ₹{{ number_format($slip->total_earnings, 2) }}</td>
                                                                <td class="text-danger">-
                                                                    ₹{{ number_format($slip->total_deductions, 2) }}</td>
                                                                <td class="text-danger">
                                                                    ₹{{ number_format($slip->lop_amount, 2) }}</td>
                                                                <td class="fw-bold text-dark">
                                                                    ₹{{ number_format($slip->net_salary, 2) }}</td>
                                                                <td class="text-center">
                                                                    <span
                                                                        class="badge bg-soft-{{ $slip->is_published ? 'success' : 'warning' }} text-{{ $slip->is_published ? 'success' : 'warning' }}">
                                                                        {{ $slip->is_published ? 'Published' : 'Draft' }}
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="7" class="text-center py-5 text-muted">No
                                                                    payslips generated for this staff member yet.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ================= ATTENDANCE DETAILS TAB ================= -->
                        <div class="tab-pane fade" id="attendanceTab" role="tabpanel">
                            <div class="row g-4 mb-4">
                                <!-- Stats Widgets -->
                                <div class="col-6 col-md-3">
                                    <div class="p-3 rounded-3 border bg-soft-primary text-primary h-100">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div
                                                style="width:36px;height:36px;border-radius:9px;background:#dbeafe;display:flex;align-items:center;justify-content:center;">
                                                <i class="feather-activity text-primary fs-5"></i>
                                            </div>
                                            <small class="text-muted fw-bold text-uppercase"
                                                style="font-size:10px;letter-spacing:.06em;">Attendance Rate</small>
                                        </div>
                                        <div class="fw-bold text-primary" style="font-size:26px;line-height:1;">
                                            {{ $attendanceStats['rate'] }}%</div>
                                        <small class="text-muted">Ratio of present to logs</small>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="p-3 rounded-3 border bg-soft-success text-success h-100">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div
                                                style="width:36px;height:36px;border-radius:9px;background:#dcfce7;display:flex;align-items:center;justify-content:center;">
                                                <i class="feather-user-check text-success fs-5"></i>
                                            </div>
                                            <small class="text-muted fw-bold text-uppercase"
                                                style="font-size:10px;letter-spacing:.06em;">Present Days</small>
                                        </div>
                                        <div class="fw-bold text-success" style="font-size:26px;line-height:1;">
                                            {{ $attendanceStats['present'] }}</div>
                                        <small class="text-muted">Total marked present</small>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="p-3 rounded-3 border bg-soft-danger text-danger h-100">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div
                                                style="width:36px;height:36px;border-radius:9px;background:#fee2e2;display:flex;align-items:center;justify-content:center;">
                                                <i class="feather-user-x text-danger fs-5"></i>
                                            </div>
                                            <small class="text-muted fw-bold text-uppercase"
                                                style="font-size:10px;letter-spacing:.06em;">Absent Days</small>
                                        </div>
                                        <div class="fw-bold text-danger" style="font-size:26px;line-height:1;">
                                            {{ $attendanceStats['absent'] }}</div>
                                        <small class="text-muted">Total marked absent</small>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="p-3 rounded-3 border bg-soft-warning text-warning h-100">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div
                                                style="width:36px;height:36px;border-radius:9px;background:#fef3c7;display:flex;align-items:center;justify-content:center;">
                                                <i class="feather-clock text-warning fs-5"></i>
                                            </div>
                                            <small class="text-muted fw-bold text-uppercase"
                                                style="font-size:10px;letter-spacing:.06em;">Late & Half-Days</small>
                                        </div>
                                        <div class="fw-bold text-warning" style="font-size:26px;line-height:1;">
                                            {{ $attendanceStats['late'] + $attendanceStats['half_day'] }}</div>
                                        <small class="text-muted">{{ $attendanceStats['late'] }} Late ·
                                            {{ $attendanceStats['half_day'] }} Half-Day</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Logs list -->
                            <div class="card border-0 shadow-sm">
                                <div
                                    class="card-header border-0 bg-transparent pt-4 px-4 d-flex align-items-center justify-content-between">
                                    <h6 class="fw-bold text-dark mb-0"><i class="feather-list me-2 text-primary"></i>Daily
                                        Attendance Register</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light sticky-top">
                                                <tr>
                                                    <th class="px-4">Date</th>
                                                    <th>Day</th>
                                                    <th>Period</th>
                                                    <th>Status</th>
                                                    <th>Remarks / Note</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($attendanceRecords as $log)
                                                    <tr>
                                                        <td class="px-4 fw-semibold text-dark">
                                                            {{ $log->date?->format('d M Y') }}</td>
                                                        <td class="text-muted">{{ $log->date?->format('l') }}</td>
                                                        <td>{{ $log->period?->name ?? 'General' }}</td>
                                                        <td>
                                                            @php
                                                                $badgeStyle = 'secondary';
                                                                if ($log->status === 'present') {
                                                                    $badgeStyle = 'success';
                                                                } elseif ($log->status === 'absent') {
                                                                    $badgeStyle = 'danger';
                                                                } elseif ($log->status === 'late') {
                                                                    $badgeStyle = 'warning';
                                                                } elseif ($log->status === 'half_day') {
                                                                    $badgeStyle = 'info';
                                                                }
                                                            @endphp
                                                            <span class="badge bg-{{ $badgeStyle }}">
                                                                {{ ucfirst(str_replace('_', ' ', $log->status)) }}
                                                            </span>
                                                        </td>
                                                        <td class="text-muted fs-12">{{ $log->remarks ?: '—' }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center py-5 text-muted">No
                                                            attendance logs available.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ================= LEAVE DETAILS TAB ================= -->
                        <div class="tab-pane fade" id="leaveTab" role="tabpanel">
                            <!-- Balances Grid -->
                            <h6 class="fw-bold text-dark mb-3"><i class="feather-pie-chart text-primary me-2"></i>Leave
                                Balance (Year: {{ now()->year }})</h6>
                            <div class="row g-3 mb-5">
                                @forelse($leaveBalances as $balance)
                                    <div class="col-md-3">
                                        <div class="card border shadow-sm">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <h6 class="fw-bold text-dark mb-0">
                                                        {{ $balance->leaveType?->name ?? 'Leave Type' }}</h6>
                                                    <span
                                                        class="badge bg-soft-info text-info">{{ $balance->leaveType?->code ?? 'LV' }}</span>
                                                </div>
                                                <div class="row text-center mt-3">
                                                    <div class="col-4 border-end">
                                                        <h5 class="fw-bold mb-0 text-dark">{{ $balance->allocated_days }}
                                                        </h5>
                                                        <small class="text-muted fs-9 text-uppercase">Allocated</small>
                                                    </div>
                                                    <div class="col-4 border-end">
                                                        <h5 class="fw-bold mb-0 text-danger">{{ $balance->used_days }}
                                                        </h5>
                                                        <small class="text-muted fs-9 text-uppercase">Used</small>
                                                    </div>
                                                    <div class="col-4">
                                                        <h5 class="fw-bold mb-0 text-success">
                                                            {{ max(0, $balance->allocated_days - $balance->used_days) }}
                                                        </h5>
                                                        <small class="text-muted fs-9 text-uppercase">Balance</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="alert alert-light border text-muted">No leave balance initialized for
                                            the current year.</div>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Requests List -->
                            <div class="card border-0 shadow-sm">
                                <div
                                    class="card-header border-0 bg-transparent pt-4 px-4 d-flex align-items-center justify-content-between">
                                    <h6 class="fw-bold text-dark mb-0"><i class="feather-list me-2 text-info"></i>Leave
                                        Requests History</h6>
                                    <a href="{{ route('hr.leave-requests.create', $tenantKey) }}"
                                        class="btn btn-primary btn-sm no-print">Apply Leave</a>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="px-4">Leave Type</th>
                                                    <th>Date Range</th>
                                                    <th>Total Days</th>
                                                    <th>Reason</th>
                                                    <th class="text-center">Status</th>
                                                    <th>Approved By</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($leaveRequests as $req)
                                                    <tr>
                                                        <td class="px-4 fw-semibold text-dark">
                                                            {{ $req->leaveType?->name ?? 'N/A' }}</td>
                                                        <td>{{ $req->from_date?->format('d M Y') }} to
                                                            {{ $req->to_date?->format('d M Y') }}</td>
                                                        <td>
                                                            <span class="fw-bold text-dark">{{ $req->total_days }}</span>
                                                            @if ($req->half_day)
                                                                <small class="badge bg-soft-info text-info ms-1">Half
                                                                    Day</small>
                                                            @endif
                                                        </td>
                                                        <td class="text-muted fs-12">{{ $req->reason }}</td>
                                                        <td class="text-center">
                                                            @php
                                                                $statusClass = 'secondary';
                                                                if ($req->status === 'approved') {
                                                                    $statusClass = 'success';
                                                                } elseif ($req->status === 'rejected') {
                                                                    $statusClass = 'danger';
                                                                } elseif ($req->status === 'pending') {
                                                                    $statusClass = 'warning';
                                                                }
                                                            @endphp
                                                            <span
                                                                class="badge bg-{{ $statusClass }}">{{ ucfirst($req->status) }}</span>
                                                        </td>
                                                        <td>{{ $req->approver?->name ?? '—' }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center py-5 text-muted">No leave
                                                            requests logged.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ================= CLASS SCHEDULE TAB ================= -->
                        <div class="tab-pane fade" id="timetableTab" role="tabpanel">
                            <div class="card border shadow-sm">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="card-title mb-0">
                                        <i class="feather-calendar me-2 text-primary"></i>Weekly Routine
                                    </h6>
                                </div>
                                <div class="card-body p-4">
                                    <div class="table-responsive rounded shadow-sm border overflow-hidden">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                                <tr class="table-light">
                                                    <th class="text-center align-middle" width="120">PERIOD</th>
                                                    @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                                                        <th class="text-center py-3">{{ strtoupper($day) }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($regularPeriods as $period)
                                                    <tr>
                                                        <td class="text-center align-middle bg-light">
                                                            <div class="fw-bold text-dark fs-13">{{ $period->name }}</div>
                                                            <div class="fs-10 text-muted">{{ $period->start_time }} -
                                                                {{ $period->end_time }}</div>
                                                        </td>
                                                        @foreach ([1, 2, 3, 4, 5, 6] as $dayOfWeek)
                                                            @php
                                                                $slots =
                                                                    $unifiedGrid->get($period->id)?->get($dayOfWeek) ??
                                                                    collect();
                                                            @endphp
                                                            <td class="p-2 align-top" style="min-width: 140px;">
                                                                @forelse($slots as $slot)
                                                                    <div
                                                                        class="rounded p-2 mb-2 border-start border-4 border-primary shadow-xs bg-white">
                                                                        <div class="fw-bold text-dark fs-12 mb-1 text-truncate"
                                                                            title="{{ $slot->subject?->name }}">
                                                                            {{ $slot->subject?->name ?? 'N/A' }}
                                                                        </div>
                                                                        <div class="d-flex flex-wrap gap-1 mb-1">
                                                                            <span
                                                                                class="badge bg-soft-secondary text-dark border-0 px-1 py-0"
                                                                                style="font-size: 9px;">
                                                                                {{ $slot->timetable->class->name ?? 'N/A' }}
                                                                                {{ $slot->timetable->section->name ?? '' }}
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                @empty
                                                                    <div class="text-center py-3 text-muted"
                                                                        style="opacity:0.2;">-</div>
                                                                @endforelse
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="7" class="text-center py-5 text-muted">No weekly
                                                                routine slots assigned.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ================= LIBRARY TAB ================= -->
                            <div class="tab-pane fade" id="libraryTab" role="tabpanel">
                                <div class="card border shadow-sm">
                                    <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                                        <h6 class="card-title mb-0">
                                            <i class="feather-book me-2 text-primary"></i>Library Records
                                        </h6>
                                        <span class="badge bg-light text-dark">{{ $bookIssues->count() }} total records</span>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="px-4">Book Title</th>
                                                        <th>Author / ISBN</th>
                                                        <th>Issue Date</th>
                                                        <th>Due Date</th>
                                                        <th>Return Date</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($bookIssues as $book)
                                                        @php
                                                            $isOverdue =
                                                                $book->status === 'issued' &&
                                                                $book->due_date &&
                                                                \Carbon\Carbon::parse($book->due_date)->isPast();
                                                            $statusLabel = $isOverdue
                                                                ? 'Overdue'
                                                                : ucfirst($book->status);
                                                            $badgeClass = $isOverdue
                                                                ? 'danger'
                                                                : ($book->status === 'returned'
                                                                    ? 'success'
                                                                    : 'warning');
                                                        @endphp
                                                        <tr>
                                                            <td class="px-4 fw-semibold text-dark">
                                                                {{ $book->book?->title ?? '—' }}</td>
                                                            <td>
                                                                <div class="text-muted fs-12">
                                                                    {{ $book->book?->author ?? '—' }}</div>
                                                                <div class="text-muted fs-11 font-monospace">
                                                                    {{ $book->book?->isbn ?? '—' }}</div>
                                                            </td>
                                                            <td>{{ $book->issue_date ? \Carbon\Carbon::parse($book->issue_date)->format('d M Y') : '—' }}
                                                            </td>
                                                            <td>{{ $book->due_date ? \Carbon\Carbon::parse($book->due_date)->format('d M Y') : '—' }}
                                                            </td>
                                                            <td>{{ $book->return_date ? \Carbon\Carbon::parse($book->return_date)->format('d M Y') : '—' }}
                                                            </td>
                                                            <td>
                                                                <span
                                                                    class="badge bg-soft-{{ $badgeClass }} text-{{ $badgeClass }}">
                                                                    {{ $statusLabel }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="6" class="text-center py-5 text-muted">No library
                                                                records logged.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ================= DOCUMENTS TAB ================= -->
                            <div class="tab-pane fade" id="documentsTab" role="tabpanel">
                                <div class="card border shadow-sm">
                                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title mb-0">Uploaded Documents</h6>
                                            <small class="text-muted">Staff files and credentials</small>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            @forelse($staff->documents ?? [] as $doc)
                                                <div class="col-md-6 col-xl-4" id="doc-{{ $doc->id }}">
                                                    <div
                                                        class="border rounded p-3 bg-light d-flex flex-column h-100 justify-content-between">
                                                        <div>
                                                            <div
                                                                class="d-flex align-items-center justify-content-between mb-2">
                                                                <span
                                                                    class="badge bg-soft-primary text-primary">{{ $doc->doc_type }}</span>
                                                                <small
                                                                    class="text-muted">{{ $doc->created_at->format('d M Y') }}</small>
                                                            </div>
                                                            <p class="text-muted small mb-3">
                                                                {{ $doc->note ?: 'No note description' }}</p>
                                                        </div>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <a href="{{ Storage::url($doc->file_path) }}" target="_blank"
                                                                class="btn btn-sm btn-outline-primary flex-fill">
                                                                <i class="feather-eye me-1"></i>View File
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="col-12 text-center py-5 text-muted">
                                                    <i class="feather-file-text fs-1 text-muted mb-3 d-block"></i>
                                                    <h6>No documents uploaded yet</h6>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            {{-- <a href="{{ route('hr.staff.index', $tenantKey) }}"
            class="btn btn-secondary position-fixed bottom-0 end-0 m-4 shadow-lg no-print" style="z-index: 1050;">
            <i class="feather-list me-1"></i>Back to List
        </a> --}}
        </div>

        <style>
            /* Styling Navigation Pills */
            .nav-pills-custom .nav-link {
                border-radius: 6px;
                font-weight: 500;
                color: #4b5563;
                padding: 8px 16px;
                transition: all 0.2s ease;
                background: transparent;
                border: 1px solid transparent;
            }

            .nav-pills-custom .nav-link:hover {
                background: #f3f4f6;
                color: #1f2937;
            }

            .nav-pills-custom .nav-link.active {
                background: #eff6ff;
                color: #2563eb;
                border-color: #bfdbfe;
                font-weight: 600;
            }

            .bg-soft-primary {
                background-color: #eff6ff !important;
                color: #2563eb !important;
            }

            .bg-soft-success {
                background-color: #ecfdf5 !important;
                color: #059669 !important;
            }

            .bg-soft-danger {
                background-color: #fef2f2 !important;
                color: #dc2626 !important;
            }

            .bg-soft-warning {
                background-color: #fffbeb !important;
                color: #d97706 !important;
            }

            .bg-soft-info {
                background-color: #ecfeff !important;
                color: #0891b2 !important;
            }

            .bg-soft-secondary {
                background-color: #f3f4f6 !important;
                color: #4b5563 !important;
            }
        </style>
    @endsection
