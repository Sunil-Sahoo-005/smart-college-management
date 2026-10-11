@extends('admin_layout')
@section('content')

<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Library.sidebar')
        
        <div class="content-area" data-scrollbar-target="#psScrollbarInit">
            <div class="content-area-header bg-white sticky-top">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="feather-camera me-2"></i>
                        Scan Barcode
                    </h5>
                    <a href="{{ route('admin.books.index') }}" class="btn btn-outline-secondary">
                        <i class="feather-arrow-left me-1"></i>Back to List
                    </a>
                </div>
            </div>

            <div class="content-area-body">
                @include('elements.flash')
                
                <div class="row g-4">
                    {{-- LEFT COLUMN --}}
                    <div class="col-4">
                        <div class="card sticky-top" style="top: 80px;">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">
                                    <i class="feather-camera me-2"></i>
                                    Barcode Scanner
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="scanner-wrapper mb-3">
                                    <div id="scanner-container">
                                        <video id="scanner-video" style="width:100%;height:100%;object-fit:cover;"></video>
                                    </div>
                                </div>
                                
                                <div class="alert alert-info">
                                    <i class="feather-info me-2"></i>
                                    <strong>Tips:</strong>"Keep the barcode flat, 10-20cm away from the camera, in good lighting"
                                </div>
                                
                                <hr>
                                
                                <form id="manual-scan-form" class="mt-3">
                                    @csrf
                                    <label class="form-label fw-bold">Manual Entry</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control" 
                                               id="manual-barcode" 
                                               placeholder="Enter barcode manually"
                                               autofocus>
                                        <button class="btn btn-primary" type="submit">
                                            <i class="feather-search"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT COLUMN --}}
                    <div class="col-8">
                        <div class="text-center py-5" id="loading" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Searching for book...</p>
                        </div>

                        <div class="card" id="not-found-card" style="display: none;">
                            <div class="card-body text-center py-5">
                                <i class="feather-alert-circle display-1 text-warning mb-3"></i>
                                <h5>Book Not Found</h5>
                                <p class="text-muted" id="not-found-message"></p>
                                <button class="btn btn-primary mt-3" onclick="resetScanner()">
                                    <i class="feather-refresh-cw me-2"></i>Scan Again
                                </button>
                            </div>
                        </div>

                        <div class="card" id="results-card" style="display: none;">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="feather-book-open me-2"></i>
                                    Book Details
                                </h6>
                                <span class="badge bg-success" id="scan-type-badge">Copy</span>
                            </div>
                            <div class="card-body" id="results-content"></div>
                        </div>

                        <div class="card" id="initial-state">
                            <div class="card-body text-center py-5">
                                <i class="feather-camera display-1 text-muted mb-3"></i>
                                <h5>Ready to Scan</h5>
                                <p class="text-muted">
                                    Scan a barcode using your camera<br>
                                    or enter it manually in the left panel
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/@zxing/library@latest/umd/index.min.js"></script>

<script>
let codeReader = null;
let selectedDeviceId = null;

document.addEventListener('DOMContentLoaded', function () {
    initializeScanner();

    document.getElementById('manual-scan-form').addEventListener('submit', function (e) {
        e.preventDefault();
        const barcode = document.getElementById('manual-barcode').value.trim();
        if (barcode) searchBook(barcode);
    });
});

function initializeScanner() {
    codeReader = new ZXing.BrowserMultiFormatReader();

    codeReader.listVideoInputDevices().then(videoInputDevices => {
        if (videoInputDevices.length === 0) {
            showScannerError('No camera found on this device');
            return;
        }

        const backCamera = videoInputDevices.find(d =>
            d.label.toLowerCase().includes('back') ||
            d.label.toLowerCase().includes('rear') ||
            d.label.toLowerCase().includes('environment')
        );
        selectedDeviceId = backCamera ? backCamera.deviceId : videoInputDevices[0].deviceId;

        codeReader.decodeFromVideoDevice(selectedDeviceId, 'scanner-video', (result, err) => {
            if (result) {
                const code = result.getText();
                if (navigator.vibrate) navigator.vibrate(200);
                codeReader.reset();
                document.getElementById('manual-barcode').value = code;
                searchBook(code);
            }
        });

    }).catch(err => {
        showScannerError('Camera access failed: ' + err.message);
    });
}

