@extends('admin_layout')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">{{ !empty($invoice->id) ? 'Edit Invoice' : 'Create Invoice' }}</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.billing-invoices.index') }}">Billing Invoices</a></li>
                <li class="breadcrumb-item">{{ !empty($invoice->id) ? 'Edit Invoice' : 'Create Invoice' }}</li>
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
                    <a href="{{ route('admin.billing-invoices.index') }}" class="btn btn-secondary">
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
                <form action="{{ route('admin.billing-invoices.store') }}" method="POST">
                    @csrf
                    @if (!empty($invoice->id))
                        <input type="hidden" name="id" value="{{ $invoice->id }}">
                    @endif

                    <div class="card stretch stretch-full">
                        <div class="card-body general-info">
                            <div class="mb-5 d-flex align-items-center justify-content-between">
                                <h5 class="fw-bold mb-0 me-4">
                                    <span class="d-block mb-2">Invoice Info :</span>
                                    <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                        Billing invoice details
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
                                                        {{ old('tenant_id', $invoice->tenant_id ?? '') == $tenant->id ? 'selected' : '' }}>
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
                                    <label for="invoice_number" class="fw-semibold">Invoice Number:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-hash"></i></div>
                                        <input type="text"
                                               class="form-control @error('invoice_number') is-invalid @enderror"
                                               id="invoice_number"
                                               name="invoice_number"
                                               value="{{ old('invoice_number', $invoice->invoice_number ?? '') }}"
                                               placeholder="Invoice Number"
                                               required>
                                    </div>
                                    @error('invoice_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="amount" class="fw-semibold">Amount:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-dollar-sign"></i></div>
                                        <input type="number"
                                               class="form-control @error('amount') is-invalid @enderror"
                                               id="amount"
                                               name="amount"
                                               value="{{ old('amount', $invoice->amount ?? 0) }}"
                                               placeholder="Amount"
                                               step="0.01"
                                               required>
                                    </div>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="currency" class="fw-semibold">Currency:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-dollar-sign"></i></div>
                                        <select class="form-select @error('currency') is-invalid @enderror"
                                                id="currency"
                                                name="currency"
                                                required>
                                            <option value="">Select Currency</option>
                                            <option value="INR" {{ old('currency', $invoice->currency ?? 'INR') == 'INR' ? 'selected' : '' }}>
                                                INR
                                            </option>
                                            <option value="USD" {{ old('currency', $invoice->currency ?? 'INR') == 'USD' ? 'selected' : '' }}>
                                                USD
                                            </option>
                                            <option value="EUR" {{ old('currency', $invoice->currency ?? 'INR') == 'EUR' ? 'selected' : '' }}>
                                                EUR
                                            </option>
                                        </select>
                                    </div>
                                    @error('currency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="due_date" class="fw-semibold">Due Date:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-calendar"></i></div>
                                        <input type="date"
                                               class="form-control @error('due_date') is-invalid @enderror"
                                               id="due_date"
                                               name="due_date"
                                               value="{{ old('due_date', $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '') }}"
                                               required>
                                    </div>
                                    @error('due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="paid_at" class="fw-semibold">Paid At:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-calendar"></i></div>
                                        <input type="datetime-local"
                                               class="form-control @error('paid_at') is-invalid @enderror"
                                               id="paid_at"
                                               name="paid_at"
                                               value="{{ old('paid_at', $invoice->paid_at ? $invoice->paid_at->format('Y-m-d\TH:i') : '') }}">
                                    </div>
                                    @error('paid_at')
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
                                            <option value="unpaid" {{ old('status', $invoice->status ?? 'unpaid') == 'unpaid' ? 'selected' : '' }}>
                                                Unpaid
                                            </option>
                                            <option value="paid" {{ old('status', $invoice->status ?? 'unpaid') == 'paid' ? 'selected' : '' }}>
                                                Paid
                                            </option>
                                            <option value="overdue" {{ old('status', $invoice->status ?? 'unpaid') == 'overdue' ? 'selected' : '' }}>
                                                Overdue
                                            </option>
                                        </select>
                                    </div>
                                    @error('status')
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
