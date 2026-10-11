@extends('admin_layout')

@section('content')
    <div class="nxl-content">
        <!-- [ page-header ] start -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Mark Student Attendance</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.student_attendance.index') }}">Attendance</a></li>
                    <li class="breadcrumb-item">Mark Attendance</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                    <a href="{{ route('admin.student_attendance.index') }}" class="btn btn-secondary">
                        <i class="feather-arrow-left me-2"></i>Back to List
                    </a>
                </div>
            </div>
        </div>

        <div class="main-content">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <!-- Filters -->
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Class</label>
                                    <select class="form-select" id="class_id">
                                        <option value="">Select Class</option>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Section</label>
                                    <select class="form-select" id="section_id">
                                        <option value="">Select Section</option>
                                        @foreach ($sections as $section)
                                            <option value="{{ $section->id }}">{{ $section->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold">Date</label>
                                    <input type="date" class="form-control" id="date" value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold">Period</label>
                                    <select class="form-select" id="period_id">
                                        <option value="">All Periods</option>
                                        @foreach ($periods as $period)
                                            <option value="{{ $period->id }}">{{ $period->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" id="loadStudents" class="btn btn-primary w-100">
                                        <i class="feather-loader me-2"></i>Load Students
                                    </button>
                                </div>
                            </div>

                            <!-- Students Table -->
                            <div id="studentsTable" class="attendance-table-wrapper" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 id="studentsCount" class="mb-0"></h6>
                                    <button type="button" id="submitAttendance" class="btn btn-success">
                                        <i class="feather-check me-2"></i>Submit Attendance
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="60">Roll</th>
                                                <th>Student Name</th>
                                                <th width="150">
                                                    <div class="d-flex align-items-center">
                                                        <span id="selectedSubject" class="me-2"></span>
                                                        <select class="form-select form-select-sm" id="subject_id"
                                                            style="width: auto;">
                                                            <option value="">All Subjects</option>
                                                        </select>
                                                    </div>
                                                </th>
                                                <th width="200" class="text-center">Status</th>
                                                <th width="150">Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody id="studentsList">
                                            <!-- Students loaded via AJAX -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- No Students Message -->
                            <div id="noStudents" class="text-center py-5" style="display: none;">
                                <i class="feather-users fs-1 text-muted mb-3"></i>
                                <h5 class="text-muted">No students found</h5>
                                <p class="text-muted">Please select a valid class and section combination.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div class="modal fade" id="editAttendanceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Attendance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editAttendanceForm">
                        @csrf
                        <input type="hidden" id="edit_id">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="edit_status" required>
                                <option value="present">Present</option>
                                <option value="absent">Absent</option>
                                <option value="late">Late</option>
                                <option value="half_day">Half Day</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Remarks</label>
                            <textarea class="form-control" id="edit_remarks" rows="2"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveEdit">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    
    <script>
        $(document).ready(function() {
            let studentsData = [];

            // Load students when filters change
            $('#loadStudents').click(function() {
                const classId = $('#class_id').val();
                const sectionId = $('#section_id').val();
                const date = $('#date').val();
                const periodId = $('#period_id').val();

                if (!classId || !sectionId || !date) {
                    alert('Please select Class, Section, and Date');
                    return;
                }

                $.ajax({
                    url: "{{ route('admin.student_attendance.get_students') }}",
                    method: 'POST',
                    data: {
                        class_id: classId,
                        section_id: sectionId,
                        date: date,
                        period_id: periodId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        studentsData = response.students;
                        displayStudents(response);
                    }
                });
            });

            function displayStudents(response) {
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
                $('#selectedSubject').text(response.subjects.length ? response.subjects[0].subject.name :
                'General');

                // Populate subjects
                const subjectSelect = $('#subject_id');
                subjectSelect.empty().append('<option value="">All Subjects</option>');
                response.subjects.forEach(subject => {
                    subjectSelect.append(`<option value="${subject.id}">${subject.subject.name}</option>`);
                });

                studentsData.forEach((student, index) => {
                    const existing = response.existing[student.id] || 'absent';
                    const remarks = ''; // Fetch from existing if needed

                    const row = `
                <tr data-student-id="${student.id}">
                    <td>${student.roll_number}</td>
                    <td>${student.first_name} ${student.last_name}</td>
                    <td>
                        <select class="form-select form-select-sm subject-select">
                            <option value="">General</option>
                            ${response.subjects.map(s => `<option value="${s.id}">${s.subject.name}</option>`).join('')}
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
                        <input type="text" class="form-control form-control-sm" name="remarks_${student.id}" value="${remarks}" placeholder="Optional remarks">
                    </td>
                </tr>
            `;
                    studentsList.append(row);
                });
            }

            // Submit attendance
            $('#submitAttendance').click(function() {
                const classId = $('#class_id').val();
                const sectionId = $('#section_id').val();
                const date = $('#date').val();
                const periodId = $('#period_id').val();

                const attendance = {};
                studentsData.forEach(student => {
                    const status = $(`input[name="status_${student.id}"]:checked`).val() ||
                    'absent';
                    const remarks = $(`input[name="remarks_${student.id}"]`).val();
                    attendance[student.id] = {
                        status,
                        remarks
                    };
                });

                $.ajax({
                    url: "{{ route('admin.student_attendance.store') }}",
                    method: 'POST',
                    data: {
                        class_id: classId,
                        section_id: sectionId,
                        date: date,
                        period_id: periodId,
                        attendance: attendance,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        alert('Attendance marked successfully!');
                        $('#studentsTable').hide();
                    }
                });
            });
        });
    </script>
@endsection



