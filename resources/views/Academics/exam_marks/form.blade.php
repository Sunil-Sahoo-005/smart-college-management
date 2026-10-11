@extends('admin_layout')
@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Academics.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            {{-- HEADER --}}
            <div class="content-area-header bg-white px-4 py-3 border-bottom shadow-sm"
                 style="position:sticky; top:0; z-index:200;">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">
                            {{ isset($examMark) ? 'Edit Exam Mark' : 'Enter Exam Marks' }}
                        </h5>
                        <small class="text-muted">Bulk entry or per-student entry</small>
                    </div>
                    <div class="ms-auto">
                        <a href="{{ route('admin.exam_marks.index') }}"
                           class="btn btn-outline-secondary btn-sm">
                            <i class="feather-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
            </div>

            <div class="content-area-body">
                @include('elements.flash')

                {{-- TABS --}}
                <ul class="nav nav-tabs px-3 pt-3 bg-white border-bottom" id="markEntryTabs">
                    <li class="nav-item">
                        <button class="nav-link active fw-semibold" id="tab-bulk" data-bs-toggle="tab" data-bs-target="#pane-bulk" type="button">
                            <i class="feather-list me-1"></i> Bulk Entry
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-semibold" id="tab-student" data-bs-toggle="tab" data-bs-target="#pane-student" type="button">
                            <i class="feather-user me-1"></i> By Student
                        </button>
                    </li>
                </ul>

                <div class="tab-content">

                    {{-- ═══════════════════════════════════════════ --}}
                    {{-- TAB 1 — BULK ENTRY                         --}}
                    {{-- ═══════════════════════════════════════════ --}}
                    <div class="tab-pane fade show active" id="pane-bulk">

                        {{-- EXAM SELECTOR + DATE --}}
                        <div class="card mb-3 mt-3 mx-3">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-5">
                                        <label class="form-label fw-semibold">Select Exam <span class="text-danger">*</span></label>
                                        <select id="bulk_exam_id" class="form-select">
                                            <option value="">— Choose Exam —</option>
                                            @foreach($exams as $exam)
                                                <option value="{{ $exam->id }}"
                                                    {{ isset($examMark) && $examMark->exam_id == $exam->id ? 'selected' : '' }}>
                                                    {{ $exam->name }}
                                                    ({{ $exam->subject->name ?? '' }} |
                                                     {{ $exam->class->name ?? '' }} -
                                                     {{ $exam->section->name ?? '' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">Mark Entry Date <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="feather-calendar"></i></span>
                                            <input type="date" id="bulk_mark_date" class="form-control"
                                                   value="{{ date('Y-m-d') }}">
                                        </div>
                                    </div>

                                    {{-- Exam Info Badge --}}
                                    <div class="col-md-4" id="bulkExamInfoBox" style="display:none;">
                                        <div class="d-flex gap-2 flex-wrap">
                                            <span class="badge bg-primary-subtle text-primary px-3 py-2">
                                                Subject: <strong id="bulkInfoSubject"></strong>
                                            </span>
                                            <span class="badge bg-success-subtle text-success px-3 py-2">
                                                Total: <strong id="bulkInfoTotal"></strong>
                                            </span>
                                            <span class="badge bg-warning-subtle text-warning px-3 py-2">
                                                Pass: <strong id="bulkInfoPass"></strong>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- LOADING --}}
                        <div id="bulkLoadingBox" class="text-center py-5 d-none">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="text-muted mt-2 mb-0">Loading students…</p>
                        </div>

                        {{-- MARKS TABLE --}}
                        <div id="bulkMarksSection" class="d-none mx-3">
                            <div class="card">
                                <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
                                    <div>
                                        <h6 class="fw-bold mb-0">Student Marks Entry</h6>
                                        <small class="text-muted">
                                            <span id="bulkStudentCount">0</span> students
                                        </small>
                                    </div>
                                    <button type="button" id="bulkFillAllBtn" class="btn btn-outline-success btn-sm">
                                        <i class="feather-check-circle me-1"></i> Fill Max Marks All
                                    </button>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th style="width:50px;">#</th>
                                                    <th style="width:80px;">Roll No</th>
                                                    <th>Student Name</th>
                                                    <th style="width:160px;">
                                                        Marks
                                                        <small class="text-muted fw-normal" id="bulkThTotal"></small>
                                                    </th>
                                                    <th style="width:110px;">Grade</th>
                                                    <th>Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody id="bulkMarksTbody"></tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer bg-white d-flex justify-content-end gap-2 py-3">
                                    <a href="{{ route('admin.exam_marks.index') }}" class="btn btn-outline-secondary">Cancel</a>
                                    <button type="button" id="bulkSaveBtn" class="btn btn-primary px-4">
                                        <i class="feather-save me-1"></i> Save Marks
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="bulkEmptyState" class="text-center py-5 d-none">
                            <i class="feather-users text-muted d-block mb-2" style="font-size:3rem;"></i>
                            <h6 class="text-muted">No students found for this exam</h6>
                        </div>
                    </div>{{-- /pane-bulk --}}


                    {{-- ═══════════════════════════════════════════ --}}
                    {{-- TAB 2 — BY STUDENT                         --}}
                    {{-- ═══════════════════════════════════════════ --}}
                    <div class="tab-pane fade" id="pane-student">

                        <div class="card mb-3 mt-3 mx-3">
                            <div class="card-body">
                                <p class="text-muted small mb-3">
                                    <i class="feather-info me-1"></i>
                                    Select an exam first — then pick a specific student to enter or update their mark.
                                </p>
                                <div class="row g-3 align-items-end">

                                    {{-- Exam --}}
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Select Exam <span class="text-danger">*</span></label>
                                        <select id="stu_exam_id" class="form-select">
                                            <option value="">— Choose Exam —</option>
                                            @foreach($exams as $exam)
                                                <option value="{{ $exam->id }}">
                                                    {{ $exam->name }}
                                                    ({{ $exam->subject->name ?? '' }} |
                                                     {{ $exam->class->name ?? '' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Student (populated via JS) --}}
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Select Student <span class="text-danger">*</span></label>
                                        <select id="stu_student_id" class="form-select" disabled>
                                            <option value="">— Select exam first —</option>
                                        </select>
                                    </div>

                                    {{-- Date --}}
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Mark Entry Date <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="feather-calendar"></i></span>
                                            <input type="date" id="stu_mark_date" class="form-control"
                                                   value="{{ date('Y-m-d') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Student loading spinner --}}
                        <div id="stuLoadingBox" class="text-center py-4 d-none mx-3">
                            <div class="spinner-border text-primary spinner-border-sm" role="status"></div>
                            <span class="text-muted ms-2">Loading students…</span>
                        </div>

                        {{-- Single Student Mark Form --}}
                        <div id="stuMarkForm" class="d-none mx-3">
                            <div class="card">
                                <div class="card-header bg-white py-3">
                                    <div class="d-flex align-items-center gap-3 flex-wrap">
                                        <h6 class="fw-bold mb-0" id="stuStudentName">—</h6>
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            Roll: <strong id="stuRollNo">—</strong>
                                        </span>
                                        <span class="badge bg-primary-subtle text-primary">
                                            Subject: <strong id="stuSubject">—</strong>
                                        </span>
                                        <span class="badge bg-success-subtle text-success">
                                            Total: <strong id="stuTotal">—</strong>
                                        </span>
                                        <span class="badge bg-warning-subtle text-warning">
                                            Pass: <strong id="stuPass">—</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">Marks Obtained <span class="text-danger">*</span></label>
                                            <input type="number" id="stu_marks" class="form-control"
                                                   min="0" step="0.01" placeholder="0">
                                            <div class="form-text" id="stuMarksHint"></div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">Grade</label>
                                            <select id="stu_grade" class="form-select">
                                                <option value="">— Auto —</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Remarks</label>
                                            <input type="text" id="stu_remarks" class="form-control"
                                                   placeholder="Optional…" maxlength="500">
                                        </div>
                                    </div>

                                    {{-- Result indicator --}}
                                    <div id="stuResultBadge" class="mt-3 d-none">
                                        <span class="badge fs-6 px-3 py-2" id="stuResultText"></span>
                                    </div>
                                </div>
                                <div class="card-footer bg-white d-flex justify-content-end gap-2 py-3">
                                    <button type="button" id="stuClearBtn" class="btn btn-outline-secondary">
                                        <i class="feather-x me-1"></i> Clear
                                    </button>
                                    <button type="button" id="stuSaveBtn" class="btn btn-primary px-4">
                                        <i class="feather-save me-1"></i> Save Mark
                                    </button>
                                </div>
                            </div>

                            {{-- Recent saves for this session --}}
                            <div id="stuSavedList" class="mt-3 d-none">
                                <h6 class="fw-semibold text-muted mb-2">
                                    <i class="feather-check-circle text-success me-1"></i>
                                    Saved this session
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered bg-white">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Student</th>
                                                <th>Marks</th>
                                                <th>Grade</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody id="stuSavedTbody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div id="stuEmptyState" class="text-center py-5 d-none mx-3">
                            <i class="feather-user-x text-muted d-block mb-2" style="font-size:2.5rem;"></i>
                            <h6 class="text-muted">No students enrolled for this exam</h6>
                        </div>

                    </div>{{-- /pane-student --}}

                </div>{{-- /tab-content --}}
            </div>
        </div>
    </div>
</div>

<script>
const STUDENTS_URL = "{{ route('admin.exam_marks.students_by_exam') }}";
const STORE_URL    = "{{ route('admin.exam_marks.store') }}";
const INDEX_URL    = "{{ route('admin.exam_marks.index') }}";
const CSRF_TOKEN   = "{{ csrf_token() }}";

document.addEventListener('DOMContentLoaded', function () {

    // ─────────────────────────────────────────
    // Shared state
    // ─────────────────────────────────────────
    let bulkExamData   = null;
    let bulkGrades     = [];
    let stuExamData    = null;
    let stuGrades      = [];
    let stuAllStudents = [];
    let stuExisting    = {};
    let stuSavedRows   = [];

    // ─────────────────────────────────────────
    // esc helper
    // ─────────────────────────────────────────
    function esc(str) {
        if (str == null) return '';
        return String(str)
            .replace(/&/g,'&amp;').replace(/</g,'&lt;')
            .replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    }

    // ══════════════════════════════════════════
    // TAB 1 — BULK ENTRY
    // ══════════════════════════════════════════
    const bulkExamSelect  = document.getElementById('bulk_exam_id');
    const bulkMarkDate    = document.getElementById('bulk_mark_date');
    const bulkLoadingBox  = document.getElementById('bulkLoadingBox');
    const bulkMarksSection= document.getElementById('bulkMarksSection');
    const bulkEmptyState  = document.getElementById('bulkEmptyState');
    const bulkExamInfoBox = document.getElementById('bulkExamInfoBox');
    const bulkMarksTbody  = document.getElementById('bulkMarksTbody');
    const bulkSaveBtn     = document.getElementById('bulkSaveBtn');
    const bulkFillAllBtn  = document.getElementById('bulkFillAllBtn');

    bulkExamSelect.addEventListener('change', function () {
        const examId = this.value;
        if (!examId) { bulkHideAll(); return; }
        bulkLoadStudents(examId);
    });

    function bulkHideAll() {
        bulkMarksSection.classList.add('d-none');
        bulkEmptyState.classList.add('d-none');
        bulkExamInfoBox.style.display = 'none';
    }

    function bulkLoadStudents(examId) {
        bulkHideAll();
        bulkLoadingBox.classList.remove('d-none');

        fetch(`${STUDENTS_URL}?exam_id=${examId}`, {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            bulkLoadingBox.classList.add('d-none');
            bulkExamData = data.exam;
            bulkGrades   = data.grades ?? [];

            document.getElementById('bulkInfoSubject').textContent = data.exam.subject;
            document.getElementById('bulkInfoTotal').textContent   = data.exam.total_marks;
            document.getElementById('bulkInfoPass').textContent    = data.exam.pass_marks;
            document.getElementById('bulkThTotal').textContent     = `/ ${data.exam.total_marks}`;
            bulkExamInfoBox.style.display = '';

            if (!data.students.length) {
                bulkEmptyState.classList.remove('d-none');
                return;
            }

            document.getElementById('bulkStudentCount').textContent = data.students.length;
            bulkRenderTable(data.students, data.existing);
            bulkMarksSection.classList.remove('d-none');
        })
        .catch(() => {
            bulkLoadingBox.classList.add('d-none');
            alert('Failed to load students. Please try again.');
        });
    }

    function bulkRenderTable(students, existing) {
        bulkMarksTbody.innerHTML = students.map((s, i) => {
            const ex      = existing[s.student_id] ?? {};
            const marks   = ex.marks_obtained ?? '';
            const grade   = ex.grade_letter   ?? '';
            const remarks = ex.remarks        ?? '';

            const gradeOptions = bulkGrades.map(g =>
                `<option value="${esc(g.letter)}" ${grade === g.letter ? 'selected' : ''}>${esc(g.letter)}</option>`
            ).join('');

            return `
                <tr>
                    <td class="text-muted">${i + 1}</td>
                    <td class="fw-medium">${esc(s.roll_number ?? '—')}</td>
                    <td>${esc(s.first_name)} ${esc(s.last_name)}</td>
                    <td>
                        <input type="number" class="form-control form-control-sm marks-input"
                               data-student-id="${s.student_id}"
                               value="${marks}" min="0" max="${bulkExamData.total_marks}"
                               step="0.01" placeholder="0" style="width:110px;">
                    </td>
                    <td>
                        <select class="form-select form-select-sm grade-select"
                                data-student-id="${s.student_id}" style="width:100px;">
                            <option value="">—</option>
                            ${gradeOptions}
                        </select>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm remarks-input"
                               data-student-id="${s.student_id}"
                               value="${esc(remarks)}" placeholder="Optional…" maxlength="500">
                    </td>
                </tr>`;
        }).join('');

        bulkMarksTbody.querySelectorAll('.marks-input').forEach(input => {
            input.addEventListener('input', function () {
                autoGrade(this, bulkGrades, bulkMarksTbody);
            });
        });
    }

    bulkFillAllBtn.addEventListener('click', function () {
        if (!bulkExamData) return;
        bulkMarksTbody.querySelectorAll('.marks-input').forEach(input => {
            input.value = bulkExamData.total_marks;
            input.dispatchEvent(new Event('input'));
        });
    });

    bulkSaveBtn.addEventListener('click', function () {
        const examId   = bulkExamSelect.value;
        const markDate = bulkMarkDate.value;

        if (!examId) { alert('Please select an exam first.'); return; }
        if (!markDate) { alert('Please select a mark entry date.'); return; }

        const marks = [];
        bulkMarksTbody.querySelectorAll('.marks-input').forEach(input => {
            const v = input.value;
            if (v === '' || v === null) return;
            const sid = input.dataset.studentId;
            marks.push({
                student_id:     sid,
                marks_obtained: v,
                grade_letter:   bulkMarksTbody.querySelector(`.grade-select[data-student-id="${sid}"]`)?.value  ?? '',
                remarks:        bulkMarksTbody.querySelector(`.remarks-input[data-student-id="${sid}"]`)?.value ?? '',
                mark_date:      markDate,
            });
        });

        if (!marks.length) { alert('Please enter marks for at least one student.'); return; }

        bulkSaveBtn.disabled = true;
        bulkSaveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving…';

        fetch(STORE_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            body: JSON.stringify({ exam_id: examId, marks }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.message) window.location.href = INDEX_URL + '?success=1';
        })
        .catch(() => alert('Failed to save. Please try again.'))
        .finally(() => {
            bulkSaveBtn.disabled = false;
            bulkSaveBtn.innerHTML = '<i class="feather-save me-1"></i> Save Marks';
        });
    });


    // ══════════════════════════════════════════
    // TAB 2 — BY STUDENT
    // ══════════════════════════════════════════
    const stuExamSelect  = document.getElementById('stu_exam_id');
    const stuStudentSel  = document.getElementById('stu_student_id');
    const stuMarkDate    = document.getElementById('stu_mark_date');
    const stuLoadingBox  = document.getElementById('stuLoadingBox');
    const stuMarkForm    = document.getElementById('stuMarkForm');
    const stuEmptyState  = document.getElementById('stuEmptyState');
    const stuMarksInput  = document.getElementById('stu_marks');
    const stuGradeSelect = document.getElementById('stu_grade');
    const stuRemarks     = document.getElementById('stu_remarks');
    const stuSaveBtn     = document.getElementById('stuSaveBtn');
    const stuClearBtn    = document.getElementById('stuClearBtn');
    const stuResultBadge = document.getElementById('stuResultBadge');
    const stuResultText  = document.getElementById('stuResultText');

    // Step 1: exam changed → load all students for that exam
    stuExamSelect.addEventListener('change', function () {
        const examId = this.value;
        stuStudentSel.disabled = true;
        stuStudentSel.innerHTML = '<option value="">— Loading… —</option>';
        stuMarkForm.classList.add('d-none');
        stuEmptyState.classList.add('d-none');
        stuAllStudents = [];
        stuExisting    = {};

        if (!examId) {
            stuStudentSel.innerHTML = '<option value="">— Select exam first —</option>';
            return;
        }

        stuLoadingBox.classList.remove('d-none');

        fetch(`${STUDENTS_URL}?exam_id=${examId}`, {
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            stuLoadingBox.classList.add('d-none');
            stuExamData    = data.exam;
            stuGrades      = data.grades ?? [];
            stuAllStudents = data.students;
            stuExisting    = data.existing ?? {};

            if (!data.students.length) {
                stuStudentSel.innerHTML = '<option value="">— No students —</option>';
                stuEmptyState.classList.remove('d-none');
                return;
            }

            // Populate student dropdown
            stuStudentSel.innerHTML = '<option value="">— Choose Student —</option>' +
                data.students.map(s =>
                    `<option value="${s.student_id}" data-roll="${esc(s.roll_number ?? '')}">
                        ${esc(s.roll_number ? s.roll_number + ' — ' : '')}${esc(s.first_name)} ${esc(s.last_name)}
                    </option>`
                ).join('');
            stuStudentSel.disabled = false;

            // Populate grade options
            stuGradeSelect.innerHTML = '<option value="">— Auto —</option>' +
                stuGrades.map(g => `<option value="${esc(g.letter)}">${esc(g.letter)}</option>`).join('');

            // Update info badges
            document.getElementById('stuSubject').textContent = data.exam.subject;
            document.getElementById('stuTotal').textContent   = data.exam.total_marks;
            document.getElementById('stuPass').textContent    = data.exam.pass_marks;
            stuMarksInput.max = data.exam.total_marks;
            document.getElementById('stuMarksHint').textContent = `Max: ${data.exam.total_marks}`;
        })
        .catch(() => {
            stuLoadingBox.classList.add('d-none');
            alert('Failed to load students. Please try again.');
        });
    });

    // Step 2: student selected → prefill existing marks
    stuStudentSel.addEventListener('change', function () {
        const studentId = this.value;
        stuMarkForm.classList.add('d-none');
        stuResultBadge.classList.add('d-none');
        stuClearFields();

        if (!studentId) return;

        const student = stuAllStudents.find(s => s.student_id == studentId);
        if (!student) return;

        document.getElementById('stuStudentName').textContent =
            `${student.first_name} ${student.last_name}`;
        document.getElementById('stuRollNo').textContent = student.roll_number ?? '—';

        // Prefill existing marks if any
        const ex = stuExisting[studentId] ?? {};
        stuMarksInput.value   = ex.marks_obtained ?? '';
        stuGradeSelect.value  = ex.grade_letter   ?? '';
        stuRemarks.value      = ex.remarks        ?? '';

        if (ex.marks_obtained !== undefined) {
            showPassFail(ex.marks_obtained);
        }

        stuMarkForm.classList.remove('d-none');
    });

    // Auto-grade on marks input
    stuMarksInput.addEventListener('input', function () {
        if (!stuGrades.length) return;
        const val = parseFloat(this.value);
        if (isNaN(val)) { stuResultBadge.classList.add('d-none'); return; }
        const match = stuGrades.find(g => val >= g.min_marks && val <= g.max_marks);
        stuGradeSelect.value = match ? match.letter : '';
        showPassFail(val);
    });

    function showPassFail(val) {
        if (!stuExamData) return;
        stuResultBadge.classList.remove('d-none');
        const passed = parseFloat(val) >= parseFloat(stuExamData.pass_marks);
        stuResultText.className = `badge fs-6 px-3 py-2 ${passed ? 'bg-success' : 'bg-danger'}`;
        stuResultText.textContent = passed ? '✓ Pass' : '✗ Fail';
    }

    stuClearBtn.addEventListener('click', stuClearFields);
    function stuClearFields() {
        stuMarksInput.value  = '';
        stuGradeSelect.value = '';
        stuRemarks.value     = '';
        stuResultBadge.classList.add('d-none');
    }

    // Save single student mark
    stuSaveBtn.addEventListener('click', function () {
        const examId    = stuExamSelect.value;
        const studentId = stuStudentSel.value;
        const markDate  = stuMarkDate.value;
        const marksVal  = stuMarksInput.value;

        if (!examId)    { alert('Please select an exam.');    return; }
        if (!studentId) { alert('Please select a student.');  return; }
        if (!markDate)  { alert('Please select a date.');     return; }
        if (marksVal === '') { alert('Please enter marks.');  return; }

        stuSaveBtn.disabled = true;
        stuSaveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving…';

        fetch(STORE_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            body: JSON.stringify({
                exam_id: examId,
                marks: [{
                    student_id:     studentId,
                    marks_obtained: marksVal,
                    grade_letter:   stuGradeSelect.value,
                    remarks:        stuRemarks.value,
                    mark_date:      markDate,
                }],
            }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.message) {
                // Add to session saved list
                const student = stuAllStudents.find(s => s.student_id == studentId);
                stuSavedRows.push({
                    name:   `${student?.first_name ?? ''} ${student?.last_name ?? ''}`.trim(),
                    marks:  marksVal,
                    grade:  stuGradeSelect.value || '—',
                    date:   markDate,
                });
                renderSavedList();

                // Update existing cache
                stuExisting[studentId] = {
                    marks_obtained: marksVal,
                    grade_letter:   stuGradeSelect.value,
                    remarks:        stuRemarks.value,
                };

                // Show toast-like feedback
                stuSaveBtn.innerHTML = '<i class="feather-check me-1"></i> Saved!';
                stuSaveBtn.classList.replace('btn-primary', 'btn-success');
                setTimeout(() => {
                    stuSaveBtn.innerHTML = '<i class="feather-save me-1"></i> Save Mark';
                    stuSaveBtn.classList.replace('btn-success', 'btn-primary');
                    stuSaveBtn.disabled = false;
                }, 1500);
            }
        })
        .catch(() => {
            alert('Failed to save. Please try again.');
            stuSaveBtn.disabled = false;
            stuSaveBtn.innerHTML = '<i class="feather-save me-1"></i> Save Mark';
        });
    });

    function renderSavedList() {
        const container = document.getElementById('stuSavedList');
        const tbody     = document.getElementById('stuSavedTbody');
        container.classList.remove('d-none');
        tbody.innerHTML = stuSavedRows.map(r => `
            <tr>
                <td>${esc(r.name)}</td>
                <td><strong>${esc(r.marks)}</strong></td>
                <td><span class="badge bg-secondary">${esc(r.grade)}</span></td>
                <td class="text-muted">${esc(r.date)}</td>
            </tr>`).join('');
    }

    // ─────────────────────────────────────────
    // Shared: auto-grade helper
    // ─────────────────────────────────────────
    function autoGrade(input, grades, tbody) {
        const val = parseFloat(input.value);
        const sid = input.dataset.studentId;
        const sel = tbody.querySelector(`.grade-select[data-student-id="${sid}"]`);
        if (!sel || isNaN(val)) return;
        const match = grades.find(g => val >= g.min_marks && val <= g.max_marks);
        sel.value = match ? match.letter : '';
    }

    // Auto-load if editing
    @if(isset($examMark))
        bulkExamSelect.dispatchEvent(new Event('change'));
    @endif
});
</script>

<style>
.content-area { overflow-y: auto !important; }
.nav-tabs .nav-link { color: #6c757d; font-size: 0.9rem; }
.nav-tabs .nav-link.active { color: #0d6efd; border-bottom: 2px solid #0d6efd; }
.marks-input:focus { border-color: #0d6efd; box-shadow: 0 0 0 2px rgba(13,110,253,.15); }
.table th { font-size: 0.8125rem; white-space: nowrap; padding: 0.75rem 1rem; }
.table td { padding: 0.65rem 1rem; vertical-align: middle; font-size: 0.875rem; }
#stuSavedList .table { font-size: 0.85rem; }
</style>
@endsection