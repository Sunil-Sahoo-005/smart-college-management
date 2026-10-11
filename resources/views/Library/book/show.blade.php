@extends('admin_layout')
@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Library.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">

                {{-- ── HEADER ── --}}
                <div class="content-area-header bg-white sticky-top">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div class="d-flex align-items-center gap-3">
                            <a href="{{ route('admin.books.index') }}" class="btn btn-light btn-sm">
                                <i class="feather-arrow-left"></i>
                            </a>
                            <div>
                                <h6 class="mb-0 fw-bold">{{ $masterBook->title }}</h6>
                                <p class="text-muted mb-0" style="font-size:12px;">
                                    {{ $masterBook->author ?? 'Unknown Author' }}
                                    @if ($masterBook->publisher)· {{ $masterBook->publisher }}@endif
                                    @if ($masterBook->edition)· {{ $masterBook->edition }}@endif
                                </p>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            @if(auth()->user()->hasPermissionTo('Edit Book'))
                                <a href="{{ route('admin.books.edit', $masterBook->id) }}" class="btn btn-primary btn-sm">
                                    <i class="feather-edit-2 me-1"></i>Edit
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="content-area-body">
                    @include('elements.flash')

                    {{-- ── STATS ROW ── --}}
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md">
                            <div class="stat-pill stat-total">
                                <div class="stat-val">{{ $stats['total'] }}</div>
                                <div class="stat-lbl">Total Copies</div>
                            </div>
                        </div>
                        <div class="col-6 col-md">
                            <div class="stat-pill stat-available">
                                <div class="stat-val">{{ $stats['available'] }}</div>
                                <div class="stat-lbl">Available</div>
                            </div>
                        </div>
                        <div class="col-6 col-md">
                            <div class="stat-pill stat-issued">
                                <div class="stat-val">{{ $copies->where('status', 'issued')->count() }}</div>
                                <div class="stat-lbl">Issued</div>
                            </div>
                        </div>
                        <div class="col-6 col-md">
                            <div class="stat-pill stat-damaged">
                                <div class="stat-val">{{ $stats['damaged'] }}</div>
                                <div class="stat-lbl">Damaged</div>
                            </div>
                        </div>
                        <div class="col-6 col-md">
                            <div class="stat-pill stat-lost">
                                <div class="stat-val">{{ $stats['lost'] }}</div>
                                <div class="stat-lbl">Lost</div>
                            </div>
                        </div>
                        <div class="col-6 col-md">
                            <div class="stat-pill stat-repair">
                                <div class="stat-val">{{ $stats['repair'] }}</div>
                                <div class="stat-lbl">Under Repair</div>
                            </div>
                        </div>
                    </div>

                    {{-- ── BOOK DETAILS CARD ── --}}
                    <div class="card mb-4 border-0 shadow-sm" style="border-radius:16px;overflow:hidden;">
                        <div class="row g-0">
                            <div class="col-md-2 d-flex align-items-stretch" style="background:#f8fafc;min-height:220px;">
                                <div class="d-flex align-items-center justify-content-center w-100 p-3">
                                    @if ($masterBook->book_image)
                                        <img src="{{ Storage::url($masterBook->book_image) }}"
                                            class="img-fluid rounded-3 shadow"
                                            style="max-height:180px;object-fit:cover;" alt="Cover">
                                    @else
                                        <div class="book-cover-placeholder">
                                            <i class="feather-book-open"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="card-body p-4">
                                    <div class="row g-3">
                                        <div class="col-sm-6 col-lg-3">
                                            <div class="detail-block">
                                                <span class="detail-label">Author</span>
                                                <span class="detail-value">{{ $masterBook->author ?? '—' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-3">
                                            <div class="detail-block">
                                                <span class="detail-label">Publisher</span>
                                                <span class="detail-value">{{ $masterBook->publisher ?? '—' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-3">
                                            <div class="detail-block">
                                                <span class="detail-label">Edition</span>
                                                <span class="detail-value">{{ $masterBook->edition ?? '—' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-3">
                                            <div class="detail-block">
                                                <span class="detail-label">Year</span>
                                                <span class="detail-value">{{ $masterBook->year ?? '—' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-3">
                                            <div class="detail-block">
                                                <span class="detail-label">Category</span>
                                                <span class="detail-value">
                                                    @if ($masterBook->category)
                                                        <span class="badge bg-light text-dark border">{{ $masterBook->category }}</span>
                                                    @else —
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-3">
                                            <div class="detail-block">
                                                <span class="detail-label">Academic Group</span>
                                                <span class="detail-value">
                                                    @if ($masterBook->academicGroup)
                                                        <span class="badge bg-soft-info text-info border border-info border-opacity-25 px-2 py-1">
                                                            <i class="feather-layers me-1" style="font-size:10px;"></i>
                                                            {{ $masterBook->academicGroup->name }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">Not assigned</span>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-3">
                                            <div class="detail-block">
                                                <span class="detail-label">Pages</span>
                                                <span class="detail-value">{{ $masterBook->page ? number_format($masterBook->page) . ' pages' : '—' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-3">
                                            <div class="detail-block">
                                                <span class="detail-label">Volume</span>
                                                <span class="detail-value">{{ $masterBook->volume ?? '—' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-3">
                                            <div class="detail-block">
                                                <span class="detail-label">Price</span>
                                                <span class="detail-value">
                                                    @if ($masterBook->price) ₹{{ number_format($masterBook->price, 2) }}
                                                    @else —
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-3">
                                            <div class="detail-block">
                                                <span class="detail-label">Source</span>
                                                <span class="detail-value">{{ $masterBook->source ?? '—' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-lg-6">
                                            <div class="detail-block">
                                                <span class="detail-label">ISBN</span>
                                                <span class="detail-value font-monospace">{{ $masterBook->isbn ?? '—' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── COPIES + HISTORY ── --}}
                    <div class="row g-4">

                        {{-- Copies --}}
                        <div class="col-lg-7">
                            <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
                                <div class="card-header bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center"
                                    style="border-radius:16px 16px 0 0;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="icon-box-sm bg-primary-subtle text-primary">
                                            <i class="feather-copy" style="font-size:14px;"></i>
                                        </div>
                                        <h6 class="mb-0 fw-semibold">All Copies
                                            <span class="text-muted fw-normal">({{ $stats['total'] }})</span>
                                        </h6>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-success-subtle text-success px-3 py-1">
                                            {{ $stats['available'] }} Available
                                        </span>
                                        {{-- ✅ BULK PRINT BUTTON --}}
                                        @php $printableCopies = $copies->filter(fn($c) => $c->qr_code); @endphp
                                        @if ($printableCopies->count() > 0 && auth()->user()->hasPermissionTo('Print Book Barcodes'))
                                            <button class="btn btn-sm btn-outline-success"
                                                onclick="printAllBarcodes()"
                                                title="Print All Barcodes">
                                                <i class="feather-printer me-1"></i>Print All ({{ $printableCopies->count() }})
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive" style="max-height:520px;overflow-y:auto;">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light" style="position:sticky;top:0;z-index:1;">
                                                <tr>
                                                    <th class="ps-4">Copy</th>
                                                    <th>Copy ISBN</th>
                                                    <th>Status</th>
                                                    <th>Location</th>
                                                    <th class="text-center">Barcode</th>
                                                    <th class="text-center">Print</th>
                                                    <th class="text-end pe-4">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($copies as $copy)
                                                    <tr>
                                                        <td class="ps-4">
                                                            <span class="badge bg-primary-subtle text-primary fw-semibold px-2">
                                                                #{{ $copy->copy_number }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <code class="small text-muted">{{ $copy->copy_isbn }}</code>
                                                        </td>
                                                        <td>
                                                            @php
                                                                $statusMap = [
                                                                    'available'    => ['success',   'Available'],
                                                                    'issued'       => ['info',      'Issued'],
                                                                    'damaged'      => ['warning',   'Damaged'],
                                                                    'lost'         => ['danger',    'Lost'],
                                                                    'under_repair' => ['secondary', 'Repair'],
                                                                ];
                                                                [$sc, $sl] = $statusMap[$copy->status] ?? ['secondary', 'Unknown'];
                                                            @endphp
                                                            <span class="badge bg-{{ $sc }}-subtle text-{{ $sc }} px-2 py-1">
                                                                {{ $sl }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if ($copy->room_id && isset($libraryLocations[$copy->room_id]))
                                                                <div class="small fw-medium">{{ $libraryLocations[$copy->room_id]->name }}</div>
                                                                @if ($copy->row_id && isset($libraryLocations[$copy->row_id]))
                                                                    <div class="small text-muted">{{ $libraryLocations[$copy->row_id]->name }}</div>
                                                                @endif
                                                            @else
                                                                <span class="text-muted small">Not assigned</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if ($copy->qr_code)
                                                                <a href="{{ Storage::url($copy->qr_code) }}" target="_blank">
                                                                    <img src="{{ Storage::url($copy->qr_code) }}"
                                                                        style="width:72px;height:30px;object-fit:contain;"
                                                                        class="rounded" alt="Barcode">
                                                                </a>
                                                            @else
                                                                <span class="text-muted small">—</span>
                                                            @endif
                                                        </td>

                                                        {{-- ✅ PRINT COLUMN --}}
                                                        <td class="text-center">
                                                            @if ($copy->qr_code && auth()->user()->hasPermissionTo('Print Book Barcodes'))
                                                                <button class="btn btn-sm btn-outline-success"
                                                                    title="Print Barcode"
                                                                    onclick="printSingle(
                                                                        '{{ Storage::url($copy->qr_code) }}',
                                                                        '{{ addslashes($masterBook->title) }}',
                                                                        '{{ $copy->copy_isbn }}',
                                                                        '#{{ $copy->copy_number }}'
                                                                    )">
                                                                    <i class="feather-printer"></i>
                                                                </button>
                                                            @elseif($copy->qr_code)
                                                                <span class="text-muted small">Print disabled</span>
                                                            @else
                                                                <span class="text-muted small">—</span>
                                                            @endif
                                                        </td>

                                                        <td class="text-end pe-4">
                                                            <div class="d-flex gap-1 justify-content-end">
                                                                @if(auth()->user()->hasPermissionTo('Edit Book'))
                                                                    <a href="{{ route('admin.books.edit', $copy->id) }}"
                                                                        class="btn btn-sm btn-outline-primary" title="Edit">
                                                                        <i class="feather-edit-2"></i>
                                                                    </a>
                                                                @endif
                                                                @if(auth()->user()->hasPermissionTo('Delete Book'))
                                                                    <button class="btn btn-sm btn-outline-danger"
                                                                        onclick="confirmDelete({{ $copy->id }})"
                                                                        title="Delete">
                                                                        <i class="feather-trash-2"></i>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center py-5 text-muted">
                                                            <i class="feather-copy d-block mb-2" style="font-size:2rem;opacity:.4;"></i>
                                                            No copies found
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Issue History --}}
                        <div class="col-lg-5">
                            <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
                                <div class="card-header bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center"
                                    style="border-radius:16px 16px 0 0;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="icon-box-sm bg-warning-subtle text-warning">
                                            <i class="feather-clock" style="font-size:14px;"></i>
                                        </div>
                                        <h6 class="mb-0 fw-semibold">Issue History</h6>
                                    </div>
                                    @if ($issues->total() > 0)
                                        <span class="badge bg-info-subtle text-info px-3 py-1">
                                            {{ $issues->total() }} Records
                                        </span>
                                    @endif
                                </div>
                                <div class="card-body p-0">
                                    @if ($issues->count() > 0)
                                        <div class="table-responsive" style="max-height:480px;overflow-y:auto;">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead class="table-light" style="position:sticky;top:0;z-index:1;">
                                                    <tr>
                                                        <th class="ps-4">User</th>
                                                        <th>Issued</th>
                                                        <th>Due</th>
                                                        <th class="pe-4">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($issues as $issue)
                                                        <tr>
                                                            <td class="ps-4">
                                                                @if ($issue->student)
                                                                    <div class="fw-medium small">{{ $issue->student->first_name }} {{ $issue->student->last_name }}</div>
                                                                    <div class="text-muted" style="font-size:11px;">Student</div>
                                                                @elseif($issue->staff)
                                                                    <div class="fw-medium small">{{ $issue->staff->first_name }} {{ $issue->staff->last_name }}</div>
                                                                    <div class="text-muted" style="font-size:11px;">Staff</div>
                                                                @else
                                                                    <span class="text-muted small">Unknown</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <span class="small">
                                                                    {{ $issue->issue_date ? \Carbon\Carbon::parse($issue->issue_date)->format('d M Y') : '—' }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                @if ($issue->due_date)
                                                                    @php $due = \Carbon\Carbon::parse($issue->due_date); @endphp
                                                                    @if ($due->isPast() && $issue->status !== 'returned')
                                                                        <span class="badge bg-danger-subtle text-danger px-2" style="font-size:11px;">Overdue</span>
                                                                    @else
                                                                        <span class="small text-muted">{{ $due->format('d M') }}</span>
                                                                    @endif
                                                                @else
                                                                    <span class="text-muted">—</span>
                                                                @endif
                                                            </td>
                                                            <td class="pe-4">
                                                                @php
                                                                    $issueStatusMap = [
                                                                        'issued'   => ['info',      'Issued'],
                                                                        'returned' => ['success',   'Returned'],
                                                                        'overdue'  => ['danger',    'Overdue'],
                                                                        'lost'     => ['secondary', 'Lost'],
                                                                    ];
                                                                    [$ic, $il] = $issueStatusMap[$issue->status] ?? ['secondary', ucfirst($issue->status)];
                                                                @endphp
                                                                <span class="badge bg-{{ $ic }}-subtle text-{{ $ic }} px-2">{{ $il }}</span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="px-4 py-2 border-top bg-light" style="border-radius:0 0 16px 16px;">
                                            {{ $issues->links() }}
                                        </div>
                                    @else
                                        <div class="d-flex flex-column align-items-center justify-content-center py-5 text-center">
                                            <div class="icon-box-lg bg-light text-muted mb-3">
                                                <i class="feather-clock"></i>
                                            </div>
                                            <h6 class="fw-semibold mb-1">No Issue History</h6>
                                            <p class="text-muted mb-0 small">No copies have been issued yet.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ HIDDEN BARCODE DATA FOR BULK PRINT --}}
    <div id="barcodeData" style="display:none;"
        data-title="{{ addslashes($masterBook->title) }}">
        @foreach($copies->filter(fn($c) => $c->qr_code) as $copy)
            <div class="barcode-item"
                data-img="{{ Storage::url($copy->qr_code) }}"
                data-isbn="{{ $copy->copy_isbn }}"
                data-copy="#{{ $copy->copy_number }}">
            </div>
        @endforeach
    </div>

    <style>
        .stat-pill { border-radius:14px; padding:14px 16px; text-align:center; }
        .stat-val { font-size:1.6rem; font-weight:700; line-height:1; margin-bottom:4px; }
        .stat-lbl { font-size:11px; font-weight:500; text-transform:uppercase; letter-spacing:.05em; opacity:.7; }
        .stat-total     { background:#f1f5f9; color:#334155; }
        .stat-available { background:#dcfce7; color:#166534; }
        .stat-issued    { background:#dbeafe; color:#1e40af; }
        .stat-damaged   { background:#fef9c3; color:#854d0e; }
        .stat-lost      { background:#fee2e2; color:#991b1b; }
        .stat-repair    { background:#ede9fe; color:#5b21b6; }

        .detail-block { display:flex; flex-direction:column; gap:3px; }
        .detail-label { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.06em; color:#94a3b8; }
        .detail-value { font-size:13px; font-weight:500; color:#1e293b; }

        .book-cover-placeholder { width:90px; height:120px; background:#e2e8f0; border-radius:10px; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:2rem; }
        .icon-box-sm { width:30px; height:30px; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .icon-box-lg { width:56px; height:56px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.5rem; }

        .table > :not(caption) > * > * { padding:.65rem .75rem; }
        .table thead th { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:#64748b; background:#f8fafc; border-bottom:1px solid #e2e8f0; }
    </style>

    <script>
        // ── Single print ──────────────────────────────────────────────
        function printSingle(imgUrl, title, isbn, copyNum) {
            printWindow([{ img: imgUrl, title: title, isbn: isbn, copy: copyNum }]);
        }

        // ── Bulk print all copies ─────────────────────────────────────
        function printAllBarcodes() {
            const container = document.getElementById('barcodeData');
            const title     = container.dataset.title;
            const items     = [...container.querySelectorAll('.barcode-item')].map(el => ({
                img:   el.dataset.img,
                title: title,
                isbn:  el.dataset.isbn,
                copy:  el.dataset.copy,
            }));
            if (items.length === 0) return alert('No barcodes available to print.');
            printWindow(items);
        }

        // ── Shared print renderer ─────────────────────────────────────
        function printWindow(items) {
            const bookTitle = items[0]?.title || 'Barcode';
            const labelsHtml = items.map(item => `
                <div class="label">
                    <img src="${item.img}" class="barcode-img" alt="Barcode">
                    <div class="isbn">${item.isbn}</div>
                </div>
            `).join('');
            const win = window.open('', '_blank', 'width=900,height=600');
            win.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>${bookTitle}</title>
                    <style>
                        * { margin:0; padding:0; box-sizing:border-box; }
                        body { font-family:Arial, sans-serif; background:#fff; padding:20px; }
                        .grid { display: flex; flex-wrap: wrap; gap: 16px; justify-content: flex-start; }
                        .label { border: 1px solid #ddd; border-radius: 8px; padding: 12px 16px; text-align: center; width: 220px; }
                        .barcode-img { width: 200px; height: 90px; object-fit: contain; display: block; margin: 0 auto 0; }
                        .isbn { font-size: 11px; color: #475569; font-family: monospace; margin-top: 4px; }
                        @media print {
                            @page { margin: 10mm; }
                            body { padding: 0; }
                            .label { border: 1px solid #ccc; break-inside: avoid; }
                        }
                    </style>
                </head>
                <body>
                    <div class="grid">${labelsHtml}</div>
                    <script>
                        window.onload = function () {
                            window.print();
                            window.onafterprint = function () { window.close(); };
                        };
                    <\/script>
                </body>
                </html>
            `);
            win.document.close();
        }

        // ── Delete ────────────────────────────────────────────────────
        function confirmDelete(id) {
            if (confirm('Delete this copy? This cannot be undone.')) {
                let url = `{{ route('admin.books.destroy', ':id') }}`.replace(':id', id);
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                form.style.display = 'none';
                const csrf = document.createElement('input');
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                csrf.type = 'hidden';
                form.appendChild(csrf);
                const method = document.createElement('input');
                method.name = '_method';
                method.value = 'DELETE';
                method.type = 'hidden';
                form.appendChild(method);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@endsection