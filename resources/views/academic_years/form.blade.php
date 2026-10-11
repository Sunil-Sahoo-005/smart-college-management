@extends('admin_layout')

@section('content')
    <div class="nxl-content">
        <!-- [ page-header ] start -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">
                        {{ !empty($academicYear) ? 'Edit Academic Year' : 'Create Academic Year' }}
                    </h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.academic_years.index') }}">Academic Years</a></li>
                    <li class="breadcrumb-item">
                        {{ !empty($academicYear) ? 'Edit Academic Year' : 'Create Academic Year' }}
                    </li>
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
                        <a href="{{ route('admin.academic_years.index') }}" class="btn btn-secondary">
                            <i class="feather-arrow-left me-2"></i>
                            <span>Back to List</span>
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
@include('elements.flash')
        <!-- [ Main Content ] start -->
        <div class="main-content">
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('admin.academic_years.store') }}" method="POST">
                        @csrf
                        @if (!empty($academicYear->id))
                            <input type="hidden" name="id" value="{{ $academicYear->id }}">
                        @endif

                        <div class="card stretch stretch-full">
                            <div class="card-body general-info">
                                <div class="mb-5 d-flex align-items-center justify-content-between">
                                    <h5 class="fw-bold mb-0 me-4">
                                        <span class="d-block mb-2">Academic Year Info :</span>
                                        <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                            Basic details of the academic year
                                        </span>
                                    </h5>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="name" class="fw-semibold">Name:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-hash"></i></div>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name"
                                                value="{{ old('name', $academicYear->name ?? '') }}"
                                                placeholder="Name (e.g., 2025-26)" required>
                                        </div>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="start_date" class="fw-semibold">Start Date:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-calendar"></i></div>
                                            <input type="date"
                                                class="form-control @error('start_date') is-invalid @enderror"
                                                id="start_date" name="start_date"
                                                value="{{ old('start_date', $academicYear?->start_date?->format('Y-m-d')) }}"
                                                {{-- value="{{ old('start_date', $academicYear->start_date?->format('Y-m-d') ?? '') }}" --}} required>
                                        </div>
                                        @error('start_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="end_date" class="fw-semibold">End Date:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-calendar"></i></div>
                                            <input type="date"
                                                class="form-control @error('end_date') is-invalid @enderror" id="end_date"
                                                name="end_date"
                                                value="{{ old('end_date', $academicYear?->end_date?->format('Y-m-d') ?? '') }}"
                                                required>
                                        </div>
                                        @error('end_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="is_current" class="fw-semibold">Current Year:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="is_current"
                                                name="is_current" value="1"
                                                {{ old('is_current', $academicYear->is_current ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_current">
                                                Set as current academic year
                                            </label>
                                        </div>
                                        @error('is_current')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
@endsection