function searchBook(barcode) {
    document.getElementById('initial-state').style.display = 'none';
    document.getElementById('loading').style.display = 'block';
    document.getElementById('results-card').style.display = 'none';
    document.getElementById('not-found-card').style.display = 'none';

    fetch('{{ route("admin.books.scan-barcode") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ barcode: barcode })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('loading').style.display = 'none';
        if (data.success) {
            displayResults(data);
        } else {
            showNotFound(data.message);
        }
    })
    .catch(error => {
        document.getElementById('loading').style.display = 'none';
        showNotFound('Error searching. Please try again.');
    });
}

function showNotFound(message) {
    document.getElementById('not-found-message').textContent = message;
    document.getElementById('not-found-card').style.display = 'block';
    document.getElementById('initial-state').style.display = 'none';
    document.getElementById('results-card').style.display = 'none';
    document.getElementById('loading').style.display = 'none';
}

function displayResults(data) {
    const resultsCard    = document.getElementById('results-card');
    const resultsContent = document.getElementById('results-content');
    const badge          = document.getElementById('scan-type-badge');

    badge.textContent = 'Individual Copy';
    badge.className   = 'badge bg-success';

    const b = data.book;
    const loc = data.locations || {};
    const stats = data.stats || {};
    const history = data.issue_history || [];

    // Status color
    const statusColor = {
        available:    'success',
        damaged:      'warning',
        lost:         'danger',
        under_repair: 'info',
        issued:       'primary'
    };

    // Location string
    const locationStr = [
        loc.room?.name,
        loc.row?.name,
        loc.self?.name
    ].filter(Boolean).join(' → ') || 'Not assigned';

    // Issue history rows
    const historyRows = history.length > 0
        ? history.map(h => `
            <tr>
                <td>${h.issued_to}</td>
                <td><span class="badge bg-light text-dark">${h.type}</span></td>
                <td>${h.issue_date}</td>
                <td>${h.due_date}</td>
                <td>${h.return_date
                    ? `<span class="text-success">${h.return_date}</span>`
                    : `<span class="badge bg-warning text-dark">Not Returned</span>`}</td>
                <td>
                    <span class="badge bg-${h.status === 'returned' ? 'success' : 'primary'}">
                        ${h.status}
                    </span>
                </td>
                <td>${h.fine > 0
                    ? `<span class="text-danger">₹${h.fine}</span>`
                    : `<span class="text-muted">—</span>`}</td>
            </tr>`
        ).join('')
        : `<tr><td colspan="7" class="text-center text-muted py-3">No issue history found</td></tr>`;

    // Copies rows
    const copiesRows = (data.copies || []).map(copy => `
        <tr>
            <td><strong>#${copy.copy_number || 1}</strong></td>
            <td><code style="font-size:11px;">${copy.copy_isbn || copy.isbn}</code></td>
            <td><span class="badge bg-${statusColor[copy.status] || 'secondary'}">${copy.status}</span></td>
        </tr>`
    ).join('');

    let html = `
        <div class="row align-items-start mb-4">
            <div class="col-md-3 text-center">
                ${b.book_image
                    ? `<img src="/storage/${b.book_image}" class="img-fluid rounded shadow-sm" style="max-height:160px;">`
                    : `<div class="bg-light rounded d-flex align-items-center justify-content-center" style="height:160px;">
                           <i class="feather-book" style="font-size:48px;color:#9ca3af;"></i>
                       </div>`
                }
                ${b.qr_code
                    ? `<img src="/storage/${b.qr_code}" class="img-fluid mt-2 rounded" style="max-height:60px;" title="Barcode">`
                    : ''}
            </div>
            <div class="col-md-9">
                <h5 class="mb-1 fw-bold">${b.title}</h5>
                <p class="text-muted mb-2">by <strong>${b.author}</strong></p>

                <div class="mb-2">
                    <span class="badge bg-${statusColor[b.status] || 'secondary'} me-1">
                        ${b.status}
                    </span>
                    <span class="badge bg-light text-dark">Copy #${b.copy_number || 1} of ${stats.total}</span>
                </div>

                <div class="row g-2 small mt-2">
                    <div class="col-6 col-md-4">
                        <span class="text-muted d-block">ISBN</span>
                        <code style="font-size:11px;">${b.isbn}</code>
                    </div>
                    <div class="col-6 col-md-4">
                        <span class="text-muted d-block">Publisher</span>
                        <strong>${b.publisher || '—'}</strong>
                    </div>
                    <div class="col-6 col-md-4">
                        <span class="text-muted d-block">Edition</span>
                        <strong>${b.edition || '—'}</strong>
                    </div>
                    <div class="col-6 col-md-4">
                        <span class="text-muted d-block">Category</span>
                        <strong>${b.category || '—'}</strong>
                    </div>
                    <div class="col-6 col-md-4">
                        <span class="text-muted d-block">Year</span>
                        <strong>${b.year || '—'}</strong>
                    </div>
                    <div class="col-6 col-md-4">
                        <span class="text-muted d-block">Pages</span>
                        <strong>${b.page || '—'}</strong>
                    </div>
                    <div class="col-6 col-md-4">
                        <span class="text-muted d-block">Volume</span>
                        <strong>${b.volume || '—'}</strong>
                    </div>
                    <div class="col-6 col-md-4">
                        <span class="text-muted d-block">Price</span>
                        <strong>${b.price ? '₹' + b.price : '—'}</strong>
                    </div>
                    <div class="col-6 col-md-4">
                        <span class="text-muted d-block">Source</span>
                        <strong>${b.source || '—'}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-3 bg-light rounded mb-4">
            <p class="mb-1 fw-semibold small text-uppercase text-muted">📍 Physical Location</p>
            <span class="fw-bold">${locationStr}</span>
        </div>

       

       

        <div class="mb-3">
            <p class="fw-semibold small text-uppercase text-muted mb-2">📋 Issue History</p>
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Issued To</th>
                            <th>Type</th>
                            <th>Issue Date</th>
                            <th>Due Date</th>
                            <th>Return Date</th>
                            <th>Status</th>
                           
                        </tr>
                    </thead>
                    <tbody>${historyRows}</tbody>
                </table>
            </div>
        </div>

        <hr>
        <div class="d-flex justify-content-between">
            <a href="/admin/books/${encodeURIComponent(b.title+'|'+(b.author||'')+'|'+(b.publisher||'')+'|'+(b.edition||''))}" class="btn btn-sm btn-outline-primary">
                <i class="feather-eye me-1"></i>View All Copies
            </a>
            <button class="btn btn-sm btn-outline-secondary" onclick="resetScanner()">
                <i class="feather-refresh-cw me-1"></i>Scan Again
            </button>
        </div>`;

    resultsContent.innerHTML = html;
    resultsCard.style.display = 'block';
}

