@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Attendance Report</span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('admin.student_attendance.index') }}" class="btn btn-secondary btn-sm">
                                <i class="feather-arrow-left me-2"></i>Back
                            </a>
                            <a href="#" id="exportExcel" class="btn btn-success btn-sm">
                                <i class="feather-download me-2"></i>Excel
                            </a>
                            <a href="#" id="exportPdf" class="btn btn-danger btn-sm">
                                <i class="feather-file-text me-2"></i>PDF
                            </a>
                        </div>
                    </div>
                </div>

                <div class="content-area-body">
                    {{-- ── Filters Card ── --}}
                    <div class="card mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-4">
                                <i class="feather-filter me-2 text-primary"></i>Filter Attendance Records
                            </h6>

                            <div class="row g-2 mb-3">
                                {{-- Course --}}
                                <div class="col-lg-2 col-md-3 col-sm-6">
                                    <label class="fw-semibold mb-1 d-block small">Course</label>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-text"><i class="feather-book-open"></i></div>
                                        <select class="form-select" id="f_course_id">
                                            <option value="">All Courses</option>
                                            @foreach ($courses as $course)
                                                <option value="{{ $course->id }}"
                                                    {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                                    {{ $course->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Academic Year --}}
                                <div class="col-lg-2 col-md-3 col-sm-6">
                                    <label class="fw-semibold mb-1 d-block small">Academic Year</label>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-text"><i class="feather-calendar"></i></div>
                                        <select class="form-select" id="f_academic_year_id">
                                            <option value="">All Years</option>
                                            @foreach ($academicYears as $year)
                                                <option value="{{ $year->id }}"
                                                    {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                                                    {{ $year->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Stream --}}
                                <div class="col-lg-2 col-md-3 col-sm-6">
                                    <label class="fw-semibold mb-1 d-block small">Branch</label>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-text"><i class="feather-book"></i></div>
                                        <select class="form-select" id="f_stream_id">
                                            <option value="">All Branches</option>
                                            @foreach ($streams as $stream)
                                                <option value="{{ $stream->id }}"
                                                    {{ request('stream_id') == $stream->id ? 'selected' : '' }}>
                                                    {{ $stream->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Class --}}
                                <div class="col-lg-2 col-md-3 col-sm-6">
                                    <label class="fw-semibold mb-1 d-block small">Semester</label>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-text"><i class="feather-hash"></i></div>
                                        <select class="form-select" id="f_class_id">
                                            <option value="">All Semesters</option>
                                            @foreach ($classes as $class)
                                                <option value="{{ $class->id }}"
                                                    {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                                    {{ $class->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Section --}}
                                <div class="col-lg-2 col-md-3 col-sm-6">
                                    <label class="fw-semibold mb-1 d-block small">Section</label>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-text"><i class="feather-hash"></i></div>
                                        <select class="form-select" id="f_section_id">
                                            <option value="">All Sections</option>
                                            @foreach ($sections as $section)
                                                <option value="{{ $section->id }}"
                                                    {{ request('section_id') == $section->id ? 'selected' : '' }}>
                                                    {{ $section->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Status --}}
                                <div class="col-lg-2 col-md-3 col-sm-6">
                                    <label class="fw-semibold mb-1 d-block small">Status</label>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-text"><i class="feather-activity"></i></div>
                                        <select class="form-select" id="f_status">
                                            <option value="">All Statuses</option>
                                            <option value="present"
                                                {{ request('status') === 'present' ? 'selected' : '' }}>Present</option>
                                            <option value="absent" {{ request('status') === 'absent' ? 'selected' : '' }}>
                                                Absent</option>
                                            <option value="late" {{ request('status') === 'late' ? 'selected' : '' }}>
                                                Late</option>
                                            <option value="half_day"
                                                {{ request('status') === 'half_day' ? 'selected' : '' }}>Half Day</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Search --}}
                                <div class="col-lg-2 col-md-3 col-sm-6">
                                    <label class="fw-semibold mb-1 d-block small">Search Student</label>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-text"><i class="feather-search"></i></div>
                                        <input type="text" class="form-control" id="f_search"
                                            value="{{ request('search') }}" placeholder="Name or Roll No...">
                                    </div>
                                </div>

                                {{-- Date --}}
                                <div class="col-lg-2 col-md-3 col-sm-6">
                                    <label class="fw-semibold mb-1 d-block small">Date</label>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-text"><i class="feather-calendar"></i></div>
                                        <input type="date" class="form-control" id="f_date"
                                            value="{{ request('date', date('Y-m-d')) }}">
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-6 mt-4 d-flex gap-2">
                                    <button type="button" id="resetFilters" class="btn btn-outline-secondary btn-sm px-4">
                                    <i class="feather-x me-2"></i>Reset
                                </button>
                                <button type="button" id="applyFilters" class="btn btn-primary btn-sm px-4">
                                    <i class="feather-search me-2"></i>Apply Filters
                                </button>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <!-- <button type="button" id="applyFilters" class="btn btn-primary btn-sm px-4">
                                    <i class="feather-search me-2"></i>Apply Filters
                                </button> -->
                                <!-- <button type="button" id="resetFilters" class="btn btn-outline-secondary btn-sm px-4">
                                    <i class="feather-x me-2"></i>Reset
                                </button> -->
                            </div>
                        </div>
                    </div>

                    {{-- ── Summary Stats ── --}}
                    @if ($attendances->total() > 0)
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <div class="card border-0 bg-light">
                                    <div class="card-body py-3 text-center">
                                        <h4 class="mb-0 fw-bold text-dark">{{ $attendances->total() }}</h4>
                                        <small class="text-muted">Total Records</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0"
                                    style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%)">
                                    <div class="card-body py-3 text-center">
                                        <h4 class="mb-0 fw-bold text-success">{{ $stats['present'] ?? 0 }}</h4>
                                        <small class="text-success fw-semibold">Present</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0"
                                    style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%)">
                                    <div class="card-body py-3 text-center">
                                        <h4 class="mb-0 fw-bold text-danger">{{ $stats['absent'] ?? 0 }}</h4>
                                        <small class="text-danger fw-semibold">Absent</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0"
                                    style="background: linear-gradient(135deg, #fef9c3 0%, #fef08a 100%)">
                                    <div class="card-body py-3 text-center">
                                        <h4 class="mb-0 fw-bold text-warning">
                                            {{ ($stats['late'] ?? 0) + ($stats['half_day'] ?? 0) }}</h4>
                                        <small class="text-warning fw-semibold">Late + Half Day</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- ── Attendance Table ── --}}
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">#</th>
                                            <th>Roll No</th>
                                            <th>Student</th>
                                            <th>Course</th>
                                            <th>Branch</th>
                                            <th>Semester</th>
                                            <th>Section</th>
                                            <th>Period</th>
                                            <th>Status</th>
                                            <th>Remarks</th>
                                            <th>Marked By</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($attendances as $i => $attendance)
                                            <tr>
                                                <td class="ps-4 text-muted">{{ $attendances->firstItem() + $i }}</td>
                                                <td>
                                                    <span
                                                        class="fw-bold">{{ $attendance->enrollment->student->roll_number ?? '—' }}</span>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong class="d-block">
                                                            {{ $attendance->enrollment->student->first_name ?? '' }}
                                                            {{ $attendance->enrollment->student->last_name ?? '' }}
                                                        </strong>
                                                        <small class="text-muted">
                                                            ID: {{ $attendance->enrollment->student->id ?? 'N/A' }}
                                                        </small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark fs-2xs">
                                                        {{ $attendance->enrollment->course->name ?? '—' }}
                                                    </span>
                                                </td>
                                                <td>{{ $attendance->enrollment->stream->name ?? '—' }}</td>
                                                <td>
                                                    <span class="badge bg-primary fs-2xs">
                                                        {{ $attendance->enrollment->class->name ?? '—' }}
                                                    </span>
                                                </td>
                                                <td>{{ $attendance->enrollment->section->name ?? '—' }}</td>
                                                <td>
                                                    <span class="badge bg-info text-dark fs-2xs">
                                                        {{ $attendance->period->name ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @php
                                                        $statusConfig = [
                                                            'present' => ['bg-success', '✓ Present'],
                                                            'absent' => ['bg-danger', '✗ Absent'],
                                                            'late' => ['bg-warning text-dark', '→ Late'],
                                                            'half_day' => ['bg-info text-dark', '½ Half Day'],
                                                            'default' => ['bg-secondary', 'Unknown'],
                                                        ];
                                                        $statusClass =
                                                            $statusConfig[$attendance->status][0] ??
                                                            $statusConfig['default'][0];
                                                        $statusLabel =
                                                            $statusConfig[$attendance->status][1] ??
                                                            $statusConfig['default'][1];
                                                    @endphp
                                                    <span class="badge {{ $statusClass }}">
                                                        {{ $statusLabel }}
                                                    </span>
                                                </td>

                                                <td>
                                                    @if ($attendance->remarks)
                                                        <span class="text-muted small d-block"
                                                            title="{{ $attendance->remarks }}">
                                                            {{ Str::limit($attendance->remarks, 30) }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ $attendance->markedBy->name ?? 'System' }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <strong>{{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}</strong>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="11" class="text-center py-5 text-muted">
                                                    <i class="feather-calendar fs-1 mb-3 d-block opacity-50"></i>
                                                    <h6>No attendance records found</h6>
                                                    <p class="mb-0 small">Try adjusting the filters above to see results.
                                                    </p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if ($attendances->hasPages())
                                <div class="d-flex justify-content-between align-items-center py-3 px-4 border-top">
                                    <div class="small text-muted">
                                        Showing {{ $attendances->firstItem() }} to {{ $attendances->lastItem() }}
                                        of {{ $attendances->total() }} results
                                    </div>
                                    <div>
                                        {{ $attendances->appends(request()->query())->links() }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── jQuery & Custom Scripts ── --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            let currentFilters = {
                course_id: '{{ request('course_id') }}',
                academic_year_id: '{{ request('academic_year_id') }}',
                stream_id: '{{ request('stream_id') }}',
                class_id: '{{ request('class_id') }}'
            };

            // ── Dynamic Filter Chain: Course → Stream → Class → Section ────────
            $('#f_course_id').on('change', function() {
                currentFilters.course_id = $(this).val();
                currentFilters.stream_id = '';
                currentFilters.class_id = '';
                $('#f_stream_id, #f_class_id, #f_section_id').empty().append(
                    '<option value="">All Branches</option>');
                $('#f_class_id').append('<option value="">All Semesters</option>');
                $('#f_section_id').append('<option value="">All Sections</option>');
                if (currentFilters.course_id) loadStreams();
            });

            $('#f_academic_year_id, #f_stream_id').on('change', function() {
                currentFilters.academic_year_id = $('#f_academic_year_id').val();
                currentFilters.stream_id = $('#f_stream_id').val();
                currentFilters.class_id = '';
                $('#f_class_id, #f_section_id').empty().append(
                    '<option value="">All Semesters</option>');
                $('#f_section_id').append('<option value="">All Sections</option>');
                if (currentFilters.academic_year_id && currentFilters.stream_id) loadClasses();
            });

            $('#f_class_id').on('change', function() {
                currentFilters.class_id = $(this).val();
                $('#f_section_id').empty().append('<option value="">All Sections</option>');
                if (currentFilters.class_id) loadSections();
            });

            function loadStreams() {
                const $streamSelect = $('#f_stream_id');
                $streamSelect.html('<option value="">Loading branches...</option>');

                $.ajax({
                    url: "{{ route('admin.student_attendance.get_streams') }}",
                    method: 'POST',
                    data: {
                        course_id: currentFilters.course_id,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $streamSelect.html('<option value="">All Branches</option>');
                        response.streams.forEach(st => {
                            $streamSelect.append(
                                `<option value="${st.id}">${st.name}</option>`);
                        });
                        // Restore selected stream if it exists
                        if (currentFilters.stream_id && response.streams.some(s => s.id == currentFilters.stream_id)) {
                            $streamSelect.val(currentFilters.stream_id);
                            loadClasses();
                        }
                    }
                });
            }

            function loadClasses() {
                const $classSelect = $('#f_class_id');
                $classSelect.html('<option value="">Loading semesters...</option>');

                $.ajax({
                    url: "{{ route('admin.student_attendance.get_classes') }}",
                    method: 'POST',
                    data: {
                        course_id: currentFilters.course_id,
                        academic_year_id: currentFilters.academic_year_id,
                        stream_id: currentFilters.stream_id,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $classSelect.html('<option value="">All Semesters</option>');
                        response.classes.forEach(cls => {
                            $classSelect.append(
                                `<option value="${cls.id}">${cls.name}</option>`);
                        });
                        // Restore selected class if it exists in new list
                        if (currentFilters.class_id && response.classes.some(c => c.id == currentFilters
                                .class_id)) {
                            $classSelect.val(currentFilters.class_id);
                            loadSections();
                        }
                    },
                    error: function() {
                        $classSelect.html('<option value="">Error loading semesters</option>');
                    }
                });
            }

            function loadSections() {
                const $sectionSelect = $('#f_section_id');
                if (!currentFilters.class_id) return;

                $sectionSelect.html('<option value="">Loading sections...</option>');

                $.ajax({
                    url: "{{ route('admin.student_attendance.get_sections') }}",
                    method: 'POST',
                    data: {
                        course_id: currentFilters.course_id,
                        stream_id: currentFilters.stream_id,
                        class_id: currentFilters.class_id,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $sectionSelect.html('<option value="">All Sections</option>');
                        response.sections.forEach(sec => {
                            $sectionSelect.append(
                                `<option value="${sec.id}">${sec.name}</option>`);
                        });
                    }
                });
            }

            // ── Auto-apply filters on change ─────────────────────────────────────
            $('#f_course_id, #f_academic_year_id, #f_stream_id, #f_class_id, #f_section_id, #f_status, #f_date').on('change', function() {
                $('#applyFilters').trigger('click');
            });

            // ── Filter Application ───────────────────────────────────────────────
            function buildQuery() {
                const params = new URLSearchParams();
                const filters = {
                    date: $('#f_date').val(),
                    course_id: $('#f_course_id').val(),
                    academic_year_id: $('#f_academic_year_id').val(),
                    stream_id: $('#f_stream_id').val(),
                    class_id: $('#f_class_id').val(),
                    section_id: $('#f_section_id').val(),
                    status: $('#f_status').val(),
                    search: $('#f_search').val()
                };

                Object.entries(filters).forEach(([key, value]) => {
                    if (value) params.set(key, value);
                });

                return params.toString();
            }

            $('#applyFilters').on('click', function() {
                const query = buildQuery();
                window.location.href = "{{ route('admin.student_attendance.date') }}?" + query;
            });

            $('#resetFilters').on('click', function() {
                window.location.href = "{{ route('admin.student_attendance.date') }}";
            });

            // ── Export with Filters ──────────────────────────────────────────────
            $('#exportExcel').on('click', function(e) {
                e.preventDefault();
                window.location.href = "{{ route('admin.student_attendance.date.export.excel') }}?" +
                    buildQuery();
            });

            $('#exportPdf').on('click', function(e) {
                e.preventDefault();
                window.location.href = "{{ route('admin.student_attendance.date.export.pdf') }}?" +
                    buildQuery();
            });

            // ── Enter key on date/search input ──────────────────────────────────────────
            $('#f_date, #f_search').on('keydown', function(e) {
                if (e.key === 'Enter') $('#applyFilters').trigger('click');
            });

            // ── Initialize current filters on page load ──────────────────────────
            if (currentFilters.course_id) loadStreams();
            // loadClasses and loadSections will be chained inside loadStreams/loadClasses if IDs exist
        });
    </script>
@endsection
