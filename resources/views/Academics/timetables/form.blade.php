@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">
                            {{ !empty($timetable->id) ? 'Edit Timetable' : 'Create Timetable' }}
                        </span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                            <a href="{{ route('admin.timetables.index') }}" class="btn btn-secondary">
                                <i class="feather-arrow-left me-2"></i>
                                <span>Back to List</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="content-area-body">
                    @include('elements.flash')
                    <div class="card stretch stretch-full">
                        <div class="card-body general-info">
                            <div class="mb-5 d-flex align-items-center justify-content-between">
                                <h5 class="fw-bold mb-0 me-4">
                                    <span class="d-block mb-2">Timetable Info :</span>
                                    <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                        Define timetable for a class‑section‑academic‑year‑term
                                    </span>
                                </h5>
                                <button type="submit" form="timetableForm" class="btn btn-primary">Save</button>
                            </div>

                            <form id="timetableForm" action="{{ route('admin.timetables.store') }}" method="POST">
                                @csrf
                                @if (!empty($timetable->id))
                                    <input type="hidden" name="id" value="{{ $timetable->id }}">
                                @endif

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="class_id" class="fw-semibold">Class:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-hash"></i></div>
                                            <select class="form-select @error('stream_id') is-invalid @enderror"
                                                id="stream_id" name="stream_id" required>
                                                <option value="">Select Stream</option>
                                                @foreach ($streams as $stream)
                                                    <option value="{{ $stream->id }}"
                                                        {{ old('stream_id', $timetable->stream_id ?? '') == $stream->id ? 'selected' : '' }}>
                                                        {{ $stream->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('stream_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="class_id" class="fw-semibold">Class:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-hash"></i></div>
                                            <select class="form-select @error('class_id') is-invalid @enderror"
                                                id="class_id" name="class_id" required>
                                                <option value="">Select Class</option>
                                                @foreach ($classes as $class)
                                                    <option value="{{ $class->id }}"
                                                        {{ old('class_id', $timetable->class_id ?? '') == $class->id ? 'selected' : '' }}>
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
                                                        {{ old('section_id', $timetable->section_id ?? '') == $section->id ? 'selected' : '' }}>
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
                                        <label for="academic_year_id" class="fw-semibold">Academic Year:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-calendar"></i></div>
                                            <select class="form-select @error('academic_year_id') is-invalid @enderror"
                                                id="academic_year_id" name="academic_year_id" required>
                                                <option value="">Select Academic Year</option>
                                                @foreach ($academicYears as $year)
                                                    <option value="{{ $year->id }}"
                                                        {{ old('academic_year_id', $timetable->academic_year_id ?? '') == $year->id ? 'selected' : '' }}>
                                                        {{ $year->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('academic_year_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="term_id" class="fw-semibold">Term (Optional):</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-calendar"></i></div>
                                            <select class="form-select @error('term_id') is-invalid @enderror"
                                                id="term_id" name="term_id">
                                                <option value="">No Term</option>
                                                @foreach ($terms as $term)
                                                    <option value="{{ $term->id }}"
                                                        {{ old('term_id', $timetable->term_id ?? '') == $term->id ? 'selected' : '' }}>
                                                        {{ $term->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('term_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="name" class="fw-semibold">Name:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-book"></i></div>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name"
                                                value="{{ old('name', $timetable->name ?? '') }}"
                                                placeholder="Timetable Name" required>
                                        </div>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
