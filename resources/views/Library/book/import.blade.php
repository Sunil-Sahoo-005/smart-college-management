@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Library.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">

                {{-- TOP BAR --}}
                <div class="content-area-header bg-white sticky-top">
                    <h6 class="card-title mb-0">Import Books via Excel</h6>
                    <div class="ms-auto">
                        <a href="{{ route('admin.books.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="feather-arrow-left me-1"></i>Back to Books
                        </a>
                    </div>
                </div>

                <div class="content-area-body">
                    @include('elements.flash')

                    {{-- SUCCESS / ERROR SUMMARY --}}
                    @if (session('import_success'))
                        <div class="alert alert-success d-flex align-items-center gap-2 mb-3" role="alert">
                            <i class="feather-check-circle fs-18"></i>
                            <div>{{ session('import_success') }}</div>
                        </div>
                        @if (session('import_errors') && count(session('import_errors')))
                            <div class="alert alert-warning mb-4" role="alert">
                                <p class="fw-semibold mb-2">
                                    <i class="feather-alert-triangle me-1"></i>
                                    {{ count(session('import_errors')) }} warning(s):
                                </p>
                                <ul class="mb-0 ps-3">
                                    @foreach (session('import_errors') as $err)
                                        <li class="small">{{ $err }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    @endif

                    <div class="imp-layout">

                        {{-- ── LEFT: UPLOAD ── --}}
                        <div class="imp-main">

                            {{-- STEP 1 – Download template --}}
                            <div class="imp-card">
                                <div class="imp-step-badge">Step 1</div>
                                <p class="imp-card-title">Download the Template</p>
                                <p class="imp-card-desc">
                                    Download the official Excel template. It includes all required columns,
                                    drop-down validation for Status, and 3 sample rows to guide you.
                                </p>
                                <a href="{{ route('admin.books.template') }}" class="imp-btn-download">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                        <polyline points="7 10 12 15 17 10" />
                                        <line x1="12" y1="15" x2="12" y2="3" />
                                    </svg>
                                    Download books_import_template.xlsx
                                </a>
                                <p class="imp-hint mt-2">Supports up to 100 book titles per file (unlimited copies per
                                    title).</p>
                            </div>

                            {{-- STEP 2 – Fill template --}}
                            <div class="imp-card">
                                <div class="imp-step-badge">Step 2</div>
                                <p class="imp-card-title">Fill in Your Book Data</p>

                                <div class="imp-field-table">
                                    <div class="imp-field-head">
                                        <span>Column</span><span>Required</span><span>Notes</span>
                                    </div>
                                    @foreach ([['title', true, 'Book title'], ['author', true, 'Author name'], ['publisher', false, 'Publisher — defaults to "Auto-Publisher"'], ['edition', false, 'e.g. 3rd — defaults to "1st"'], ['category', false, 'e.g. Physics — defaults to "General"'], ['year', false, '4-digit year'], ['isbn', false, 'Leave blank → auto EAN-13 generated'], ['total_copies', true, 'How many physical copies (1–100)'], ['status', true, 'available | damaged | lost | under_repair'], ['room_name', false, 'Must match Library Master room name exactly'], ['self_name', false, 'Must match Library Master shelf name'], ['row_name', false, 'Must match Library Master row name'], ['source', false, 'e.g. Purchased, Donated'], ['page', false, 'Number of pages'], ['volume', false, 'e.g. Vol. 1'], ['price', false, 'Numeric price in local currency']] as [$col, $req, $note])
                                        <div class="imp-field-row">
                                            <span class="imp-field-name">{{ $col }}</span>
                                            <span>
                                                @if ($req)
                                                    <span class="imp-badge-req">Required</span>
                                                @else
                                                    <span class="imp-badge-opt">Optional</span>
                                                @endif
                                            </span>
                                            <span class="imp-field-note">{{ $note }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- STEP 3 – Upload --}}
                            <div class="imp-card">
                                <div class="imp-step-badge">Step 3</div>
                                <p class="imp-card-title">Upload &amp; Import</p>
                                <p class="imp-card-desc">
                                    Upload your completed file. The system will validate each row,
                                    create book copies, and generate EAN-13 barcodes automatically.
                                </p>

                                <form id="importForm" action="{{ route('admin.books.import.post') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf

                                    {{-- Drop zone --}}
                                    <div class="imp-drop-zone" id="impDropZone"
                                        onclick="document.getElementById('impFileInput').click()">
                                        <div id="impDropDefault">
                                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none"
                                                stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" style="margin:0 auto 10px;display:block;">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                                <polyline points="14 2 14 8 20 8" />
                                                <line x1="12" y1="18" x2="12" y2="12" />
                                                <line x1="9" y1="15" x2="15" y2="15" />
                                            </svg>
                                            <p class="imp-drop-title">Click to choose file</p>
                                            <p class="imp-drop-sub">or drag &amp; drop here · .xlsx or .xls · max 5 MB</p>
                                        </div>
                                        <div id="impDropSelected" style="display:none;">
                                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none"
                                                stroke="#1D9E75" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" style="margin:0 auto 8px;display:block;">
                                                <polyline points="20 6 9 17 4 12" />
                                            </svg>
                                            <p class="imp-drop-title" id="impFileName" style="color:#1D9E75;">—</p>
                                            <p class="imp-drop-sub">Click to change file</p>
                                        </div>
                                    </div>

                                    <input type="file" id="impFileInput" name="import_file" accept=".xlsx,.xls"
                                        style="display:none;" onchange="impHandleFile(this)">

                                    @error('import_file')
                                        <p class="imp-error mt-2">{{ $message }}</p>
                                    @enderror

                                    <button type="submit" class="imp-btn-submit" id="impSubmitBtn" disabled>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <polyline points="16 16 12 12 8 16" />
                                            <line x1="12" y1="12" x2="12" y2="21" />
                                            <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3" />
                                        </svg>
                                        Import Books
                                    </button>
                                </form>
                            </div>

                        </div>{{-- end .imp-main --}}

                        {{-- ── RIGHT: TIPS ── --}}
                        <div class="imp-sidebar">
                            <div class="imp-card">
                                <p class="imp-card-title">
                                    <i class="feather-info" style="color:#378ADD;margin-right:6px;"></i>
                                    Tips &amp; Rules
                                </p>
                                <ul class="imp-tips-list">
                                    <li>Start data from <strong>row 8</strong> — rows 5–7 are sample rows (you can delete
                                        them).</li>
                                    <li>Each Excel row creates <strong>one book title</strong> with N copies.</li>
                                    <li>Each copy gets its own barcode: <code>ISBN-001</code>, <code>ISBN-002</code>…</li>
                                    <li>Leaving <strong>isbn</strong> blank generates a valid EAN-13 automatically.</li>
                                    <li><strong>room_name</strong> must exactly match what you have in Library Master —
                                        spelling and case.</li>
                                    <li>Status must be one of: <code>available</code>, <code>damaged</code>,
                                        <code>lost</code>, <code>under_repair</code>.</li>
                                    <li>Max <strong>100 copies</strong> per row. For more, add another row with the same
                                        title.</li>
                                    <li>Rows with missing required fields are <strong>skipped</strong> — a warning is shown
                                        after import.</li>
                                </ul>
                            </div>

                            <div class="imp-card" style="background:#EBF5FF;border-color:#B5D4F4;">
                                <p class="imp-card-title" style="color:#185FA5;">
                                    <i class="feather-zap" style="margin-right:6px;color:#378ADD;"></i>
                                    What happens on import?
                                </p>
                                <ol class="imp-tips-list" style="padding-left:18px;">
                                    <li>Each row is validated for required fields.</li>
                                    <li>Library Master locations are matched by name.</li>
                                    <li>N copy records are created in the database.</li>
                                    <li>EAN-13 barcodes are auto-generated for each copy.</li>
                                    <li>A summary shows created count + any warnings.</li>
                                </ol>
                            </div>
                        </div>

                    </div>{{-- end .imp-layout --}}
                </div>
            </div>
        </div>
    </div>

    <style>
        .imp-layout {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 16px;
            align-items: start;
        }

        @media (max-width: 960px) {
            .imp-layout {
                grid-template-columns: 1fr;
            }
        }

        .imp-main {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .imp-sidebar {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .imp-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            position: relative;
        }

        .imp-step-badge {
            display: inline-block;
            background: #378ADD;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 10px;
            border-radius: 20px;
            letter-spacing: .06em;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .imp-card-title {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
            margin: 0 0 6px;
        }

        .imp-card-desc {
            font-size: 13px;
            color: #6B7280;
            margin: 0 0 14px;
            line-height: 1.5;
        }

        /* Download button */
        .imp-btn-download {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #1D9E75;
            color: #fff;
            border-radius: 8px;
            padding: 9px 18px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: background .15s;
        }

        .imp-btn-download:hover {
            background: #158a62;
            color: #fff;
        }

        /* Field table */
        .imp-field-table {
            font-size: 12px;
        }

        .imp-field-head {
            display: grid;
            grid-template-columns: 130px 90px 1fr;
            gap: 8px;
            font-weight: 700;
            color: #6B7280;
            padding: 6px 8px;
            background: #F3F4F6;
            border-radius: 6px 6px 0 0;
            border: 1px solid #e5e7eb;
        }

        .imp-field-row {
            display: grid;
            grid-template-columns: 130px 90px 1fr;
            gap: 8px;
            padding: 5px 8px;
            border-left: 1px solid #e5e7eb;
            border-right: 1px solid #e5e7eb;
            border-bottom: 1px solid #f3f4f6;
            align-items: center;
        }

        .imp-field-row:last-child {
            border-bottom: 1px solid #e5e7eb;
            border-radius: 0 0 6px 6px;
        }

        .imp-field-name {
            font-family: monospace;
            font-size: 12px;
            color: #1E3A5F;
            font-weight: 600;
        }

        .imp-field-note {
            color: #6B7280;
        }

        .imp-badge-req {
            background: #D1FAE5;
            color: #065F46;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px;
        }

        .imp-badge-opt {
            background: #F3F4F6;
            color: #6B7280;
            font-size: 10px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 20px;
        }

        /* Drop zone */
        .imp-drop-zone {
            border: 2px dashed #d1d5db;
            border-radius: 10px;
            padding: 30px 20px;
            text-align: center;
            cursor: pointer;
            transition: border-color .15s, background .15s;
            margin-bottom: 14px;
        }

        .imp-drop-zone:hover,
        .imp-drop-zone.dragover {
            border-color: #378ADD;
            background: #EBF5FF;
        }

        .imp-drop-title {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin: 0;
        }

        .imp-drop-sub {
            font-size: 11px;
            color: #9CA3AF;
            margin: 4px 0 0;
        }

        /* Submit btn */
        .imp-btn-submit {
            width: 100%;
            height: 40px;
            background: #378ADD;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background .15s;
        }

        .imp-btn-submit:hover:not(:disabled) {
            background: #185FA5;
        }

        .imp-btn-submit:disabled {
            background: #9CA3AF;
            cursor: not-allowed;
        }

        /* Tips list */
        .imp-tips-list {
            font-size: 12px;
            color: #374151;
            line-height: 1.7;
            margin: 0;
            padding-left: 16px;
        }

        .imp-tips-list li {
            margin-bottom: 4px;
        }

        .imp-tips-list code {
            background: #F3F4F6;
            padding: 0 4px;
            border-radius: 4px;
            font-size: 11px;
        }

        .imp-hint {
            font-size: 11px;
            color: #9CA3AF;
            margin: 0;
        }

        .imp-error {
            font-size: 11px;
            color: #E24B4A;
        }
    </style>

    <script>
        function impHandleFile(input) {
            if (!input.files || !input.files[0]) return;
            const file = input.files[0];
            document.getElementById('impDropDefault').style.display = 'none';
            document.getElementById('impDropSelected').style.display = 'block';
            document.getElementById('impFileName').textContent = file.name;
            document.getElementById('impSubmitBtn').disabled = false;
        }

        // Drag & drop
        const zone = document.getElementById('impDropZone');
        zone.addEventListener('dragover', e => {
            e.preventDefault();
            zone.classList.add('dragover');
        });
        zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
        zone.addEventListener('drop', e => {
            e.preventDefault();
            zone.classList.remove('dragover');
            const dt = e.dataTransfer;
            if (dt.files.length) {
                const inp = document.getElementById('impFileInput');
                inp.files = dt.files;
                impHandleFile(inp);
            }
        });

        // Loading state on submit
        document.getElementById('importForm').addEventListener('submit', function() {
            const btn = document.getElementById('impSubmitBtn');
            btn.disabled = true;
            btn.innerHTML = `
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
             stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
             style="animation:spin 1s linear infinite">
            <polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-.49-4.69"/>
        </svg>
        Importing…`;
        });
    </script>
    <style>
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }
    </style>
@endsection
