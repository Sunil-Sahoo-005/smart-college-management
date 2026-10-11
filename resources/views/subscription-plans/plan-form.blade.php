@extends('admin_layout')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">{{ !empty($plan->id) ? 'Edit Subscription Plan' : 'Create Subscription Plan' }}</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.subscription-plans.index') }}">Subscription Plans</a></li>
                <li class="breadcrumb-item">{{ !empty($plan->id) ? 'Edit Plan' : 'Create Plan' }}</li>
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
                    <a href="{{ route('admin.subscription-plans.index') }}" class="btn btn-secondary">
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
    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <form action="{{ route('admin.subscription-plans.store') }}" method="POST">
                    @csrf
                    @if (!empty($plan->id))
                        <input type="hidden" name="id" value="{{ $plan->id }}">
                    @endif

                    <div class="card stretch stretch-full">
                        <div class="card-body general-info">
                            <div class="mb-5 d-flex align-items-center justify-content-between">
                                <h5 class="fw-bold mb-0 me-4">
                                    <span class="d-block mb-2">Plan Info :</span>
                                    <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                        Subscription plan details
                                    </span>
                                </h5>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="name" class="fw-semibold">Name:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-tag"></i></div>
                                        <input type="text"
                                               class="form-control @error('name') is-invalid @enderror"
                                               id="name"
                                               name="name"
                                               value="{{ old('name', $plan->name ?? '') }}"
                                               placeholder="Plan Name"
                                               required>
                                    </div>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="description" class="fw-semibold">Description:</label>
                                </div>
                                <div class="col-lg-8">
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description"
                                              name="description"
                                              rows="3"
                                              placeholder="Description">{{ old('description', $plan->description ?? '') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="monthly_price" class="fw-semibold">Monthly Price:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-dollar-sign"></i></div>
                                        <input type="number"
                                               class="form-control @error('monthly_price') is-invalid @enderror"
                                               id="monthly_price"
                                               name="monthly_price"
                                               value="{{ old('monthly_price', $plan->monthly_price ?? 0) }}"
                                               placeholder="Monthly Price"
                                               step="0.01"
                                               required>
                                    </div>
                                    @error('monthly_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="annual_price" class="fw-semibold">Annual Price:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-dollar-sign"></i></div>
                                        <input type="number"
                                               class="form-control @error('annual_price') is-invalid @enderror"
                                               id="annual_price"
                                               name="annual_price"
                                               value="{{ old('annual_price', $plan->annual_price ?? 0) }}"
                                               placeholder="Annual Price"
                                               step="0.01"
                                               required>
                                    </div>
                                    @error('annual_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="max_users" class="fw-semibold">Max Users:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-users"></i></div>
                                        <input type="number"
                                               class="form-control @error('max_users') is-invalid @enderror"
                                               id="max_users"
                                               name="max_users"
                                               value="{{ old('max_users', $plan->max_users ?? '') }}"
                                               placeholder="Max Users (leave empty for unlimited)">
                                    </div>
                                    @error('max_users')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="max_students" class="fw-semibold">Max Students:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-user"></i></div>
                                        <input type="number"
                                               class="form-control @error('max_students') is-invalid @enderror"
                                               id="max_students"
                                               name="max_students"
                                               value="{{ old('max_students', $plan->max_students ?? '') }}"
                                               placeholder="Max Students (leave empty for unlimited)">
                                    </div>
                                    @error('max_students')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="max_staff" class="fw-semibold">Max Staff:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-user-check"></i></div>
                                        <input type="number"
                                               class="form-control @error('max_staff') is-invalid @enderror"
                                               id="max_staff"
                                               name="max_staff"
                                               value="{{ old('max_staff', $plan->max_staff ?? '') }}"
                                               placeholder="Max Staff (leave empty for unlimited)">
                                    </div>
                                    @error('max_staff')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="is_active" class="fw-semibold">Status:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                               {{ old('is_active', $plan->is_active ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Active</label>
                                    </div>
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
