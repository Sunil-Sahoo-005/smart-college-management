@extends('admin_layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Library.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            <div class="content-area-header bg-white sticky-top">
                <div>
                    <h5 class="fw-bold mb-0">Edit Stock Entry</h5>
                    <p class="text-muted mb-0" style="font-size:12px;">Order: <code>{{ $orderId }}</code></p>
                </div>
                <div class="page-header-right ms-auto d-flex gap-2">
                    <a href="{{ route('admin.library.stock.show', $orderId) }}" class="btn btn-secondary btn-sm">
                        <i class="feather-arrow-left me-1"></i>Back
                    </a>
                </div>
            </div>

            <div class="content-area-body">

                <div id="alertArea"></div>

                <form id="editStockForm" enctype="multipart/form-data" novalidate>
                    @csrf
                    @method('PUT')

                    {{-- Bill & Withdrawal Details --}}
                    <div class="bs-card mb-4">
                        <div class="bs-section-label">Bill &amp; Withdrawal Details</div>

                        <div class="row g-3 mb-3">
                            <div class="col-lg-2 col-md-4">
                                <label class="bs-label">Bill No.</label>
                                <input type="text" name="bill_no" class="form-control form-control-sm"
                                    value="{{ $header->bill_no }}" placeholder="e.g. BILL-2024-001">
                            </div>
                            <div class="col-lg-2 col-md-4">
                                <label class="bs-label">Bill Date</label>
                                <input type="date" name="bill_date" class="form-control form-control-sm"
                                    value="{{ $header->bill_date }}">
                            </div>
                            <div class="col-lg-2 col-md-4">
                                <label class="bs-label">Withdrawal No.</label>
                                <input type="text" name="withdrawal_no" class="form-control form-control-sm"
                                    value="{{ $header->withdrawal_no }}" placeholder="e.g. WD-2024-001">
                            </div>
                            <div class="col-lg-2 col-md-4">
                                <label class="bs-label">Withdrawal Date</label>
                                <input type="date" name="withdrawal_date" class="form-control form-control-sm"
                                    value="{{ $header->withdrawal_date }}">
                            </div>
                            <div class="col-lg-2 col-md-4">
                                <label class="bs-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select form-select-sm" required>
                                    @foreach(['received','pending','returned','cancelled'] as $s)
                                    <option value="{{ $s }}" @selected($header->status === $s)>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-4">
                                <label class="bs-label">Source</label>
                                <input type="text" name="source" class="form-control form-control-sm"
                                    value="{{ $header->source }}" placeholder="Vendor / source">
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-lg-4">
                                <label class="bs-label">Replace Bill PDF</label>
                                <label class="bs-upload-zone" id="uploadZone" for="billPdf">
                                    <i class="feather-upload-cloud bs-upload-icon"></i>
                                    @if($header->bill_pdf)
                                        <p class="mb-0 small text-success fw-semibold">
                                            <i class="feather-check-circle me-1"></i>PDF already uploaded
                                        </p>
                                        <p class="mb-0 text-muted" style="font-size:11px;">Upload to replace</p>
                                    @else
                                        <p class="mb-0 small fw-semibold">Click to upload or drag &amp; drop</p>
                                        <p class="mb-0 text-muted" style="font-size:11px;">.pdf files only</p>
                                    @endif
                                    <input type="file" id="billPdf" name="bill_pdf" accept=".pdf" style="display:none">
                                </label>
                                <div id="uploadedFileName" class="mt-1 small text-primary fw-semibold" style="display:none;"></div>
                            </div>
                            <div class="col-lg-4">
                                <label class="bs-label">Remarks</label>
                                <textarea name="remarks" class="form-control form-control-sm" rows="4"
                                    placeholder="Enter remarks (optional)">{{ $header->remarks }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Books Table --}}
                    <div class="bs-card mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="bs-section-label mb-0">Books</div>
                            <div class="d-flex align-items-center gap-2">
                                <span id="bookCountBadge" class="badge bg-light text-dark border">
                                    {{ $titleRows->count() }} {{ Str::plural('book', $titleRows->count()) }}
                                </span>
                                <span class="badge bg-warning bg-opacity-15 text-warning" style="font-size:11px;">
                                    <i class="feather-info me-1" style="font-size:10px;"></i>
                                    Reducing qty won't delete issued copies
                                </span>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered bs-books-table align-middle">
                                <thead>
                                    <tr>
                                        <th width="32">#</th>
                                        <th style="min-width:170px;">Title <span class="text-danger">*</span></th>
                                        <th style="min-width:130px;">Author <span class="text-danger">*</span></th>
                                        <th style="min-width:110px;">Publisher</th>
                                        <th style="min-width:90px;">Edition</th>
                                        <th style="min-width:90px;">Volume</th>
                                        <th style="min-width:65px;">Year</th>
                                        <th style="min-width:65px;">Pages</th>
                                        <th style="min-width:80px;">Price (&#x20b9;)</th>
                                        <th width="55">Qty <span class="text-danger">*</span></th>
                                        <th width="90">Total (&#x20b9;)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($titleRows as $i => $book)
                                    <tr>
                                        {{-- Hidden: the representative copy ID so controller knows which title group to update --}}
                                        <input type="hidden" name="books[{{ $i }}][book_id]" value="{{ $book->id }}">
                                        <input type="hidden" name="books[{{ $i }}][book_status]" value="available">

                                        <td class="text-muted text-center" style="font-size:12px;">{{ $i + 1 }}</td>

                                        <td><input type="text" class="form-control form-control-sm"
                                            name="books[{{ $i }}][title]"
                                            value="{{ $book->title }}" required></td>

                                        <td><input type="text" class="form-control form-control-sm"
                                            name="books[{{ $i }}][author]"
                                            value="{{ $book->author }}" required></td>

                                        <td><input type="text" class="form-control form-control-sm"
                                            name="books[{{ $i }}][publisher]"
                                            value="{{ $book->publisher }}"></td>

                                        <td><input type="text" class="form-control form-control-sm"
                                            name="books[{{ $i }}][edition]"
                                            value="{{ $book->edition }}"></td>

                                        <td><input type="text" class="form-control form-control-sm"
                                            name="books[{{ $i }}][volume]"
                                            value="{{ $book->volume }}"></td>

                                        <td><input type="number" class="form-control form-control-sm text-center"
                                            name="books[{{ $i }}][year]"
                                            value="{{ $book->year }}" min="1900" max="2026"></td>

                                        <td><input type="number" class="form-control form-control-sm text-center"
                                            name="books[{{ $i }}][page]"
                                            value="{{ $book->page }}" min="1" max="5000"></td>

                                        <td><input type="number" class="form-control form-control-sm text-end"
                                            name="books[{{ $i }}][price]"
                                            value="{{ $book->price }}"
                                            min="0" step="0.01"
                                            oninput="calcRow({{ $i }})"></td>

                                        <td><input type="number" class="form-control form-control-sm text-center"
                                            name="books[{{ $i }}][total_copies]"
                                            id="qty_{{ $i }}"
                                            value="{{ $book->actual_copy_count }}"
                                            min="1" max="100"
                                            oninput="calcRow({{ $i }})" required></td>

                                        <td><input type="text" class="form-control form-control-sm text-end"
                                            id="total_{{ $i }}" readonly
                                            value="{{ $book->actual_copy_count > 0 && $book->price > 0 ? number_format($book->actual_copy_count * $book->price, 2) : '' }}"></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Summary bar --}}
                        <div class="bs-summary mt-3">
                            <div class="bs-summary-item">
                                <div class="bs-summary-val">{{ $titleRows->count() }}</div>
                                <div class="bs-summary-lbl">Titles</div>
                            </div>
                            <div class="bs-summary-divider"></div>
                            <div class="bs-summary-item">
                                <div class="bs-summary-val" id="totalQtyVal">{{ $titleRows->sum('actual_copy_count') }}</div>
                                <div class="bs-summary-lbl">Total Copies</div>
                            </div>
                            <div class="bs-summary-divider"></div>
                            <div class="bs-summary-item">
                                <div class="bs-summary-val text-success" id="grandTotalVal">
                                    &#x20b9;{{ number_format($titleRows->sum(fn($b) => $b->actual_copy_count * ($b->price ?? 0)), 2) }}
                                </div>
                                <div class="bs-summary-lbl">Grand Total</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mb-4">
                        <a href="{{ route('admin.library.stock.show', $orderId) }}"
                            class="btn btn-secondary">
                            <i class="feather-x me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary px-4" id="submitBtn">
                            <i class="feather-save me-1"></i>Save Changes
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const UPDATE_URL  = '{{ route('admin.library.stock.update', $orderId) }}';
    const CSRF_TOKEN  = '{{ csrf_token() }}';
    const ROW_COUNT   = {{ $titleRows->count() }};
