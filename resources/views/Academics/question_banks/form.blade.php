@extends('admin_layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Academics.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">
            <div class="content-area-header bg-white sticky-top">
                <h5 class="fw-bold mb-0 me-4">
                    <span class="d-block mb-2">
                        {{ !empty($questionBank->id) ? 'Edit Question Bank' : 'New Question Bank' }}
                    </span>
                </h5>
                <div class="page-header-right ms-auto">
                    <a href="{{ route('admin.question_banks.index') }}" class="btn btn-secondary">
                        <i class="feather-arrow-left me-2"></i>
                        <span>Back to List</span>
                    </a>
                </div>
            </div>

            @include('elements.flash')

            <div class="content-area-body">
                <form action="{{ route('admin.question_banks.store') }}" method="POST">
                    @csrf
                    @if (!empty($questionBank->id))
                        <input type="hidden" name="id" value="{{ $questionBank->id }}">
                    @endif

                    <div class="card stretch stretch-full">
                        <div class="card-body general-info">
                            <div class="mb-5 d-flex align-items-center justify-content-between">
                                <h5 class="fw-bold mb-0 me-4">
                                    <span class="d-block mb-2">Question Bank Info :</span>
                                    <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                        Define question bank for a specific course, branch, class and subject.
                                    </span>
                                </h5>
                                <button type="submit" class="btn btn-primary">Next</button>
                            </div>

                            {{-- COURSE --}}
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label class="fw-semibold">Course <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form-select @error('course_id') is-invalid @enderror"
                                        id="course_id" name="course_id" required>
                                        <option value="">Select Course</option>
                                        @foreach ($courses as $course)
                                            <option value="{{ $course->id }}"
                                                data-streams="{{ $course->streams->pluck('id')->join(',') }}"
                                                {{ old('course_id', $questionBank->course_id ?? '') == $course->id ? 'selected' : '' }}>
                                                {{ $course->name }} {{ $course->code ? '('.$course->code.')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('course_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- BRANCH --}}
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label class="fw-semibold">Branch <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form-select @error('stream_id') is-invalid @enderror"
                                        id="stream_id" name="stream_id" required>
                                        <option value="">Select Branch</option>
                                        @foreach ($streams as $stream)
                                            <option value="{{ $stream->id }}"
                                                {{ old('stream_id', $questionBank->stream_id ?? '') == $stream->id ? 'selected' : '' }}>
                                                {{ $stream->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('stream_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- CLASS --}}
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label class="fw-semibold">Class / Semester <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form-select @error('class_id') is-invalid @enderror"
                                        id="class_id" name="class_id" required>
                                        <option value="">Select Class</option>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}"
                                                {{ old('class_id', $questionBank->class_id ?? '') == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('class_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- SUBJECT --}}
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label class="fw-semibold">Subject <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form-select @error('subject_id') is-invalid @enderror"
                                        id="subject_id" name="subject_id" required>
                                        <option value="">Select Subject</option>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}"
                                                {{ old('subject_id', $questionBank->subject_id ?? '') == $subject->id ? 'selected' : '' }}>
                                                {{ $subject->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('subject_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- NAME --}}
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label class="fw-semibold">Question Bank Name <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name"
                                        value="{{ old('name', $questionBank->name ?? '') }}"
                                        placeholder="Enter Question Bank Name" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- DESCRIPTION --}}
                            <div class="row mb-4 align-items-start">
                                <div class="col-lg-4">
                                    <label class="fw-semibold">Description:</label>
                                </div>
                                <div class="col-lg-8">
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                        id="description" name="description" rows="3"
                                        placeholder="Enter description (optional)">{{ old('description', $questionBank->description ?? '') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- STATUS --}}
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label class="fw-semibold">Status <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form-select @error('status') is-invalid @enderror"
                                        id="status" name="status" required>
                                        <option value="1" {{ old('status', $questionBank->status ?? 1) == '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status', $questionBank->status ?? 1) == '0' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
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

{{-- JavaScript for Dynamic Branch Filtering (Like Exam) --}}
<script>
    (function() {
        const courseSelect = document.getElementById('course_id');
        const streamSelect = document.getElementById('stream_id');

        if (!courseSelect || !streamSelect) return;

        // Store all stream options
        const allOptions = Array.from(streamSelect.options).map(opt => ({
            value: opt.value,
            text: opt.text,
            element: opt.cloneNode(true),
        }));

        function filterStreams(courseEl) {
            const dataStreams = courseEl?.dataset?.streams ?? '';
            const allowed = dataStreams ? dataStreams.split(',').map(s => s.trim()) : [];

            streamSelect.innerHTML = '<option value="">Select Branch</option>';

            allOptions.forEach(opt => {
                if (!opt.value) return;
                if (!allowed.length || allowed.includes(opt.value)) {
                    const o = opt.element.cloneNode(true);
                    streamSelect.appendChild(o);
                }
            });
        }

        // Initial filter if course is selected
        if (courseSelect.selectedIndex >= 0 && courseSelect.value) {
            filterStreams(courseSelect.options[courseSelect.selectedIndex]);
        }

        courseSelect.addEventListener('change', function() {
            filterStreams(this.options[this.selectedIndex]);
        });
    })();
</script>
@endsection