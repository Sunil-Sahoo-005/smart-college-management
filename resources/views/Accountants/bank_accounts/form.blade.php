@extends('admin_layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Accountants.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">
            
            <!-- Header -->
            <div class="content-area-header bg-white sticky-top">
                <h5 class="fw-bold mb-0 me-4">
                    <span class="d-block mb-2">
                        {{ isset($account) ? 'Edit' : 'Add' }} Bank Account
                    </span>
                </h5>

                <div class="page-header-right ms-auto">
                    <a href="{{ route('admin.bank_accounts.index') }}" class="btn btn-secondary">
                        <i class="feather-arrow-left me-2"></i> Back to List
                    </a>
                </div>
            </div>

            <!-- Body -->
            <div class="content-area-body">
                @include('elements.flash')
                <div class="card stretch stretch-full">
                    <div class="card-body">

                        <form method="POST"
                              action="{{ isset($account) 
                                ? route('admin.bank_accounts.update', $account) 
                                : route('admin.bank_accounts.store') }}">
                            
                            @csrf
                            @if(isset($account))
                                @method('PUT')
                            @endif

                            <div class="row mb-3">
                                <!-- Bank Name -->
                                <div class="col-md-6">
                                    <label class="form-label">Bank Name</label>
                                    <input type="text" name="bank_name" class="form-control" required autocomplete="off"
                                        value="{{ isset($account) ? $account->bank_name : old('bank_name') }}">
                                </div>

                                <!-- Account Number -->
                                <div class="col-md-6">
                                    <label class="form-label">Account Number</label>
                                    <input type="text" name="account_number" class="form-control" required autocomplete="off"
                                        value="{{ isset($account) ? $account->account_number : old('account_number') }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <!-- IFSC Code -->
                                <div class="col-md-6">
                                    <label class="form-label">IFSC Code</label>
                                    <input type="text" name="ifsc_code" class="form-control" autocomplete="off"
                                        value="{{ isset($account) ? $account->ifsc_code : old('ifsc_code') }}">
                                </div>

                                <!-- Status -->
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select" required autocomplete="off">
                                        <option value="active"
                                            {{ (isset($account) ? $account->status : old('status')) == 'active' ? 'selected' : '' }}>
                                            Active
                                        </option>
                                        <option value="inactive"
                                            {{ (isset($account) ? $account->status : old('status')) == 'inactive' ? 'selected' : '' }}>
                                            Inactive
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    {{ isset($account) ? 'Update' : 'Save' }} Account
                                </button>

                                <a href="{{ route('admin.bank_accounts.index') }}" class="btn btn-secondary">
                                    Cancel
                                </a>
                            </div>

                        </form>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection