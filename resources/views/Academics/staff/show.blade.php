@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')

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
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#timetableTab">
                                        <i class="feather-calendar me-1"></i>Class Schedule
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#libraryTab">
                                        <i class="feather-book me-1"></i>Library
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#documentsTab">
                                        <i class="feather-file-text me-1"></i>Documents
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#practicalTimetableTab">
                                        <i class="feather-layers me-1"></i>Practical Timetable
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="content-area-body">
                    <div class="main-content">
                        <div class="tab-content">

                            {{-- ===== OVERVIEW TAB ===== --}}
                            <div class="tab-pane fade active show" id="overviewTab">

                                {{-- QUICK STATS ROW --}}
                                <div class="row g-3 mb-4">
                                    <div class="col-6 col-md-3">
                                        <div class="card border-0 shadow-sm bg-soft-primary h-100">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-text avatar-md bg-primary text-white rounded me-3">
                                                        <i class="feather-award"></i>
                                                    </div>
                                                    <div>
                                                        <p class="text-muted fs-11 mb-0 text-uppercase fw-bold">Experience</p>
                                                        <h5 class="fw-bold mb-0 text-dark">{{ $staff->experience ?? '0' }}+ Years</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="card border-0 shadow-sm bg-soft-info h-100">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-text avatar-md bg-info text-white rounded me-3">
                                                        <i class="feather-users"></i>
                                                    </div>
                                                    <div>
                                                        <p class="text-muted fs-11 mb-0 text-uppercase fw-bold">Department</p>
                                                        <h5 class="fw-bold mb-0 text-dark">{{ $staff->department->name ?? 'Faculty' }}</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="card border-0 shadow-sm bg-soft-success h-100">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-text avatar-md bg-success text-white rounded me-3">
                                                        <i class="feather-calendar"></i>
                                                    </div>
                                                    <div>
                                                        <p class="text-muted fs-11 mb-0 text-uppercase fw-bold">Joined</p>
                                                        <h5 class="fw-bold mb-0 text-dark">{{ $staff->joining_date ? $staff->joining_date->format('M Y') : 'N/A' }}</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="card border-0 shadow-sm bg-soft-warning h-100">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-text avatar-md bg-warning text-white rounded me-3">
                                                        <i class="feather-check-circle"></i>
                                                    </div>
                                                    <div>
                                                        <p class="text-muted fs-11 mb-0 text-uppercase fw-bold">Status</p>
                                                        <h5 class="fw-bold mb-0 text-dark">{{ ucfirst($staff->status) }}</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    {{-- MAIN CONTENT (8 COLS) --}}
                                    <div class="col-xl-8">

                                        {{-- IDENTITY CARD --}}
                                        <div class="card border-0 shadow-sm mb-4 overflow-hidden">
                                            <div class="bg-primary" style="height: 100px; background-image: linear-gradient(120deg, #2563eb, #3b82f6);"></div>
                                            <div class="card-body pt-0 px-4 pb-4">
                                                <div class="d-flex align-items-end mb-4" style="margin-top: -50px;">
                                                    <div class="position-relative">
                                                        @if ($staff->photo_url)
                                                            <img src="{{ asset('storage/' . $staff->photo_url) }}"
                                                                class="rounded-circle border border-4 border-white shadow-sm"
                                                                style="width: 120px; height: 120px; object-fit: cover; background: #fff;">
                                                        @else
                                                            <div class="avatar-text avatar-xl bg-soft-primary text-primary rounded-circle border border-4 border-white shadow-sm d-flex align-items-center justify-content-center"
                                                                style="width: 120px; height: 120px; font-size: 40px; background: #fff;">
                                                                {{ substr($staff->first_name, 0, 1) }}{{ substr($staff->last_name, 0, 1) }}
                                                            </div>
                                                        @endif
                                                        <span class="position-absolute bottom-0 end-0 mb-2 me-1 p-2 bg-success border border-2 border-white rounded-circle shadow-sm" title="Active"></span>
                                                    </div>
                                                    <div class="ms-4 mb-2">
                                                        <h3 class="fw-bold text-dark mb-1">{{ $staff->first_name }} {{ $staff->last_name }}</h3>
                                                        <div class="d-flex align-items-center gap-3">
                                                            <span class="text-muted fs-13"><i class="feather-briefcase me-1 text-primary"></i> {{ $staff->designation->name ?? 'Faculty' }}</span>
                                                            <span class="text-light">|</span>
                                                            <span class="text-muted fs-13"><i class="feather-id me-1 text-info"></i> ID: {{ $staff->employee_id ?? $staff->id }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="ms-auto mb-2 no-print">
                                                        <a href="{{ route('admin.staff.edit', $staff) }}" class="btn btn-primary px-4 shadow-sm">
                                                            <i class="feather-edit-3 me-2"></i>Edit Profile
                                                        </a>
                                                    </div>
                                                </div>

                                                <hr class="opacity-10 mb-4">

                                                <div class="row g-4">
                                                    <div class="col-md-6">
                                                        <div class="d-flex align-items-start">
                                                            <div class="avatar-text avatar-sm bg-soft-light text-primary rounded me-3"><i class="feather-mail"></i></div>
                                                            <div>
                                                                <label class="text-muted fs-11 mb-1 d-block">EMAIL ADDRESS</label>
                                                                <span class="fw-semibold text-dark">{{ $staff->email ?? 'N/A' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="d-flex align-items-start">
                                                            <div class="avatar-text avatar-sm bg-soft-light text-success rounded me-3"><i class="feather-phone"></i></div>
                                                            <div>
                                                                <label class="text-muted fs-11 mb-1 d-block">CONTACT PHONE</label>
                                                                <span class="fw-semibold text-dark">{{ $staff->phone ?? 'N/A' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="d-flex align-items-start">
                                                            <div class="avatar-text avatar-sm bg-soft-light text-warning rounded me-3"><i class="feather-map-pin"></i></div>
                                                            <div>
                                                                <label class="text-muted fs-11 mb-1 d-block">CURRENT ADDRESS</label>
                                                                <span class="text-dark fs-12">{{ $staff->address ?? 'N/A' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="d-flex align-items-start">
                                                            <div class="avatar-text avatar-sm bg-soft-light text-info rounded me-3"><i class="feather-book-open"></i></div>
                                                            <div>
                                                                <label class="text-muted fs-11 mb-1 d-block">QUALIFICATION</label>
                                                                <span class="fw-bold text-dark">{{ $staff->qualification ?? 'N/A' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- PERSONAL & PROFESSIONAL SECTIONS --}}
                                        <div class="row g-4">
                                            <div class="col-md-6">
                                                <div class="card border-0 shadow-sm h-100">
                                                    <div class="card-header border-0 bg-transparent pt-4 px-4">
                                                        <h6 class="fw-bold text-dark mb-0"><i class="feather-user me-2 text-primary"></i>Personal Details</h6>
                                                    </div>
                                                    <div class="card-body p-4">
                                                        <ul class="list-group list-group-flush">
                                                            <li class="list-group-item px-0 py-2 border-0 d-flex justify-content-between">
                                                                <span class="text-muted fs-12">Gender</span>
                                                                <span class="fw-semibold text-dark">{{ ucfirst($staff->gender ?? 'N/A') }}</span>
                                                            </li>
                                                            <li class="list-group-item px-0 py-2 border-0 d-flex justify-content-between">
                                                                <span class="text-muted fs-12">Date of Birth</span>
                                                                <span class="fw-semibold text-dark">{{ $staff->dob ? $staff->dob->format('d M, Y') : 'N/A' }}</span>
                                                            </li>
                                                            <li class="list-group-item px-0 py-2 border-0 d-flex justify-content-between">
                                                                <span class="text-muted fs-12">Blood Group</span>
                                                                <span class="badge bg-soft-danger text-danger border-0">{{ $staff->blood_group ?? 'N/A' }}</span>
                                                            </li>
                                                            <li class="list-group-item px-0 py-2 border-0 d-flex justify-content-between">
                                                                <span class="text-muted fs-12">Marital Status</span>
                                                                <span class="fw-semibold text-dark">{{ $staff->marital_status ?? 'N/A' }}</span>
                                                            </li>
                                                            <li class="list-group-item px-0 py-2 border-0 d-flex justify-content-between">
                                                                <span class="text-muted fs-12">Nationality</span>
                                                                <span class="fw-semibold text-dark">{{ $staff->nationality ?? 'N/A' }}</span>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card border-0 shadow-sm h-100">
                                                    <div class="card-header border-0 bg-transparent pt-4 px-4">
                                                        <h6 class="fw-bold text-dark mb-0"><i class="feather-shield me-2 text-info"></i>Compliance & Other</h6>
                                                    </div>
                                                    <div class="card-body p-4">
                                                        <ul class="list-group list-group-flush">
                                                            <li class="list-group-item px-0 py-2 border-0 d-flex justify-content-between">
                                                                <span class="text-muted fs-12">BPUT ID</span>
                                                                <span class="fw-bold text-dark">{{ $staff->bput_id ?? 'N/A' }}</span>
                                                            </li>
                                                            <li class="list-group-item px-0 py-2 border-0 d-flex justify-content-between">
                                                                <span class="text-muted fs-12">Employment Type</span>
                                                                <span class="badge bg-soft-info text-info border-0">{{ $staff->employment_type ?? 'N/A' }}</span>
                                                            </li>
                                                            <li class="list-group-item px-0 py-2 border-0 d-flex justify-content-between">
                                                                <span class="text-muted fs-12">Joining Date</span>
                                                                <span class="fw-semibold text-dark">{{ $staff->joining_date ? $staff->joining_date->format('d M, Y') : 'N/A' }}</span>
                                                            </li>
                                                            <li class="list-group-item px-0 py-2 border-0 d-flex justify-content-between">
                                                                <span class="text-muted fs-12">Category</span>
                                                                <span class="fw-semibold text-dark">{{ $staff->category ?? 'N/A' }}</span>
                                                            </li>
                                                            <li class="list-group-item px-0 py-2 border-0 d-flex justify-content-between">
                                                                <span class="text-muted fs-12">Religion</span>
                                                                <span class="fw-semibold text-dark">{{ $staff->religion ?? 'N/A' }}</span>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- SIDEBAR CONTENT (4 COLS) --}}
                                    <div class="col-xl-4">

                                        {{-- IDENTITY CARDS --}}
                                        <div class="card border-0 shadow-sm mb-4">
                                            <div class="card-header border-0 bg-transparent pt-4 px-4">
                                                <h6 class="fw-bold text-dark mb-0"><i class="feather-hash me-2 text-primary"></i>Identity Info</h6>
                                            </div>
                                            <div class="card-body p-4 pt-0">
                                                <div class="mt-3">
                                                    <div class="p-3 bg-light rounded mb-3">
                                                        <label class="text-muted fs-10 mb-1 d-block text-uppercase fw-bold">Aadhaar Card</label>
                                                        <span class="fw-bold text-dark fs-14 letter-spacing-1">{{ $staff->aadhaar ? chunk_split($staff->aadhaar, 4, ' ') : 'N/A' }}</span>
                                                    </div>
                                                    <div class="p-3 bg-light rounded mb-3">
                                                        <label class="text-muted fs-10 mb-1 d-block text-uppercase fw-bold">PAN Card</label>
                                                        <span class="fw-bold text-dark fs-14 letter-spacing-1 text-uppercase">{{ $staff->pan ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="p-3 bg-light rounded">
                                                        <label class="text-muted fs-10 mb-1 d-block text-uppercase fw-bold">PF Number</label>
                                                        <span class="fw-bold text-dark fs-14">{{ $staff->pf_number ?? 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- BANKING CARD --}}
                                        <div class="card border-0 shadow-sm mb-4 bg-primary text-white overflow-hidden" style="background-image: linear-gradient(135deg, #1e3a8a, #2563eb);">
                                            <div class="card-body p-4">
                                                <div class="d-flex justify-content-between align-items-start mb-4">
                                                    <i class="feather-credit-card fs-1 opacity-50"></i>
                                                    <span class="badge bg-white text-primary border-0 fs-10">{{ strtoupper($staff->account_type ?? 'Savings') }}</span>
                                                </div>
                                                <div class="mb-4">
                                                    <label class="fs-9 opacity-75 mb-1 d-block">ACCOUNT NUMBER</label>
                                                    <h4 class="fw-bold mb-0 letter-spacing-2">{{ $staff->bank_account_number ?? '•••• •••• ••••' }}</h4>
                                                </div>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <label class="fs-9 opacity-75 mb-1 d-block">IFSC CODE</label>
                                                        <span class="fw-semibold fs-12">{{ $staff->ifsc ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="col-6 text-end">
                                                        <label class="fs-9 opacity-75 mb-1 d-block">BANK NAME</label>
                                                        <span class="fw-semibold fs-11 text-truncate d-block">{{ $staff->bank_name ?? 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- ADDRESS CARD --}}
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-header border-0 bg-transparent pt-4 px-4">
                                                <h6 class="fw-bold text-dark mb-0"><i class="feather-home me-2 text-success"></i>Permanent Address</h6>
                                            </div>
                                            <div class="card-body p-4">
                                                <div class="d-flex">
                                                    <i class="feather-map fs-4 text-muted me-3"></i>
                                                    <p class="mb-0 text-muted fs-12 line-height-1-5">{{ $staff->permanent_address ?? 'Not provided' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- ===== END OVERVIEW TAB ===== --}}

                            {{-- ===== TIMETABLE TAB ===== --}}
                            @include('Academics.staff.timetable')
                            {{-- ===== END TIMETABLE TAB ===== --}}

                            {{-- ===== LIBRARY TAB ===== --}}
                            <div class="tab-pane fade" id="libraryTab">
                                <div class="card stretch stretch-full">
                                    <div class="card-header d-flex align-items-center justify-content-between">
                                        <h6 class="card-title mb-0">
                                            <i class="feather-book me-2 text-primary"></i>
                                            Library Records — {{ $staff->first_name }} {{ $staff->last_name }}
                                        </h6>
                                        <span class="badge bg-light text-dark fs-12">
                                            {{ $bookIssues->count() }} total records
                                        </span>
                                    </div>

                                    <div class="card-body">

                                        {{-- Stats --}}
                                        @php
                                            $libIssuedCount   = $bookIssues->where('status', 'issued')->count();
                                            $libReturnedCount = $bookIssues->where('status', 'returned')->count();
                                            $libOverdueCount  = $bookIssues->filter(fn($b) =>
                                                $b->status === 'issued' &&
                                                $b->due_date &&
                                                \Carbon\Carbon::parse($b->due_date)->isPast()
                                            )->count();
                                            $libTotalFine = $bookIssues->sum('fine_amount');
                                            $libNextDue = $bookIssues
                                                ->filter(fn($b) => $b->status === 'issued' && $b->due_date)
                                                ->sortBy('due_date')
                                                ->first();
                                        @endphp

                                        {{-- 4 Stat Cards --}}
                                        <div class="row g-3 mb-4">
                                            <div class="col-6 col-md-3">
                                                <div class="p-3 rounded-3 border h-100" style="background:#f0f7ff;">
                                                    <div class="d-flex align-items-center gap-2 mb-2">
                                                        <div style="width:36px;height:36px;border-radius:9px;background:#dbeafe;display:flex;align-items:center;justify-content:center;">
                                                            <i class="feather-book-open text-primary" style="font-size:15px;"></i>
                                                        </div>
                                                        <small class="text-muted fw-bold text-uppercase" style="font-size:10px;letter-spacing:.06em;">Active Issues</small>
                                                    </div>
                                                    <div class="fw-bold text-primary" style="font-size:26px;line-height:1;">{{ $libIssuedCount }}</div>
                                                    <small class="text-muted">Books currently issued</small>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <div class="p-3 rounded-3 border h-100" style="background:#f0fdf4;">
                                                    <div class="d-flex align-items-center gap-2 mb-2">
                                                        <div style="width:36px;height:36px;border-radius:9px;background:#dcfce7;display:flex;align-items:center;justify-content:center;">
                                                            <i class="feather-check-circle text-success" style="font-size:15px;"></i>
                                                        </div>
                                                        <small class="text-muted fw-bold text-uppercase" style="font-size:10px;letter-spacing:.06em;">Returned</small>
                                                    </div>
                                                    <div class="fw-bold text-success" style="font-size:26px;line-height:1;">{{ $libReturnedCount }}</div>
                                                    <small class="text-muted">Books returned & cleared</small>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <div class="p-3 rounded-3 border h-100" style="background:#fff5f5;">
                                                    <div class="d-flex align-items-center gap-2 mb-2">
                                                        <div style="width:36px;height:36px;border-radius:9px;background:#fee2e2;display:flex;align-items:center;justify-content:center;">
                                                            <i class="feather-alert-circle text-danger" style="font-size:15px;"></i>
                                                        </div>
                                                        <small class="text-muted fw-bold text-uppercase" style="font-size:10px;letter-spacing:.06em;">Overdue</small>
                                                    </div>
                                                    <div class="fw-bold text-danger" style="font-size:26px;line-height:1;">{{ $libOverdueCount }}</div>
                                                    <small class="text-muted">Past due date</small>
                                                </div>
                                            </div>
                                            <!-- <div class="col-6 col-md-3">
                                                <div class="p-3 rounded-3 border h-100" style="background:#fffbeb;">
                                                    <div class="d-flex align-items-center gap-2 mb-2">
                                                        <div style="width:36px;height:36px;border-radius:9px;background:#fef3c7;display:flex;align-items:center;justify-content:center;">
                                                            <i class="feather-credit-card text-warning" style="font-size:15px;"></i>
                                                        </div>
                                                        <small class="text-muted fw-bold text-uppercase" style="font-size:10px;letter-spacing:.06em;">Fine Balance</small>
                                                    </div>
                                                    <div class="fw-bold text-warning" style="font-size:26px;line-height:1;">
                                                        Rs {{ number_format($libTotalFine, 0) }}
                                                    </div>
                                                    <small class="text-muted">Total fine accrued</small>
                                                </div>
                                            </div> -->
                                        </div>

                                        {{-- Next Due Reminder --}}
                                        @if($libNextDue)
                                            <div class="alert d-flex align-items-center gap-3 mb-4 border-0 rounded-3"
                                                 style="background:linear-gradient(135deg,#fff7ed,#fef3c7);">
                                                <div style="width:40px;height:40px;border-radius:10px;background:#fed7aa;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                                    <i class="feather-clock text-warning"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark mb-1" style="font-size:13px;">
                                                        Next Return Due:
                                                        <span class="text-warning">
                                                            {{ \Carbon\Carbon::parse($libNextDue->due_date)->format('d M Y') }}
                                                        </span>
                                                        @if(\Carbon\Carbon::parse($libNextDue->due_date)->isPast())
                                                            <span class="badge bg-danger ms-1">Overdue</span>
                                                        @elseif(\Carbon\Carbon::parse($libNextDue->due_date)->diffInDays(now()) <= 3)
                                                            <span class="badge bg-warning text-dark ms-1">Due Soon</span>
                                                        @endif
                                                    </div>
                                                    <small class="text-muted">
                                                        <i class="feather-book me-1"></i>{{ $libNextDue->book->title ?? '—' }}
                                                    </small>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Books Table --}}
                                        @if($bookIssues->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-hover align-middle mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th style="width:36px;">#</th>
                                                            <th>Book Title</th>
                                                            <th>Author / ISBN</th>
                                                            <th>Issue Date</th>
                                                            <th>Due Date</th>
                                                            <th>Return Date</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($bookIssues as $index => $book)
                                                            @php
                                                                $isOverdue = $book->status === 'issued'
                                                                    && $book->due_date
                                                                    && \Carbon\Carbon::parse($book->due_date)->isPast();
                                                                $statusLabel = $isOverdue ? 'Overdue' : ucfirst($book->status);
                                                                $badgeClass  = $isOverdue ? 'danger'
                                                                    : ($book->status === 'returned' ? 'success' : 'warning');
                                                            @endphp
                                                            <tr>
                                                                <td class="text-muted fs-12">{{ $index + 1 }}</td>
                                                                <td>
                                                                    <div class="fw-semibold text-dark">{{ $book->book->title ?? '—' }}</div>
                                                                </td>
                                                                <td>
                                                                    <div class="text-muted fs-12">{{ $book->book->author ?? '—' }}</div>
                                                                    <div class="text-muted fs-12 font-monospace">{{ $book->book->isbn ?? '—' }}</div>
                                                                </td>
                                                                <td class="fs-12 text-muted">
                                                                    {{ $book->issue_date ? \Carbon\Carbon::parse($book->issue_date)->format('d M Y') : '—' }}
                                                                </td>
                                                                <td class="fs-12 {{ $isOverdue ? 'text-danger fw-semibold' : 'text-muted' }}">
                                                                    {{ $book->due_date ? \Carbon\Carbon::parse($book->due_date)->format('d M Y') : '—' }}
                                                                    @if($isOverdue)
                                                                        <br>
                                                                        <small class="text-danger" style="font-size:10px;">
                                                                            {{ \Carbon\Carbon::parse($book->due_date)->diffForHumans() }}
                                                                        </small>
                                                                    @endif
                                                                </td>
                                                                <td class="fs-12 text-muted">
                                                                    {{ $book->return_date ? \Carbon\Carbon::parse($book->return_date)->format('d M Y') : '—' }}
                                                                </td>
                                                                <td>
                                                                    <span class="badge bg-soft-{{ $badgeClass }} text-{{ $badgeClass }}">
                                                                        <span class="me-1" style="display:inline-block;width:7px;height:7px;border-radius:50%;background:currentColor;opacity:.7;"></span>
                                                                        {{ $statusLabel }}
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    @if($libTotalFine > 0)
                                                        <tfoot class="table-light">
                                                            <tr>
                                                                <td colspan="6" class="text-end fw-bold text-muted fs-12">Total Fine:</td>
                                                                <td>
                                                                    <span class="badge bg-warning text-dark fw-bold px-2 py-1">
                                                                        Rs {{ number_format($libTotalFine, 2) }}
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        </tfoot>
                                                    @endif
                                                </table>
                                            </div>
                                        @else
                                            <div class="d-flex align-items-center justify-content-center py-5">
                                                <div class="text-center">
                                                    <div class="mb-3" style="width:64px;height:64px;border-radius:16px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;margin:auto;">
                                                        <i class="feather-book fs-3 text-muted"></i>
                                                    </div>
                                                    <h6 class="fw-semibold mb-1">No Library Records</h6>
                                                    <p class="text-muted fs-12 mb-0">This staff member has no issued or returned book records.</p>
                                                </div>
                                            </div>
                                        @endif

                                    </div>
                                </div>
                            </div>
                            {{-- ===== END LIBRARY TAB ===== --}}

                            {{-- ===== DOCUMENTS TAB ===== --}}
                            <div class="tab-pane fade" id="documentsTab">
                                <div class="card stretch stretch-full">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title mb-0">Staff Documents</h6>
                                            <small class="text-muted">Upload identity, educational & professional documents</small>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-primary" id="addDocBtn">
                                            <i class="feather-plus me-1"></i>Add Document
                                        </button>
                                    </div>
                                    <div class="card-body">

                                        <div id="documentsList">
                                            @forelse($staff->documents ?? [] as $doc)
                                            <div class="row g-3 align-items-center border rounded p-3 mb-2 bg-light" id="doc-{{ $doc->id }}">
                                                <div class="col-md-3">
                                                    <label class="form-label text-muted small">Document Type</label>
                                                    <input type="text" class="form-control form-control-sm" value="{{ $doc->doc_type }}" readonly>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label text-muted small">Note</label>
                                                    <input type="text" class="form-control form-control-sm" value="{{ $doc->note ?? '—' }}" readonly>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label text-muted small">Uploaded On</label>
                                                    <p class="mb-0 small fw-semibold">{{ $doc->created_at->format('d M Y') }}</p>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label text-muted small">File</label>
                                                    <a href="{{ Storage::url($doc->file_path) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary w-100">
                                                        <i class="feather-eye me-1"></i>View File
                                                    </a>
                                                </div>
                                                <div class="col-md-1 d-flex align-items-end justify-content-end">
                                                    <button type="button" class="btn btn-sm btn-outline-danger delete-doc-btn"
                                                            data-id="{{ $doc->id }}">
                                                        <i class="feather-trash-2"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            @empty
                                            <div class="text-center py-5" id="noDocsMsg">
                                                <i class="feather-file-text fs-1 text-muted mb-3 d-block"></i>
                                                <h6 class="text-muted">No documents uploaded yet</h6>
                                                <p class="text-muted fs-12">Click "Add Document" to upload</p>
                                            </div>
                                            @endforelse
                                        </div>

                                        <div id="newDocumentRows"></div>

                                        <div class="alert alert-light border mt-3 mb-0 py-2">
                                            <small class="text-muted">
                                                <i class="feather-info me-1 text-primary"></i>
                                                Supported formats: PDF, JPG, PNG, DOC, DOCX &nbsp;|&nbsp; Max size: 5MB per file
                                            </small>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            {{-- ===== END DOCUMENTS TAB ===== --}}
                            {{-- ===== PRACTICAL TIMETABLE TAB ===== --}}
<div class="tab-pane fade" id="practicalTimetableTab">
    <div class="card stretch stretch-full">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="card-title mb-0">
                <i class="feather-layers me-2 text-primary"></i>
                Practical Timetable — {{ $staff->first_name }} {{ $staff->last_name }}
            </h6>
        </div>

        <div class="card-body">

            @php
                $practicalSlots = \App\Models\PracticalTimetableSlot::where('tenant_id', $staff->tenant_id)
                    ->where('staff_id', $staff->id)
                    ->with([
                        'timetable.practicalGroup.groupMaster',
                        'timetable.practicalGroup.course',
                        'timetable.practicalGroup.stream',
                        'period',
                        'room',
                        'subject',
                    ])
                    ->get()
                    ->sortBy([['day_of_week', 'asc'], ['attendance_period_id', 'asc']])
                    ->values();

                $dayLabels = [
                    1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday',
                    4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday',
                ];
                $practicalByDay = $practicalSlots->groupBy('day_of_week');
            @endphp

            @if($practicalSlots->isEmpty())
                <div class="d-flex align-items-center justify-content-center py-5">
                    <div class="text-center">
                        <div class="mb-3" style="width:64px;height:64px;border-radius:16px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;margin:auto;">
                            <i class="feather-layers fs-3 text-muted"></i>
                        </div>
                        <h6 class="fw-semibold mb-1">No Practical Slots Assigned</h6>
                        <p class="text-muted fs-12 mb-0">This staff member has no practical timetable slots.</p>
                    </div>
                </div>
            @else
                {{-- Summary stats --}}
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-3 border h-100" style="background:#f0f7ff;">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div style="width:36px;height:36px;border-radius:9px;background:#dbeafe;display:flex;align-items:center;justify-content:center;">
                                    <i class="feather-layers text-primary" style="font-size:15px;"></i>
                                </div>
                                <small class="text-muted fw-bold text-uppercase" style="font-size:10px;letter-spacing:.06em;">Total Slots</small>
                            </div>
                            <div class="fw-bold text-primary" style="font-size:26px;line-height:1;">{{ $practicalSlots->count() }}</div>
                            <small class="text-muted">Across all days</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-3 border h-100" style="background:#f0fdf4;">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div style="width:36px;height:36px;border-radius:9px;background:#dcfce7;display:flex;align-items:center;justify-content:center;">
                                    <i class="feather-book text-success" style="font-size:15px;"></i>
                                </div>
                                <small class="text-muted fw-bold text-uppercase" style="font-size:10px;letter-spacing:.06em;">Subjects</small>
                            </div>
                            <div class="fw-bold text-success" style="font-size:26px;line-height:1;">{{ $practicalSlots->pluck('subject_id')->unique()->count() }}</div>
                            <small class="text-muted">Unique practical subjects</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-3 border h-100" style="background:#fff5f5;">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div style="width:36px;height:36px;border-radius:9px;background:#fee2e2;display:flex;align-items:center;justify-content:center;">
                                    <i class="feather-users text-danger" style="font-size:15px;"></i>
                                </div>
                                <small class="text-muted fw-bold text-uppercase" style="font-size:10px;letter-spacing:.06em;">Groups</small>
                            </div>
                            <div class="fw-bold text-danger" style="font-size:26px;line-height:1;">{{ $practicalSlots->pluck('timetable.practical_group_id')->unique()->count() }}</div>
                            <small class="text-muted">Practical groups</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-3 border h-100" style="background:#fffbeb;">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div style="width:36px;height:36px;border-radius:9px;background:#fef3c7;display:flex;align-items:center;justify-content:center;">
                                    <i class="feather-calendar text-warning" style="font-size:15px;"></i>
                                </div>
                                <small class="text-muted fw-bold text-uppercase" style="font-size:10px;letter-spacing:.06em;">Active Days</small>
                            </div>
                            <div class="fw-bold text-warning" style="font-size:26px;line-height:1;">{{ $practicalByDay->count() }}</div>
                            <small class="text-muted">Days with practicals</small>
                        </div>
                    </div>
                </div>

                {{-- Day-wise tables --}}
                @foreach($dayLabels as $dayNo => $dayLabel)
                    @php $daySlots = $practicalByDay->get($dayNo, collect()); @endphp
                    @if($daySlots->isNotEmpty())
                        <div class="mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="fw-semibold mb-0 text-dark">
                                    <span class="badge bg-soft-primary text-primary me-2">{{ $dayLabel }}</span>
                                </h6>
                                <small class="text-muted">{{ $daySlots->count() }} slot(s)</small>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Period</th>
                                            <th>Subject</th>
                                            <th>Group</th>
                                            <th>Room</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($daySlots as $slot)
                                            <tr>
                                                <td>
                                                    <span class="fw-semibold text-dark fs-12">{{ $slot->period->name ?? '—' }}</span>
                                                    @if($slot->period)
                                                        <div class="text-muted fs-11">{{ $slot->period->start_time }} – {{ $slot->period->end_time }}</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="fw-semibold text-dark">{{ $slot->subject->name ?? '—' }}</span>
                                                    @if($slot->subject?->code)
                                                        <div class="text-muted fs-11 font-monospace">{{ $slot->subject->code }}</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="fw-semibold text-dark fs-12">{{ $slot->timetable->practicalGroup->name ?? '—' }}</div>
                                                    @if($slot->timetable?->practicalGroup?->groupMaster)
                                                        <div class="text-muted fs-11">{{ $slot->timetable->practicalGroup->groupMaster->name }}</div>
                                                    @endif
                                                    @if($slot->timetable?->practicalGroup?->course || $slot->timetable?->practicalGroup?->stream)
                                                        <div class="text-muted fs-11">
                                                            {{ $slot->timetable->practicalGroup->course->name ?? '' }}
                                                            @if($slot->timetable?->practicalGroup?->course && $slot->timetable?->practicalGroup?->stream) • @endif
                                                            {{ $slot->timetable->practicalGroup->stream->name ?? '' }}
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-soft-secondary text-dark fs-11">
                                                        <i class="feather-map-pin me-1" style="font-size:10px;"></i>{{ $slot->room->name ?? '—' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif

        </div>
    </div>
</div>
{{-- ===== END PRACTICAL TIMETABLE TAB ===== --}}

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

    {{-- SCRIPTS --}}
    <script>
        const STAFF_ID   = {{ $staff->id }};
        const CSRF_TOKEN = '{{ csrf_token() }}';

        const DOC_TYPES = [
            '--- Identity Documents ---','Aadhaar Card','PAN Card','Passport','Voter ID','Driving License',
            '--- Educational Certificates ---','10th Marksheet','12th Marksheet','Diploma Certificate',
            'Graduation Certificate','Post Graduation Certificate','Ph.D Certificate','B.Ed Certificate',
            '--- Professional Documents ---','Resume / CV','Appointment Letter','Experience Letter',
            'Relieving Letter','Salary Slip','NOC Certificate','Character Certificate',
            '--- Other ---','Medical Certificate','Caste Certificate','Income Certificate','Other',
        ];

        let docIndex = 0;

        document.getElementById('addDocBtn').addEventListener('click', function () {
            const noMsg = document.getElementById('noDocsMsg');
            if (noMsg) noMsg.classList.add('d-none');

            const options = DOC_TYPES.map(opt => {
                if (opt.startsWith('---')) return `<option value="" disabled>── ${opt.replace(/---/g,'').trim()} ──</option>`;
                return `<option value="${opt}">${opt}</option>`;
            }).join('');

            const row = document.createElement('div');
            row.className = 'row g-3 align-items-center border rounded p-3 mb-2 new-doc-row';
            row.innerHTML = `
                <div class="col-md-3">
                    <label class="form-label text-muted small">Document Type *</label>
                    <select class="form-select form-select-sm doc-type-select" required>
                        <option value="">Select Type</option>${options}
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted small">Upload File *</label>
                    <input type="file" class="form-control form-control-sm doc-file-input"
                        accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small">Note (Optional)</label>
                    <input type="text" class="form-control form-control-sm doc-note-input" placeholder="e.g. Issued 2020">
                </div>
                <div class="col-md-2 d-flex align-items-end gap-1">
                    <button type="button" class="btn btn-sm btn-success upload-single-doc flex-fill">
                        <i class="feather-upload me-1"></i>Upload
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-new-doc-row">
                        <i class="feather-x"></i>
                    </button>
                </div>
            `;
            document.getElementById('newDocumentRows').appendChild(row);
            docIndex++;
        });

        document.addEventListener('click', function (e) {

            // Remove new doc row
            if (e.target.closest('.remove-new-doc-row')) {
                e.target.closest('.new-doc-row').remove();
            }

            // Upload single doc
            if (e.target.closest('.upload-single-doc')) {
                const btn  = e.target.closest('.upload-single-doc');
                const row  = btn.closest('.new-doc-row');
                const type = row.querySelector('.doc-type-select').value;
                const file = row.querySelector('.doc-file-input').files[0];
                const note = row.querySelector('.doc-note-input').value;

                if (!type) { alert('Please select document type.'); return; }
                if (!file) { alert('Please select a file.'); return; }

                const formData = new FormData();
                formData.append('doc_type', type);
                formData.append('file', file);
                formData.append('note', note);
                formData.append('_token', CSRF_TOKEN);

                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                fetch(`/staff/${STAFF_ID}/documents/ajax`, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const doc  = data.document;
                        const list = document.getElementById('documentsList');
                        document.getElementById('noDocsMsg')?.remove();

                        const newRow = document.createElement('div');
                        newRow.className = 'row g-3 align-items-center border rounded p-3 mb-2 bg-light';
                        newRow.id = `doc-${doc.id}`;
                        newRow.innerHTML = `
                            <div class="col-md-3">
                                <label class="form-label text-muted small">Document Type</label>
                                <input type="text" class="form-control form-control-sm" value="${doc.doc_type}" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted small">Note</label>
                                <input type="text" class="form-control form-control-sm" value="${doc.note || '—'}" readonly>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-muted small">Uploaded On</label>
                                <p class="mb-0 small fw-semibold">${doc.created_at}</p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted small">File</label>
                                <a href="${doc.file_url}" target="_blank" class="btn btn-sm btn-outline-primary w-100">
                                    <i class="feather-eye me-1"></i>View File
                                </a>
                            </div>
                            <div class="col-md-1 d-flex align-items-end justify-content-end">
                                <button type="button" class="btn btn-sm btn-outline-danger delete-doc-btn" data-id="${doc.id}">
                                    <i class="feather-trash-2"></i>
                                </button>
                            </div>
                        `;
                        list.appendChild(newRow);
                        row.remove();
                    } else {
                        alert(data.message || 'Upload failed.');
                        btn.disabled = false;
                        btn.innerHTML = '<i class="feather-upload me-1"></i>Upload';
                    }
                })
                .catch(() => {
                    alert('Something went wrong.');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="feather-upload me-1"></i>Upload';
                });
            }

            // Delete doc
            if (e.target.closest('.delete-doc-btn')) {
                const btn   = e.target.closest('.delete-doc-btn');
                const docId = btn.dataset.id;
                if (!confirm('Delete this document?')) return;

                fetch(`/staff/${STAFF_ID}/documents/${docId}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Content-Type': 'application/json' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) document.getElementById(`doc-${docId}`)?.remove();
                });
            }
        });
    </script>

    <a href="{{ route('admin.staff.index') }}"
        class="btn btn-secondary position-fixed bottom-0 end-0 m-4 shadow-lg no-print" style="z-index: 1050;">
        <i class="feather-list me-1"></i>Back to List
    </a>
@endsection