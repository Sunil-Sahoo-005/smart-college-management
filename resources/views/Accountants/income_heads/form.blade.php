@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Accountants.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">
                            {{ !empty($incomeHead) ? 'Edit Income Head' : 'New Income Head' }}
                        </span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                            <a href="{{ route('income-heads.index') }}" class="btn btn-secondary">
                                <i class="feather-arrow-left me-2"></i>
                                <span>Back to List</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="content-area-body">
                    @include('elements.flash')

                    <form action="{{ route('income-heads.store') }}" method="POST">
                        @csrf
                        @if (!empty($incomeHead))
                            <input type="hidden" name="id" value="{{ $incomeHead->id }}">
                        @endif

                        <div class="card stretch stretch-full">
                            <div class="card-body general-info">
                                <div class="mb-5 d-flex align-items-center justify-content-between">
                                    <h5 class="fw-bold mb-0 me-4">
                                        <span class="d-block mb-2">Income Head Info :</span>
                                        <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                            Define income categories such as Tuition Fee, Library Fine, Donation, etc.
                                        </span>
                                    </h5>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="name" class="fw-semibold">Name <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-tag"></i></div>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                   id="name" name="name"
                                                   value="{{ old('name', $incomeHead->name ?? '') }}"
                                                   placeholder="e.g., Tuition Fee, Library Fine" required autocomplete="off">
                                        </div>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="type" class="fw-semibold">Type <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-pie-chart"></i></div>
                                            <select class="form-select @error('type') is-invalid @enderror" 
                                                    id="type" name="type" required autocomplete="off">
                                                <option value="">Select Type</option>
                                                <option value="fee" {{ old('type', $incomeHead->type ?? '') == 'fee' ? 'selected' : '' }}>
                                                    Fee
                                                </option>
                                                <option value="other" {{ old('type', $incomeHead->type ?? '') == 'other' ? 'selected' : '' }}>
                                                    Other Income
                                                </option>
                                            </select>
                                        </div>
                                        @error('type')
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
