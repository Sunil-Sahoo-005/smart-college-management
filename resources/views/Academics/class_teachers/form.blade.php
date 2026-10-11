@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2"> Class Teacher Info :</span>

                    </h5>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                        <a href="{{ route('admin.class_teachers.index') }}" class="btn btn-secondary">
                                <i class="feather-arrow-left me-2"></i>
                                <span>Back to List</span>
                            </a>
                        </div>
                    </div>
                </div>
                @include('elements.flash')
                <div class="content-area-body">
                    <form action="{{ route('admin.class_teachers.store') }}" method="POST">
                        @csrf
                        @if (!empty($classTeacher->id))
                            <input type="hidden" name="id" value="{{ $classTeacher->id }}">
                        @endif

                        <div class="card stretch stretch-full">
                            <div class="card-body general-info">
                                <div class="mb-5 d-flex align-items-center justify-content-between">
                                    <h5 class="fw-bold mb-0 me-4">
                                        <span class="d-block mb-2">Semester Teacher Info :</span>
                                        <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                            Assign a teacher to a semester–section for an academic year
                                        </span>
                                    </h5>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="stream_id" class="fw-semibold">Branch:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-hash"></i></div>
                                            <select class="form-select @error('class_id') is-invalid @enderror"
                                                id="class_id" name="stream_id" required>
                                                <option value="">Select Branch</option>
                                                @foreach ($streams as $stream)
                                                    <option value="{{ $stream->id }}"
                                                        {{ old('stream_id', $classTeacher->stream_id ?? '') == $stream->id ? 'selected' : '' }}
                                                        >
                                                        {{ $stream->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('class_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="class_id" class="fw-semibold">Semester:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-hash"></i></div>
                                            <select class="form-select @error('class_id') is-invalid @enderror"
                                                id="class_id" name="class_id" required>
                                                <option value="">Select Semester</option>
                                                @foreach ($classes as $class)
                                                    <option value="{{ $class->id }}"
                                                        {{ old('class_id', $classTeacher->class_id ?? '') == $class->id ? 'selected' : '' }}>
                                                        {{ $class->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('class_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="section_id" class="fw-semibold">Section:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-hash"></i></div>
                                            <select class="form-select @error('section_id') is-invalid @enderror"
                                                id="section_id" name="section_id" required>
                                                <option value="">Select Section</option>
                                                @foreach ($sections as $section)
                                                    <option value="{{ $section->id }}"
                                                        {{ old('section_id', $classTeacher->section_id ?? '') == $section->id ? 'selected' : '' }}>
                                                        {{ $section->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('section_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="staff_id" class="fw-semibold">Staff:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-user"></i></div>
                                            <select class="form-select @error('staff_id') is-invalid @enderror"
                                                id="staff_id" name="staff_id" required>
                                                <option value="">Select Staff</option>
                                                @foreach ($staff as $member)
                                                    <option value="{{ $member->id }}"
                                                        {{ old('staff_id', $classTeacher->staff_id ?? '') == $member->id ? 'selected' : '' }}>
                                                        {{ $member->first_name }} {{ $member->last_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('staff_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="academic_year_id" class="fw-semibold">Academic Year:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-calendar"></i></div>
                                            <select class="form-select @error('academic_year_id') is-invalid @enderror"
                                                id="academic_year_id" name="academic_year_id" required>
                                                <option value="">Select Academic Year</option>
                                                @foreach ($academicYears as $academicYear)
                                                    <option value="{{ $academicYear->id }}"
                                                        {{ old('academic_year_id', $classTeacher->academic_year_id ?? '') == $academicYear->id ? 'selected' : '' }}>
                                                        {{ $academicYear->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('academic_year_id')
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
