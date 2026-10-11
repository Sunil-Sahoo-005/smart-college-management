<div class="tab-pane fade" id="attendanceTab">
    <div class="card stretch stretch-full">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h6 class="card-title mb-0">
                <i class="feather-calendar me-2 text-primary"></i>
                Attendance Records ({{ $studentAttendance->total() }})
            </h6>
            <form method="GET" action="{{ route('admin.students.show', $student->id) }}" class="d-flex align-items-center gap-2">
                <label for="attendance_semester" class="small fw-semibold text-muted mb-0">Semester</label>
                <select name="attendance_semester" id="attendance_semester" class="form-select form-select-sm" style="min-width: 260px;" onchange="this.form.submit()">
                    <option value="">All semesters</option>
                    @foreach ($attendanceSemesterOptions as $option)
                        <option value="{{ $option['key'] }}" {{ (string) $selectedAttendanceSemesterKey === (string) $option['key'] ? 'selected' : '' }}>
                            {{ $option['label'] }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="card-body p-0">
            <!-- 🔥 Stats Cards -->
            @if ($studentAttendance->total() > 0)
                <div class="row g-3 p-3 border-bottom">
                    <div class="col-md-3">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center py-3">
                                <h4 class="mb-0 fw-bold text-dark">{{ $studentAttendance->total() }}</h4>
                                <small class="text-muted">Total Classes</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 h-100"
                            style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%)">
                            <div class="card-body text-center py-3">
                                <h4 class="mb-0 fw-bold text-success">{{ $attendanceStats['present'] ?? 0 }}</h4>
                                <small class="text-success fw-semibold">Present</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 h-100"
                            style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%)">
                            <div class="card-body text-center py-3">
                                <h4 class="mb-0 fw-bold text-danger">{{ $attendanceStats['absent'] ?? 0 }}</h4>
                                <small class="text-danger fw-semibold">Absent</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 h-100"
                            style="background: linear-gradient(135deg, #fef9c3 0%, #fef08a 100%)">
                            <div class="card-body text-center py-3">
                                <h4 class="mb-0 fw-bold text-warning">
                                    {{ ($attendanceStats['late'] ?? 0) + ($attendanceStats['half_day'] ?? 0) }}</h4>
                                <small class="text-warning fw-semibold">Late + Half</small>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- 🔥 Recent 7 Days Summary -->
            @if ($recentAttendance->isNotEmpty())
                <div class="table-responsive" style="max-height: 200px;">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Present</th>
                                <th>Absent</th>
                                <th>Late</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentAttendance as $date => $records)
                                <tr>
                                    <td><strong>{{ \Carbon\Carbon::parse($date)->format('d M') }}</strong></td>
                                    <td>{{ $records->where('status', 'present')->sum('count') }}</td>
                                    <td>{{ $records->where('status', 'absent')->sum('count') }}</td>
                                    <td>{{ $records->where('status', 'late')->sum('count') }}</td>
                                    <td>
                                        @php
                                            $total = $records->sum('count');
                                            $present = $records->where('status', 'present')->sum('count');
                                            $attendancePercent = $total > 0 ? round(($present / $total) * 100, 1) : 0;
                                        @endphp
                                        <span
                                            class="badge {{ $attendancePercent >= 80 ? 'badge-success' : ($attendancePercent >= 60 ? 'badge-warning' : 'badge-danger') }}">
                                            {{ $attendancePercent }}%
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- 🔥 Detailed Attendance Table -->
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Subject</th>
                            <th>Semester</th>
                            <th>Section</th>
                            <th>Period</th>
                            <th>Status</th>
                            <th>Marked By</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($studentAttendance as $index => $record)
                            <tr>
                                <td class="ps-4">{{ $studentAttendance->firstItem() + $index }}</td>
                                <td>
    @if($record->resolved_subject)
        <span class="fw-semibold">{{ $record->resolved_subject->name }}</span>
        @if($record->resolved_subject->code ?? false)
            <br><small class="text-muted">{{ $record->resolved_subject->code }}</small>
        @endif
    @else
        <span class="text-muted">—</span>
    @endif
</td> 
                                <td>
                                    <span class="badge bg-primary fs-2xs">
                                        {{ $record->enrollment->class->name ?? '—' }}
                                    </span>
                                </td>
                                <td>{{ $record->enrollment->section->name ?? '—' }}</td>
                                <td>
                                    <span class="badge bg-info text-dark fs-2xs">
                                        {{ $record->period->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <span
                                        class="badge 
                                            {{ $record->status == 'present' ? 'bg-success' : '' }}
                                            {{ $record->status == 'absent' ? 'bg-danger' : '' }}
                                            {{ $record->status == 'late' ? 'bg-warning text-dark' : '' }}
                                            {{ $record->status == 'half_day' ? 'bg-info text-dark' : '' }}
                                            {{ !in_array($record->status, ['present', 'absent', 'late', 'half_day']) ? 'bg-secondary' : '' }}">
                                        {{ [
                                            'present' => '✓ Present',
                                            'absent' => '✗ Absent',
                                            'late' => '→ Late',
                                            'half_day' => '½ Half Day',
                                        ][$record->status] ?? ucfirst($record->status) }}
                                    </span>
                                </td>

                                <td>
                                    <small class="text-muted">{{ $record->markedBy->name ?? 'System' }}</small>
                                </td>
                                <td>
                                    @if ($record->remarks)
                                        <span class="text-muted small d-block" title="{{ $record->remarks }}">
                                            {{ Str::limit($record->remarks, 30) }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="feather-calendar fs-1 mb-3 d-block opacity-50"></i>
                                    <h6>No attendance records found</h6>
                                    <p class="mb-0 small">Student has no attendance data yet.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($studentAttendance->hasPages())
                <div class="d-flex justify-content-between align-items-center py-3 px-4 border-top bg-light">
                    <div class="small text-muted">
                        Showing {{ $studentAttendance->firstItem() }} to {{ $studentAttendance->lastItem() }}
                        of {{ $studentAttendance->total() }} results
                    </div>
                    <div>
                        {{ $studentAttendance->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
