@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Accountants.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">{{ isset($ledger) ? 'Edit Ledger' : 'New Ledger Account' }}</span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                            <a href="{{ route('ledgers.index') }}" class="btn btn-secondary">
                                <i class="feather-arrow-left me-2"></i>Back to List
                            </a>
                        </div>
                    </div>
                </div>

                <div class="content-area-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ isset($ledger) ? route('ledgers.update', $ledger->id) : route('ledgers.store') }}" method="POST">
                        @csrf
                        @isset($ledger)
                            @method('PUT')
                        @endisset

                        <div class="card stretch stretch-full">
                            <div class="card-body general-info">
                                <div class="mb-5 d-flex align-items-center justify-content-between">
                                    <h5 class="fw-bold mb-0 me-4">
                                        <span class="d-block mb-2">Ledger Information :</span>
                                        <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                            Configure the ledger name and its normal balance type.
                                        </span>
                                    </h5>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="feather-save me-1"></i>{{ isset($ledger) ? 'Update' : 'Save' }}
                                    </button>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="name" class="fw-semibold">Ledger Name <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-book"></i></div>
                                            <input type="text" id="name" name="name"
                                                class="form-control @error('name') is-invalid @enderror"
                                                value="{{ old('name', $ledger->name ?? '') }}"
                                                placeholder="Cash, Fee Income, Salary Expense" required autocomplete="off">
                                        </div>
                                        @error('name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="balance_type" class="fw-semibold">Normal Balance <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-layers"></i></div>
                                            <select id="balance_type" name="balance_type"
                                                class="form-select @error('balance_type') is-invalid @enderror" required autocomplete="off">
                                                <option value="debit" {{ old('balance_type', $ledger->balance_type ?? '') === 'debit' ? 'selected' : '' }}>
                                                    Debit (Assets, Expenses)
                                                </option>
                                                <option value="credit" {{ old('balance_type', $ledger->balance_type ?? '') === 'credit' ? 'selected' : '' }}>
                                                    Credit (Liabilities, Income)
                                                </option>
                                            </select>
                                        </div>
                                        <div class="fs-12 text-muted mt-2">Assets and expenses are usually debit. Income and liabilities are usually credit.</div>
                                        @error('balance_type')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-flex gap-2 pt-3">
                                    <a href="{{ route('ledgers.index') }}" class="btn btn-secondary">
                                        <i class="feather-x me-1"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="feather-save me-1"></i>{{ isset($ledger) ? 'Update Ledger' : 'Create Ledger' }}
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
