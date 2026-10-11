@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Accountants.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Chart of Accounts</span>
                        <span class="fs-12 fw-normal text-muted">Ledger accounts with current balances and quick actions.</span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper flex-wrap">
                            @if(auth()->user()->hasPermissionTo('View Trial Balance'))
                                <a href="{{ route('ledgers.trial-balance') }}" class="btn btn-outline-secondary">
                                    <i class="feather-bar-chart-2 me-2"></i>Trial Balance
                                </a>
                            @endif
                            @if(auth()->user()->hasPermissionTo('Create Journal Entry'))
                                <a href="{{ route('ledgers.journal.create') }}" class="btn btn-outline-primary">
                                    <i class="feather-edit-3 me-2"></i>Journal Entry
                                </a>
                            @endif
                            @if(auth()->user()->hasPermissionTo('Sync Ledgers'))
                                <a href="{{ route('ledgers.sync') }}" class="btn btn-outline-warning"
                                   onclick="return confirm('Re-sync ledger entries from source records?')">
                                    <i class="feather-refresh-cw me-2"></i>Sync
                                </a>
                            @endif
                            @if(auth()->user()->hasPermissionTo('Create Ledgers'))
                                <a href="{{ route('ledgers.create') }}" class="btn btn-primary">
                                    <i class="feather-plus me-2"></i>New Ledger
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="content-area-body">
                    @include('elements.flash')

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="card stretch stretch-full">
                                <div class="card-body">
                                    <span class="fs-12 text-muted d-block mb-2">Total Debit Balances</span>
                                    <h4 class="fw-bold text-success mb-0">
                                        Rs {{ number_format($ledgers->sum(fn ($ledger) => max(0, ($ledger->debit_total ?? 0) - ($ledger->credit_total ?? 0))), 2) }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card stretch stretch-full">
                                <div class="card-body">
                                    <span class="fs-12 text-muted d-block mb-2">Total Credit Balances</span>
                                    <h4 class="fw-bold text-danger mb-0">
                                        Rs {{ number_format($ledgers->sum(fn ($ledger) => max(0, ($ledger->credit_total ?? 0) - ($ledger->debit_total ?? 0))), 2) }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card stretch stretch-full">
                                <div class="card-body">
                                    <span class="fs-12 text-muted d-block mb-2">Visible Ledgers</span>
                                    <h4 class="fw-bold mb-0">{{ $ledgers->total() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body p-3">
                            <form method="GET" class="row g-3">
                                <div class="col-md-5">
                                    <input type="text" name="search" class="form-control" placeholder="Search ledger name"
                                           value="{{ request('search') }}">
                                </div>
                                <div class="col-md-4">
                                    <select name="balance_type" class="form-select">
                                        <option value="">All Types</option>
                                        <option value="debit" {{ request('balance_type') === 'debit' ? 'selected' : '' }}>Debit Accounts</option>
                                        <option value="credit" {{ request('balance_type') === 'credit' ? 'selected' : '' }}>Credit Accounts</option>
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex gap-2">
                                    <button type="submit" class="btn btn-outline-primary flex-fill">
                                        <i class="feather-filter me-1"></i>Filter
                                    </button>
                                    <a href="{{ route('ledgers.index') }}" class="btn btn-outline-secondary">Clear</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card stretch stretch-full">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover" id="ledgerList">
                                    <thead>
                                        <tr>
                                            <th>Ledger Name</th>
                                            <th>Type</th>
                                            <th class="text-end">Debits</th>
                                            <th class="text-end">Credits</th>
                                            <th class="text-end">Balance</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($ledgers as $ledger)
                                            @php
                                                $debits = $ledger->debit_total ?? 0;
                                                $credits = $ledger->credit_total ?? 0;
                                                $balance = $debits - $credits;
                                            @endphp
                                            <tr class="single-item">
                                                <td class="project-name-td">
                                                    <div class="hstack gap-3">
                                                        <div>
                                                            @if(auth()->user()->hasPermissionTo('View Ledger'))
                                                                <a href="{{ route('ledgers.show', $ledger->id) }}" class="text-truncate-1-line fw-semibold">
                                                                    {{ $ledger->name }}
                                                                </a>
                                                            @else
                                                                <span class="text-truncate-1-line fw-semibold">{{ $ledger->name }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-soft-{{ $ledger->balance_type === 'debit' ? 'success' : 'danger' }} text-{{ $ledger->balance_type === 'debit' ? 'success' : 'danger' }}">
                                                        {{ ucfirst($ledger->balance_type) }}
                                                    </span>
                                                </td>
                                                <td class="text-end text-success fw-semibold">Rs {{ number_format($debits, 2) }}</td>
                                                <td class="text-end text-danger fw-semibold">Rs {{ number_format($credits, 2) }}</td>
                                                <td class="text-end fw-bold {{ $balance >= 0 ? 'text-success' : 'text-danger' }}">
                                                    Rs {{ number_format(abs($balance), 2) }}
                                                    <span class="fs-12">{{ $balance >= 0 ? 'Dr' : 'Cr' }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="hstack gap-2 justify-content-center">
                                                        @if(auth()->user()->hasPermissionTo('View Ledgers'))
                                                            <a href="{{ route('ledgers.show', $ledger->id) }}" class="avatar-text avatar-md text-primary" title="View">
                                                                <i class="feather-eye"></i>
                                                            </a>
                                                        @endif
                                                        @if(auth()->user()->hasPermissionTo('Edit Ledgers'))
                                                            <a href="{{ route('ledgers.edit', $ledger->id) }}" class="avatar-text avatar-md text-warning" title="Edit">
                                                                <i class="feather-edit-3"></i>
                                                            </a>
                                                        @endif
                                                        @if(auth()->user()->hasPermissionTo('Delete Ledgers'))
                                                            <form action="{{ route('ledgers.destroy', $ledger->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="avatar-text avatar-md text-danger border-0 bg-transparent"
                                                                        title="Delete" onclick="return confirm('Delete this ledger?')">
                                                                    <i class="feather-trash-2"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            <tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-5 text-muted">
                                                    <i class="feather-book-open fs-1 mb-3 d-block opacity-50"></i>
                                                    No ledgers found.
                                                    @if(auth()->user()->hasPermissionTo('Create Ledger'))
                                                        <div class="mt-3">
                                                            <a href="{{ route('ledgers.create') }}" class="btn btn-sm btn-primary">
                                                                Create your first ledger
                                                            </a>
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{ $ledgers->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection