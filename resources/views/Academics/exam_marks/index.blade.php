@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Enter Exam Marks</span>
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
                    <div class="card stretch stretch-full">
                        <div class="card-body general-info">

                            <div class="mb-5 d-flex align-items-center justify-content-between">
                                <h5 class="fw-bold mb-0 me-4">
                                    <span class="d-block mb-2">Exam Marks Filters :</span>
                                    <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                        Select academic year, branch, semester, section and exam
                                    </span>
                                </h5>
                            </div>

                            {{-- ── FILTERS ROW ── --}}
                            <div class="row mb-5">

                                {{-- Course --}}
                                <div class="col-lg-2">
                                    <label for="course_id" class="fw-semibold mb-2 d-block">Course:</label>
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-book-open"></i></div>
                                        <select class="form-select" id="course_id">
                                            <option value="">Select Course</option>
                                            @foreach ($courses as $course)
                                                <option value="{{ $course->id }}">{{ $course->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Academic Year --}}
                                <div class="col-lg-2">
                                    <label for="academic_year_id" class="fw-semibold mb-2 d-block">Academic Year:</label>
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-calendar"></i></div>
                                        <select class="form-select" id="academic_year_id">
                                            <option value="">Select Year</option>
                                            @foreach ($academicYears as $year)
                                                <option value="{{ $year->id }}">{{ $year->name ?? $year->start_year.'-'.$year->end_year }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Branch --}}
                                <div class="col-lg-2">
                                    <label for="stream_id" class="fw-semibold mb-2 d-block">Branch:</label>
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-book"></i></div>
                                        <select class="form-select" id="stream_id" disabled>
                                            <option value="">Select Branch</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Semester --}}
                                <div class="col-lg-2">
                                    <label for="class_id" class="fw-semibold mb-2 d-block">Semester:</label>
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-hash"></i></div>
                                        <select class="form-select" id="class_id" disabled>
                                            <option value="">Select Semester</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Section --}}
                                <div class="col-lg-2">
                                    <label for="section_id" class="fw-semibold mb-2 d-block">Section:</label>
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-hash"></i></div>
                                        <select class="form-select" id="section_id" disabled>
                                            <option value="">Select Section</option>
                                        </select>
                                    </div>
                                </div>

                            </div>

                            {{-- ── EXAM + EXCEL UPLOAD ROW ── --}}
                            <div class="row mb-5">

                                {{-- Exam --}}
                                <div class="col-lg-4">
                                    <label for="exam_id" class="fw-semibold mb-2 d-block">Exam:</label>
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-file-text"></i></div>
                                        <select class="form-select" id="exam_id" disabled>
                                            <option value="">Select Exam</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Excel Upload --}}
                                <div class="col-lg-4 d-flex align-items-end">
                                    <div class="w-100">
                                        <label class="fw-semibold mb-2 d-block">Upload Marks Excel:</label>

                                        <div id="dropZone"
                                            style="border: 2px dashed #dee2e6; border-radius: 10px; padding: 22px 16px; text-align: center; cursor: pointer; transition: border-color 0.2s, background 0.2s; background: #fff;">
                                            <div style="width: 40px; height: 40px; background: #e8f4fd; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px;">
                                                <i class="feather-upload-cloud" style="color: #0d6efd; font-size: 18px;"></i>
                                            </div>
                                            <p class="mb-1 fw-semibold small">Drag & drop your Excel file here</p>
                                            <p class="text-muted mb-2" style="font-size: 12px;">Supports .xlsx and .xls</p>
                                            <button type="button" id="browseExcelBtn" class="btn btn-sm btn-outline-primary px-3">
                                                Browse File
                                            </button>
                                            <input type="file" id="excelFile" accept=".xlsx,.xls" style="display:none">
                                        </div>

                                        <div class="mt-2 d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center gap-1">
                                                <i class="feather-download text-muted" style="font-size: 13px;"></i>
                                                <a href="#" id="downloadTemplate" class="small text-primary">Download template</a>
                                            </div>
                                            <span id="selectedFileName" class="small text-muted fst-italic"></span>
                                        </div>

                                        <div id="uploadStatus" class="mt-2" style="display:none;"></div>

                                        {{-- Excel Preview --}}
                                        <div id="excelPreviewCard" class="mt-3"
                                            style="display:none; border: 1px solid #dee2e6; border-radius: 10px; overflow: hidden;">
                                            <div class="d-flex align-items-center justify-content-between px-3 py-2"
                                                style="background: #f8f9fa; border-bottom: 1px solid #dee2e6;">
                                                <span class="fw-semibold small" id="previewTitle">Preview</span>
                                                <div class="d-flex gap-2 align-items-center">
                                                    <span id="previewStats" class="small text-muted"></span>
                                                    <button type="button" id="submitExcelBtn" class="btn btn-success btn-sm px-3">
                                                        <i class="feather-check me-1"></i>Submit via Excel
                                                    </button>
                                                    <button type="button" id="clearExcelBtn" class="btn btn-outline-secondary btn-sm">
                                                        <i class="feather-x"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="table-responsive" style="max-height: 280px; overflow-y: auto;">
                                                <table class="table table-sm table-hover mb-0" style="font-size: 13px;">
                                                    <thead class="table-light sticky-top">
                                                        <tr>
                                                            <th width="70">Admission Number</th>
                                                            <th style="width:80px;">Roll No</th>
                                                            <th>Student Name</th>
                                                            <th style="width:100px;">Marks</th>
                                                            <th style="width:80px;">Grade</th>
                                                            <th>Remarks</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="excelPreviewBody"></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Submit Button --}}
                                <div class="col-lg-4 mt-3 d-flex justify-content-end" style="height: 50px;">
                                    <button type="button" id="submitMarksBtn" class="btn btn-primary">
                                        Submit Marks
                                    </button>
                                </div>

                            </div>

                            {{-- ── STUDENTS TABLE ── --}}
                            <div id="studentsTable" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h6 id="studentsCount" class="mb-0 fw-semibold"></h6>
                                    <div class="hstack gap-2">
                                        <button type="button" id="fillMaxMarksBtn"
                                                class="btn btn-outline-success btn-sm">
                                            <i class="feather-check-circle me-1"></i> Fill Max Marks All
                                        </button>
                                        <span id="examInfoBadge" class="badge bg-light text-dark me-2"></span>
                                        <span id="classSectionBadge" class="badge bg-secondary text-white"></span>
                                    </div>
                                </div>

                                <form id="marksForm" style="display: none;">
                                    @csrf
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="70">Admission Number</th>
                                                    <th width="70">Roll No</th>
                                                    <th>Student Name</th>
                                                    <th width="180">
                                                        Marks Obtained
                                                        <small class="text-muted fw-normal" id="thTotalMarks"></small>
                                                    </th>
                                                    <th width="120">Grade</th>
                                                    <th width="200">Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody id="studentsList"></tbody>
                                        </table>
                                    </div>
                                </form>
                            </div>

                            {{-- ── NO STUDENTS ── --}}
                            <div id="noStudents" class="text-center py-8" style="display: none;">
                                <i class="feather-users fs-1 text-muted mb-3 d-block"></i>
                                <h5 class="text-muted mb-2">No Students Found</h5>
                                <p class="text-muted mb-4">No active enrollments found for this exam's class and section.</p>
                                <button type="button" id="loadStudentsAgain" class="btn btn-outline-primary">
                                    Try Again
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
    $(document).ready(function () {

        let studentsData    = [];
        let gradesData      = [];
        let examMeta        = {};
        let parsedExcelData = [];

        /* ══════════════════════════════════════
           RESET HELPERS
        ══════════════════════════════════════ */
        function resetStudents() {
            studentsData = [];
            gradesData   = [];
            examMeta     = {};
            $('#studentsList').empty();
            $('#studentsTable, #marksForm, #noStudents').hide();
            $('#studentsCount, #examInfoBadge, #classSectionBadge').text('');
            $('#submitMarksBtn').prop('disabled', false).text('Submit Marks');
            $('#thTotalMarks').text('');
            resetExcelUpload();
        }

        function resetExams() {
            $('#exam_id').empty().append('<option value="">Select Exam</option>').prop('disabled', true);
            resetStudents();
        }

        function resetSections() {
            $('#section_id').empty().append('<option value="">Select Section</option>').prop('disabled', true);
            resetExams();
        }

        function resetClasses() {
            $('#class_id').empty().append('<option value="">Select Semester</option>').prop('disabled', true);
            resetSections();
        }

        function resetStreams() {
            $('#stream_id').empty().append('<option value="">Select Branch</option>').prop('disabled', true);
            resetClasses();
        }

        /* ══════════════════════════════════════
           1. LOAD STREAMS
        ══════════════════════════════════════ */
        function loadStreams() {
            const courseId = $('#course_id').val();
            resetStreams();
            if (!courseId) return;

            $.ajax({
                url: "{{ route('admin.student_attendance.get_streams') }}",
                method: 'POST',
                data: { course_id: courseId, _token: '{{ csrf_token() }}' },
                success(res) {
                    const $s = $('#stream_id');
                    if (res.streams && res.streams.length) {
                        res.streams.forEach(st => $s.append(`<option value="${st.id}">${st.name}</option>`));
                        $s.prop('disabled', false);
                    } else {
                        toastr.info('No branches found for this course.');
                    }
                },
                error(xhr) { toastr.error(xhr.responseJSON?.message || 'Failed to load branches'); }
            });
        }

        /* ══════════════════════════════════════
           2. LOAD CLASSES
        ══════════════════════════════════════ */
        function loadClasses() {
            const courseId       = $('#course_id').val();
            const academicYearId = $('#academic_year_id').val();
            const streamId       = $('#stream_id').val();
            resetClasses();
            if (!courseId || !academicYearId || !streamId) return;

            $.ajax({
                url: "{{ route('admin.student_attendance.get_classes') }}",
                method: 'POST',
                data: {
                    academic_year_id: academicYearId,
                    course_id: courseId,
                    stream_id: streamId,
                    _token: '{{ csrf_token() }}'
                },
                success(res) {
                    const $s = $('#class_id');
                    if (res.classes && res.classes.length) {
                        res.classes.forEach(c => $s.append(`<option value="${c.id}">${c.name}</option>`));
                        $s.prop('disabled', false);
                    } else {
                        toastr.info('No semesters found for this selection.');
                    }
                },
                error(xhr) { toastr.error(xhr.responseJSON?.message || 'Failed to load semesters'); }
            });
        }

        /* ══════════════════════════════════════
           3. LOAD SECTIONS
        ══════════════════════════════════════ */
        function loadSections() {
            const courseId = $('#course_id').val();
            const streamId = $('#stream_id').val();
            const classId  = $('#class_id').val();
            resetSections();
            if (!courseId || !streamId || !classId) return;

            $.ajax({
                url: "{{ route('admin.student_attendance.get_sections') }}",
                method: 'POST',
                data: {
                    course_id: courseId,
                    stream_id: streamId,
                    class_id: classId,
                    _token: '{{ csrf_token() }}'
                },
                success(res) {
                    const $s = $('#section_id');
                    if (res.sections && res.sections.length) {
                        res.sections.forEach(sec => $s.append(`<option value="${sec.id}">${sec.name}</option>`));
                        $s.prop('disabled', false);
                    } else {
                        toastr.info('No sections found for this class.');
                    }
                },
                error(xhr) { toastr.error(xhr.responseJSON?.message || 'Failed to load sections'); }
            });
        }

        /* ══════════════════════════════════════
           4. LOAD EXAMS  ← FIXED: POST → GET
        ══════════════════════════════════════ */
        function loadExams() {
            const academicYearId = $('#academic_year_id').val();
            const courseId       = $('#course_id').val();
            const streamId       = $('#stream_id').val();
            const classId        = $('#class_id').val();
            const sectionId      = $('#section_id').val();
            resetExams();
            if (!academicYearId || !courseId || !streamId || !classId || !sectionId) return;

            $.ajax({
                url: "{{ route('admin.exam_marks.exams_by_filters') }}",
                method: 'GET',                          // ← was POST, now GET
                data: {
                    academic_year_id: academicYearId,
                    course_id:        courseId,
                    stream_id:        streamId,
                    class_id:         classId,
                    section_id:       sectionId,
                },
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                success(res) {
                    const $s = $('#exam_id');
                    if (res.exams && res.exams.length) {
                        res.exams.forEach(e => {
                            $s.append(`<option value="${e.id}"
                                data-total="${e.total_marks}"
                                data-pass="${e.pass_marks}"
                                data-subject="${e.subject}"
                                data-type="${e.exam_type}">
                                ${e.name} (${e.subject})
                            </option>`);
                        });
                        $s.prop('disabled', false);
                    } else {
                        toastr.info('No exams found for this selection.');
                    }
                    resetStudents();
                },
                error(xhr) { toastr.error(xhr.responseJSON?.message || 'Failed to load exams'); }
            });
        }
        /* ══════════════════════════════════════
           5. LOAD STUDENTS
        ══════════════════════════════════════ */
        function loadStudents() {
            const examId = $('#exam_id').val();
            if (!examId) { toastr.warning('Please select an exam.'); return; }
            resetStudents();
            resetExcelUpload();

            $.ajax({
                url: "{{ route('admin.exam_marks.students_by_exam') }}",
                method: 'GET',
                data: { exam_id: examId },
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                success(res) {
                    studentsData = Array.isArray(res.students) ? res.students : [];
                    gradesData   = Array.isArray(res.grades)   ? res.grades   : [];
                    examMeta     = res.exam ?? {};
                    $('#thTotalMarks').text('/ ' + (examMeta.total_marks ?? ''));
                    displayStudents(res.existing ?? {});
                },
                error(xhr) { toastr.error(xhr.responseJSON?.message || 'Failed to load students'); }
            });
        }

        /* ══════════════════════════════════════
           EVENT BINDINGS
        ══════════════════════════════════════ */
        $('#course_id').on('change', loadStreams);
        $('#stream_id').on('change', loadClasses);
        $('#academic_year_id').on('change', loadClasses);
        $('#class_id').on('change', loadSections);
        $('#section_id').on('change', loadExams);
        $('#exam_id').on('change', function () {
            resetStudents();
            if ($(this).val()) loadStudents();
        });
        $('#loadStudentsAgain').on('click', loadStudents);

        /* ══════════════════════════════════════
           DISPLAY STUDENTS
        ══════════════════════════════════════ */
        function displayStudents(existing) {
            const $list = $('#studentsList').empty();

            if (!studentsData.length) {
                $('#studentsTable').hide();
                $('#noStudents').show();
                return;
            }

            $('#studentsTable').show();
            $('#noStudents').hide();
            $('#studentsCount').text(studentsData.length + ' students loaded');

            const selectedOpt = $('#exam_id option:selected');
            $('#examInfoBadge').text(selectedOpt.data('subject') ?? '');
            $('#classSectionBadge').text(
                $('#class_id option:selected').text() + ' - ' + $('#section_id option:selected').text()
            );

            const allSubmitted = studentsData.length > 0 &&
                studentsData.every(s => existing[s.student_id]);

            studentsData.forEach(student => {
                const sid     = student.student_id;
                const ex      = existing[sid] ?? {};
                const marks   = ex.marks_obtained ?? '';
                const grade   = ex.grade_letter   ?? '';
                const remarks = ex.remarks        ?? '';
                const disabled = '';

                const gradeOpts = `<option value="">—</option>` +
                    gradesData.map(g =>
                        `<option value="${g.letter}" ${grade === g.letter ? 'selected' : ''}>${g.letter}</option>`
                    ).join('');

                $list.append(`
                    <tr data-student-id="${sid}">
                        <td>${student.admission_number ?? '—'}</td>
                        <td>${student.roll_number ?? '—'}</td>
                        <td>${student.first_name} ${student.last_name}</td>
                        <td>
                            <input type="number"
                                   class="form-control form-control-sm marks-input"
                                   data-student-id="${sid}"
                                   value="${marks}"
                                   min="0"
                                   max="${examMeta.total_marks ?? ''}"
                                   step="0.01"
                                   placeholder="0"
                                   style="width:110px;"
                                   ${disabled}>
                        </td>
                        <td>
                            <select class="form-select form-select-sm grade-select"
                                    data-student-id="${sid}"
                                    style="width:100px;"
                                    ${disabled}>
                                ${gradeOpts}
                            </select>
                        </td>
                        <td>
                            <input type="text"
                                   class="form-control form-control-sm remarks-input"
                                   data-student-id="${sid}"
                                   value="${remarks}"
                                   placeholder="Remarks"
                                   maxlength="500"
                                   ${disabled}>
                        </td>
                    </tr>
                `);
            });

            $('#marksForm').show();

            if (allSubmitted) {
              $('#submitMarksBtn').prop('disabled', false).text('Update Marks');
            } else {
                $('#submitMarksBtn').prop('disabled', false).text('Submit Marks');
            }

            // Auto-grade
            $('#studentsList').off('input', '.marks-input').on('input', '.marks-input', function () {
                if (!gradesData.length) return;
                const val = parseFloat($(this).val());
                const sid = $(this).data('student-id');
                if (isNaN(val)) return;
                const match = gradesData.find(g => val >= g.min_marks && val <= g.max_marks);
                $(`.grade-select[data-student-id="${sid}"]`).val(match ? match.letter : '');
            });
        }

        /* ══════════════════════════════════════
           FILL MAX MARKS
        ══════════════════════════════════════ */
        $('#fillMaxMarksBtn').on('click', function () {
            if (!examMeta.total_marks) return;
            $('.marks-input').val(examMeta.total_marks).trigger('input');
        });

        /* ══════════════════════════════════════
           MANUAL SUBMIT
        ══════════════════════════════════════ */
        $('#submitMarksBtn').on('click', function () {
            const examId = $('#exam_id').val();
            if (!examId) { toastr.warning('Please select an exam.'); return; }
            if (!studentsData.length) { toastr.warning('No students loaded.'); return; }

            const marks = [];
            studentsData.forEach(student => {
                const sid      = student.student_id;
                const marksVal = $(`.marks-input[data-student-id="${sid}"]`).val();
                if (marksVal === '' || marksVal === null) return;
                marks.push({
                    student_id:     sid,
                    marks_obtained: marksVal,
                    grade_letter:   $(`.grade-select[data-student-id="${sid}"]`).val() || '',
                    remarks:        $(`.remarks-input[data-student-id="${sid}"]`).val() || '',
                });
            });

            if (!marks.length) { toastr.warning('Please enter marks for at least one student.'); return; }

            // submitMarks(examId, marks, function () {
            //     toastr.success('Marks submitted successfully!');
            //     $('#studentsTable, #marksForm').hide();
            // });
            submitMarks(examId, marks, function () {
                toastr.success('Marks submitted successfully!');
                $('#studentsTable').html(`
                    <div class="text-center py-5">
                        <div style="width:70px;height:70px;background:#d1fae5;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                            <i class="feather-check-circle" style="color:#10b981;font-size:32px;"></i>
                        </div>
                        <h5 class="fw-bold text-success mb-2">Marks Submitted Successfully!</h5>
                        <p class="text-muted mb-4">Marks for <strong>${marks.length} student(s)</strong> have been saved.</p>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="location.reload()">
                            <i class="feather-refresh-cw me-1"></i> Submit for Another Exam
                        </button>
                    </div>
                `).show();
            });
        });

        /* ══════════════════════════════════════
           SHARED SUBMIT
        ══════════════════════════════════════ */
        function submitMarks(examId, marks, onSuccess) {
            $.ajax({
                url: "{{ route('admin.exam_marks.store') }}",
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ exam_id: examId, marks }),
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                beforeSend() { $('#submitMarksBtn, #submitExcelBtn').prop('disabled', true); },
                success(res) { onSuccess(res); },
                error(xhr)   { toastr.error(xhr.responseJSON?.message || 'Failed to save marks'); },
                complete()   { $('#submitMarksBtn, #submitExcelBtn').prop('disabled', false); }
            });
        }

        /* ══════════════════════════════════════
           TEMPLATE DOWNLOAD
        ══════════════════════════════════════ */
        $('#downloadTemplate').on('click', function (e) {
            e.preventDefault();
            if (!studentsData.length) {
                const rows = [
                    ['Roll No', 'Student Name', 'Marks Obtained', 'Grade', 'Remarks'],
                    ['001', 'Example Student', '85', 'A', ''],
                ];
                exportExcel(rows, 'exam_marks_template.xlsx');
                toastr.info('Generic template downloaded. Load students first for pre-filled template.');
                return;
            }
            const rows = [['Roll No', 'Student Name', 'Marks Obtained', 'Grade', 'Remarks']];
            studentsData.forEach(s => rows.push([s.roll_number, `${s.first_name} ${s.last_name}`, '', '', '']));
            exportExcel(rows, `exam_marks_${$('#exam_id option:selected').text().split('(')[0].trim()}.xlsx`);
            toastr.success(`Template with ${studentsData.length} students downloaded!`);
        });

        function exportExcel(rows, filename) {
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(rows);
            ws['!cols'] = [{ wch: 12 }, { wch: 28 }, { wch: 16 }, { wch: 10 }, { wch: 25 }];
            XLSX.utils.book_append_sheet(wb, ws, 'Marks');
            XLSX.writeFile(wb, filename);
        }

        /* ══════════════════════════════════════
           DRAG & DROP
        ══════════════════════════════════════ */
        const dropZone = document.getElementById('dropZone');

        dropZone.addEventListener('dragover', e => {
            e.preventDefault();
            dropZone.style.borderColor = '#0d6efd';
            dropZone.style.background  = '#f0f6ff';
        });
        dropZone.addEventListener('dragleave', () => {
            dropZone.style.borderColor = '#dee2e6';
            dropZone.style.background  = '#fff';
        });
        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.style.borderColor = '#dee2e6';
            dropZone.style.background  = '#fff';
            const file = e.dataTransfer.files[0];
            if (file) processExcelFile(file);
        });

        dropZone.addEventListener('click', () => $('#excelFile').click());
        $('#browseExcelBtn').on('click', e => { e.stopPropagation(); $('#excelFile').click(); });
        $('#excelFile').on('change', function () {
            if (this.files[0]) processExcelFile(this.files[0]);
            this.value = '';
        });

        /* ══════════════════════════════════════
           EXCEL PARSE & PREVIEW
        ══════════════════════════════════════ */
        function processExcelFile(file) {
            if (!studentsData.length) {
                toastr.warning('Please select an exam and load students first, then upload Excel.');
                return;
            }
            if (!file.name.match(/\.(xlsx|xls)$/i)) {
                toastr.error('Please upload a valid .xlsx or .xls file.');
                return;
            }

            $('#selectedFileName').text(file.name);

            const reader = new FileReader();
            reader.onload = function (e) {
                try {
                    const wb   = XLSX.read(e.target.result, { type: 'binary' });
                    const ws   = wb.Sheets[wb.SheetNames[0]];
                    const rows = XLSX.utils.sheet_to_json(ws, { header: 1, defval: '' });

                    if (rows.length < 2) { showUploadStatus('danger', 'Excel file is empty.'); return; }

                    const header     = rows[0].map(h => String(h).toLowerCase().trim());
                    const colRoll    = header.findIndex(h => h.includes('roll'));
                    const colMarks   = header.findIndex(h => h.includes('mark'));
                    const colGrade   = header.findIndex(h => h.includes('grade'));
                    const colRemarks = header.findIndex(h => h.includes('remark'));

                    if (colRoll  === -1) { showUploadStatus('danger', 'Column "Roll No" not found.'); return; }
                    if (colMarks === -1) { showUploadStatus('danger', 'Column "Marks Obtained" not found.'); return; }

                    const rollMap = {};
                    studentsData.forEach(s => { rollMap[String(s.roll_number).trim().toLowerCase()] = s; });

                    const matched = [], unrecognized = [], invalidMarks = [];

                    rows.slice(1).forEach(row => {
                        const roll     = String(row[colRoll] ?? '').trim();
                        if (!roll) return;
                        const marksRaw = row[colMarks];
                        const marks    = parseFloat(marksRaw);
                        const grade    = colGrade   !== -1 ? String(row[colGrade]   ?? '').trim() : '';
                        const remarks  = colRemarks !== -1 ? String(row[colRemarks] ?? '').trim() : '';
                        const student  = rollMap[roll.toLowerCase()];

                        if (!student) { unrecognized.push(roll); return; }
                        if (isNaN(marks) || marks < 0 || marks > (examMeta.total_marks ?? Infinity)) {
                            invalidMarks.push(`Roll ${roll} → "${marksRaw}"`); return;
                        }

                        let finalGrade = grade;
                        if (!finalGrade && gradesData.length) {
                            const match = gradesData.find(g => marks >= g.min_marks && marks <= g.max_marks);
                            finalGrade  = match ? match.letter : '';
                        }

                        matched.push({ studentId: student.student_id, roll: student.roll_number,
                            name: `${student.first_name} ${student.last_name}`, marks, grade: finalGrade, remarks });
                    });

                    parsedExcelData = matched;
                    renderExcelPreview(matched, unrecognized, invalidMarks);

                } catch (err) {
                    showUploadStatus('danger', 'Could not parse file. Please use the downloaded template.');
                }
            };
            reader.readAsBinaryString(file);
        }

        function renderExcelPreview(matched, unrecognized, invalidMarks) {
            const $tbody = $('#excelPreviewBody').empty();
            matched.forEach(row => {
                const pct = examMeta.total_marks ? (row.marks / examMeta.total_marks * 100) : 0;
                const cls = pct >= 75 ? 'bg-success-subtle text-success' :
                            pct >= 40 ? 'bg-warning-subtle text-warning' : 'bg-danger-subtle text-danger';
                $tbody.append(`
                    <tr>
                        <td class="text-muted">${row.roll}</td>
                        <td>${row.name}</td>
                        <td><span class="badge ${cls}">${row.marks}</span></td>
                        <td>${row.grade ? `<span class="badge bg-info-subtle text-info">${row.grade}</span>` : '—'}</td>
                        <td class="text-muted">${row.remarks || '—'}</td>
                    </tr>
                `);
            });

            let summary = `${matched.length} of ${studentsData.length} students matched`;
            if (unrecognized.length) summary += ` · <span class="text-danger">${unrecognized.length} unrecognized</span>`;
            if (invalidMarks.length)  summary += ` · <span class="text-warning">${invalidMarks.length} invalid</span>`;

            $('#previewTitle').text('Excel preview — ' + $('#exam_id option:selected').text().split('(')[0].trim());
            $('#previewStats').html(summary);
            $('#excelPreviewCard').show();

            if (unrecognized.length || invalidMarks.length) {
                let msg = '';
                if (unrecognized.length) msg += `Unrecognized rolls: ${unrecognized.join(', ')}. `;
                if (invalidMarks.length)  msg += `Invalid marks: ${invalidMarks.join('; ')}.`;
                showUploadStatus('warning', msg);
            } else {
                showUploadStatus('success', `All ${matched.length} rows parsed successfully.`);
            }
        }

        /* ══════════════════════════════════════
           EXCEL SUBMIT
        ══════════════════════════════════════ */
        $('#submitExcelBtn').on('click', function () {
            if (!parsedExcelData.length) { toastr.warning('No valid data to submit.'); return; }
            const examId = $('#exam_id').val();
            if (!examId) { toastr.warning('Please select an exam.'); return; }

            const marks = parsedExcelData.map(row => ({
                student_id:     row.studentId,
                marks_obtained: row.marks,
                grade_letter:   row.grade || '',
                remarks:        row.remarks || '',
            }));

            if (!confirm(`Submit marks for ${marks.length} students via Excel?`)) return;

            // submitMarks(examId, marks, function () {
            //     toastr.success('Marks submitted via Excel!');
            //     resetExcelUpload();
            //     $('#studentsTable, #marksForm').hide();
            // });
                submitMarks(examId, marks, function () {
                toastr.success('Marks submitted via Excel!');
                resetExcelUpload();
                $('#studentsTable').html(`
                    <div class="text-center py-5">
                        <div style="width:70px;height:70px;background:#d1fae5;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                            <i class="feather-check-circle" style="color:#10b981;font-size:32px;"></i>
                        </div>
                        <h5 class="fw-bold text-success mb-2">Marks Submitted Successfully!</h5>
                        <p class="text-muted mb-4">Marks for <strong>${marks.length} student(s)</strong> have been saved via Excel.</p>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="location.reload()">
                            <i class="feather-refresh-cw me-1"></i> Submit for Another Exam
                        </button>
                    </div>
                `).show();
            });
        });

        $('#clearExcelBtn').on('click', resetExcelUpload);

        function resetExcelUpload() {
            parsedExcelData = [];
            $('#excelPreviewCard').hide();
            $('#excelPreviewBody').empty();
            $('#uploadStatus').hide().empty();
            $('#selectedFileName').text('');
            $('#excelFile').val('');
        }

        function showUploadStatus(type, msg) {
            const icons = { success: 'check-circle', warning: 'alert-triangle', danger: 'alert-circle' };
            $('#uploadStatus').show().html(`
                <div class="alert alert-${type} py-2 px-3 mb-0 d-flex align-items-start gap-2" style="font-size: 13px;">
                    <i class="feather-${icons[type] || 'info'} flex-shrink-0 mt-1" style="font-size: 14px;"></i>
                    <span>${msg}</span>
                </div>
            `);
        }

    });
    </script>
@endsection
