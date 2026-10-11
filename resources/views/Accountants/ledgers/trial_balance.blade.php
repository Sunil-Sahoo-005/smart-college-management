@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Accountants.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Trial Balance</span>
                        <span class="fs-12 fw-normal text-muted">Review debit and credit balances across all ledgers.</span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                            <a href="{{ route('ledgers.index') }}" class="btn btn-secondary">
                                <i class="feather-arrow-left me-2"></i>Back to Ledgers
                            </a>
                        </div>
                    </div>
                </div>

                <div class="content-area-body">
                    <div class="card mb-4">
                        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <span class="fs-12 text-muted d-block mb-1">Status</span>
                                <h5 class="mb-0 {{ $isBalanced ? 'text-success' : 'text-danger' }}">
                                    {{ $isBalanced ? 'Balanced' : 'Out of Balance' }}
                                </h5>
                            </div>
                            <div class="text-end">
                                <span class="fs-12 text-muted d-block mb-1">Difference</span>
                                <h5 class="mb-0 {{ $isBalanced ? 'text-success' : 'text-danger' }}">
                                    Rs {{ number_format(abs($totalDebits - $totalCredits), 2) }}
                                </h5>
                            </div>
                        </div>
                    </div>

                    <div class="card stretch stretch-full">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Account Name</th>
                                            <th>Type</th>
                                            <th class="text-end">Debit</th>
                                            <th class="text-end">Credit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($ledgers as $ledger)
                                            <tr class="single-item">
                                                <td>
                                                    <a href="{{ route('ledgers.show', $ledger->id) }}" class="fw-semibold">
                                                        {{ $ledger->name }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <span class="badge bg-soft-{{ $ledger->balance_type === 'debit' ? 'success' : 'danger' }} text-{{ $ledger->balance_type === 'debit' ? 'success' : 'danger' }}">
                                                        {{ ucfirst($ledger->balance_type) }}
                                                    </span>
                                                </td>
                                                <td class="text-end fw-semibold">
                                                    {{ $ledger->net_balance > 0 ? 'Rs ' . number_format($ledger->net_balance, 2) : '-' }}
                                                </td>
                                                <td class="text-end fw-semibold">
                                                    {{ $ledger->net_balance < 0 ? 'Rs ' . number_format(abs($ledger->net_balance), 2) : '-' }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-5 text-muted">
                                                    <i class="feather-bar-chart fs-1 mb-3 d-block opacity-50"></i>
                                                    No ledger balances available.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2">Totals</th>
                                            <th class="text-end">Rs {{ number_format($totalDebits, 2) }}</th>
                                            <th class="text-end">Rs {{ number_format($totalCredits, 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
