@extends('admin_layout')

@section('content')
    <div class="nxl-content">
        <!-- [ page-header ] start -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">{{ !empty($tenant->id) ? 'Edit Unit' : 'Create Unit' }}</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.tenants.index') }}">Units</a></li>
                    <li class="breadcrumb-item">{{ !empty($tenant->id) ? 'Edit Unit' : 'Create Unit' }}</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <div class="page-header-right-items">
                    <div class="d-flex d-md-none">
                        <a href="javascript:void(0)" class="page-header-right-close-toggle">
                            <i class="feather-arrow-left me-2"></i>
                            <span>Back</span>
                        </a>
                    </div>
                    <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                        <a href="{{ route('admin.tenants.index') }}" class="btn btn-secondary">
                            <i class="feather-arrow-left me-2"></i>
                            <span>Back to List</span>
                        </a>
                    </div>
                </div>
                <div class="d-md-none d-flex align-items-center">
                    <a href="javascript:void(0)" class="page-header-right-open-toggle">
                        <i class="feather-align-right fs-20"></i>
                    </a>
                </div>
            </div>
        </div>
        <!-- [ page-header ] end -->

        <!-- [ Main Content ] start -->
        @include('elements.flash')
        
        <div class="main-content">
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('admin.tenants.store') }}" method="POST">
                        @csrf
                        @if (!empty($tenant->id))
                            <input type="hidden" name="id" value="{{ $tenant->id }}">
                        @endif

                        <div class="card stretch stretch-full">
                            <div class="card-body general-info">
                                <div class="mb-5 d-flex align-items-center justify-content-between">
                                    <h5 class="fw-bold mb-0 me-4">
                                        <span class="d-block mb-2">Unit Info :</span>
                                        <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                            General information for your unit (school)
                                        </span>
                                    </h5>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="name" class="fw-semibold">Name: <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-building"></i></div>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name" value="{{ old('name', $tenant->name ?? '') }}"
                                                placeholder="Unit Name" required autocomplete="off">
                                        </div>
                                        @error('name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="email" class="fw-semibold">Email:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-mail"></i></div>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                id="email" name="email"
                                                value="{{ old('email', $tenant->email ?? '') }}" placeholder="Email" autocomplete="off">
                                        </div>
                                        @error('email')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Email must be unique across all tenants</small>
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="phone" class="fw-semibold">Phone:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-phone"></i></div>
                                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                                id="phone" name="phone"
                                                value="{{ old('phone', $tenant->phone ?? '') }}" placeholder="Phone" autocomplete="off">
                                        </div>
                                        @error('phone')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Phone number must be unique across all tenants</small>
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="address" class="fw-semibold">Address:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3"
                                            placeholder="Address" autocomplete="off">{{ old('address', $tenant->address ?? '') }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="logo_url" class="fw-semibold">Logo URL:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-image"></i></div>
                                            <input type="url" class="form-control @error('logo_url') is-invalid @enderror"
                                                id="logo_url" name="logo_url"
                                                value="{{ old('logo_url', $tenant->logo_url ?? '') }}" 
                                                placeholder="https://example.com/logo.png" autocomplete="off">
                                        </div>
                                        @error('logo_url')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="timezone" class="fw-semibold">Timezone: <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-clock"></i></div>
                                            <select class="form-select @error('timezone') is-invalid @enderror"
                                                id="timezone" name="timezone" required autocomplete="off">
                                                <option value="">Select Timezone</option>
                                                <option value="Asia/Kolkata"
                                                    {{ old('timezone', $tenant->timezone ?? '') == 'Asia/Kolkata' ? 'selected' : '' }}>
                                                    Asia/Kolkata (IST)
                                                </option>
                                                <option value="Asia/Dubai"
                                                    {{ old('timezone', $tenant->timezone ?? '') == 'Asia/Dubai' ? 'selected' : '' }}>
                                                    Asia/Dubai (GST)
                                                </option>
                                            </select>
                                        </div>
                                        @error('timezone')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="currency" class="fw-semibold">Currency: <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-dollar-sign"></i></div>
                                            <select class="form-select @error('currency') is-invalid @enderror"
                                                id="currency" name="currency" required autocomplete="off">
                                                <option value="">Select Currency</option>
                                                <option value="INR"
                                                    {{ old('currency', $tenant->currency ?? '') == 'INR' ? 'selected' : '' }}>
                                                    INR (₹)
                                                </option>
                                                <option value="USD"
                                                    {{ old('currency', $tenant->currency ?? '') == 'USD' ? 'selected' : '' }}>
                                                    USD ($)
                                                </option>
                                                <option value="EUR"
                                                    {{ old('currency', $tenant->currency ?? '') == 'EUR' ? 'selected' : '' }}>
                                                    EUR (€)
                                                </option>
                                            </select>
                                        </div>
                                        @error('currency')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="academic_year_start_month" class="fw-semibold">Academic Year Start
                                            Month: <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-calendar"></i></div>
                                            <select
                                                class="form-select @error('academic_year_start_month') is-invalid @enderror"
                                                id="academic_year_start_month" name="academic_year_start_month" required autocomplete="off">
                                                <option value="">Select Month</option>
                                                @for ($i = 1; $i <= 12; $i++)
                                                    <option value="{{ $i }}"
                                                        {{ old('academic_year_start_month', $tenant->academic_year_start_month ?? 4) == $i ? 'selected' : '' }}>
                                                        {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                        @error('academic_year_start_month')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="status" class="fw-semibold">Status:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="status"
                                                name="status" value="1"
                                                {{ old('status', $tenant->status ?? 'active') == 'active' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="status">Active</label>
                                        </div>
                                        <small class="text-muted d-block mt-1">Inactive tenants cannot access the system</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
@endsection