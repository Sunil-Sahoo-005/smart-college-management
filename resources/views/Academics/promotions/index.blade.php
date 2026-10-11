@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="mb-0">
                        <i class="feather-arrow-up-circle me-2 text-success"></i>
                        Batch Promotion / Transfer
                    </h5>
                </div>

                <div class="card border-0 shadow-sm m-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">Current Student Filters</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-lg-2">
                                <label class="form-label fw-semibold">Academic Year</label>
                                <select class="form-select" id="from_academic_year_id">
                                    <option value="">Select Year</option>
                                    @foreach ($academicYears as $year)
                                        <option value="{{ $year->id }}">{{ $year->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-2">
                                <label class="form-label fw-semibold">Course</label>
                                <select class="form-select" id="from_course_id">
                                    <option value="">Select Course</option>
                                    @foreach ($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-2">
                                <label class="form-label fw-semibold">Stream</label>
                                <select class="form-select" id="from_stream_id" disabled>
                                    <option value="">Select Stream</option>
                                </select>
                            </div>

                            <div class="col-lg-2">
                                <label class="form-label fw-semibold">Semester/Class</label>
                                <select class="form-select" id="from_class_id" disabled>
                                    <option value="">Select Semester/Class</option>
                                </select>
                            </div>

                            <div class="col-lg-2">
                                <label class="form-label fw-semibold">Section</label>
                                <select class="form-select" id="from_section_id" disabled>
                                    <option value="">All Sections</option>
                                </select>
                            </div>

                            <div class="col-lg-2 d-flex align-items-end">
                                <button class="btn btn-primary w-100" id="loadStudentsBtn" disabled>
                                    <i class="feather-users me-2"></i>Load Students
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm m-4" id="targetCard" style="display:none;">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">Target Placement</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-lg-4">
                                <label class="form-label fw-semibold">Target Semester/Class</label>
                                <select class="form-select" id="to_class_id" disabled>
                                    <option value="">Select Class</option>
                                </select>
                            </div>

                            <div class="col-lg-4">
                                <label class="form-label fw-semibold">Target Section</label>
                                <select class="form-select" id="to_section_id" disabled>
                                    <option value="">Select Section</option>
                                </select>
                            </div>

                            <div class="col-lg-4 d-flex align-items-end">
                                <button class="btn btn-success w-100" id="promoteBtn" disabled>
                                    Promote Selected (<span id="selectedCount">0</span>)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-lg m-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Students <span class="badge bg-primary" id="totalStudents">0</span></h6>
                        <div>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="selectAllBtn">Select
                                All</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="clearAllBtn">Clear
                                All</button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="50"><input type="checkbox" id="selectAllTop"></th>
                                    <th>Student</th>
                                    <th>Roll / Admission</th>
                                    <th>Current Placement</th>
                                    <th>Status</th>
                                    <th>GPA</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody id="studentsTbody">
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        Load students using filters above
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
   <script>
    let currentStudents = [];
    let selectedStudents = [];

    $(function () {
        const token = $('meta[name="csrf-token"]').attr('content');
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': token }
        });

        $('#from_course_id').change(function () {
            resetCurrentBelow('course');
            const courseId = $(this).val();
            if (courseId) loadStreams(courseId, '#from_stream_id', "{{ route('admin.promotions.streams') }}");
        });

        $('#from_stream_id').change(function () {
            resetCurrentBelow('stream');
            const courseId = $('#from_course_id').val();
            const streamId = $(this).val();
            if (courseId && streamId) loadClasses(courseId, streamId, '#from_class_id', "{{ route('admin.promotions.classes') }}");
        });

        $('#from_class_id').change(function () {
            resetCurrentBelow('class');
            const courseId = $('#from_course_id').val();
            const streamId = $('#from_stream_id').val();
            const classId = $(this).val();

            if (courseId && streamId && classId) {
                loadSections(courseId, streamId, classId, '#from_section_id', "{{ route('admin.promotions.sections') }}");
                loadTargetClasses(courseId, streamId);
                $('#loadStudentsBtn').prop('disabled', false);
            }
        });

        $('#from_section_id').change(function () {
            $('#loadStudentsBtn').prop('disabled', false);
        });

        $('#loadStudentsBtn').click(loadStudents);

        $('#selectAllTop, #selectAllBtn').click(function () {
            $('.student-checkbox').prop('checked', true).trigger('change');
            $('#selectAllTop').prop('checked', true);
        });

        $('#clearAllBtn').click(function () {
            $('.student-checkbox').prop('checked', false).trigger('change');
            $('#selectAllTop').prop('checked', false);
        });

        $(document).on('change', '#selectAllTop', function () {
            $('.student-checkbox').prop('checked', $(this).is(':checked')).trigger('change');
        });

        $(document).on('change', '.student-checkbox', function () {
            updateSelection();
        });

        $(document).on('change', '#to_class_id', function () {
            resetTargetBelow('class');
            const courseId = $('#from_course_id').val();
            const streamId = $('#from_stream_id').val();
            const classId = $(this).val();

            if (courseId && streamId && classId) {
                loadSections(courseId, streamId, classId, '#to_section_id', "{{ route('admin.promotions.target-sections') }}");
            }
            updatePromoteButton();
        });

        $(document).on('change', '#to_section_id', function () {
            updatePromoteButton();
        });

        $('#promoteBtn').click(executePromotion);
    });

    function resetCurrentBelow(level) {
        if (level === 'course') {
            $('#from_stream_id').html('<option value="">Select Stream</option>').prop('disabled', true);
            $('#from_class_id').html('<option value="">Select Semester/Class</option>').prop('disabled', true);
            $('#from_section_id').html('<option value="">All Sections</option>').prop('disabled', true);
        }

        if (level === 'stream') {
            $('#from_class_id').html('<option value="">Select Semester/Class</option>').prop('disabled', true);
            $('#from_section_id').html('<option value="">All Sections</option>').prop('disabled', true);
        }

        if (level === 'class') {
            $('#from_section_id').html('<option value="">All Sections</option>').prop('disabled', true);
        }

        $('#loadStudentsBtn').prop('disabled', true);
        clearStudents();
    }

    function resetTargetBelow(level) {
        if (level === 'class') {
            $('#to_section_id').html('<option value="">Select Section</option>').prop('disabled', true);
        }
        updatePromoteButton();
    }

    function loadStreams(courseId, target, url) {
        $.post(url, { course_id: courseId }, function (res) {
            let html = '<option value="">Select Stream</option>';
            res.streams.forEach(item => {
                html += `<option value="${item.id}">${item.name}</option>`;
            });
            $(target).html(html).prop('disabled', false);
        }).fail(showError);
    }

    function loadClasses(courseId, streamId, target, url) {
        $.post(url, { course_id: courseId, stream_id: streamId }, function (res) {
            let html = '<option value="">Select Class</option>';
            res.classes.forEach(item => {
                html += `<option value="${item.id}">${item.name}</option>`;
            });
            $(target).html(html).prop('disabled', false);
        }).fail(showError);
    }

    function loadTargetClasses(courseId, streamId) { 
        $.post("{{ route('admin.promotions.target-classes') }}", {
            course_id: courseId,
            stream_id: streamId
        }, function (res) {
            let html = '<option value="">Select Class</option>';
            res.classes.forEach(item => {
                html += `<option value="${item.id}">${item.name}</option>`;
            });
            $('#to_class_id').html(html).prop('disabled', false);
            $('#targetCard').show();
            updatePromoteButton();
        }).fail(showError);
    }

    function loadSections(courseId, streamId, classId, target, url) {
        $.post(url, { course_id: courseId, stream_id: streamId, class_id: classId }, function (res) {
            let first = target === '#from_section_id'
                ? '<option value="">All Sections</option>'
                : '<option value="">Select Section</option>';

            let html = first;
            res.sections.forEach(item => {
                html += `<option value="${item.id}">${item.name}</option>`;
            });
            $(target).html(html).prop('disabled', false);
        }).fail(showError);
    }

    function loadStudents() {
        const payload = {
            academic_year_id: $('#from_academic_year_id').val(),
            course_id: $('#from_course_id').val(),
            stream_id: $('#from_stream_id').val(),
            class_id: $('#from_class_id').val(),
            section_id: $('#from_section_id').val() || null
        };

        if (!payload.academic_year_id || !payload.course_id || !payload.stream_id || !payload.class_id) {
            alert('Please select academic year, course, stream and class.');
            return;
        }

        $('#studentsTbody').html(`
            <tr>
                <td colspan="7" class="text-center py-4">
                    <div class="spinner-border text-primary"></div>
                    <div class="mt-2 text-muted">Loading students...</div>
                </td>
            </tr>
        `);

        $.post("{{ route('admin.promotions.students') }}", payload, function (res) {
            currentStudents = res.students;
            selectedStudents = [];
            renderStudents(res.students);
            $('#totalStudents').text(res.total);
            $('#targetCard').show();
            $('#to_class_id').val('');
            $('#to_section_id').html('<option value="">Select Section</option>').prop('disabled', true);
            updatePromoteButton();
        }).fail(showError);
    }

    function renderStudents(students) {
        if (!students.length) {
            clearStudents('No students found.');
            return;
        }

        let html = '';
        students.forEach(row => {
            const s = row.student;
            const roll = s.roll_number || s.admission_number || 'N/A';

            html += `
                <tr>
                    <td><input type="checkbox" class="student-checkbox" value="${s.id}"></td>
                    <td>${s.first_name} ${s.last_name ?? ''}</td>
                    <td>${roll}</td>
                    <td>
                        ${row.course?.name ?? ''}<br>
                        <small class="text-muted">${row.stream?.name ?? ''} / ${row.class?.name ?? ''} ${row.section ? '/ ' + row.section.name : ''}</small>
                    </td>
                    <td>
                        <select class="form-select form-select-sm student-status" data-student="${s.id}">
                            <option value="promoted">Promoted</option>
                            <option value="repeated">Repeated</option>
                            <option value="failed">Failed</option>
                        </select>
                    </td>
                    <td>
                        <input type="number" step="0.01" max="10" class="form-control form-control-sm student-gpa" data-student="${s.id}" placeholder="GPA">
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm student-remarks" data-student="${s.id}" placeholder="Remarks">
                    </td>
                </tr>
            `;
        });

        $('#studentsTbody').html(html);
        updateSelection();
    }

    function clearStudents(message = 'Load students using filters above') {
        currentStudents = [];
        selectedStudents = [];
        $('#studentsTbody').html(`
            <tr>
                <td colspan="7" class="text-center py-5 text-muted">${message}</td>
            </tr>
        `);
        $('#totalStudents').text(0);
        $('#selectedCount').text(0);
        $('#promoteBtn').prop('disabled', true);
    }

    function updateSelection() {
        selectedStudents = $('.student-checkbox:checked').map(function () {
            return parseInt($(this).val());
        }).get();

        $('#selectedCount').text(selectedStudents.length);
        updatePromoteButton();
    }

    function updatePromoteButton() {
        const enabled =
            selectedStudents.length > 0 &&
            $('#to_class_id').val();

        $('#promoteBtn').prop('disabled', !enabled);
    }

    function executePromotion() {
        if (!selectedStudents.length) {
            alert('Please select at least one student.');
            return;
        }

        const promotions = {};
        selectedStudents.forEach(studentId => {
            promotions[studentId] = {
                status: $(`.student-status[data-student="${studentId}"]`).val(),
                student_gpa: $(`.student-gpa[data-student="${studentId}"]`).val() || null,
                remarks: $(`.student-remarks[data-student="${studentId}"]`).val() || null
            };
        });

        const payload = {
            from_academic_year_id: $('#from_academic_year_id').val(),
            from_course_id: $('#from_course_id').val(),
            from_stream_id: $('#from_stream_id').val(),
            from_class_id: $('#from_class_id').val(),
            from_section_id: $('#from_section_id').val() || null,

            to_class_id: $('#to_class_id').val(),
            to_section_id: $('#to_section_id').val() || null,

            promotions: promotions
        };

        if (!confirm(`Process ${selectedStudents.length} selected students?`)) {
            return;
        }

        $('#promoteBtn').prop('disabled', true).text('Processing...');

        $.post("{{ route('admin.promotions.promote') }}", payload, function (res) {
            alert(res.message);
            location.reload();
        }).fail(function (xhr) {
            showError(xhr);
            updatePromoteButton();
            $('#promoteBtn').text('Promote Selected (' + selectedStudents.length + ')');
        });
    }

    function showError(xhr) {
        let msg = xhr.responseJSON?.message || 'Something went wrong';
        if (xhr.responseJSON?.errors) {
            const errors = Object.values(xhr.responseJSON.errors).flat();
            if (errors.length) msg = errors[0];
        }
        alert(msg);
    }
</script>
@endsection
