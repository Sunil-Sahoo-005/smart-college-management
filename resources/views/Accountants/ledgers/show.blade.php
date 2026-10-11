@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Accountants.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">{{ $ledger->name }}</span>
                        <span class="fs-12 fw-normal text-muted">
                            {{ ucfirst($ledger->balance_type) }} account ledger statement.
                        </span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                            <a href="{{ route('ledgers.index') }}" class="btn btn-secondary">
                                <i class="feather-arrow-left me-2"></i>Back
                            </a>
                        </div>
                    </div>
                </div>

                <div class="content-area-body">
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="card stretch stretch-full">
                                <div class="card-body">
                                    <span class="fs-12 text-muted d-block mb-2">Total Debits</span>
                                    <h4 class="fw-bold text-success mb-0">Rs {{ number_format($ledger->debit_total ?? 0, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card stretch stretch-full">
                                <div class="card-body">
                                    <span class="fs-12 text-muted d-block mb-2">Total Credits</span>
                                    <h4 class="fw-bold text-danger mb-0">Rs {{ number_format($ledger->credit_total ?? 0, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            @php $net = ($ledger->debit_total ?? 0) - ($ledger->credit_total ?? 0); @endphp
                            <div class="card stretch stretch-full">
                                <div class="card-body">
                                    <span class="fs-12 text-muted d-block mb-2">Net Balance</span>
                                    <h4 class="fw-bold {{ $net >= 0 ? 'text-success' : 'text-danger' }} mb-0">
                                        Rs {{ number_format(abs($net), 2) }}
                                        <span class="fs-12">{{ $net >= 0 ? 'Dr' : 'Cr' }}</span>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body p-3">
                            <form method="GET" class="row g-3">
                                <div class="col-md-3">
                                    <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                                </div>
                                <div class="col-md-3">
                                    <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                                </div>
                                <div class="col-md-3">
                                    <select name="type" class="form-select">
                                        <option value="">All Types</option>
                                        <option value="debit" {{ request('type') === 'debit' ? 'selected' : '' }}>Debit</option>
                                        <option value="credit" {{ request('type') === 'credit' ? 'selected' : '' }}>Credit</option>
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex gap-2">
                                    <button type="submit" class="btn btn-outline-primary flex-fill">
                                        <i class="feather-filter me-1"></i>Filter
                                    </button>
                                    <a href="{{ route('ledgers.show', $ledger->id) }}" class="btn btn-outline-secondary">Clear</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card stretch stretch-full">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Description</th>
                                            <th>Reference</th>
                                            <th class="text-end">Debit</th>
                                            <th class="text-end">Credit</th>
                                            <th class="text-end">Running Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (request('from') && $openingBalance != 0)
                                            <tr>
                                                <td colspan="5" class="fw-semibold text-muted">Opening Balance</td>
                                                <td class="text-end fw-bold">{{ number_format(abs($openingBalance), 2) }} {{ $openingBalance >= 0 ? 'Dr' : 'Cr' }}</td>
                                            </tr>
                                        @endif

                                        @php $running = $openingBalance; @endphp

                                        @forelse ($entries as $entry)
                                            @php
                                                $running += $entry->type === 'debit' ? $entry->amount : -$entry->amount;
                                                $referenceLabel = match (true) {
                                                    str_contains($entry->reference_type, 'FeePayment') => 'Fee Payment',
                                                    str_contains($entry->reference_type, 'IncomeRecord') => 'Income',
                                                    str_contains($entry->reference_type, 'ExpenseRecord') => 'Expense',
                                                    str_contains($entry->reference_type, 'SalaryRecord') => 'Salary',
                                                    $entry->reference_type === 'manual_journal' => 'Journal',
                                                    default => class_basename($entry->reference_type),
                                                };
                                            @endphp
                                            <tr class="single-item">
                                                <td>{{ optional($entry->entry_date ?? $entry->created_at)->format('d M Y') }}</td>
                                                <td>{{ $entry->description ?: '-' }}</td>
                                                <td>
                                                    <span class="badge bg-soft-secondary text-secondary">
                                                        {{ $referenceLabel }}{{ $entry->reference_id ? ' #' . $entry->reference_id : '' }}
                                                    </span>
                                                </td>
                                                <td class="text-end text-success fw-semibold">
                                                    {{ $entry->type === 'debit' ? 'Rs ' . number_format($entry->amount, 2) : '-' }}
                                                </td>
                                                <td class="text-end text-danger fw-semibold">
                                                    {{ $entry->type === 'credit' ? 'Rs ' . number_format($entry->amount, 2) : '-' }}
                                                </td>
                                                <td class="text-end fw-bold {{ $running >= 0 ? 'text-success' : 'text-danger' }}">
                                                    Rs {{ number_format(abs($running), 2) }} {{ $running >= 0 ? 'Dr' : 'Cr' }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-5 text-muted">
                                                    <i class="feather-file-text fs-1 mb-3 d-block opacity-50"></i>
                                                    No ledger entries found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{ $entries->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
