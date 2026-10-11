@extends('admin_layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Library.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            {{-- Page Header --}}
            <div class="content-area-header bg-white sticky-top">
                <h5 class="fw-bold mb-0 me-4">Stock Entries</h5>
                <div class="page-header-right ms-auto d-flex gap-2">
                    @if(auth()->user()->hasPermissionTo('Create Library Stock Entry'))
                    <a href="{{ route('admin.library.stock.create') }}" class="btn btn-primary btn-sm">
                        <i class="feather-plus me-1"></i>New Stock Entry
                    </a>
                    @endif
                </div>
            </div>

            <div class="content-area-body">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3">
                        <i class="feather-check-circle"></i> {{ session('success') }}
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Filters --}}
                <div class="si-card mb-4">
                    <form method="GET" action="{{ route('admin.library.stock.index') }}" class="row g-2 align-items-end">
                        <div class="col-lg-3 col-md-5">
                            <label class="si-label">Search</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="feather-search text-muted" style="font-size:13px;"></i>
                                </span>
                                <input type="text" name="search" value="{{ request('search') }}"
                                    class="form-control border-start-0"
                                    placeholder="Order ID, Bill No, Source…" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-3">
                            <label class="si-label">Status</label>
                            <select name="status" class="form-select form-select-sm" autocomplete="off">
                                <option value="">All statuses</option>
                                @foreach($statuses as $s)
                                    <option value="{{ $s }}" @selected(request('status') === $s)>
                                        {{ ucfirst($s) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-3">
                            <label class="si-label">From</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}"
                                class="form-control form-control-sm" autocomplete="off">
                        </div>
                        <div class="col-lg-2 col-md-3">
                            <label class="si-label">To</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}"
                                class="form-control form-control-sm" autocomplete="off">
                        </div>
                        <div class="col-auto d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="feather-filter me-1"></i>Filter
                            </button>
                            <a href="{{ route('admin.library.stock.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="feather-x"></i>
                            </a>
                        </div>
                    </form>
                </div>

                {{-- Summary chips --}}
                <div class="d-flex gap-2 flex-wrap mb-3">
                    <div class="si-chip">
                        <span class="si-chip-val">{{ $entries->total() }}</span>
                        <span class="si-chip-lbl">Total Entries</span>
                    </div>
                    <div class="si-chip">
                        <span class="si-chip-val">{{ $entries->sum('total_copies') }}</span>
                        <span class="si-chip-lbl">Total Copies</span>
                    </div>
                    <div class="si-chip">
                        <span class="si-chip-val">&#x20b9;{{ number_format($entries->sum('total_amount'), 2) }}</span>
                        <span class="si-chip-lbl">Total Value</span>
                    </div>
                </div>

                {{-- Table --}}
                <div class="si-card p-0">
                    <div class="table-responsive">
                        <table class="table table-hover si-table mb-0">
                            <thead>
                                <tr>
                                    <th width="40">#</th>
                                    <th>Order ID</th>
                                    <th>Bill No.</th>
                                    <th>Bill Date</th>
                                    <th>Source</th>
                                    <th>Copies</th>
                                    <th>Total (&#x20b9;)</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($entries as $i => $entry)
                                <tr>
                                    <td class="text-muted si-sno">{{ $entries->firstItem() + $i }}</td>
                                    <td>
                                        <code class="si-order-id">{{ $entry->order_id }}</code>
                                    </td>
                                    <td>
                                        @if($entry->bill_no)
                                            <span class="fw-semibold">{{ $entry->bill_no }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $entry->bill_date ? \Carbon\Carbon::parse($entry->bill_date)->format('d M Y') : '—' }}
                                    </td>
                                    <td>{{ $entry->source ?? '—' }}</td>
                                    
                                    <td>
                                        <span class="badge bg-info bg-opacity-10 text-white fw-semibold">
                                            {{ $entry->total_copies }}
                                        </span>
                                    </td>
                                    <td class="fw-semibold">
                                        &#x20b9;{{ number_format($entry->total_amount, 2) }}
                                    </td>
                                    <td>
                                        @php
                                            $sc = [
                                                'received'  => 'success',
                                                'pending'   => 'warning',
                                                'returned'  => 'secondary',
                                                'cancelled' => 'danger',
                                            ][$entry->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $sc }} bg-opacity-15 text-white fw-semibold">
                                            {{ ucfirst($entry->status) }}
                                        </span>
                                    </td>
                                    <td class="text-muted si-date">
                                        {{ \Carbon\Carbon::parse($entry->created_at)->format('d M Y') }}
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('admin.library.stock.show', $entry->order_id) }}"
                                                class="btn btn-sm btn-outline-primary si-action-btn"
                                                title="View">
                                                <i class="feather-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.library.stock.edit', $entry->order_id) }}"
                                                class="btn btn-sm btn-outline-secondary si-action-btn"
                                                title="Edit">
                                                <i class="feather-edit-2"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-sm btn-outline-danger si-action-btn"
                                                title="Delete"
                                                onclick="confirmDelete('{{ $entry->order_id }}', '{{ addslashes($entry->bill_no ?? $entry->order_id) }}', {{ $entry->total_copies }})">
                                                <i class="feather-trash-2"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center py-5">
                                        <div class="si-empty">
                                            <i class="feather-package si-empty-icon"></i>
                                            <p class="mb-1 fw-semibold">No stock entries found</p>
                                            <p class="text-muted small mb-3">
                                                @if(request()->hasAny(['search','status','date_from','date_to']))
                                                    Try clearing your filters.
                                                @else
                                                    Start by creating a new stock entry.
                                                @endif
                                            </p>
                                            <a href="{{ route('admin.library.stock.create') }}"
                                                class="btn btn-primary btn-sm">
                                                <i class="feather-plus me-1"></i>New Entry
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pagination --}}
                @if($entries->hasPages())
                <div class="d-flex justify-content-end mt-3">
                    {{ $entries->links() }}
                </div>
                @endif

            </div>
        </div>
    </div>
