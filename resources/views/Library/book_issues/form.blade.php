@extends('admin_layout')
@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Library.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">

                <div class="content-area-header bg-white sticky-top">
                    <h6 class="card-title mb-0">
                        <i class="feather-book-open me-2"></i>Issue Books
                    </h6>
                </div>

                <div class="content-area-body">
                    @include('elements.flash')

                    <div class="bi-layout">

                        {{-- ══ LEFT: Borrower Panel ══ --}}
                        <div class="bi-left">

                            {{-- Step 1: Select Borrower --}}
                            <div class="bi-card">
                                <div class="bi-card-head">
                                    <div class="bi-step">1</div>
                                    <p class="bi-card-title">Select borrower</p>
                                </div>
                                <div class="bi-card-body">

                                    {{-- Student by Card --}}
                                    <label class="bi-label">Student card number</label>
                                    <div class="bi-input-group">
                                        <input type="text" id="cardNumber" class="bi-input" placeholder="e.g. SC-00123">
                                        <button type="button" class="bi-search-btn" onclick="searchStudent()">
                                            Search
                                        </button>
                                    </div>
                                    <div id="studentInfo" style="display:none; margin-top:10px;"></div>

                                    <div class="bi-divider">or</div>

                                    {{-- Staff --}}
                                    <label class="bi-label">Staff member</label>
                                    <select id="staffSelect" class="bi-select">
                                        <option value="">Choose staff…</option>
                                        @foreach ($staffs ?? [] as $staff)
                                            <option value="{{ $staff->id }}">
                                                {{ $staff->first_name }} {{ $staff->last_name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    {{-- Hidden fields --}}
                                    <input type="hidden" name="student_id" id="studentId">
                                    <input type="hidden" name="staff_id"   id="staffId">
                                </div>
                            </div>

                            {{-- Borrower summary card (shown after selection) --}}
                            <div class="bi-card" id="borrowerCard" style="display:none;">
                                <div class="bi-card-body" style="padding:14px 16px;">
                                    <p class="bi-label" style="margin-bottom:8px;">Borrower</p>
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        <div id="borrowerAvatar" class="bi-avatar"></div>
                                        <div>
                                            <p id="borrowerName" class="bi-borrower-name"></p>
                                            <p id="borrowerMeta" class="bi-borrower-meta"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>{{-- end .bi-left --}}

                        {{-- ══ RIGHT: Book Panel ══ --}}
                        <div class="bi-card bi-right">

                            <div class="bi-card-head">
                                <div class="bi-step">2</div>
                                <p class="bi-card-title">Add books</p>
                                <span id="booksCountBadge" class="bi-count-badge" style="display:none; margin-left:auto;">
                                    0 added
                                </span>
                            </div>

                            {{-- Search + Scan --}}
                            <div class="bi-search-area">
                                <div class="bi-input-group">
                                    <input type="text" id="bookSearch" class="bi-input"
                                        placeholder="Search by title, ISBN, author…" oninput="searchBooks()">
                                    {{-- Barcode scan button --}}
                                    <button type="button" class="bi-scan-btn" onclick="openScanner()" title="Scan barcode">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M3 5h2M3 5v2M3 5H1M21 5h-2M21 5v2M21 5h2M3 19h2M3 19v-2M3 19H1M21 19h-2M21 19v-2M21 19h2"/>
                                            <rect x="7" y="7" width="3" height="10" rx="1"/>
                                            <rect x="14" y="7" width="3" height="10" rx="1"/>
                                        </svg>
                                        Scan
                                    </button>
                                </div>
                            </div>

                            {{-- Results list --}}
                            <div id="bookResults" class="bi-results">
                                <div class="bi-empty">
                                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="1" stroke-linecap="round"
                                        stroke-linejoin="round" style="margin:0 auto 8px; display:block; opacity:.4;">
                                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                                    </svg>
                                    Search for books or scan a barcode
                                </div>
                            </div>

                            {{-- Selected Tray --}}
                            <div class="bi-tray">
                                <div class="bi-tray-head">
                                    <span class="bi-tray-title">
                                        Selected
                                        <span class="bi-count-badge" id="trayCount">0</span>
                                    </span>
                                    <button type="button" class="bi-clear-btn" onclick="clearBooks()">
                                        Clear all
                                    </button>
                                </div>
                                <div id="trayItems" class="bi-tray-empty">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10" />
                                        <line x1="12" y1="8" x2="12" y2="12" />
                                        <line x1="12" y1="16" x2="12.01" y2="16" />
                                    </svg>
                                    No books selected yet
                                </div>
                            </div>

                        </div>{{-- end .bi-right --}}

                    </div>{{-- end .bi-layout --}}

                    {{-- ─── Footer: Issue Date + Due Date + Issue Button ─── --}}
                    <div class="bi-footer">

                        {{-- Issue Date --}}
                        <div class="bi-date-wrap">
                            <label class="bi-duration-label" for="issueDatePicker">
                                <i class="feather-calendar" style="font-size:11px; margin-right:3px;"></i>
                                Issue Date
                            </label>
                            <input type="date" id="issueDatePicker" class="bi-date-input"
                                   title="Issue date — today or earlier">
                        </div>

                        <div class="bi-date-sep">→</div>

                        {{-- Due Date --}}
                        <div class="bi-date-wrap">
                            <label class="bi-duration-label" for="dueDatePicker">
                                <i class="feather-clock" style="font-size:11px; margin-right:3px;"></i>
                                Due Date
                            </label>
                            <input type="date" id="dueDatePicker" class="bi-date-input"
                                   title="Due date — must be in the future">
                        </div>

                        <div class="bi-preview" id="previewInfo" style="display:none;">
                            Issuing <strong id="previewBooks">0</strong> book(s) to
                            <strong id="previewUser">—</strong>
                        </div>

                        <button type="button" class="bi-issue-btn" id="issueBtn" onclick="submitIssue()">
                            Issue books
                        </button>
                    </div>

                </div>{{-- end .content-area-body --}}
            </div>{{-- end .content-area --}}
        </div>
    </div>

    {{-- Hidden submission form --}}
    <form id="issueForm" method="POST" action="{{ route('admin.book_issues.store') }}">
        @csrf
        <input type="hidden" name="student_id"  id="fStudentId">
        <input type="hidden" name="staff_id"    id="fStaffId">
        <input type="hidden" name="books"        id="fBooksData">
        <input type="hidden" name="issue_date"   id="fIssueDate">
        <input type="hidden" name="due_date"     id="fDueDate">
    </form>

    {{-- ══════════════════════ BARCODE SCANNER MODAL ══════════════════════ --}}
    <div id="scannerModal" class="scanner-modal-overlay" style="display:none;">
        <div class="scanner-modal">

            {{-- Modal Header --}}
            <div class="scanner-modal-head">
                <div style="display:flex; align-items:center; gap:10px;">
                    <div class="scanner-icon-wrap">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 5h2M3 5v2M3 5H1M21 5h-2M21 5v2M21 5h2M3 19h2M3 19v-2M3 19H1M21 19h-2M21 19v-2M21 19h2"/>
                            <rect x="7" y="7" width="3" height="10" rx="1"/>
                            <rect x="14" y="7" width="3" height="10" rx="1"/>
                        </svg>
                    </div>
                    <div>
                        <p class="scanner-modal-title">Scan Book Barcode</p>
                        <p class="scanner-modal-sub">Point camera at the book's barcode or enter ISBN manually</p>
                    </div>
                </div>
                <button type="button" class="scanner-close-btn" onclick="closeScanner()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="scanner-modal-body">

                {{-- Camera Feed --}}
                <div class="scanner-viewport" id="scannerViewport">
                    <video id="scannerVideo" autoplay muted playsinline
                           style="width:100%; height:100%; object-fit:cover; display:block;"></video>

                    {{-- Scanning overlay --}}
                    <div class="scanner-overlay-ui">
                        <div class="scanner-frame">
                            <div class="scanner-corner tl"></div>
                            <div class="scanner-corner tr"></div>
                            <div class="scanner-corner bl"></div>
                            <div class="scanner-corner br"></div>
                            <div class="scanner-line" id="scanLine"></div>
                        </div>
                    </div>

                    {{-- Camera error state --}}
                    <div id="cameraError" class="scanner-cam-error" style="display:none;">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                            <line x1="1" y1="1" x2="23" y2="23"/>
                        </svg>
                        <p id="cameraErrorMsg">Camera not available</p>
                    </div>
                </div>

                {{-- Status bar --}}
                <div class="scanner-status" id="scannerStatus">
                    <div class="scanner-status-dot scanning" id="scanStatusDot"></div>
                    <span id="scanStatusText">Scanning…</span>
                </div>

                {{-- Manual Entry --}}
                <div class="scanner-manual">
                    <p class="scanner-manual-label">Or enter ISBN / copy barcode manually</p>
                    <div class="bi-input-group">
                        <input type="text" id="manualBarcode" class="bi-input"
                               placeholder="e.g. 9780262033848 or 9780262033848-001"
                               style="flex:1;">
                        <button type="button" class="bi-search-btn" onclick="searchByBarcode()">
                            Search
                        </button>
                    </div>
                </div>

                {{-- Scan Result --}}
                <div id="scanResult" style="display:none;">
                    <div class="scanner-result-card" id="scanResultCard"></div>
                </div>

            </div>
        </div>
    </div>

    {{-- ZXing barcode library --}}
    <script src="https://unpkg.com/@zxing/library@latest/umd/index.min.js"></script>

    {{-- ══════════════════════════════ STYLES ══════════════════════════════ --}}
    <style>
        /* ── Layout ── */
        .bi-layout {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 16px;
            align-items: start;
        }
        @media (max-width: 960px) { .bi-layout { grid-template-columns: 1fr; } }

        .bi-left { display: flex; flex-direction: column; gap: 12px; }

        /* ── Card ── */
        .bi-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
        }
        .bi-card-head {
            padding: 14px 18px;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .bi-step {
            width: 22px; height: 22px; border-radius: 50%;
            background: #EBF5FF; border: 1px solid #B5D4F4;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 600; color: #185FA5; flex-shrink: 0;
        }
        .bi-card-title { font-size: 13px; font-weight: 500; color: #111827; margin: 0; }
        .bi-card-body  { padding: 16px 18px; }

        /* ── Form elements ── */
        .bi-label {
            font-size: 11px; font-weight: 500; text-transform: uppercase;
            letter-spacing: .06em; color: #9ca3af; display: block; margin-bottom: 6px;
        }
        .bi-input-group { display: flex; gap: 6px; }
        .bi-input {
            flex: 1; height: 36px; padding: 0 10px;
            border: 1px solid #e5e7eb; border-radius: 8px;
            font-size: 13px; color: #111827; background: #fff;
            outline: none; transition: border-color .15s, box-shadow .15s;
            width: 100%; box-sizing: border-box;
        }
        .bi-input:hover { border-color: #d1d5db; }
        .bi-input:focus { border-color: #378ADD; box-shadow: 0 0 0 3px rgba(55,138,221,.1); }

        .bi-search-btn {
            height: 36px; padding: 0 14px; background: #378ADD; color: #fff;
            border: none; border-radius: 8px; font-size: 13px; font-weight: 500;
            cursor: pointer; white-space: nowrap; transition: background .15s; flex-shrink: 0;
        }
        .bi-search-btn:hover { background: #185FA5; }

        /* ── Scan button ── */
        .bi-scan-btn {
            height: 36px; padding: 0 12px;
            background: #1D9E75; color: #fff;
            border: none; border-radius: 8px;
            font-size: 12px; font-weight: 500;
            cursor: pointer; white-space: nowrap;
            transition: background .15s; flex-shrink: 0;
            display: flex; align-items: center; gap: 6px;
        }
        .bi-scan-btn:hover { background: #17805f; }

        .bi-select {
            height: 36px; padding: 0 10px;
            border: 1px solid #e5e7eb; border-radius: 8px;
            font-size: 13px; color: #111827; background: #fff;
            outline: none; width: 100%; box-sizing: border-box;
        }
        .bi-select:focus { border-color: #378ADD; box-shadow: 0 0 0 3px rgba(55,138,221,.1); }

        .bi-divider {
            display: flex; align-items: center; gap: 10px;
            margin: 14px 0; font-size: 11px; color: #9ca3af;
        }
        .bi-divider::before, .bi-divider::after {
            content: ''; flex: 1; height: 1px; background: #f3f4f6;
        }

        /* ── Borrower ── */
        .bi-user-found { background: #F0FDF4; border: 1px solid #86EFAC; border-radius: 8px; padding: 10px 12px; }
        .bi-user-name  { font-size: 13px; font-weight: 500; color: #166534; margin: 0; }
        .bi-user-meta  { font-size: 11px; color: #4ade80; margin: 4px 0 0; display: flex; align-items: center; gap: 6px; }
        .bi-valid-badge { background: #22c55e; color: #fff; font-size: 10px; padding: 1px 6px; border-radius: 99px; font-weight: 500; }
        .bi-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: #EBF5FF; border: 1px solid #B5D4F4;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 600; color: #185FA5; flex-shrink: 0;
        }
        .bi-borrower-name { font-size: 13px; font-weight: 500; color: #111827; margin: 0; }
        .bi-borrower-meta { font-size: 11px; color: #6b7280; margin: 2px 0 0; }

        /* ── Book search area ── */
        .bi-search-area { padding: 14px 18px; border-bottom: 1px solid #f3f4f6; }

        .bi-results {
            display: flex; flex-direction: column; padding: 0;
            max-height: 360px; overflow-y: auto;
        }
        .bi-results::-webkit-scrollbar { width: 4px; }
        .bi-results::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 4px; }

        /* ── List row ── */
        .bi-list-row {
            display: flex; align-items: center; gap: 14px;
            padding: 10px 18px; border-bottom: 1px solid #f3f4f6;
            transition: background .12s;
        }
        .bi-list-row:last-child { border-bottom: none; }
        .bi-list-row:hover      { background: #f9fafb; }
        .bi-list-row.selected   { background: #F0FDF4; }

        .bi-list-thumb {
            width: 44px; height: 58px; border-radius: 5px;
            background: #f3f4f6; border: 1px solid #e5e7eb;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; overflow: hidden;
        }
        .bi-list-main  { flex: 1; min-width: 0; }
        .bi-list-title { font-size: 13px; font-weight: 500; color: #111827; margin: 0 0 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .bi-list-meta  { font-size: 11px; color: #6b7280; margin: 0 0 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .bi-list-pills { display: flex; flex-wrap: wrap; gap: 5px; align-items: center; }
        .bi-list-pill  { font-size: 11px; padding: 2px 7px; border-radius: 99px; border: 1px solid #e5e7eb; color: #6b7280; background: #f9fafb; }
        .bi-list-right { display: flex; flex-direction: column; align-items: flex-end; gap: 5px; flex-shrink: 0; }
        .bi-copy-label { font-size: 11px; color: #9ca3af; font-family: monospace; }

        .bi-add-btn {
            width: 90px; height: 30px; border: none; border-radius: 6px;
            font-size: 12px; font-weight: 500; cursor: pointer;
            transition: all .15s; display: flex; align-items: center; justify-content: center; gap: 4px;
        }
        .bi-add-btn.add        { background: #EBF5FF; color: #185FA5; }
        .bi-add-btn.add:hover  { background: #378ADD; color: #fff; }
        .bi-add-btn.added      { background: #F0FDF4; color: #166534; }

        .bi-empty { padding: 32px 0; text-align: center; color: #9ca3af; font-size: 13px; }

        /* ── Tray ── */
        .bi-tray { border-top: 1px solid #f3f4f6; }
        .bi-tray-head { padding: 12px 18px; display: flex; align-items: center; justify-content: space-between; }
        .bi-tray-title { font-size: 12px; font-weight: 500; color: #111827; display: flex; align-items: center; gap: 8px; }
        .bi-count-badge {
            background: #EBF5FF; color: #185FA5;
            font-size: 11px; font-weight: 600; padding: 2px 8px;
            border-radius: 99px; border: 1px solid #B5D4F4;
            display: inline-flex; align-items: center;
        }
        .bi-clear-btn { font-size: 11px; color: #E24B4A; background: none; border: none; cursor: pointer; padding: 0; }
        .bi-clear-btn:hover { text-decoration: underline; }
        .bi-tray-items { display: flex; gap: 8px; padding: 0 18px 14px; flex-wrap: wrap; }
        .bi-tray-empty { padding: 12px 18px 14px; font-size: 12px; color: #9ca3af; display: flex; align-items: center; gap: 6px; }
        .bi-tray-chip { display: flex; align-items: center; gap: 6px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 99px; padding: 5px 10px 5px 8px; font-size: 12px; color: #111827; }
        .bi-chip-thumb { width: 20px; height: 20px; border-radius: 50%; background: #EBF5FF; display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 600; color: #185FA5; flex-shrink: 0; }
        .bi-chip-label { max-width: 120px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
        .bi-chip-remove { width: 16px; height: 16px; border-radius: 50%; background: #e5e7eb; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #6b7280; font-size: 12px; padding: 0; flex-shrink: 0; line-height: 1; }
        .bi-chip-remove:hover { background: #E24B4A; color: #fff; }

        /* ── Footer ── */
        .bi-footer {
            display: flex; align-items: flex-end; gap: 20px;
            background: #fff; border: 1px solid #e5e7eb; border-radius: 12px;
            padding: 14px 20px; margin-top: 14px; flex-wrap: wrap;
        }
        .bi-date-wrap { display: flex; flex-direction: column; gap: 4px; }
        .bi-duration-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; color: #6b7280; display: block; }
        .bi-date-input {
            height: 38px; padding: 0 10px; border: 1px solid #e5e7eb; border-radius: 8px;
            font-size: 13px; color: #111827; background: #fff; outline: none;
            cursor: pointer; min-width: 155px; transition: border-color .15s, box-shadow .15s;
        }
        .bi-date-input:hover { border-color: #d1d5db; }
        .bi-date-input:focus { border-color: #378ADD; box-shadow: 0 0 0 3px rgba(55,138,221,.1); }
        .bi-date-sep { font-size: 18px; color: #d1d5db; padding-bottom: 20px; flex-shrink: 0; }
        .bi-preview { flex: 1; font-size: 12px; color: #6b7280; min-width: 160px; padding-bottom: 4px; }
        .bi-preview strong { color: #111827; }
        .bi-issue-btn {
            height: 38px; padding: 0 24px; background: #22c55e; color: #fff;
            border: none; border-radius: 8px; font-size: 13px; font-weight: 600;
            cursor: pointer; transition: all .15s; white-space: nowrap;
            opacity: .4; pointer-events: none; margin-left: auto; align-self: flex-end;
        }
        .bi-issue-btn.ready            { opacity: 1; pointer-events: all; }
        .bi-issue-btn.ready:hover      { background: #16a34a; }

        /* ════════════════════ LOCATION ICON + HOVER TOOLTIP CARD ════════════════════ */

        /* The trigger icon button */
        .bi-loc-trigger {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 26px;
            border-radius: 7px;
            background: #F0FDF4;
            border: 1px solid #86EFAC;
            color: #166534;
            cursor: default;
            flex-shrink: 0;
            transition: background .15s, border-color .15s, box-shadow .15s;
            /* IMPORTANT: overflow visible so tooltip shows outside */
            overflow: visible;
        }
        .bi-loc-trigger:hover {
            background: #DCFCE7;
            border-color: #4ade80;
            box-shadow: 0 0 0 3px rgba(34,197,94,.1);
        }

        /* The tooltip card — hidden by default */
        .bi-loc-tooltip {
            visibility: hidden;
            opacity: 0;
            position: absolute;
            bottom: calc(100% + 10px);
            left: 50%;
            transform: translateX(-50%) translateY(4px);
            z-index: 99999;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 12px 32px rgba(0,0,0,.14), 0 2px 8px rgba(0,0,0,.06);
            padding: 0;
            min-width: 220px;
            max-width: 280px;
            pointer-events: none;
            white-space: nowrap;
            transition: opacity .18s ease, transform .18s ease, visibility .18s;
        }
        .bi-loc-trigger:hover .bi-loc-tooltip {
            visibility: visible;
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        /* Arrow pointer */
        .bi-loc-tooltip::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 7px solid transparent;
            border-top-color: #ffffff;
        }
        .bi-loc-tooltip::before {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 8px solid transparent;
            border-top-color: #e5e7eb;
            margin-top: 1px;
        }

        /* Tooltip header */
        .bi-loc-tooltip-header {
            padding: 10px 14px 8px;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            align-items: center;
            gap: 7px;
        }
        .bi-loc-tooltip-header-icon {
            width: 24px; height: 24px;
            border-radius: 6px;
            background: #EBF5FF;
            border: 1px solid #B5D4F4;
            display: flex; align-items: center; justify-content: center;
            color: #185FA5;
            flex-shrink: 0;
        }
        .bi-loc-tooltip-title {
            font-size: 11px;
            font-weight: 600;
            color: #111827;
            margin: 0;
            letter-spacing: .01em;
        }
        .bi-loc-tooltip-sub {
            font-size: 10px;
            color: #9ca3af;
            margin: 0;
        }

        /* Tooltip body */
        .bi-loc-tooltip-body {
            padding: 10px 14px;
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        /* Each location row */
        .bi-loc-row {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 5px 0;
        }
        .bi-loc-row + .bi-loc-row {
            border-top: 1px dashed #f3f4f6;
        }
        .bi-loc-icon-wrap {
            width: 22px; height: 22px;
            border-radius: 5px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .bi-loc-label {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #9ca3af;
            min-width: 36px;
            flex-shrink: 0;
        }
        .bi-loc-val {
            font-size: 12px;
            font-weight: 500;
            color: #111827;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        /* Muted text for placeholder */
        .text-muted {
            color: #9ca3af !important;
        }

        /* Divider between location + group sections */
        .bi-loc-section-divider {
            height: 1px;
            background: #f3f4f6;
            margin: 6px 0;
        }

        /* Section label */
        .bi-loc-section-label {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #b0b9c6;
            padding: 2px 0 4px;
        }

        /* No data state */
        .bi-loc-empty-row {
            font-size: 11px;
            color: #9ca3af;
            padding: 4px 0;
            font-style: italic;
        }

        /* ════════════════════ SCANNER MODAL ════════════════════ */
        .scanner-modal-overlay {
            position: fixed; inset: 0; z-index: 9999;
            background: rgba(0,0,0,.55);
            display: flex; align-items: center; justify-content: center;
            padding: 16px;
            backdrop-filter: blur(4px);
            animation: fadeIn .18s ease;
        }
        @keyframes fadeIn { from { opacity:0; } to { opacity:1; } }

        .scanner-modal {
            background: #fff;
            border-radius: 16px;
            width: 100%;
            max-width: 520px;
            max-height: 92vh;
            overflow-y: auto;
            box-shadow: 0 24px 60px rgba(0,0,0,.22);
            animation: slideUp .2s ease;
        }
        @keyframes slideUp { from { transform:translateY(20px); opacity:0; } to { transform:translateY(0); opacity:1; } }

        /* Modal Header */
        .scanner-modal-head {
            padding: 18px 20px 14px;
            border-bottom: 1px solid #f3f4f6;
            display: flex; align-items: flex-start; justify-content: space-between; gap: 12px;
        }
        .scanner-icon-wrap {
            width: 34px; height: 34px; border-radius: 10px;
            background: #EBF5FF; border: 1px solid #B5D4F4;
            display: flex; align-items: center; justify-content: center;
            color: #185FA5; flex-shrink: 0;
        }
        .scanner-modal-title { font-size: 14px; font-weight: 600; color: #111827; margin: 0; }
        .scanner-modal-sub   { font-size: 11px; color: #9ca3af; margin: 2px 0 0; }
        .scanner-close-btn {
            width: 30px; height: 30px; border-radius: 8px;
            background: #f9fafb; border: 1px solid #e5e7eb;
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            color: #6b7280; flex-shrink: 0; transition: all .12s;
        }
        .scanner-close-btn:hover { background: #fee2e2; border-color: #fca5a5; color: #E24B4A; }

        /* Modal Body */
        .scanner-modal-body { padding: 18px 20px; display: flex; flex-direction: column; gap: 14px; }

        /* Camera viewport */
        .scanner-viewport {
            width: 100%; height: 240px;
            background: #0a0a0a;
            border-radius: 12px;
            overflow: hidden;
            position: relative;
        }

        /* Scan overlay UI */
        .scanner-overlay-ui {
            position: absolute; inset: 0;
            display: flex; align-items: center; justify-content: center;
            pointer-events: none;
        }
        .scanner-frame {
            width: 200px; height: 120px;
            position: relative;
        }
        .scanner-corner {
            position: absolute; width: 20px; height: 20px;
            border-color: #22c55e; border-style: solid;
        }
        .scanner-corner.tl { top:0; left:0;  border-width: 2px 0 0 2px; border-radius: 4px 0 0 0; }
        .scanner-corner.tr { top:0; right:0; border-width: 2px 2px 0 0; border-radius: 0 4px 0 0; }
        .scanner-corner.bl { bottom:0; left:0;  border-width: 0 0 2px 2px; border-radius: 0 0 0 4px; }
        .scanner-corner.br { bottom:0; right:0; border-width: 0 2px 2px 0; border-radius: 0 0 4px 0; }

        .scanner-line {
            position: absolute; left: 0; right: 0; top: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #22c55e, transparent);
            animation: scanMove 1.8s ease-in-out infinite;
        }
        @keyframes scanMove {
            0%   { top: 0; }
            50%  { top: calc(100% - 2px); }
            100% { top: 0; }
        }

        /* Camera error */
        .scanner-cam-error {
            position: absolute; inset: 0;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 10px; color: #9ca3af; font-size: 13px;
            background: #111;
        }

        /* Status bar */
        .scanner-status {
            display: flex; align-items: center; gap: 8px;
            padding: 8px 12px;
            background: #f9fafb; border: 1px solid #f3f4f6;
            border-radius: 8px; font-size: 12px; color: #6b7280;
        }
        .scanner-status-dot {
            width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
        }
        .scanner-status-dot.scanning { background: #22c55e; animation: pulse 1.2s ease-in-out infinite; }
        .scanner-status-dot.found    { background: #378ADD; animation: none; }
        .scanner-status-dot.error    { background: #E24B4A; animation: none; }
        @keyframes pulse { 0%,100% { opacity:1; } 50% { opacity:.4; } }

        /* Manual entry */
        .scanner-manual { display: flex; flex-direction: column; gap: 6px; }
        .scanner-manual-label {
            font-size: 11px; font-weight: 500; text-transform: uppercase;
            letter-spacing: .06em; color: #9ca3af;
        }

        /* Scan result card */
        .scanner-result-card {
            border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden;
        }
        .scanner-result-inner {
            display: flex; gap: 12px; padding: 14px;
        }
        .scanner-result-thumb {
            width: 50px; height: 66px; border-radius: 6px;
            background: #f3f4f6; border: 1px solid #e5e7eb;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; overflow: hidden;
        }
        .scanner-result-thumb img { width:100%; height:100%; object-fit:cover; }
        .scanner-result-info { flex: 1; min-width: 0; }
        .scanner-result-title { font-size: 13px; font-weight: 600; color: #111827; margin: 0 0 3px; }
        .scanner-result-meta  { font-size: 11px; color: #6b7280; margin: 0 0 6px; }
        .scanner-result-pills { display: flex; flex-wrap: wrap; gap: 4px; margin-bottom: 10px; align-items: center; }
        .scanner-result-pill  {
            font-size: 10px; padding: 2px 7px; border-radius: 99px;
            border: 1px solid #e5e7eb; color: #6b7280; background: #f9fafb;
        }
        .scanner-result-footer {
            padding: 10px 14px; border-top: 1px solid #f3f4f6;
            display: flex; align-items: center; justify-content: space-between;
        }
        .scanner-add-btn {
            height: 32px; padding: 0 16px;
            background: #22c55e; color: #fff;
            border: none; border-radius: 8px; font-size: 12px; font-weight: 600;
            cursor: pointer; transition: background .15s;
            display: flex; align-items: center; gap: 6px;
        }
        .scanner-add-btn:hover  { background: #16a34a; }
        .scanner-add-btn.added  { background: #EAF3DE; color: #27500A; cursor: default; }
        .scanner-scan-again-btn {
            height: 32px; padding: 0 12px;
            background: transparent; color: #6b7280;
            border: 1px solid #e5e7eb; border-radius: 8px;
            font-size: 12px; cursor: pointer; transition: all .15s;
        }
        .scanner-scan-again-btn:hover { background: #f9fafb; color: #374151; }

        /* Unavailable state */
        .scanner-unavailable {
            padding: 10px 14px; border-top: 1px solid #f3f4f6;
            background: #FEF2F2; font-size: 12px; color: #991B1B;
            display: flex; align-items: center; gap: 6px;
        }
    </style>

    {{-- ═══════════════════════════════════════ SCRIPTS ═══════════════════════════════════════ --}}
    <script>
        let selectedBooks   = [];
        let currentBorrower = null;
        let codeReader      = null;
        let scannerOpen     = false;
        let lastScannedCode = null;

        /* ─── Init ─── */
        document.addEventListener('DOMContentLoaded', function () {
            const today     = new Date().toISOString().split('T')[0];
            const issueInput = document.getElementById('issueDatePicker');
            const dueInput   = document.getElementById('dueDatePicker');

            issueInput.value = today;
            issueInput.max   = today;

            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            dueInput.min = tomorrow.toISOString().split('T')[0];

            issueInput.addEventListener('change', function () {
                const picked = new Date(this.value);
                picked.setDate(picked.getDate() + 1);
                dueInput.min = picked.toISOString().split('T')[0];
                if (dueInput.value && dueInput.value <= this.value) dueInput.value = '';
                updateFooter();
            });
            dueInput.addEventListener('change', updateFooter);

            const sessionBooks = @json(session('selected_books', []));
            if (sessionBooks?.length > 0) { selectedBooks = sessionBooks; renderTray(); updateFooter(); }

            document.getElementById('cardNumber').addEventListener('keypress', e => { if (e.key === 'Enter') searchStudent(); });
            document.getElementById('bookSearch').addEventListener('keypress', e => { if (e.key === 'Enter') searchBooks(); });
            document.getElementById('manualBarcode').addEventListener('keypress', e => { if (e.key === 'Enter') searchByBarcode(); });

            document.getElementById('staffSelect').addEventListener('change', function () {
                if (!this.value) return;
                document.getElementById('staffId').value   = this.value;
                document.getElementById('studentId').value = '';
                document.getElementById('studentInfo').style.display = 'none';
                document.getElementById('cardNumber').value = '';
                const name = this.options[this.selectedIndex].text;
                currentBorrower = { name, meta: 'Staff member' };
                showBorrowerCard();
                updateFooter();
            });

            // Close modal on overlay click
            document.getElementById('scannerModal').addEventListener('click', function(e) {
                if (e.target === this) closeScanner();
            });
        });

        /* ═══════════════════════════════════════════════════════════════
           HELPER: Build Location + Academic Group hover tooltip HTML
           ALWAYS shows Room, Shelf, Row — with "Not assigned" if missing
           ═══════════════════════════════════════════════════════════════ */
        function buildLocationTooltip(book) {
            // Normalize — handles both flat keys (room_name) and nested objects (room.name)
            const roomVal  = (book.room_name)  ? book.room_name
                           : (book.room  && book.room.name)  ? book.room.name  : null;
            const selfVal  = (book.self_name)  ? book.self_name
                           : (book.self  && book.self.name)  ? book.self.name  : null;
            const rowVal   = (book.row_name)   ? book.row_name
                           : (book.row   && book.row.name)   ? book.row.name   : null;
            const groupVal = (book.academic_group_name) ? book.academic_group_name
                           : (book.academic_group && book.academic_group.name) ? book.academic_group.name : null;

            // Always build location rows (Room, Shelf, Row) with placeholders
            let locationRows = '';

            // ── Room ── always show
            locationRows += `
                <div class="bi-loc-row">
                    <div class="bi-loc-icon-wrap" style="background:#E1F5EE;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#085041" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                    </div>
                    <span class="bi-loc-label">Room</span>
                    <span class="bi-loc-val ${roomVal ? '' : 'text-muted'}">${roomVal ? escapeHtml(roomVal) : 'Not assigned'}</span>
                </div>`;

            // ── Shelf (self) ── always show
            locationRows += `
                <div class="bi-loc-row">
                    <div class="bi-loc-icon-wrap" style="background:#EBF5FF;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#185FA5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="8" y1="6" x2="21" y2="6"/>
                            <line x1="8" y1="12" x2="21" y2="12"/>
                            <line x1="8" y1="18" x2="21" y2="18"/>
                            <line x1="3" y1="6" x2="3.01" y2="6"/>
                            <line x1="3" y1="12" x2="3.01" y2="12"/>
                            <line x1="3" y1="18" x2="3.01" y2="18"/>
                        </svg>
                    </div>
                    <span class="bi-loc-label">Shelf</span>
                    <span class="bi-loc-val ${selfVal ? '' : 'text-muted'}">${selfVal ? escapeHtml(selfVal) : 'Not assigned'}</span>
                </div>`;

            // ── Row ── always show
            locationRows += `
                <div class="bi-loc-row">
                    <div class="bi-loc-icon-wrap" style="background:#FFF7ED;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#C2410C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            <line x1="3" y1="9" x2="21" y2="9"/>
                            <line x1="3" y1="15" x2="21" y2="15"/>
                        </svg>
                    </div>
                    <span class="bi-loc-label">Row</span>
                    <span class="bi-loc-val ${rowVal ? '' : 'text-muted'}">${rowVal ? escapeHtml(rowVal) : 'Not assigned'}</span>
                </div>`;

            /* ── Academic group section (only if present) ── */
            let groupSection = '';
            if (groupVal) {
                groupSection = `
                    <div class="bi-loc-section-divider"></div>
                    <div class="bi-loc-section-label">Academic Group</div>
                    <div class="bi-loc-row">
                        <div class="bi-loc-icon-wrap" style="background:#F5F3FF;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#7C3AED" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <span class="bi-loc-label">Group</span>
                        <span class="bi-loc-val">${escapeHtml(groupVal)}</span>
                    </div>`;
            }

            return `
                <span class="bi-loc-trigger">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    <div class="bi-loc-tooltip">
                        <div class="bi-loc-tooltip-header">
                            <div class="bi-loc-tooltip-header-icon">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                    <polyline points="9 22 9 12 15 12 15 22"/>
                                </svg>
                            </div>
                            <div>
                                <p class="bi-loc-tooltip-title">Physical Location</p>
                                <p class="bi-loc-tooltip-sub">Room · Shelf · Row</p>
                            </div>
                        </div>
                        <div class="bi-loc-tooltip-body">
                            ${locationRows}
                            ${groupSection}
                        </div>
                    </div>
                </span>`;
        }

        /* ═══════════════════════════ SCANNER ═══════════════════════════ */

        function openScanner() {
            if (scannerOpen) return;
            scannerOpen = true;
            lastScannedCode = null;

            const modal = document.getElementById('scannerModal');
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';

            // Reset UI
            document.getElementById('scanResult').style.display = 'none';
            document.getElementById('manualBarcode').value = '';
            setStatus('scanning', 'Scanning…');

            startCamera();
        }

        function closeScanner() {
            scannerOpen = false;
            document.getElementById('scannerModal').style.display = 'none';
            document.body.style.overflow = '';
            stopCamera();
        }

        function startCamera() {
            if (typeof ZXing === 'undefined') {
                showCameraError('ZXing library not loaded. Please try manual entry.');
                return;
            }

            codeReader = new ZXing.BrowserMultiFormatReader();

            codeReader.listVideoInputDevices().then(devices => {
                if (!devices || devices.length === 0) {
                    showCameraError('No camera found. Use manual entry below.');
                    return;
                }

                // Prefer back/rear camera
                const backCam = devices.find(d =>
                    /back|rear|environment/i.test(d.label)
                );
                const deviceId = backCam ? backCam.deviceId : devices[0].deviceId;

                codeReader.decodeFromVideoDevice(deviceId, 'scannerVideo', (result, err) => {
                    if (result && scannerOpen) {
                        const code = result.getText();
                        if (code === lastScannedCode) return; // debounce
                        lastScannedCode = code;

                        if (navigator.vibrate) navigator.vibrate(100);
                        setStatus('found', 'Barcode detected: ' + code);
                        fetchBookByBarcode(code);
                    }
                });

            }).catch(err => {
                showCameraError('Camera access denied. Use manual entry below.');
            });
        }

        function stopCamera() {
            if (codeReader) {
                try { codeReader.reset(); } catch(e) {}
                codeReader = null;
            }
        }

        function showCameraError(msg) {
            document.getElementById('cameraError').style.display = 'flex';
            document.getElementById('cameraErrorMsg').textContent = msg;
            setStatus('error', msg);
        }

        function setStatus(type, text) {
            const dot  = document.getElementById('scanStatusDot');
            const span = document.getElementById('scanStatusText');
            dot.className = 'scanner-status-dot ' + type;
            span.textContent = text;
        }

        /* ── Search by manual barcode entry ── */
        function searchByBarcode() {
            const code = document.getElementById('manualBarcode').value.trim();
            if (!code) { alert('Please enter a barcode or ISBN'); return; }
            lastScannedCode = code;
            setStatus('found', 'Searching: ' + code);
            fetchBookByBarcode(code);
        }

        /* ── Fetch book from server by barcode ── */
        function fetchBookByBarcode(barcode) {
            document.getElementById('scanResult').style.display = 'none';

            fetch('{{ route("admin.books.scan-barcode") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ barcode: barcode })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showScanResult(data.book);
                } else {
                    setStatus('error', '⚠ Book not found: ' + barcode);
                    showScanResultError(barcode, data.message || 'Book not found with this barcode.');
                }
            })
            .catch(() => {
                setStatus('error', 'Network error. Try again.');
            });
        }

        /* ── Show scanned book result in modal ── */
        function showScanResult(book) {
            const isAvailable = book.status === 'available';
            const isSelected  = selectedBooks.some(b => b.id == book.id);

            const statusColor = isAvailable
                ? 'background:#F0FDF4;color:#166534;border-color:#86EFAC;'
                : book.status === 'issued'
                    ? 'background:#FFFBEB;color:#854D0E;border-color:#FDE047;'
                    : 'background:#FEF2F2;color:#991B1B;border-color:#FECACA;';

            const statusLabel = {
                available:    'Available',
                issued:       'Issued',
                damaged:      'Damaged',
                lost:         'Lost',
                under_repair: 'Under Repair',
            }[book.status] || book.status;

            const thumb = book.book_image
                ? `<img src="/storage/${escapeHtml(book.book_image)}" alt="${escapeHtml(book.title)}">`
                : `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" style="opacity:.4;"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>`;

            const meta = [
                book.author    ? escapeHtml(book.author)             : null,
                book.publisher ? escapeHtml(book.publisher)           : null,
                book.edition   ? escapeHtml(book.edition) + ' ed'    : null,
            ].filter(Boolean).join(' · ');

            const copyLabel = book.copy_number
                ? `Copy #${book.copy_number}` + (book.copy_isbn ? ` · ${book.copy_isbn}` : '')
                : '';

            /* ── Single location icon replacing room + group pills in scanner ── */
            const locationIcon = buildLocationTooltip(book);

            let footerHtml = '';
            if (!isAvailable && !isSelected) {
                footerHtml = `
                    <div class="scanner-unavailable">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                        This copy is currently <strong>${statusLabel}</strong> — cannot be issued.
                        <button class="scanner-scan-again-btn ms-auto" onclick="resetScanResult()">Scan Again</button>
                    </div>`;
            } else {
                footerHtml = `
                    <div class="scanner-result-footer">
                        <button class="scanner-scan-again-btn" onclick="resetScanResult()">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:4px;"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                            Scan again
                        </button>
                        <button class="scanner-add-btn ${isSelected ? 'added' : ''}"
                                id="scannerAddBtn"
                                onclick="addBookFromScanner(${JSON.stringify(book).replace(/"/g,'&quot;')})">
                            ${isSelected
                                ? `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Already Added`
                                : `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Add to Issue`
                            }
                        </button>
                    </div>`;
            }

            document.getElementById('scanResultCard').innerHTML = `
                <div class="scanner-result-inner">
                    <div class="scanner-result-thumb">${thumb}</div>
                    <div class="scanner-result-info">
                        <p class="scanner-result-title">${escapeHtml(book.title)}</p>
                        ${meta ? `<p class="scanner-result-meta">${meta}</p>` : ''}
                        <div class="scanner-result-pills">
                            <span class="scanner-result-pill" style="${statusColor}">${statusLabel}</span>
                            ${book.category ? `<span class="scanner-result-pill">${escapeHtml(book.category)}</span>` : ''}
                            ${copyLabel     ? `<span class="scanner-result-pill" style="font-family:monospace;">${escapeHtml(copyLabel)}</span>` : ''}
                            ${book.isbn     ? `<span class="scanner-result-pill" style="font-family:monospace;">ISBN: ${escapeHtml(book.isbn)}</span>` : ''}
                            ${locationIcon}
                        </div>
                    </div>
                </div>
                ${footerHtml}`;

            document.getElementById('scanResult').style.display = 'block';
            setStatus('found', `Found: "${book.title}"`);
        }

        function showScanResultError(barcode, message) {
            document.getElementById('scanResultCard').innerHTML = `
                <div style="padding:16px; text-align:center; color:#991B1B;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="display:block;margin:0 auto 8px;opacity:.6;">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <p style="font-size:13px;font-weight:500;margin:0 0 4px;">${escapeHtml(message)}</p>
                    <p style="font-size:11px;color:#9ca3af;margin:0 0 12px;">Barcode: <code>${escapeHtml(barcode)}</code></p>
                    <button class="scanner-scan-again-btn" onclick="resetScanResult()">Try Again</button>
                </div>`;
            document.getElementById('scanResult').style.display = 'block';
        }

        function resetScanResult() {
            lastScannedCode = null;
            document.getElementById('scanResult').style.display = 'none';
            document.getElementById('manualBarcode').value = '';
            setStatus('scanning', 'Scanning…');
        }

        /* ── Add book from scanner to selected list ── */
        function addBookFromScanner(book) {
            if (selectedBooks.some(b => b.id == book.id)) {
                closeScanner();
                return;
            }

            selectedBooks.push(book);
            renderTray();
            updateFooter();

            // Update the add button to "added"
            const addBtn = document.getElementById('scannerAddBtn');
            if (addBtn) {
                addBtn.className = 'scanner-add-btn added';
                addBtn.innerHTML = `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Added!`;
            }

            // Also update the result card in the book search list if visible
            const card = document.getElementById('bc' + book.id);
            if (card) {
                card.classList.add('selected');
                const btn = card.querySelector('.bi-add-btn');
                if (btn) { btn.className = 'bi-add-btn added'; btn.textContent = '✓ Added'; }
            }

            // Auto close scanner after short delay
            setTimeout(() => closeScanner(), 900);
        }

        /* ═══════════════════════ BORROWER SEARCH ═══════════════════════ */

        function searchStudent() {
            const cardNumber = document.getElementById('cardNumber').value.trim();
            if (!cardNumber) { alert('Please enter a card number'); return; }

            const info = document.getElementById('studentInfo');
            info.style.display = 'block';
            info.innerHTML = '<div style="font-size:12px;color:#9ca3af;">Searching…</div>';

            fetch(`{{ route('admin.book_issues.search_student') }}?card_number=${encodeURIComponent(cardNumber)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const s = data.student;
                        info.innerHTML = `
                            <div class="bi-user-found">
                                <p class="bi-user-name">${escapeHtml(s.name)}</p>
                                <p class="bi-user-meta">
                                    <span class="bi-valid-badge">${s.is_valid ? 'Valid' : 'Expired'}</span>
                                    ${escapeHtml(s.card_number)}
                                    ${s.expiry_date ? '· Expires ' + s.expiry_date : ''}
                                </p>
                            </div>`;
                        document.getElementById('studentId').value    = s.id;
                        document.getElementById('staffId').value      = '';
                        document.getElementById('staffSelect').value  = '';
                        currentBorrower = {
                            name: s.name,
                            meta: 'Card: ' + s.card_number + (s.expiry_date ? ' · Expires ' + s.expiry_date : '')
                        };
                        showBorrowerCard();
                        updateFooter();
                    } else {
                        info.innerHTML = `<div style="background:#FEF9C3;border:1px solid #FDE047;border-radius:8px;padding:10px 12px;font-size:12px;color:#854D0E;">${escapeHtml(data.message)}</div>`;
                    }
                })
                .catch(() => {
                    info.innerHTML = '<div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:8px;padding:10px 12px;font-size:12px;color:#991B1B;">Search failed. Please try again.</div>';
                });
        }

        function showBorrowerCard() {
            if (!currentBorrower) return;
            const initials = currentBorrower.name.split(' ').map(w => w[0]).join('').slice(0,2).toUpperCase();
            document.getElementById('borrowerAvatar').textContent = initials;
            document.getElementById('borrowerName').textContent   = currentBorrower.name;
            document.getElementById('borrowerMeta').textContent   = currentBorrower.meta;
            document.getElementById('borrowerCard').style.display = 'block';
        }

        /* ═══════════════════════ BOOK SEARCH ═══════════════════════ */

        function searchBooks() {
            const q    = document.getElementById('bookSearch').value.trim();
            const cont = document.getElementById('bookResults');
            cont.innerHTML = '<div class="bi-empty">Searching…</div>';

            fetch(`{{ route('admin.book_issues.search_books') }}?q=${encodeURIComponent(q)}`)
                .then(res => res.json())
                .then(data => renderBookResults(data))
                .catch(() => {
                    cont.innerHTML = '<div class="bi-empty" style="color:#E24B4A;">Search failed. Try again.</div>';
                });
        }

        function renderBookResults(books) {
            const cont = document.getElementById('bookResults');
            if (!books || books.length === 0) {
                cont.innerHTML = '<div class="bi-empty">No available books found</div>';
                return;
            }

            cont.innerHTML = books.map(book => {
                const isSelected  = selectedBooks.some(b => b.id == book.id);
                const isAvailable = book.status === 'available';

                const thumb = book.book_image
                    ? `<img src="${escapeHtml(book.book_image)}" alt="${escapeHtml(book.title)}" style="width:100%;height:100%;object-fit:cover;">`
                    : `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" style="opacity:.4;"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>`;

                const statusColor = isAvailable
                    ? 'background:#F0FDF4;color:#166534;border-color:#86EFAC;'
                    : book.status === 'issued'
                        ? 'background:#FFFBEB;color:#854D0E;border-color:#FDE047;'
                        : 'background:#FEF2F2;color:#991B1B;border-color:#FECACA;';

                const statusLabel = book.status === 'available' ? 'Available'
                    : book.status === 'issued' ? 'Issued'
                    : (book.status ?? 'N/A').replace('_', ' ');

                const meta = [
                    book.author    ? escapeHtml(book.author)          : null,
                    book.publisher ? escapeHtml(book.publisher)        : null,
                    book.edition   ? escapeHtml(book.edition) + ' ed' : null,
                    book.year      ? escapeHtml(String(book.year))     : null,
                ].filter(Boolean).join(' · ');

                const pills = [
                    book.category ? `<span class="bi-list-pill">${escapeHtml(book.category)}</span>` : '',
                    book.page     ? `<span class="bi-list-pill">${escapeHtml(String(book.page))} pages</span>` : '',
                ].join('');

                const isbn = book.isbn
                    ? `<span class="bi-list-pill" style="font-family:monospace;background:#E6F1FB;color:#0C447C;border-color:#B5D4F4;">ISBN: ${escapeHtml(book.isbn)}</span>`
                    : '';

                const copyInfo = book.copy_number
                    ? `Copy #${escapeHtml(String(book.copy_number))}` + (book.copy_isbn ? ` · ${escapeHtml(book.copy_isbn)}` : '')
                    : (book.copy_isbn ? escapeHtml(book.copy_isbn) : '');

                /* ── Single location icon with hover tooltip (replaces room + group pills) ── */
                const locationIcon = buildLocationTooltip(book);

                return `
                <div class="bi-list-row ${isSelected ? 'selected' : ''}" id="bc${book.id}">
                    <div class="bi-list-thumb">${thumb}</div>
                    <div class="bi-list-main">
                        <p class="bi-list-title">${escapeHtml(book.title)}</p>
                        ${meta ? `<p class="bi-list-meta">${meta}</p>` : ''}
                        <div class="bi-list-pills">
                            <span class="bi-list-pill" style="${statusColor}">${statusLabel}</span>
                            ${pills}
                            ${isbn}
                            ${locationIcon}
                        </div>
                    </div>
                    <div class="bi-list-right">
                        <button class="bi-add-btn ${isSelected ? 'added' : 'add'}"
                            ${!isAvailable && !isSelected ? 'disabled style="opacity:.4;cursor:default;"' : ''}
                            onclick="toggleBook(${book.id})"
                            data-book='${JSON.stringify(book).replace(/'/g, "&#39;")}'>
                            ${isSelected ? '✓ Added' : isAvailable ? '+ Add' : 'Unavailable'}
                        </button>
                        ${copyInfo ? `<span class="bi-copy-label">${copyInfo}</span>` : ''}
                    </div>
                </div>`;
            }).join('');
        }

        /* ═══════════════════════ BOOK TOGGLE ═══════════════════════ */

        function toggleBook(id) {
            const btn  = document.querySelector(`#bc${id} .bi-add-btn`);
            const book = JSON.parse(btn.getAttribute('data-book').replace(/&#39;/g, "'"));

            if (selectedBooks.some(b => b.id == id)) {
                selectedBooks = selectedBooks.filter(b => b.id != id);
            } else {
                selectedBooks.push(book);
            }

            const card          = document.getElementById('bc' + id);
            const isNowSelected = selectedBooks.some(b => b.id == id);
            if (card) {
                card.classList.toggle('selected', isNowSelected);
                btn.className   = 'bi-add-btn ' + (isNowSelected ? 'added' : 'add');
                btn.textContent = isNowSelected ? '✓ Added' : '+ Add';
            }

            renderTray();
            updateFooter();
        }

        function removeBook(id) {
            selectedBooks = selectedBooks.filter(b => b.id != id);
            const card = document.getElementById('bc' + id);
            if (card) {
                card.classList.remove('selected');
                const btn = card.querySelector('.bi-add-btn');
                if (btn) { btn.className = 'bi-add-btn add'; btn.textContent = '+ Add'; }
            }
            renderTray();
            updateFooter();
        }

        function clearBooks() {
            if (selectedBooks.length === 0) return;
            if (!confirm('Clear all selected books?')) return;
            selectedBooks = [];
            document.querySelectorAll('.bi-list-row.selected').forEach(card => {
                card.classList.remove('selected');
                const btn = card.querySelector('.bi-add-btn');
                if (btn) { btn.className = 'bi-add-btn add'; btn.textContent = '+ Add'; }
            });
            renderTray();
            updateFooter();
        }

        /* ═══════════════════════ TRAY ═══════════════════════ */

        function renderTray() {
            const cont   = document.getElementById('trayItems');
            const badge  = document.getElementById('trayCount');
            const hBadge = document.getElementById('booksCountBadge');
            const count  = selectedBooks.length;

            badge.textContent  = count;
            hBadge.textContent = count + ' added';
            hBadge.style.display = count > 0 ? 'inline-flex' : 'none';

            if (count === 0) {
                cont.className = 'bi-tray-empty';
                cont.innerHTML = `
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    No books selected yet`;
                return;
            }

            cont.className = 'bi-tray-items';
            cont.innerHTML = selectedBooks.map(b => `
                <div class="bi-tray-chip">
                    <div class="bi-chip-thumb">${escapeHtml(b.title.slice(0,2).toUpperCase())}</div>
                    <span class="bi-chip-label">${escapeHtml(b.title)}</span>
                    <button class="bi-chip-remove" onclick="removeBook(${b.id})">×</button>
                </div>`).join('');
        }

        /* ═══════════════════════ FOOTER ═══════════════════════ */

        function updateFooter() {
            const btn       = document.getElementById('issueBtn');
            const preview   = document.getElementById('previewInfo');
            const issueDate = document.getElementById('issueDatePicker').value;
            const dueDate   = document.getElementById('dueDatePicker').value;
            const ready     = selectedBooks.length > 0 && currentBorrower && issueDate && dueDate;

            btn.className = 'bi-issue-btn' + (ready ? ' ready' : '');

            if (ready) {
                document.getElementById('previewBooks').textContent = selectedBooks.length;
                document.getElementById('previewUser').textContent  = currentBorrower.name;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        }

        /* ═══════════════════════ SUBMIT ═══════════════════════ */

        function submitIssue() {
            if (selectedBooks.length === 0) { alert('Please select at least one book'); return; }
            if (!currentBorrower)           { alert('Please select a borrower first'); return; }

            const issueDate = document.getElementById('issueDatePicker').value;
            const dueDate   = document.getElementById('dueDatePicker').value;

            if (!issueDate)              { alert('Please select an issue date'); return; }
            if (!dueDate)                { alert('Please select a due date'); return; }
            if (dueDate <= issueDate)    { alert('Due date must be after the issue date'); return; }

            document.getElementById('fStudentId').value = document.getElementById('studentId').value;
            document.getElementById('fStaffId').value   = document.getElementById('staffId').value;
            document.getElementById('fIssueDate').value = issueDate;
            document.getElementById('fDueDate').value   = dueDate;
            document.getElementById('fBooksData').value = JSON.stringify(
                selectedBooks.map(b => ({ book_id: parseInt(b.id) }))
            );

            document.getElementById('issueForm').submit();
        }

        /* ═══════════════════════ HELPERS ═══════════════════════ */

        function escapeHtml(text) {
            if (!text) return '';
            return String(text).replace(/[&<>"']/g, m => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
            }[m]));
        }

        // Cleanup camera on page unload
        window.addEventListener('beforeunload', () => { if (codeReader) { try { codeReader.reset(); } catch(e){} } });
    </script>
@endsection