@extends('admin_layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Library.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            {{-- Page Header --}}
            <div class="content-area-header bg-white sticky-top">
                <div>
                    <h5 class="fw-bold mb-0">Stock Entry Details</h5>
                    <p class="text-muted mb-0" style="font-size:12px;">
                        Order: <code>{{ $header->order_id }}</code>
                    </p>
                </div>
                <div class="page-header-right ms-auto d-flex gap-2">
                    @if($header->bill_pdf)
                    <a href="{{ Storage::url($header->bill_pdf) }}" target="_blank"
                        class="btn btn-outline-secondary btn-sm">
                        <i class="feather-file-text me-1"></i>View Bill PDF
                    </a>
                    @endif
                    <a href="{{ route('admin.library.stock.edit', $orderId) }}"
                        class="btn btn-outline-secondary btn-sm">
                        <i class="feather-edit-2 me-1"></i>Edit
                    </a>
                    <button type="button" class="btn btn-outline-danger btn-sm"
                        onclick="confirmDelete()">
                        <i class="feather-trash-2 me-1"></i>Delete
                    </button>
                    <a href="{{ route('admin.library.stock.index') }}" class="btn btn-secondary btn-sm">
                        <i class="feather-arrow-left me-1"></i>Back
                    </a>
                </div>
            </div>

            <div class="content-area-body">

                {{-- ── Stats row ── --}}
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="sv-stat-card">
                            <div class="sv-stat-icon bg-primary bg-opacity-10 text-primary">
                                <i class="feather-book-open"></i>
                            </div>
                            <div>
                                <div class="sv-stat-val">{{ $stats->total_titles }}</div>
                                <div class="sv-stat-lbl">Titles</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="sv-stat-card">
                            <div class="sv-stat-icon bg-info bg-opacity-10 text-info">
                                <i class="feather-copy"></i>
                            </div>
                            <div>
                                <div class="sv-stat-val">{{ $stats->total_copies }}</div>
                                <div class="sv-stat-lbl">Total Copies</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="sv-stat-card">
                            <div class="sv-stat-icon bg-success bg-opacity-10 text-success">
                                <i class="feather-check-circle"></i>
                            </div>
                            <div>
                                <div class="sv-stat-val">{{ $stats->available }}</div>
                                <div class="sv-stat-lbl">Available</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="sv-stat-card">
                            <div class="sv-stat-icon bg-warning bg-opacity-10 text-warning">
                                <i class="feather-dollar-sign"></i>
                            </div>
                            <div>
                                <div class="sv-stat-val">&#x20b9;{{ number_format($stats->total_amount, 2) }}</div>
                                <div class="sv-stat-lbl">Total Value</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">

                    {{-- ── Left: Bill & Withdrawal Details ── --}}
                    <div class="col-lg-4">
                        <div class="sv-card h-100">
                            <div class="sv-section-label">Bill &amp; Withdrawal Details</div>

                            <div class="sv-meta-grid">
                                <div class="sv-meta-item">
                                    <span class="sv-meta-lbl">Bill No.</span>
                                    <span class="sv-meta-val">{{ $header->bill_no ?? '—' }}</span>
                                </div>
                                <div class="sv-meta-item">
                                    <span class="sv-meta-lbl">Bill Date</span>
                                    <span class="sv-meta-val">
                                        {{ $header->bill_date ? \Carbon\Carbon::parse($header->bill_date)->format('d M Y') : '—' }}
                                    </span>
                                </div>
                                <div class="sv-meta-item">
                                    <span class="sv-meta-lbl">Withdrawal No.</span>
                                    <span class="sv-meta-val">{{ $header->withdrawal_no ?? '—' }}</span>
                                </div>
                                <div class="sv-meta-item">
                                    <span class="sv-meta-lbl">Withdrawal Date</span>
                                    <span class="sv-meta-val">
                                        {{ $header->withdrawal_date ? \Carbon\Carbon::parse($header->withdrawal_date)->format('d M Y') : '—' }}
                                    </span>
                                </div>
                                <div class="sv-meta-item">
                                    <span class="sv-meta-lbl">Source</span>
                                    <span class="sv-meta-val">{{ $header->source ?? '—' }}</span>
                                </div>
                                <div class="sv-meta-item">
                                    <span class="sv-meta-lbl">Status</span>
                                    <span class="sv-meta-val">
                                        @php
                                            $sc = [
                                                'received'  => 'success',
                                                'pending'   => 'warning',
                                                'returned'  => 'secondary',
                                                'cancelled' => 'danger',
                                            ][$header->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $sc }} bg-opacity-15 text-{{ $sc }}">
                                            {{ ucfirst($header->status) }}
                                        </span>
                                    </span>
                                </div>
                                @if($header->bill_pdf)
                                <div class="sv-meta-item sv-meta-full">
                                    <span class="sv-meta-lbl">Bill PDF</span>
                                    <span class="sv-meta-val">
                                        <a href="{{ Storage::url($header->bill_pdf) }}" target="_blank"
                                            class="btn btn-outline-primary btn-sm py-0 px-2">
                                            <i class="feather-download me-1" style="font-size:11px;"></i>Download
                                        </a>
                                    </span>
                                </div>
                                @endif
                                @if($header->remarks)
                                <div class="sv-meta-item sv-meta-full">
                                    <span class="sv-meta-lbl">Remarks</span>
                                    <span class="sv-meta-val text-muted">{{ $header->remarks }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ── Right: Copy-status breakdown ── --}}
                    <div class="col-lg-8">
                        <div class="sv-card h-100">
                            <div class="sv-section-label">Copy Status Breakdown</div>
                            <div class="d-flex gap-3 flex-wrap">
                                @php
                                    $breakdown = [
                                        ['label' => 'Available',   'count' => $stats->available,    'color' => '#16a34a', 'bg' => '#f0fdf4'],
                                        ['label' => 'Damaged',     'count' => $stats->damaged,      'color' => '#d97706', 'bg' => '#fffbeb'],
                                        ['label' => 'Lost',        'count' => $stats->lost,         'color' => '#dc2626', 'bg' => '#fef2f2'],
                                        ['label' => 'Under Repair','count' => $stats->under_repair, 'color' => '#2563eb', 'bg' => '#eff6ff'],
                                    ];
                                @endphp
                                @foreach($breakdown as $b)
                                <div class="sv-breakdown-item" style="border-left: 3px solid {{ $b['color'] }}; background: {{ $b['bg'] }};">
                                    <div class="sv-breakdown-val" style="color: {{ $b['color'] }};">{{ $b['count'] }}</div>
                                    <div class="sv-breakdown-lbl">{{ $b['label'] }}</div>
                                </div>
                                @endforeach
                            </div>

                            {{-- Mini progress bar --}}
                            @if($stats->total_copies > 0)
                            <div class="mt-3">
                                <div class="d-flex justify-content-between mb-1" style="font-size:11px; color:#6b7280;">
                                    <span>Availability</span>
                                    <span>{{ round($stats->available / $stats->total_copies * 100) }}%</span>
                                </div>
                                <div class="progress" style="height:6px; border-radius:4px;">
                                    @php $pct = round($stats->available / $stats->total_copies * 100); @endphp
                                    <div class="progress-bar bg-success" style="width: {{ $pct }}%;"></div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ── Books in this order ── --}}
                <div class="sv-card mt-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="sv-section-label mb-0">
                            Books in this Entry
                            <span class="badge bg-light text-dark border ms-2">{{ $stats->total_titles }} titles</span>
                        </div>
                    </div>

                    @foreach($titleGroups as $tg)
                    <div class="sv-book-card mb-3">
                        {{-- Title header --}}
                        <div class="sv-book-header">
                            <div class="sv-book-title">{{ $tg->title }}</div>
                            <div class="d-flex align-items-center gap-2 ms-auto flex-shrink-0">
                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                    {{ $tg->total_copies }} {{ Str::plural('copy', $tg->total_copies) }}
                                </span>
                                @if($tg->price)
                                <span class="sv-book-price">&#x20b9;{{ number_format($tg->price, 2) }}/copy</span>
                                @endif
                                <button class="btn btn-sm sv-expand-btn"
                                    onclick="toggleCopies(this)"
                                    data-target="copies-{{ $loop->index }}">
                                    <i class="feather-chevron-down"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Book meta --}}
                        <div class="sv-book-meta">
                            @if($tg->author)
                            <span><i class="feather-user me-1"></i>{{ $tg->author }}</span>
                            @endif
                            @if($tg->publisher)
                            <span><i class="feather-home me-1"></i>{{ $tg->publisher }}</span>
                            @endif
                            @if($tg->edition)
                            <span><i class="feather-layers me-1"></i>{{ $tg->edition }}</span>
                            @endif
                            @if($tg->year)
                            <span><i class="feather-calendar me-1"></i>{{ $tg->year }}</span>
                            @endif
                            @if($tg->isbn)
                            <span><i class="feather-hash me-1"></i>{{ $tg->isbn }}</span>
                            @endif
                            @if($tg->volume)
                            <span><i class="feather-book me-1"></i>Vol. {{ $tg->volume }}</span>
                            @endif
                            @if($tg->page)
                            <span><i class="feather-file-text me-1"></i>{{ $tg->page }} pp.</span>
                            @endif
                        </div>

                        {{-- Copies table (collapsible) --}}
                        <div class="sv-copies-wrap" id="copies-{{ $loop->index }}" style="display:none;">
                            <table class="table table-sm sv-copies-table mb-0 mt-2">
                                <thead>
                                    <tr>
                                        <th>Copy #</th>
                                        <th>Copy ISBN</th>
                                        <th>Status</th>
                                        @if($locations->isNotEmpty())
                                        <th>Location</th>
                                        @endif
                                        <th>Barcode</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tg->copies as $copy)
                                    <tr>
                                        <td>
                                            <span class="badge bg-light text-dark border">
                                                #{{ $copy->copy_number }}
                                            </span>
                                        </td>
                                        <td>
                                            <code style="font-size:11px;">{{ $copy->copy_isbn }}</code>
                                        </td>
                                        <td>
                                            @php
                                                $cs = [
                                                    'available'   => ['success', 'Available'],
                                                    'damaged'     => ['warning', 'Damaged'],
                                                    'lost'        => ['danger',  'Lost'],
                                                    'under_repair'=> ['info',    'Repair'],
                                                ][$copy->status] ?? ['secondary', $copy->status];
                                            @endphp
                                            <span class="badge bg-{{ $cs[0] }} bg-opacity-15 text-{{ $cs[0] }}">
                                                {{ $cs[1] }}
                                            </span>
                                        </td>
                                        @if($locations->isNotEmpty())
                                        <td style="font-size:11.5px; color:#6b7280;">
                                            @if($copy->room_id && isset($locations[$copy->room_id]))
                                                <span title="Room">
                                                    <i class="feather-map-pin" style="font-size:10px;"></i>
                                                    {{ $locations[$copy->room_id]->name }}
                                                </span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        @endif
                                        <td>
                                            @if($copy->qr_code)
                                            <img src="{{ Storage::url($copy->qr_code) }}"
                                                alt="barcode" height="28"
                                                style="cursor:pointer;"
                                                onclick="showBarcode('{{ Storage::url($copy->qr_code) }}', '{{ $copy->copy_isbn }}')">
                                            @else
                                            <span class="text-muted" style="font-size:11px;">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Barcode modal --}}
