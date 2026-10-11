{{-- @extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Accountants.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Generate Fee Schedule</span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <a href="{{ route('admin.fee_structures.index') }}" class="btn btn-secondary">
                            Back to List
                        </a>
                    </div>
                </div>

                <div class="content-area-body">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.fee_structures.generate.store', $feeStructure) }}">
                                @csrf

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Fee Structure</label>
                                        <div class="form-control-plaintext">
                                            {{ $feeStructure->name }}
                                            ({{ $feeStructure->stream?->name ?? 'N/A' }} – {{ $feeStructure->class?->name ?? 'N/A' }})
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Frequency</label>
                                        <select name="frequency" class="form-select" required>
                                            <option value="monthly">Monthly</option>
                                            <option value="quarterly">Quarterly</option>
                                            <option value="termly">Termly</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <i class="feather-info me-2"></i>
                                    This will generate fee installments for <strong>{{ $studentPlans->count() }}</strong>
                                    students assigned to this fee structure.
                                    Students who already have installments for this period will be skipped.
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    Generate for All Students
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection --}}


@extends('admin_layout')

@section('content')
    <div class="nxl-content px-4">
        <!-- [ Page Header ] start -->
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <div class="page-header-left">
                <div class="page-header-title">
                    <h5 class="m-b-10">Generate Fee Schedule</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.fee_structures.index') }}">Fee Structures</a></li>
                    <li class="breadcrumb-item active">Generation</li>
                </ul>
            </div>
            <div class="page-header-right">
                <a href="{{ route('admin.fee_structures.index') }}" class="btn btn-outline-secondary">
                    <i class="feather-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
        <!-- [ Page Header ] end -->

        <div class="main-content">
            @include('elements.flash')
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-10">
                    <div class="card border-0 shadow-sm overflow-hidden">
                        <div class="card-header bg-primary text-white py-4 px-4" style="background: linear-gradient(135deg, #4481eb 0%, #04befe 100%);">
                            <h5 class="fw-bold mb-0 text-white"><i class="feather-settings me-2"></i>Generation Wizard</h5>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST" action="{{ route('admin.fee_structures.generate.store', $feeStructure) }}">
                                @csrf

                                <div class="mb-4 p-3 bg-light-soft rounded border border-light">
                                    <div class="row align-items-center">
                                        <div class="col-sm-2 text-center text-primary">
                                            <i class="feather-layers fs-1"></i>
                                        </div>
                                        <div class="col-sm-10">
                                            <h6 class="text-uppercase fs-11 fw-bold text-muted mb-1 text-primary">Target Structure</h6>
                                            <h5 class="mb-0 fw-bold">{{ $feeStructure->name }}</h5>
                                            <p class="mb-0 text-muted fs-13">
                                                Branch: <strong>{{ $feeStructure->stream?->name ?? 'N/A' }}</strong> | Session: <strong>{{ $feeStructure->academicYear->name ?? 'Default' }}</strong>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info border-info-20 bg-soft-info d-flex align-items-center py-3 mb-4">
                                    <div class="flex-shrink-0 avatar-sm bg-soft-info text-info rounded-circle d-flex align-items-center justify-content-center me-3">
                                        <i class="feather-users fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-0 fs-14">
                                            This action will generate fee installments for <strong>{{ $studentPlans->count() }}</strong> student(s) currently assigned to this structure.
                                        </p>
                                        <small class="text-muted">Students who already have installments for this period will be automatically skipped to prevent duplicates.</small>
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg shadow-sm py-3">
                                        <i class="feather-play-circle me-2"></i>Generate Installments Now
                                    </button>
                                    <p class="text-center text-muted fs-12 mt-2">
                                        <i class="feather-shield-check me-1"></i> Process is irreversible but safe for existing data.
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <div class="mt-4 text-center">
                         <div class="fs-13 text-muted">
                            <i class="feather-info me-1"></i> Missing students? Assign them to this structure first in <strong>Student Management</strong>.
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-light-soft { background-color: #f8f9fa; }
        .bg-soft-info { background-color: rgba(10, 179, 156, 0.05) !important; color: #0ab39c; }
        .border-info-20 { border-color: rgba(10, 179, 156, 0.2) !important; }
        .bg-soft-success { background-color: rgba(28, 202, 184, 0.1) !important; color: #1ccab8; }
        .border-success-20 { border-color: rgba(28, 202, 184, 0.2) !important; }
        .bg-soft-warning { background-color: rgba(255, 184, 34, 0.1) !important; color: #ffb822; }
        .border-warning-20 { border-color: rgba(255, 184, 34, 0.2) !important; }
        .bg-soft-secondary { background-color: rgba(144, 155, 166, 0.1) !important; color: #909ba6; }
        .border-secondary-20 { border-color: rgba(144, 155, 166, 0.2) !important; }
    </style>
@endsection
