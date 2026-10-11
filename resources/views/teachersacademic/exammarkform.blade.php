@extends('teacher_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            
            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">
                            {{ !empty($examMark) ? 'Edit Exam Marks' : 'Enter Exam Marks' }}
                        </span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                            <a href="{{ route('admin.exam_marks.index') }}" class="btn btn-secondary">
                                <i class="feather-arrow-left me-2"></i>
                                <span>Back to List</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="content-area-body">
                    <form action="{{ route('admin.exam_marks.store') }}" method="POST">
                        @csrf
                        @if (!empty($examMark))
                            <input type="hidden" name="id" value="{{ $examMark->id }}">
                        @endif

                        <div class="card stretch stretch-full">
                            <div class="card-body general-info">
                                <div class="mb-5 d-flex align-items-center justify-content-between">
                                    <h5 class="fw-bold mb-0 me-4">
                                        <span class="d-block mb-2">Exam Marks Info :</span>
                                        <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                            Enter or update marks for a student in a specific exam.
                                        </span>
                                    </h5>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="exam_id" class="fw-semibold">Exam:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-list"></i></div>
                                            <select class="form-select @error('exam_id') is-invalid @enderror"
                                                id="exam_id" name="exam_id" required>
                                                <option value="">Select Exam</option>
                                                @foreach ($exams as $exam)
                                                    <option value="{{ $exam->id }}"
                                                        {{ old('exam_id', $examMark->exam_id ?? '') == $exam->id ? 'selected' : '' }}>
                                                        {{ $exam->name }} ({{ $exam->examType?->name ?? 'N/A' }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('exam_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="student_id" class="fw-semibold">Student:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-user"></i></div>
                                            <select class="form-select @error('student_id') is-invalid @enderror"
                                                id="student_id" name="student_id" required>
                                                <option value="">Select Student</option>
                                                @foreach ($students as $student)
                                                    <option value="{{ $student->id }}"
                                                        {{ old('student_id', $examMark->student_id ?? '') == $student->id ? 'selected' : '' }}>
                                                        {{ $student->first_name }} {{ $student->last_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('student_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="marks_obtained" class="fw-semibold">Marks Obtained:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-award"></i></div>
                                            <input type="number" step="0.01"
                                                class="form-control @error('marks_obtained') is-invalid @enderror"
                                                id="marks_obtained" name="marks_obtained"
                                                value="{{ old('marks_obtained', $examMark->marks_obtained ?? '') }}"
                                                min="0" max="9999.99" required>
                                        </div>
                                        @error('marks_obtained')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="grade_letter" class="fw-semibold">Grade Letter:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-star"></i></div>
                                            <input type="text"
                                                class="form-control @error('grade_letter') is-invalid @enderror"
                                                id="grade_letter" name="grade_letter"
                                                value="{{ old('grade_letter', $examMark->grade_letter ?? '') }}"
                                                placeholder="A, B, C, etc.">
                                        </div>
                                        @error('grade_letter')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="remarks" class="fw-semibold">Remarks:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-align-left"></i></div>
                                            <textarea class="form-control @error('remarks') is-invalid @enderror" id="remarks" name="remarks" rows="3"
                                                placeholder="Optional remarks">{{ old('remarks', $examMark->remarks ?? '') }}</textarea>
                                        </div>
                                        @error('remarks')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="entered_by_user_id" class="fw-semibold">Entered By:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-user-check"></i></div>
                                            <select class="form-select @error('entered_by_user_id') is-invalid @enderror"
                                                id="entered_by_user_id" name="entered_by_user_id" required>
                                                <option value="">Select User</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}"
                                                        {{ old('entered_by_user_id', $examMark->entered_by_user_id ?? '') == $user->id ? 'selected' : '' }}>
                                                        {{ $user->first_name }} {{ $user->last_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('entered_by_user_id')
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
