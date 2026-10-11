@extends('admin_layout')
@section('content')

<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Academics.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            <!-- HEADER -->
            <div class="content-area-header bg-white sticky-top">
                <div class="d-flex align-items-center w-100">
                    <h5 class="fw-bold mb-0">Import Staff</h5>
                    <div class="ms-auto">
                        <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="feather-arrow-left me-1"></i>Back to Staff List
                        </a>
                    </div>
                </div>
            </div>

            <div class="content-area-body">
                @include('elements.flash')

                <!-- ════════════════════════════════════════════════
                     IMPORT SUMMARY BANNER
                ════════════════════════════════════════════════ -->
                @if(session('import_summary'))
                    @php $summary = session('import_summary'); @endphp
                    <div class="alert border-0 rounded-3 mb-4 p-4"
                         style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border-left:4px solid #22c55e !important;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:48px;height:48px;background:#22c55e20">
                                <i class="feather-bar-chart-2 text-success fs-5"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-1 text-success">Import Summary</h6>
                                <div class="d-flex gap-4 flex-wrap">
                                    <span><i class="feather-check-circle text-success me-1"></i>
                                        <strong>{{ $summary['imported'] }}</strong> imported successfully
                                    </span>
                                    <span><i class="feather-alert-circle text-danger me-1"></i>
                                        <strong>{{ $summary['errors'] }}</strong> row(s) with errors
                                    </span>
                                    <span><i class="feather-skip-forward text-warning me-1"></i>
                                        <strong>{{ $summary['skipped'] }}</strong> blank row(s) skipped
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row g-4">

                    <!-- ════════════════════════════════════════════════
                         LEFT: UPLOAD CARD
                    ════════════════════════════════════════════════ -->
                    <div class="col-lg-5">

                        <!-- Step 1: Download Template -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                         style="width:48px;height:48px;background:#EEF2FF">
                                        <i class="feather-download" style="color:#4F46E5"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1">Step 1 — Download Template</h6>
                                        <p class="text-muted small mb-3">
                                            The template already contains your current Designation &amp;
                                            Department dropdowns so you don't have to type them manually.
                                        </p>
                                        <a href="{{ route('admin.staff.import.template') }}"
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="feather-download me-1"></i>Download Template (.xlsx)
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Upload -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white p-4 pb-0 border-0">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center"
                                         style="width:40px;height:40px;background:#EEF2FF">
                                        <i class="feather-upload" style="color:#4F46E5"></i>
                                    </div>
                                    <h6 class="fw-bold mb-0">Step 2 — Upload Filled Template</h6>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <form method="POST"
                                      action="{{ route('admin.staff.import.process') }}"
                                      enctype="multipart/form-data"
                                      id="importForm">
                                    @csrf

                                    <!-- Drop Zone -->
                                    <div id="dropZone"
                                         class="rounded-3 text-center p-5 mb-3 position-relative"
                                         style="border:2px dashed #C7D2FE;background:#F5F7FF;cursor:pointer;transition:all .2s">
                                        <input type="file" name="import_file" id="importFile"
                                               accept=".xlsx,.xls" class="position-absolute top-0 start-0 w-100 h-100 opacity-0"
                                               style="cursor:pointer">
                                        <div id="dropContent">
                                            <i class="feather-file-text mb-2 d-block"
                                               style="font-size:2.5rem;color:#818CF8"></i>
                                            <p class="fw-semibold mb-1" style="color:#4F46E5">
                                                Click or drag &amp; drop your Excel file
                                            </p>
                                            <small class="text-muted">.xlsx or .xls · Max 5 MB</small>
                                        </div>
                                        <div id="fileSelected" class="d-none">
                                            <i class="feather-check-circle mb-2 d-block text-success"
                                               style="font-size:2rem"></i>
                                            <p class="fw-semibold mb-1 text-success" id="fileName">—</p>
                                            <small class="text-muted" id="fileSize">—</small>
                                        </div>
                                    </div>

                                    @error('import_file')
                                        <div class="alert alert-danger py-2 small">{{ $message }}</div>
                                    @enderror

                                    <button type="submit" class="btn btn-primary w-100" id="importBtn" disabled>
                                        <i class="feather-upload-cloud me-2"></i>Import Staff
                                    </button>

                                    <!-- Progress bar (shown on submit) -->
                                    <div id="importProgress" class="mt-3 d-none">
                                        <div class="d-flex justify-content-between small mb-1">
                                            <span>Uploading &amp; processing…</span>
                                        </div>
                                        <div class="progress" style="height:6px">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary w-100"></div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Tips -->
                        <div class="card border-0 mt-4" style="background:#FFFBEB">
                            <div class="card-body p-4">
                                <h6 class="fw-bold mb-3">
                                    <i class="feather-info text-warning me-2"></i>Tips
                                </h6>
                                <ul class="small text-muted mb-0 ps-3">
                                    <li class="mb-1">Columns with <strong>*</strong> in the header are required.</li>
                                    <li class="mb-1">Use the dropdowns in <strong>Designation</strong> and <strong>Department</strong> columns.</li>
                                    <li class="mb-1">Dates must be <strong>YYYY-MM-DD</strong> (e.g. 2024-06-01).</li>
                                    <li class="mb-1">Phone numbers must be 10–15 digits.</li>
                                    <li class="mb-1">Duplicate emails / phones will be flagged per row.</li>
                                    <li class="mb-1">The sample row 2 is for reference — it won't be imported.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- ════════════════════════════════════════════════
                         RIGHT: ERROR TABLE
                    ════════════════════════════════════════════════ -->
                    <div class="col-lg-7">

                        @if(session('import_errors') && count(session('import_errors')) > 0)
                            @php $importErrors = session('import_errors'); @endphp

                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white p-4 border-0 d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                                             style="width:40px;height:40px;background:#FEF2F2">
                                            <i class="feather-alert-triangle text-danger"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-0 text-danger">
                                                {{ count($importErrors) }} Row(s) Failed
                                            </h6>
                                            <small class="text-muted">Fix the issues and re-import those rows.</small>
                                        </div>
                                    </div>
                                    <button class="btn btn-outline-secondary btn-sm" id="downloadErrorsBtn">
                                        <i class="feather-download me-1"></i>Download Error Report
                                    </button>
                                </div>

                                <div class="card-body p-0">
                                    <div style="max-height:520px;overflow-y:auto">
                                        @foreach($importErrors as $errRow)
                                            <div class="p-3 border-bottom"
                                                 style="background:{{ $loop->even ? '#FAFAFA' : '#FFFFFF' }}">
                                                <div class="d-flex align-items-start gap-3">

                                                    <!-- Row Badge -->
                                                    <div class="badge rounded-pill px-3 py-2 flex-shrink-0"
                                                         style="background:#FEF2F2;color:#DC2626;font-size:0.75rem;font-weight:700">
                                                        Row {{ $errRow['row'] }}
                                                    </div>

                                                    <div class="flex-grow-1">
                                                        <!-- Staff name if available -->
                                                        @if(!empty($errRow['data']['first_name']) || !empty($errRow['data']['last_name']))
                                                            <p class="fw-semibold mb-1 small">
                                                                {{ trim($errRow['data']['first_name'] . ' ' . $errRow['data']['last_name']) }}
                                                                @if(!empty($errRow['data']['phone']))
                                                                    <span class="text-muted fw-normal">— {{ $errRow['data']['phone'] }}</span>
                                                                @endif
                                                            </p>
                                                        @endif

                                                        <!-- Error messages -->
                                                        <ul class="mb-0 ps-3">
                                                            @foreach($errRow['messages'] as $msg)
                                                                <li class="text-danger small">{{ $msg }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden data for CSV download -->
                            <script>
                            window.__importErrors = @json($importErrors);
                            </script>

                        @else
                            <!-- Empty state -->
                            <div class="card border-0 shadow-sm h-100 d-flex align-items-center justify-content-center">
                                <div class="card-body text-center py-5">
                                    <div class="mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center"
                                         style="width:72px;height:72px;background:#EEF2FF">
                                        <i class="feather-file-text" style="font-size:2rem;color:#818CF8"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1">No Errors</h6>
                                    <p class="text-muted small mb-0">
                                        Import errors (if any) will appear here after processing.
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

    // ── File selection / drag-drop ──────────────────────────────────────────
    const input   = document.getElementById('importFile');
    const dropZone= document.getElementById('dropZone');
    const btn     = document.getElementById('importBtn');
    const form    = document.getElementById('importForm');
    const progress= document.getElementById('importProgress');

    function showFile(file) {
        if (!file) return;
        document.getElementById('dropContent').classList.add('d-none');
        document.getElementById('fileSelected').classList.remove('d-none');
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent  = (file.size / 1024).toFixed(1) + ' KB';
        dropZone.style.borderColor = '#22c55e';
        dropZone.style.background  = '#f0fdf4';
        btn.disabled = false;
    }

    input.addEventListener('change', () => showFile(input.files[0]));

    dropZone.addEventListener('dragover', e => {
        e.preventDefault();
        dropZone.style.borderColor = '#4F46E5';
        dropZone.style.background  = '#EEF2FF';
    });
    dropZone.addEventListener('dragleave', () => {
        dropZone.style.borderColor = '#C7D2FE';
        dropZone.style.background  = '#F5F7FF';
    });
    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        const file = e.dataTransfer.files[0];
        if (file) {
            // Transfer to input
            const dt   = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;
            showFile(file);
        }
    });

    // ── Show progress on submit ─────────────────────────────────────────────
    form.addEventListener('submit', () => {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing…';
        progress.classList.remove('d-none');
    });

    // ── Download error report as CSV ────────────────────────────────────────
    const dlBtn = document.getElementById('downloadErrorsBtn');
    if (dlBtn && window.__importErrors) {
        dlBtn.addEventListener('click', () => {
            const rows = [['Row', 'Name', 'Phone', 'Email', 'Errors']];
            window.__importErrors.forEach(e => {
                rows.push([
                    e.row,
                    (e.data.first_name + ' ' + e.data.last_name).trim(),
                    e.data.phone,
                    e.data.email,
                    e.messages.join(' | ')
                ]);
            });

            const csv = rows.map(r =>
                r.map(v => `"${String(v).replace(/"/g, '""')}"`).join(',')
            ).join('\n');

            const a    = document.createElement('a');
            a.href     = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv);
            a.download = 'staff_import_errors_{{ now()->format("Ymd_His") }}.csv';
            a.click();
        });
    }
});
</script>

@endsection