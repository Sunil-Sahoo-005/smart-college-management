@extends('teacher_layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        <div class="content-area" data-scrollbar-target="#psScrollbarInit">
            
            {{-- HEADER --}}
            <div class="content-area-header bg-white sticky-top shadow-sm">
                <div class="d-flex align-items-center justify-content-between p-3 w-100">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('teacher.teachersacademic.show') }}" class="btn btn-sm btn-light border me-3 rounded-circle" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                            <i class="feather-arrow-left"></i>
                        </a>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">Take Attendance</h5>
                            <p class="text-muted fs-12 mb-0">
                                {{ $slot->subject?->name }} • {{ $slot->timetable?->class?->name }} {{ $slot->timetable?->section?->name }}
                            </p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="input-group input-group-sm shadow-sm rounded overflow-hidden" style="width: 200px;">
                            <span class="input-group-text bg-white border-end-0 border-primary text-primary"><i class="feather-calendar"></i></span>
                            <input type="date" class="form-control border-start-0 border-primary ps-0 fw-bold" id="attendanceDate" value="{{ $date->toDateString() }}" max="{{ date('Y-m-d') }}" style="color: #4f46e5;">
                        </div>
                        <span class="badge bg-soft-primary text-primary px-3 py-2 border border-primary-subtle fs-12">
                            <i class="feather-clock me-1"></i> {{ $slot->period?->name }} ({{ Carbon\Carbon::parse($slot->period?->start_time)->format('h:i A') }} - {{ Carbon\Carbon::parse($slot->period?->end_time)->format('h:i A') }})
                        </span>
                    </div>
                </div>
            </div>

            <div class="content-area-body p-4 bg-light" style="min-height: calc(100vh - 70px);">
                
                {{-- Stats Summary --}}
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: linear-gradient(135deg, #4f46e5, #3b82f6);">
                            <div class="card-body p-4 position-relative">
                                <div class="position-absolute top-0 end-0 opacity-10 p-3">
                                    <i class="feather-users" style="font-size: 60px; color: white;"></i>
                                </div>
                                <div class="fs-12 text-white-50 text-uppercase fw-bold mb-1 letter-spacing-1">Total Enrolled</div>
                                <div class="fs-2 fw-bold text-white mb-0">{{ $students->count() }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: linear-gradient(135deg, #10b981, #059669);">
                            <div class="card-body p-4 position-relative">
                                <div class="position-absolute top-0 end-0 opacity-10 p-3">
                                    <i class="feather-check-circle" style="font-size: 60px; color: white;"></i>
                                </div>
                                <div class="fs-12 text-white-50 text-uppercase fw-bold mb-1 letter-spacing-1">Present</div>
                                <div class="fs-2 fw-bold text-white mb-0" id="presentCount">0</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                            <div class="card-body p-4 position-relative">
                                <div class="position-absolute top-0 end-0 opacity-10 p-3">
                                    <i class="feather-x-circle" style="font-size: 60px; color: white;"></i>
                                </div>
                                <div class="fs-12 text-white-50 text-uppercase fw-bold mb-1 letter-spacing-1">Absent</div>
                                <div class="fs-2 fw-bold text-white mb-0" id="absentCount">0</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                            <div class="card-body p-4 position-relative">
                                <div class="position-absolute top-0 end-0 opacity-10 p-3">
                                    <i class="feather-clock" style="font-size: 60px; color: white;"></i>
                                </div>
                                <div class="fs-12 text-white-50 text-uppercase fw-bold mb-1 letter-spacing-1">Late</div>
                                <div class="fs-2 fw-bold text-white mb-0" id="lateCount">0</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Main Attendance Card --}}
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold mb-0 text-dark fs-15"><i class="feather-list me-2 text-primary"></i>Student List</h6>
                        <div class="hstack gap-3">
                            <button type="button" class="btn btn-light border btn-sm rounded-pill px-4 fw-semibold shadow-sm hover-elevate" id="markAllPresent">
                                <i class="feather-check-circle text-success me-1"></i> Mark All Present
                            </button>
                            <button type="button" class="btn btn-primary btn-sm rounded-pill px-4 fw-semibold shadow-sm hover-elevate" id="saveAttendanceBtn">
                                <i class="feather-save me-1"></i> Save Attendance
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 custom-attendance-table">
                                <thead class="bg-light text-muted fs-11 text-uppercase letter-spacing-1">
                                    <tr>
                                        <th class="ps-4 border-0 py-3" width="100">Roll No</th>
                                        <th class="border-0 py-3">Student Name</th>
                                        <th class="text-center border-0 py-3" width="350">Attendance Status</th>
                                        <th class="pe-4 border-0 py-3" width="300">Remarks (Optional)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($students as $student)
                                        @php
                                            $record = $existing[$student->id] ?? null;
                                            $status = $record['status'] ?? 'absent';
                                            $remarks = $record['remarks'] ?? '';
                                        @endphp
                                        <tr data-student-id="{{ $student->id }}" class="attendance-row transition-all {{ $status == 'present' ? 'bg-soft-success' : ($status == 'absent' ? 'bg-soft-danger' : ($status == 'late' ? 'bg-soft-warning' : '')) }}">
                                            <td class="ps-4">
                                                <div class="badge bg-light text-dark border fs-12 py-2 px-3 fw-bold shadow-xs">{{ $student->student->roll_number }}</div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center py-2">
                                                    @if($student->student->photo_url)
                                                        <img src="{{ asset('storage/' . $student->student->photo_url) }}" class="rounded-circle shadow-sm me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                                    @else
                                                        <div class="rounded-circle shadow-sm me-3 d-flex align-items-center justify-content-center text-white fw-bold fs-14" style="width: 40px; height: 40px; background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                                                            {{ strtoupper(substr($student->student->first_name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="fw-bold text-dark fs-14">{{ $student->student->first_name }} {{ $student->student->last_name }}</div>
                                                        <div class="text-muted fs-11 mt-1"><i class="feather-hash"></i> {{ $student->student->registration_number }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="attendance-toggle bg-white rounded-pill p-1 shadow-sm border d-inline-flex">
                                                    <input type="radio" class="btn-check" name="status_{{ $student->id }}" id="present_{{ $student->id }}" value="present" {{ $status == 'present' ? 'checked' : '' }}>
                                                    <label class="btn btn-sm rounded-pill px-4 fw-semibold m-0 btn-toggle-present" for="present_{{ $student->id }}">Present</label>

                                                    <input type="radio" class="btn-check" name="status_{{ $student->id }}" id="absent_{{ $student->id }}" value="absent" {{ $status == 'absent' ? 'checked' : '' }}>
                                                    <label class="btn btn-sm rounded-pill px-4 fw-semibold m-0 btn-toggle-absent" for="absent_{{ $student->id }}">Absent</label>

                                                    <input type="radio" class="btn-check" name="status_{{ $student->id }}" id="late_{{ $student->id }}" value="late" {{ $status == 'late' ? 'checked' : '' }}>
                                                    <label class="btn btn-sm rounded-pill px-4 fw-semibold m-0 btn-toggle-late" for="late_{{ $student->id }}">Late</label>
                                                </div>
                                            </td>
                                            <td class="pe-4">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light border-0 text-muted"><i class="feather-edit-3"></i></span>
                                                    <input type="text" class="form-control bg-light border-0 fs-13" name="remarks_{{ $student->id }}" value="{{ $remarks }}" placeholder="Add remark...">
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5">
                                                <div class="text-muted">
                                                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                                        <i class="feather-users fs-1"></i>
                                                    </div>
                                                    <h5 class="fw-bold text-dark">No Students Found</h5>
                                                    <p class="fs-13">There are no active students enrolled in this class.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if($students->count() > 0)
                    <div class="card-footer bg-light border-top py-4 px-4 d-flex justify-content-between align-items-center">
                        <span class="text-muted fs-12"><i class="feather-info me-1"></i> Don't forget to save your changes</span>
                        <button type="button" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-sm hover-elevate fs-14" id="saveAttendanceBtnBottom">
                            <i class="feather-check-circle me-2"></i> Submit Attendance
                        </button>
                    </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    .letter-spacing-1 { letter-spacing: 1px; }
    .shadow-xs { box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .hover-elevate { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .hover-elevate:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important; }
    .transition-all { transition: all 0.3s ease; }
    
    /* Soft Backgrounds for Rows */
    .bg-soft-success { background-color: rgba(16, 185, 129, 0.05) !important; }
    .bg-soft-danger { background-color: rgba(239, 68, 68, 0.05) !important; }
    .bg-soft-warning { background-color: rgba(245, 158, 11, 0.05) !important; }
    
    .custom-attendance-table td { border-bottom: 1px solid #f3f4f6; }
    .custom-attendance-table tr:last-child td { border-bottom: none; }
    
    /* Toggle Button Styles */
    .attendance-toggle .btn { border: none; color: #6b7280; box-shadow: none !important; }
    
    .btn-check:checked + .btn-toggle-present { background-color: #10b981 !important; color: white !important; }
    .btn-check:checked + .btn-toggle-absent { background-color: #ef4444 !important; color: white !important; }
    .btn-check:checked + .btn-toggle-late { background-color: #f59e0b !important; color: white !important; }
    
    .attendance-row:hover { background-color: #f9fafb !important; }
</style>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.css">

<script>
    $(document).ready(function() {
        function updateStats() {
            let present = $('input[type="radio"][value="present"]:checked').length;
            let absent = $('input[type="radio"][value="absent"]:checked').length;
            let late = $('input[type="radio"][value="late"]:checked').length;

            $('#presentCount').text(present);
            $('#absentCount').text(absent);
            $('#lateCount').text(late);
        }

        // Row background update on change
        $('input[type="radio"]').on('change', function() {
            const row = $(this).closest('tr');
            row.removeClass('bg-soft-success bg-soft-danger bg-soft-warning');
            
            if (this.value === 'present') row.addClass('bg-soft-success');
            else if (this.value === 'absent') row.addClass('bg-soft-danger');
            else if (this.value === 'late') row.addClass('bg-soft-warning');
            
            updateStats();
        });

        // Date Change handling
        $('#attendanceDate').on('change', function() {
            const newDate = $(this).val();
            if (newDate) {
                let url = "{{ route('teacher.student_attendance.mark_direct', ['slot' => $slot->id, 'date' => ':date']) }}";
                url = url.replace(':date', newDate);
                window.location.href = url;
            }
        });

        // Initial stats
        updateStats();

        // Mark All Present
        $('#markAllPresent').on('click', function() {
            $('input[type="radio"][value="present"]').prop('checked', true).trigger('change');
            toastr.success('All students marked as present');
        });

        // Save Attendance
        $('#saveAttendanceBtn, #saveAttendanceBtnBottom').on('click', function() {
            const btn = $(this);
            const originalHtml = btn.html();
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Saving...');

            const attendance = {};
            $('tr[data-student-id]').each(function() {
                const studentId = $(this).data('student-id');
                const status = $(`input[name="status_${studentId}"]:checked`).val() || 'absent';
                const remarks = $(`input[name="remarks_${studentId}"]`).val();

                attendance[studentId] = { status, remarks };
            });

            $.ajax({
                url: "{{ route('teacher.student_attendance.store') }}",
                method: 'POST',
                data: {
                    date: "{{ $date->toDateString() }}",
                    slot_id: "{{ $slot->id }}",
                    attendance: attendance,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    toastr.success('Attendance saved successfully!');
                    setTimeout(() => {
                        window.location.href = "{{ route('teacher.teachersacademic.show') }}";
                    }, 1500);
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Failed to save attendance');
                    btn.prop('disabled', false).html(originalHtml);
                }
            });
        });
    });
</script>
@endsection