</script>

<style>
.bs-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:1.25rem 1.5rem; }
.bs-section-label { font-size:11px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.06em; margin-bottom:1rem; }
.bs-label { display:block; font-size:12px; font-weight:500; color:#374151; margin-bottom:5px; }
.bs-upload-zone { display:block; border:2px dashed #d1d5db; border-radius:8px; padding:18px 12px; text-align:center; cursor:pointer; background:#f9fafb; transition:all .15s; }
.bs-upload-zone:hover { border-color:#2563eb; background:#eff6ff; }
.bs-upload-icon { font-size:22px; color:#9ca3af; display:block; margin-bottom:6px; }
.bs-books-table thead th { background:#f8f9fa; font-size:11px; font-weight:600; color:#4b5563; padding:7px 6px; white-space:nowrap; }
.bs-books-table td { padding:4px 5px; vertical-align:middle; }
.bs-books-table td input, .bs-books-table td select { font-size:11.5px; padding:4px 6px; border-radius:5px; width:100%; }
.bs-books-table td input[readonly] { background:#f3f4f6; color:#374151; font-weight:600; }
.bs-summary { display:flex; align-items:center; background:#f8f9fa; border:1px solid #e5e7eb; border-radius:10px; overflow:hidden; }
.bs-summary-item { flex:1; text-align:center; padding:14px 12px; }
.bs-summary-val { font-size:20px; font-weight:700; color:#111827; line-height:1; }
.bs-summary-lbl { font-size:11px; color:#9ca3af; margin-top:4px; }
.bs-summary-divider { width:1px; height:40px; background:#e5e7eb; flex-shrink:0; }
.bs-invalid { border-color:#ef4444 !important; background:#fff5f5 !important; }
#submitBtn .spinner-border { width:14px; height:14px; border-width:2px; }
</style>

<script>
(function () {
    // Recalculate row total
    window.calcRow = function (idx) {
        var qty   = parseFloat(document.getElementById('qty_' + idx)?.value)   || 0;
        var price = parseFloat(document.querySelector('[name="books[' + idx + '][price]"]')?.value) || 0;
        var el    = document.getElementById('total_' + idx);
        if (el) el.value = (qty > 0 && price > 0) ? (qty * price).toFixed(2) : '';
        updateSummary();
    };

    function updateSummary() {
        var qty = 0, grand = 0;
        for (var i = 0; i < ROW_COUNT; i++) {
            qty   += parseFloat(document.getElementById('qty_' + i)?.value)     || 0;
            grand += parseFloat(document.getElementById('total_' + i)?.value)   || 0;
        }
        document.getElementById('totalQtyVal').textContent  = qty;
        document.getElementById('grandTotalVal').textContent = '\u20b9' + grand.toFixed(2);
    }

    function showAlert(type, html, autoHide) {
        autoHide = autoHide === undefined ? true : autoHide;
        var map   = { success:'alert-success', danger:'alert-danger', warning:'alert-warning' };
        var icons = { success:'feather-check-circle', danger:'feather-alert-circle', warning:'feather-alert-triangle' };
        var area  = document.getElementById('alertArea');
        area.innerHTML =
            '<div class="alert ' + (map[type]||'alert-info') + ' alert-dismissible fade show d-flex align-items-start gap-2 mb-3">' +
            '<i class="' + (icons[type]||'feather-info') + ' mt-1" style="flex-shrink:0;font-size:16px;"></i>' +
            '<div>' + html + '</div>' +
            '<button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button></div>';
        area.scrollIntoView({ behavior:'smooth', block:'nearest' });
        if (autoHide) setTimeout(function(){ var a=area.querySelector('.alert'); if(a) a.remove(); }, 5000);
    }

    // Submit via AJAX (PUT)
    document.getElementById('editStockForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        // Validate required per row
        var ok = true;
        document.querySelectorAll('[name*="[title]"],[name*="[author]"],[name*="[total_copies]"]').forEach(function (el) {
            if (el.name.startsWith('books') && !el.value.trim()) {
                el.classList.add('bs-invalid'); ok = false;
            }
        });
        if (!ok) { showAlert('warning', 'Please fill all required fields.'); return; }

        var statusEl = document.querySelector('[name="status"]');
        if (!statusEl.value) { statusEl.classList.add('bs-invalid'); showAlert('warning', 'Status is required.'); return; }

        var fd  = new FormData(this);
        var btn = document.getElementById('submitBtn');
        btn.disabled  = true;
        btn.innerHTML = '<span class="spinner-border me-1"></span>Saving\u2026';

        try {
            var res  = await fetch(UPDATE_URL, {
                method: 'POST',   // Laravel treats _method=PUT via FormData
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                body: fd,
            });
            var data = await res.json();

            if (res.ok) {
                showAlert('success', '<strong>\u2705 ' + data.message + '</strong>');
                setTimeout(function () {
                    window.location.href = '{{ route('admin.library.stock.show', $orderId) }}';
                }, 1200);
            } else if (res.status === 422) {
                var errHtml = '<strong>Please fix errors:</strong><ul class="mb-0 mt-1">';
                Object.values(data.errors || {}).forEach(function (msgs) {
                    msgs.forEach(function (m) { errHtml += '<li>' + m + '</li>'; });
                });
                errHtml += '</ul>';
                showAlert('danger', errHtml, false);
            } else {
                showAlert('danger', '<strong>Error:</strong> ' + (data.message || 'Something went wrong.'));
            }
        } catch (err) {
            showAlert('danger', '<strong>Network error:</strong> ' + err.message);
        } finally {
            btn.disabled  = false;
            btn.innerHTML = '<i class="feather-save me-1"></i>Save Changes';
        }
    });

    // PDF upload feedback
    document.getElementById('billPdf').addEventListener('change', function () {
        var fn = document.getElementById('uploadedFileName');
        if (this.files[0]) {
            fn.textContent = '\ud83d\udcce ' + this.files[0].name;
            fn.style.display = 'block';
            document.querySelector('.bs-upload-zone').style.borderColor = '#16a34a';
        }
    });

    // Drag & drop
    var zone = document.getElementById('uploadZone');
    zone.addEventListener('dragover',  function(e){ e.preventDefault(); zone.style.borderColor='#2563eb'; });
    zone.addEventListener('dragleave', function(){ zone.style.borderColor='#d1d5db'; });
    zone.addEventListener('drop', function(e){
        e.preventDefault(); zone.style.borderColor='#d1d5db';
        var file = e.dataTransfer.files[0];
        if (file && file.type==='application/pdf') {
            var dt = new DataTransfer(); dt.items.add(file);
            document.getElementById('billPdf').files = dt.files;
            document.getElementById('billPdf').dispatchEvent(new Event('change'));
        } else { alert('Please drop a PDF file.'); }
    });

    // Clear invalid on input
    document.addEventListener('input',  function(e){ if(e.target.classList.contains('bs-invalid')&&e.target.value.trim()) e.target.classList.remove('bs-invalid'); });
    document.addEventListener('change', function(e){ if(e.target.classList.contains('bs-invalid')&&e.target.value.trim()) e.target.classList.remove('bs-invalid'); });
})();
</script>
@endsection