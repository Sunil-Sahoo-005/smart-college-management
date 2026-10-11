{{-- 🔥 HOSTEL TAB --}}
<div class="tab-pane fade" id="hostelTab">
    <div class="card stretch stretch-full">
        <div class="card-header">
            <h6 class="card-title mb-0">
                <i class="feather-home me-2 text-success"></i>Hostel Allocation
            </h6>
        </div>
        <div class="card-body">
            <form id="hostelForm" method="POST" action="{{ route('admin.student.hostel.store', $student->id) }}">
                @csrf
                <input type="hidden" name="student_id" value="{{ $student->id }}">

                @if ($existingHostel)
                    <input type="hidden" name="allocation_id" value="{{ $existingHostel->id }}">
                @endif

                <div class="row mb-4">
                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">Hostel <span class="text-danger">*</span></label>
                        <select class="form-select @error('hostel_id') is-invalid @enderror" id="hostel_id" name="hostel_id" required>
                            <option value="">Select Hostel</option>
                            @foreach ($hostels as $hostel)
                                <option value="{{ $hostel->id }}"
                                    {{ old('hostel_id', $existingHostel->hostel_id ?? '') == $hostel->id ? 'selected' : '' }}>
                                    {{ $hostel->name }} ({{ $hostel->type }})
                                </option>
                            @endforeach
                        </select>
                        @error('hostel_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">Room <span class="text-danger">*</span></label>
                        <div id="rooms-container">
                            @if($existingHostel && $existingHostel->room)
                                <select name="room_id" id="room_id" class="form-select" required>
                                    <option value="{{ $existingHostel->room_id }}" selected>
                                        {{ $existingHostel->room->room_number ?? 'N/A' }} 
                                        ({{ $existingHostel->room->type ?? 'N/A' }})
                                    </option>
                                </select>
                            @else
                                <select name="room_id" id="room_id" class="form-select" disabled>
                                    <option>Select hostel first</option>
                                </select>
                            @endif
                        </div>
                        @error('room_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">Bed <span class="text-danger">*</span></label>
                        <div id="beds-container">
                            @if($existingHostel && $existingHostel->bed)
                                <select name="bed_id" id="bed_id" class="form-select" required>
                                    <option value="{{ $existingHostel->bed_id }}" selected data-amount="{{ $existingHostel->bed->amount ?? 0 }}">
                                        {{ $existingHostel->bed->bed_number ?? 'N/A' }} 
                                        (₹{{ number_format($existingHostel->bed->amount ?? 0, 2) }})
                                    </option>
                                </select>
                            @else
                                <select name="bed_id" id="bed_id" class="form-select" disabled>
                                    <option>Select room first</option>
                                </select>
                            @endif
                        </div>
                        @error('bed_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">Hostel Fee (Monthly)</label>
                        <input type="number" step="0.01" name="hostel_fee" id="hostel_fee" 
                               class="form-control" value="{{ old('hostel_fee', $existingHostel->bed->amount ?? '') }}" min="0">
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">From Date <span class="text-danger">*</span></label>
                        <input type="date" name="from_date" class="form-control"
                               value="{{ old('from_date', $existingHostel->from_date ?? now()->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label fw-semibold">To Date</label>
                        <input type="date" name="to_date" class="form-control"
                               value="{{ old('to_date', $existingHostel->to_date ?? '') }}" min="{{ old('from_date', now()->format('Y-m-d')) }}">
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        {{ $existingHostel ? 'Update' : 'Allocate' }} Hostel
                    </button>
                    @if ($existingHostel)
                        <button type="button" class="btn btn-outline-danger" onclick="removeHostel({{ $student->id }})">
                            Remove Allocation
                        </button>
                    @endif
                </div>
            </form>

            {{-- 🔥 CURRENT ALLOCATION DISPLAY WITH NULL CHECKS --}}
            @if ($existingHostel)
                <div class="mt-4 p-4 border rounded bg-light">
                    <h6 class="mb-3">
                        <i class="feather-check-circle text-success me-2"></i>
                        Current Hostel Allocation
                    </h6>
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="p-3">
                                <i class="feather-home fs-2 text-success mb-2 d-block"></i>
                                <h6>{{ $existingHostel->hostel->name ?? 'N/A' }}</h6>
                                <small class="text-muted">{{ $existingHostel->hostel->type ?? 'N/A' }}</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <i class="feather-grid fs-2 text-primary mb-2 d-block"></i>
                                <h6>{{ optional($existingHostel->room)->room_number ?? 'N/A' }}</h6>
                                <small class="text-muted">{{ optional($existingHostel->room)->type ?? 'N/A' }}</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <i class="feather-bed fs-2 text-info mb-2 d-block"></i>
                                <h6>{{ optional($existingHostel->bed)->bed_number ?? 'N/A' }}</h6>
                                <small class="text-muted">{{ optional($existingHostel->bed)->status ?? 'N/A' }}</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <i class="feather-dollar-sign fs-2 text-warning mb-2 d-block"></i>
                                <h6>₹{{ number_format(optional($existingHostel->bed)->amount ?? 0, 2) }}</h6>
                                <small class="text-muted">Monthly</small>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        // Hostel selection change
        $('#hostel_id').change(function() {
            const hostelId = $(this).val();
            const roomsContainer = $('#rooms-container');

            if (hostelId) {
                // Show loading
                roomsContainer.html(
                    '<div class="text-center p-3"><i class="feather-loader spinner"></i> Loading rooms...</div>'
                );

                // AJAX call
                $.get(`/admin/hostels/${hostelId}/rooms`, function(rooms) {
                    let options = '<option value="">Select Room</option>';
                    rooms.forEach(function(room) {
                        options +=
                            `<option value="${room.id}">${room.room_number} (${room.type}, ${room.max_beds} beds)</option>`;
                    });
                    roomsContainer.html(
                        `<select id="room_id" name="room_id" class="form-select" required>${options}</select>`
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
                // Reset beds container
                $('#beds-container').html(
                    '<select name="bed_id" class="form-select" disabled><option>Select room first</option></select>'
                );
            }
        });

        // Room selection change
        $(document).on('change', '#room_id', function() {
            const roomId = $(this).val();
            const bedsContainer = $('#beds-container');
            
            if (roomId) {
                console.log('Selected Room ID:', roomId);
                bedsContainer.html(
                    '<div class="text-center p-3"><i class="feather-loader spinner"></i> Loading beds...</div>'
                );
                
                $.get(`/admin/rooms/${roomId}/beds`, function(data) {
                    bedsContainer.html(data);
                }).fail(function() {
                    bedsContainer.html(
                        '<select class="form-select" disabled><option>Error loading beds</option></select>'
                    );
                });
            } else {
                bedsContainer.html(
                    '<select name="bed_id" class="form-select" disabled><option>Select room first</option></select>'
                );
            }
        });

        // Auto-fill hostel fee when bed is selected
        $(document).on('change', '#bed_id', function() {
            const amount = $(this).find('option:selected').data('amount');
            if (amount !== undefined && amount !== null) {
                $('input[name="hostel_fee"]').val(amount);
            } else {
                $('input[name="hostel_fee"]').val('');
            }
        });
    });

    // Remove hostel allocation function
    function removeHostel(studentId) {
        if (confirm('Are you sure you want to remove this hostel allocation?')) {
            $.ajax({
                url: `/admin/students/${studentId}/hostel/remove`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('An error occurred while removing the allocation.');
                }
            });
        }
    }
</script>

<style>
    .spinner {
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>