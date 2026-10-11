@extends('admin_layout')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">{{ !empty($subscription->id) ? 'Edit Subscription' : 'Create Subscription' }}</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.subscriptions.index') }}">Subscriptions</a></li>
                <li class="breadcrumb-item">{{ !empty($subscription->id) ? 'Edit Subscription' : 'Create Subscription' }}</li>
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
                    <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary">
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
                <form action="{{ route('admin.subscriptions.store') }}" method="POST">
                    @csrf
                    @if (!empty($subscription->id))
                        <input type="hidden" name="id" value="{{ $subscription->id }}">
                    @endif

                    <div class="card stretch stretch-full">
                        <div class="card-body general-info">
                            <div class="mb-5 d-flex align-items-center justify-content-between">
                                <h5 class="fw-bold mb-0 me-4">
                                    <span class="d-block mb-2">Subscription Info :</span>
                                    <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                        Subscription details for tenant
                                    </span>
                                </h5>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="tenant_id" class="fw-semibold">Tenant:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-building"></i></div>
                                        <select class="form-select @error('tenant_id') is-invalid @enderror"
                                                id="tenant_id"
                                                name="tenant_id"
                                                required>
                                            <option value="">Select Tenant</option>
                                            @foreach ($tenants as $tenant)
                                                <option value="{{ $tenant->id }}"
                                                        {{ old('tenant_id', $subscription->tenant_id ?? '') == $tenant->id ? 'selected' : '' }}>
                                                    {{ $tenant->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('tenant_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="plan_id" class="fw-semibold">Plan:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-tag"></i></div>
                                        <select class="form-select @error('plan_id') is-invalid @enderror"
                                                id="plan_id"
                                                name="plan_id"
                                                required>
                                            <option value="">Select Plan</option>
                                            @foreach ($plans as $plan)
                                                <option value="{{ $plan->id }}"
                                                        {{ old('plan_id', $subscription->plan_id ?? '') == $plan->id ? 'selected' : '' }}>
                                                    {{ $plan->name }} ({{ number_format($plan->monthly_price, 2) }} / {{ number_format($plan->annual_price, 2) }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('plan_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="starts_at" class="fw-semibold">Start Date:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-calendar"></i></div>
                                        <input type="date"
                                               class="form-control @error('starts_at') is-invalid @enderror"
                                               id="starts_at"
                                               name="starts_at"
                                               value="{{ old('starts_at', $subscription->starts_at ? $subscription->starts_at->format('Y-m-d') : '') }}"
                                               required>
                                    </div>
                                    @error('starts_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="expires_at" class="fw-semibold">Expiry Date:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-calendar"></i></div>
                                        <input type="date"
                                               class="form-control @error('expires_at') is-invalid @enderror"
                                               id="expires_at"
                                               name="expires_at"
                                               value="{{ old('expires_at', $subscription->expires_at ? $subscription->expires_at->format('Y-m-d') : '') }}"
                                               required>
                                    </div>
                                    @error('expires_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="status" class="fw-semibold">Status:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-shield"></i></div>
                                        <select class="form-select @error('status') is-invalid @enderror"
                                                id="status"
                                                name="status"
                                                required>
                                            <option value="">Select Status</option>
                                            <option value="active" {{ old('status', $subscription->status ?? 'active') == 'active' ? 'selected' : '' }}>
                                                Active
                                            </option>
                                            <option value="cancelled" {{ old('status', $subscription->status ?? 'active') == 'cancelled' ? 'selected' : '' }}>
                                                Cancelled
                                            </option>
                                            <option value="past_due" {{ old('status', $subscription->status ?? 'active') == 'past_due' ? 'selected' : '' }}>
                                                Past Due
                                            </option>
                                        </select>
                                    </div>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="payment_method" class="fw-semibold">Payment Method:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-credit-card"></i></div>
                                        <input type="text"
                                               class="form-control @error('payment_method') is-invalid @enderror"
                                               id="payment_method"
                                               name="payment_method"
                                               value="{{ old('payment_method', $subscription->payment_method ?? '') }}"
                                               placeholder="Payment Method (cash/card/online)">
                                    </div>
                                    @error('payment_method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
