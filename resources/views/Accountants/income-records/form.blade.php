@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Accountants.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">{{ !empty($incomeRecord) ? 'Edit' : 'New' }} Income Record</span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <a href="{{ route('income-records.index') }}" class="btn btn-secondary">
                            <i class="feather-arrow-left me-2"></i>Back to List
                        </a>
                    </div>
                </div>

                <div class="content-area-body">
                    @include('elements.flash')
                    <form action="{{ route('income-records.' . (!empty($incomeRecord) ? 'update' : 'store'), $incomeRecord->id ?? '') }}" method="POST">
                        @csrf
                        @if(!empty($incomeRecord))
                            @method('PUT')
                        @endif

                        <div class="card stretch stretch-full">
                            <div class="card-body general-info">
                                <div class="mb-5 d-flex align-items-center justify-content-between">
                                    <h5 class="fw-bold mb-0 me-4">
                                        <span class="d-block mb-2">Income Record Info :</span>
                                        <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                            Record income transactions with category and amount details.
                                        </span>
                                    </h5>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label class="fw-semibold">Income Head <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-list"></i></div>
                                            <select name="income_head_id" class="form-select @error('income_head_id') is-invalid @enderror" required>
                                                <option value="">Select Income Head</option>
                                                @foreach($incomeHeads as $head)
                                                    <option value="{{ $head->id }}" {{ old('income_head_id', $incomeRecord->income_head_id ?? '') == $head->id ? 'selected' : '' }}>
                                                        {{ $head->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('income_head_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label class="fw-semibold">Amount <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text">₹</div>
                                            <input type="number" step="0.01" min="0"
                                                   name="amount" class="form-control @error('amount') is-invalid @enderror"
                                                   value="{{ old('amount', $incomeRecord->amount ?? '') }}" required autocomplete="off">
                                        </div>
                                        @error('amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label class="fw-semibold">Date <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-calendar"></i></div>
                                            <input type="date" name="date" class="form-control @error('date') is-invalid @enderror"
                                                   value="{{ old('date', $incomeRecord->date ?? now()->format('Y-m-d') ) }}" required autocomplete="off">
                                        </div>
                                        @error('date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label class="fw-semibold">Description</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-align-left"></i></div>
                                            <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                                      rows="3">{{ old('description', $incomeRecord->description ?? '') }}</textarea>
                                        </div>
                                        @error('description')
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
    </div>
@endsection
