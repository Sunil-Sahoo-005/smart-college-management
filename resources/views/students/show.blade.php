@extends('admin_layout')

@section('content')

    <div class="nxl-content">
        <!-- [ page-header ] start -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Student</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}">Students</a></li>
                    <li class="breadcrumb-item">{{ $student->first_name }} {{ $student->last_name }}</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <div class="page-header-right-items">
                    <div class="d-flex d-md-none">
                        <a href="javascript:void(0)" class="page-header-right-close-toggle">
                            <i class="feather-arrow-left me-2"></i>
                            <span>Back</span>
                        </a>
                    </div>
                    <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                        <div class="dropdown">
                            <a class="btn btn-icon btn-light-brand" data-bs-toggle="dropdown" data-bs-offset="0, 10" data-bs-auto-close="outside">
                                <i class="feather-more-vertical"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="{{ route('admin.students.edit', $student) }}" class="dropdown-item">
                                    <i class="feather-edit me-3"></i>
                                    <span>Edit Student</span>
                                </a>
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <i class="feather-copy me-3"></i>
                                    <span>Copy Student</span>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <i class="feather-eye me-3"></i>
                                    <span>Student View</span>
                                </a>
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <i class="feather-cast me-3"></i>
                                    <span>Export Student</span>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="javascript:void(0);" class="dropdown-item text-danger">
                                    <i class="feather-trash-2 me-3"></i>
                                    <span>Delete Student</span>
                                </a>
                            </div>
                        </div>
                        <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
                            <i class="feather-plus me-2"></i>
                            <span>Create Student</span>
                        </a>
                    </div>
                </div>
                <div class="d-md-none d-flex align-items-center">
                    <a href="javascript:void(0)" class="page-header-right-open-toggle">
                        <i class="feather-align-right fs-20"></i>
                    </a>
                </div>
            </div>
        </div>
        <!-- [ page-header ] end -->

        <div class="bg-white py-3 border-bottom rounded-0 p-md-0 mb-0">
            <div class="d-flex align-items-center justify-content-between">
                <div class="nav-tabs-wrapper page-content-left-sidebar-wrapper">
                    <ul class="nav nav-tabs nav-tabs-custom-style" id="studentTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#overviewTab">Overview</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#parentsTab">Parents</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#addressTab">Address</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#documentsTab">Documents</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#attendanceTab">Attendance</button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- [ Main Content ] start -->
        <div class="main-content">
            <div class="tab-content">
                <!-- Overview Tab -->
                <div class="tab-pane fade active show" id="overviewTab">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card stretch stretch-full">
                                <div class="card-body task-header d-md-flex align-items-center justify-content-between">
                                    <div class="me-4">
                                        <h4 class="mb-4 fw-bold d-flex">
                                            <span class="text-truncate-1-line">
                                                {{ $student->first_name }} {{ $student->last_name }}
                                                <span class="badge bg-soft-{{ $student->status == 'active' ? 'success' : ($student->status == 'transfer_out' ? 'warning' : 'secondary') }} text-{{ $student->status == 'active' ? 'success' : ($student->status == 'transfer_out' ? 'warning' : 'secondary') }} mx-3">
                                                    {{ ucfirst($student->status) }}
                                                </span>
                                            </span>
                                        </h4>
                                        <div class="d-flex align-items-center">
                                            <span class="text-muted fs-12">
                                                Admission #: {{ $student->admission_number }}
                                            </span>
                                            <span class="vr mx-3 text-muted"></span>
                                            <span class="text-muted fs-12">
                                                Roll #: {{ $student->roll_number ?? 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-8">
                            <div class="card stretch stretch-full">
                                <div class="card-header">
                                    <h6 class="card-title">Student Details</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label">Admission Number</label>
                                            <p>{{ $student->admission_number }}</p>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label">Roll Number</label>
                                            <p>{{ $student->roll_number ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label">Name</label>
                                            <p>{{ $student->first_name }} {{ $student->last_name }}</p>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label">Gender</label>
                                            <p>{{ ucfirst($student->gender ?? 'N/A') }}</p>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label">Date of Birth</label>
                                            <p>{{ $student->dob ? $student->dob->format('Y-m-d') : 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label">Blood Group</label>
                                            <p>{{ $student->blood_group ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label">Religion</label>
                                            <p>{{ $student->religion ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label">Nationality</label>
                                            <p>{{ $student->nationality ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label">Aadhaar</label>
                                            <p>{{ $student->aadhaar ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label">Status</label>
                                            <p>{{ ucfirst($student->status) }}</p>
                                        </div>
                                        <div class="col-md-12 mb-4">
                                            <label class="form-label">Medical Conditions</label>
                                            <p>{{ $student->medical_conditions ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-12 mb-4">
                                            <label class="form-label">Allergies</label>
                                            <p>{{ $student->allergies ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4">
                            <div class="card stretch stretch-full">
                                <div class="card-body text-center">
                                    @if($student->photo_url)
                                        <img src="{{ $student->photo_url }}" class="img-fluid rounded mb-3" style="max-height: 180px;" alt="Student Photo">
                                    @else
                                        <div class="avatar-text avatar-xl bg-soft-primary text-primary mb-3">
                                            <i class="feather-user"></i>
                                        </div>
                                    @endif
                                    <h6>{{ $student->first_name }} {{ $student->last_name }}</h6>
                                    <p class="text-muted fs-12">
                                        Admission #: {{ $student->admission_number }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Parents Tab -->
                 @include('students.parents')
                <!-- Address Tab -->
                @include('students.address')
                <!-- Documents Tab -->
                @include('students.documents')
                <!-- Attendance Tab -->
                <div class="tab-pane fade" id="attendanceTab">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-center" style="height: calc(100vh - 315px)">
                                <div class="text-center">
                                    <h2 class="fs-16 fw-semibold">No attendance yet!</h2>
                                    <p class="fs-12 text-muted">Attendance data will be added later.</p>
                                    <a href="javascript:void(0);" class="avatar-text bg-soft-primary text-primary mx-auto" data-bs-toggle="tooltip" title="Add Attendance">
                                        <i class="feather-plus"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>

@endsection
