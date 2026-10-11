@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2"> Attendance Period Info :</span>

                    </h5>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                        <a href="{{ route('admin.attendance_periods.index') }}" class="btn btn-secondary">
                                <i class="feather-arrow-left me-2"></i>
                                <span>Back to List</span>
                            </a>
                        </div>
                    </div>
                </div>
                @include('elements.flash')
                <div class="content-area-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
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
                                                placeholder="Period name (e.g., Period 1, Break)" required autocomplete="off">
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
                                                required autocomplete="off">
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
                                                value="{{ old('end_time', $attendancePeriod->end_time ?? '') }}" required autocomplete="off">
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
    </div>
@endsection
