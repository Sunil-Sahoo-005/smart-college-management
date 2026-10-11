@extends('admin_layout')
@section('content')
    <style>
        .content-area-header {
            overflow: hidden;
        }

        .content-area-header .student-tabs-row {
            width: 100%;
            min-width: 0;
        }

        .content-area-header .student-tabs-scroll {
            display: block;
            width: 100%;
            max-width: 100%;
            min-width: 0;
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scroll-behavior: smooth;
        }

        .content-area-header .student-tabs-scroll::-webkit-scrollbar {
            height: 6px;
        }

        .content-area-header .student-tabs-scroll::-webkit-scrollbar-thumb {
            background: rgba(108, 117, 125, 0.45);
            border-radius: 999px;
        }

        .content-area-header #studentTabs {
            display: flex;
            flex-wrap: nowrap;
            width: max-content;
            min-width: max-content;
            margin-bottom: 0;
            white-space: nowrap;
        }

        .content-area-header #studentTabs .nav-item {
            flex: 0 0 auto;
        }

        .content-area-header #studentTabs .nav-link {
            white-space: nowrap;
        }
    </style>
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <div class="student-tabs-row">
                        <div class="nav-tabs-wrapper page-content-left-sidebar-wrapper student-tabs-scroll">
                            <ul class="nav nav-tabs nav-tabs-custom-style" id="studentTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#overviewTab">
                                        Overview
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#enrollmentTab">
                                        Enrollment
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#parentsTab">
                                        Parents
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#addressTab">
                                        Address
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#documentsTab">
                                        Documents
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#qualificationsTab">
                                        Qualifications
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#duesTab">
                                        Manage Dues
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#attendanceTab">
                                        Attendance
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#timetableTab">
                                        Timetable
                                    </button>
                                </li>

                                @php
                                    $residenceTypeForTabs = $enrollments->first()?->residence_type;
                                @endphp

                                @if ($residenceTypeForTabs !== 'hosteller')
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#transportTab">
                                            Transport
                                        </button>
                                    </li>
                                @endif

                                @if ($residenceTypeForTabs !== 'day_scholar')
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#hostelTab">
                                            Hostel
                                        </button>
                                    </li>
                                @endif

                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#bankTab">
                                        Bank Details
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#noteTab">
                                        Note
                                    </button>
                                </li>
                                {{-- ===== LIBRARY TAB BUTTON ===== --}}
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#libraryTab">
                                        <i class="feather-book me-1"></i>Library
                                    </button>
                                </li>
                                {{-- ===== END LIBRARY TAB BUTTON ===== --}}
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#examMarksTab">
                                        Exam Marks
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="content-area-body">
                    <div class="row g-4 align-items-start">

                        {{-- LEFT SIDEBAR: Student Card --}}
                        <div class="col-12 col-xl-3">
                            <div class="position-sticky" style="top: 90px;">
                                <div class="card stretch stretch-full">
                                    <div class="card-body text-center">
                                        @if ($student->photo_url)
                                            <div class="mb-4 position-relative">
                                                <img src="{{ asset('storage/' . $student->photo_url) }}"
                                                    class="img-fluid rounded-4 shadow-lg border mb-3"
                                                    style="max-height: 200px; width: 100%; object-fit: cover;"
                                                    alt="{{ $student->first_name }} {{ $student->last_name }}">
                                            </div>
                                        @else
                                            <div class="avatar-text avatar-xxl bg-gradient-primary text-white mb-4 mx-auto">
                                                <div class="d-flex align-items-center justify-content-center"
                                                    style="width: 180px; height: 180px;">
                                                    <i class="feather-user fs-1"></i>
                                                </div>
                                            </div>
                                        @endif

                                        <h4 class="mb-2 fw-bold">{{ $student->first_name }} {{ $student->last_name }}</h4>
                                        <div class="d-flex align-items-center justify-content-center gap-3 mb-3 flex-wrap">
                                            <span class="badge bg-light text-dark fs-12 px-3 py-2">
                                                <i class="feather-hash me-1"></i>{{ $student->admission_number }}
                                            </span>
                                            <span class="badge bg-soft-{{ $student->status == 'active' ? 'success' : ($student->status == 'transfer_out' ? 'warning' : 'secondary') }} text-{{ $student->status == 'active' ? 'success' : ($student->status == 'transfer_out' ? 'warning' : 'secondary') }} fs-12 px-3 py-2">
                                                {{ ucfirst($student->status) }}
                                            </span>
                                        </div>
                                        <div class="row g-3 text-center">
                                            <div class="col-6">
                                                <div class="fw-semibold fs-4 text-primary">
                                                    {{ $student->dob ? $student->dob->age : 'N/A' }}
                                                </div>
                                                <small class="text-muted">Age</small>
                                            </div>
                                            <div class="col-6">
                                                <div class="fw-semibold fs-4 text-info">
                                                    {{ ucfirst($student->gender ?? 'N/A') }}
                                                </div>
                                                <small class="text-muted">Gender</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- RIGHT: Tab Content --}}
                        <div class="col-12 col-xl-9">
                            <div class="tab-content">

                                {{-- ===== OVERVIEW TAB ===== --}}
                                <div class="tab-pane fade active show" id="overviewTab">
                                    @php $currentEnrollment = $enrollments->first(); @endphp
                                    <div class="row">
                                        <div class="card stretch stretch-full">
                                            <div class="card-header d-flex align-items-center justify-content-between">
                                                <h6 class="card-title mb-0">
                                                    <i class="feather-user me-2 text-primary"></i>Overview
                                                </h6>
                                                <a href="{{ route('admin.students.edit', $student->id) }}" class="btn btn-light btn-sm">
                                                    <i class="feather-edit-3 me-1"></i>Edit
                                                </a>
                                            </div>
                                            <div class="card-body">

                                                {{-- Personal Info --}}
                                                <div class="table-responsive mb-4">
                                                    <table class="table table-sm table-bordered align-middle overview-table mb-0">
                                                        <tbody>
                                                            <tr>
                                                                <th>Admission Number</th>
                                                                <td>{{ $student->admission_number }}</td>
                                                                <th>Roll Number</th>
                                                                <td>{{ $student->roll_number ?? 'N/A' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Full Name</th>
                                                                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                                                <th>Status</th>
                                                                <td>
                                                                    <span class="badge bg-soft-{{ $student->status == 'active' ? 'success' : ($student->status == 'transfer_out' ? 'warning' : 'secondary') }} text-{{ $student->status == 'active' ? 'success' : ($student->status == 'transfer_out' ? 'warning' : 'secondary') }}">
                                                                        {{ ucfirst(str_replace('_', ' ', $student->status)) }}
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>Gender</th>
                                                                <td>{{ ucfirst($student->gender ?? 'N/A') }}</td>
                                                                <th>Date of Birth</th>
                                                                <td>{{ $student->dob ? $student->dob->format('d M Y') . ' (' . $student->dob->age . ' yrs)' : 'N/A' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Blood Group</th>
                                                                <td>{{ $student->blood_group ?? 'N/A' }}</td>
                                                                <th>Category</th>
                                                                <td>{{ $student->category ? strtoupper($student->category) : 'N/A' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Religion</th>
                                                                <td>{{ $student->religion ?? 'N/A' }}</td>
                                                                <th>Nationality</th>
                                                                <td>{{ $student->nationality ?? 'N/A' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Aadhaar</th>
                                                                <td>{{ $student->aadhaar ?? 'N/A' }}</td>
                                                                <th>Email</th>
                                                                <td>{{ $student->email ?? 'N/A' }}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>

                                                {{-- Academic Snapshot --}}
                                                <h6 class="fw-bold mb-3">
                                                    <i class="feather-book-open me-2 text-success"></i>Academic Snapshot
                                                </h6>
                                                <div class="table-responsive mb-4">
                                                    <table class="table table-sm table-bordered align-middle overview-table mb-0">
                                                        <tbody>
                                                            <tr>
                                                                <th>Academic Year</th>
                                                                <td>{{ $currentEnrollment?->academicYear?->name ?? 'N/A' }}</td>
                                                                <th>Course</th>
                                                                <td>{{ $currentEnrollment?->course?->name ?? 'N/A' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Semester/Class</th>
                                                                <td>{{ $currentEnrollment?->class?->name ?? 'N/A' }}</td>
                                                                <th>Section</th>
                                                                <td>{{ $currentEnrollment?->section?->name ?? 'N/A' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Branch/Stream</th>
                                                                <td>{{ $currentEnrollment?->stream?->name ?? 'N/A' }}</td>
                                                                <th>Enrollment Status</th>
                                                                <td>{{ $currentEnrollment ? ucfirst($currentEnrollment->status) : 'N/A' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Student Type</th>
                                                                <td>{{ $currentEnrollment ? ucfirst($currentEnrollment->student_type ?? 'N/A') : 'N/A' }}</td>
                                                                <th>Residence Type</th>
                                                                <td>{{ $currentEnrollment ? ucfirst(str_replace('_', ' ', $currentEnrollment->residence_type ?? 'N/A')) : 'N/A' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Date of Admission</th>
                                                                <td>{{ $currentEnrollment?->dateof_admission ? \Carbon\Carbon::parse($currentEnrollment->dateof_admission)->format('d M Y') : 'N/A' }}</td>
                                                                <th>Date of Leaving</th>
                                                                <td>{{ $currentEnrollment?->date_of_leaving ? \Carbon\Carbon::parse($currentEnrollment->date_of_leaving)->format('d M Y') : 'N/A' }}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>

                                                {{-- Health & Notes --}}
                                                <h6 class="fw-bold mb-3">
                                                    <i class="feather-heart me-2 text-danger"></i>Health & Notes
                                                </h6>
                                                <div class="table-responsive mb-4">
                                                    <table class="table table-sm table-bordered align-middle overview-table mb-0">
                                                        <tbody>
                                                            <tr>
                                                                <th>Medical Conditions</th>
                                                                <td>{{ $student->medical_conditions ?? 'N/A' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Allergies</th>
                                                                <td>{{ $student->allergies ?? 'N/A' }}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>

                                                {{-- Academic Qualifications --}}
                                                <h6 class="fw-bold mb-3">
                                                    <i class="feather-book me-2 text-warning"></i>Academic Qualifications
                                                </h6>
                                                @if($academicQualifications->isNotEmpty())
                                                    <div class="table-responsive mb-4">
                                                        <table class="table table-sm table-bordered align-middle overview-table mb-0">
                                                            <thead>
                                                                <tr>
                                                                    <th>Level</th>
                                                                    <th>Board / University</th>
                                                                    <th>School / College</th>
                                                                    <th>Year</th>
                                                                    <th>%</th>
                                                                    <th>Grade</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($academicQualifications as $qual)
                                                                    <tr>
                                                                        <td>
                                                                            <span class="badge bg-soft-primary text-primary">
                                                                                {{ ucfirst(str_replace('_', ' ', $qual->qualification_level)) }}
                                                                            </span>
                                                                        </td>
                                                                        <td>{{ $qual->board_university ?? '—' }}</td>
                                                                        <td>{{ $qual->school_college ?? '—' }}</td>
                                                                        <td>{{ $qual->passing_year ?? '—' }}</td>
                                                                        <td>{{ $qual->percentage ? $qual->percentage . '%' : '—' }}</td>
                                                                        <td>{{ $qual->grade ?? '—' }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @else
                                                    <p class="text-muted small mb-4">No qualifications added yet.
                                                        <a href="#" onclick="document.querySelector('[data-bs-target=\'#qualificationsTab\']').click(); return false;" class="text-primary">Add now →</a>
                                                    </p>
                                                @endif

                                                @if ($student->status == 'transfer_out')
                                                    <div class="mt-2">
                                                        <div class="alert alert-warning">
                                                            <label class="form-label fw-bold">Transfer Out Reason</label>
                                                            <p class="mb-0">{{ $student->transfer_out_reason ?? 'No reason provided' }}</p>
                                                        </div>
                                                    </div>
                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- ===== END OVERVIEW TAB ===== --}}

                                {{-- Other tabs --}}
                                @include('Academics.student.attendance')
                                @include('Academics.student.notes')
                                @include('Academics.student.timetable')

                                @if (($currentEnrollment?->residence_type ?? null) !== 'hosteller')
                                    @include('Academics.student.transport')
                                @endif
                                @if (($currentEnrollment?->residence_type ?? null) !== 'day_scholar')
                                    @include('Academics.student.hostel')
                                @endif

                                @include('Academics.student.parents')
                                @include('Academics.student.address')
                                @include('Academics.student.document')
                                @include('Academics.student.academic_qualifications')
                                @include('Academics.student.dues')

                                {{-- ===== BANK DETAILS TAB ===== --}}
                                <div class="tab-pane fade" id="bankTab">
                                    <div class="row justify-content-center">
                                        <div class="card stretch stretch-full">
                                            <div class="card-header d-flex align-items-center justify-content-between">
                                                <h6 class="card-title mb-0">
                                                    <i class="feather-dollar-sign me-2 text-primary"></i>Bank Account Details
                                                </h6>
                                                <a href="{{ route('admin.students.edit', $student->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="feather-edit-2 me-1"></i>Edit
                                                </a>
                                            </div>
                                            <div class="card-body">
                                                @if ($student->account_number)
                                                    <div class="row g-4">
                                                        <div class="col-md-6">
                                                            <div class="d-flex align-items-start gap-3">
                                                                <div class="flex-shrink-0 bg-soft-primary text-primary" style="width:42px;height:42px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                                                                    <i class="feather-user"></i>
                                                                </div>
                                                                <div>
                                                                    <small class="text-muted d-block mb-1">Account Holder Name</small>
                                                                    <span class="fw-semibold">{{ $student->account_holder_name ?? 'N/A' }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="d-flex align-items-start gap-3">
                                                                <div class="flex-shrink-0 bg-soft-info text-info" style="width:42px;height:42px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                                                                    <i class="feather-briefcase"></i>
                                                                </div>
                                                                <div>
                                                                    <small class="text-muted d-block mb-1">Bank Name</small>
                                                                    <span class="fw-semibold">{{ $student->bank_name ?? 'N/A' }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="d-flex align-items-start gap-3">
                                                                <div class="flex-shrink-0 bg-soft-warning text-warning" style="width:42px;height:42px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                                                                    <i class="feather-map-pin"></i>
                                                                </div>
                                                                <div>
                                                                    <small class="text-muted d-block mb-1">Branch Name</small>
                                                                    <span class="fw-semibold">{{ $student->branch_name ?? 'N/A' }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="d-flex align-items-start gap-3">
                                                                <div class="flex-shrink-0 bg-soft-success text-success" style="width:42px;height:42px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                                                                    <i class="feather-layers"></i>
                                                                </div>
                                                                <div>
                                                                    <small class="text-muted d-block mb-1">Account Type</small>
                                                                    <span class="badge bg-soft-success text-success px-3 py-1">
                                                                        {{ ucfirst($student->account_type ?? 'N/A') }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="d-flex align-items-start gap-3">
                                                                <div class="flex-shrink-0 bg-soft-primary text-primary" style="width:42px;height:42px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                                                                    <i class="feather-credit-card"></i>
                                                                </div>
                                                                <div>
                                                                    <small class="text-muted d-block mb-1">Account Number</small>
                                                                    <span class="fw-semibold font-monospace" id="accNumDisplay">
                                                                        ●●●● ●●●● {{ substr($student->account_number, -4) }}
                                                                    </span>
                                                                    <button type="button" class="btn btn-link btn-sm p-0 ms-2 text-muted"
                                                                        id="toggleAccNum"
                                                                        data-full="{{ $student->account_number }}"
                                                                        data-masked="●●●● ●●●● {{ substr($student->account_number, -4) }}"
                                                                        data-visible="0">
                                                                        <i class="feather-eye" id="toggleAccIcon"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="d-flex align-items-start gap-3">
                                                                <div class="flex-shrink-0 bg-soft-danger text-danger" style="width:42px;height:42px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                                                                    <i class="feather-hash"></i>
                                                                </div>
                                                                <div>
                                                                    <small class="text-muted d-block mb-1">IFSC Code</small>
                                                                    <span class="fw-semibold font-monospace text-uppercase">
                                                                        {{ $student->ifsc_code ?? 'N/A' }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="d-flex align-items-center justify-content-center py-5">
                                                        <div class="text-center">
                                                            <div class="mb-3" style="width:64px;height:64px;border-radius:16px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;margin:auto;">
                                                                <i class="feather-dollar-sign fs-3 text-muted"></i>
                                                            </div>
                                                            <h6 class="fw-semibold mb-1">No Bank Details Found</h6>
                                                            <p class="text-muted fs-12 mb-3">Bank account information has not been added yet.</p>
                                                            <a href="{{ route('admin.students.edit', $student->id) }}" class="btn btn-primary btn-sm">
                                                                <i class="feather-plus me-1"></i>Add Bank Details
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- ===== END BANK TAB ===== --}}

                                {{-- ===== ENROLLMENT TAB ===== --}}
                                <div class="tab-pane fade" id="enrollmentTab">
                                    @php $currentEnrollment = $enrollments->first(); @endphp
                                    <div class="row justify-content-center">
                                        <div class="card mb-4">
                                            <div class="card-header d-flex align-items-center justify-content-between">
                                                <h6 class="card-title mb-0">
                                                    <i class="feather-book-open me-2 text-success"></i>Current Enrollment
                                                </h6>
                                                <span class="badge bg-{{ $currentEnrollment ? 'success' : 'secondary' }}">
                                                    {{ $currentEnrollment ? ucfirst($currentEnrollment->status) : 'Not Enrolled' }}
                                                </span>
                                            </div>
                                            <div class="card-body">
                                                @if ($currentEnrollment)
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered align-middle overview-table mb-0">
                                                            <tbody>
                                                                <tr>
                                                                    <th>Academic Year</th>
                                                                    <td>{{ $currentEnrollment->academicYear?->name ?? 'N/A' }}</td>
                                                                    <th>Course</th>
                                                                    <td>{{ $currentEnrollment->course?->name ?? 'N/A' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Branch</th>
                                                                    <td>{{ $currentEnrollment->stream?->name ?? 'N/A' }}</td>
                                                                    <th>Semester/Class</th>
                                                                    <td>{{ $currentEnrollment->class?->name ?? 'N/A' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Section</th>
                                                                    <td>{{ $currentEnrollment->section?->name ?? 'N/A' }}</td>
                                                                    <th>Type</th>
                                                                    <td>{{ ucfirst($currentEnrollment->student_type ?? 'N/A') }} / {{ ucfirst(str_replace('_', ' ', $currentEnrollment->residence_type ?? 'N/A')) }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Admission Date</th>
                                                                    <td>{{ $currentEnrollment->dateof_admission ? \Carbon\Carbon::parse($currentEnrollment->dateof_admission)->format('d M Y') : 'N/A' }}</td>
                                                                    <th>Leaving Date</th>
                                                                    <td>{{ $currentEnrollment->date_of_leaving ? \Carbon\Carbon::parse($currentEnrollment->date_of_leaving)->format('d M Y') : 'N/A' }}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @else
                                                    <div class="text-center py-4 text-muted">
                                                        <i class="feather-book-open d-block mb-2" style="font-size: 2rem;"></i>
                                                        No active enrollment found.
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="card stretch stretch-full mb-4">
                                            <div class="card-header d-flex align-items-center justify-content-between">
                                                <h6 class="card-title mb-0">
                                                    <i class="feather-trending-up me-2 text-warning"></i>Promotion History
                                                </h6>
                                                <span class="badge bg-light text-dark fs-12">{{ $promotionHistory->count() }} records</span>
                                            </div>
                                            <div class="card-body p-0">
                                                @if ($promotionHistory->count() > 0)
                                                    <div class="table-responsive">
                                                        <table class="table table-hover mb-0">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Academic Year</th>
                                                                    <th>From</th>
                                                                    <th>To</th>
                                                                    <th>GPA</th>
                                                                    <th>Status</th>
                                                                    <th>Remarks</th>
                                                                    <th>Date</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($promotionHistory as $promotion)
                                                                    <tr>
                                                                        <td><strong>{{ $promotion->academicYear?->name ?? 'N/A' }}</strong></td>
                                                                        <td>
                                                                            <div class="fw-semibold">{{ $promotion->from_class_name ?? 'N/A' }}</div>
                                                                            <small class="text-muted">{{ $promotion->from_stream_name ?? 'N/A' }} {{ $promotion->from_section_name ? '/ ' . $promotion->from_section_name : '' }}</small>
                                                                        </td>
                                                                        <td>
                                                                            <div class="fw-semibold">{{ $promotion->to_class_name ?? 'N/A' }}</div>
                                                                            <small class="text-muted">{{ $promotion->to_stream_name ?? 'N/A' }} {{ $promotion->to_section_name ? '/ ' . $promotion->to_section_name : '' }}</small>
                                                                        </td>
                                                                        <td>
                                                                            <span class="badge bg-light text-dark">
                                                                                {{ $promotion->student_gpa ?? 'N/A' }}
                                                                                @if ($promotion->gpa_required)/ req {{ $promotion->gpa_required }}@endif
                                                                            </span>
                                                                        </td>
                                                                        <td>
                                                                            <span class="badge bg-{{ $promotion->status === 'promoted' ? 'success' : ($promotion->status === 'repeated' ? 'warning' : 'danger') }}">
                                                                                {{ ucfirst($promotion->status) }}
                                                                            </span>
                                                                        </td>
                                                                        <td>{{ $promotion->remarks ?? 'N/A' }}</td>
                                                                        <td>{{ $promotion->created_at?->format('d M Y') ?? 'N/A' }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @else
                                                    <div class="text-center py-5">
                                                        <i class="feather-trending-up text-muted mb-3" style="font-size: 3rem;"></i>
                                                        <h5 class="text-muted mb-2">No Promotion Records</h5>
                                                        <p class="text-muted mb-0">Promotions will appear here once this student is promoted or repeated.</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- ===== END ENROLLMENT TAB ===== --}}

                                {{-- ===== NOTES TAB ===== --}}
                                <div class="tab-pane fade" id="noteTab">
                                    <div class="card stretch stretch-full">
                                        <div class="card-header d-flex align-items-center justify-content-between">
                                            <h6 class="card-title mb-0">
                                                <i class="feather-file-text me-2 text-primary"></i>
                                                Notes — {{ $student->first_name }} {{ $student->last_name }}
                                            </h6>
                                            <button class="btn btn-primary btn-sm" id="showNoteFormBtn">
                                                <i class="feather-plus me-1"></i>Add Note
                                            </button>
                                        </div>
                                        <div class="card-body">

                                            {{-- ADD/EDIT FORM (default hidden) --}}
                                            <div id="noteFormWrapper" style="display:none;" class="mb-4">
                                                <form action="{{ route('admin.student.records.store', $student->id) }}"
                                                      method="POST" enctype="multipart/form-data" id="noteForm">
                                                    @csrf
                                                    <input type="hidden" name="id" id="noteRecordId" value="">

                                                    <div class="row g-3">
                                                        <div class="col-md-8">
                                                            <label class="form-label fw-semibold">Message</label>
                                                            <textarea name="message" id="noteMessage" class="form-control" rows="5"
                                                                placeholder="Write a note..."></textarea>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label fw-semibold">Upload PDF</label>
                                                            <input type="file" name="file_path" class="form-control" accept=".pdf">
                                                            <small class="text-muted">PDF only, max 5MB</small>
                                                            <div id="currentFileWrapper" class="mt-2" style="display:none;">
                                                                <span class="text-muted small">Current file: </span>
                                                                <a id="currentFileLink" href="#" target="_blank"
                                                                   class="btn btn-sm btn-outline-danger mt-1">
                                                                    <i class="feather-file me-1"></i>View PDF
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex gap-2 mt-3">
                                                        <button type="submit" class="btn btn-primary btn-sm">
                                                            <i class="feather-save me-1"></i>
                                                            <span id="noteSubmitLabel">Save Note</span>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-secondary btn-sm" id="cancelNoteBtn">
                                                            Cancel
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>

                                            {{-- NOTES LIST --}}
                                            @if ($records->count())
                                                <div class="table-responsive">
                                                    <table class="table table-hover align-middle">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Message</th>
                                                                <th>File</th>
                                                                <th>Date</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($records as $index => $record)
                                                                <tr>
                                                                    <td>{{ $records->firstItem() + $index }}</td>
                                                                    <td>{{ $record->message ?? '—' }}</td>
                                                                    <td>
                                                                        @if ($record->file_path)
                                                                            <a href="{{ asset('storage/' . $record->file_path) }}"
                                                                               target="_blank" class="btn btn-sm btn-outline-danger">
                                                                                <i class="feather-file me-1"></i>View PDF
                                                                            </a>
                                                                        @else
                                                                            <span class="text-muted">—</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ $record->created_at->format('d M Y, h:i A') }}</td>
                                                                    <td>
                                                                        <div class="d-flex gap-2">
                                                                            <button type="button" class="btn btn-sm btn-outline-primary edit-note-btn"
                                                                                data-id="{{ $record->id }}"
                                                                                data-message="{{ $record->message }}"
                                                                                data-file="{{ $record->file_path ? asset('storage/' . $record->file_path) : '' }}">
                                                                                <i class="feather-edit-2 me-1"></i>Edit
                                                                            </button>
                                                                            <form action="{{ route('admin.student.records.destroy', $record->id) }}"
                                                                                  method="POST" class="d-inline">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                                    onclick="return confirm('Delete this note?')">
                                                                                    <i class="feather-trash-2 me-1"></i>Delete
                                                                                </button>
                                                                            </form>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="mt-3">{{ $records->links() }}</div>
                                            @else
                                                <div class="text-center py-5">
                                                    <div class="mb-3" style="width:64px;height:64px;border-radius:16px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;margin:auto;">
                                                        <i class="feather-inbox fs-3 text-muted"></i>
                                                    </div>
                                                    <h6 class="fw-semibold mb-1">No Notes Found</h6>
                                                    <p class="text-muted fs-12 mb-3">Abhi tak koi note add nahi hua.</p>
                                                </div>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                                {{-- ===== END NOTES TAB ===== --}}
{{-- ===== LIBRARY TAB ===== --}}
<div class="tab-pane fade" id="libraryTab">
    <div class="card stretch stretch-full">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="card-title mb-0">
                <i class="feather-book me-2 text-primary"></i>
                Library Records — {{ $student->first_name }} {{ $student->last_name }}
            </h6>
            <span class="badge bg-light text-dark fs-12">
                {{ $bookIssues->count() }} total records
            </span>
        </div>

        <div class="card-body">

            {{-- ── Stats Row ── --}}
            
           @php
    $libIssuedCount   = $bookIssues->where('status', 'issued')->count();
    $libReturnedCount = $bookIssues->where('status', 'returned')->count();
    $libOverdueCount  = $bookIssues->filter(fn($b) =>
        $b->status === 'issued' &&
        $b->due_date &&
        \Carbon\Carbon::parse($b->due_date)->isPast()
    )->count();
    $libIssueFine = $bookIssues->sum('fine_amount');
    $libBookFine  = ($bookFines ?? collect())->sum('amount');
    $libTotalFine = $libIssueFine + $libBookFine;
    $libNextDue = $bookIssues
        ->filter(fn($b) => $b->status === 'issued' && $b->due_date)
        ->sortBy('due_date')
        ->first();
@endphp

            {{-- ── 4 Stat Cards ── --}}
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
                <div class="col-6 col-md-3">
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
</div>
            </div>

            {{-- ── Next Due Reminder ── --}}
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

            {{-- ── Books Issue Table ── --}}
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
                        @if($libIssueFine > 0)
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="6" class="text-end fw-bold text-muted fs-12">Issue Fines Subtotal:</td>
                                    <td colspan="2">
                                        <span class="badge bg-warning text-dark fw-bold px-2 py-1">
                                            Rs {{ number_format($libIssueFine, 2) }}
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
                        <p class="text-muted fs-12 mb-0">This student has no issued or returned book records.</p>
                    </div>
                </div>
            @endif

            {{-- ── Additional / Manual Book Fines ── --}}
            @if(isset($bookFines) && $bookFines->count() > 0)
                <h6 class="fw-bold mt-4 mb-3">
                    <i class="feather-alert-circle me-2 text-danger"></i>Additional Fines
                </h6>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Reason</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookFines as $fineIndex => $fine)
                                <tr>
                                    <td class="text-muted fs-12">{{ $fineIndex + 1 }}</td>
                                    <td>{{ $fine->reason ?? '—' }}</td>
                                    <td>
                                        <span class="badge bg-soft-danger text-danger fw-bold px-2 py-1">
                                            Rs {{ number_format($fine->amount, 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-soft-{{ ($fine->status ?? 'unpaid') === 'paid' ? 'success' : 'danger' }} text-{{ ($fine->status ?? 'unpaid') === 'paid' ? 'success' : 'danger' }}">
                                            {{ ucfirst($fine->status ?? 'unpaid') }}
                                        </span>
                                    </td>
                                    <td class="text-muted fs-12">
                                        {{ $fine->created_at?->format('d M Y') ?? '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="2" class="text-end fw-bold text-muted fs-12">Additional Fines Subtotal:</td>
                                <td colspan="3">
                                    <span class="badge bg-danger fw-bold px-2 py-1">
                                        Rs {{ number_format($libBookFine, 2) }}
                                    </span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif

            {{-- ── Grand Total Fine (agar dono sources se fine ho) ── --}}
            @if($libTotalFine > 0)
                <div class="d-flex justify-content-end mt-3">
                    <div class="p-3 rounded-3 border" style="background:#fffbeb; min-width:220px;">
                        <div class="d-flex justify-content-between align-items-center gap-3">
                            <span class="fw-bold text-muted fs-12 text-uppercase">Grand Total Fine</span>
                            <span class="fw-bold text-warning" style="font-size:18px;">
                                Rs {{ number_format($libTotalFine, 2) }}
                            </span>
                        </div>
                        @if($libIssueFine > 0 && $libBookFine > 0)
                            <div class="text-muted mt-1" style="font-size:11px;">
                                Issue fines: Rs {{ number_format($libIssueFine, 2) }}
                                + Manual fines: Rs {{ number_format($libBookFine, 2) }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
{{-- ===== END LIBRARY TAB ===== --}}

                                {{-- ===== EXAM MARKS TAB ===== --}}
                                <div class="tab-pane fade" id="examMarksTab">
                                    <div class="card stretch stretch-full">
                                        <div class="card-header d-flex align-items-center justify-content-between">
                                            <h6 class="card-title mb-0">
                                                <i class="feather-award me-2 text-primary"></i>Exam Marks
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            @if ($allExams->isEmpty())
                                                <div class="d-flex align-items-center justify-content-center py-5">
                                                    <div class="text-center">
                                                        <div class="mb-3" style="width:64px;height:64px;border-radius:16px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;margin:auto;">
                                                            <i class="feather-award fs-3 text-muted"></i>
                                                        </div>
                                                        <h6 class="fw-semibold mb-1">No Exams Found</h6>
                                                        <p class="text-muted fs-12 mb-0">No exams have been created for this student's class.</p>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="table-responsive">
                                                    <table class="table table-hover mb-0">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Exam Name</th>
                                                                <th>Subject</th>
                                                                <th>Exam Date</th>
                                                                <th>Total Marks</th>
                                                                <th>Pass Marks</th>
                                                                <th>Marks Obtained</th>
                                                                <th>Grade</th>
                                                                <th>Status</th>
                                                                <th>Remarks</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($allExams as $exam)
                                                                @php $mark = $examMarks[$exam->id] ?? null; @endphp
                                                                <tr>
                                                                    <td><strong>{{ $exam->name }}</strong></td>
                                                                    <td>{{ $exam->subject->name ?? 'N/A' }}</td>
                                                                    <td>{{ $exam->exam_date ? \Carbon\Carbon::parse($exam->exam_date)->format('d M Y') : 'N/A' }}</td>
                                                                    <td>{{ $exam->total_marks }}</td>
                                                                    <td>{{ $exam->pass_marks }}</td>
                                                                    <td>{{ $mark ? $mark->marks_obtained : '—' }}</td>
                                                                    <td>
                                                                        @if($mark)
                                                                            @php
                                                                                $grade = $exam->gradeLevels
                                                                                    ->where('min_score', '<=', $mark->marks_obtained)
                                                                                    ->where('max_score', '>=', $mark->marks_obtained)
                                                                                    ->first();
                                                                            @endphp
                                                                            <span class="badge bg-soft-{{ $mark->marks_obtained >= $exam->pass_marks ? 'success' : 'danger' }} text-{{ $mark->marks_obtained >= $exam->pass_marks ? 'success' : 'danger' }}">
                                                                                {{ $grade?->grade_letter ?? 'N/A' }}
                                                                            </span>
                                                                        @else
                                                                            <span class="badge bg-soft-secondary text-secondary">—</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if($mark)
                                                                            <span class="badge bg-soft-{{ $mark->marks_obtained >= $exam->pass_marks ? 'success' : 'danger' }} text-{{ $mark->marks_obtained >= $exam->pass_marks ? 'success' : 'danger' }}">
                                                                                {{ $mark->marks_obtained >= $exam->pass_marks ? 'Passed' : 'Failed' }}
                                                                            </span>
                                                                        @else
                                                                            <span class="badge bg-soft-warning text-warning">Not Entered</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ $mark ? ($mark->remarks ?? '—') : '—' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                {{-- ===== END EXAM MARKS TAB ===== --}}

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <a href="{{ route('admin.students.index') }}"
            class="btn btn-secondary position-fixed bottom-0 end-0 m-4 shadow-lg no-print" style="z-index: 1050;">
            <i class="feather-list me-1"></i>Back to List
        </a>
    </div>

    <style>
        .overview-table th {
            width: 18%;
            background: #f8fafc;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            vertical-align: middle;
            white-space: nowrap;
        }
        .overview-table td {
            color: #111827;
            font-weight: 500;
            vertical-align: middle;
        }
        .overview-table td,
        .overview-table th {
            padding: 0.85rem 1rem;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const formWrapper  = document.getElementById('noteFormWrapper');
            const showBtn      = document.getElementById('showNoteFormBtn');
            const cancelBtn    = document.getElementById('cancelNoteBtn');
            const recordId     = document.getElementById('noteRecordId');
            const message      = document.getElementById('noteMessage');
            const submitLabel  = document.getElementById('noteSubmitLabel');
            const fileWrapper  = document.getElementById('currentFileWrapper');
            const fileLink     = document.getElementById('currentFileLink');

            if (showBtn) {
                showBtn.addEventListener('click', function () {
                    recordId.value          = '';
                    message.value           = '';
                    submitLabel.textContent = 'Save Note';
                    fileWrapper.style.display = 'none';
                    formWrapper.style.display = 'block';
                    formWrapper.scrollIntoView({ behavior: 'smooth' });
                });
            }

            if (cancelBtn) {
                cancelBtn.addEventListener('click', function () {
                    formWrapper.style.display = 'none';
                });
            }

            document.querySelectorAll('.edit-note-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    recordId.value          = this.dataset.id;
                    message.value           = this.dataset.message;
                    submitLabel.textContent = 'Update Note';
                    if (this.dataset.file) {
                        fileLink.href             = this.dataset.file;
                        fileWrapper.style.display = 'block';
                    } else {
                        fileWrapper.style.display = 'none';
                    }
                    formWrapper.style.display = 'block';
                    formWrapper.scrollIntoView({ behavior: 'smooth' });
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('toggleAccNum');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function () {
                    const display   = document.getElementById('accNumDisplay');
                    const icon      = document.getElementById('toggleAccIcon');
                    const isVisible = this.dataset.visible === '1';
                    if (isVisible) {
                        display.textContent    = this.dataset.masked;
                        icon.className         = 'feather-eye';
                        this.dataset.visible   = '0';
                    } else {
                        display.textContent    = this.dataset.full;
                        icon.className         = 'feather-eye-off';
                        this.dataset.visible   = '1';
                    }
                });
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabKey   = 'studentProfileActiveTab';
            const studentTabsScroll = document.querySelector('.student-tabs-scroll');
            const tabLinks = document.querySelectorAll('#studentTabs .nav-link');
            const lastTab  = localStorage.getItem(tabKey);

            const scrollTabIntoView = (tab) => {
                if (!tab || !studentTabsScroll) {
                    return;
                }

                tab.scrollIntoView({
                    behavior: 'smooth',
                    inline: 'center',
                    block: 'nearest'
                });
            };

            if (studentTabsScroll) {
                studentTabsScroll.addEventListener('wheel', function (event) {
                    if (Math.abs(event.deltaY) > Math.abs(event.deltaX)) {
                        event.preventDefault();
                        studentTabsScroll.scrollLeft += event.deltaY;
                    }
                }, { passive: false });
            }

            if (lastTab) {
                const trigger = document.querySelector(`#studentTabs .nav-link[data-bs-target="${lastTab}"]`);
                if (trigger) {
                    new bootstrap.Tab(trigger).show();
                    scrollTabIntoView(trigger);
                }
            }
            tabLinks.forEach(link => {
                link.addEventListener('shown.bs.tab', function (e) {
                    localStorage.setItem(tabKey, e.target.getAttribute('data-bs-target'));
                    scrollTabIntoView(e.target);
                });
            });

            const activeTab = document.querySelector('#studentTabs .nav-link.active');
            if (activeTab) {
                scrollTabIntoView(activeTab);
            }
        });
    </script>
@endsection
