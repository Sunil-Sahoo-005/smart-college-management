@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Mark Student Attendance</span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                            <a href="" class="btn btn-secondary">
                                <i class="feather-arrow-left me-2"></i>
                                <span>Back to Dashboard</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="content-area-body">
                    <div class="card stretch stretch-full">
                        <div class="card-body general-info">
                            <div class="mb-5 d-flex align-items-center justify-content-between">
                                <h5 class="fw-bold mb-0 me-4">
                                    <span class="d-block mb-2">Attendance Filters :</span>
                                    <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                        Select academic year, branch, semester, section, date and slot
                                    </span>
                                </h5>
                            </div>

                            {{-- Filters Row --}}
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
                                                <option value="{{ $year->id }}">{{ $year->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Branch — starts EMPTY, filled via AJAX after course selected --}}
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

                                {{-- Date --}}
                                <div class="col-lg-2">
                                    <label for="date" class="fw-semibold mb-2 d-block">Date:</label>
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-calendar"></i></div>
                                        <input type="date" class="form-control" id="date"
                                            value="{{ date('Y-m-d') }}"
                                            min="{{ date('Y-m-d') }}"
                                            max="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                            </div>

                            {{-- Period + Excel Upload Row --}}
                            <div class="row mb-5">
                                <div class="col-lg-4">
                                    <label for="slot_id" class="fw-semibold mb-2 d-block">Period:</label>
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-clock"></i></div>
                                        <select class="form-select" id="slot_id" disabled>
                                            <option value="">Select Period</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-4 d-flex align-items-end">
                                    <div class="w-100">
                                        <label class="fw-semibold mb-2 d-block">Upload Attendance Excel:</label>

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
                                                            <th style="width: 80px;">Roll No</th>
                                                            <th>Student Name</th>
                                                            <th style="width: 100px;">Status</th>
                                                            <th>Remarks</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="excelPreviewBody"></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4 mt-3 d-flex justify-content-end" style="height: 50px;">
                                    <button type="button" id="submitAttendanceBtn" class="btn btn-primary">
                                        Submit Attendance
                                    </button>
                                </div>
                            </div>

                            {{-- Students Table --}}
                            <div id="studentsTable" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h6 id="studentsCount" class="mb-0 fw-semibold"></h6>
                                    <div class="hstack gap-2">
                                        <span id="selectedSubject" class="badge bg-light text-dark me-2"></span>
                                        <span id="selectedClassSection" class="badge bg-secondary text-white"></span>
                                    </div>
                                </div>

                                <form id="attendanceForm" style="display: none;">
                                    @csrf
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="70">Roll No</th>
                                                    <th>Student Name</th>
                                                    <th width="220" class="text-center">Status</th>
                                                    <th width="150">Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody id="studentsList"></tbody>
                                        </table>
                                    </div>
                                </form>
                            </div>

                            {{-- No Students Message --}}
                            <div id="noStudents" class="text-center py-8" style="display: none;">
                                <i class="feather-users fs-1 text-muted mb-3 d-block"></i>
                                <h5 class="text-muted mb-2">No Students Found</h5>
                                <p class="text-muted mb-4">Please select a valid date and slot.</p>
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
            let parsedExcelData = [];

            /* ══════════════════════════════════════
               RESET HELPERS
            ══════════════════════════════════════ */
            function resetStudents() {
                studentsData = [];
                $('#studentsList').empty();
                $('#studentsTable, #attendanceForm, #noStudents').hide();
                $('#studentsCount, #selectedSubject, #selectedClassSection').text('');
                $('#submitAttendanceBtn').prop('disabled', false).removeClass('disabled').text('Submit Attendance');
                resetExcelUpload();
            }

            function resetSlots() {
                $('#slot_id').empty().append('<option value="">Select Period</option>').prop('disabled', true);
                resetStudents();
            }

            function resetSections() {
                $('#section_id').empty().append('<option value="">Select Section</option>').prop('disabled', true);
                resetSlots();
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
               1. LOAD STREAMS — depends on course
            ══════════════════════════════════════ */
            function loadStreams() {
                const courseId = $('#course_id').val();
                resetStreams();
                if (!courseId) return;

                $.ajax({
                    url: "{{ route('admin.student_attendance.get_streams') }}",
                    method: 'POST',
                    data: { course_id: courseId, _token: "{{ csrf_token() }}" },
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
               2. LOAD CLASSES — depends on course + academic_year + stream
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
                        _token: "{{ csrf_token() }}"
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
               3. LOAD SECTIONS — depends on course + stream + class
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
                        _token: "{{ csrf_token() }}"
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
               4. LOAD SLOTS — depends on all filters + date
            ══════════════════════════════════════ */
            function loadSlots() {
                const academicYearId = $('#academic_year_id').val();
                const courseId       = $('#course_id').val();
                const streamId       = $('#stream_id').val();
                const classId        = $('#class_id').val();
                const sectionId      = $('#section_id').val();
                const date           = $('#date').val();
                resetSlots();
                if (!academicYearId || !courseId || !streamId || !classId || !sectionId || !date) return;

                $.ajax({
                    url: "{{ route('admin.student_attendance.get_slots') }}",
                    method: 'POST',
                    data: {
                        academic_year_id: academicYearId,
                        course_id: courseId,
                        stream_id: streamId,
                        class_id: classId,
                        section_id: sectionId,
                        date,
                        _token: "{{ csrf_token() }}"
                    },
                    success(res) {
                        const $s = $('#slot_id');
                        if (res.slots && res.slots.length) {
                            res.slots.forEach(slot => {
                                const text = `${slot.timetable.class.name} - ${slot.timetable.section.name} | ${slot.subject.name} | ${slot.period.name}`;
                                $s.append(`<option value="${slot.id}">${text}</option>`);
                            });
                            $s.prop('disabled', false);
                        } else {
                            toastr.info(res.message || 'No slots found for this date.');
                        }
                        resetExcelUpload();
                    },
                    error(xhr) { toastr.error(xhr.responseJSON?.message || 'Failed to load slots'); }
                });
            }

            /* ══════════════════════════════════════
               5. LOAD STUDENTS — depends on slot + date
            ══════════════════════════════════════ */
            function loadStudents() {
                const date           = $('#date').val();
                const slotId         = $('#slot_id').val();
                const academicYearId = $('#academic_year_id').val();
                const courseId       = $('#course_id').val();
                const streamId       = $('#stream_id').val();
                const classId        = $('#class_id').val();
                const sectionId      = $('#section_id').val();

                if (!date || !slotId) {
                    toastr.warning('Please select date and slot.');
                    return;
                }
                resetExcelUpload();

                $.ajax({
                    url: "{{ route('admin.student_attendance.get_students', ['slot' => '__slot_id__']) }}"
                            .replace('__slot_id__', slotId),
                    method: 'POST',
                    data: {
                        date,
                        academic_year_id: academicYearId,
                        course_id: courseId,
                        stream_id: streamId,
                        class_id: classId,
                        section_id: sectionId,
                        _token: "{{ csrf_token() }}"
                    },
                    success(res) {
                        studentsData = Array.isArray(res.students) ? res.students : [];
                        displayStudents(res);
                    },
                    error(xhr) { toastr.error(xhr.responseJSON?.message || 'Failed to load students'); }
                });
            }

            /* ══════════════════════════════════════
               6. DISPLAY STUDENTS
            ══════════════════════════════════════ */
            function displayStudents(response) {
                const $list = $('#studentsList').empty();

                if (!studentsData.length) {
                    $('#studentsTable').hide();
                    $('#noStudents').show();
                    return;
                }

                $('#studentsTable').show();
                $('#noStudents').hide();
                $('#studentsCount').text(`${studentsData.length} students loaded`);

                const slotOption = $('#slot_id option:selected').text();
                $('#selectedSubject').text(slotOption.split('|')[1]?.trim() || 'General');
                $('#selectedClassSection').text(slotOption.split('|')[0]?.trim() || '');

                // existing keys are enrollment_id
                const allSubmitted = studentsData.length > 0 &&
                    studentsData.every(s => response.existing && response.existing[s.enrollment_id]);

                studentsData.forEach(student => {
                    const eid            = student.enrollment_id;
                    const existingRecord = response.existing?.[eid] || null;
                    const existing       = existingRecord?.status || 'absent';
                    const remarks        = existingRecord?.remarks || '';
                    const disabled       = allSubmitted ? 'disabled' : '';

                    $list.append(`
                        <tr data-enrollment-id="${eid}">
                            <td>${student.roll_number ?? '—'}</td>
                            <td>${student.first_name} ${student.last_name}</td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <input type="radio" class="btn-check" name="status_${eid}" id="present_${eid}" value="present" ${existing === 'present' ? 'checked' : ''} autocomplete="off" ${disabled}>
                                    <label class="btn btn-sm btn-outline-success" for="present_${eid}">Present</label>

                                    <input type="radio" class="btn-check" name="status_${eid}" id="absent_${eid}" value="absent" ${existing === 'absent' ? 'checked' : ''} autocomplete="off" ${disabled}>
                                    <label class="btn btn-sm btn-outline-danger" for="absent_${eid}">Absent</label>

                                    <input type="radio" class="btn-check" name="status_${eid}" id="late_${eid}" value="late" ${existing === 'late' ? 'checked' : ''} autocomplete="off" ${disabled}>
                                    <label class="btn btn-sm btn-outline-warning" for="late_${eid}">Late</label>
                                </div>
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm"
                                    name="remarks[${eid}]" value="${remarks}"
                                    placeholder="Remarks" ${disabled}>
                            </td>
                        </tr>
                    `);
                });

                $('#attendanceForm').show();

                if (allSubmitted) {
                    $('#submitAttendanceBtn').prop('disabled', true).addClass('disabled').text('Attendance Already Submitted');
                } else {
                    $('#submitAttendanceBtn').prop('disabled', false).removeClass('disabled').text('Submit Attendance');
                }
            }

            /* ══════════════════════════════════════
               EVENT BINDINGS
            ══════════════════════════════════════ */

            // Course change → load streams first
            $('#course_id').on('change', function () {
                loadStreams();
            });

            // Stream change → load classes (needs course + academic_year + stream)
            $('#stream_id').on('change', function () {
                loadClasses();
            });

            // Academic year change → reload classes if stream already selected
            $('#academic_year_id').on('change', function () {
                loadClasses();
            });

            // Class change → load sections
            $('#class_id').on('change', function () {
                loadSections();
            });

            // Section or date change → load slots
            $('#section_id, #date').on('change', loadSlots);

            // Slot change → load students automatically
            $('#slot_id').on('change', function () {
                loadStudents();
            });

            $('#loadStudentsAgain').on('click', loadStudents);

            /* ══════════════════════════════════════
               MANUAL FORM SUBMIT
            ══════════════════════════════════════ */
            $('#submitAttendanceBtn').on('click', function () {
                const date   = $('#date').val();
                const slotId = $('#slot_id').val();

                if (!date || !slotId) {
                    toastr.warning('Please select date and slot.');
                    return;
                }

                if (!studentsData.length) {
                    toastr.warning('No students loaded.');
                    return;
                }

                const attendance = {};
                studentsData.forEach(student => {
                    const eid = student.enrollment_id;
                    attendance[eid] = {
                        status:  $(`input[name="status_${eid}"]:checked`).val() || 'absent',
                        remarks: $(`input[name="remarks[${eid}]"]`).val() || '',
                    };
                });

                submitAttendance(date, slotId, attendance, function () {
                    toastr.success('Attendance submitted successfully!');
                    $('#studentsTable, #attendanceForm').hide();
                });
            });

            /* ══════════════════════════════════════
               SHARED SUBMIT FUNCTION
            ══════════════════════════════════════ */
            function submitAttendance(date, slotId, attendance, onSuccess) {
                $.ajax({
                    url: "{{ route('admin.student_attendance.store') }}",
                    method: 'POST',
                    data: { date, slot_id: slotId, attendance, _token: "{{ csrf_token() }}" },
                    beforeSend() { $('#submitAttendanceBtn, #submitExcelBtn').prop('disabled', true); },
                    success(res) { onSuccess(res); },
                    error(xhr)   { toastr.error(xhr.responseJSON?.message || 'Failed to save attendance'); },
                    complete()   { $('#submitAttendanceBtn, #submitExcelBtn').prop('disabled', false); }
                });
            }

            /* ══════════════════════════════════════
               TEMPLATE DOWNLOAD
            ══════════════════════════════════════ */
            $('#downloadTemplate').on('click', function (e) {
                e.preventDefault();

                if (!studentsData.length) {
                    const rows = [
                        ['Roll No', 'Student Name', 'Status', 'Remarks'],
                        ['001', 'Example Student', 'present', ''],
                        ['002', 'Example Student 2', 'absent', 'Sick leave'],
                    ];
                    exportExcel(rows, 'attendance_template.xlsx');
                    toastr.info('Generic template downloaded. Load students first for a pre-filled template.');
                    return;
                }

                const rows = [['Roll No', 'Student Name', 'Status', 'Remarks']];
                studentsData.forEach(s => rows.push([s.roll_number, `${s.first_name} ${s.last_name}`, 'present', '']));
                exportExcel(rows, `attendance_${$('#date').val() || 'date'}.xlsx`);
                toastr.success(`Template with ${studentsData.length} students downloaded!`);
            });

            function exportExcel(rows, filename) {
                const wb = XLSX.utils.book_new();
                const ws = XLSX.utils.aoa_to_sheet(rows);
                ws['!cols'] = [{ wch: 12 }, { wch: 28 }, { wch: 12 }, { wch: 25 }];
                XLSX.utils.book_append_sheet(wb, ws, 'Attendance');
                XLSX.writeFile(wb, filename);
            }

            /* ══════════════════════════════════════
               DRAG & DROP ZONE
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
                    toastr.warning('Please load students first by selecting a slot, then upload Excel.');
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

                        if (rows.length < 2) {
                            showUploadStatus('danger', 'Excel file is empty or has no data rows.');
                            return;
                        }

                        const header    = rows[0].map(h => String(h).toLowerCase().trim());
                        const colRoll   = header.findIndex(h => h.includes('roll'));
                        const colStatus = header.findIndex(h => h.includes('status'));
                        const colRemarks = header.findIndex(h => h.includes('remark'));

                        if (colRoll   === -1) { showUploadStatus('danger', 'Column "Roll No" not found.'); return; }
                        if (colStatus === -1) { showUploadStatus('danger', 'Column "Status" not found.'); return; }

                        const rollMap = {};
                        studentsData.forEach(s => { rollMap[String(s.roll_number).trim().toLowerCase()] = s; });

                        const validStatuses = ['present', 'absent', 'late'];
                        const matched = [], unrecognized = [], invalidStatus = [];

                        rows.slice(1).forEach(row => {
                            const roll    = String(row[colRoll] ?? '').trim();
                            if (!roll) return;
                            const status  = String(row[colStatus] ?? '').toLowerCase().trim();
                            const remarks = colRemarks !== -1 ? String(row[colRemarks] ?? '').trim() : '';
                            const student = rollMap[roll.toLowerCase()];

                            if (!student)                         { unrecognized.push(roll); return; }
                            if (!validStatuses.includes(status))  { invalidStatus.push(`Roll ${roll} → "${status}"`); return; }

                            matched.push({
                                enrollmentId: student.enrollment_id,
                                roll:         student.roll_number,
                                name:         `${student.first_name} ${student.last_name}`,
                                status, remarks,
                            });
                        });

                        parsedExcelData = matched;
                        renderExcelPreview(matched, unrecognized, invalidStatus);
                    } catch (err) {
                        showUploadStatus('danger', 'Could not parse the Excel file. Please use the downloaded template.');
                    }
                };
                reader.readAsBinaryString(file);
            }

            function renderExcelPreview(matched, unrecognized, invalidStatus) {
                const $tbody = $('#excelPreviewBody').empty();

                matched.forEach(row => {
                    const cls = row.status === 'present' ? 'bg-success-subtle text-success' :
                                row.status === 'absent'  ? 'bg-danger-subtle text-danger' :
                                                           'bg-warning-subtle text-warning';
                    $tbody.append(`
                        <tr>
                            <td class="text-muted">${row.roll}</td>
                            <td>${row.name}</td>
                            <td><span class="badge ${cls} text-capitalize">${row.status}</span></td>
                            <td class="text-muted">${row.remarks || '—'}</td>
                        </tr>
                    `);
                });

                let summary = `${matched.length} of ${studentsData.length} students matched`;
                if (unrecognized.length) summary += ` · <span class="text-danger">${unrecognized.length} unrecognized roll(s)</span>`;
                if (invalidStatus.length) summary += ` · <span class="text-warning">${invalidStatus.length} invalid status</span>`;

                $('#previewTitle').text(`Excel preview — ${$('#date').val()}`);
                $('#previewStats').html(summary);
                $('#excelPreviewCard').show();

                if (unrecognized.length || invalidStatus.length) {
                    let msg = '';
                    if (unrecognized.length) msg += `Unrecognized roll numbers: ${unrecognized.join(', ')}. `;
                    if (invalidStatus.length) msg += `Invalid status: ${invalidStatus.join('; ')}.`;
                    showUploadStatus('warning', msg);
                } else {
                    showUploadStatus('success', `All ${matched.length} rows parsed. Review and click "Submit via Excel".`);
                }
            }

            /* ══════════════════════════════════════
               EXCEL SUBMIT
            ══════════════════════════════════════ */
            $('#submitExcelBtn').on('click', function () {
                if (!parsedExcelData.length) { toastr.warning('No valid data to submit.'); return; }

                const date   = $('#date').val();
                const slotId = $('#slot_id').val();
                if (!date || !slotId) { toastr.warning('Please select date and slot.'); return; }

                const attendance = {};
                parsedExcelData.forEach(row => {
                    attendance[row.enrollmentId] = { status: row.status, remarks: row.remarks || '' };
                });

                const inExcel = new Set(parsedExcelData.map(r => r.enrollmentId));
                studentsData.forEach(s => {
                    if (!inExcel.has(s.enrollment_id)) {
                        attendance[s.enrollment_id] = { status: 'absent', remarks: 'Not in Excel upload' };
                    }
                });

                if (!confirm(`Submit attendance for ${Object.keys(attendance).length} students via Excel?`)) return;

                submitAttendance(date, slotId, attendance, function () {
                    toastr.success(`Attendance submitted via Excel!`);
                    resetExcelUpload();
                    $('#studentsTable, #attendanceForm').hide();
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
                const icons = { success: 'check-circle', warning: 'alert-triangle', danger: 'alert-circle', info: 'info' };
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