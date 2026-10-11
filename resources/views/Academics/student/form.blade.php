@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                            <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
                                <i class="feather-arrow-left me-2"></i>Back to List
                            </a>
                        </div>
                    </div>
                </div>

                <div class="content-area-body">
                    @include('elements.flash')

                    {{-- Single unified form --}}
                    <form action="{{ route('admin.students.preview') }}" method="POST" enctype="multipart/form-data" id="combinedForm">
                        @csrf

                        {{-- Hidden IDs for edit mode --}}
                        @if (!empty($student->id))
                            <input type="hidden" name="student_id_edit" value="{{ $student->id }}">
                        @endif
                        @if (!empty($enrollment->id))
                            <input type="hidden" name="enrollment_id" value="{{ $enrollment->id }}">
                        @endif

                        {{-- Edit mode flag --}}
                        @php $isEdit = !empty($student->id); @endphp

                        {{-- ═══════════════════════════════════════════
                             SECTION 1 — STUDENT PROFILE
                        ═══════════════════════════════════════════ --}}
                        <div class="card stretch stretch-full shadow-sm border-0 mb-4">
                            <div class="card-header bg-gradient-primary text-white p-4">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                    <div>
                                        <h4 class="mb-1 fw-bold">
                                            {{ $student->first_name ?? 'New' }} {{ $student->last_name ?? 'Student' }}'s
                                            Profile
                                        </h4>
                                        <small class="text-black-50">Step 1 of 2 — Personal Information</small>
                                    </div>
                                    <div class="progress" style="width: 200px; height: 8px;">
                                        <div class="progress-bar bg-white" role="progressbar" style="width: 0%"
                                            id="formProgress"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body p-4">
                                {{-- Photo + Admission Row --}}
                                <div class="row mb-5">
                                    <div class="col-lg-3 text-center">
                                        <div class="photo-upload position-relative mx-auto mb-3"
                                            style="width: 150px; height: 150px;">
                                            @if (isset($student) && $student->photo_url)
                                                <img src="{{ asset('storage/' . $student->photo_url) }}" alt="Student Photo"
                                                    class="img-fluid rounded-circle border border-3 border-white shadow-sm"
                                                    id="photoPreview" style="width: 100%; height: 100%; object-fit: cover;">
                                                <input type="hidden" name="old_photo" value="{{ $student->photo_url }}">
                                            @else
                                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center"
                                                    id="photoPlaceholder"
                                                    style="width: 100%; height: 100%; border: 3px dashed #dee2e6;">
                                                    <i class="feather-camera text-muted" style="font-size: 2rem;"></i>
                                                </div>
                                            @endif
                                            <input type="file" class="d-none" id="studentPhoto" name="student_photo"
                                                accept="image/*">
                                        </div>
                                        <label for="studentPhoto" class="btn btn-outline-primary btn-sm w-100">
                                            <i class="feather-upload me-1"></i>Change Photo
                                        </label>
                                        <small class="text-muted d-block mt-1">JPG/PNG, max 2MB</small>
                                        @error('student_photo')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-lg-9">
                                        <div class="row g-4">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Admission Number</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="feather-hash"></i></span>
                                                    <input type="text" class="form-control bg-light" readonly
                                                        value="{{ $student->admission_number ?? 'Auto Generated' }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Roll Number</label>
                                                <input type="text"
                                                    class="form-control @error('roll_number') is-invalid @enderror"
                                                    name="roll_number" autocomplete="off"
                                                    value="{{ old('roll_number', $student->roll_number ?? '') }}">
                                                @error('roll_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Student Field Tabs --}}
                                <ul class="nav nav-tabs nav-tabs-custom bg-light rounded-top mb-0" id="studentTabs"
                                    role="tablist">
                                    <li>
                                        <button class="nav-link active fw-semibold" data-bs-toggle="tab"
                                            data-bs-target="#personal" type="button">
                                            <i class="feather-user me-1"></i>Personal Info
                                        </button>
                                    </li>
                                    <li>
                                        <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#contact"
                                            type="button">
                                            <i class="feather-phone me-1"></i>Contact / Emergency
                                        </button>
                                    </li>
                                    <li>
                                        <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#medical"
                                            type="button">
                                            <i class="feather-heart me-1"></i>Medical
                                        </button>
                                    </li>
                                    <li>
                                        <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#bank"
                                            type="button">
                                            <i class="feather-credit-card me-1"></i>Bank Details
                                        </button>
                                    </li>
                                </ul>

                                {{-- TAB CONTENT --}}
                                <div class="tab-content bg-white rounded-bottom p-4 border shadow-sm"
                                    id="studentTabContent">

                                    {{-- Personal Info Tab --}}
                                    <div class="tab-pane fade show active" id="personal" role="tabpanel">
                                        <div class="row g-4">

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">First Name <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="feather-user"></i></span>
                                                    <input type="text"
                                                        class="form-control @error('first_name') is-invalid @enderror"
                                                        name="first_name"
                                                        value="{{ old('first_name', $student->first_name ?? '') }}"
                                                        required>
                                                </div>
                                                @error('first_name')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Last Name <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="feather-user"></i></span>
                                                    <input type="text"
                                                        class="form-control @error('last_name') is-invalid @enderror"
                                                        name="last_name"
                                                        value="{{ old('last_name', $student->last_name ?? '') }}"
                                                        required>
                                                </div>
                                                @error('last_name')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Gender <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="feather-users"></i></span>
                                                    <select class="form-select @error('gender') is-invalid @enderror"
                                                        name="gender" required>
                                                        <option value="">-- Select Gender --</option>
                                                        <option value="male"
                                                            {{ old('gender', $student->gender ?? '') == 'male' ? 'selected' : '' }}>
                                                            Male</option>
                                                        <option value="female"
                                                            {{ old('gender', $student->gender ?? '') == 'female' ? 'selected' : '' }}>
                                                            Female</option>
                                                        <option value="other"
                                                            {{ old('gender', $student->gender ?? '') == 'other' ? 'selected' : '' }}>
                                                            Other</option>
                                                    </select>
                                                </div>
                                                @error('gender')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Date of Birth</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="feather-calendar"></i></span>
                                                    <input type="date"
                                                        class="form-control @error('dob') is-invalid @enderror"
                                                        name="dob"
                                                        value="{{ old('dob', $student->dob ? $student->dob->format('Y-m-d') : '') }}">
                                                </div>
                                                @error('dob')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Blood Group</label>
                                                <select class="form-select @error('blood_group') is-invalid @enderror"
                                                    name="blood_group">
                                                    <option value="">-- Select Blood Group --</option>
                                                    <option value="A+"  {{ old('blood_group', $student->blood_group ?? '') == 'A+'  ? 'selected' : '' }}>A+</option>
                                                    <option value="A-"  {{ old('blood_group', $student->blood_group ?? '') == 'A-'  ? 'selected' : '' }}>A−</option>
                                                    <option value="B+"  {{ old('blood_group', $student->blood_group ?? '') == 'B+'  ? 'selected' : '' }}>B+</option>
                                                    <option value="B-"  {{ old('blood_group', $student->blood_group ?? '') == 'B-'  ? 'selected' : '' }}>B−</option>
                                                    <option value="AB+" {{ old('blood_group', $student->blood_group ?? '') == 'AB+' ? 'selected' : '' }}>AB+</option>
                                                    <option value="AB-" {{ old('blood_group', $student->blood_group ?? '') == 'AB-' ? 'selected' : '' }}>AB−</option>
                                                    <option value="O+"  {{ old('blood_group', $student->blood_group ?? '') == 'O+'  ? 'selected' : '' }}>O+</option>
                                                    <option value="O-"  {{ old('blood_group', $student->blood_group ?? '') == 'O-'  ? 'selected' : '' }}>O−</option>
                                                </select>
                                                @error('blood_group')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Caste</label>
                                                <select class="form-select" name="category" id="category" onchange="toggleSubCaste()">
                                                    <option value="">-- Select --</option>
                                                    <option value="sc"      {{ old('category', $student->category ?? '') == 'sc'      ? 'selected' : '' }}>SC</option>
                                                    <option value="st"      {{ old('category', $student->category ?? '') == 'st'      ? 'selected' : '' }}>ST</option>
                                                    <option value="obc"     {{ old('category', $student->category ?? '') == 'obc'     ? 'selected' : '' }}>OBC</option>
                                                    <option value="general" {{ old('category', $student->category ?? '') == 'general' ? 'selected' : '' }}>GENERAL</option>
                                                    <option value="sebc"    {{ old('category', $student->category ?? '') == 'sebc'    ? 'selected' : '' }}>SEBC</option>
                                                    <option value="other"   {{ old('category', $student->category ?? '') == 'other'   ? 'selected' : '' }}>Other</option>
                                                </select>
                                                <div id="subCasteBox" style="display: {{ old('category', $student->category ?? '') == 'other' ? 'block' : 'none' }}; margin-top: 8px;">
                                                    <input type="text"
                                                           class="form-control @error('sub_caste') is-invalid @enderror"
                                                           name="sub_caste"
                                                           id="sub_caste"
                                                           placeholder="Enter caste name"
                                                           value="{{ old('sub_caste', $student->sub_caste ?? '') }}">
                                                    @error('sub_caste')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Religion</label>
                                                <select class="form-select" name="religion">
                                                    <option value="">-- Select Religion --</option>
                                                    <option value="Hindu"     {{ old('religion', $student->religion ?? '') == 'Hindu'     ? 'selected' : '' }}>Hindu</option>
                                                    <option value="Muslim"    {{ old('religion', $student->religion ?? '') == 'Muslim'    ? 'selected' : '' }}>Muslim</option>
                                                    <option value="Christian" {{ old('religion', $student->religion ?? '') == 'Christian' ? 'selected' : '' }}>Christian</option>
                                                    <option value="Sikh"      {{ old('religion', $student->religion ?? '') == 'Sikh'      ? 'selected' : '' }}>Sikh</option>
                                                    <option value="Buddhist"  {{ old('religion', $student->religion ?? '') == 'Buddhist'  ? 'selected' : '' }}>Buddhist</option>
                                                    <option value="Jain"      {{ old('religion', $student->religion ?? '') == 'Jain'      ? 'selected' : '' }}>Jain</option>
                                                    <option value="Parsi"     {{ old('religion', $student->religion ?? '') == 'Parsi'     ? 'selected' : '' }}>Parsi</option>
                                                    <option value="Jewish"    {{ old('religion', $student->religion ?? '') == 'Jewish'    ? 'selected' : '' }}>Jewish</option>
                                                    <option value="Other"     {{ old('religion', $student->religion ?? '') == 'Other'     ? 'selected' : '' }}>Other</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Nationality</label>
                                                <select class="form-select" name="nationality">
                                                    <option value="">-- Select Nationality --</option>
                                                    <option value="Indian" {{ old('nationality', $student->nationality ?? '') == 'Indian' ? 'selected' : '' }}>Indian</option>
                                                    <option value="Other"  {{ old('nationality', $student->nationality ?? '') == 'Other'  ? 'selected' : '' }}>Other</option>
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Aadhaar</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="feather-hash"></i></span>
                                                    <input type="text"
                                                        class="form-control @error('aadhaar') is-invalid @enderror"
                                                        name="aadhaar" placeholder="12 digits" autocomplete="off"
                                                        value="{{ old('aadhaar', $student->aadhaar ?? '') }}">
                                                </div>
                                                @error('aadhaar')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Physically Handicapped</label>
                                                <select class="form-select" name="physically_handicapped"
                                                    id="physicallyHandicapped" onchange="toggleHandicapDetails()">
                                                    <option value="">-- Select --</option>
                                                    <option value="no"
                                                        {{ old('physically_handicapped', $student->physically_handicapped ?? 'no') == 'no' ? 'selected' : '' }}>
                                                        No</option>
                                                    <option value="yes"
                                                        {{ old('physically_handicapped', $student->physically_handicapped ?? '') == 'yes' ? 'selected' : '' }}>
                                                        Yes</option>
                                                </select>
                                            </div>

                                            <div class="col-md-12" id="handicapDetailsBox"
                                                style="display: {{ old('physically_handicapped', $student->physically_handicapped ?? '') == 'yes' ? 'block' : 'none' }};">
                                                <label class="form-label fw-semibold">Disability Details <span
                                                        class="text-danger">*</span></label>
                                                <textarea class="form-control" name="handicap_details" rows="2"
                                                    placeholder="e.g. Visual impairment, Hearing impairment, Locomotor disability...">{{ old('handicap_details', $student->handicap_details ?? '') }}</textarea>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Student Status <span
                                                        class="text-danger">*</span></label>
                                                <div class="d-flex align-items-center gap-2">
                                                    <select
                                                        class="form-select status-select @error('student_status') is-invalid @enderror"
                                                        name="student_status" required>
                                                        <option value="">Select Status</option>
                                                        <option value="active"
                                                            {{ old('student_status', $student->status ?? 'active') == 'active' ? 'selected' : '' }}>
                                                            Active</option>
                                                        <option value="transfer_out"
                                                            {{ old('student_status', $student->status ?? '') == 'transfer_out' ? 'selected' : '' }}>
                                                            Transfer Out</option>
                                                        <option value="alumni"
                                                            {{ old('student_status', $student->status ?? '') == 'alumni' ? 'selected' : '' }}>
                                                            Alumni</option>
                                                        <option value="discontinued"
                                                            {{ old('student_status', $student->status ?? '') == 'discontinued' ? 'selected' : '' }}>
                                                            Discontinued</option>
                                                    </select>
                                                    <button type="button" id="exitStudentBtn"
                                                        class="btn btn-danger btn-sm px-3 flex-shrink-0"
                                                        onclick="triggerExitStudent()"
                                                        title="Mark student as discontinued">
                                                        <i class="feather-log-out me-1"></i>Exit
                                                    </button>
                                                </div>
                                                @error('student_status')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>{{-- end row --}}

                                        {{-- Transfer Out Reason --}}
                                        <div class="row mt-3" id="transferOutReasonBox"
                                            style="display: {{ old('student_status', $student->status ?? '') == 'transfer_out' ? 'flex' : 'none' }};">
                                            <div class="col-md-12">
                                                <label class="form-label fw-semibold">
                                                    <i class="feather-alert-circle text-warning me-1"></i>
                                                    Transfer Out Reason <span class="text-danger">*</span>
                                                </label>
                                                <textarea class="form-control @error('transfer_out_reason') is-invalid @enderror" name="transfer_out_reason"
                                                    rows="3" placeholder="Please provide reason for transfer out...">{{ old('transfer_out_reason', $student->transfer_out_reason ?? '') }}</textarea>
                                                @error('transfer_out_reason')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Discontinued Fields --}}
                                        <div id="discontinuedFields" class="mt-4 p-3 bg-light rounded"
                                            style="display: {{ old('student_status', $student->status ?? '') == 'discontinued' ? 'block' : 'none' }};">
                                            <h6 class="text-danger mb-3">
                                                <i class="feather-alert-triangle me-2"></i>
                                                <strong>Discontinuation Details</strong>
                                            </h6>
                                            <div id="pendingInstallmentsList" class="mb-3"></div>
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold text-danger">Discontinue Date</label>
                                                    <input type="date" class="form-control" name="discontinue_date"
                                                        value="{{ old('discontinue_date', $student->discontinue_date ?? '') }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold text-danger">Reason</label>
                                                    <textarea class="form-control" name="discontinue_reason" rows="2" placeholder="e.g. Personal reasons...">{{ old('discontinue_reason', $student->discontinue_reason ?? '') }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-3 p-3 bg-warning bg-opacity-10 border rounded"
                                            id="paidAmountSummary" style="display: none;">
                                            <h6><i class="feather-info me-2 text-warning"></i>Payment Summary</h6>
                                            <div id="paidSummaryContent"></div>
                                        </div>
                                    </div>{{-- end personal tab --}}

                                    {{-- Contact Tab --}}
                                    <div class="tab-pane fade" id="contact" role="tabpanel">
                                        <div class="row g-4">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Email</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="feather-mail"></i></span>
                                                    <input type="email"
                                                        class="form-control @error('email') is-invalid @enderror"
                                                        name="email" placeholder="example@email.com"
                                                        value="{{ old('email', $student->email ?? '') }}">
                                                </div>
                                                @error('email')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-semibold">WhatsApp Number</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="feather-phone"></i></span>
                                                    <input type="tel"
                                                        class="form-control @error('whatsapp_number') is-invalid @enderror"
                                                        name="whatsapp_number" placeholder="10 digits number" autocomplete="off"
                                                        value="{{ old('whatsapp_number', $student->whatsapp_number ?? '') }}">
                                                </div>
                                                @error('whatsapp_number')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row g-4">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Emergency Contact Name</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="feather-user"></i></span>
                                                    <input type="text" class="form-control"
                                                        name="emergency_contact_name"
                                                        value="{{ old('emergency_contact_name', $student->emergency_contact_name ?? '') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Emergency Phone <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="feather-phone"></i></span>
                                                    <input type="tel"
                                                        class="form-control @error('emergency_contact_phone') is-invalid @enderror"
                                                        name="emergency_contact_phone"
                                                        placeholder="10 digits number"
                                                        value="{{ old('emergency_contact_phone', $student->emergency_contact_phone ?? '') }}"
                                                        autocomplete="off">
                                                </div>
                                                @error('emergency_contact_phone')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>{{-- end contact tab --}}

                                    {{-- Medical Tab --}}
                                    <div class="tab-pane fade" id="medical" role="tabpanel">
                                        <div class="row g-4">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Medical Conditions</label>
                                                <textarea class="form-control" name="medical_conditions" rows="4">{{ old('medical_conditions', $student->medical_conditions ?? '') }}</textarea>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Historical Disease</label>
                                                <textarea class="form-control" name="allergies" rows="4">{{ old('allergies', $student->allergies ?? '') }}</textarea>
                                            </div>
                                        </div>
                                    </div>{{-- end medical tab --}}

                                    {{-- Bank Tab --}}
                                    <div class="tab-pane fade" id="bank" role="tabpanel">
                                        <div class="row g-4">

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Account Holder Name <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="feather-user"></i></span>
                                                    <input type="text" class="form-control @error('account_holder_name') is-invalid @enderror"
                                                        name="account_holder_name" placeholder="As per bank records" 
                                                        value="{{ old('account_holder_name', $student->account_holder_name ?? '') }}" autocomplete="off">
                                                </div>
                                                @error('account_holder_name')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Bank Name <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="feather-briefcase"></i></span>
                                                    <input type="text" class="form-control @error('bank_name') is-invalid @enderror"
                                                        name="bank_name" placeholder="e.g. State Bank of India" 
                                                        value="{{ old('bank_name', $student->bank_name ?? '') }}" autocomplete="off">
                                                </div>
                                                @error('bank_name')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Branch Name</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="feather-map-pin"></i></span>
                                                    <input type="text" class="form-control" name="branch_name"
                                                        value="{{ old('branch_name', $student->branch_name ?? '') }}" autocomplete="off">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Account Type</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="feather-layers"></i></span>
                                                    <select class="form-select" name="account_type">
                                                        <option value="">-- Select --</option>
                                                        <option value="savings" {{ old('account_type', $student->account_type ?? '') == 'savings' ? 'selected' : '' }}>Savings</option>
                                                        <option value="current" {{ old('account_type', $student->account_type ?? '') == 'current' ? 'selected' : '' }}>Current</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Account Number <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="feather-credit-card"></i></span>
                                                    <input type="text" class="form-control @error('account_number') is-invalid @enderror"
                                                        name="account_number" id="account_number" 
                                                        value="{{ old('account_number', $student->account_number ?? '') }}" autocomplete="off">
                                                </div>
                                                @error('account_number')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">IFSC Code <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="feather-hash"></i></span>
                                                    <input type="text" class="form-control @error('ifsc_code') is-invalid @enderror"
                                                        name="ifsc_code" id="ifsc_code" placeholder="e.g. SBIN0001234"
                                                        maxlength="11" style="text-transform:uppercase;" 
                                                        value="{{ old('ifsc_code', $student->ifsc_code ?? '') }}" autocomplete="off">
                                                </div>
                                                @error('ifsc_code')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">{{-- intentionally empty --}}</div>

                                        </div>
                                    </div>{{-- end bank tab --}}

                                </div>{{-- end tab-content --}}
                            </div>{{-- end card-body --}}
                        </div>{{-- end student card --}}


                        {{-- ═══════════════════════════════════════════
                             SECTION 2 — ENROLLMENT
                        ═══════════════════════════════════════════ --}}
                        <div class="card stretch stretch-full shadow-sm border-0 mb-4">
                            <div class="card-header bg-gradient-success text-white p-4">
                                <h4 class="mb-1 fw-bold"><i class="feather-book-open me-2"></i>Enrollment Information</h4>
                                <small class="text-black-50">Step 2 of 2 — Link to Class, Section & Fees</small>
                            </div>

                            <div class="card-body p-4">

                                
                                @if($isEdit)
                                    
                                @endif

                                <div class="row g-4">

                                    {{-- ── ACADEMIC YEAR ─────────────────────────────────── --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">
                                            Academic Year <span class="text-danger">*</span>
                                            @if($isEdit)<i class="feather-lock ms-1 text-muted" style="font-size:12px;" title="Locked"></i>@endif
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="feather-calendar"></i></span>
                                            <select class="form-select @error('academic_year_id') is-invalid @enderror {{ $isEdit ? 'bg-light' : '' }}"
                                                id="academic_year_id"
                                                name="{{ $isEdit ? '_academic_year_id_display' : 'academic_year_id' }}"
                                                required
                                                {{ $isEdit ? 'disabled' : '' }}>
                                                <option value="">Select Academic Year</option>
                                                @foreach ($academicYears as $year)
                                                    <option value="{{ $year->id }}"
                                                        {{ old('academic_year_id', $enrollment->academic_year_id ?? '') == $year->id ? 'selected' : '' }}>
                                                        {{ $year->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @if($isEdit)
                                            <input type="hidden" name="academic_year_id" value="{{ $enrollment->academic_year_id }}">
                                        @endif
                                        @error('academic_year_id')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- ── COURSE ─────────────────────────────────────────── --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">
                                            Course <span class="text-danger">*</span>
                                            @if($isEdit)<i class="feather-lock ms-1 text-muted" style="font-size:12px;" title="Locked"></i>@endif
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="feather-book-open"></i></span>
                                            <select class="form-select @error('course_id') is-invalid @enderror {{ $isEdit ? 'bg-light' : '' }}"
                                                id="course_id"
                                                name="{{ $isEdit ? '_course_id_display' : 'course_id' }}"
                                                required
                                                {{ $isEdit ? 'disabled' : '' }}>
                                                <option value="">Select Course</option>
                                                @foreach ($courses as $course)
                                                    <option value="{{ $course->id }}"
                                                        data-is-lateral-entry="{{ $course->is_lateral_entry ? 1 : 0 }}"
                                                        {{ old('course_id', $enrollment->course_id ?? '') == $course->id ? 'selected' : '' }}>
                                                        {{ $course->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @if($isEdit)
                                            <input type="hidden" name="course_id" value="{{ $enrollment->course_id }}">
                                        @endif
                                        @error('course_id')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- ── STUDENT TYPE ────────────────────────────────────── --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">
                                            Student Type
                                            @if($isEdit)<i class="feather-lock ms-1 text-muted" style="font-size:12px;" title="Locked"></i>@endif
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="feather-users"></i></span>
                                            <select class="form-select {{ $isEdit ? 'bg-light' : '' }}"
                                                id="student_type"
                                                name="{{ $isEdit ? '_student_type_display' : 'student_type' }}"
                                                required
                                                {{ $isEdit ? 'disabled' : '' }}>
                                                <option value="regular"
                                                    {{ old('student_type', $enrollment->student_type ?? '') == 'regular' ? 'selected' : '' }}>
                                                    Regular</option>
                                                <option value="lateral" id="student_type_lateral_option"
                                                    {{ old('student_type', $enrollment->student_type ?? '') == 'lateral' ? 'selected' : '' }}>
                                                    Lateral</option>
                                            </select>
                                        </div>
                                        @if($isEdit)
                                            <input type="hidden" name="student_type" value="{{ $enrollment->student_type }}">
                                        @endif
                                    </div>

                                    {{-- ── BRANCH ──────────────────────────────────────────── --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">
                                            Branch <span class="text-danger">*</span>
                                            @if($isEdit)<i class="feather-lock ms-1 text-muted" style="font-size:12px;" title="Locked"></i>@endif
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="feather-book"></i></span>
                                            <select class="form-select @error('stream_id') is-invalid @enderror {{ $isEdit ? 'bg-light' : '' }}"
                                                id="stream_id"
                                                name="{{ $isEdit ? '_stream_id_display' : 'stream_id' }}"
                                                required
                                                {{ $isEdit ? 'disabled' : '' }}>
                                                <option value="">Select Branch</option>
                                                @foreach ($streams as $stream)
                                                    <option value="{{ $stream->id }}"
                                                        {{ old('stream_id', $enrollment->stream_id ?? '') == $stream->id ? 'selected' : '' }}>
                                                        {{ $stream->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @if($isEdit)
                                            <input type="hidden" name="stream_id" value="{{ $enrollment->stream_id }}">
                                        @endif
                                        @error('stream_id')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- ── SEMESTER ─────────────────────────────────────────── --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">
                                            Semester <span class="text-danger">*</span>
                                            @if($isEdit)<i class="feather-lock ms-1 text-muted" style="font-size:12px;" title="Locked"></i>@endif
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="feather-hash"></i></span>
                                            <select class="form-select @error('class_id') is-invalid @enderror {{ $isEdit ? 'bg-light' : '' }}"
                                                id="class_id"
                                                name="{{ $isEdit ? '_class_id_display' : 'class_id' }}"
                                                required
                                                {{ $isEdit ? 'disabled' : '' }}>
                                                <option value="">Select Semester</option>
                                                @foreach ($classes as $class)
                                                    <option value="{{ $class->id }}"
                                                        data-order-index="{{ $class->order_index ?? '' }}"
                                                        {{ old('class_id', $enrollment->class_id ?? '') == $class->id ? 'selected' : '' }}>
                                                        {{ $class->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @if($isEdit)
                                            <input type="hidden" name="class_id" value="{{ $enrollment->class_id }}">
                                        @endif
                                        @error('class_id')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- ── SECTION (editable always) ───────────────────────── --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Section <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="feather-hash"></i></span>
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
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <div id="seatAvailabilityAlert" class="alert alert-info d-none mb-0">
                                            <i class="feather-info me-2"></i>
                                            <span id="seatAvailabilityText"></span>
                                        </div>
                                    </div>

                                    {{-- ── DATE OF ADMISSION (locked in edit) ──────────────── --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">
                                            Date of Admission
                                            @if($isEdit)<i class="feather-lock ms-1 text-muted" style="font-size:12px;" title="Locked"></i>@endif
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="feather-calendar"></i></span>
                                            <input type="date"
                                                class="form-control {{ $isEdit ? 'bg-light' : '' }}"
                                                name="{{ $isEdit ? '_dateof_admission_display' : 'dateof_admission' }}"
                                                {{ $isEdit ? 'readonly' : '' }}
                                                value="{{ old('dateof_admission', $enrollment->dateof_admission ?? now()->format('Y-m-d')) }}">
                                        </div>
                                        @if($isEdit)
                                            <input type="hidden" name="dateof_admission" value="{{ $enrollment->dateof_admission }}">
                                        @endif
                                    </div>

                                    {{-- ── DATE OF LEAVING (always editable) ───────────────── --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Date of Leaving</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="feather-log-out"></i></span>
                                            <input type="date" class="form-control" name="date_of_leaving"
                                                value="{{ old('date_of_leaving', isset($enrollment->date_of_leaving) ? \Carbon\Carbon::parse($enrollment->date_of_leaving)->format('Y-m-d') : '') }}">
                                        </div>
                                    </div>

                                    {{-- ── ENROLLMENT STATUS (always editable) ─────────────── --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Enrollment Status <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="feather-shield"></i></span>
                                            <select class="form-select @error('enrollment_status') is-invalid @enderror"
                                                name="enrollment_status" required>
                                                <option value="">Select Status</option>
                                                <option value="active"
                                                    {{ old('enrollment_status', $enrollment->status ?? 'active') == 'active' ? 'selected' : '' }}>
                                                    Active</option>
                                                <option value="promoted"
                                                    {{ old('enrollment_status', $enrollment->status ?? '') == 'promoted' ? 'selected' : '' }}>
                                                    Promoted</option>
                                                <option value="transferred"
                                                    {{ old('enrollment_status', $enrollment->status ?? '') == 'transferred' ? 'selected' : '' }}>
                                                    Transferred</option>
                                                <option value="completed"
                                                    {{ old('enrollment_status', $enrollment->status ?? '') == 'completed' ? 'selected' : '' }}>
                                                    Completed</option>
                                            </select>
                                        </div>
                                        @error('enrollment_status')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- ── RESIDENCE TYPE (always editable) ────────────────── --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Residence Type</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="feather-home"></i></span>
                                            <select class="form-select" id="residence_type" name="residence_type">
                                                <option value="">Select Residence Type</option>
                                                <option value="day_scholar"
                                                    {{ old('residence_type', $enrollment->residence_type ?? '') == 'day_scholar' ? 'selected' : '' }}>
                                                    Day Scholar</option>
                                                <option value="hosteller"
                                                    {{ old('residence_type', $enrollment->residence_type ?? '') == 'hosteller' ? 'selected' : '' }}>
                                                    Hosteller</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>{{-- end row --}}

                                {{-- ── HOSTEL SECTION ──────────────────────────────── --}}
                                <div id="hostel-section" style="display: none;" class="mt-4">
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <div class="flex-shrink-0 bg-opacity-10 rounded p-2">
                                            <i class="feather-home text-success fs-5"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-0">Hostel Allocation</h6>
                                            <small class="text-muted">Assign hostel, room and bed to the student</small>
                                        </div>
                                    </div>
                                    <hr class="mt-0">

                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Hostel</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="feather-home"></i></span>
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
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Room</label>
                                            <div id="rooms-container">
                                                @if (!empty($existingHostel) && $existingHostel->room)
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="feather-grid"></i></span>
                                                        <select name="room_id" id="room_id" class="form-select">
                                                            <option value="{{ $existingHostel->room_id }}" selected>
                                                                {{ $existingHostel->room->room_number }}
                                                                ({{ $existingHostel->room->type }})
                                                            </option>
                                                        </select>
                                                    </div>
                                                @else
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="feather-grid"></i></span>
                                                        <select name="room_id" id="room_id" class="form-select" disabled>
                                                            <option>Select hostel first</option>
                                                        </select>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Bed</label>
                                            <div id="beds-container">
                                                @if (!empty($existingHostel) && $existingHostel->bed)
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="feather-minus-square"></i></span>
                                                        <select name="bed_id" id="bed_id" class="form-select">
                                                            <option value="{{ $existingHostel->bed_id }}" selected
                                                                data-amount="{{ $existingHostel->bed->amount }}">
                                                                {{ $existingHostel->bed->bed_number }}
                                                                (₹{{ number_format($existingHostel->bed->amount, 2) }})
                                                            </option>
                                                        </select>
                                                    </div>
                                                @else
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="feather-minus-square"></i></span>
                                                        <select name="bed_id" id="bed_id" class="form-select" disabled>
                                                            <option>Select room first</option>
                                                        </select>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Fee (Yearly)</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="feather-dollar-sign"></i></span>
                                                <input type="number" step="0.01" name="hostel_fee" id="hostel_fee"
                                                    class="form-control"
                                                    value="{{ old('hostel_fee', $existingHostel->bed->amount ?? '') }}"
                                                    min="0" placeholder="0.00">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">From Date</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="feather-calendar"></i></span>
                                                <input type="date" name="hostel_from_date" class="form-control"
                                                    value="{{ old('hostel_from_date', $existingHostel->from_date ?? now()->format('Y-m-d')) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">To Date</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="feather-calendar"></i></span>
                                                <input type="date" name="hostel_to_date" class="form-control"
                                                    value="{{ old('hostel_to_date', $existingHostel->to_date ?? '') }}">
                                            </div>
                                        </div>
                                    </div>

                                    @if (!empty($existingHostel))
                                        <div class="mt-3 p-3 rounded border border-success border-opacity-25 bg-opacity-10">
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
                                                    <small class="text-muted d-block">Yearly</small>
                                                    <span class="fw-semibold text-success">₹{{ number_format($existingHostel->bed->amount, 2) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>{{-- end hostel-section --}}

                                {{-- ── FEE STRUCTURES ──────────────────────────────── --}}
                                <div class="mt-4">
                                    <label class="form-label fw-semibold">
                                        Fee Structures <span class="text-danger">*</span>
                                        <small class="text-muted fw-normal ms-2">
                                            @if (!empty($enrollment->id))
                                                Currently assigned: {{ count($existingFeeStructureIds ?? []) }} fee(s)
                                            @else
                                                Select Academic Year, Course, Branch and Semester above to load
                                            @endif
                                        </small>
                                    </label>
                                    <div id="fee-structures-container" class="fee-grid-container">
                                        <div class="alert alert-info text-center py-4">
                                            <i class="feather-book fs-2 mb-2 d-block"></i>
                                            <div>Select Academic Year, Course, Branch and Semester above</div>
                                            <div class="small">to view available fee structures</div>
                                        </div>
                                    </div>
                                    <!-- @error('fee_structure_ids')
                                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                                    @enderror -->
                                </div>

                            </div>{{-- end card-body --}}
                        </div>{{-- end enrollment card --}}

                        {{-- ── SUBMIT BUTTON ────────────────────────────── --}}
                        <div class="d-flex justify-content-end gap-3 mb-5">
                            <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary px-4">
                                <i class="feather-x me-2"></i>Cancel
                            </a>
                           <button type="submit" class="btn btn-warning btn-lg px-5">
                                <i class="feather-eye me-2"></i>Preview before saving
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .fee-card {
            transition: all .3s ease;
            border: 2px solid transparent;
        }
        .fee-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, .1) !important;
        }
        .fee-card.border-primary {
            border-color: #0d6efd !important;
            box-shadow: 0 0 0 .25rem rgba(13, 110, 253, .25) !important;
        }
        .spin {
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }
        #hostel-section {
            overflow: hidden;
            transition: all .35s ease;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const IS_EDIT_MODE = {{ $isEdit ? 'true' : 'false' }};

        function triggerExitStudent() {
            const sel = document.querySelector('.status-select');
            if (!sel) return;
            sel.value = 'discontinued';
            sel.dispatchEvent(new Event('change'));
            const box = document.getElementById('discontinuedFields');
            if (box) box.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        $(document).ready(function () {

            /* ── Photo preview ──────────────────────────────────────── */
            $('#studentPhoto').on('change', function () {
                const file = this.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = function (e) {
                    let preview = document.getElementById('photoPreview');
                    if (!preview) {
                        preview = document.createElement('img');
                        preview.id = 'photoPreview';
                        preview.className = 'img-fluid rounded-circle border border-3 border-white shadow-sm';
                        preview.style.cssText = 'width:100%;height:100%;object-fit:cover;';
                        document.querySelector('.photo-upload').prepend(preview);
                    }
                    preview.src = e.target.result;
                    const placeholder = document.getElementById('photoPlaceholder');
                    if (placeholder) placeholder.style.visibility = 'hidden';
                };
                reader.readAsDataURL(file);
            });

            /* ── IFSC uppercase ─────────────────────────────────────── */
            $('#ifsc_code').on('input', function () {
                this.value = this.value.toUpperCase();
            });

            /* ── Student status toggles ─────────────────────────────── */
            const statusSelect    = document.querySelector('.status-select');
            const transferBox     = document.getElementById('transferOutReasonBox');
            const discontinuedBox = document.getElementById('discontinuedFields');

            function applyStudentStatus(val) {
                transferBox.style.display     = val === 'transfer_out'  ? 'block' : 'none';
                discontinuedBox.style.display = val === 'discontinued'  ? 'block' : 'none';
                if (val === 'discontinued') checkPendingInstallments();
            }

            if (statusSelect) {
                statusSelect.addEventListener('change', function () { applyStudentStatus(this.value); });
                applyStudentStatus(statusSelect.value);
            }

            /* ── Physically Handicapped toggle ─────────────────────── */
            function toggleHandicapDetails() {
                const val = document.getElementById('physicallyHandicapped').value;
                document.getElementById('handicapDetailsBox').style.display = val === 'yes' ? 'block' : 'none';
            }
            toggleHandicapDetails();
            document.getElementById('physicallyHandicapped').addEventListener('change', toggleHandicapDetails);

            function checkPendingInstallments() {
                const studentId = {{ $student->id ?? 'null' }};
                const studentFeesUrlTemplate = @json(route('admin.students.check-fees', ['student' => '__STUDENT__']));
                if (!studentId) return;
                fetch(studentFeesUrlTemplate.replace('__STUDENT__', studentId), {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.has_pending) {
                        let html = '<div class="alert alert-warning"><h6>⏳ Pending Installments:</h6><ul class="mb-0">';
                        data.installments.forEach(item => {
                            html += `<li><strong>${item.fee_category_name}</strong> — ₹${item.amount} (Due: ${item.due_date}) <span class="badge ${item.status === 'paid' ? 'bg-success' : 'bg-danger'}">${item.status.toUpperCase()}</span></li>`;
                        });
                        html += '</ul></div>';
                        document.getElementById('pendingInstallmentsList').innerHTML = html;

                        document.getElementById('paidSummaryContent').innerHTML = `
                            <div class="row">
                                <div class="col-4"><strong>Total:</strong> ₹${data.summary.total_fees}</div>
                                <div class="col-4"><strong>Paid:</strong> ₹${data.summary.paid_amount}</div>
                                <div class="col-4"><strong>Pending:</strong> <span class="text-danger">₹${data.summary.pending_amount}</span></div>
                            </div>`;
                        document.getElementById('paidAmountSummary').style.display = 'block';
                    }
                })
                .catch(console.error);
            }

            /* ── URL builders ───────────────────────────────────────── */
            const oldRoomId    = @json(old('room_id', $existingHostel->room_id ?? null));
            const oldBedId     = @json(old('bed_id',  $existingHostel->bed_id  ?? null));
            const oldCourseId  = @json(old('course_id',  $enrollment->course_id  ?? null));
            const oldStreamId  = @json(old('stream_id',  $enrollment->stream_id  ?? null));
            const oldClassId   = @json(old('class_id',   $enrollment->class_id   ?? null));
            const oldSectionId = @json(old('section_id', $enrollment->section_id ?? null));
            const currentEnrollmentId = @json($enrollment->id ?? null);

            const hostelRoomsUrlTemplate  = @json(route('admin.hostels.rooms', ['hostel' => '__HOSTEL__']));
            const roomBedsUrlTemplate     = @json(route('admin.rooms.beds',    ['room'   => '__ROOM__']));
            const studentStreamsUrlTemplate  = "{{ route('admin.students.streams',  ['courseId' => ':course']) }}";
            const studentClassesUrlTemplate  = "{{ route('admin.students.classes',  ['courseId' => ':course', 'streamId' => ':stream']) }}";
            const studentSectionsUrlTemplate = "{{ route('admin.students.sections', ['courseId' => ':course', 'streamId' => ':stream', 'classId' => ':class']) }}";
            const seatAvailabilityUrl = @json(route('admin.students.seat-availability'));

            function buildRoute(template, replacements) {
                let url = template;
                url = url.replace(':course', replacements.courseId || '');
                url = url.replace(':stream', replacements.streamId || '');
                url = url.replace(':class',  replacements.classId  || '');
                return url;
            }

            function resetAcademicSelect(selector, placeholder) {
                $(selector).html(`<option value="">${placeholder}</option>`).prop('disabled', true);
            }

            function setSelectOptions(selector, items, placeholder, selectedValue = '') {
                const select  = $(selector);
                let   options = `<option value="">${placeholder}</option>`;
                items.forEach(item => {
                    const selected    = String(selectedValue) === String(item.id) ? ' selected' : '';
                    const orderIndex  = item.order_index ? ` data-order-index="${item.order_index}"` : '';
                    options += `<option value="${item.id}"${orderIndex}${selected}>${item.name}</option>`;
                });
                select.html(options).prop('disabled', false);
            }

            function selectedCourseAllowsLateral() {
                return String($('#course_id option:selected').attr('data-is-lateral-entry')) === '1';
            }

            function syncStudentTypeOptions() {
                if (IS_EDIT_MODE) return; // locked in edit mode
                const allowsLateral  = selectedCourseAllowsLateral();
                const typeSelect     = $('#student_type');
                let   lateralOption  = $('#student_type_lateral_option');

                if (!allowsLateral && lateralOption.length) {
                    lateralOption.detach();
                } else if (allowsLateral && !lateralOption.length) {
                    typeSelect.append('<option value="lateral" id="student_type_lateral_option">Lateral</option>');
                    lateralOption = $('#student_type_lateral_option');
                }

                if (!allowsLateral && typeSelect.val() === 'lateral') {
                    typeSelect.val('regular');
                }
            }

            let selectedSeatAvailable = true;

            function resetSeatAvailability() {
                selectedSeatAvailable = true;
                $('#seatAvailabilityAlert')
                    .addClass('d-none')
                    .removeClass('alert-success alert-warning alert-danger')
                    .addClass('alert-info');
                $('#seatAvailabilityText').text('');
            }

            function renderSeatAvailability(data) {
                selectedSeatAvailable = Boolean(data.available);
                const alert     = $('#seatAvailabilityAlert');
                const typeLabel = data.student_type === 'lateral' ? 'Lateral' : 'Regular';
                const detail    = `${typeLabel}: ${data.remaining} available / ${data.limit} total (${data.used} used)`;

                alert
                    .removeClass('d-none alert-info alert-success alert-warning alert-danger')
                    .addClass(data.available ? 'alert-success' : 'alert-danger');

                $('#seatAvailabilityText').text(`${data.message} ${detail}`);
            }

            function loadSeatAvailability() {
                // Read the actual values — from hidden inputs in edit mode, from selects in create mode
                const academicYearId = IS_EDIT_MODE
                    ? $('input[name="academic_year_id"]').val()
                    : $('#academic_year_id').val();
                const courseId = IS_EDIT_MODE
                    ? $('input[name="course_id"]').val()
                    : $('#course_id').val();
                const streamId = IS_EDIT_MODE
                    ? $('input[name="stream_id"]').val()
                    : $('#stream_id').val();
                const classId = IS_EDIT_MODE
                    ? $('input[name="class_id"]').val()
                    : $('#class_id').val();
                const sectionId  = $('#section_id').val();
                const studentType = IS_EDIT_MODE
                    ? $('input[name="student_type"]').val()
                    : ($('#student_type').val() || 'regular');

                if (!academicYearId || !courseId || !streamId || !classId || !sectionId || !studentType) {
                    resetSeatAvailability();
                    return;
                }

                $('#seatAvailabilityAlert')
                    .removeClass('d-none alert-success alert-warning alert-danger')
                    .addClass('alert-info');
                $('#seatAvailabilityText').text('Checking seat availability...');

                $.get(seatAvailabilityUrl, {
                    academic_year_id: academicYearId,
                    course_id:        courseId,
                    stream_id:        streamId,
                    class_id:         classId,
                    section_id:       sectionId,
                    student_type:     studentType,
                    enrollment_id:    currentEnrollmentId,
                }).done(renderSeatAvailability).fail(function () {
                    selectedSeatAvailable = false;
                    $('#seatAvailabilityAlert')
                        .removeClass('d-none alert-info alert-success alert-warning')
                        .addClass('alert-danger');
                    $('#seatAvailabilityText').text('Unable to check seat availability. Please try again.');
                });
            }

            /* ── Cascade loaders (only active in CREATE mode) ───────── */
            function loadStreams(selectedValue = '', callback = null) {
                if (IS_EDIT_MODE) { if (typeof callback === 'function') callback(); return; }
                const courseId = $('#course_id').val();
                syncStudentTypeOptions();
                resetAcademicSelect('#stream_id',  'Select Branch');
                resetAcademicSelect('#class_id',   'Select Semester');
                resetAcademicSelect('#section_id', 'Select Section');
                resetSeatAvailability();
                if (!courseId) { if (typeof callback === 'function') callback(); return; }
                $.get(buildRoute(studentStreamsUrlTemplate, { courseId }), function (response) {
                    setSelectOptions('#stream_id', response, 'Select Branch', selectedValue);
                    if (typeof callback === 'function') callback();
                });
            }

            function loadClasses(selectedValue = '', callback = null) {
                if (IS_EDIT_MODE) { if (typeof callback === 'function') callback(); return; }
                const courseId = $('#course_id').val();
                const streamId = $('#stream_id').val();
                resetAcademicSelect('#class_id',   'Select Semester');
                resetAcademicSelect('#section_id', 'Select Section');
                resetSeatAvailability();
                if (!courseId || !streamId) { if (typeof callback === 'function') callback(); return; }
                $.get(buildRoute(studentClassesUrlTemplate, { courseId, streamId }), function (response) {
                    setSelectOptions('#class_id', response, 'Select Semester', selectedValue);
                    if (typeof callback === 'function') callback();
                });
            }

            function loadSections(selectedValue = '', callback = null) {
                const courseId = IS_EDIT_MODE ? $('input[name="course_id"]').val() : $('#course_id').val();
                const streamId = IS_EDIT_MODE ? $('input[name="stream_id"]').val() : $('#stream_id').val();
                const classId  = IS_EDIT_MODE ? $('input[name="class_id"]').val()  : $('#class_id').val();

                resetAcademicSelect('#section_id', 'Select Section');
                resetSeatAvailability();
                if (!courseId || !streamId || !classId) { if (typeof callback === 'function') callback(); return; }
                $.get(buildRoute(studentSectionsUrlTemplate, { courseId, streamId, classId }), function (response) {
                    setSelectOptions('#section_id', response, 'Select Section', selectedValue);
                    if (typeof callback === 'function') callback();
                });
            }

            function applyStudentTypeDefault() {
                if (IS_EDIT_MODE) return;
                syncStudentTypeOptions();
                const type        = $('#student_type').val();
                const classSelect = $('#class_id');
                if (!classSelect.children('option').length) return;
                if (type === 'lateral') {
                    const lateralOption = classSelect.find('option[data-order-index="3"]').first();
                    if (lateralOption.length) classSelect.val(lateralOption.val());
                } else if (type === 'regular') {
                    const regularOption = classSelect.find('option[data-order-index="1"]').first();
                    if (regularOption.length && !classSelect.val()) classSelect.val(regularOption.val());
                }
            }

            function getSelectedFeeStructureIds() {
                return $('input[name="fee_structure_ids[]"]:checked').map(function () {
                    return $(this).val();
                }).get();
            }

            function loadFeeStructures() {
                const academicYearId = IS_EDIT_MODE ? $('input[name="academic_year_id"]').val() : $('#academic_year_id').val();
                const courseId       = IS_EDIT_MODE ? $('input[name="course_id"]').val()        : $('#course_id').val();
                const streamId       = IS_EDIT_MODE ? $('input[name="stream_id"]').val()        : $('#stream_id').val();
                const classId        = IS_EDIT_MODE ? $('input[name="class_id"]').val()         : $('#class_id').val();
                const container      = $('#fee-structures-container');
                const selectedFeeStructureIds = getSelectedFeeStructureIds();

                if (academicYearId && courseId && streamId && classId) {
                    container.html('<div class="text-center p-4"><i class="feather-loader spin me-2"></i>Loading fee structures...</div>');
                    $.get('{{ route('admin.student_class_enrollments.ajax_fee_structures') }}', {
                        academic_year_id: academicYearId,
                        course_id:        courseId,
                        stream_id:        streamId,
                        class_id:         classId,
                        selected_fee_structure_ids: selectedFeeStructureIds,
                        @if (!empty($enrollment->id))
                            enrollment_id: {{ $enrollment->id }},
                            student_id:    {{ $enrollment->student_id ?? 'null' }},
                        @endif
                    }, function (response) {
                        container.html(response.html);
                    }).fail(function () {
                        container.html('<div class="alert alert-danger">Error loading fee structures</div>');
                    });
                } else {
                    container.html('<div class="alert alert-info"><i class="feather-info me-2"></i>Select Academic Year, Course, Branch and Semester first</div>');
                }
            }

            /* ── Change listeners (only fire cascade in create mode) ── */
            if (!IS_EDIT_MODE) {
                $('#course_id').on('change', function () {
                    syncStudentTypeOptions();
                    loadStreams('', loadFeeStructures);
                });

                $('#stream_id').on('change', function () {
                    loadClasses('', function () {
                        applyStudentTypeDefault();
                        $('#class_id').trigger('change');
                    });
                });

                $('#class_id').on('change', function () {
                    loadSections('', function () {
                        loadFeeStructures();
                        loadSeatAvailability();
                    });
                });

                $('#academic_year_id').on('change', function () {
                    loadFeeStructures();
                    loadSeatAvailability();
                });

                $('#student_type').on('change', function () {
                    applyStudentTypeDefault();
                    $('#class_id').trigger('change');
                    loadSeatAvailability();
                });
            }

            /* Section change always triggers seat check & fee load */
            $('#section_id').on('change', function () {
                loadFeeStructures();
                loadSeatAvailability();
            });

            /* ── Initial load ───────────────────────────────────────── */
            if (IS_EDIT_MODE) {
                // In edit mode: sections are already loaded server-side,
                // just load fee structures and seat availability
                loadSections(oldSectionId, function () {
                    loadFeeStructures();
                    loadSeatAvailability();
                });
            } else if (oldCourseId) {
                syncStudentTypeOptions();
                loadStreams(oldStreamId, function () {
                    loadClasses(oldClassId, function () {
                        applyStudentTypeDefault();
                        loadSections(oldSectionId, function () {
                            loadFeeStructures();
                            loadSeatAvailability();
                        });
                    });
                });
            } else {
                syncStudentTypeOptions();
                resetAcademicSelect('#stream_id',  'Select Branch');
                resetAcademicSelect('#class_id',   'Select Semester');
                resetAcademicSelect('#section_id', 'Select Section');
            }

            /* ── Hostel section toggle ──────────────────────────────── */
            function toggleHostelSection(val, animate) {
                if (val === 'hosteller') {
                    animate ? $('#hostel-section').slideDown(350) : $('#hostel-section').show();
                } else {
                    animate ? $('#hostel-section').slideUp(350) : $('#hostel-section').hide();
                }
            }
            toggleHostelSection($('#residence_type').val(), false);
            $('#residence_type').on('change', function () {
                toggleHostelSection($(this).val(), true);
            });

            function resetBedsContainer() {
                $('#beds-container').html(
                    '<div class="input-group"><span class="input-group-text"><i class="feather-minus-square"></i></span><select id="bed_id" name="bed_id" class="form-select" disabled><option>Select room first</option></select></div>'
                );
            }

            function loadBeds(roomId, selectedBedId = null) {
                const container = $('#beds-container');
                if (roomId) {
                    container.html('<div class="input-group"><span class="input-group-text"><i class="feather-loader spin"></i></span><select class="form-select" disabled><option>Loading...</option></select></div>');
                    $.get(roomBedsUrlTemplate.replace('__ROOM__', roomId), function (data) {
                        container.html(data);
                        if (selectedBedId) $('#bed_id').val(String(selectedBedId));
                        $('#bed_id').trigger('change');
                    }).fail(function () {
                        container.html('<div class="alert alert-danger small">Error loading beds</div>');
                    });
                } else {
                    resetBedsContainer();
                }
            }

            function loadRooms(hostelId, selectedRoomId = null, selectedBedId = null) {
                const container = $('#rooms-container');
                if (hostelId) {
                    container.html('<div class="input-group"><span class="input-group-text"><i class="feather-loader spin"></i></span><select class="form-select" disabled><option>Loading...</option></select></div>');
                    $.get(hostelRoomsUrlTemplate.replace('__HOSTEL__', hostelId), function (rooms) {
                        let opts = '<option value="">Select Room</option>';
                        rooms.forEach(r => {
                            opts += `<option value="${r.id}">${r.room_number} (${r.type}, ${r.max_beds} beds)</option>`;
                        });
                        container.html(`<div class="input-group"><span class="input-group-text"><i class="feather-grid"></i></span><select id="room_id" name="room_id" class="form-select">${opts}</select></div>`);
                        if (selectedRoomId) $('#room_id').val(String(selectedRoomId));
                        loadBeds($('#room_id').val(), selectedBedId);
                    }).fail(function () {
                        container.html('<div class="alert alert-danger small">Error loading rooms</div>');
                    });
                } else {
                    container.html('<div class="input-group"><span class="input-group-text"><i class="feather-grid"></i></span><select id="room_id" name="room_id" class="form-select" disabled><option>Select hostel first</option></select></div>');
                    resetBedsContainer();
                }
            }

            $('#hostel_id').on('change', function () { loadRooms($(this).val()); });
            $(document).on('change', '#room_id', function () { loadBeds($(this).val()); });
            $(document).on('change', '#bed_id',  function () {
                const amount = $(this).find('option:selected').data('amount');
                $('#hostel_fee').val(amount !== undefined ? amount : '');
            });

            if ($('#residence_type').val() === 'hosteller' && $('#hostel_id').val()) {
                loadRooms($('#hostel_id').val(), oldRoomId, oldBedId);
            }

            /* ── Form submit guard ──────────────────────────────────── */
            $('#combinedForm').on('submit', function (e) {
                if (!selectedSeatAvailable) {
                    e.preventDefault();
                    loadSeatAvailability();
                }
            });

            /* ── Form progress bar ──────────────────────────────────── */
            function updateProgress() {
                const inputs = document.querySelectorAll('#combinedForm input[required], #combinedForm select[required]');
                let filled = 0;
                inputs.forEach(i => { if (i.value.trim()) filled++; });
                const pct = Math.round((filled / inputs.length) * 100);
                const bar = document.getElementById('formProgress');
                if (bar) bar.style.width = pct + '%';
            }
            document.querySelectorAll('#combinedForm input, #combinedForm select').forEach(el => {
                el.addEventListener('change', updateProgress);
            });
            updateProgress();

        });

        function toggleSubCaste() {
            const val = document.getElementById('category').value;
            const box = document.getElementById('subCasteBox');
            box.style.display = val === 'other' ? 'block' : 'none';
            if (val !== 'other') document.getElementById('sub_caste').value = '';
        }
    </script>
@endsection