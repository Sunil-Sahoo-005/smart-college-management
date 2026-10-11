@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('hostel.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">
                            {{ !empty($allocation) ? 'Edit' : 'New' }} Hostel Allocation
                        </span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3">
                            <a href="{{ route('admin.hostelallocation.index') }}" class="btn btn-secondary">
                                <i class="feather-arrow-left me-2"></i>Back to List
                            </a>
                        </div>
                    </div>
                </div>

                <div class="content-area-body">
                    @include('elements.flash')

                    @if ($errors->any())
                        <div class="alert alert-danger mb-4">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif


                    <div class="card stretch stretch-full shadow-sm border-0">
                        <div class="card-body p-4">
                            <form method="POST" action="{{ route('admin.hostelallocation.store') }}" id="allocationForm">
                                @csrf
                                @if (!empty($allocation))
                                    <input type="hidden" name="id" value="{{ $allocation->id }}">
                                @endif

                                <div class="row g-4">

                                    {{-- STUDENT --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Student <span class="text-danger">*</span></label>
                                        <select name="student_id" id="studentSelect" class="form-select" required>
                                            <option value="">Select Student</option>
                                            @foreach ($students as $student)
                                                <option value="{{ $student->id }}"
                                                    {{ old('student_id', $allocation->student_id ?? '') == $student->id ? 'selected' : '' }}>
                                                    {{ $student->first_name }} {{ $student->last_name }}
                                                    ({{ $student->admission_number ?? 'N/A' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- HOSTEL --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Hostel <span class="text-danger">*</span></label>
                                        <select name="hostel_id" id="hostelSelect" class="form-select" required>
                                            <option value="">Select Hostel</option>
                                            @foreach ($hostels ?? [] as $hostel)
                                                <option value="{{ $hostel->id }}"
                                                    {{ old('hostel_id', $allocation->room->hostel_id ?? '') == $hostel->id ? 'selected' : '' }}>
                                                    {{ $hostel->name }} ({{ $hostel->type }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- ROOM --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Room <span class="text-danger">*</span></label>
                                        <select name="room_id" id="roomSelect" class="form-select" required disabled>
                                            <option value="">Select Hostel First</option>
                                            @if (!empty($allocation))
                                                <option value="{{ $allocation->room_id }}" selected>
                                                    {{ $allocation->room->room_number }} ({{ $allocation->room->type }})
                                                </option>
                                            @endif
                                        </select>
                                    </div>

                                    {{-- BED --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Bed <span class="text-danger">*</span></label>
                                        <select name="bed_id" id="bedSelect" class="form-select" required disabled>
                                            <option value="">Select Room First</option>
                                            @if (!empty($allocation))
                                                <option value="{{ $allocation->bed_id }}" selected>
                                                    {{ $allocation->bed->bed_number }}
                                                    (₹{{ number_format($allocation->bed->amount, 2) }})
                                                </option>
                                            @endif
                                        </select>
                                    </div>

                                    {{-- FROM DATE --}}
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">From Date <span class="text-danger">*</span></label>
                                        <input type="date" name="from_date" id="fromDate" class="form-control"
                                            value="{{ old('from_date', $allocation->from_date ?? '') }}" required>
                                    </div>

                                    {{-- TO DATE --}}
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">To Date</label>
                                        <input type="date" name="to_date" id="toDate" class="form-control"
                                            value="{{ old('to_date', $allocation->to_date ?? '') }}">
                                    </div>
                                </div>

                                <div class="d-flex gap-3 mt-5">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="feather-save me-2"></i>
                                        {{ !empty($allocation) ? 'Update Allocation' : 'Create Allocation' }}
                                    </button>
                                    <a href="{{ route('admin.hostelallocation.index') }}"
                                        class="btn btn-outline-secondary px-4">
                                        <i class="feather-x me-2"></i>Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const studentSelect = document.getElementById('studentSelect');
            const hostelSelect  = document.getElementById('hostelSelect');
            const roomSelect    = document.getElementById('roomSelect');
            const bedSelect     = document.getElementById('bedSelect');
            const fromDate      = document.getElementById('fromDate');
            const toDate        = document.getElementById('toDate');

            const isEditMode    = {{ !empty($allocation) ? 'true' : 'false' }};

            // ─────────────────────────────────────────
            // STUDENT CHANGE → fetch existing allocation
            // ─────────────────────────────────────────
            studentSelect.addEventListener('change', function () {
                const studentId = this.value;

                // Edit mode mein student change hone par banner hide karo

                if (!studentId) {
                    resetAllFields();
                    return;
                }

                // Sirf create mode mein existing allocation check karo
                if (!isEditMode) {
                    fetch(`{{ url('admin/hostelallocation/student') }}/${studentId}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.found) {
                                // Banner dikhao

                                // Hostel set karo aur rooms load karo
                                hostelSelect.value = data.hostel_id;
                                loadRooms(data.room_id, data.bed_id);

                                // Dates set karo
                                fromDate.value = data.from_date ?? '';
                                toDate.value   = data.to_date ?? '';
                            } else {
                                // Koi allocation nahi — fields clear karo
                                resetAllFields();
                            }
                        })
                        .catch(() => resetAllFields());
                }
            });

            // ─────────────────────────────────────────
            // HOSTEL CHANGE → rooms load karo
            // ─────────────────────────────────────────
            hostelSelect.addEventListener('change', function () {
                loadRooms(null, null);
            });

            // ─────────────────────────────────────────
            // ROOM CHANGE → beds load karo
            // ─────────────────────────────────────────
            roomSelect.addEventListener('change', function () {
                loadBeds(null);
            });

            // ─────────────────────────────────────────
            // HELPER: Rooms load karo (optionally auto-select)
            // ─────────────────────────────────────────
            function loadRooms(autoSelectRoomId = null, autoSelectBedId = null) {
                const hostelId = hostelSelect.value;

                if (!hostelId) {
                    roomSelect.innerHTML = '<option value="">Select Hostel First</option>';
                    roomSelect.disabled  = true;
                    bedSelect.innerHTML  = '<option value="">Select Room First</option>';
                    bedSelect.disabled   = true;
                    return;
                }

                roomSelect.innerHTML = '<option>Loading rooms...</option>';
                roomSelect.disabled  = true;
                bedSelect.innerHTML  = '<option value="">Select Room First</option>';
                bedSelect.disabled   = true;

                fetch(`{{ route('admin.hostelallocation.rooms', ':hostel') }}`.replace(':hostel', hostelId))
                    .then(res => res.json())
                    .then(rooms => {
                        roomSelect.innerHTML = '<option value="">Select Room</option>';
                        rooms.forEach(room => {
                            const opt = new Option(
                                `${room.room_number} (${room.type}) - ${room.max_beds} beds`,
                                room.id
                            );
                            roomSelect.appendChild(opt);
                        });
                        roomSelect.disabled = false;

                        // Auto-select room if provided
                        if (autoSelectRoomId && roomSelect.querySelector(`[value="${autoSelectRoomId}"]`)) {
                            roomSelect.value = autoSelectRoomId;
                            loadBeds(autoSelectBedId);
                        }

                        // Edit mode mein room auto-select
                        @if (!empty($allocation))
                            if (!autoSelectRoomId) {
                                const editRoomId = '{{ $allocation->room_id }}';
                                if (editRoomId && roomSelect.querySelector(`[value="${editRoomId}"]`)) {
                                    roomSelect.value = editRoomId;
                                    loadBeds('{{ $allocation->bed_id }}');
                                }
                            }
                        @endif
                    })
                    .catch(() => {
                        roomSelect.innerHTML = '<option value="">Error loading rooms</option>';
                    });
            }

            // ─────────────────────────────────────────
            // HELPER: Beds load karo (optionally auto-select)
            // ─────────────────────────────────────────
            function loadBeds(autoSelectBedId = null) {
                const roomId = roomSelect.value;

                if (!roomId) {
                    bedSelect.innerHTML = '<option value="">Select Room First</option>';
                    bedSelect.disabled  = true;
                    return;
                }

                bedSelect.innerHTML = '<option>Loading beds...</option>';
                bedSelect.disabled  = true;

                fetch(`{{ route('admin.hostelallocation.beds', ':room') }}`.replace(':room', roomId))
                    .then(res => res.json())
                    .then(beds => {
                        bedSelect.innerHTML = '<option value="">Select Bed</option>';
                        beds.forEach(bed => {
                            const opt = new Option(
                                `${bed.bed_number} (₹${parseFloat(bed.amount).toLocaleString('en-IN', { minimumFractionDigits: 2 })}) - ${bed.status}`,
                                bed.id
                            );
                            bedSelect.appendChild(opt);
                        });
                        bedSelect.disabled = false;

                        // Auto-select bed if provided
                        if (autoSelectBedId && bedSelect.querySelector(`[value="${autoSelectBedId}"]`)) {
                            bedSelect.value = autoSelectBedId;
                        }

                        // Edit mode mein bed auto-select
                        @if (!empty($allocation))
                            if (!autoSelectBedId) {
                                const editBedId = '{{ $allocation->bed_id }}';
                                if (editBedId && bedSelect.querySelector(`[value="${editBedId}"]`)) {
                                    bedSelect.value = editBedId;
                                }
                            }
                        @endif
                    })
                    .catch(() => {
                        bedSelect.innerHTML = '<option value="">Error loading beds</option>';
                    });
            }

            // ─────────────────────────────────────────
            // HELPER: Sabhi fields reset karo
            // ─────────────────────────────────────────
            function resetAllFields() {
                hostelSelect.value   = '';
                roomSelect.innerHTML = '<option value="">Select Hostel First</option>';
                roomSelect.disabled  = true;
                bedSelect.innerHTML  = '<option value="">Select Room First</option>';
                bedSelect.disabled   = true;
                fromDate.value       = '';
                toDate.value         = '';
            }

            // ─────────────────────────────────────────
            // Edit mode mein page load par auto-cascade
            // ─────────────────────────────────────────
            @if (!empty($allocation) && isset($allocation->room->hostel_id))
                hostelSelect.value = '{{ $allocation->room->hostel_id }}';
                setTimeout(() => loadRooms(null, null), 100);
            @endif
        });
    </script>
@endsection