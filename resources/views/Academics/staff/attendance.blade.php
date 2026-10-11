@extends('admin_layout')
@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Academics.sidebar')
        
        <div class="content-area">
            <div class="content-area-header bg-white sticky-top">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('admin.staff.classes', $staff->id) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="feather-arrow-left"></i> Back
                    </a>
                    <h5 class="mb-0 fw-bold">
    Mark Attendance - {{ $class->name }}
    @if($stream) ({{ $stream->name }}) @endif
    @if($section) | {{ $section->name }} @endif
</h5>

                </div>
            </div>
            
            <div class="content-area-body">
                <div class="card stretch stretch-full">
                    <div class="card-header d-flex justify-content-between">
                        <div>
                            <span class="badge bg-primary">{{ $period->name ?? 'General' }}</span>
                            <span class="badge bg-info ms-2">{{ now()->format('d M Y, h:i A') }}</span>
                        </div>
                        <button id="markAllPresent" class="btn btn-success btn-sm">Mark All Present</button>
                    </div>
                    
                    <div class="card-body p-0" style="max-height: 70vh; overflow-y: auto;">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th>#</th>
                                        <th>Photo</th>
                                        <th>Roll No</th>
                                        <th>Student Name</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="studentsTable">
                                    @forelse($students as $index => $student)
                                    <tr data-student-id="{{ $student->student_id }}" data-enrollment-id="{{ $student->id }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            @if($student->student->photo_url)
                                                <img src="{{ asset('storage/' . $student->student->photo_url) }}" 
                                                     class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;"
                                                     alt="{{ $student->name }}">
                                            @else
                                                <div class="avatar-text bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center" 
                                                     style="width: 40px; height: 40px; font-size: 0.8rem;">
                                                    {{ substr($student->name, 0, 2) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td><strong>{{ $student->student->roll_number ?? 'N/A' }}</strong></td>
                                        <td>{{ $student->name }}</td>
                                        <td>
                                            <select class="form-select form-select-sm attendance-status" 
                                                    data-enrollment-id="{{ $student->id }}">
                                                <option value="present">Present</option>
                                                <option value="absent">Absent</option>
                                                <option value="late">Late</option>
                                                <option value="half_day">Half Day</option>
                                            </select>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="feather-users fs-3 mb-2"></i>
                                            <p>No students found in this class</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <button id="saveAttendance" class="btn btn-primary px-4">
                                <i class="feather-save me-2"></i>Save Attendance
                            </button>
                            <div>
                                <span id="presentCount" class="badge bg-success me-2">0 Present</span>
                                <span id="absentCount" class="badge bg-danger me-2">0 Absent</span>
                                <span id="totalCount" class="badge bg-secondary">0 Total</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    let totalStudents = {{ $students->count() }};
    
    // Update counters
    function updateCounters() {
        let present = $('.attendance-status[value="present"]').length;
        let absent = $('.attendance-status[value="absent"]').length;
        let late = $('.attendance-status[value="late"]').length;
        let halfDay = $('.attendance-status[value="half_day"]').length;
        
        $('#presentCount').text(present + ' Present');
        $('#absentCount').text((absent + halfDay) + ' Absent/Late');
        $('#totalCount').text(present + absent + late + halfDay + ' / ' + totalStudents + ' Total');
    }
    
    // Status change handler
    $(document).on('change', '.attendance-status', function() {
        updateCounters();
    });
    
    // Mark all present
    $('#markAllPresent').click(function() {
        $('.attendance-status').val('present').trigger('change');
    });
    
    // Save attendance - FIXED ROUTE
    $('#saveAttendance').click(function() {
        let attendanceData = [];
        $('#studentsTable tr[data-student-id]').each(function() {
            let row = $(this);
            attendanceData.push({
                enrollment_id: row.data('enrollment-id'),
                status: row.find('.attendance-status').val(),
                student_id: row.data('student-id'),
                date: "{{ now()->format('Y-m-d') }}",
                period_id: {{ $period->id ?? 1 }}
            });
        });
        
        // ✅ FIXED: Correct route parameters
        $.ajax({
            url: "{{ route('admin.staff.attendance.store', [$staff->id, $class->id]) }}" + 
                 @if($section) "/{{ $section->id }}" @endif,
            method: 'POST',
            data: {
                attendance: attendanceData,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                alert('✅ Attendance saved successfully!');
                // Optional: redirect back
            },
            error: function(xhr) {
                alert('❌ Error: ' + xhr.responseJSON?.message || 'Failed to save');
            }
        });
    });
    
    updateCounters();
});
</script>
@endsection
