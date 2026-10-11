@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Library.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Book Stock Entry</span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <a href="{{ route('admin.books.index') }}" class="btn btn-secondary btn-sm">
                            <i class="feather-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>

                <div class="content-area-body">

                    <div id="alertArea"></div>

                    <form id="stockEntryForm" enctype="multipart/form-data" novalidate>
                        @csrf

                        {{-- Bill & Withdrawal Details --}}
                        <div class="bs-card mb-4">
                            <div class="bs-section-label">Bill &amp; Withdrawal Details</div>

                            <div class="row g-3 mb-3">
                                <div class="col-lg-2 col-md-4">
                                    <label class="bs-label">Bill No. <span class="text-danger">*</span></label>
                                    <input type="text" name="bill_no" id="bill_no" class="form-control form-control-sm"
                                        placeholder="e.g. BILL-2024-001" required>
                                </div>

                                <div class="col-lg-2 col-md-4">
                                    <label class="bs-label">Bill Date <span class="text-danger">*</span></label>
                                    <input type="date" name="bill_date" id="bill_date"
                                        class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                                </div>

                                <div class="col-lg-2 col-md-4">
                                    <label class="bs-label">Withdrawal No.</label>
                                    <input type="text" name="withdrawal_no" class="form-control form-control-sm"
                                        placeholder="e.g. WD-2024-001">
                                </div>

                                <div class="col-lg-2 col-md-4">
                                    <label class="bs-label">Withdrawal Date</label>
                                    <input type="date" name="withdrawal_date" class="form-control form-control-sm">
                                </div>

                                <div class="col-lg-2 col-md-4">
                                    <label class="bs-label">Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-select form-select-sm" required>
                                        <option value="">Select status</option>
                                        <option value="received">Received</option>
                                        <option value="pending">Pending</option>
                                        <option value="returned">Returned</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>

                                <div class="col-lg-2 col-md-4">
                                    <label class="bs-label">Default Source</label>
                                    <input type="text" name="source" id="globalSource"
                                        class="form-control form-control-sm" placeholder="e.g. Vendor name">
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-lg-4">
                                    <label class="bs-label">Bill (PDF)</label>
                                    <label class="bs-upload-zone" id="uploadZone" for="billPdf">
                                        <i class="feather-upload-cloud bs-upload-icon"></i>
                                        <p class="mb-0 small fw-semibold">Click to upload or drag &amp; drop</p>
                                        <p class="mb-0 text-muted" style="font-size:11px;">.pdf files only</p>
                                        <input type="file" id="billPdf" name="bill_pdf" accept=".pdf"
                                            style="display:none">
                                    </label>
                                    <div id="uploadedFileName" class="mt-1 small text-primary fw-semibold"
                                        style="display:none;"></div>
                                </div>

                                <div class="col-lg-4">
                                    <label class="bs-label">Remarks</label>
                                    <textarea name="remarks" class="form-control form-control-sm" rows="4" placeholder="Enter remarks (optional)"></textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Books Table --}}
                        <div class="bs-card mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="bs-section-label mb-0">Books</div>
                                <span id="bookCountBadge" class="badge bg-light text-dark border">1 book</span>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered bs-books-table align-middle" id="booksTable">
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
                                            <th width="36"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="bookRows">
                                        {{-- rows injected by JS --}}
                                    </tbody>
                                </table>
                            </div>

                            <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="addRowBtn">
                                <i class="feather-plus me-1"></i>Add Book
                            </button>

                            <div class="bs-summary mt-3">
                                <div class="bs-summary-item">
                                    <div class="bs-summary-val" id="totalBooksVal">1</div>
                                    <div class="bs-summary-lbl">Books</div>
                                </div>
                                <div class="bs-summary-divider"></div>
                                <div class="bs-summary-item">
                                    <div class="bs-summary-val" id="totalQtyVal">0</div>
                                    <div class="bs-summary-lbl">Total Copies</div>
                                </div>
                                <div class="bs-summary-divider"></div>
                                <div class="bs-summary-item">
                                    <div class="bs-summary-val text-success" id="grandTotalVal">&#x20b9;0.00</div>
                                    <div class="bs-summary-lbl">Grand Total</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mb-4">
                            <button type="button" class="btn btn-secondary" id="resetBtn">
                                <i class="feather-refresh-cw me-1"></i>Reset
                            </button>
                            <button type="submit" class="btn btn-primary px-4" id="submitBtn">
                                <i class="feather-save me-1"></i>Submit Entry
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const CSRF_TOKEN = '{{ csrf_token() }}';
        const STOCK_STORE_URL = '{{ route('admin.library.stock.store') }}';
    </script>

    <style>
        .bs-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
        }

        .bs-section-label {
            font-size: 11px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 1rem;
        }

        .bs-label {
            display: block;
            font-size: 12px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 5px;
        }

        .bs-upload-zone {
            display: block;
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 18px 12px;
            text-align: center;
            cursor: pointer;
            background: #f9fafb;
            transition: all .15s;
        }

        .bs-upload-zone:hover {
            border-color: #2563eb;
            background: #eff6ff;
        }

        .bs-upload-icon {
            font-size: 22px;
            color: #9ca3af;
            display: block;
            margin-bottom: 6px;
        }

        .bs-books-table thead th {
            background: #f8f9fa;
            font-size: 11px;
            font-weight: 600;
            color: #4b5563;
            vertical-align: middle;
            padding: 7px 6px;
            white-space: nowrap;
        }

        .bs-books-table td {
            padding: 4px 5px;
            vertical-align: middle;
        }

        .bs-books-table td input,
        .bs-books-table td select {
            font-size: 11.5px;
            padding: 4px 6px;
            border-radius: 5px;
            width: 100%;
        }

        .bs-books-table td input[readonly] {
            background: #f3f4f6;
            color: #374151;
            font-weight: 600;
        }

        .bs-row-num {
            font-size: 12px;
            color: #9ca3af;
            text-align: center;
        }

        .bs-remove-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: #d1d5db;
            padding: 4px 5px;
            border-radius: 5px;
            line-height: 1;
            transition: all .12s;
        }

        .bs-remove-btn:hover {
            color: #dc2626;
            background: #fef2f2;
        }

        .bs-summary {
            display: flex;
            align-items: center;
            background: #f8f9fa;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
        }

        .bs-summary-item {
            flex: 1;
            text-align: center;
            padding: 14px 12px;
        }

        .bs-summary-val {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
            line-height: 1;
        }

        .bs-summary-lbl {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 4px;
        }

        .bs-summary-divider {
            width: 1px;
            height: 40px;
            background: #e5e7eb;
            flex-shrink: 0;
        }

        @keyframes bsRowIn {
            from {
                opacity: 0;
                transform: translateY(6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .bs-new-row {
            animation: bsRowIn .18s ease both;
        }

        .bs-invalid {
            border-color: #ef4444 !important;
            background: #fff5f5 !important;
        }

        #submitBtn .spinner-border {
            width: 14px;
            height: 14px;
            border-width: 2px;
        }
    </style>

    <script>
        (function() {
            'use strict';

            let rowIndex = 0;

            // Build a new book row.
            // Hidden fields supply the defaults the controller requires but we don't show:
            //   isbn       → empty string → controller auto-generates via generateValidIsbn13()
            //   category   → empty → controller defaults to 'General' (nullable in validator)
            //   room       → '1'  → controller's nullable fallback; adjust if your tenant
            //                        always has a default room, or make nullable in validator
            //   book_status→ 'available' always
            function addRow() {
                const tbody = document.getElementById('bookRows');
                const idx = rowIndex++;
                const n = 'books[' + idx + ']';

                const tr = document.createElement('tr');
                tr.id = 'bs_row_' + idx;
                tr.className = 'bs-new-row';

                const currentYear = new Date().getFullYear();

                tr.innerHTML =
                    // Hidden defaults (not shown to user)
                    '<input type="hidden" name="' + n + '[book_status]" value="available">' +

                    // Visible columns
                    '<td class="bs-row-num"></td>' +

                    '<td><input type="text" class="form-control form-control-sm" name="' + n +
                    '[title]" placeholder="Book title" required></td>' +

                    '<td><input type="text" class="form-control form-control-sm" name="' + n +
                    '[author]" placeholder="Author" required></td>' +

                    '<td><input type="text" class="form-control form-control-sm" name="' + n +
                    '[publisher]" placeholder="Publisher"></td>' +

                    '<td><input type="text" class="form-control form-control-sm" name="' + n +
                    '[edition]" placeholder="e.g. 1st"></td>' +

                    '<td><input type="text" class="form-control form-control-sm" name="' + n +
                    '[volume]" placeholder="Vol."></td>' +

                    '<td><input type="number" class="form-control form-control-sm text-center" name="' + n +
                    '[year]" placeholder="' + currentYear + '" min="1900" max="2026"></td>' +

                    '<td><input type="number" class="form-control form-control-sm text-center" name="' + n +
                    '[page]" placeholder="300" min="1" max="5000"></td>' +

                    '<td><input type="number" class="form-control form-control-sm text-end" name="' + n +
                    '[price]" placeholder="0.00" min="0" step="0.01" oninput="calcRow(' + idx + ')"></td>' +

                    '<td><input type="text" class="form-control form-control-sm text-center" name="' + n +
                    '[total_copies]" placeholder="1" min="1" max="100" oninput="calcRow(' + idx + ')" required></td>' +

                    '<td><input type="text" class="form-control form-control-sm text-end" id="bs_total_' + idx +
                    '" readonly placeholder="0.00"></td>' +

                    '<td><button type="button" class="bs-remove-btn" onclick="removeRow(' + idx +
                    ')" title="Remove"><i class="feather-x"></i></button></td>';

                tbody.appendChild(tr);
                updateNumbers();
                updateSummary();
                tr.querySelector('[name="' + n + '[title]"]').focus();
            }

            window.removeRow = function(idx) {
                const row = document.getElementById('bs_row_' + idx);
                if (!row) return;
                if (document.querySelectorAll('#bookRows tr').length <= 1) {
                    showAlert('warning', 'At least one book row is required.');
                    return;
                }
                row.style.opacity = '0';
                row.style.transition = 'opacity .15s';
                setTimeout(function() {
                    row.remove();
                    updateNumbers();
                    updateSummary();
                }, 150);
            };

            window.calcRow = function(idx) {
                const qty = parseFloat(document.querySelector('[name="books[' + idx + '][total_copies]"]') &&
                    document.querySelector('[name="books[' + idx + '][total_copies]"]').value) || 0;
                const price = parseFloat(document.querySelector('[name="books[' + idx + '][price]"]') && document
                    .querySelector('[name="books[' + idx + '][price]"]').value) || 0;
                const el = document.getElementById('bs_total_' + idx);
                if (el) el.value = (qty > 0 && price > 0) ? (qty * price).toFixed(2) : '';
                updateSummary();
            };

            function updateNumbers() {
                const rows = document.querySelectorAll('#bookRows tr');
                const count = rows.length;
                rows.forEach(function(tr, i) {
                    const cell = tr.querySelector('.bs-row-num');
                    if (cell) cell.textContent = i + 1;
                });
                document.getElementById('bookCountBadge').textContent = count + (count === 1 ? ' book' : ' books');
                document.getElementById('totalBooksVal').textContent = count;
            }

            function updateSummary() {
                let qty = 0,
                    grand = 0;
                document.querySelectorAll('#bookRows tr').forEach(function(tr) {
                    const qtyEl = tr.querySelector('input[name*="[total_copies]"]');
                    const totalEl = tr.querySelector('input[name*="[total]"]');
                    qty += parseFloat((qtyEl && qtyEl.value) || 0);
                    grand += parseFloat((totalEl && totalEl.value) || 0);
                });
                document.getElementById('totalQtyVal').textContent = qty;
                document.getElementById('grandTotalVal').textContent = '\u20b9' + grand.toFixed(2);
            }

            function showAlert(type, html, autoHide) {
                autoHide = (autoHide === undefined) ? true : autoHide;
                const map = {
                    success: 'alert-success',
                    danger: 'alert-danger',
                    warning: 'alert-warning',
                    info: 'alert-info'
                };
                const icons = {
                    success: 'feather-check-circle',
                    danger: 'feather-alert-circle',
                    warning: 'feather-alert-triangle',
                    info: 'feather-info'
                };
                const area = document.getElementById('alertArea');
                area.innerHTML =
                    '<div class="alert ' + (map[type] || 'alert-info') +
                    ' alert-dismissible fade show d-flex align-items-start gap-2 mb-3">' +
                    '<i class="' + (icons[type] || 'feather-info') +
                    ' mt-1" style="flex-shrink:0;font-size:16px;"></i>' +
                    '<div>' + html + '</div>' +
                    '<button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>' +
                    '</div>';
                area.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
                if (autoHide) setTimeout(function() {
                    var a = area.querySelector('.alert');
                    if (a) a.remove();
                }, 6000);
            }

            function highlightErrors(errors) {
                document.querySelectorAll('.bs-invalid').forEach(function(el) {
                    el.classList.remove('bs-invalid');
                });
                Object.keys(errors).forEach(function(key) {
                    // Laravel key: "books.0.title" -> name="books[0][title]"
                    var name = key.replace(/\.(\d+)\./g, '[$1][').replace(/\.(\w+)$/, '[$1]');
                    var el = document.querySelector('[name="' + name + '"]');
                    if (el) el.classList.add('bs-invalid');
                });
            }

            // Submit via AJAX (controller returns JSON)
            document.getElementById('stockEntryForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                // Per-row required: title, author, total_copies
                let clientOk = true;
                document.querySelectorAll('#bookRows tr').forEach(function(tr) {
                    ['input[name*="[title]"]', 'input[name*="[author]"]',
                        'input[name*="[total_copies]"]'
                    ].forEach(function(sel) {
                        var el = tr.querySelector(sel);
                        if (el && !el.value.trim()) {
                            el.classList.add('bs-invalid');
                            clientOk = false;
                        }
                    });
                });
                if (!clientOk) {
                    showAlert('warning', 'Please fill all required fields (Title, Author, Qty).');
                    return;
                }

                // Header required: bill_no, bill_date, status
                var headerOk = true;
                var bill_no = document.getElementById('bill_no');
                var bill_date = document.getElementById('bill_date');
                var statusSel = document.querySelector('[name="status"]');
                [bill_no, bill_date, statusSel].forEach(function(el) {
                    if (el && !el.value.trim()) {
                        el.classList.add('bs-invalid');
                        headerOk = false;
                    } else if (el) el.classList.remove('bs-invalid');
                });
                if (!headerOk) {
                    showAlert('warning', 'Please fill Bill No., Bill Date and Status.');
                    return;
                }

                var fd = new FormData(this);
                var btn = document.getElementById('submitBtn');
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border me-1"></span>Saving\u2026';

                try {
                    var res = await fetch(STOCK_STORE_URL, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': CSRF_TOKEN,
                            'Accept': 'application/json'
                        },
                        body: fd,
                    });
                    var data = await res.json();

                    if (res.ok) {
                        showAlert('success',
                            '<strong>\u2705 ' + data.message + '</strong><br>' +
                            'Order ID: <code>' + data.order_id + '</code> &nbsp;|&nbsp; ' +
                            'Copies created: <strong>' + data.copies_created + '</strong>');
                        this.reset();
                        document.getElementById('bookRows').innerHTML = '';
                        rowIndex = 0;
                        document.getElementById('uploadedFileName').style.display = 'none';
                        document.querySelector('.bs-upload-zone').style.borderColor = '#d1d5db';
                        addRow();
                    } else if (res.status === 422) {
                        highlightErrors(data.errors || {});
                        var errHtml =
                            '<strong>Please fix the following errors:</strong><ul class="mb-0 mt-1">';
                        Object.values(data.errors || {}).forEach(function(msgs) {
                            msgs.forEach(function(m) {
                                errHtml += '<li>' + m + '</li>';
                            });
                        });
                        errHtml += '</ul>';
                        showAlert('danger', errHtml, false);
                    } else {
                        showAlert('danger', '<strong>Error:</strong> ' + (data.message ||
                            'Something went wrong.'));
                    }
                } catch (err) {
                    showAlert('danger', '<strong>Network error:</strong> ' + err.message);
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="feather-save me-1"></i>Submit Entry';
                }
            });

            document.getElementById('addRowBtn').addEventListener('click', addRow);

            document.getElementById('resetBtn').addEventListener('click', function() {
                if (!confirm('Reset all entries?')) return;
                document.getElementById('stockEntryForm').reset();
                document.getElementById('bookRows').innerHTML = '';
                rowIndex = 0;
                document.getElementById('uploadedFileName').style.display = 'none';
                document.querySelector('.bs-upload-zone').style.borderColor = '#d1d5db';
                document.getElementById('alertArea').innerHTML = '';
                addRow();
            });

            document.getElementById('billPdf').addEventListener('change', function() {
                var fn = document.getElementById('uploadedFileName');
                if (this.files[0]) {
                    fn.textContent = '\ud83d\udcce ' + this.files[0].name;
                    fn.style.display = 'block';
                    document.querySelector('.bs-upload-zone').style.borderColor = '#16a34a';
                }
            });

            var zone = document.getElementById('uploadZone');
            zone.addEventListener('dragover', function(e) {
                e.preventDefault();
                zone.style.borderColor = '#2563eb';
            });
            zone.addEventListener('dragleave', function() {
                zone.style.borderColor = '#d1d5db';
            });
            zone.addEventListener('drop', function(e) {
                e.preventDefault();
                zone.style.borderColor = '#d1d5db';
                var file = e.dataTransfer.files[0];
                if (file && file.type === 'application/pdf') {
                    var dt = new DataTransfer();
                    dt.items.add(file);
                    document.getElementById('billPdf').files = dt.files;
                    document.getElementById('billPdf').dispatchEvent(new Event('change'));
                } else {
                    showAlert('warning', 'Please drop a PDF file.');
                }
            });

            document.addEventListener('input', function(e) {
                if (e.target.classList.contains('bs-invalid') && e.target.value.trim())
                    e.target.classList.remove('bs-invalid');
            });
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('bs-invalid') && e.target.value.trim())
                    e.target.classList.remove('bs-invalid');
            });

            addRow();
        })();
    </script>
@endsection
