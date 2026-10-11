@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">

            {{-- SIDEBAR --}}
            @include('hostel.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">

                <div class="content-area-header bg-white sticky-top border-bottom d-flex align-items-center px-4 py-3">
                    <h6 class="mb-0 fw-semibold">New Bed</h6>

                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                        </div>
                    </div>
                </div>

                <div class="content-area-body p-4">
                    @include('elements.flash')

                    <div class="card stretch stretch-full shadow-sm border-0">
                        <div class="card-body px-4 py-4">
                            <form id="bedForm" method="POST" action="{{ route('admin.hostelbed.store') }}">
                                @csrf

                                @if (isset($hostelbed))
                                    <input type="hidden" name="id" value="{{ $hostelbed->id }}">
                                @endif

                                <div class="row g-4">
                                    {{-- 🔥 HOSTEL SELECT --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Hostel <span
                                                class="text-danger">*</span></label>
                                        <select name="hostel_id" id="hostel_id"
                                            class="form-select @error('hostel_id') is-invalid @enderror" required>
                                            <option value="">Select Hostel</option>
                                            @foreach ($hostels as $hostel)
                                                <option value="{{ $hostel->id }}"
                                                    {{ old('hostel_id', $hostelbed->room->hostel_id ?? '') == $hostel->id ? 'selected' : '' }}>
                                                    {{ $hostel->name }} ({{ $hostel->type }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('hostel_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- ROOM SELECT --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Room <span
                                                class="text-danger">*</span></label>
                                        <div id="rooms-container">
                                            @if (isset($hostelbed) && $hostelbed->room)
                                                <select name="room_id" class="form-select" required>
                                                    <option value="{{ $hostelbed->room_id }}" selected>
                                                        {{ $hostelbed->room->room_number }} ({{ $hostelbed->room->type }})
                                                    </option>
                                                </select>
                                            @else
                                                <select name="room_id" class="form-select" disabled>
                                                    <option>Select hostel first</option>
                                                </select>
                                            @endif
                                        </div>
                                        @error('room_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- BED NUMBER --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Bed Number</label>
                                        <input type="text" name="bed_number" class="form-control"
                                            value="{{ old('bed_number', $hostelbed->bed_number ?? '') }}" required>
                                    </div>

                                    {{-- STATUS --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Status</label>
                                        <select name="status" class="form-select" required>
                                            <option value="vacant"
                                                {{ old('status', $hostelbed->status ?? 'vacant') == 'vacant' ? 'selected' : '' }}>
                                                Vacant
                                            </option>
                                            <option value="occupied"
                                                {{ old('status', $hostelbed->status ?? '') == 'occupied' ? 'selected' : '' }}>
                                                Occupied
                                            </option>
                                        </select>
                                    </div>

                                    {{-- AMOUNT --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Monthly Amount</label>
                                        <input type="number" step="0.01" name="amount" class="form-control"
                                            value="{{ old('amount', $hostelbed->amount ?? '') }}" required>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 mt-4">
                                    <a href="{{ route('admin.hostelbed.index') }}" class="btn btn-outline-secondary">
                                        <i class="feather-arrow-left me-1"></i>Back
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i
                                            class="feather-save me-1"></i>{{ isset($hostelbed) ? 'Update Bed' : 'Create Bed' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    {{-- JavaScript for Dynamic Rooms --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#hostel_id').change(function() {
                const hostelId = $(this).val();
                const roomsContainer = $('#rooms-container');

                if (hostelId) {
                    // Show loading
                    roomsContainer.html(
                        '<div class="text-center p-3"><i class="feather-loader spinner"></i> Loading rooms...</div>'
                    );

                    // AJAX call
                    // $.get(`/admin/hostels/${hostelId}/rooms`, function(rooms) {
                    $.get('{{ route("admin.hostels.rooms", ":id") }}'.replace(':id', hostelId), function(rooms) {
                        let options = '<option value="">Select Room</option>';
                        rooms.forEach(function(room) {
                            options +=
                                `<option value="${room.id}">${room.room_number} (${room.type}, ${room.max_beds} beds)</option>`;
                        });
                        roomsContainer.html(
                            `<select name="room_id" class="form-select" required>${options}</select>`
                        );
                    }).fail(function() {
                        roomsContainer.html(
                            '<select class="form-select" disabled><option>Error loading rooms</option></select>'
                        );
                    });
                } else {
                    roomsContainer.html(
                        '<select name="room_id" class="form-select" disabled><option>Select hostel first</option></select>'
                    );
                }
            });
        });
    </script>
@endsection