<div class="modal fade" id="barcodeModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content text-center p-3">
            <div id="barcodeModalIsbn" class="mb-2 fw-semibold" style="font-size:13px;"></div>
            <img id="barcodeModalImg" src="" alt="barcode" style="max-width:100%;">
            <button class="btn btn-sm btn-outline-secondary mt-3" data-bs-dismiss="modal">Close</button>
        </div>
    </div>
</div>

<style>
/* Stats */
.sv-stat-card {
    display: flex;
    align-items: center;
    gap: 14px;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 14px 16px;
}
.sv-stat-icon {
    width: 42px; height: 42px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.sv-stat-val { font-size: 20px; font-weight: 700; color: #111827; line-height: 1; }
.sv-stat-lbl { font-size: 11px; color: #9ca3af; margin-top: 2px; }

/* Cards */
.sv-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.1rem 1.25rem;
}
.sv-section-label {
    font-size: 11px;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: .06em;
    margin-bottom: .85rem;
}

/* Meta grid */
.sv-meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px 16px; }
.sv-meta-full { grid-column: 1 / -1; }
.sv-meta-item { display: flex; flex-direction: column; gap: 2px; }
.sv-meta-lbl { font-size: 11px; color: #9ca3af; }
.sv-meta-val { font-size: 13px; color: #111827; font-weight: 500; }

/* Breakdown */
.sv-breakdown-item {
    padding: 10px 16px;
    border-radius: 8px;
    min-width: 100px;
}
.sv-breakdown-val { font-size: 22px; font-weight: 700; line-height: 1; }
.sv-breakdown-lbl { font-size: 11px; color: #6b7280; margin-top: 3px; }

/* Book cards */
.sv-book-card {
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    overflow: hidden;
}
.sv-book-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
}
.sv-book-title {
    font-size: 13.5px;
    font-weight: 600;
    color: #111827;
    flex: 1;
    min-width: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.sv-book-price {
    font-size: 12px;
    color: #16a34a;
    font-weight: 600;
}
.sv-book-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    padding: 8px 14px;
    font-size: 12px;
    color: #6b7280;
    border-bottom: 1px solid #f3f4f6;
}
.sv-book-meta i { font-size: 11px; }
.sv-copies-wrap { padding: 0 14px 10px; }
.sv-copies-table thead th {
    font-size: 11px;
    font-weight: 600;
    color: #6b7280;
    background: #f8f9fa;
    padding: 6px 8px;
}
.sv-copies-table td {
    font-size: 12px;
    padding: 6px 8px;
    vertical-align: middle;
}
.sv-expand-btn {
    padding: 2px 6px;
    font-size: 12px;
    color: #6b7280;
    background: transparent;
    border: 1px solid #e5e7eb;
    border-radius: 5px;
    line-height: 1.4;
    transition: all .12s;
}
.sv-expand-btn:hover { background: #f3f4f6; }
.sv-expand-btn.open i { transform: rotate(180deg); }
.sv-expand-btn i { transition: transform .15s; }
</style>

<script>
function toggleCopies(btn) {
    const targetId = btn.getAttribute('data-target');
    const wrap     = document.getElementById(targetId);
    if (!wrap) return;
    const isOpen = wrap.style.display !== 'none';
    wrap.style.display = isOpen ? 'none' : 'block';
    btn.classList.toggle('open', !isOpen);
}

function showBarcode(src, isbn) {
    document.getElementById('barcodeModalImg').src  = src;
    document.getElementById('barcodeModalIsbn').textContent = isbn;
    new bootstrap.Modal(document.getElementById('barcodeModal')).show();
}

// Auto-expand first title group
document.addEventListener('DOMContentLoaded', function () {
    var first = document.querySelector('.sv-expand-btn');
    if (first) toggleCopies(first);
});
</script>
{{-- Delete confirmation modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content">
            <div class="modal-body p-4 text-center">
                <div class="mb-3" style="font-size:40px; color:#ef4444;">
                    <i class="feather-alert-triangle"></i>
                </div>
                <h6 class="fw-bold mb-1">Delete this Stock Entry?</h6>
                <p class="text-muted mb-1" style="font-size:13px;">
                    Order: <code>{{ $orderId }}</code>
                </p>
                <p class="text-muted mb-3" style="font-size:12px;">
                    This will permanently delete <strong>{{ $stats->total_copies }}</strong>
                    book copies. Issued copies will be skipped.
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <button class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST"
                        action="{{ route('admin.library.stock.destroy', $orderId) }}"
                        style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm px-4">
                            <i class="feather-trash-2 me-1"></i>Yes, Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete() {
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endsection