function resetScanner() {
    if (codeReader) codeReader.reset();
    document.getElementById('manual-barcode').value = '';
    document.getElementById('initial-state').style.display = 'block';
    document.getElementById('results-card').style.display = 'none';
    document.getElementById('not-found-card').style.display = 'none';
    document.getElementById('scanner-container').innerHTML = 
        '<video id="scanner-video" style="width:100%;height:100%;object-fit:cover;"></video>';
    setTimeout(() => initializeScanner(), 800);
}

function showScannerError(message) {
    document.getElementById('scanner-container').innerHTML = `
        <div class="alert alert-warning m-0 text-center p-3">
            <i class="feather-alert-circle me-2"></i>${message}
        </div>`;
}

window.addEventListener('beforeunload', function () {
    if (codeReader) codeReader.reset();
});
</script>

<style>
#scanner-container {
    width: 100%;
    height: 300px;
    border-radius: 8px;
    border: 2px solid #e5e7eb;
    background: #000;
    overflow: hidden;
    position: relative;
}

#scanner-container video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.scanner-wrapper {
    background: #000;
    border-radius: 8px;
}

.sticky-top { z-index: 10; }

.badge.bg-primary-subtle { background-color: rgba(55,138,221,0.1); color: #185FA5; }
.badge.bg-success-subtle { background-color: rgba(29,158,117,0.1); color: #1D9E75; }
.badge.bg-warning-subtle { background-color: rgba(239,159,39,0.1); color: #EF9F27; }
.badge.bg-danger-subtle  { background-color: rgba(226,75,74,0.1);  color: #E24B4A; }

@media (max-width: 768px) {
    .sticky-top { position: relative; top: 0; }
}
</style>

@endsection