@extends('teacher_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
           
            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">
                            {{ !empty($exam) ? 'Edit Exam' : 'New Exam' }}
                        </span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                            <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary">
                                <i class="feather-arrow-left me-2"></i>
                                <span>Back to List</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="content-area-body">
                    <form action="{{ route('admin.exams.store') }}" method="POST">
                        @csrf
                        @if (!empty($exam))
                            <input type="hidden" name="id" value="{{ $exam->id }}">
                        @endif

                        <div class="card stretch stretch-full">
                            <div class="card-body general-info">
                                <div class="mb-5 d-flex align-items-center justify-content-between">
                                    <h5 class="fw-bold mb-0 me-4">
                                        <span class="d-block mb-2">Exam Info :</span>
                                        <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                            Define an exam for a specific class–section–subject with date, time, and marks.
                                        </span>
                                    </h5>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="exam_type_id" class="fw-semibold">Exam Type:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-list"></i></div>
                                            <select class="form-select @error('exam_type_id') is-invalid @enderror"
                                                id="exam_type_id" name="exam_type_id" required>
                                                <option value="">Select Exam Type</option>
                                                @foreach ($examTypes as $type)
                                                    <option value="{{ $type->id }}"
                                                        {{ old('exam_type_id', $exam->exam_type_id ?? '') == $type->id ? 'selected' : '' }}>
                                                        {{ $type->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('exam_type_id')
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
                                            <div class="input-group-text"><i class="feather-tag"></i></div>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name" value="{{ old('name', $exam->name ?? '') }}"
                                                placeholder="Exam Name" required>
                                        </div>
                                        @error('name')
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
                                                        {{ old('class_id', $exam->class_id ?? '') == $class->id ? 'selected' : '' }}>
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
                                                        {{ old('section_id', $exam->section_id ?? '') == $section->id ? 'selected' : '' }}>
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
                                        <label for="subject_id" class="fw-semibold">Subject:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-book"></i></div>
                                            <select class="form-select @error('subject_id') is-invalid @enderror"
                                                id="subject_id" name="subject_id" required>
                                                <option value="">Select Subject</option>
                                                @foreach ($subjects as $subject)
                                                    <option value="{{ $subject->id }}"
                                                        {{ old('subject_id', $exam->subject_id ?? '') == $subject->id ? 'selected' : '' }}>
                                                        {{ $subject->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('subject_id')
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
                                                        {{ old('academic_year_id', $exam->academic_year_id ?? '') == $year->id ? 'selected' : '' }}>
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
                                        <label for="term_id" class="fw-semibold">Term:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-calendar"></i></div>
                                            <select class="form-select @error('term_id') is-invalid @enderror"
                                                id="term_id" name="term_id">
                                                <option value="">Select Term (optional)</option>
                                                @foreach ($terms as $term)
                                                    <option value="{{ $term->id }}"
                                                        {{ old('term_id', $exam->term_id ?? '') == $term->id ? 'selected' : '' }}>
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
                                        <label for="exam_date" class="fw-semibold">Exam Date:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-calendar"></i></div>
                                            <input type="date"
                                                class="form-control @error('exam_date') is-invalid @enderror"
                                                id="exam_date" name="exam_date"
                                                value="{{ old('exam_date', $exam->exam_date ?? '') }}" required>
                                        </div>
                                        @error('exam_date')
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
                                                value="{{ old('start_time', $exam->start_time ?? '') }}" required>
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
                                                class="form-control @error('end_time') is-invalid @enderror"
                                                id="end_time" name="end_time"
                                                value="{{ old('end_time', $exam->end_time ?? '') }}" required>
                                        </div>
                                        @error('end_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="total_marks" class="fw-semibold">Total Marks:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-award"></i></div>
                                            <input type="number"
                                                class="form-control @error('total_marks') is-invalid @enderror"
                                                id="total_marks" name="total_marks"
                                                value="{{ old('total_marks', $exam->total_marks ?? '') }}" min="1"
                                                required>
                                        </div>
                                        @error('total_marks')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="pass_marks" class="fw-semibold">Pass Marks:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-award"></i></div>
                                            <input type="number"
                                                class="form-control @error('pass_marks') is-invalid @enderror"
                                                id="pass_marks" name="pass_marks"
                                                value="{{ old('pass_marks', $exam->pass_marks ?? '') }}" min="0"
                                                required>
                                        </div>
                                        @error('pass_marks')
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
