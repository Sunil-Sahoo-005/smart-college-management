@extends('admin_layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">

        {{-- SIDEBAR --}}
        @include('hostel.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            {{-- TOP HEADER --}}
            <div class="content-area-header bg-white sticky-top border-bottom d-flex align-items-center">
                <h6 class="card-title mb-0">
                    {{ !empty($hostelroom) ? 'Edit Room' : 'Create New Room' }}
                </h6>

                <div class="page-header-right ms-auto">
                    <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper"></div>
                </div>
            </div>

            {{-- BODY --}}
            <div class="content-area-body p-4">
                @include('elements.flash')

                <div class="card stretch stretch-full shadow-sm border-0">

                    <div class="card-header bg-white border-bottom px-4 py-3">
                        {{-- Step Indicators --}}
                        <ul class="nav nav-pills nav-justified">
                            <li class="nav-item">
                                <a class="nav-link active" id="step1-indicator" href="javascript:void(0)" onclick="goToStep(1)">
                                    <i class="feather-info me-1"></i> Step 1: Room Details
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="step2-indicator" href="javascript:void(0)" onclick="goToStep(2)">
                                    <i class="feather-bed me-1"></i> Step 2: Bed Configuration
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body px-4 py-4">

                        <form id="roomForm"
                              action="{{ !empty($hostelroom) 
                                  ? route('admin.hostelroom.update', $hostelroom->id) 
                                  : route('admin.hostelroom.store') }}"
                              method="POST">
                            @csrf

                            @if (!empty($hostelroom))
                                @method('PUT')
                                <input type="hidden" name="id" value="{{ $hostelroom->id }}">
                            @endif

                            {{-- STEP 1: ROOM DETAILS --}}
                            <div id="step-1" class="wizard-step">
                                <div class="row g-4">
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <i class="feather-info me-2"></i> 
                                            Please fill the basic room details first. After completing this step, you'll configure bed-wise details.
                                        </div>
                                    </div>

                                    {{-- ROOM NUMBER --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Room Number <span class="text-danger">*</span></label>
                                        <input type="text"
                                               name="room_number"
                                               id="room_number"
                                               class="form-control @error('room_number') is-invalid @enderror"
                                               value="{{ old('room_number', $hostelroom->room_number ?? '') }}"
                                               placeholder="e.g., 101, A-101, Room 1"
                                               required autocomplete="off">
                                        @error('room_number')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- HOSTEL (with type information) --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Select Hostel <span class="text-danger">*</span></label>
                                        <select name="hostel_id" id="hostel_id" class="form-select @error('hostel_id') is-invalid @enderror" required autocomplete="off">
                                            <option value="">Select Hostel</option>
                                            @foreach ($hostels as $hostel)
                                                <option value="{{ $hostel->id }}"
                                                    {{ old('hostel_id', $hostelroom->hostel_id ?? '') == $hostel->id ? 'selected' : '' }}>
                                                    {{ $hostel->name }} 
                                                    @if($hostel->type == 'boys')
                                                        (Boys Hostel)
                                                    @elseif($hostel->type == 'girls')
                                                        (Girls Hostel)
                                                    @elseif($hostel->type == 'mixed')
                                                        (Mixed Hostel)
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Hostel type (Boys/Girls/Mixed) is shown in brackets</small>
                                        @error('hostel_id')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- ROOM TYPE (AC/Non-AC) --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Room Type <span class="text-danger">*</span></label>
                                        <select name="type" id="room_type" class="form-select @error('type') is-invalid @enderror" required>
                                            <option value="">Select Room Type</option>
                                            <option value="ac"
                                                {{ old('type', $hostelroom->type ?? '') == 'ac' ? 'selected' : '' }}>
                                                AC 
                                            </option>
                                            <option value="non_ac"
                                                {{ old('type', $hostelroom->type ?? '') == 'non_ac' ? 'selected' : '' }}>
                                                Non-AC 
                                            </option>
                                        </select>
                                        @error('type')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- MAX BEDS --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Maximum Beds <span class="text-danger">*</span></label>
                                        <input type="number"
                                               name="max_beds"
                                               id="max_beds"
                                               class="form-control @error('max_beds') is-invalid @enderror"
                                               min="1"
                                               max="20"
                                               value="{{ old('max_beds', $hostelroom->max_beds ?? '') }}"
                                               required>
                                        <small class="text-muted">Number of beds available in this room (1-20)</small>
                                        @error('max_beds')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 mt-4">
                                        <div class="d-flex justify-content-end">
                                            <button type="button" class="btn btn-primary" onclick="validateAndGoToStep2()">
                                                Next: Configure Beds <i class="feather-arrow-right ms-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- STEP 2: BED CONFIGURATION --}}
                            <div id="step-2" class="wizard-step" style="display:none;">
                                <div class="row g-4">
                                    <div class="col-12">
                                        <div class="alert alert-success">
                                            <i class="feather-check-circle me-2"></i> 
                                            <strong>Step 1 completed!</strong> Now configure bed-wise details for this room.
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="card border">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">
                                                    <i class="feather-bed me-2"></i>
                                                    Bed Configuration for Room <span id="display_room_number" class="fw-bold"></span>
                                                </h6>
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-hover mb-0">
                                                        <thead class="table-light">
                                                                <tr>
                                                                    <th width="5%">#</th>
                                                                    <th width="25%">Bed No.</th>
                                                                    <th width="35%">Amount per Year (₹)</th>
                                                                    <th width="35%">Status</th>
                                                                </tr>
                                                        </thead>
                                                        <tbody id="bedsTableBody">
                                                            <!-- Dynamic rows will be added here -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-3">
                                        <div class="d-flex gap-2 justify-content-between">
                                            <button type="button" class="btn btn-outline-secondary" onclick="goToStep(1)">
                                                <i class="feather-arrow-left me-2"></i> Previous: Room Details
                                            </button>
                                            <div>
                                                <button type="button" class="btn btn-primary" onclick="validateBedsAndSubmit()">
                                                    <i class="feather-save me-2"></i>
                                                    {{ !empty($hostelroom) ? 'Update Room & Beds' : 'Save Room & Beds' }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    let bedRows = [];

    // Function to navigate between steps
    function goToStep(step) {
        // Hide all steps
        document.querySelectorAll('.wizard-step').forEach(stepDiv => {
            stepDiv.style.display = 'none';
        });
        
        // Show the selected step
        document.getElementById('step-' + step).style.display = 'block';
        
        // Update step indicators
        for(let i = 1; i <= 2; i++) {
            const indicator = document.getElementById('step' + i + '-indicator');
            if(indicator) {
                if(i === step) {
                    indicator.classList.add('active');
                } else {
                    indicator.classList.remove('active');
                }
            }
        }
        
        // If going to step 2, generate bed rows
        if(step === 2) {
            generateBedRows();
        }
    }
    
    // Validate Step 1 and go to Step 2
    function validateAndGoToStep2() {
        const roomNumber = document.getElementById('room_number').value.trim();
        const hostelId = document.getElementById('hostel_id').value;
        const roomType = document.getElementById('room_type').value;
        const maxBeds = document.getElementById('max_beds').value;
        
        let isValid = true;
        
        if(!roomNumber) {
            document.getElementById('room_number').classList.add('is-invalid');
            showError('room_number', 'Room number is required');
            isValid = false;
        } else {
            document.getElementById('room_number').classList.remove('is-invalid');
            clearError('room_number');
        }
        
        if(!hostelId) {
            document.getElementById('hostel_id').classList.add('is-invalid');
            showError('hostel_id', 'Please select a hostel');
            isValid = false;
        } else {
            document.getElementById('hostel_id').classList.remove('is-invalid');
            clearError('hostel_id');
        }
        
        if(!roomType) {
            document.getElementById('room_type').classList.add('is-invalid');
            showError('room_type', 'Please select room type');
            isValid = false;
        } else {
            document.getElementById('room_type').classList.remove('is-invalid');
            clearError('room_type');
        }
        
        if(!maxBeds || maxBeds < 1) {
            document.getElementById('max_beds').classList.add('is-invalid');
            showError('max_beds', 'Maximum beds must be at least 1');
            isValid = false;
        } else if(maxBeds > 20) {
            document.getElementById('max_beds').classList.add('is-invalid');
            showError('max_beds', 'Maximum beds cannot exceed 20');
            isValid = false;
        } else {
            document.getElementById('max_beds').classList.remove('is-invalid');
            clearError('max_beds');
        }
        
        if(isValid) {
            // Display room number in step 2
            document.getElementById('display_room_number').textContent = roomNumber;
            goToStep(2);
        }
    }
    
    // Show error message
    function showError(fieldId, message) {
        const field = document.getElementById(fieldId);
        let errorDiv = field.parentElement.querySelector('.text-danger');
        if(!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'text-danger small mt-1';
            field.parentElement.appendChild(errorDiv);
        }
        errorDiv.textContent = message;
    }
    
    // Clear error message
    function clearError(fieldId) {
        const field = document.getElementById(fieldId);
        const errorDiv = field.parentElement.querySelector('.text-danger');
        if(errorDiv) {
            errorDiv.remove();
        }
    }
    
    // Generate bed rows based on max beds
    function generateBedRows() {
        const maxBeds = parseInt(document.getElementById('max_beds').value) || 1;
        const tbody = document.getElementById('bedsTableBody');
        tbody.innerHTML = '';
        bedRows = [];
        
        // Get existing beds data for edit mode
        let existingBeds = [];
        @if(!empty($hostelroom) && isset($hostelroom->beds))
            existingBeds = @json($hostelroom->beds);
        @endif
        
        for(let i = 1; i <= maxBeds; i++) {
            let existingBed = null;
            
            // Find existing bed data for this bed number
            if(existingBeds.length > 0) {
                existingBed = existingBeds.find(bed => {
                    return bed.bed_number == i;
                });
            }
            
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="text-center fw-bold">
                    ${i}
                    ${existingBed && existingBed.id ? `<input type="hidden" name="beds[${i-1}][id]" value="${existingBed.id}">` : ''}
                    <input type="hidden" name="beds[${i-1}][bed_number]" value="${i}">
                  </td>
                  <td>
                    <input type="text" 
                           name="beds[${i-1}][bed_number]" 
                           class="form-control" 
                           value="${existingBed ? existingBed.bed_number : i}"
                           readonly 
                           style="background-color: #f8f9fa;">
                  </td>
                  <td>
                    <input type="number" 
                           name="beds[${i-1}][amount]" 
                           class="form-control" 
                           step="0.01" 
                           min="0" 
                           value="${existingBed ? existingBed.amount : ''}"
                           placeholder="Enter amount per year"
                           required>
                  </td>
                  <td>
                    <select name="beds[${i-1}][status]" class="form-select status-field" required onchange="updateStatusCount()">
                        <option value="">Select Status</option>
                        <option value="vacant" ${existingBed && existingBed.status === 'vacant' ? 'selected' : ''}>Vacant</option>
                        <option value="occupied" ${existingBed && existingBed.status === 'occupied' ? 'selected' : ''}>Occupied</option>
                        <option value="maintenance" ${existingBed && existingBed.status === 'maintenance' ? 'selected' : ''}>Maintenance</option>
                    </select>
                  </td>
            `;
            tbody.appendChild(tr);
            bedRows.push(tr);
        }
        
        updateStatusCount();
    }
    
    // Update status count display
    function updateStatusCount() {
        const statusFields = document.querySelectorAll('.status-field');
        let vacant = 0, occupied = 0, maintenance = 0;
        
        statusFields.forEach(field => {
            const status = field.value;
            if(status === 'vacant') vacant++;
            else if(status === 'occupied') occupied++;
            else if(status === 'maintenance') maintenance++;
        });
        
        let statusSummary = document.getElementById('statusSummary');
        if(!statusSummary) {
            const summaryDiv = document.createElement('div');
            summaryDiv.id = 'statusSummary';
            summaryDiv.className = 'alert alert-info mt-3';
            document.querySelector('#step-2 .row.g-4').insertBefore(summaryDiv, document.querySelector('#step-2 .col-12.mt-3'));
            statusSummary = summaryDiv;
        }
        
        statusSummary.innerHTML = `
            <strong>Bed Status Summary:</strong> 
            <span class="badge bg-success ms-2">Vacant: ${vacant}</span>
            <span class="badge bg-warning ms-2">Occupied: ${occupied}</span>
            <span class="badge bg-danger ms-2">Maintenance: ${maintenance}</span>
        `;
    }
    
    // Validate beds before submit
    function validateBedsAndSubmit() {
        const statusFields = document.querySelectorAll('.status-field');
        let isValid = true;
        let errorMessage = '';
        
        for(let i = 0; i < statusFields.length; i++) {
            const status = statusFields[i].value;
            
            if(!status) {
                statusFields[i].classList.add('is-invalid');
                isValid = false;
                errorMessage = 'Please select status for all beds';
            } else {
                statusFields[i].classList.remove('is-invalid');
            }
        }
        
        if(isValid) {
            // Check if at least one bed is vacant for new room
            let hasVacantBed = false;
            for(let i = 0; i < statusFields.length; i++) {
                if(statusFields[i].value === 'vacant') {
                    hasVacantBed = true;
                    break;
                }
            }
            
            if(!hasVacantBed && !{{ !empty($hostelroom) ? 'true' : 'false' }}) {
                if(confirm('No vacant beds available. New room should have at least one vacant bed. Do you want to continue?')) {
                    document.getElementById('roomForm').submit();
                }
                return false;
            }
            
            // Submit the form
            document.getElementById('roomForm').submit();
        } else {
            alert(errorMessage || 'Please select status for all beds.');
        }
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        @if($errors->any())
            @if($errors->has('room_number') || $errors->has('hostel_id') || $errors->has('type') || $errors->has('max_beds'))
                goToStep(1);
            @elseif($errors->has('beds'))
                goToStep(2);
                setTimeout(() => generateBedRows(), 100);
            @endif
        @else
            goToStep(1);
            
            @if(!empty($hostelroom))
                window.existingBeds = @json($hostelroom->beds);
            @endif
        @endif
    });
</script>

<style>
    .wizard-step {
        animation: fadeIn 0.3s ease-in-out;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .is-invalid {
        border-color: #dc3545;
        background-image: none;
    }
    
    .nav-pills .nav-link.active {
        background-color: #0d6efd;
        color: white;
    }
    
    .nav-pills .nav-link {
        color: #6c757d;
        border-radius: 0.375rem;
        transition: all 0.3s;
    }
    
    .nav-pills .nav-link:hover:not(.active) {
        background-color: #e9ecef;
        color: #0d6efd;
    }
    
    .badge {
        padding: 5px 10px;
        font-size: 12px;
    }
</style>

@endsection