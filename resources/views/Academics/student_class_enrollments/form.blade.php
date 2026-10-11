@extends('admin_layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Academics.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">
            <div class="content-area-header bg-white sticky-top">
                <h5 class="fw-bold mb-0 me-4">
                    <span class="d-block mb-2">
                        {{ !empty($enrollment->id) ? 'Edit Enrollment' : 'Enroll Student' }}
                    </span>
                </h5>
                <div class="page-header-right ms-auto">
                    <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                        <a href="{{ route('admin.student_class_enrollments.index') }}" class="btn btn-secondary">
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
                                <span class="d-block mb-2">Enrollment Information :</span>
                                <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                    Link student to class, section, and academic year
                                </span>
                            </h5>
                            <button type="submit" form="enrollmentForm" class="btn btn-primary">Save</button>
                        </div>

                        <form id="enrollmentForm" action="{{ route('admin.student_class_enrollments.store') }}" method="POST">
                            @csrf
                            @if (!empty($enrollment->id))
                                <input type="hidden" name="id" value="{{ $enrollment->id }}">
                            @endif

                            {{-- Academic Year --}}
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="academic_year_id" class="fw-semibold">Academic Year:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-calendar"></i></div>
                                        <select class="form-select @error('academic_year_id') is-invalid @enderror"
                                                id="academic_year_id" name="academic_year_id" required>
                                            <option value="">Select Academic Year</option>
                                            @foreach ($academicYears as $year)
                                                <option value="{{ $year->id }}"
                                                        {{ old('academic_year_id', $enrollment->academic_year_id ?? '') == $year->id ? 'selected' : '' }}>
                                                    {{ $year->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('academic_year_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Student Type --}}
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="student_type" class="fw-semibold">Student Type:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-users"></i></div>
                                        <select class="form-select @error('student_type') is-invalid @enderror"
                                                id="student_type" name="student_type">
                                            <option value="">Select Student Type</option>
                                            <option value="regular"
                                                {{ old('student_type', $enrollment->student_type ?? '') == 'regular' ? 'selected' : '' }}>
                                                Regular
                                            </option>
                                            <option value="lateral"
                                                {{ old('student_type', $enrollment->student_type ?? '') == 'lateral' ? 'selected' : '' }}>
                                                Lateral
                                            </option>
                                        </select>
                                    </div>
                                    @error('student_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Branch --}}
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="stream_id" class="fw-semibold">Branch:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-book"></i></div>
                                        <select class="form-select @error('stream_id') is-invalid @enderror"
                                                id="stream_id" name="stream_id">
                                            <option value="">No Branch</option>
                                            @foreach ($streams as $stream)
                                                <option value="{{ $stream->id }}"
                                                        {{ old('stream_id', $enrollment->stream_id ?? '') == $stream->id ? 'selected' : '' }}>
                                                    {{ $stream->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('stream_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Semester --}}
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="class_id" class="fw-semibold">Semester:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-hash"></i></div>
                                        <select class="form-select @error('class_id') is-invalid @enderror"
                                                id="class_id" name="class_id" required>
                                            <option value="">Select Semester</option>
                                            @foreach ($classes as $class)
                                                <option value="{{ $class->id }}"
                                                        {{ old('class_id', $enrollment->class_id ?? '') == $class->id ? 'selected' : '' }}>
                                                    {{ $class->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('class_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Section --}}
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="section_id" class="fw-semibold">Section:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-hash"></i></div>
                                        <select class="form-select @error('section_id') is-invalid @enderror"
                                                id="section_id" name="section_id" required>
                                            <option value="">Select Section</option>
                                            @foreach ($sections as $section)
                                                <option value="{{ $section->id }}"
                                                        {{ old('section_id', $enrollment->section_id ?? '') == $section->id ? 'selected' : '' }}>
                                                    {{ $section->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('section_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Student --}}
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="student_id" class="fw-semibold">Student:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-user"></i></div>
                                        <select class="form-select @error('student_id') is-invalid @enderror"
                                                id="student_id" name="student_id" required>
                                            <option value="">Select Student</option>
                                            @foreach ($students as $student)
                                                <option value="{{ $student->id }}"
                                                        {{ old('student_id', $enrollment->student_id ?? '') == $student->id ? 'selected' : '' }}>
                                                    {{ $student->first_name }} {{ $student->last_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('student_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Date Of Admission --}}
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="dateof_admission" class="fw-semibold">Date Of Admission:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-calendar"></i></div>
                                        <input type="date"
                                               class="form-control @error('dateof_admission') is-invalid @enderror"
                                               id="dateof_admission" name="dateof_admission"
                                               value="{{ old('dateof_admission', $enrollment->dateof_admission ?? '') }}">
                                    </div>
                                    @error('dateof_admission')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Date Of Leaving --}}
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="date_of_leaving" class="fw-semibold">Date Of Leaving:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-log-out"></i></div>
                                        <input type="date"
                                               class="form-control @error('date_of_leaving') is-invalid @enderror"
                                               id="date_of_leaving" name="date_of_leaving"
                                               value="{{ old('date_of_leaving', isset($enrollment->date_of_leaving) ? \Carbon\Carbon::parse($enrollment->date_of_leaving)->format('Y-m-d') : '') }}">
                                    </div>
                                    @error('date_of_leaving')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Status --}}
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="status" class="fw-semibold">Status:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-shield"></i></div>
                                        <select class="form-select @error('status') is-invalid @enderror"
                                                id="status" name="status" required>
                                            <option value="">Select Status</option>
                                            <option value="active"
                                                    {{ old('status', $enrollment->status ?? 'active') == 'active' ? 'selected' : '' }}>
                                                Active
                                            </option>
                                            <option value="promoted"
                                                    {{ old('status', $enrollment->status ?? 'active') == 'promoted' ? 'selected' : '' }}>
                                                Promoted
                                            </option>
                                            <option value="transferred"
                                                    {{ old('status', $enrollment->status ?? 'active') == 'transferred' ? 'selected' : '' }}>
                                                Transferred
                                            </option>
                                            <option value="completed"
                                                    {{ old('status', $enrollment->status ?? 'active') == 'completed' ? 'selected' : '' }}>
                                                Completed
                                            </option>
                                        </select>
                                    </div>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Residence Type --}}
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="residence_type" class="fw-semibold">Residence Type:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-home"></i></div>
                                        <select class="form-select @error('residence_type') is-invalid @enderror"
                                                id="residence_type" name="residence_type">
                                            <option value="">Select Residence Type</option>
                                            <option value="day_scholar"
                                                {{ old('residence_type', $enrollment->residence_type ?? '') == 'day_scholar' ? 'selected' : '' }}>
                                                Day Scholar
                                            </option>
                                            <option value="hosteller"
                                                {{ old('residence_type', $enrollment->residence_type ?? '') == 'hosteller' ? 'selected' : '' }}>
                                                Hosteller
                                            </option>
                                        </select>
                                    </div>
                                    @error('residence_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- 🏠 HOSTEL ALLOCATION SECTION (shows only when Hosteller selected) --}}
                            <div id="hostel-section" style="display: none;">
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="d-flex align-items-center gap-2 mb-3">
                                            <div class="flex-shrink-0 bg-success bg-opacity-10 rounded p-2">
                                                <i class="feather-home text-success fs-5"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-0">Hostel Allocation</h6>
                                                <small class="text-muted">Assign hostel, room and bed to the student</small>
                                            </div>
                                        </div>
                                        <hr class="mt-0">
                                    </div>
                                </div>

                                {{-- Hostel + Room Row --}}
                                <div class="row mb-4">
                                    <div class="col-lg-6 mb-3 mb-lg-0">
                                        <div class="row align-items-center">
                                            <div class="col-lg-4">
                                                <label for="hostel_id" class="fw-semibold">Hostel:</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="input-group">
                                                    <div class="input-group-text"><i class="feather-home"></i></div>
                                                    <select class="form-select" id="hostel_id" name="hostel_id">
                                                        <option value="">Select Hostel</option>
                                                        @foreach ($hostels ?? [] as $hostel)
                                                            <option value="{{ $hostel->id }}"
                                                                {{ old('hostel_id', $existingHostel->hostel_id ?? '') == $hostel->id ? 'selected' : '' }}>
                                                                {{ $hostel->name }} ({{ $hostel->type }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @error('hostel_id')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="row align-items-center">
                                            <div class="col-lg-4">
                                                <label class="fw-semibold">Room:</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div id="rooms-container">
                                                    @if(!empty($existingHostel) && $existingHostel->room)
                                                        <div class="input-group">
                                                            <div class="input-group-text"><i class="feather-grid"></i></div>
                                                            <select name="room_id" id="room_id" class="form-select" required>
                                                                <option value="{{ $existingHostel->room_id }}" selected>
                                                                    {{ $existingHostel->room->room_number }} ({{ $existingHostel->room->type }})
                                                                </option>
                                                            </select>
                                                        </div>
                                                    @else
                                                        <div class="input-group">
                                                            <div class="input-group-text"><i class="feather-grid"></i></div>
                                                            <select name="room_id" id="room_id" class="form-select" disabled>
                                                                <option>Select hostel first</option>
                                                            </select>
                                                        </div>
                                                    @endif
                                                </div>
                                                @error('room_id')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Bed + Hostel Fee Row --}}
                                <div class="row mb-4">
                                    <div class="col-lg-6 mb-3 mb-lg-0">
                                        <div class="row align-items-center">
                                            <div class="col-lg-4">
                                                <label class="fw-semibold">Bed:</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div id="beds-container">
                                                    @if(!empty($existingHostel) && $existingHostel->bed)
                                                        <div class="input-group">
                                                            <div class="input-group-text"><i class="feather-minus-square"></i></div>
                                                            <select name="bed_id" id="bed_id" class="form-select" required>
                                                                <option value="{{ $existingHostel->bed_id }}" selected
                                                                    data-amount="{{ $existingHostel->bed->amount }}">
                                                                    {{ $existingHostel->bed->bed_number }}
                                                                    (₹{{ number_format($existingHostel->bed->amount, 2) }})
                                                                </option>
                                                            </select>
                                                        </div>
                                                    @else
                                                        <div class="input-group">
                                                            <div class="input-group-text"><i class="feather-minus-square"></i></div>
                                                            <select name="bed_id" id="bed_id" class="form-select" disabled>
                                                                <option>Select room first</option>
                                                            </select>
                                                        </div>
                                                    @endif
                                                </div>
                                                @error('bed_id')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="row align-items-center">
                                            <div class="col-lg-4">
                                                <label for="hostel_fee" class="fw-semibold">Fee (Monthly):</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="input-group">
                                                    <div class="input-group-text"><i class="feather-dollar-sign"></i></div>
                                                    <input type="number" step="0.01" name="hostel_fee" id="hostel_fee"
                                                           class="form-control"
                                                           value="{{ old('hostel_fee', $existingHostel->bed->amount ?? '') }}"
                                                           min="0" placeholder="0.00">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- From Date + To Date Row --}}
                                <div class="row mb-4">
                                    <div class="col-lg-6 mb-3 mb-lg-0">
                                        <div class="row align-items-center">
                                            <div class="col-lg-4">
                                                <label class="fw-semibold">From Date:</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="input-group">
                                                    <div class="input-group-text"><i class="feather-calendar"></i></div>
                                                    <input type="date" name="hostel_from_date" class="form-control"
                                                           value="{{ old('hostel_from_date', $existingHostel->from_date ?? now()->format('Y-m-d')) }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row align-items-center">
                                            <div class="col-lg-4">
                                                <label class="fw-semibold">To Date:</label>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="input-group">
                                                    <div class="input-group-text"><i class="feather-calendar"></i></div>
                                                    <input type="date" name="hostel_to_date" class="form-control"
                                                           value="{{ old('hostel_to_date', $existingHostel->to_date ?? '') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Existing Allocation Info (Edit mode) --}}
                                @if (!empty($existingHostel))
                                    <div class="row mb-2">
                                        <div class="col-lg-8 offset-lg-4">
                                            <div class="p-3 rounded border border-success border-opacity-25 bg-success bg-opacity-10">
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <i class="feather-check-circle text-success"></i>
                                                    <span class="fw-semibold text-success">Current Allocation</span>
                                                </div>
                                                <div class="row text-center g-2">
                                                    <div class="col-3">
                                                        <small class="text-muted d-block">Hostel</small>
                                                        <span class="fw-semibold">{{ $existingHostel->hostel->name }}</span>
                                                    </div>
                                                    <div class="col-3">
                                                        <small class="text-muted d-block">Room</small>
                                                        <span class="fw-semibold">{{ $existingHostel->room->room_number }}</span>
                                                    </div>
                                                    <div class="col-3">
                                                        <small class="text-muted d-block">Bed</small>
                                                        <span class="fw-semibold">{{ $existingHostel->bed->bed_number }}</span>
                                                    </div>
                                                    <div class="col-3">
                                                        <small class="text-muted d-block">Monthly</small>
                                                        <span class="fw-semibold text-success">₹{{ number_format($existingHostel->bed->amount, 2) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            {{-- END HOSTEL SECTION --}}

                            {{-- 🔥 FEE STRUCTURES SECTION --}}
                            <div class="row mb-4 mt-2">
                                <div class="col-lg-4">
                                    <label class="fw-semibold">Fee Structures <span class="text-danger">*</span></label>
                                    <small class="d-block text-muted">
                                        @if(!empty($enrollment->id))
                                            Currently assigned: {{ count($existingFeeStructureIds ?? []) }} fee(s)
                                        @else
                                            Select multiple semesters
                                        @endif
                                    </small>
                                </div>
                                <div class="col-lg-8">
                                    <div id="fee-structures-container" class="fee-grid-container">
                                        <div class="alert alert-info text-center py-4">
                                            <i class="feather-book fs-2 mb-2 d-block"></i>
                                            <div>Select Academic Year and Branch above</div>
                                            <div class="small">to view available fee structures</div>
                                        </div>
                                    </div>
                                    @error('fee_structure_ids')
                                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- STYLES --}}
<style>
    .fee-card {
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    .fee-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1) !important;
    }
    .fee-card.border-primary {
        border-color: #0d6efd !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, .25) !important;
    }
    .spin {
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    #hostel-section {
        overflow: hidden;
        transition: all 0.35s ease;
    }
</style>

{{-- SCRIPTS --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {

    function loadFeeStructures() {
        const academicYearId = $('#academic_year_id').val();
        const streamId = $('#stream_id').val();
        const container = $('#fee-structures-container');

        if (academicYearId && streamId) {
            container.html('<div class="text-center p-4"><i class="feather-loader spin me-2"></i>Loading fee structures...</div>');
            $.get('{{ route("admin.student_class_enrollments.ajax_fee_structures") }}', {
                academic_year_id: academicYearId,
                stream_id: streamId,
                @if(!empty($enrollment->id))
                enrollment_id: {{ $enrollment->id }},
                student_id: {{ $enrollment->student_id ?? 'null' }},
                @endif
            }, function (response) {
                container.html(response.html);
            }).fail(function () {
                container.html('<div class="alert alert-danger">Error loading fee structures</div>');
            });
        } else {
            container.html('<div class="alert alert-info"><i class="feather-info me-2"></i>Select Academic Year and Branch first</div>');
        }
    }

    $('#academic_year_id, #stream_id').change(loadFeeStructures);

    // Student Type → Auto-select Semester
    // Regular = 1st Semester (id=1), Lateral = 3rd Semester (id=3)
   $('#student_type').change(function () {
    const studentType = $(this).val();
    if (studentType === 'lateral') {
        $('#class_id option[value="1"]').hide();//hide
        $('#class_id option[value="2"]').hide();//hide
        $('#class_id').val(3);
    } else if (studentType === 'regular') {
        $('#class_id option').show();
        $('#class_id').val(1);
    } else {
        $('#class_id option').show();
        $('#class_id').val('');
    }
});

    @if (isset($enrollment) && $enrollment->academic_year_id && $enrollment->stream_id)
        loadFeeStructures();
    @endif

    function toggleHostelSection(val, animate) {
        if (val === 'hosteller') {
            animate ? $('#hostel-section').slideDown(350) : $('#hostel-section').show();
            $('#hostel_id').attr('required', true);
        } else {
            animate ? $('#hostel-section').slideUp(350) : $('#hostel-section').hide();
            $('#hostel_id').removeAttr('required');
        }
    }

    toggleHostelSection($('#residence_type').val(), false);
    $('#residence_type').change(function () {
        toggleHostelSection($(this).val(), true);
    });

    $('#hostel_id').change(function () {
        const hostelId = $(this).val();
        const roomsContainer = $('#rooms-container');
        if (hostelId) {
            roomsContainer.html('<div class="input-group"><div class="input-group-text"><i class="feather-loader spin"></i></div><select class="form-select" disabled><option>Loading rooms...</option></select></div>');
            $.get(`/admin/hostels/${hostelId}/rooms`, function (rooms) {
                let options = '<option value="">Select Room</option>';
                rooms.forEach(function (room) {
                    options += `<option value="${room.id}">${room.room_number} (${room.type}, ${room.max_beds} beds)</option>`;
                });
                roomsContainer.html(`<div class="input-group"><div class="input-group-text"><i class="feather-grid"></i></div><select id="room_id" name="room_id" class="form-select" required>${options}</select></div>`);
            }).fail(function () {
                roomsContainer.html('<div class="input-group"><div class="input-group-text"><i class="feather-grid"></i></div><select class="form-select" disabled><option>Error loading rooms</option></select></div>');
            });
        } else {
            roomsContainer.html('<div class="input-group"><div class="input-group-text"><i class="feather-grid"></i></div><select name="room_id" class="form-select" disabled><option>Select hostel first</option></select></div>');
        }
    });

    $(document).on('change', '#room_id', function () {
        const roomId = $(this).val();
        const bedsContainer = $('#beds-container');
        if (roomId) {
            bedsContainer.html('<div class="input-group"><div class="input-group-text"><i class="feather-loader spin"></i></div><select class="form-select" disabled><option>Loading beds...</option></select></div>');
            $.get(`/admin/rooms/${roomId}/beds`, function (data) {
                bedsContainer.html(data);
            }).fail(function () {
                bedsContainer.html('<div class="input-group"><div class="input-group-text"><i class="feather-minus-square"></i></div><select class="form-select" disabled><option>Error loading beds</option></select></div>');
            });
        } else {
            bedsContainer.html('<div class="input-group"><div class="input-group-text"><i class="feather-minus-square"></i></div><select name="bed_id" class="form-select" disabled><option>Select room first</option></select></div>');
        }
    });

    $(document).on('change', '#bed_id', function () {
        const amount = $(this).find('option:selected').data('amount');
        $('#hostel_fee').val(amount !== undefined ? amount : '');
    });

});
</script>
@endsection
