@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Accountants.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">
                            {{ !empty($scholarship) ? 'Edit Scholarship' : 'New Scholarship' }}
                        </span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                            <a href="{{ route('scholarships.index') }}" class="btn btn-secondary">
                                <i class="feather-arrow-left me-2"></i>
                                <span>Back to List</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="content-area-body">
                    @include('elements.flash')

                    <form action="{{ route('scholarships.store') }}" method="POST">
                        @csrf
                        @if (!empty($scholarship))
                            <input type="hidden" name="id" value="{{ $scholarship->id }}">
                        @endif

                        <div class="card stretch stretch-full">
                            <div class="card-body general-info">
                                <div class="mb-5 d-flex align-items-center justify-content-between">
                                    <h5 class="fw-bold mb-0 me-4">
                                        <span class="d-block mb-2">Scholarship Info :</span>
                                        <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                            Configure college concessions, government scholarships, caste category rules, and rates.
                                        </span>
                                    </h5>
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>

                                <!-- Scholarship Name -->
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="name" class="fw-semibold">Scholarship Name <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-award"></i></div>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                   id="name" name="name"
                                                   value="{{ old('name', $scholarship->name ?? '') }}"
                                                   placeholder="e.g., SC/ST Concession, Merit Scholarship" required autocomplete="off">
                                        </div>
                                        @error('name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Type (Percentage vs Fixed) -->
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="type" class="fw-semibold">Concession Type <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-activity"></i></div>
                                            <select class="form-select @error('type') is-invalid @enderror" 
                                                    id="type" name="type" required>
                                                <option value="">Select Type</option>
                                                <option value="percentage" {{ old('type', $scholarship->type ?? '') == 'percentage' ? 'selected' : '' }}>
                                                    Percentage (%)
                                                </option>
                                                <option value="fixed" {{ old('type', $scholarship->type ?? '') == 'fixed' ? 'selected' : '' }}>
                                                    Fixed Amount (₹)
                                                </option>
                                            </select>
                                        </div>
                                        @error('type')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Value -->
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="value" class="fw-semibold">Value <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-hash"></i></div>
                                            <input type="number" step="0.01" class="form-control @error('value') is-invalid @enderror"
                                                   id="value" name="value"
                                                   value="{{ old('value', $scholarship->value ?? '') }}"
                                                   placeholder="e.g., 50 or 500" required autocomplete="off">
                                        </div>
                                        <div class="form-text text-muted" id="valueHelp">Select concession type first.</div>
                                        @error('value')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Source -->
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="source" class="fw-semibold">Source <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-globe"></i></div>
                                            <select class="form-select @error('source') is-invalid @enderror" 
                                                    id="source" name="source" required>
                                                <option value="">Select Source</option>
                                                <option value="college" {{ old('source', $scholarship->source ?? '') == 'college' ? 'selected' : '' }}>
                                                    College
                                                </option>
                                                <option value="government" {{ old('source', $scholarship->source ?? '') == 'government' ? 'selected' : '' }}>
                                                    Government
                                                </option>
                                                <option value="trust" {{ old('source', $scholarship->source ?? '') == 'trust' ? 'selected' : '' }}>
                                                    Trust
                                                </option>
                                                <option value="nsp" {{ old('source', $scholarship->source ?? '') == 'nsp' ? 'selected' : '' }}>
                                                    National Scholarship Portal (NSP)
                                                </option>
                                            </select>
                                        </div>
                                        @error('source')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Caste Category Mapping -->
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="caste_category" class="fw-semibold">Caste Category <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-users"></i></div>
                                            <select class="form-select @error('caste_category') is-invalid @enderror" 
                                                    id="caste_category" name="caste_category" required>
                                                <option value="ALL" {{ old('caste_category', $scholarship->caste_category ?? '') == 'ALL' ? 'selected' : '' }}>
                                                    ALL (Apply to all categories)
                                                </option>
                                                <option value="SC" {{ old('caste_category', $scholarship->caste_category ?? '') == 'SC' ? 'selected' : '' }}>
                                                    SC
                                                </option>
                                                <option value="ST" {{ old('caste_category', $scholarship->caste_category ?? '') == 'ST' ? 'selected' : '' }}>
                                                    ST
                                                </option>
                                                <option value="OBC" {{ old('caste_category', $scholarship->caste_category ?? '') == 'OBC' ? 'selected' : '' }}>
                                                    OBC
                                                </option>
                                                <option value="General" {{ old('caste_category', $scholarship->caste_category ?? '') == 'General' ? 'selected' : '' }}>
                                                    General
                                                </option>
                                            </select>
                                        </div>
                                        @error('caste_category')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Status Toggle -->
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="is_active" class="fw-semibold">Status</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                                   {{ old('is_active', $scholarship->is_active ?? true) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-medium" for="is_active">Active / Available</label>
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

    <!-- Live Validation and Helper updates -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const typeSelect = document.getElementById('type');
            const valueInput = document.getElementById('value');
            const valueHelp = document.getElementById('valueHelp');

            function updateValueHelp() {
                if (typeSelect.value === 'percentage') {
                    valueHelp.innerHTML = '<span class="text-info fw-semibold"><i class="feather-info me-1"></i> Enter value as percentage rate (e.g. 50 for 50% concession). Maximum is 100%.</span>';
                    valueInput.setAttribute('max', '100');
                    valueInput.setAttribute('placeholder', 'e.g. 25');
                } else if (typeSelect.value === 'fixed') {
                    valueHelp.innerHTML = '<span class="text-warning fw-semibold"><i class="feather-info me-1"></i> Enter fixed concession amount in Rupees (e.g. 5000 for ₹5000 concession).</span>';
                    valueInput.removeAttribute('max');
                    valueInput.setAttribute('placeholder', 'e.g. 5000');
                } else {
                    valueHelp.innerHTML = '<span class="text-muted">Select concession type above first.</span>';
                    valueInput.setAttribute('placeholder', 'e.g. 50 or 5000');
                }
            }

            typeSelect.addEventListener('change', updateValueHelp);
            updateValueHelp(); // Initialize state
        });
    </script>
@endsection
