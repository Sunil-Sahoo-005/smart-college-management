@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Accountants.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">
                            {{ isset($payment) ? 'Edit' : 'Record' }} Fee Payment
                        </span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                            <a href="{{ route('admin.fee-payments.index') }}" class="btn btn-secondary">
                                <i class="feather-arrow-left me-2"></i>
                                <span>Back to List</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="content-area-body">
                    @include('elements.flash')
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <form method="POST"
                                  action="{{ isset($payment)
                                      ? route('admin.fee-payments.update', $payment)
                                      : route('admin.fee-payments.store') }}">
                                @csrf
                                @if (isset($payment))
                                    @method('PUT')
                                @endif

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Student</label>
                                        <select name="student_id" class="form-select" required>
                                            <option value="">Select Student</option>
                                            @foreach ($students as $student)
                                                <option value="{{ $student->id }}"
                                                    {{ (isset($payment) ? $payment->student_id : old('student_id')) == $student->id ? 'selected' : '' }}>
                                                    {{ $student->first_name }} {{ $student->last_name }}
                                                    ({{ $student->roll_number ?? 'N/A' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Payment Method</label>
                                        <select name="payment_method" class="form-select" required>
                                            <option value="">Select Method</option>
                                            <option value="cash"
                                                {{ (isset($payment) ? $payment->payment_method : old('payment_method')) == 'cash' ? 'selected' : '' }}>
                                                Cash
                                            </option>
                                            <option value="online"
                                                {{ (isset($payment) ? $payment->payment_method : old('payment_method')) == 'online' ? 'selected' : '' }}>
                                                Online
                                            </option>
                                            <option value="bank_transfer"
                                                {{ (isset($payment) ? $payment->payment_method : old('payment_method')) == 'bank_transfer' ? 'selected' : '' }}>
                                                Bank Transfer
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Transaction ID (if online)</label>
                                        <input type="text" name="transaction_id" class="form-control"
                                               value="{{ isset($payment) ? $payment->transaction_id : old('transaction_id') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Gateway / Bank Name</label>
                                        <input type="text" name="gateway_name" class="form-control"
                                               value="{{ isset($payment) ? $payment->gateway_name : old('gateway_name') }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Amount Paid</label>
                                        <input type="number" step="0.01" name="amount_paid" class="form-control" required
                                               value="{{ isset($payment) ? $payment->amount_paid : old('amount_paid') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Payment Date</label>
                                        <input type="date" name="payment_date" class="form-control" required
                                               value="{{ isset($payment) ? $payment->payment_date->format('Y-m-d') : old('payment_date') }}">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Receipt Number</label>
                                    <input type="text" name="receipt_number" class="form-control" required
                                           value="{{ isset($payment) ? $payment->receipt_number : old('receipt_number') }}">
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        {{ isset($payment) ? 'Update' : 'Record' }} Payment
                                    </button>
                                    <a href="{{ route('admin.fee-payments.index') }}" class="btn btn-secondary">
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
