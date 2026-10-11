@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Accountants.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">
                            {{ !empty($expenseRecord) ? 'Edit Expense Record' : 'New Expense Record' }}
                        </span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                            <a href="{{ route('expense-records.index') }}" class="btn btn-secondary">
                                <i class="feather-arrow-left me-2"></i>
                                <span>Back to List</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="content-area-body">
                    @include('elements.flash')
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ !empty($expenseRecord) ? route('expense-records.update', $expenseRecord) : route('expense-records.store') }}" method="POST">
                        @csrf
                        @if(!empty($expenseRecord))
                            @method('PUT')
                        @endif

                        <div class="card stretch stretch-full">
                            <div class="card-body general-info">
                                <div class="mb-5 d-flex align-items-center justify-content-between">
                                    <h5 class="fw-bold mb-0 me-4">
                                        <span class="d-block mb-2">Expense Record Info :</span>
                                        <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                            Record expense transactions with category, amount and date details.
                                        </span>
                                    </h5>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="feather-save me-1"></i>
                                        {{ !empty($expenseRecord) ? 'Update' : 'Save' }}
                                    </button>
                                </div>

                                {{-- Expense Head --}}
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="expense_head_id" class="fw-semibold">Expense Head <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-list"></i></div>
                                            <select class="form-select @error('expense_head_id') is-invalid @enderror" 
                                                    id="expense_head_id" name="expense_head_id" required autocomplete="off">
                                                <option value="">Select Expense Head</option>
                                                @foreach($expenseHeads as $head)
                                                    <option value="{{ $head->id }}" 
                                                            {{ old('expense_head_id', $expenseRecord->expense_head_id ?? '') == $head->id ? 'selected' : '' }}>
                                                        {{ $head->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('expense_head_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Amount --}}
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="amount" class="fw-semibold">Amount <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-credit-card"></i></div>
                                            <span class="input-group-text bg-light">₹</span>
                                            <input type="number" 
                                                   step="0.01" 
                                                   min="0" 
                                                   id="amount"
                                                   name="amount" 
                                                   class="form-control @error('amount') is-invalid @enderror"
                                                   value="{{ old('amount', $expenseRecord->amount ?? '') }}" 
                                                   placeholder="0.00" required autocomplete="off">
                                        </div>
                                        @error('amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Date --}}
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="date" class="fw-semibold">Date <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-calendar"></i></div>
                                            <input type="date" 
                                                   id="date"
                                                   name="date" 
                                                   class="form-control @error('date') is-invalid @enderror"
                                                   value="{{ old('date', $expenseRecord->date ?? now()->format('Y-m-d')) }}" 
                                                   required autocomplete="off">
                                        </div>
                                        @error('date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Description --}}
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="description" class="fw-semibold">Description</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-align-left"></i></div>
                                            <textarea name="description" 
                                                      id="description"
                                                      class="form-control @error('description') is-invalid @enderror"
                                                      rows="3" 
                                                      placeholder="Optional description...">{{ old('description', $expenseRecord->description ?? '') }}</textarea>
                                        </div>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-flex gap-2 pt-3">
                                    <a href="{{ route('expense-records.index') }}" class="btn btn-secondary">
                                        <i class="feather-x me-1"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="feather-save me-1"></i>
                                        {{ !empty($expenseRecord) ? 'Update Expense' : 'Save Expense' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
