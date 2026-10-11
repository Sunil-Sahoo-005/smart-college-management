@extends('admin_layout')
@section('content')

<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Academics.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            <!-- HEADER -->
            <div class="content-area-header bg-white sticky-top px-4 py-3 border-bottom shadow-sm">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="flex-shrink-0">
                        <h5 class="fw-bold mb-0 text-dark">Import Students</h5>
                        <small class="text-muted">Bulk upload students via Excel template</small>
                    </div>
                    <div class="ms-auto">
                        <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="feather-arrow-left me-1"></i>Back to Students
                        </a>
                    </div>
                </div>
            </div>

            <div class="content-area-body">
                @include('elements.flash')

                <!-- ══════════════════════════════════════════
                     IMPORT SUMMARY BANNER
                ══════════════════════════════════════════ -->
                @if(session('import_summary'))
                    @php $summary = session('import_summary'); @endphp
                    <div class="alert border-0 rounded-3 mb-4 p-4"
                         style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border-left:5px solid #22c55e !important;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width:52px;height:52px;background:#22c55e25">
                                <i class="feather-bar-chart-2 text-success fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-2 text-success">Import Summary</h6>
                                <div class="d-flex gap-4 flex-wrap">
                                    <div class="text-center">
                                        <div class="fw-bold fs-4 text-success">{{ $summary['imported'] }}</div>
                                        <small class="text-muted">Imported</small>
                                    </div>
                                    <div class="text-center">
                                        <div class="fw-bold fs-4 text-danger">{{ $summary['errors'] }}</div>
                                        <small class="text-muted">Errors</small>
                                    </div>
                                    <div class="text-center">
                                        <div class="fw-bold fs-4 text-warning">{{ $summary['skipped'] }}</div>
                                        <small class="text-muted">Skipped</small>
                                    </div>
                                </div>
                            </div>
                            @if($summary['imported'] > 0)
                                <a href="{{ route('admin.students.index') }}" class="btn btn-success btn-sm flex-shrink-0">
                                    <i class="feather-users me-1"></i>View Students
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="row g-4">

                    <!-- ══════════════════════════════════════════
                         LEFT — UPLOAD STEPS
                    ══════════════════════════════════════════ -->
                    <div class="col-lg-5">

                        <!-- Step 1: Download Template -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                         style="width:48px;height:48px;background:#EEF2FF">
                                        <span class="fw-bold" style="color:#4F46E5">1</span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-1">Download Template</h6>
                                        <p class="text-muted small mb-3">
                                            The template is pre-loaded with your current
                                            <strong>Academic Years, Courses, Branches, Semesters,</strong> and <strong>Sections</strong>
                                            as dropdown options — no manual typing needed.
                                        </p>
                                        <a href="{{ route('admin.students.import.template') }}"
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
                                         style="width:40px;height:40px;background:#ECFDF5">
                                        <span class="fw-bold text-success">2</span>
                                    </div>
                                    <h6 class="fw-bold mb-0">Upload Filled Template</h6>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <form method="POST"
                                      action="{{ route('admin.students.import.process') }}"
                                      enctype="multipart/form-data"
                                      id="importForm">
                                    @csrf

                                    <!-- Drop Zone -->
                                    <div id="dropZone"
                                         class="rounded-3 text-center p-5 mb-3 position-relative"
                                         style="border:2px dashed #A7F3D0;background:#F0FDF4;cursor:pointer;transition:all .2s">
                                        <input type="file" name="import_file" id="importFile"
                                               accept=".xlsx,.xls"
                                               class="position-absolute top-0 start-0 w-100 h-100 opacity-0"
                                               style="cursor:pointer">
                                        <div id="dropContent">
                                            <i class="feather-upload-cloud mb-2 d-block" style="font-size:2.5rem;color:#34D399"></i>
                                            <p class="fw-semibold mb-1 text-success">Click or drag &amp; drop your Excel file</p>
                                            <small class="text-muted">.xlsx or .xls · Max 5 MB</small>
                                        </div>
                                        <div id="fileSelected" class="d-none">
                                            <i class="feather-check-circle mb-2 d-block text-success" style="font-size:2rem"></i>
                                            <p class="fw-semibold mb-1 text-success" id="fileName">—</p>
                                            <small class="text-muted" id="fileSize">—</small>
                                        </div>
                                    </div>

                                    @error('import_file')
                                        <div class="alert alert-danger py-2 small mb-3">{{ $message }}</div>
                                    @enderror

                                    <button type="submit" class="btn btn-primary w-100" id="importBtn" disabled>
                                        <i class="feather-upload-cloud me-2"></i>Import Students
                                    </button>

                                    <div id="importProgress" class="mt-3 d-none">
                                        <div class="d-flex justify-content-between small mb-1">
                                            <span class="text-muted">Uploading &amp; processing…</span>
                                        </div>
                                        <div class="progress" style="height:6px">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success w-100"></div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- System Reference -->
                        <div class="card border-0 mt-4">
                            <div class="card-header bg-white border-0 pb-0">
                                <h6 class="fw-bold small mb-0 text-muted text-uppercase" style="letter-spacing:.04em">
                                    <i class="feather-database me-1"></i>Your System Data
                                </h6>
                            </div>
                            <div class="card-body p-3 pt-2">
                                <div class="row g-2">
                                    <div class="col-4">
                                        <div class="rounded-2 p-2 text-center" style="background:#EEF2FF">
                                            <div class="fw-bold text-primary">{{ $academicYears->count() }}</div>
                                            <small class="text-muted">Academic Years</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="rounded-2 p-2 text-center" style="background:#F5F3FF">
                                            <div class="fw-bold" style="color:#6366F1">{{ $courses->count() }}</div>
                                            <small class="text-muted">Courses</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="rounded-2 p-2 text-center" style="background:#ECFDF5">
                                            <div class="fw-bold text-success">{{ $streams->count() }}</div>
                                            <small class="text-muted">Branches</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="rounded-2 p-2 text-center" style="background:#FFF7ED">
                                            <div class="fw-bold text-warning">{{ $classes->count() }}</div>
                                            <small class="text-muted">Semesters</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="rounded-2 p-2 text-center" style="background:#FDF4FF">
                                            <div class="fw-bold" style="color:#9333EA">{{ $sections->count() }}</div>
                                            <small class="text-muted">Sections</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tips -->
                        <div class="card border-0 mt-3" style="background:#FFFBEB">
                            <div class="card-body p-3">
                                <h6 class="fw-bold mb-2 small"><i class="feather-info text-warning me-1"></i>Important Notes</h6>
                                <ul class="small text-muted mb-0 ps-3">
                                    <li class="mb-1">Columns with <strong>*</strong> in the header are required.</li>
                                    <li class="mb-1">Academic Year, Course, Branch, Semester &amp; Section must <strong>exactly match</strong> what's in your system.</li>
                                    <li class="mb-1">Each student gets an <strong>auto-generated Admission Number</strong>.</li>
                                    <li class="mb-1">Default password = DOB in <code>ddmmyyyy</code> format.</li>
                                    <li class="mb-1">Fee Structures must be assigned manually after import.</li>
                                    <li class="mb-1">Hostel allocation must be done manually after import.</li>
                                    <li class="mb-1">Dates must be <strong>YYYY-MM-DD</strong> format.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- ══════════════════════════════════════════
                         RIGHT — ERROR TABLE
                    ══════════════════════════════════════════ -->
                    <div class="col-lg-7">

                        @if(session('import_errors') && count(session('import_errors')) > 0)
                            @php $importErrors = session('import_errors'); @endphp

                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white p-4 border-0">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-3 d-flex align-items-center justify-content-center"
                                                 style="width:40px;height:40px;background:#FEF2F2">
                                                <i class="feather-alert-triangle text-danger"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-0 text-danger">{{ count($importErrors) }} Row(s) Failed</h6>
                                                <small class="text-muted">Fix these issues and re-import those rows.</small>
                                            </div>
                                        </div>
                                        <button class="btn btn-outline-secondary btn-sm" id="downloadErrorsBtn">
                                            <i class="feather-download me-1"></i>Download Error Report
                                        </button>
                                    </div>

                                    <!-- Search errors -->
                                    <div class="mt-3 position-relative">
                                        <i class="feather-search position-absolute top-50 translate-middle-y text-muted"
                                           style="left:10px;font-size:13px"></i>
                                        <input type="text" id="errorSearch"
                                               class="form-control form-control-sm ps-4"
                                               placeholder="Search by name, row number, or error message…">
                                    </div>
                                </div>

                                <div class="card-body p-0">
                                    <div id="errorList" style="max-height:560px;overflow-y:auto">
                                        @foreach($importErrors as $errRow)
                                            <div class="error-item p-3 border-bottom"
                                                 style="background:{{ $loop->even ? '#FAFAFA' : '#FFFFFF' }}"
                                                 data-name="{{ strtolower(trim($errRow['data']['first_name'].' '.$errRow['data']['last_name'])) }}"
                                                 data-row="{{ $errRow['row'] }}"
                                                 data-errors="{{ strtolower(implode(' ', $errRow['messages'])) }}">
                                                <div class="d-flex align-items-start gap-3">

                                                    <div class="badge rounded-pill px-3 py-2 flex-shrink-0"
                                                         style="background:#FEF2F2;color:#DC2626;font-size:0.7rem;font-weight:700">
                                                        Row {{ $errRow['row'] }}
                                                    </div>

                                                    <div class="flex-grow-1">
                                                        @php $name = trim($errRow['data']['first_name'].' '.$errRow['data']['last_name']); @endphp
                                                        @if($name)
                                                            <p class="fw-semibold mb-1 small">
                                                                {{ $name }}
                                                                @if(!empty($errRow['data']['email']))
                                                                    <span class="text-muted fw-normal">— {{ $errRow['data']['email'] }}</span>
                                                                @endif
                                                                @if(!empty($errRow['data']['academic_year']))
                                                                    <span class="badge bg-light text-dark ms-1" style="font-size:0.65rem">
                                                                        {{ $errRow['data']['academic_year'] }} / {{ $errRow['data']['stream'] }}
                                                                    </span>
                                                                @endif
                                                            </p>
                                                        @endif

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

                                    <div id="noErrorsFound" class="text-center py-4 d-none">
                                        <small class="text-muted">No matching errors found.</small>
                                    </div>
                                </div>

                                <div class="card-footer bg-white border-top p-3">
                                    <small class="text-muted">
                                        <i class="feather-info me-1"></i>
                                        After fixing, copy the failed rows into a new file and re-import.
                                    </small>
                                </div>
                            </div>

                            <script>
                            window.__importErrors = @json($importErrors);
                            </script>

                        @else
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body d-flex align-items-center justify-content-center" style="min-height:400px">
                                    <div class="text-center">
                                        <div class="mx-auto mb-4 rounded-circle d-flex align-items-center justify-content-center"
                                             style="width:80px;height:80px;background:#EEF2FF">
                                            <i class="feather-check-circle" style="font-size:2.5rem;color:#818CF8"></i>
                                        </div>
                                        <h6 class="fw-bold mb-1">No Errors</h6>
                                        <p class="text-muted small mb-0">
                                            Import errors (if any) will appear here after processing.
                                        </p>
                                    </div>
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

    // ── File upload / drag-drop ─────────────────────────────────────────────
    const input    = document.getElementById('importFile');
    const dropZone = document.getElementById('dropZone');
    const btn      = document.getElementById('importBtn');
    const form     = document.getElementById('importForm');
    const progress = document.getElementById('importProgress');

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
        dropZone.style.borderColor = '#16a34a';
        dropZone.style.background  = '#DCFCE7';
    });
    dropZone.addEventListener('dragleave', () => {
        dropZone.style.borderColor = '#A7F3D0';
        dropZone.style.background  = '#F0FDF4';
    });
    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        const file = e.dataTransfer.files[0];
        if (file) {
            const dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;
            showFile(file);
        }
    });

    form.addEventListener('submit', () => {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing…';
        progress.classList.remove('d-none');
    });

    // ── Error search ────────────────────────────────────────────────────────
    const errorSearch = document.getElementById('errorSearch');
    if (errorSearch) {
        errorSearch.addEventListener('input', function () {
            const q = this.value.trim().toLowerCase();
            const items = document.querySelectorAll('.error-item');
            let visible = 0;
            items.forEach(item => {
                const match = !q ||
                    item.dataset.name.includes(q) ||
                    item.dataset.row.includes(q) ||
                    item.dataset.errors.includes(q);
                item.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            document.getElementById('noErrorsFound').classList.toggle('d-none', visible > 0);
        });
    }

    // ── Download error CSV ──────────────────────────────────────────────────
    const dlBtn = document.getElementById('downloadErrorsBtn');
    if (dlBtn && window.__importErrors) {
        dlBtn.addEventListener('click', () => {
            const rows = [['Row', 'Name', 'Email', 'Academic Year', 'Branch', 'Errors']];
            window.__importErrors.forEach(e => {
                rows.push([
                    e.row,
                    (e.data.first_name + ' ' + e.data.last_name).trim(),
                    e.data.email,
                    e.data.academic_year,
                    e.data.stream,
                    e.messages.join(' | ')
                ]);
            });
            const csv = rows.map(r =>
                r.map(v => `"${String(v ?? '').replace(/"/g,'""')}"`).join(',')
            ).join('\n');

            const a    = document.createElement('a');
            a.href     = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv);
            a.download = 'student_import_errors_{{ now()->format("Ymd_His") }}.csv';
            a.click();
        });
    }
});
</script>

<style>
.error-item { transition: background .15s; }
.error-item:hover { background: #FFF9F9 !important; }
</style>

@endsection