@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Accountants.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">
                            {{ isset($installment) ? 'Edit' : 'Add' }} Fee Installment
                        </span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                            <a href="{{ route('admin.fee_installments.index') }}" class="btn btn-secondary">
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
                                  action="{{ isset($installment)
                                      ? route('admin.fee_installments.update', $installment)
                                      : route('admin.fee_installments.store') }}">
                                @csrf
                                @if (isset($installment))
                                    @method('PUT')
                                @endif

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Student</label>
                                        <select name="student_id" class="form-select" required autocomplete="off">
                                            <option value="">Select Student</option>
                                            @foreach ($students as $student)
                                                <option value="{{ $student->id }}"
                                                    {{ (isset($installment) ? $installment->student_id : old('student_id')) == $student->id ? 'selected' : '' }}>
                                                    {{ $student->first_name }} {{ $student->last_name }}
                                                    ({{ $student->roll_number ?? 'N/A' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Fee Category</label>
                                        <select name="fee_category_id" class="form-select" required autocomplete="off">
                                            <option value="">Select Category</option>
                                            @foreach ($feeCategories as $cat)
                                                <option value="{{ $cat->id }}"
                                                    {{ (isset($installment) ? $installment->fee_category_id : old('fee_category_id')) == $cat->id ? 'selected' : '' }}>
                                                    {{ $cat->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Amount</label>
                                        <input type="number" step="0.01" name="amount" class="form-control" required autocomplete="off"
                                               value="{{ isset($installment) ? $installment->amount : old('amount') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Due Date</label>
                                        <input type="date" name="due_date" class="form-control" required autocomplete="off"
                                               value="{{ isset($installment) ? $installment->due_date->format('Y-m-d') : old('due_date') }}">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select" required autocomplete="off">
                                        <option value="unpaid"
                                            {{ (isset($installment) ? $installment->status : old('status')) == 'unpaid' ? 'selected' : '' }}>
                                            Unpaid
                                        </option>
                                        <option value="partial"
                                            {{ (isset($installment) ? $installment->status : old('status')) == 'partial' ? 'selected' : '' }}>
                                            Partial
                                        </option>
                                        <option value="paid"
                                            {{ (isset($installment) ? $installment->status : old('status')) == 'paid' ? 'selected' : '' }}>
                                            Paid
                                        </option>
                                    </select>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        {{ isset($installment) ? 'Update' : 'Save' }} Installment
                                    </button>
                                    <a href="{{ route('admin.fee_installments.index') }}" class="btn btn-secondary">
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