</div>

<style>
.si-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.1rem 1.25rem;
}
.si-label {
    display: block;
    font-size: 11.5px;
    font-weight: 500;
    color: #6b7280;
    margin-bottom: 4px;
}
/* Summary chips */
.si-chip {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 8px 14px;
}
.si-chip-val {
    font-size: 15px;
    font-weight: 700;
    color: #111827;
}
.si-chip-lbl {
    font-size: 11px;
    color: #9ca3af;
}
/* Table */
.si-table thead th {
    background: #f8f9fa;
    font-size: 11.5px;
    font-weight: 600;
    color: #4b5563;
    padding: 10px 12px;
    border-bottom: 1px solid #e5e7eb;
    white-space: nowrap;
}
.si-table td {
    font-size: 12.5px;
    color: #374151;
    padding: 10px 12px;
    vertical-align: middle;
    border-bottom: 1px solid #f3f4f6;
}
.si-table tbody tr:last-child td { border-bottom: none; }
.si-table tbody tr:hover { background: #f9fafb; }
.si-sno { color: #9ca3af !important; font-size: 12px !important; }
.si-date { font-size: 12px !important; }
.si-order-id {
    font-size: 11px;
    background: #f3f4f6;
    padding: 2px 7px;
    border-radius: 4px;
    color: #374151;
}
.si-action-btn {
    padding: 3px 8px;
    font-size: 12px;
    line-height: 1.4;
}
/* Empty state */
.si-empty { padding: 20px 0; }
.si-empty-icon {
    font-size: 36px;
    color: #d1d5db;
    display: block;
    margin-bottom: 10px;
}
</style>
{{-- Delete confirmation modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content">
            <div class="modal-body p-4 text-center">
                <div class="mb-3" style="font-size:40px; color:#ef4444;">
                    <i class="feather-alert-triangle"></i>
                </div>
                <h6 class="fw-bold mb-1">Delete Stock Entry?</h6>
                <p class="text-muted mb-1" style="font-size:13px;">
                    Bill: <strong id="dmBillNo"></strong>
                </p>
                <p class="text-muted mb-3" style="font-size:12px;">
                    This will permanently delete <strong id="dmCopies"></strong> book
                    copies. Issued copies will be skipped.
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <button class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm px-4">
                            <i class="feather-trash-2 me-1"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(orderId, billNo, copies) {
    document.getElementById('dmBillNo').textContent = billNo;
    document.getElementById('dmCopies').textContent = copies;

    let url = "{{ route('admin.library.stock.destroy', ':id') }}";
    url = url.replace(':id', orderId);

    document.getElementById('deleteForm').action = url;

    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endsection