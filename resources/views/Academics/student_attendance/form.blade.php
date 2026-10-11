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
                            <a href="{{ route('admin.student_attendance.index') }}" class="btn btn-secondary">
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
                                    <span class="d-block mb-2">Attendance Filters :</span>
                                    <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                        Select academic year, stream, class, section, and date
                                    </span>
                                </h5>
                                <button type="button" id="submitAttendanceBtn" class="btn btn-primary">
                                    Submit Attendance
                                </button>
                            </div>

                            {{-- Filters Row --}}
                            <div class="row mb-5">
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

                                <div class="col-lg-2">
                                    <label for="stream_id" class="fw-semibold mb-2 d-block">Stream:</label>
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-book"></i></div>
                                        <select class="form-select" id="stream_id">
                                            <option value="">All Streams</option>
                                            @foreach ($streams as $stream)
                                                <option value="{{ $stream->id }}">{{ $stream->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-2">
                                    <label for="class_id" class="fw-semibold mb-2 d-block">Class:</label>
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-hash"></i></div>
                                        <select class="form-select" id="class_id">
                                            <option value="">Select Class</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-2">
                                    <label for="section_id" class="fw-semibold mb-2 d-block">Section:</label>
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-hash"></i></div>
                                        <select class="form-select" id="section_id">
                                            <option value="">Select Section</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-2">
                                    <label for="date" class="fw-semibold mb-2 d-block">Date:</label>
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-calendar"></i></div>
                                        <input type="date" class="form-control" id="date"
                                               value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>

                                <div class="col-lg-2 d-flex align-items-end">
                                    <button type="button" id="loadStudents" class="btn btn-outline-primary w-100">
                                        <i class="feather-users me-2"></i>Load Students
                                    </button>
                                </div>
                            </div>

                            {{-- Period Selection (from timetable) --}}
                            <div class="row mb-5" id="periodRow" style="display: none;">
                                <div class="col-lg-2">
                                    <label for="period_id" class="fw-semibold mb-2 d-block">Period:</label>
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-clock"></i></div>
                                        <select class="form-select" id="period_id">
                                            <option value="">Select Period</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Students Table --}}
                            <div id="studentsTable" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h6 id="studentsCount" class="mb-0 fw-semibold"></h6>
                                    <div class="hstack gap-2">
                                        <span id="selectedSubject" class="badge bg-light text-dark me-2"></span>
                                        <select class="form-select form-select-sm" id="subject_id" style="width: 180px;">
                                            <option value="">All Subjects</option>
                                        </select>
                                    </div>
                                </div>

                                <form id="attendanceForm" style="display: none;">
                                    @csrf
                                    <input type="hidden" id="form_academic_year_id" name="academic_year_id">
                                    <input type="hidden" id="form_stream_id" name="stream_id">
                                    <input type="hidden" id="form_class_id" name="class_id">
                                    <input type="hidden" id="form_section_id" name="section_id">
                                    <input type="hidden" id="form_date" name="date">
                                    <input type="hidden" id="form_period_id" name="period_id">

                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="70">Roll No</th>
                                                    <th>Student Name</th>
                                                    <th width="180">Subject</th>
                                                    <th width="220" class="text-center">Status</th>
                                                    <th width="150">Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody id="studentsList">
                                                {{-- Students loaded via AJAX --}}
                                            </tbody>
                                        </table>
                                    </div>
                                </form>

                                <div class="text-center mt-4">
                                    <button type="button" id="submitAttendanceBtnBottom" class="btn btn-success btn-lg px-5">
                                        <i class="feather-check-circle me-2"></i>Submit Attendance
                                    </button>
                                </div>
                            </div>

                            {{-- No Students Message --}}
                            <div id="noStudents" class="text-center py-8" style="display: none;">
                                <i class="feather-users fs-1 text-muted mb-3 d-block"></i>
                                <h5 class="text-muted mb-2">No Students Found</h5>
                                <p class="text-muted mb-4">Please select a valid Academic Year, Class, and Section.</p>
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

    <script>
        $(document).ready(function () {
            let studentsData = [];
            let subjectsData = [];
            let timetableSlots = [];

            // Load classes when academic_year_id or stream_id changes
            function loadClasses() {
                console.log('Loading classes...');
                const academicYearId = $('#academic_year_id').val();
                const streamId = $('#stream_id').val();

                if (!academicYearId) {
                    $('#class_id').empty().append('<option value="">Select Class</option>');
                    $('#section_id').empty().append('<option value="">Select Section</option>');
                    return;
                }

                $.ajax({
                    url: "{{ route('admin.student_attendance.get_classes') }}",
                    method: 'POST',
                    data: {
                        academic_year_id: academicYearId,
                        stream_id: streamId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        const classSelect = $('#class_id');
                        classSelect.empty().append('<option value="">Select Class</option>');
                        response.classes.forEach(cls => {
                            classSelect.append(`<option value="${cls.id}">${cls.name}</option>`);
                        });
                    }
                });
            }

            // Load sections when class_id changes
            function loadSections() {
                console.log('Loading sections...');
                const classId = $('#class_id').val();

                if (!classId) {
                    $('#section_id').empty().append('<option value="">Select Section</option>');
                    return;
                }

                $.ajax({
                    url: "{{ route('admin.student_attendance.get_sections') }}",
                    method: 'POST',
                    data: {
                        class_id: classId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        const sectionSelect = $('#section_id');
                        sectionSelect.empty().append('<option value="">Select Section</option>');
                        response.sections.forEach(sec => {
                            sectionSelect.append(`<option value="${sec.id}">${sec.name}</option>`);
                        });
                    }
                });
            }

            // Load timetable for given date/class/section
            function loadTimetable() {
                console.log('Loading timetable...');
                const academicYearId = $('#academic_year_id').val();
                const classId = $('#class_id').val();
                const sectionId = $('#section_id').val();
                const date = $('#date').val();

                if (!academicYearId || !classId || !sectionId || !date) {
                    timetableSlots = [];
                    $('#period_id').empty().append('<option value="">Select Period</option>');
                    $('#periodRow').hide();
                    return;
                }

                $.ajax({
                    url: "{{ route('admin.student_attendance.get_timetable') }}",
                    method: 'POST',
                    data: {
                        academic_year_id: academicYearId,
                        class_id: classId,
                        section_id: sectionId,
                        date: date,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        timetableSlots = response.slots || [];

                        const periodSelect = $('#period_id');
                        periodSelect.empty().append('<option value="">Select Period</option>');
                        timetableSlots.forEach(slot => {
                            periodSelect.append(
                                `<option value="${slot.period.id}">
                                    ${slot.period.name} (${slot.period.start_time} - ${slot.period.end_time})
                                </option>`
                            );
                        });

                        if (timetableSlots.length > 0) {
                            periodSelect.val(timetableSlots[0].period.id);
                        }

                        $('#periodRow').show();
                        filterSubjectsForPeriod(periodSelect.val());
                    },
                    error: function (xhr) {
                        let msg = 'Failed to load timetable';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        toastr.error(msg);
                    }
                });
            }

            // Filter subjects for selected period (from timetable)
            function filterSubjectsForPeriod(periodId) {
                console.log('Filtering subjects for period:', periodId);
                const periodSlot = timetableSlots.find(s => s.period.id == periodId);
                const allowedSubjectId = periodSlot ? periodSlot.subject.id : null;

                const subjectSelect = $('#subject_id');
                subjectSelect.empty().append('<option value="">All Subjects</option>');

                subjectsData.forEach(subject => {
                    if (allowedSubjectId && subject.id != allowedSubjectId) {
                        return;
                    }
                    subjectSelect.append(`<option value="${subject.id}">${subject.subject.name}</option>`);
                });

                $('.subject-select').each(function () {
                    const studentSelect = $(this);
                    studentSelect.empty().append('<option value="">General</option>');
                    subjectsData.forEach(subject => {
                        if (allowedSubjectId && subject.id != allowedSubjectId) {
                            return;
                        }
                        studentSelect.append(`<option value="${subject.id}">${subject.subject.name}</option>`);
                    });

                    if (allowedSubjectId) {
                        studentSelect.val(allowedSubjectId);
                    }
                });
            }

            // Load Students via AJAX
            function loadStudents() {
                console.log('Loading students...');
                const academicYearId = $('#academic_year_id').val();
                const streamId = $('#stream_id').val();
                const classId = $('#class_id').val();
                const sectionId = $('#section_id').val();
                const date = $('#date').val();
                const periodId = $('#period_id').val();
console.log('Filters - Year:', academicYearId, 'Stream:', streamId, 'Class:', classId, 'Section:', sectionId, 'Date:', date, 'Period:', periodId);
                if (!academicYearId || !classId || !sectionId || !date) {
                    toastr.warning('Please select Academic Year, Class, Section, and Date');
                    return;
                }

                $.ajax({
                    url: "{{ route('admin.student_attendance.get_students') }}",
                    method: 'POST',
                    data: {
                        academic_year_id: academicYearId,
                        stream_id: streamId,
                        class_id: classId,
                        section_id: sectionId,
                        date: date,
                        period_id: periodId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        studentsData = Array.isArray(response.students) ? response.students : [];
                        subjectsData = response.subjects || [];

                        displayStudents(response);
                    },
                    error: function (xhr) {
                        let msg = 'Failed to load students';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        toastr.error(msg);
                    }
                });
            }

            function displayStudents(response) {
                console.log('Displaying students...');
                const studentsList = $('#studentsList');
                studentsList.empty();

                if (studentsData.length === 0) {
                    $('#studentsTable').hide();
                    $('#noStudents').show();
                    return;
                }

                $('#studentsTable').show();
                $('#noStudents').hide();

                $('#studentsCount').text(`${studentsData.length} students loaded`);
                $('#selectedSubject').text(subjectsData.length ? subjectsData[0].subject.name : 'General');

                const subjectSelect = $('#subject_id');
                subjectSelect.empty().append('<option value="">All Subjects</option>');
                subjectsData.forEach(subject => {
                    subjectSelect.append(`<option value="${subject.id}">${subject.subject.name}</option>`);
                });

                studentsData.forEach((student, index) => {
                    const existing = response.existing[student.id] || 'absent';
                    const remarks = response.existing[student.id] ? response.existing[student.id].remarks || '' : '';

                    const row = `
                        <tr data-student-id="${student.id}">
                            <td>${student.roll_number}</td>
                            <td>${student.first_name} ${student.last_name}</td>
                            <td>
                                <select class="form-select form-select-sm subject-select" name="subject[${student.id}]">
                                    <option value="">General</option>
                                    ${subjectsData.map(s => `<option value="${s.id}">${s.subject.name}</option>`).join('')}
                                </select>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <input type="radio" class="btn-check" name="status_${student.id}" id="present_${student.id}" value="present" ${existing === 'present' ? 'checked' : ''} autocomplete="off">
                                    <label class="btn btn-sm btn-success" for="present_${student.id}">Present</label>

                                    <input type="radio" class="btn-check" name="status_${student.id}" id="absent_${student.id}" value="absent" ${existing === 'absent' ? 'checked' : ''} autocomplete="off">
                                    <label class="btn btn-sm btn-danger" for="absent_${student.id}">Absent</label>

                                    <input type="radio" class="btn-check" name="status_${student.id}" id="late_${student.id}" value="late" ${existing === 'late' ? 'checked' : ''} autocomplete="off">
                                    <label class="btn btn-sm btn-warning" for="late_${student.id}">Late</label>
                                </div>
                            </td>
                            <td>
                                <input type="text"
                                       class="form-control form-control-sm"
                                       name="remarks[${student.id}]"
                                       value="${remarks}"
                                       placeholder="Remarks">
                            </td>
                        </tr>
                    `;
                    studentsList.append(row);
                });

                $('#attendanceForm').show();
            }

            // Bind events
            $('#academic_year_id, #stream_id').on('change', function () {
                loadClasses();
                loadTimetable();
            });

            $('#class_id').on('change', function () {
                loadSections();
                loadTimetable();
            });

            $('#section_id, #date').on('change', loadTimetable);
            $('#loadStudents').on('click', loadStudents);
            $('#loadStudentsAgain').on('click', loadStudents);

            $('#period_id').on('change', function () {
                filterSubjectsForPeriod($(this).val());
            });

            // Submit Attendance
            $('#submitAttendanceBtn, #submitAttendanceBtnBottom').on('click', function () {
                const academicYearId = $('#academic_year_id').val();
                const streamId = $('#stream_id').val();
                const classId = $('#class_id').val();
                const sectionId = $('#section_id').val();
                const date = $('#date').val();
                const periodId = $('#period_id').val();

                if (!academicYearId || !classId || !sectionId || !date) {
                    toastr.warning('Please select Academic Year, Class, Section, and Date');
                    return;
                }

                const attendance = {};
                studentsData.forEach(student => {
                    const status = $(`input[name="status_${student.id}"]:checked`).val() || 'absent';
                    const remarks = $(`input[name="remarks[${student.id}]"]`).val();
                    const subjectId = $(`select[name="subject[${student.id}]"]`).val();

                    attendance[student.id] = {
                        status,
                        remarks,
                        subject_id: subjectId
                    };
                });

                $.ajax({
                    url: "{{ route('admin.student_attendance.store') }}",
                    method: 'POST',
                    data: {
                        academic_year_id: academicYearId,
                        stream_id: streamId,
                        class_id: classId,
                        section_id: sectionId,
                        date: date,
                        period_id: periodId,
                        attendance: attendance,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        toastr.success('Attendance marked successfully!');
                        $('#studentsTable').hide();
                        $('#attendanceForm').hide();
                    },
                    error: function (xhr) {
                        let msg = 'Failed to save attendance';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        toastr.error(msg);
                    }
                });
            });
        });
    </script>
@endsection

@push('scripts')
@endpush
