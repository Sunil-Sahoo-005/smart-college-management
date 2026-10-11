@extends('admin_layout')

@section('content')
    @php
        $isMergedAttendance = isset($schedule);
        $isLocked = $isLocked ?? false;
        $attendancePeriod = $isMergedAttendance ? $schedule->period : $slot->period;
        $attendanceSubject = $isMergedAttendance ? $schedule->subject : $slot->subject;
        $attendanceStaffId = $isMergedAttendance ? $schedule->staff_id : $slot->staff_id;
        $backRoute = route('admin.staff.show', $attendanceStaffId);
        $dateChangeRoute = $isMergedAttendance
            ? route('admin.student_attendance.mark_merged_direct', ['schedule' => $schedule->id, 'date' => ':date'])
            : route('admin.student_attendance.mark_direct', ['slot' => $slot->id, 'date' => ':date']);
        $storeRoute = $isMergedAttendance
            ? route('admin.student_attendance.store_merged')
            : route('admin.student_attendance.store');
    @endphp

    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top shadow-sm">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div class="d-flex align-items-center">
                            <a href="{{ $backRoute }}" class="btn btn-sm btn-light border me-3">
                                <i class="feather-arrow-left"></i>
                            </a>
                            <div>
                                <h5 class="fw-bold mb-0">Record Attendance</h5>
                                <p class="text-muted fs-12 mb-0">
                                    @if ($isMergedAttendance)
                                        {{ $attendanceSubject?->name ?? 'Merged Class' }} - Merged class attendance
                                    @else
                                        {{ $attendanceSubject?->name ?? 'Subject' }} - {{ $slot->timetable?->class?->name }} {{ $slot->timetable?->section?->name }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="input-group input-group-sm shadow-sm" style="width: 180px;">
                                <span class="input-group-text bg-white border-end-0"><i class="feather-calendar text-primary"></i></span>
                                <input type="date" class="form-control border-start-0 ps-0" id="attendanceDate" value="{{ $date->toDateString() }}" max="{{ date('Y-m-d') }}">
                            </div>
                            <span class="badge bg-soft-info text-info px-3 py-2">
                                <i class="feather-clock me-1"></i> {{ $attendancePeriod?->name }} ({{ $attendancePeriod?->start_time }} - {{ $attendancePeriod?->end_time }})
                            </span>
                        </div>
                    </div>
                </div>

                <div class="content-area-body p-4">
                    @if ($isLocked)
                        <div class="alert alert-warning border-0 shadow-sm mb-4">
                            Attendance is already saved for this date and period. You can view it, but you cannot edit it now.
                        </div>
                    @endif

                    @if ($isMergedAttendance)
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body p-3">
                                <div class="fw-bold text-dark mb-2">Merged Class Groups</div>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach (collect($schedule->class_combinations ?? []) as $combo)
                                        <span class="badge bg-light text-dark border px-3 py-2">
                                            {{ \App\Models\Course::find($combo['course_id'] ?? null)?->name ?? 'Course' }}
                                            - {{ \App\Models\Stream::find($combo['stream_id'] ?? null)?->name ?? 'Branch' }}
                                            - {{ \App\Models\Classe::find($combo['class_id'] ?? null)?->name ?? 'Semester' }}
                                            - {{ \App\Models\Section::find($combo['section_id'] ?? null)?->name ?? 'Section' }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row g-4 mb-4">
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm bg-primary text-white">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <div class="fs-11 text-white-50 text-uppercase fw-bold mb-1">Total Students</div>
                                            <div class="fs-24 fw-bold">{{ $students->count() }}</div>
                                        </div>
                                        <div class="stats-icon bg-white-20 rounded p-2">
                                            <i class="feather-users fs-20"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm bg-success text-white">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <div class="fs-11 text-white-50 text-uppercase fw-bold mb-1">Present</div>
                                            <div class="fs-24 fw-bold" id="presentCount">0</div>
                                        </div>
                                        <div class="stats-icon bg-white-20 rounded p-2">
                                            <i class="feather-check-circle fs-20"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm bg-danger text-white">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <div class="fs-11 text-white-50 text-uppercase fw-bold mb-1">Absent</div>
                                            <div class="fs-24 fw-bold" id="absentCount">0</div>
                                        </div>
                                        <div class="stats-icon bg-white-20 rounded p-2">
                                            <i class="feather-x-circle fs-20"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm bg-warning text-white">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <div class="fs-11 text-white-50 text-uppercase fw-bold mb-1">Late</div>
                                            <div class="fs-24 fw-bold" id="lateCount">0</div>
                                        </div>
                                        <div class="stats-icon bg-white-20 rounded p-2">
                                            <i class="feather-clock fs-20"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                            <h6 class="fw-bold mb-0">Student List</h6>
                            <div class="hstack gap-2">
                                <button type="button" class="btn btn-sm btn-outline-success" id="markAllPresent" {{ $isLocked ? 'disabled' : '' }}>
                                    Mark All Present
                                </button>
                                <button type="button" class="btn btn-sm btn-primary" id="saveAttendanceBtn" {{ $isLocked ? 'disabled' : '' }}>
                                    <i class="feather-save me-1"></i> Save Attendance
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4" width="100">Roll No</th>
                                            <th>Student Name</th>
                                            <th class="text-center" width="300">Status</th>
                                            <th class="pe-4" width="250">Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($students as $student)
                                            @php
                                                $record = $existing[$student->id] ?? null;
                                                $status = $record['status'] ?? 'absent';
                                                $remarks = $record['remarks'] ?? '';
                                            @endphp
                                            <tr data-student-id="{{ $student->id }}">
                                                <td class="ps-4 fw-semibold text-dark">{{ $student->student->roll_number }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm bg-soft-primary text-primary rounded-circle me-3 d-flex align-items-center justify-content-center fw-bold">
                                                            {{ strtoupper(substr($student->student->first_name, 0, 1)) }}
                                                        </div>
                                                        <div>
                                                            <div class="fw-bold text-dark">{{ $student->student->first_name }} {{ $student->student->last_name }}</div>
                                                            <div class="text-muted fs-11">
                                                                ID: {{ $student->student->registration_number }}
                                                                @if ($isMergedAttendance)
                                                                    - {{ $student->course?->name ?? 'Course' }}
                                                                    - {{ $student->stream?->name ?? 'Branch' }}
                                                                    - {{ $student->class?->name ?? 'Semester' }}
                                                                    - {{ $student->section?->name ?? 'Section' }}
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group w-100" role="group">
                                                        <input type="radio" class="btn-check" name="status_{{ $student->id }}" id="present_{{ $student->id }}" value="present" {{ $status == 'present' ? 'checked' : '' }} autocomplete="off" {{ $isLocked ? 'disabled' : '' }}>
                                                        <label class="btn btn-outline-success btn-sm py-2 px-3" for="present_{{ $student->id }}">Present</label>

                                                        <input type="radio" class="btn-check" name="status_{{ $student->id }}" id="absent_{{ $student->id }}" value="absent" {{ $status == 'absent' ? 'checked' : '' }} autocomplete="off" {{ $isLocked ? 'disabled' : '' }}>
                                                        <label class="btn btn-outline-danger btn-sm py-2 px-3" for="absent_{{ $student->id }}">Absent</label>

                                                        <input type="radio" class="btn-check" name="status_{{ $student->id }}" id="late_{{ $student->id }}" value="late" {{ $status == 'late' ? 'checked' : '' }} autocomplete="off" {{ $isLocked ? 'disabled' : '' }}>
                                                        <label class="btn btn-outline-warning btn-sm py-2 px-3" for="late_{{ $student->id }}">Late</label>
                                                    </div>
                                                </td>
                                                <td class="pe-4">
                                                    <input type="text" class="form-control form-control-sm border-0 bg-light" name="remarks_{{ $student->id }}" value="{{ $remarks }}" placeholder="Optional remarks..." {{ $isLocked ? 'readonly' : '' }}>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-5">
                                                    <div class="text-muted">
                                                        <i class="feather-users fs-1 mb-2 d-block"></i>
                                                        <p>No students enrolled in this class.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-top py-3 text-end">
                            <button type="button" class="btn btn-primary px-5" id="saveAttendanceBtnBottom" {{ $isLocked ? 'disabled' : '' }}>
                                <i class="feather-check-circle me-2"></i> Submit Attendance
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-white-20 { background: rgba(255, 255, 255, 0.2); }
        .stats-icon { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; }
        .avatar-sm { width: 32px; height: 32px; font-size: 12px; }
        .btn-check:checked + .btn-outline-success { background-color: #10b981 !important; color: white !important; border-color: #10b981 !important; }
        .btn-check:checked + .btn-outline-danger { background-color: #ef4444 !important; color: white !important; border-color: #ef4444 !important; }
        .btn-check:checked + .btn-outline-warning { background-color: #f59e0b !important; color: white !important; border-color: #f59e0b !important; }
    </style>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.css">

    <script>
        $(document).ready(function() {
            const isLocked = @json($isLocked);

            function updateStats() {
                let present = $('input[type="radio"][value="present"]:checked').length;
                let absent = $('input[type="radio"][value="absent"]:checked').length;
                let late = $('input[type="radio"][value="late"]:checked').length;

                $('#presentCount').text(present);
                $('#absentCount').text(absent);
                $('#lateCount').text(late);
            }

            $('#attendanceDate').on('change', function() {
                const newDate = $(this).val();
                if (newDate) {
                    let url = @json($dateChangeRoute);
                    url = url.replace(':date', newDate);
                    window.location.href = url;
                }
            });

            updateStats();
            $('input[type="radio"]').on('change', updateStats);

            $('#markAllPresent').on('click', function() {
                if (isLocked) {
                    return;
                }
                $('input[type="radio"][value="present"]').prop('checked', true);
                updateStats();
                toastr.info('All students marked as present');
            });

            $('#saveAttendanceBtn, #saveAttendanceBtnBottom').on('click', function() {
                if (isLocked) {
                    toastr.warning('Attendance is locked and cannot be edited.');
                    return;
                }

                const btn = $(this);
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');

                const attendance = {};
                $('tr[data-student-id]').each(function() {
                    const studentId = $(this).data('student-id');
                    const status = $(`input[name="status_${studentId}"]:checked`).val() || 'absent';
                    const remarks = $(`input[name="remarks_${studentId}"]`).val();

                    attendance[studentId] = {
                        status,
                        remarks
                    };
                });

                $.ajax({
                    url: @json($storeRoute),
                    method: 'POST',
                    data: {
                        date: "{{ $date->toDateString() }}",
                        @if ($isMergedAttendance)
                        schedule_id: "{{ $schedule->id }}",
                        @else
                        slot_id: "{{ $slot->id }}",
                        @endif
                        attendance: attendance,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        toastr.success('Attendance saved successfully!');
                        setTimeout(() => {
                            window.location.href = @json($backRoute);
                        }, 1500);
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Failed to save attendance');
                        btn.prop('disabled', false).html('<i class="feather-save me-1"></i> Save Attendance');
                    }
                });
            });
        });
    </script>
@endsection
