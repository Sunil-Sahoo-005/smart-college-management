@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Accountants.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">
                            {{ $planId ? 'Edit Fee Structure Assignment' : 'Assign Fee Structure' }}
                        </span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <a href="{{ route('admin.student_fee_structures.index') }}" class="btn btn-secondary">
                            Back to Plans
                        </a>
                    </div>
                </div>

                <div class="content-area-body">
                    <form method="POST" action="{{ route('admin.student_fee_structures.assign.store') }}" id="assignForm">
                        @csrf
                        @if ($planId)
                            <input type="hidden" name="plan_id" value="{{ $planId }}">
                        @endif

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Branch (Stream)</label>
                                <select name="stream_id" id="stream_id" class="form-select" required>
                                    <option value="">Select Branch</option>
                                    @foreach ($streams as $stream)
                                        <option value="{{ $stream->id }}"
                                            {{ (request('stream_id') ?? old('stream_id')) == $stream->id ? 'selected' : '' }}>
                                            {{ $stream->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Academic Year</label>
                                <select name="academic_year_id" id="academic_year_id" class="form-select" required>
                                    <option value="">Select Year</option>
                                    @foreach ($academicYears as $year)
                                        <option value="{{ $year->id }}"
                                            {{ (request('academic_year_id') ?? old('academic_year_id')) == $year->id ? 'selected' : '' }}>
                                            {{ $year->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Fee Structure</label>
                                <select name="fee_structure_id" class="form-select" required>
                                    <option value="">Select Fee Structure</option>
                                    @foreach ($feeStructures as $fs)
                                        <option value="{{ $fs->id }}"
                                            {{ (request('fee_structure_id') ?? old('fee_structure_id')) == $fs->id ? 'selected' : '' }}>
                                            {{ $fs->name }}
                                            ({{ $fs->stream?->name ?? 'N/A' }} – {{ $fs->class?->name ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            @if (!$planId)
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                                    Select All
                                </button>
                            @endif
                        </div>

                        <div class="table-responsive" id="enrollments-container">
                            <!-- students will be injected here -->
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                {{ $planId ? 'Update Assignment' : 'Assign Fee Structure' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function loadEnrollments() {
            const streamId       = $('#stream_id').val();
            const academicYearId = $('#academic_year_id').val();
            const planId         = '{{ $planId ?? '' }}';
            const isEditMode     = !!planId;

            if (!streamId || !academicYearId) {
                $('#enrollments-container').html(
                    '<div class="text-center text-muted">Select branch and academic year to see enrolled students.</div>'
                );
                return;
            }

            const url = isEditMode
                ? "{{ route('admin.ajax.enrolled_students') }}"
                : "{{ route('admin.ajax.enrolled_students_add') }}";

            const data = {
                stream_id: streamId,
                academic_year_id: academicYearId,
            };

            if (isEditMode) {
                data.fee_structure_id = $('select[name="fee_structure_id"]').val();
                data.plan_id          = planId;
            }

            $.ajax({
                url: url,
                method: 'GET',
                data: data,
                success: function(response) {
                    $('#enrollments-container').html(response.html);
                },
                error: function(xhr) {
                    let msg = 'Failed to load students.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    $('#enrollments-container').html(
                        '<div class="text-center text-danger">' + msg + '</div>'
                    );
                }
            });
        }

        $(document).ready(function() {
            const streamId       = $('#stream_id').val();
            const academicYearId = $('#academic_year_id').val();

            if (streamId && academicYearId) {
                loadEnrollments();
            }
        });

        $('#stream_id, #academic_year_id, select[name="fee_structure_id"]').on('change', loadEnrollments);

        function toggleAll(checkbox) {
            const checkboxes = document.querySelectorAll('.student-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = checkbox.checked;
            });
        }

        function selectAll() {
            const selectAllCheckbox = document.getElementById('select-all');
            selectAllCheckbox.checked = true;
            toggleAll(selectAllCheckbox);
        }
    </script>
@endsection
