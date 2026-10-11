@extends('admin_layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Academics.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">
            <div class="content-area-header bg-white sticky-top">
                <h5 class="fw-bold mb-0 me-4">
                    <span class="d-block mb-2">
                        {{ !empty($course->id) ? 'Edit Course' : 'Course Info :' }}
                    </span>
                </h5>

                <div class="page-header-right ms-auto">
                    <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                        <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">
                            <i class="feather-arrow-left me-2"></i>
                            <span>Back to List</span>
                        </a>
                    </div>
                </div>
            </div>

            @include('elements.flash')

            <div class="content-area-body">
                <form action="{{ route('admin.courses.store') }}" method="POST">
                    @csrf

                    @if (!empty($course->id))
                        <input type="hidden" name="id" value="{{ $course->id }}">
                    @endif

                    <div class="card stretch stretch-full">
                        <div class="card-body general-info">
                            <div class="mb-5 d-flex align-items-center justify-content-between">
                                <h5 class="fw-bold mb-0 me-4"></h5>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="name" class="fw-semibold">Course Name:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-book-open"></i></div>
                                        <input type="text"
                                               class="form-control @error('name') is-invalid @enderror"
                                               id="name"
                                               name="name"
                                               value="{{ old('name', $course->name ?? '') }}"
                                               placeholder="Course name (e.g., B.Tech, MBA, Class 1-10)"
                                               required autocomplete="off">
                                    </div>
                                    @error('name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="code" class="fw-semibold">Course Code:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-code"></i></div>
                                        <input type="text"
                                               class="form-control @error('code') is-invalid @enderror"
                                               id="code"
                                               name="code"
                                               value="{{ old('code', $course->code ?? '') }}"
                                               placeholder="Course code"
                                               required autocomplete="off">
                                    </div>
                                    @error('code')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="duration_years" class="fw-semibold">Duration (Years):</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-clock"></i></div>
                                        <input type="number"
                                               class="form-control @error('duration_years') is-invalid @enderror"
                                               id="duration_years"
                                               name="duration_years"
                                               value="{{ old('duration_years', $course->duration_years ?? '') }}"
                                               placeholder="e.g., 4"
                                               required autocomplete="off">
                                    </div>
                                    @error('duration_years')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="course_type" class="fw-semibold">Course Type:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-clock"></i></div>
                                        <select name="course_type" id="course_type" class="form-control @error('course_type') is-invalid @enderror">
                                            <option value="">Select Course Type</option>
                                            <option value="semester" {{ old('course_type', $course->course_type ?? '') === 'semester' ? 'selected' : '' }}>Semester Wise</option>
                                            <option value="yearly" {{ old('course_type', $course->course_type ?? '') === 'yearly' ? 'selected' : '' }}>Yearly</option>
                                        </select>
                                    </div>
                                    @error('course_type')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="strength" class="fw-semibold">Strength:</label>
                                    <small class="d-block text-muted mt-1">Maximum student seats.</small>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-users"></i></div>
                                        <input type="number"
                                               class="form-control @error('strength') is-invalid @enderror"
                                               id="strength"
                                               name="strength"
                                               value="{{ old('strength', $course->strength ?? '') }}"
                                               placeholder="e.g., 60"
                                               min="0"
                                               required autocomplete="off">
                                    </div>
                                    @error('strength')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="is_lateral_entry" class="fw-semibold">Lateral Entry:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="is_lateral_entry" value="0">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               role="switch"
                                               id="is_lateral_entry"
                                               name="is_lateral_entry"
                                               value="1"
                                               {{ old('is_lateral_entry', $course->is_lateral_entry ?? false) ? 'checked' : '' }} autocomplete="off">
                                        <label class="form-check-label" for="is_lateral_entry">Lateral admission available</label>
                                    </div>
                                    @error('is_lateral_entry')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4 align-items-center" id="lateralSeatPercentageRow">
                                <div class="col-lg-4">
                                    <label for="lateral_seat_percentage" class="fw-semibold">Lateral Seat (%):</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-percent"></i></div>
                                        <input type="number"
                                               class="form-control @error('lateral_seat_percentage') is-invalid @enderror"
                                               id="lateral_seat_percentage"
                                               name="lateral_seat_percentage"
                                               value="{{ old('lateral_seat_percentage', $course->lateral_seat_percentage ?? '') }}"
                                               placeholder="e.g., 10"
                                               min="0"
                                               max="100"
                                               step="0.01"
                                               data-lateral-required autocomplete="off">
                                    </div>
                                    @error('lateral_seat_percentage')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-5 align-items-start">
                                <div class="col-lg-4">
                                    <label class="fw-semibold mt-2">Branches / Streams:</label>
                                    <small class="d-block text-muted mt-1">Select the branches associated with this course.</small>
                                </div>
                                <div class="col-lg-8">
                                    @php
                                        $oldStreams = old('streams', $selectedStreams ?? []);
                                    @endphp

                                    <div class="d-flex flex-wrap gap-2 p-4 border rounded bg-light" style="max-height: 300px; overflow-y: auto;">
                                        @forelse($streams as $stream)
                                            <div class="stream-option">
                                                <input type="checkbox"
                                                       class="btn-check"
                                                       id="stream_{{ $stream->id }}"
                                                       name="streams[]"
                                                       value="{{ $stream->id }}"
                                                       {{ in_array($stream->id, $oldStreams) ? 'checked' : '' }}
                                                       autocomplete="off">
                                                <label class="btn btn-outline-primary px-3 py-2 rounded-pill d-flex align-items-center gap-2 mb-0" for="stream_{{ $stream->id }}">
                                                    <i class="feather-check-circle check-icon" style="display: none;"></i>
                                                    <i class="feather-circle uncheck-icon"></i>
                                                    {{ $stream->name }}
                                                </label>
                                            </div>
                                        @empty
                                            <div class="text-muted fst-italic w-100 text-center py-3">
                                                No branches available. Please create them first.
                                            </div>
                                        @endforelse
                                    </div>

                                    @error('streams')
                                        <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                                    @enderror
                                    @error('streams.*')
                                        <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
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
<style>
    .stream-option .btn-outline-primary {
        color: #4b5563;
        border-color: #d1d5db;
        background-color: #ffffff;
        transition: all 0.2s ease-in-out;
    }
    
    .stream-option .btn-check:checked + .btn-outline-primary {
        background-color: #e0e7ff;
        border-color: #6366f1;
        color: #4f46e5;
        font-weight: 600;
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
    }
    
    .stream-option .btn-check:checked + .btn-outline-primary .check-icon {
        display: inline-block !important;
    }
    
    .stream-option .btn-check:checked + .btn-outline-primary .uncheck-icon {
        display: none !important;
    }
    
    .stream-option .btn-outline-primary:hover {
        background-color: #f3f4f6;
        border-color: #9ca3af;
    }
    
    .stream-option .btn-check:checked + .btn-outline-primary:hover {
        background-color: #c7d2fe;
    }
    
    .content-area-body .card {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border: none;
        border-radius: 0.5rem;
    }
    
    .content-area-body .form-control {
        border-radius: 0 0.375rem 0.375rem 0;
    }
    
    .content-area-body .input-group-text {
        border-radius: 0.375rem 0 0 0.375rem;
        background-color: #f9fafb;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const lateralSwitch = document.getElementById('is_lateral_entry');
        const lateralRow = document.getElementById('lateralSeatPercentageRow');
        const lateralInput = document.getElementById('lateral_seat_percentage');

        function toggleLateralPercentage() {
            const enabled = lateralSwitch.checked;
            lateralRow.classList.toggle('d-none', !enabled);
            lateralInput.required = enabled;

            if (!enabled) {
                lateralInput.value = '';
            }
        }

        lateralSwitch.addEventListener('change', toggleLateralPercentage);
        lateralInput.addEventListener('input', function () {
            if (Number(this.value) > 100) {
                this.value = 100;
            }
        });

        toggleLateralPercentage();
    });
</script>
@endsection

