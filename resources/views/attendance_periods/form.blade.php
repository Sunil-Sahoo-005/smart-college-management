@extends('admin_layout')

@section('content')
    <div class="nxl-content">
        <!-- [ page-header ] start -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">
                        {{ !empty($attendancePeriod) ? 'Edit Attendance Period' : 'Create Attendance Period' }}
                    </h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.attendance_periods.index') }}">Attendance Periods</a>
                    </li>
                    <li class="breadcrumb-item">
                        {{ !empty($attendancePeriod) ? 'Edit Period' : 'Create Period' }}
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
                        <a href="{{ route('admin.attendance_periods.index') }}" class="btn btn-secondary">
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

        <!-- [ Main Content ] start -->
        <div class="main-content">
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('admin.attendance_periods.store') }}" method="POST">
                        @csrf
                        @if (!empty($attendancePeriod->id))
                            <input type="hidden" name="id" value="{{ $attendancePeriod->id }}">
                        @endif

                        <div class="card stretch stretch-full">
                            <div class="card-body general-info">
                                <div class="mb-5 d-flex align-items-center justify-content-between">
                                    <h5 class="fw-bold mb-0 me-4">
                                        <span class="d-block mb-2">Attendance Period Info :</span>
                                        <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                            Define a period for attendance (e.g., Period 1, Break)
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
                                                value="{{ old('name', $attendancePeriod->name ?? '') }}"
                                                placeholder="Period name (e.g., Period 1, Break)" required>
                                        </div>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="start_time" class="fw-semibold">Start Time:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-clock"></i></div>
                                            <input type="time"
                                                class="form-control @error('start_time') is-invalid @enderror"
                                                id="start_time" name="start_time"
                                                value="{{ old('start_time', $attendancePeriod->start_time ?? '') }}"
                                                required>
                                        </div>
                                        @error('start_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="end_time" class="fw-semibold">End Time:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-clock"></i></div>
                                            <input type="time"
                                                class="form-control @error('end_time') is-invalid @enderror" id="end_time"
                                                name="end_time"
                                                value="{{ old('end_time', $attendancePeriod->end_time ?? '') }}" required>
                                        </div>
                                        @error('end_time')
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
