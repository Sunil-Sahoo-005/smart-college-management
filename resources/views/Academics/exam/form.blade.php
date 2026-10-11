@extends('admin_layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Academics.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            {{-- ══ STICKY HEADER ══ --}}
            <div class="content-area-header bg-white sticky-top">
                <h5 class="fw-bold mb-0 me-4">
                    <span class="d-block mb-2">{{ !empty($exam->id) ? 'Edit Exam' : 'New Exam' }}</span>
                </h5>
                <div class="page-header-right ms-auto">
                    <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary">
                        <i class="feather-arrow-left me-2"></i> Back to List
                    </a>
                </div>
            </div>

            @include('elements.flash')

            <div class="content-area-body">

                {{-- STEP 1 — Exam Info --}}
                <div id="step1Section">
                    <form action="{{ route('admin.exams.store') }}" method="POST" id="examForm">
                        @csrf
                        @if (!empty($exam->id))
                            <input type="hidden" name="id" value="{{ $exam->id }}">
                        @endif

                        <div class="card stretch stretch-full">
                            <div class="card-body general-info">
                                <div class="mb-5 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <h5 class="fw-bold mb-0 me-4">
                                        <span class="d-block mb-2">Exam Info :</span>
                                        <span class="fs-12 fw-normal text-muted">
                                            Define an exam for a specific class and subject with date, time, and marks.
                                        </span>
                                    </h5>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary" id="saveExamBtn">
                                            <i class="feather-save me-1"></i> Save
                                        </button>
                                        <button type="button" class="btn btn-success d-none" id="nextToQBBtn">
                                            <i class="feather-arrow-right me-1"></i> Next: Select Question Bank
                                        </button>
                                    </div>
                                </div>

                                {{-- EXAM MODE --}}
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4"><label for="exam_mode" class="fw-semibold">Exam Mode:</label></div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-monitor"></i></div>
                                            <select class="form-select @error('exam_mode') is-invalid @enderror" id="exam_mode" name="exam_mode" required>
                                                <option value="offline" {{ old('exam_mode', $exam->exam_mode ?? 'offline') == 'offline' ? 'selected' : '' }}>Offline</option>
                                                <option value="online"  {{ old('exam_mode', $exam->exam_mode ?? '') == 'online'  ? 'selected' : '' }}>Online</option>
                                            </select>
                                        </div>
                                        @error('exam_mode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                {{-- EXAM CODE --}}
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="exam_code" class="fw-semibold">Exam Code:</label>
                                        <span class="d-block fs-12 text-muted mt-1">Leave blank to auto-generate</span>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-hash"></i></div>
                                            <input type="text" class="form-control @error('exam_code') is-invalid @enderror"
                                                id="exam_code" name="exam_code" value="{{ old('exam_code', $exam->exam_code ?? '') }}"
                                                placeholder="e.g. EXAM-2026001" autocomplete="off">
                                        </div>
                                        @error('exam_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                {{-- ✅ EXAM CENTER (shown only for online mode) --}}
                                <div class="row mb-4 align-items-center" id="centerRow" style="display:none;">
                                    <div class="col-lg-4">
                                        <label for="center_id" class="fw-semibold">
                                            Exam Center:
                                            <span class="text-danger ms-1">*</span>
                                        </label>
                                        <span class="d-block fs-12 text-muted mt-1">Required for online exams</span>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-map-pin"></i></div>
                                            <select class="form-select @error('center_id') is-invalid @enderror"
                                                    id="center_id" name="center_id">
                                                <option value="">— Select Exam Center —</option>
                                                @foreach ($examCenters as $center)
                                                    <option value="{{ $center->id }}"
                                                        {{ old('center_id', $exam->center_id ?? '') == $center->id ? 'selected' : '' }}>
                                                        {{ $center->name }}
                                                        @if($center->code) ({{ $center->code }}) @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('center_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror

                                        {{-- Empty state hint --}}
                                        @if($examCenters->isEmpty())
                                            <div class="mt-2">
                                                <small class="text-warning">
                                                    <i class="feather-alert-triangle me-1"></i>
                                                    No active exam centers found.
                                                    <a href="{{ route('admin.exam_center.create') }}" target="_blank" class="text-primary">
                                                        Create one
                                                    </a>
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- EXAM TYPE --}}
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4"><label for="exam_type_id" class="fw-semibold">Exam Type:</label></div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-list"></i></div>
                                            <select class="form-select @error('exam_type_id') is-invalid @enderror" id="exam_type_id" name="exam_type_id" required>
                                                <option value="">Select Exam Type</option>
                                                @foreach ($examTypes as $type)
                                                    <option value="{{ $type->id }}" {{ old('exam_type_id', $exam->exam_type_id ?? '') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('exam_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                {{-- COURSE --}}
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4"><label for="course_id" class="fw-semibold">Course:</label></div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-book-open"></i></div>
                                            <select class="form-select" id="course_id" name="course_id">
                                                <option value="">Select Course</option>
                                                @foreach ($courses as $course)
                                                    <option value="{{ $course->id }}"
                                                        data-streams="{{ $course->streams->pluck('id')->join(',') }}"
                                                        {{ old('course_id', $courses->first(fn($c) => $c->streams->pluck('id')->contains($exam->stream_id ?? 0))?->id ?? '') == $course->id ? 'selected' : '' }}>
                                                        {{ $course->name }}{{ $course->code ? ' ('.$course->code.')' : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- BRANCH --}}
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4"><label for="stream_id" class="fw-semibold">Branch:</label></div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-git-branch"></i></div>
                                            <select class="form-select @error('stream_id') is-invalid @enderror" id="stream_id" name="stream_id">
                                                <option value="">Select Branch (optional)</option>
                                                @foreach ($streams as $stream)
                                                    <option value="{{ $stream->id }}" {{ old('stream_id', $exam->stream_id ?? '') == $stream->id ? 'selected' : '' }}>{{ $stream->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('stream_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                {{-- CLASS / SEM --}}
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4"><label for="class_id" class="fw-semibold">Year/Semester:</label></div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-hash"></i></div>
                                            <select class="form-select @error('class_id') is-invalid @enderror" id="class_id" name="class_id" required>
                                                <option value="">Select Semester</option>
                                                @foreach ($classes as $class)
                                                    <option value="{{ $class->id }}" {{ old('class_id', $exam->class_id ?? '') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                {{-- EXAM NAME --}}
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4"><label for="name" class="fw-semibold">Exam Name:</label></div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-tag"></i></div>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name" value="{{ old('name', $exam->name ?? '') }}" placeholder="Exam Name" required autocomplete="off">
                                        </div>
                                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                {{-- SUBJECT --}}
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4"><label for="subject_id" class="fw-semibold">Subject:</label></div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-book"></i></div>
                                            <select class="form-select @error('subject_id') is-invalid @enderror" id="subject_id" name="subject_id" required>
                                                <option value="">Select Subject</option>
                                                @foreach ($subjects as $subject)
                                                    <option value="{{ $subject->id }}" {{ old('subject_id', $exam->subject_id ?? '') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('subject_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                {{-- ACADEMIC YEAR --}}
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4"><label for="academic_year_id" class="fw-semibold">Academic Year:</label></div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-calendar"></i></div>
                                            <select class="form-select @error('academic_year_id') is-invalid @enderror" id="academic_year_id" name="academic_year_id" required>
                                                <option value="">Select Academic Year</option>
                                                @foreach ($academicYears as $year)
                                                    <option value="{{ $year->id }}" {{ old('academic_year_id', $exam->academic_year_id ?? '') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('academic_year_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                {{-- EXAM DATE --}}
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4"><label for="exam_date" class="fw-semibold">Exam Date:</label></div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-calendar"></i></div>
                                            <input type="date" class="form-control @error('exam_date') is-invalid @enderror"
                                                id="exam_date" name="exam_date" value="{{ old('exam_date', $exam->exam_date ?? '') }}" required autocomplete="off">
                                        </div>
                                        @error('exam_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                {{-- START TIME --}}
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4"><label for="start_time" class="fw-semibold">Start Time:</label></div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-clock"></i></div>
                                            <input type="time" class="form-control @error('start_time') is-invalid @enderror"
                                                id="start_time" name="start_time" value="{{ old('start_time', $exam->start_time ?? '') }}" required autocomplete="off">
                                        </div>
                                        @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                {{-- END TIME --}}
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4"><label for="end_time" class="fw-semibold">End Time:</label></div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-clock"></i></div>
                                            <input type="time" class="form-control @error('end_time') is-invalid @enderror"
                                                id="end_time" name="end_time" value="{{ old('end_time', $exam->end_time ?? '') }}" required autocomplete="off">
                                        </div>
                                        @error('end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                {{-- TOTAL MARKS --}}
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4"><label for="total_marks" class="fw-semibold">Total Marks:</label></div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-award"></i></div>
                                            <input type="number" class="form-control @error('total_marks') is-invalid @enderror"
                                                id="total_marks" name="total_marks" value="{{ old('total_marks', $exam->total_marks ?? '') }}" min="1" required autocomplete="off">
                                        </div>
                                        @error('total_marks')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                {{-- PASS MARKS --}}
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4"><label for="pass_marks" class="fw-semibold">Pass Marks:</label></div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-award"></i></div>
                                            <input type="number" class="form-control @error('pass_marks') is-invalid @enderror"
                                                id="pass_marks" name="pass_marks" value="{{ old('pass_marks', $exam->pass_marks ?? '') }}" min="0" required autocomplete="off">
                                        </div>
                                        @error('pass_marks')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                {{-- GRADING SYSTEM --}}
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="grading_system_id" class="fw-semibold">Grading System:</label>
                                        <span class="d-block fs-12 text-muted mt-1">Grade levels will preview below</span>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-bar-chart-2"></i></div>
                                            <select class="form-select @error('grading_system_id') is-invalid @enderror" id="grading_system_id" name="grading_system_id">
                                                <option value="">Select Grading System</option>
                                                @foreach ($gradingSystems as $gs)
                                                    <option value="{{ $gs->id }}" {{ old('grading_system_id', $exam->grading_system_id ?? '') == $gs->id ? 'selected' : '' }}>{{ $gs->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('grading_system_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                {{-- GRADE LEVELS PREVIEW --}}
                                <div class="row mb-4 align-items-start" id="gradeLevelsRow" style="display:none;">
                                    <div class="col-lg-4">
                                        <label class="fw-semibold">Grade Levels:</label>
                                        <span class="d-block fs-12 text-muted mt-1">Defined for selected grading system</span>
                                    </div>
                                    <div class="col-lg-8">
                                        <div id="gradeLevelsPreview" class="d-flex flex-wrap gap-2"></div>
                                        <small class="text-muted mt-2 d-block" id="gradeLevelsNote"></small>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>{{-- /step1Section --}}

                {{-- STEP 2 — Select Question Bank --}}
                <div id="step2Section" class="d-none">

                    <div class="d-flex align-items-center gap-2 mb-4 flex-wrap">
                        <span class="badge bg-success px-3 py-2"><i class="feather-check me-1"></i> Step 1: Exam Info</span>
                        <i class="feather-chevron-right text-muted"></i>
                        <span class="badge bg-primary px-3 py-2">Step 2: Select Question Bank</span>
                        <i class="feather-chevron-right text-muted"></i>
                        <span class="badge bg-secondary px-3 py-2">Step 3: Pick Questions</span>
                    </div>

                    <div class="alert alert-info d-flex flex-wrap align-items-center gap-3 mb-4">
                        <i class="feather-info flex-shrink-0"></i>
                        <div>
                            <strong>Online Exam</strong>
                            <span class="ms-2 text-muted" id="examSummaryText"></span>
                        </div>
                        <div class="ms-auto d-flex gap-2 flex-shrink-0 flex-wrap">
                            {{-- ✅ Selected center badge shown in step 2 --}}
                            <span class="badge bg-info text-dark py-2 px-3" id="displayCenterBadge" style="display:none!important;">
                                <i class="feather-map-pin me-1"></i>
                                Center: <strong id="displayCenterName">—</strong>
                            </span>
                            <span class="badge bg-warning text-dark py-2 px-3">
                                Max Marks: <strong id="displayMaxMarks">—</strong>
                            </span>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="backToStep1Btn">
                                <i class="feather-arrow-left me-1"></i> Edit Exam Info
                            </button>
                        </div>
                    </div>

                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div>
                                    <h5 class="fw-bold mb-0">Available Question Banks</h5>
                                    <small class="text-muted">Matching your course, branch, semester and subject</small>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-primary fs-12" id="qbCountBadge">0 found</span>
                                    {{-- Legend --}}
                                    <span id="qbLegend" class="d-none" style="font-size:.75rem;">
                                        <span style="display:inline-flex;align-items:center;gap:4px;">
                                            <span style="width:10px;height:10px;border-radius:50%;background:#16a34a;display:inline-block;"></span>
                                            Previously selected
                                        </span>
                                        &nbsp;
                                        <span style="display:inline-flex;align-items:center;gap:4px;">
                                            <span style="width:10px;height:10px;border-radius:50%;background:#94a3b8;display:inline-block;"></span>
                                            Other banks
                                        </span>
                                    </span>
                                </div>
                            </div>

                            <div class="row g-3" id="qbCardsRow"></div>

                            <div id="qbEmpty" class="text-center py-5 d-none">
                                <i class="feather-inbox text-muted d-block mb-3" style="font-size:3rem;"></i>
                                <h6 class="text-muted">No Question Banks Found</h6>
                                <p class="text-muted small">No active question banks match your selected filters.</p>
                                <a href="{{ route('admin.question_banks.create') }}" class="btn btn-primary btn-sm mt-2" target="_blank">
                                    <i class="feather-plus me-1"></i> Create Question Bank
                                </a>
                            </div>
                        </div>
                    </div>
                </div>{{-- /step2Section --}}

                {{-- STEP 3 — Pick Questions --}}
                <div id="step3Section" class="d-none">

                    <div class="d-flex align-items-center gap-2 mb-4 flex-wrap">
                        <span class="badge bg-success px-3 py-2"><i class="feather-check me-1"></i> Step 1: Exam Info</span>
                        <i class="feather-chevron-right text-muted"></i>
                        <span class="badge bg-success px-3 py-2"><i class="feather-check me-1"></i> Step 2: Question Bank</span>
                        <i class="feather-chevron-right text-muted"></i>
                        <span class="badge bg-primary px-3 py-2">Step 3: Pick Questions</span>
                    </div>

                    {{-- Marks tracker --}}
                    <div class="marks-tracker-bar mb-4" id="marksTrackerBar"
                         style="position:sticky;top:70px;z-index:150;background:#fff;
                                border:1.5px solid #e2e8f0;border-radius:12px;padding:14px 20px;
                                box-shadow:0 4px 16px rgba(0,0,0,.07);">
                        <div class="d-flex align-items-center flex-wrap gap-3">
                            <div class="flex-shrink-0">
                                <span class="text-muted small">Question Bank:</span>
                                <strong class="text-primary ms-1" id="step3QBName">—</strong>
                            </div>
                            {{-- ✅ Center display in step 3 tracker --}}
                            <div class="flex-shrink-0" id="step3CenterWrap" style="display:none;">
                                <span class="text-muted small">Center:</span>
                                <strong class="text-info ms-1" id="step3CenterName">—</strong>
                            </div>
                            <div class="vr d-none d-sm-block"></div>
                            <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                <span class="text-muted small">Selected:</span>
                                <strong class="text-dark ms-1" id="trackerQCount">0</strong>
                                <span class="text-muted small">questions</span>
                            </div>
                            <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                <span class="text-muted small">Added Marks:</span>
                                <strong class="text-success ms-1" id="trackerAdded">0</strong>
                            </div>
                            <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                <span class="text-muted small">Remaining:</span>
                                <strong class="ms-1" id="trackerRemaining" style="color:#f59e0b;">—</strong>
                            </div>
                            <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                <span class="text-muted small">Max:</span>
                                <strong class="text-dark ms-1" id="trackerMax">—</strong>
                            </div>
                            <div class="flex-grow-1" style="min-width:100px;">
                                <div style="background:#f1f5f9;border-radius:6px;height:8px;overflow:hidden;">
                                    <div id="marksProgressBar" style="height:100%;border-radius:6px;
                                         background:linear-gradient(90deg,#22c55e,#16a34a);
                                         width:0%;transition:width .3s ease;"></div>
                                </div>
                            </div>
                            <div class="ms-auto d-flex gap-2 flex-shrink-0">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="backToStep2Btn">
                                    <i class="feather-arrow-left me-1"></i> Change QB
                                </button>
                                <button type="button" class="btn btn-success btn-sm" id="finalSaveBtn" disabled>
                                    <i class="feather-save me-1"></i> Save Online Exam
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Loading --}}
                    <div id="questionsLoading" class="text-center py-5 d-none">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="text-muted mt-2">Loading questions...</p>
                    </div>

                    {{-- Questions table --}}
                    <div id="questionsTableWrap" class="d-none">
                        <div class="card stretch stretch-full">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0" id="questionsTable">
                                        <thead class="bg-light">
                                            <tr>
                                                <th width="4%">#</th>
                                                <th width="9%">Type</th>
                                                <th width="34%">Question</th>
                                                <th width="7%" class="text-center">Marks</th>
                                                <th width="7%" class="text-center">Neg. Marks</th>
                                                <th width="10%" class="text-center">Status</th>
                                                <th width="29%" class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="questionsTbody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Empty --}}
                    <div id="questionsEmpty" class="text-center py-5 d-none">
                        <i class="feather-inbox text-muted d-block mb-3" style="font-size:3rem;"></i>
                        <h6 class="text-muted">No Questions Found</h6>
                        <p class="text-muted small">This question bank has no questions yet.</p>
                    </div>

                    {{-- Hidden final submit form --}}
                    <form id="finalExamForm" action="{{ route('admin.exams.store') }}" method="POST" class="d-none">
                        @csrf
                        @if (!empty($exam->id))
                            <input type="hidden" name="id" value="{{ $exam->id }}">
                        @endif
                        <input type="hidden" name="exam_mode"         id="f_exam_mode">
                        <input type="hidden" name="exam_code"         id="f_exam_code">
                        <input type="hidden" name="exam_type_id"      id="f_exam_type_id">
                        <input type="hidden" name="course_id"         id="f_course_id">
                        <input type="hidden" name="stream_id"         id="f_stream_id">
                        <input type="hidden" name="class_id"          id="f_class_id">
                        <input type="hidden" name="name"              id="f_name">
                        <input type="hidden" name="subject_id"        id="f_subject_id">
                        <input type="hidden" name="academic_year_id"  id="f_academic_year_id">
                        <input type="hidden" name="exam_date"         id="f_exam_date">
                        <input type="hidden" name="start_time"        id="f_start_time">
                        <input type="hidden" name="end_time"          id="f_end_time">
                        <input type="hidden" name="total_marks"       id="f_total_marks">
                        <input type="hidden" name="pass_marks"        id="f_pass_marks">
                        <input type="hidden" name="grading_system_id" id="f_grading_system_id">
                        <input type="hidden" name="question_bank_id"  id="f_question_bank_id">
                        {{-- ✅ center_id hidden field in final form --}}
                        <input type="hidden" name="center_id"         id="f_center_id">
                    </form>

                </div>{{-- /step3Section --}}

            </div>{{-- /content-area-body --}}
        </div>
    </div>
</div>

{{-- DATA from server --}}
<script>
    const ALL_QUESTION_BANKS      = @json($questionBanksForJs ?? []);
    const QUESTIONS_API_URL       = "{{ route('admin.question_paper_types.questions') }}";
    const CSRF_TOKEN              = "{{ csrf_token() }}";
    const ALL_GRADE_LEVELS        = @json($gradeLevels ?? []);
    const EXISTING_QB_ID          = {{ $existingQuestionBankId ?? 'null' }};
    const EXISTING_QUESTION_IDS   = @json($existingQuestionIds ?? []);
</script>

{{-- MAIN SCRIPT --}}
<script>
(function () {

    const step1Section   = document.getElementById('step1Section');
    const step2Section   = document.getElementById('step2Section');
    const step3Section   = document.getElementById('step3Section');
    const examModeSelect = document.getElementById('exam_mode');
    const saveExamBtn    = document.getElementById('saveExamBtn');
    const nextToQBBtn    = document.getElementById('nextToQBBtn');
    const backToStep1Btn = document.getElementById('backToStep1Btn');
    const backToStep2Btn = document.getElementById('backToStep2Btn');
    const finalSaveBtn   = document.getElementById('finalSaveBtn');

    // ✅ Center row elements
    const centerRow    = document.getElementById('centerRow');
    const centerSelect = document.getElementById('center_id');

    let selectedQBId     = null;
    let addedQuestions   = {};
    let allQuestionsData = [];

    // ── Course → Branch filter ─────────────────────────────────
    const courseSelect = document.getElementById('course_id');
    const streamSelect = document.getElementById('stream_id');

    if (courseSelect && streamSelect) {
        const allStreamOpts = Array.from(streamSelect.options).map(o => ({
            value: o.value, text: o.text, el: o.cloneNode(true)
        }));
        const savedStreamId = '{{ old('stream_id', $exam->stream_id ?? '') }}';

        function filterStreams(courseEl) {
            const allowed = (courseEl?.dataset?.streams ?? '').split(',').map(s => s.trim()).filter(Boolean);
            streamSelect.innerHTML = '<option value="">Select Branch (optional)</option>';
            allStreamOpts.forEach(opt => {
                if (!opt.value) return;
                if (!allowed.length || allowed.includes(opt.value)) {
                    const o = opt.el.cloneNode(true);
                    if (opt.value === savedStreamId) o.selected = true;
                    streamSelect.appendChild(o);
                }
            });
        }

        if (savedStreamId) {
            Array.from(courseSelect.options).forEach(opt => {
                if ((opt.dataset.streams ?? '').split(',').map(s => s.trim()).includes(savedStreamId))
                    opt.selected = true;
            });
        }
        filterStreams(courseSelect.options[courseSelect.selectedIndex]);
        courseSelect.addEventListener('change', function () { filterStreams(this.options[this.selectedIndex]); });
    }

    // ── Grade Levels Preview ───────────────────────────────────
    const gsSelect      = document.getElementById('grading_system_id');
    const levelsRow     = document.getElementById('gradeLevelsRow');
    const levelsPreview = document.getElementById('gradeLevelsPreview');
    const levelsNote    = document.getElementById('gradeLevelsNote');

    const glColors = [
        { bg:'#ede9fe',text:'#6d28d9',border:'#c4b5fd' },
        { bg:'#dcfce7',text:'#15803d',border:'#86efac' },
        { bg:'#e0f2fe',text:'#0369a1',border:'#7dd3fc' },
        { bg:'#fef9c3',text:'#a16207',border:'#fde047' },
        { bg:'#ffe4e6',text:'#be123c',border:'#fda4af' },
        { bg:'#fff7ed',text:'#c2410c',border:'#fed7aa' },
    ];

    function renderGradeLevels() {
        if (!gsSelect || !levelsRow) return;
        const val = gsSelect.value;
        if (!val) { levelsRow.style.display = 'none'; return; }
        const levels = ALL_GRADE_LEVELS.filter(g => String(g.grading_system_id) === val);
        levelsRow.style.display = 'flex';
        if (!levels.length) {
            levelsPreview.innerHTML = `<div class="text-muted small py-2"><i class="feather-alert-circle me-1"></i>No grade levels defined. <a href="{{ route('admin.grade_levels.create') }}" target="_blank">Add</a></div>`;
            levelsNote.textContent = '';
            return;
        }
        levelsNote.textContent = levels.length + ' grade level(s)';
        levelsPreview.innerHTML = levels.map((g, i) => {
            const c = glColors[i % glColors.length];
            return `<div title="${g.remarks ?? ''}" style="background:${c.bg};border:1.5px solid ${c.border};color:${c.text};border-radius:10px;padding:6px 14px;font-size:.8125rem;font-weight:700;display:inline-flex;align-items:center;gap:6px;white-space:nowrap;">
                <span style="font-size:1rem;">${g.grade_letter}</span>
                <span style="font-weight:400;opacity:.75;font-size:.75rem;">${g.min_score}–${g.max_score}%</span>
            </div>`;
        }).join('');
    }
    if (gsSelect) { gsSelect.addEventListener('change', renderGradeLevels); renderGradeLevels(); }

    // ── Exam Mode toggle ───────────────────────────────────────
    function toggleModeButtons() {
        const online = examModeSelect.value === 'online';
        saveExamBtn.classList.toggle('d-none', online);
        nextToQBBtn.classList.toggle('d-none', !online);

        // ✅ Show/hide center row and toggle required attribute
        if (online) {
            centerRow.style.display = '';
            if (centerSelect) centerSelect.setAttribute('required', 'required');
        } else {
            centerRow.style.display = 'none';
            if (centerSelect) {
                centerSelect.removeAttribute('required');
                centerSelect.value = '';
            }
        }
    }
    examModeSelect.addEventListener('change', toggleModeButtons);
    toggleModeButtons();

    // ── Validate Step 1 ────────────────────────────────────────
    function validateStep1() {
        const form = document.getElementById('examForm');
        let valid  = true;
        form.querySelectorAll('[required]').forEach(field => {
            // ✅ Skip hidden center_id when mode is offline
            if (field.id === 'center_id' && examModeSelect.value !== 'online') return;
            if (!field.value.trim()) { field.classList.add('is-invalid'); valid = false; }
            else field.classList.remove('is-invalid');
        });
        if (!valid) {
            const first = form.querySelector('.is-invalid');
            if (first) { first.scrollIntoView({ behavior:'smooth', block:'center' }); first.focus(); }
        }
        return valid;
    }

    // ── Step 1 → Step 2 ────────────────────────────────────────
    nextToQBBtn.addEventListener('click', function () {
        if (!validateStep1()) return;

        step1Section.classList.add('d-none');
        step2Section.classList.remove('d-none');
        step3Section.classList.add('d-none');

        const getSelText = id => {
            const el = document.getElementById(id);
            return el?.options?.[el.selectedIndex]?.text?.trim() ?? '—';
        };

        document.getElementById('displayMaxMarks').textContent = document.getElementById('total_marks').value;
        document.getElementById('examSummaryText').textContent =
            [document.getElementById('name').value, getSelText('course_id'), getSelText('stream_id'),
             getSelText('class_id'), getSelText('subject_id')].filter(v => v && v !== '—').join(' · ');

        // ✅ Show selected center name in step 2 badge
        const centerBadge = document.getElementById('displayCenterBadge');
        const centerNameEl = document.getElementById('displayCenterName');
        if (centerSelect && centerSelect.value) {
            const selOpt = centerSelect.options[centerSelect.selectedIndex];
            centerNameEl.textContent = selOpt.text;
            centerBadge.style.removeProperty('display');
            centerBadge.style.display = 'inline-flex';
        } else {
            centerBadge.style.display = 'none';
        }

        loadQBCards();
        window.scrollTo({ top:0, behavior:'smooth' });
    });

    // ── Back buttons ───────────────────────────────────────────
    backToStep1Btn.addEventListener('click', function () {
        step2Section.classList.add('d-none');
        step1Section.classList.remove('d-none');
        window.scrollTo({ top:0, behavior:'smooth' });
    });

    backToStep2Btn.addEventListener('click', function () {
        step3Section.classList.add('d-none');
        step2Section.classList.remove('d-none');
        addedQuestions = {};
        window.scrollTo({ top:0, behavior:'smooth' });
    });

    // ── Load QB Cards (Step 2) ─────────────────────────────────
    function loadQBCards() {
        const courseId  = document.getElementById('course_id').value;
        const streamId  = document.getElementById('stream_id').value;
        const classId   = document.getElementById('class_id').value;
        const subjectId = document.getElementById('subject_id').value;

        const qbCardsRow   = document.getElementById('qbCardsRow');
        const qbEmpty      = document.getElementById('qbEmpty');
        const qbCountBadge = document.getElementById('qbCountBadge');
        const qbLegend     = document.getElementById('qbLegend');

        qbCardsRow.innerHTML = '';
        qbEmpty.classList.add('d-none');

        const filtered = ALL_QUESTION_BANKS.filter(qb => {
            let m = qb.status == 1 || qb.status === true;
            if (courseId)  m = m && String(qb.course_id)  === String(courseId);
            if (streamId)  m = m && String(qb.stream_id)  === String(streamId);
            if (classId)   m = m && String(qb.class_id)   === String(classId);
            if (subjectId) m = m && String(qb.subject_id) === String(subjectId);
            return m;
        });

        qbCountBadge.textContent = filtered.length + ' found';

        if (EXISTING_QB_ID && filtered.length) {
            qbLegend.classList.remove('d-none');
        } else {
            qbLegend.classList.add('d-none');
        }

        if (!filtered.length) { qbEmpty.classList.remove('d-none'); return; }

        const colors = [
            { bg:'#ede9fe', icon:'#6d28d9', border:'#c4b5fd' },
            { bg:'#e0f2fe', icon:'#0369a1', border:'#7dd3fc' },
            { bg:'#dcfce7', icon:'#15803d', border:'#86efac' },
            { bg:'#fef9c3', icon:'#a16207', border:'#fde047' },
            { bg:'#ffe4e6', icon:'#be123c', border:'#fda4af' },
            { bg:'#f0fdf4', icon:'#166534', border:'#bbf7d0' },
            { bg:'#fff7ed', icon:'#c2410c', border:'#fed7aa' },
            { bg:'#fdf4ff', icon:'#7e22ce', border:'#e9d5ff' },
        ];

        qbCardsRow.innerHTML = filtered.map((qb, i) => {
            const isPrev  = EXISTING_QB_ID && String(qb.id) === String(EXISTING_QB_ID);
            const c       = colors[i % colors.length];

            const cardStyle = isPrev
                ? `background:${c.bg};border:2.5px solid ${c.icon};border-radius:14px;
                   padding:1.5rem;cursor:pointer;transition:all .22s ease;position:relative;
                   box-shadow:0 4px 20px ${c.icon}40;`
                : `background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:14px;
                   padding:1.5rem;cursor:pointer;transition:all .22s ease;position:relative;
                   opacity:0.65;filter:saturate(0.3);`;

            const iconColor  = isPrev ? c.icon  : '#94a3b8';
            const iconBg     = isPrev ? `${c.icon}20` : '#e2e8f0';
            const nameColor  = isPrev ? 'text-dark' : 'text-secondary';
            const badgeBg    = isPrev ? `${c.icon}20` : '#e2e8f0';
            const badgeColor = isPrev ? c.icon   : '#64748b';

            const prevBadge = isPrev
                ? `<div style="position:absolute;top:10px;right:10px;">
                       <span style="background:#16a34a;color:#fff;border-radius:20px;
                                    padding:3px 10px;font-size:.7rem;font-weight:700;
                                    display:inline-flex;align-items:center;gap:4px;">
                           <i class="feather-check-circle" style="font-size:11px;"></i> Previously Selected
                       </span>
                   </div>`
                : '';

            return `
            <div class="col-sm-6 col-md-4 col-xl-3">
                <div class="qb-select-card h-100" data-qb-id="${qb.id}" data-qb-name="${esc(qb.name)}"
                     data-is-prev="${isPrev ? '1' : '0'}"
                     style="${cardStyle}">
                    ${prevBadge}
                    <div class="d-flex align-items-center gap-3 mb-3" style="margin-top:${isPrev ? '18px' : '0'};">
                        <div style="width:46px;height:46px;border-radius:12px;background:${iconBg};
                                    display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="feather-file-text" style="color:${iconColor};font-size:20px;"></i>
                        </div>
                        <div>
                            <div class="fw-bold ${nameColor}" style="font-size:.95rem;line-height:1.3;">${esc(qb.name)}</div>
                            ${qb.description ? `<small class="text-muted" style="font-size:.75rem;">${esc(qb.description)}</small>` : ''}
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-1">
                        <span class="badge" style="background:${badgeBg};color:${badgeColor};font-size:.7rem;">${esc(qb.class_name ?? '—')}</span>
                        <span class="badge" style="background:${badgeBg};color:${badgeColor};font-size:.7rem;">${esc(qb.subject_name ?? '—')}</span>
                    </div>
                    <div class="mt-3 d-flex justify-content-end">
                        <button class="btn btn-sm ${isPrev ? 'btn-success' : 'btn-primary'}" style="border-radius:8px;">
                            <i class="feather-arrow-right me-1"></i> ${isPrev ? 'Select Again' : 'Select & View Questions'}
                        </button>
                    </div>
                </div>
            </div>`;
        }).join('');

        qbCardsRow.querySelectorAll('.qb-select-card').forEach(card => {
            const isPrev = card.dataset.isPrev === '1';
            card.addEventListener('mouseenter', function () {
                this.style.transform  = 'translateY(-4px)';
                this.style.boxShadow  = isPrev ? '0 12px 32px rgba(0,0,0,.15)' : '0 8px 24px rgba(0,0,0,.08)';
                if (!isPrev) this.style.opacity = '0.85';
            });
            card.addEventListener('mouseleave', function () {
                this.style.transform = '';
                this.style.boxShadow = isPrev ? `0 4px 20px rgba(0,0,0,.1)` : '';
                if (!isPrev) this.style.opacity = '0.65';
            });
            card.addEventListener('click', function () {
                openQBQuestions(this.dataset.qbId, this.dataset.qbName);
            });
        });
    }

    // ── Open Step 3 ────────────────────────────────────────────
    function openQBQuestions(qbId, qbName) {
        selectedQBId   = qbId;
        addedQuestions = {};

        step2Section.classList.add('d-none');
        step3Section.classList.remove('d-none');

        document.getElementById('step3QBName').textContent  = qbName;
        document.getElementById('f_question_bank_id').value = qbId;

        // ✅ Show center name in step 3 tracker bar
        const step3CenterWrap = document.getElementById('step3CenterWrap');
        const step3CenterName = document.getElementById('step3CenterName');
        if (centerSelect && centerSelect.value) {
            const selOpt = centerSelect.options[centerSelect.selectedIndex];
            step3CenterName.textContent = selOpt.text;
            step3CenterWrap.style.display = '';
        } else {
            step3CenterWrap.style.display = 'none';
        }

        const maxMarks = parseFloat(document.getElementById('total_marks').value) || 0;
        document.getElementById('trackerMax').textContent = maxMarks;
        updateTracker();

        document.getElementById('questionsLoading').classList.remove('d-none');
        document.getElementById('questionsTableWrap').classList.add('d-none');
        document.getElementById('questionsEmpty').classList.add('d-none');
        window.scrollTo({ top:0, behavior:'smooth' });

        fetch(`${QUESTIONS_API_URL}?question_bank_id=${qbId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('questionsLoading').classList.add('d-none');
            allQuestionsData = data.questions || [];
            if (!allQuestionsData.length) {
                document.getElementById('questionsEmpty').classList.remove('d-none');
                return;
            }
            if (EXISTING_QB_ID && String(qbId) === String(EXISTING_QB_ID) && EXISTING_QUESTION_IDS.length) {
                EXISTING_QUESTION_IDS.forEach(qid => {
                    const q = allQuestionsData.find(x => String(x.id) === String(qid));
                    if (q) {
                        addedQuestions[q.id] = {
                            id:   String(q.id),
                            mark: parseFloat(q.mark) || 0,
                            text: q.question,
                            type: q.type,
                        };
                    }
                });
                updateTracker();
            }
            document.getElementById('questionsTableWrap').classList.remove('d-none');
            renderQuestionsTable();
        })
        .catch(() => {
            document.getElementById('questionsLoading').classList.add('d-none');
            document.getElementById('questionsEmpty').classList.remove('d-none');
            document.getElementById('questionsEmpty').innerHTML =
                '<p class="text-danger py-4">Failed to load questions. Please try again.</p>';
        });
    }

    // ── Truncate helper ────────────────────────────────────────
    function truncate(str, len) {
        if (!str) return '—';
        return str.length > len ? str.substring(0, len) + '...' : str;
    }

    // ── Render Questions Table ─────────────────────────────────
    function renderQuestionsTable() {
        const tbody    = document.getElementById('questionsTbody');
        const maxMarks = parseFloat(document.getElementById('total_marks').value) || 0;

        const typeColors = { mcq:'primary', true_false:'info', short_answer:'warning', long_answer:'secondary' };
        const typeLabels = { mcq:'MCQ', true_false:'True/False', short_answer:'Short Ans', long_answer:'Long Ans' };

        tbody.innerHTML = allQuestionsData.map((q, i) => {
            const isAdded     = !!addedQuestions[q.id];
            const isActive    = q.is_active == 1 || q.is_active === true;
            const qMark       = parseFloat(q.mark) || 0;
            const addedTotal  = Object.values(addedQuestions).reduce((s, qq) => s + parseFloat(qq.mark || 0), 0);
            const wouldExceed = !isAdded && (addedTotal + qMark) > maxMarks;
            const qShort      = truncate(q.question, 30);

            let optionsHtml = '';
            if (q.type === 'mcq' && q.options?.length) {
                optionsHtml = '<div class="mt-1">' + q.options.map(opt =>
                    `<small class="${opt.is_correct ? 'text-success fw-bold' : 'text-muted'}">
                        <i class="feather-${opt.is_correct ? 'check-circle' : 'circle'}" style="font-size:10px;"></i>
                        ${esc(truncate(opt.option_text, 25))}
                    </small><br>`
                ).join('') + '</div>';
            }
            if (q.type === 'true_false' && q.options?.length) {
                const correct = q.options.find(o => o.is_correct);
                optionsHtml = `<small class="text-muted ms-1">Correct: <strong>${esc(correct?.option_text ?? '—')}</strong></small>`;
            }

            const statusBadge = isActive
                ? `<span class="badge bg-success-subtle text-success">Active</span>`
                : `<span class="badge bg-danger-subtle text-danger">Inactive</span>`;

            const toggleBtn = isActive
                ? `<button type="button" class="btn btn-xs btn-outline-danger toggle-status-btn mt-1"
                        data-q-id="${q.id}" data-new-status="0"
                        style="font-size:.7rem;padding:2px 8px;border-radius:6px;">
                        <i class="feather-toggle-left me-1"></i>Inactive
                   </button>`
                : `<button type="button" class="btn btn-xs btn-outline-success toggle-status-btn mt-1"
                        data-q-id="${q.id}" data-new-status="1"
                        style="font-size:.7rem;padding:2px 8px;border-radius:6px;">
                        <i class="feather-toggle-right me-1"></i>Active
                   </button>`;

            const editBtn = `<a href="/admin/question-paper-types/${q.id}/edit" target="_blank"
                    class="btn btn-warning btn-sm" style="border-radius:8px;" title="Edit Question">
                    <i class="feather-edit"></i>
                </a>`;

            let actionBtn = '';
            if (!isActive) {
                actionBtn = `<div class="d-flex gap-1 justify-content-center flex-wrap">
                    ${editBtn}
                    <span class="badge bg-secondary-subtle text-secondary align-self-center"
                          style="font-size:.72rem;border-radius:8px;padding:6px 8px;">Activate first</span>
                </div>`;
            } else if (isAdded) {
                actionBtn = `<div class="d-flex gap-1 justify-content-center flex-wrap">
                    ${editBtn}
                    <button type="button" class="btn btn-danger btn-sm remove-q-btn"
                            data-q-id="${q.id}" style="border-radius:8px;">
                        <i class="feather-minus me-1"></i> Remove
                    </button>
                </div>`;
            } else if (wouldExceed) {
                actionBtn = `<div class="d-flex gap-1 justify-content-center flex-wrap">
                    ${editBtn}
                    <span class="badge bg-danger-subtle text-danger align-self-center"
                          style="font-size:.72rem;border-radius:8px;padding:6px 8px;">
                        <i class="feather-alert-circle me-1"></i>Exceeds marks
                    </span>
                </div>`;
            } else {
                actionBtn = `<div class="d-flex gap-1 justify-content-center flex-wrap">
                    ${editBtn}
                    <button type="button" class="btn btn-primary btn-sm add-q-btn"
                            data-q-id="${q.id}" data-q-mark="${qMark}"
                            data-q-text="${esc(q.question)}" data-q-type="${q.type}"
                            style="border-radius:8px;">
                        <i class="feather-plus me-1"></i> Add
                    </button>
                </div>`;
            }

            const rowBg = isAdded ? 'style="background:#f0fdf4;"' : '';

            return `<tr ${rowBg} id="q-row-${q.id}">
                <td>${i + 1}</td>
                <td><span class="badge bg-${typeColors[q.type] ?? 'secondary'}">${typeLabels[q.type] ?? q.type}</span></td>
                <td>
                    <div title="${esc(q.question)}">${esc(qShort)}</div>
                    ${optionsHtml}
                </td>
                <td class="text-center"><span class="badge bg-success">${qMark}</span></td>
                <td class="text-center">
                    <span class="badge ${parseFloat(q.negative_marks) > 0 ? 'bg-danger' : 'bg-light text-dark'}">
                        ${q.negative_marks ?? 0}
                    </span>
                </td>
                <td class="text-center">${statusBadge}${toggleBtn}</td>
                <td class="text-center">${actionBtn}</td>
            </tr>`;
        }).join('');

        // Add
        tbody.querySelectorAll('.add-q-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const qId        = this.dataset.qId;
                const qMark      = parseFloat(this.dataset.qMark) || 0;
                const maxMarks   = parseFloat(document.getElementById('total_marks').value) || 0;
                const addedTotal = Object.values(addedQuestions).reduce((s, qq) => s + parseFloat(qq.mark || 0), 0);
                if ((addedTotal + qMark) > maxMarks) {
                    showToast(`Cannot add! Only ${(maxMarks - addedTotal).toFixed(2)} marks remaining.`, 'danger');
                    return;
                }
                addedQuestions[qId] = { id: qId, mark: qMark, text: this.dataset.qText, type: this.dataset.qType };
                updateTracker();
                renderQuestionsTable();
            });
        });

        // Remove
        tbody.querySelectorAll('.remove-q-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                delete addedQuestions[this.dataset.qId];
                updateTracker();
                renderQuestionsTable();
            });
        });

        // Toggle Status
        tbody.querySelectorAll('.toggle-status-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const qId       = this.dataset.qId;
                const newStatus = parseInt(this.dataset.newStatus);
                const self      = this;
                self.disabled   = true;
                self.innerHTML  = '<span class="spinner-border spinner-border-sm"></span>';

                fetch(`/admin/question-paper-types/${qId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN':     CSRF_TOKEN,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type':     'application/json',
                    },
                    body: JSON.stringify({ is_active: newStatus })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        const q = allQuestionsData.find(x => String(x.id) === String(qId));
                        if (q) q.is_active = newStatus;
                        if (newStatus === 0) { delete addedQuestions[qId]; updateTracker(); }
                        showToast(newStatus === 1 ? 'Question activated!' : 'Question deactivated!', 'success');
                        renderQuestionsTable();
                    } else {
                        showToast(data.message || 'Failed to update status.', 'danger');
                        self.disabled  = false;
                        self.innerHTML = newStatus === 1
                            ? '<i class="feather-toggle-right me-1"></i>Activate'
                            : '<i class="feather-toggle-left me-1"></i>Deactivate';
                    }
                })
                .catch(() => {
                    showToast('Network error.', 'danger');
                    self.disabled  = false;
                    self.innerHTML = newStatus === 1
                        ? '<i class="feather-toggle-right me-1"></i>Activate'
                        : '<i class="feather-toggle-left me-1"></i>Deactivate';
                });
            });
        });
    }

    // ── Marks Tracker ──────────────────────────────────────────
    function updateTracker() {
        const maxMarks   = parseFloat(document.getElementById('total_marks').value) || 0;
        const added      = Object.values(addedQuestions);
        const addedTotal = added.reduce((s, q) => s + parseFloat(q.mark || 0), 0);
        const remaining  = maxMarks - addedTotal;
        const pct        = maxMarks > 0 ? Math.min(100, (addedTotal / maxMarks) * 100) : 0;

        document.getElementById('trackerQCount').textContent    = added.length;
        document.getElementById('trackerAdded').textContent     = addedTotal.toFixed(2);
        document.getElementById('trackerRemaining').textContent = remaining.toFixed(2);
        document.getElementById('trackerMax').textContent       = maxMarks;

        const bar = document.getElementById('marksProgressBar');
        bar.style.width      = pct + '%';
        bar.style.background = pct >= 100 ? 'linear-gradient(90deg,#ef4444,#dc2626)'
                             : pct >= 80  ? 'linear-gradient(90deg,#f59e0b,#d97706)'
                             :              'linear-gradient(90deg,#22c55e,#16a34a)';

        document.getElementById('trackerRemaining').style.color =
            remaining < 0 ? '#ef4444' : remaining === 0 ? '#22c55e' : '#f59e0b';

        finalSaveBtn.disabled = added.length === 0;
    }

    // ── Final Save ─────────────────────────────────────────────
    finalSaveBtn.addEventListener('click', function () {
        const added = Object.values(addedQuestions);
        if (!added.length) { showToast('Please add at least one question!', 'danger'); return; }

        const fieldMap = ['exam_mode','exam_code','exam_type_id','course_id','stream_id','class_id',
                          'name','subject_id','academic_year_id','exam_date','start_time',
                          'end_time','total_marks','pass_marks','grading_system_id'];
        fieldMap.forEach(id => {
            const src = document.getElementById(id);
            const tgt = document.getElementById('f_' + id);
            if (src && tgt) tgt.value = src.value;
        });

        document.getElementById('f_question_bank_id').value = selectedQBId;

        // ✅ Copy center_id to the final hidden form
        document.getElementById('f_center_id').value = centerSelect ? centerSelect.value : '';

        const form = document.getElementById('finalExamForm');
        form.querySelectorAll('input[name="selected_question_ids[]"]').forEach(el => el.remove());
        added.forEach(q => {
            const inp = document.createElement('input');
            inp.type  = 'hidden';
            inp.name  = 'selected_question_ids[]';
            inp.value = q.id;
            form.appendChild(inp);
        });
        form.submit();
    });

    // ── Toast ──────────────────────────────────────────────────
    function showToast(msg, type = 'info') {
        let container = document.getElementById('toastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toastContainer';
            container.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:8px;';
            document.body.appendChild(container);
        }
        const toast = document.createElement('div');
        toast.style.cssText = `background:${type==='danger'?'#fee2e2':type==='success'?'#dcfce7':'#e0f2fe'};
            color:${type==='danger'?'#dc2626':type==='success'?'#15803d':'#0369a1'};
            border:1.5px solid ${type==='danger'?'#fca5a5':type==='success'?'#86efac':'#7dd3fc'};
            border-radius:10px;padding:12px 18px;font-size:.875rem;font-weight:500;
            box-shadow:0 4px 16px rgba(0,0,0,.1);max-width:340px;animation:slideInToast .3s ease;`;
        toast.innerHTML = `<i class="feather-${type==='danger'?'alert-circle':type==='success'?'check-circle':'info'} me-2"></i>${msg}`;
        container.appendChild(toast);
        setTimeout(() => { toast.style.opacity='0'; toast.style.transition='opacity .3s'; setTimeout(() => toast.remove(), 300); }, 3500);
    }

    // ── HTML escape ────────────────────────────────────────────
    function esc(str) {
        if (str == null) return '—';
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    }

})();
</script>

{{-- STYLES --}}
<style>
@keyframes slideInToast {
    from { transform: translateX(40px); opacity: 0; }
    to   { transform: translateX(0);    opacity: 1; }
}
.table th { white-space: nowrap; font-size: .8125rem; padding: .75rem 1rem; }
.table td { padding: .75rem 1rem; vertical-align: middle; font-size: .875rem; }
.content-area { overflow-y: auto !important; }
.qb-select-card:active { transform: scale(.97) !important; }
#marksTrackerBar { transition: box-shadow .2s; }
.btn-xs { font-size: .7rem !important; padding: 2px 8px !important; }

@keyframes prevQbPulse {
    0%   { box-shadow: 0 4px 20px rgba(22,163,74,.25); }
    50%  { box-shadow: 0 4px 28px rgba(22,163,74,.5);  }
    100% { box-shadow: 0 4px 20px rgba(22,163,74,.25); }
}
.qb-select-card[data-is-prev="1"] {
    animation: prevQbPulse 2.5s ease-in-out infinite;
}
.qb-select-card[data-is-prev="1"]:hover {
    animation: none;
}
</style>

@endsection