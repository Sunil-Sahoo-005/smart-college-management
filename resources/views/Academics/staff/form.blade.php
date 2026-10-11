@extends('admin_layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Academics.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            <!-- HEADER BAR -->
            <div class="content-area-header bg-white sticky-top">
                <div class="page-header-right ms-auto">
                    <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary">
                        <i class="feather-arrow-left me-2"></i>Back to List
                    </a>
                </div>
            </div>

            <div class="content-area-body">
                <!-- @include('elements.flash') -->
                {{-- Backend Validation Errors --}}
                @if ($errors->any())
                <div style="background:#FCEBEB;border:0.5px solid #F7C1C1;border-radius:10px;padding:16px 20px;margin-bottom:16px;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="feather-alert-circle" style="color:#A32D2D;font-size:16px;"></i>
                        <span style="font-weight:600;font-size:13.5px;color:#A32D2D;">Please fix the following errors:</span>
                    </div>
                    <ul class="mb-0 ps-3" style="font-size:13px;color:#7B2020;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

               @if (session('success'))
                <div data-auto-dismiss
                    style="background:#EAF3DE;border:0.5px solid #C0DD97;border-radius:10px;padding:14px 20px;
                    margin-bottom:16px;display:flex;align-items:center;gap:10px;">
                    <i class="feather-check-circle" style="color:#27500A;font-size:16px;"></i>
                    <span style="font-size:13px;font-weight:500;color:#27500A;">{{ session('success') }}</span>
                </div>
                @endif

                @if (session('error'))
                <div data-auto-dismiss
                    style="background:#FCEBEB;border:0.5px solid #F7C1C1;border-radius:10px;padding:14px 20px;
                    margin-bottom:16px;display:flex;align-items:center;gap:10px;">
                    <i class="feather-x-circle" style="color:#A32D2D;font-size:16px;"></i>
                    <span style="font-size:13px;font-weight:500;color:#A32D2D;">{{ session('error') }}</span>
                </div>
                @endif

                <form method="POST"
                      action="{{ route('admin.staff.store') }}"
                      enctype="multipart/form-data"
                      id="staffForm">
                    @csrf

                    @if(!empty($staff->id))
                        <input type="hidden" name="id" value="{{ $staff->id }}">
                    @endif

                    <div class="card stretch stretch-full shadow-sm border-0">

                        
                        <!-- CARD HEADER -->
                        <div class="card-header p-4" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
                           <div class="d-flex align-items-center flex-wrap gap-3 w-100">
                                <div>
                                    <h4 class="mb-1 fw-bold text-white">
                                        {{ $staff->first_name ??  'New'  }} {{ $staff->last_name ?? 'Staff' }}'s Profile
                                    </h4>
                                    <small class="text-white opacity-75">Complete all sections to save</small>
                                </div>
                                <div class="d-flex align-items-center gap-2 ms-auto">
                                    <!-- <div>
                                        <div class="text-white opacity-75 mb-1" style="font-size:11px;">Form Progress</div>
                                        <div class="progress" style="width:160px;height:6px;background:rgba(255,255,255,0.3);">
                                            <div class="progress-bar bg-white" id="formProgress" style="width:0%;transition:width 0.3s;"></div>
                                        </div>
                                    </div> -->
                                    <button type="button" class="btn btn-light btn-sm px-4" id="previewBtn"
                                        style="border-radius:8px;font-weight:500;font-size:13px;">
                                        <i class="feather-eye me-1"></i>Preview
                                    </button>
                                    <button type="submit" class="btn btn-warning btn-sm px-3"
                                        style="border-radius:8px;font-weight:600;font-size:13px;color:#000;">
                                        <i class="feather-save me-1"></i>Save Staff
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- CARD BODY -->
                        <div class="card-body p-4">

                            <!-- PHOTO SECTION -->
                            <div class="row mb-5">
                                <div class="col-lg-3 text-center">
                                    <div class="photo-upload position-relative mx-auto mb-3"
                                         style="width:150px;height:150px;">
                                        @if(!empty($staff->photo_url))
                                            <img src="{{ asset('storage/'.$staff->photo_url) }}"
                                                 class="img-fluid rounded-circle border border-3 border-white shadow-sm"
                                                 id="photoPreview"
                                                 style="width:100%;height:100%;object-fit:cover;">
                                        @else
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center"
                                                 id="photoPlaceholder"
                                                 style="width:100%;height:100%;border:3px dashed #dee2e6;">
                                                <i class="feather-camera text-muted fs-3"></i>
                                            </div>
                                        @endif
                                        <input type="file" name="staff_photo" id="staffPhoto" accept="image/*" class="d-none">
                                    </div>
                                    <label for="staffPhoto" class="btn btn-outline-primary btn-sm w-100">
                                        <i class="feather-upload me-1"></i>Change Photo
                                    </label>
                                </div>

                                <div class="col-lg-9">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                                            {{-- First Name --}}
                                            <input type="text" name="first_name" 
                                                class="form-control @error('first_name') is-invalid @enderror" required
                                                value="{{ old('first_name', $staff->first_name ?? '') }}">
                                            @error('first_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Last Name *</label>
                                            {{-- Last Name --}}
                                            <input type="text" name="last_name" 
                                                class="form-control @error('last_name') is-invalid @enderror" required
                                                value="{{ old('last_name', $staff->last_name ?? '') }}">
                                            @error('last_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TABS -->
                            <ul class="nav nav-tabs nav-tabs-custom bg-light rounded-top mb-0">
                                <li>
                                    {{-- Personal Info tab --}}
                                    <button type="button" class="nav-link active fw-semibold"
                                            data-bs-toggle="tab"
                                            data-bs-target="#personal">
                                        <i class="feather-user me-1"></i>Personal Info
                                        <span class="text-danger ms-1">*</span>
                                    </button>
                                </li>
                                <li>
                                    {{-- Professional tab --}}
                                <button type="button" class="nav-link fw-semibold"
                                        data-bs-toggle="tab"
                                        data-bs-target="#professional">
                                    <i class="feather-briefcase me-1"></i>Professional
                                    <span class="text-danger ms-1">*</span>
                                </button>
                                </li>
                                <li>
                                    <button type="button" class="nav-link fw-semibold"
                                            data-bs-toggle="tab"
                                            data-bs-target="#bank">
                                        <i class="feather-credit-card me-1"></i>Bank
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="nav-link fw-semibold"
                                            data-bs-toggle="tab"
                                            data-bs-target="#documents">
                                        <i class="feather-file-text me-1"></i>Documents
                                    </button>
                                </li>
                            </ul>

                            <!-- TAB CONTENT -->
                            <div class="tab-content bg-white rounded-bottom p-4 border shadow-sm">

                               <div class="tab-pane fade show active" id="personal">

                            {{-- Contact Details --}}
                            <div class="mb-4">
                                <div class="mb-3 pb-2 border-bottom">
                                    <span class="fw-semibold text-secondary small text-uppercase">Contact Details</span>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-uppercase text-muted">Email Address</label>
                                        {{-- Email --}}
                                        <input type="email" name="email" 
                                            class="form-control @error('email') is-invalid @enderror"
                                            placeholder="staff@school.com"
                                            value="{{ old('email', $staff->email ?? '') }}" autocomplete="off">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-uppercase text-muted">
                                            Phone Number <span class="text-danger">*</span>
                                        </label>
                                        {{-- Phone --}}
                                        <input type="text" name="phone" 
                                            class="form-control @error('phone') is-invalid @enderror" required
                                            placeholder="10-digit mobile number" autocomplete="off"
                                            value="{{ old('phone', $staff->phone ?? '') }}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-uppercase text-muted">Whatsapp Number</label>
                                        <input type="text" name="alternate_phone" class="form-control"
                                            placeholder="Optional" autocomplete="off"
                                            value="{{ old('alternate_phone', $staff->alternate_phone ?? '') }}">
                                    </div>
                                </div>
                            </div>

                            {{-- Personal Details --}}
                            <div class="mb-4">
                                <div class="mb-3 pb-2 border-bottom">
                                    <span class="fw-semibold text-secondary small text-uppercase">Personal Details</span>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold text-uppercase text-muted">Gender</label>
                                        <select name="gender" class="form-select">
                                            <option value="">Select gender</option>
                                            @foreach(['male','female','other'] as $g)
                                                <option value="{{ $g }}" @selected(old('gender', $staff->gender ?? '') == $g)>
                                                    {{ ucfirst($g) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold text-uppercase text-muted">Date of Birth</label>
                                        <input type="date" name="dob" class="form-control"
                                            value="{{ old('dob', optional($staff)->dob?->format('Y-m-d')) }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold text-uppercase text-muted">Blood Group</label>
                                        <select name="blood_group" class="form-select">
                                            <option value="">Select</option>
                                            @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                                                <option value="{{ $bg }}" @selected(old('blood_group', $staff->blood_group ?? '') == $bg)>
                                                    {{ $bg }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                        <select name="marital_status" class="form-select">
                                            <option value="">Select</option>
                                            @foreach(['Single','Married','Divorced','Widowed'] as $ms)
                                                <option value="{{ $ms }}" @selected(old('marital_status', $staff->marital_status ?? '') == $ms)>
                                                    {{ $ms }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Address --}}
                            <div class="mb-2">
                                <div class="mb-3 pb-2 border-bottom">
                                    <span class="fw-semibold text-secondary small text-uppercase">Address</span>
                                </div>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold text-uppercase text-muted">Permanent Address</label>
                                        <textarea name="permanent_address" class="form-control" rows="3"
                                                placeholder="Full permanent address including city, state and PIN code">{{ old('permanent_address', $staff->permanent_address ?? '') }}</textarea>
                                    </div>
                                </div>
                            </div>

                        </div>

                                <!-- PROFESSIONAL -->
                                <div class="tab-pane fade" id="professional">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Designation *</label>
                                            {{-- Designation --}}
                                            <select name="designation_id" 
                                                    class="form-select @error('designation_id') is-invalid @enderror" required>
                                                <option value="">Select Designation</option>
                                                @foreach($designations as $d)
                                                    <option value="{{ $d->id }}"
                                                        @selected(old('designation_id',$staff->designation_id ?? '')==$d->id)>
                                                        {{ $d->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('designation_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Department *</label>
                                            {{-- Department --}}
                                            <select name="department_id" 
                                                    class="form-select @error('department_id') is-invalid @enderror" required>
                                                <option value="">Select Department</option>
                                                @foreach($departments as $dep)
                                                    <option value="{{ $dep->id }}"
                                                        @selected(old('department_id',$staff->department_id ?? '')==$dep->id)>
                                                        {{ $dep->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                                @error('department_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                        </div>
                                       {{-- BROKEN part ko replace karo --}}
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Employee ID</label>
                                            <input type="text" name="employee_id" class="form-control"
                                                placeholder="e.g. EMP-001" autocomplete="off"
                                                value="{{ old('employee_id', $staff->employee_id ?? '') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">BPUT ID</label>
                                            <input type="text" name="bput_id" class="form-control"
                                                placeholder="e.g. BPUT-001" autocomplete="off"
                                                value="{{ old('bput_id', $staff->bput_id ?? '') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Employment Type</label>
                                            <select name="employment_type" class="form-select">
                                                <option value="">Select Type</option>
                                                @foreach(['Full-Time','Part-Time','Contract','Guest','Visiting'] as $type)
                                                    <option value="{{ $type }}"
                                                        @selected(old('employment_type', $staff->employment_type ?? '') == $type)>
                                                        {{ $type }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Joining Date *</label>                                           
                                            {{-- Joining Date --}}
                                            <input type="date" name="joining_date" 
                                                class="form-control @error('joining_date') is-invalid @enderror" required
                                                value="{{ old('joining_date', optional($staff)->joining_date?->format('Y-m-d')) }}" autocomplete="off">
                                            @error('joining_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Status *</label>
                                            {{-- Status --}}
                                            <select name="status" 
                                                    class="form-select @error('status') is-invalid @enderror" required>
                                                <option value="active"   @selected(old('status',$staff->status ?? '')=='active')>Active</option>
                                                <option value="resigned" @selected(old('status',$staff->status ?? '')=='resigned')>Resigned</option>
                                                <option value="retired"  @selected(old('status',$staff->status ?? '')=='retired')>Retired</option>
                                                <option value="on_leave" @selected(old('status',$staff->status ?? '')=='on_leave')>On Leave</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Leaving Date</label>
                                            <input type="date" name="leaving_date" class="form-control"
                                                   value="{{ old('leaving_date', optional($staff)->leaving_date?->format('Y-m-d')) }}" autocomplete="off">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Experience (Years)</label>
                                            <select name="experience" class="form-select">
                                                <option value="">Select</option>
                                                @for($i = 0; $i <= 40; $i++)
                                                    <option value="{{ $i }}"
                                                        @selected(old('experience', $staff->experience ?? '') == $i)>
                                                        {{ $i }} {{ $i == 1 ? 'Year' : 'Years' }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Qualification</label>
                                            <select name="qualification" class="form-select">
                                                <option value="">Select</option>
                                                @foreach(['10th','12th','Diploma','B.A','B.Com','B.Sc','B.Tech','B.Ed','M.A','M.Com','M.Sc','M.Tech','M.Ed','MBA','MCA','Ph.D','Other'] as $q)
                                                    <option value="{{ $q }}"
                                                        @selected(old('qualification', $staff->qualification ?? '') == $q)>
                                                        {{ $q }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Religion</label>
                                            <select name="religion" class="form-select">
                                                <option value="">Select</option>
                                                @foreach(['Hindu','Muslim','Christian','Sikh','Buddhist','Jain','Other'] as $r)
                                                    <option value="{{ $r }}"
                                                        @selected(old('religion', $staff->religion ?? '') == $r)>
                                                        {{ $r }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Nationality</label>
                                            <select name="nationality" class="form-select">
                                                <option value="">Select</option>
                                                @foreach(['Indian','Other'] as $n)
                                                    <option value="{{ $n }}"
                                                        @selected(old('nationality', $staff->nationality ?? '') == $n)>
                                                        {{ $n }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Caste</label>
                                            <select name="category" class="form-select">
                                                <option value="">Select</option>
                                                @foreach(['General','OBC','SC','ST','EWS'] as $cat)
                                                    <option value="{{ $cat }}"
                                                        @selected(old('category', $staff->category ?? '') == $cat)>
                                                        {{ $cat }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">PAN</label>
                                            <input type="text" name="pan" class="form-control"
                                                   placeholder="e.g. ABCDE1234F" autocomplete="off"
                                                   style="text-transform:uppercase;"
                                                   maxlength="10"
                                                   value="{{ old('pan', $staff->pan ?? '') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Aadhaar</label>
                                            <input type="text" name="aadhaar" class="form-control"
                                                   placeholder="12-digit Aadhaar" autocomplete="off"
                                                   maxlength="12"
                                                   value="{{ old('aadhaar', $staff->aadhaar ?? '') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">PF Number</label>
                                            <input type="text" name="pf_number" class="form-control"
                                                   placeholder="e.g. PF123456"
                                                   autocomplete="off"
                                                   value="{{ old('pf_number', $staff->pf_number ?? '') }}">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label fw-semibold">Current Address</label>
                                            <textarea name="address" class="form-control" rows="2"
                                                      placeholder="Full current address" autocomplete="off">{{ old('address', $staff->address ?? '') }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- BANK -->
                                <div class="tab-pane fade" id="bank">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Account Holder Name</label>
                                            <input type="text" name="bank_account_name" class="form-control"
                                                   placeholder="As per bank records"
                                                   autocomplete="off"
                                                   value="{{ old('bank_account_name',$staff->bank_account_name ?? '') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Account Number</label>
                                            <input type="text" name="bank_account_number" class="form-control"
                                                   placeholder="e.g. 1234567890"
                                                   autocomplete="off"
                                                   value="{{ old('bank_account_number',$staff->bank_account_number ?? '') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">IFSC Code</label>
                                            <input type="text" name="ifsc" class="form-control"
                                                   placeholder="e.g. SBIN0001234"
                                                   style="text-transform:uppercase;"
                                                   autocomplete="off"
                                                   value="{{ old('ifsc',$staff->ifsc ?? '') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Bank Name</label>
                                            <select name="bank_name" class="form-select">
                                                <option value="">Select Bank</option>
                                                @foreach(['State Bank of India','Bank of Baroda','Punjab National Bank','Canara Bank','Union Bank of India','Bank of India','Indian Bank','Central Bank of India','HDFC Bank','ICICI Bank','Axis Bank','Kotak Mahindra Bank','IndusInd Bank','Yes Bank','IDFC First Bank','Federal Bank','Other'] as $bank)
                                                    <option value="{{ $bank }}"
                                                        @selected(old('bank_name', $staff->bank_name ?? '') == $bank)>
                                                        {{ $bank }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Branch Name</label>
                                            <input type="text" name="branch_name" class="form-control"
                                                   placeholder="e.g. Main Branch, Bhubaneswar"
                                                   autocomplete="off"
                                                   value="{{ old('branch_name',$staff->branch_name ?? '') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Account Type</label>
                                            <select name="account_type" class="form-select">
                                                <option value="">Select</option>
                                                @foreach(['Savings','Current'] as $type)
                                                    <option value="{{ $type }}"
                                                        @selected(old('account_type', $staff->account_type ?? '') == $type)>
                                                        {{ $type }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- DOCUMENTS -->
                                <div class="tab-pane fade" id="documents">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <div>
                                            <h6 class="fw-semibold mb-0">Staff Documents</h6>
                                            <small class="text-muted">Upload identity, educational & professional documents</small>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-primary" id="addDocBtn">
                                            <i class="feather-plus me-1"></i>Add Document
                                        </button>
                                    </div>

                                    {{-- Existing documents --}}
                                    <div id="documentsList">
                                        @if(!empty($staff) && !empty($staff->id) && $staff->documents?->count())
                                            @foreach($staff->documents as $doc)
                                            <div class="row g-3 align-items-center border rounded p-3 mb-2 bg-light doc-row">
                                                <div class="col-md-3">
                                                    <label class="form-label text-muted small">Document Type</label>
                                                    <input type="text" class="form-control form-control-sm"
                                                          value="{{ $doc->doc_type }}" readonly>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label text-muted small">Note</label>
                                                    <input type="text" class="form-control form-control-sm"
                                                           value="{{ $doc->note ?? '—' }}" readonly>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label text-muted small">Uploaded On</label>
                                                    <p class="mb-0 small fw-semibold">{{ $doc->created_at->format('d M Y') }}</p>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label text-muted small">File</label>
                                                    <a href="{{ Storage::url($doc->file_path) }}" target="_blank"
                                                       class="btn btn-sm btn-outline-primary w-100">
                                                        <i class="feather-eye me-1"></i>View File
                                                    </a>
                                                </div>
                                                <div class="col-md-1 d-flex align-items-end justify-content-end">
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-danger delete-existing-doc"
                                                            data-id="{{ $doc->id }}" title="Delete">
                                                        <i class="feather-trash-2"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            @endforeach
                                        @endif
                                    </div>

                                    {{-- New document rows --}}
                                    <div id="newDocumentRows"></div>

                                    <div class="alert alert-light border mt-3 mb-0 py-2">
                                        <small class="text-muted">
                                            <i class="feather-info me-1 text-primary"></i>
                                            Supported formats: PDF, JPG, PNG, DOC, DOCX &nbsp;|&nbsp; Max size: 5MB per file
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                 <!-- PREVIEW MODAL -->
                                    <div class="modal fade" id="previewModal" tabindex="-1">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
                                                <div class="modal-header border-0 pb-0 px-4 pt-4">
                                                    <h6 class="fw-bold mb-0">Staff Preview</h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4" id="previewBody">
                                                    <!-- Dynamic content -->
                                                </div>
                                                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                                    <button type="button" class="btn btn-primary btn-sm" id="previewSaveBtn">
                                                        <i class="feather-save me-1"></i>Save Staff
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

<style>
.form-control.is-invalid, .form-select.is-invalid {
    border-color: #dc3545 !important;
    background-image: none !important;
}
.form-control.is-valid, .form-select.is-valid {
    border-color: #198754 !important;
    background-image: none !important;
}
.form-label .text-danger,
.form-label:after {
    color: #dc3545 !important;
    font-weight: 600;
}
.nav-link .text-danger {
    color: #dc3545 !important;
    font-weight: 700;
}
.invalid-feedback {
    display: block !important;
    font-size: 11.5px;
    color: #dc3545;
    margin-top: 4px;
}
/* Required star on labels */
label.fw-semibold::after,
label.form-label.fw-semibold[data-required]::after {
    content: none;
}
/* Tab error indicator */
.tab-has-error {
    color: #dc3545 !important;
    position: relative;
}
.tab-has-error::after {
    content: '';
    width: 6px;
    height: 6px;
    background: #dc3545;
    border-radius: 50%;
    position: absolute;
    top: 6px;
    right: 2px;
}
</style>

<script>
// ========================
// PHOTO PREVIEW
// ========================
document.getElementById('staffPhoto').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        let img = document.getElementById('photoPreview');
        if (!img) {
            img = document.createElement('img');
            img.id = 'photoPreview';
            img.className = 'img-fluid rounded-circle border border-3 border-white shadow-sm';
            img.style.cssText = 'width:100%;height:100%;object-fit:cover;';
            document.getElementById('photoPlaceholder')?.remove();
            document.querySelector('.photo-upload').prepend(img);
        }
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
});

// ========================
// BACKEND ERROR - SAHI TAB OPEN KARO
// ========================
document.addEventListener('DOMContentLoaded', function() {
    const firstInvalidField = document.querySelector('.is-invalid');
    if (firstInvalidField) {
        const pane = firstInvalidField.closest('.tab-pane');
        if (pane) {
            const tabBtn = document.querySelector(`[data-bs-target="#${pane.id}"]`);
            if (tabBtn) new bootstrap.Tab(tabBtn).show();
            setTimeout(() => {
                firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 400);
        }
    }

    // Success/error alerts auto-hide
    setTimeout(() => {
        document.querySelectorAll('[data-auto-dismiss]').forEach(el => el.remove());
    }, 5000);
});

// ========================
// DOCUMENTS
// ========================
let docIndex = {{ $staff->documents?->count() ?? 0 }};

const DOC_TYPES = [
    '--- Identity Documents ---','Aadhaar Card','PAN Card','Passport','Voter ID','Driving License',
    '--- Educational Certificates ---','10th Marksheet','12th Marksheet','Diploma Certificate',
    'Graduation Certificate','Post Graduation Certificate','Ph.D Certificate','B.Ed Certificate',
    'M.Ed Certificate','MBA Certificate','MCA Certificate',
    '--- Professional Documents ---','Resume / CV','Appointment Letter','Experience Letter',
    'Relieving Letter','Salary Slip','NOC Certificate','Character Certificate',
    '--- Other ---','Medical Certificate','Caste Certificate','Income Certificate','Other',
];

document.getElementById('addDocBtn').addEventListener('click', function() {
    const container = document.getElementById('newDocumentRows');
    const row = document.createElement('div');
    row.className = 'row g-3 align-items-center border rounded p-3 mb-2 doc-row';

    const options = DOC_TYPES.map(opt => {
        if (opt.startsWith('---')) {
            const label = opt.replace(/---/g,'').trim();
            return `<option value="" disabled class="fw-bold text-muted">── ${label} ──</option>`;
        }
        return `<option value="${opt}">${opt}</option>`;
    }).join('');

    row.innerHTML = `
        <div class="col-md-3">
            <label class="form-label text-muted small">Document Type *</label>
            <select name="documents[${docIndex}][type]" class="form-select form-select-sm" required>
                <option value="">Select Type</option>${options}
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label text-muted small">Upload File *</label>
            <input type="file" name="documents[${docIndex}][file]"
                   class="form-control form-control-sm"
                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required>
        </div>
        <div class="col-md-3">
            <label class="form-label text-muted small">Note (Optional)</label>
            <input type="text" name="documents[${docIndex}][note]"
                   class="form-control form-control-sm" placeholder="e.g. Issued 2020">
        </div>
        <div class="col-md-2 d-flex align-items-end gap-1">
            <button type="button" class="btn btn-sm btn-outline-danger remove-doc-row w-100">
                <i class="feather-trash-2 me-1"></i>Remove
            </button>
        </div>`;
    container.appendChild(row);
    docIndex++;
});

// Remove doc row
document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-doc-row')) {
        e.target.closest('.doc-row').remove();
    }
});

// ========================
// PREVIEW BUTTON
// ========================
document.getElementById('previewBtn').addEventListener('click', function() {
    const get = name => {
        const el = document.querySelector(`[name="${name}"]`);
        return el ? el.value : '';
    };

    const firstName = get('first_name') || '—';
    const lastName  = get('last_name')  || '—';
    const desigEl   = document.querySelector('[name="designation_id"]');
    const deptEl    = document.querySelector('[name="department_id"]');
    const desigName = desigEl?.options[desigEl.selectedIndex]?.text || '—';
    const deptName  = deptEl?.options[deptEl.selectedIndex]?.text   || '—';
    const status    = get('status') || '—';

    const initials  = ((firstName.charAt(0)||'')+(lastName.charAt(0)||'')).toUpperCase();
    const colors    = ['#EEEDFE|#3C3489','#E1F5EE|#085041','#FAECE7|#712B13','#E6F1FB|#0C447C'];
    const [bg, tc]  = colors[initials.charCodeAt(0) % colors.length].split('|');
    const avatarHtml = `<div style="width:72px;height:72px;border-radius:16px;background:${bg};color:${tc};
        display:flex;align-items:center;justify-content:center;font-weight:600;font-size:22px;">${initials}</div>`;

    const isActive   = status === 'active';
    const badgeHtml  = `<span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;
        border-radius:20px;background:${isActive?'#EAF3DE':'#FAEEDA'};
        color:${isActive?'#27500A':'#633806'};font-size:11.5px;font-weight:500;">
        <span style="width:6px;height:6px;border-radius:50%;background:${isActive?'#639922':'#BA7517'};display:inline-block;"></span>
        ${status.charAt(0).toUpperCase()+status.slice(1)}
    </span>`;

    const row = (label, val) => `
        <div style="display:flex;justify-content:space-between;padding:7px 0;
            border-bottom:0.5px solid #f0f0f0;gap:12px;">
            <span style="font-size:12px;color:#6c757d;">${label}</span>
            <span style="font-size:12.5px;font-weight:500;text-align:right;">${val}</span>
        </div>`;

    const section = (title, rows) => `
        <div style="margin-bottom:20px;">
            <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;
                color:#6c757d;margin-bottom:8px;padding-bottom:6px;border-bottom:1.5px solid #e9ecef;">
                ${title}
            </div>${rows}
        </div>`;

    const bankEl = document.querySelector('[name="bank_name"]');

    document.getElementById('previewBody').innerHTML = `
        <div style="background:#f8f9fa;border-radius:12px;padding:20px;margin-bottom:20px;
            display:flex;align-items:center;gap:16px;">
            ${avatarHtml}
            <div>
                <div style="font-weight:600;font-size:18px;margin-bottom:4px;">${firstName} ${lastName}</div>
                <div style="font-size:13px;color:#6c757d;margin-bottom:8px;">${desigName} &bull; ${deptName}</div>
                ${badgeHtml}
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
            <div>
                ${section('Contact',
                    row('Email',    get('email')     || '—') +
                    row('Phone',    get('phone')     || '—') +
                    row('WhatsApp', get('alternate_phone') || '—')
                )}
                ${section('Personal',
                    row('Gender',        get('gender')         || '—') +
                    row('Date of Birth', get('dob')            || '—') +
                    row('Blood Group',   get('blood_group')    || '—') +
                    row('Marital',       get('marital_status') || '—')
                )}
            </div>
            <div>
                ${section('Professional',
                    row('Employee ID',      get('employee_id')     || '—') +
                    row('Employment Type',  get('employment_type') || '—') +
                    row('Joining Date',     get('joining_date')    || '—') +
                    row('Qualification',    get('qualification')   || '—')
                )}
                ${section('Bank',
                    row('Account Holder', get('bank_account_name')   || '—') +
                    row('Account No',     get('bank_account_number') || '—') +
                    row('IFSC',           get('ifsc')                || '—') +
                    row('Bank',           bankEl?.options[bankEl.selectedIndex]?.text || '—')
                )}
            </div>
        </div>`;

    new bootstrap.Modal(document.getElementById('previewModal')).show();
});

document.getElementById('previewSaveBtn').addEventListener('click', function() {
    bootstrap.Modal.getInstance(document.getElementById('previewModal')).hide();
    document.getElementById('staffForm').submit();
});
</script>
@endsection