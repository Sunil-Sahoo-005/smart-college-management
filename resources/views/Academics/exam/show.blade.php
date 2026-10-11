@extends('admin_layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Academics.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            {{-- HEADER --}}
            <div class="content-area-header bg-white px-4 py-3 border-bottom shadow-sm"
                 style="position:sticky; top:0; z-index:200;">
                <div class="d-flex align-items-center gap-3 flex-wrap w-100">
                    <div class="flex-shrink-0">
                        <h5 class="fw-bold mb-0 text-dark">Exam Details</h5>
                        <small class="text-muted">Full information about the selected exam</small>
                    </div>
                    <div class="ms-auto d-flex align-items-center gap-2">
                        @if (auth()->user()->hasPermissionTo('Edit Exam list'))
                            <a href="{{ route('admin.exams.edit', $exam->id) }}"
                               class="btn btn-warning btn-sm d-inline-flex align-items-center gap-1">
                                <i class="feather-edit-3" style="font-size:13px;"></i>
                                <span>Edit</span>
                            </a>
                        @endif
                        <a href="{{ route('admin.exams.index') }}"
                           class="btn btn-secondary btn-sm d-inline-flex align-items-center gap-1">
                            <i class="feather-arrow-left" style="font-size:13px;"></i>
                            <span>Back to List</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- CONTENT --}}
            <div class="content-area-body p-4">
                @include('elements.flash')

                {{-- Top Summary Card --}}
                <div class="exam-hero-card mb-4">
                    <div class="d-flex align-items-center gap-4 flex-wrap">
                        <div class="exam-icon-wrap">
                            <i class="feather-book-open"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <span class="exam-type-badge">{{ $exam->examType?->name ?? 'N/A' }}</span>
                                @if($exam->exam_mode)
                                    <span class="exam-mode-badge {{ $exam->exam_mode === 'online' ? 'mode-online' : 'mode-offline' }}">
                                        <i class="feather-{{ $exam->exam_mode === 'online' ? 'wifi' : 'map-pin' }} me-1" style="font-size:10px;"></i>
                                        {{ ucfirst($exam->exam_mode) }}
                                    </span>
                                @endif
                                @if($exam->term)
                                    <span class="term-badge">{{ $exam->term->name }}</span>
                                @endif
                            </div>
                            <h3 class="exam-title mb-1">{{ $exam->name }}</h3>
                            <p class="exam-subtitle mb-0">
                                {{ $exam->class?->name ?? 'N/A' }}
                                &nbsp;|&nbsp; {{ $exam->subject?->name ?? 'N/A' }}
                            </p>
                        </div>
                        <div class="d-flex gap-3 flex-shrink-0 flex-wrap">
                            <div class="stat-pill stat-total">
                                <span class="stat-pill-label">Total Marks</span>
                                <span class="stat-pill-value">{{ $exam->total_marks }}</span>
                            </div>
                            <div class="stat-pill stat-pass">
                                <span class="stat-pill-label">Pass Marks</span>
                                <span class="stat-pill-value">{{ $exam->pass_marks }}</span>
                            </div>
                            <button type="button" id="toggleMarksBtn"
                                    onclick="toggleMarksSection()"
                                    style="background:rgba(255,255,255,0.2); border:1.5px solid rgba(255,255,255,0.4); color:#fff; border-radius:10px; padding:10px 18px; font-size:.85rem; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:6px; backdrop-filter:blur(4px); transition:all .2s;">
                                <i class="feather-plus-circle"></i>
                                <span>Enter Marks</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Details Grid --}}
                <div class="row g-4">

                    {{-- Exam Information --}}
                    <div class="col-lg-6">
                        <div class="detail-card h-100">
                            <div class="detail-card-header">
                                <i class="feather-info me-2"></i> Exam Information
                            </div>
                            <div class="detail-card-body">
                                <div class="detail-row">
                                    <div class="detail-label">
                                        <i class="feather-hash me-2 text-primary"></i>Exam Code
                                    </div>
                                    <div class="detail-value"><span class="badge bg-soft-primary text-primary fw-bold">{{ $exam->exam_code }}</span></div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">
                                        <i class="feather-list me-2 text-primary"></i>Exam Type
                                    </div>
                                    <div class="detail-value">{{ $exam->examType?->name ?? 'N/A' }}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">
                                        <i class="feather-monitor me-2 text-primary"></i>Exam Mode
                                    </div>
                                    <div class="detail-value">
                                        @if($exam->exam_mode)
                                            <span class="badge {{ $exam->exam_mode === 'online' ? 'bg-info' : 'bg-secondary' }} px-3 py-1">
                                                <i class="feather-{{ $exam->exam_mode === 'online' ? 'wifi' : 'map-pin' }} me-1"></i>
                                                {{ ucfirst($exam->exam_mode) }}
                                            </span>
                                        @else
                                            —
                                        @endif
                                    </div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">
                                        <i class="feather-tag me-2 text-primary"></i>Exam Name
                                    </div>
                                    <div class="detail-value fw-semibold">{{ $exam->name }}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">
                                        <i class="feather-calendar me-2 text-primary"></i>Academic Year
                                    </div>
                                    <div class="detail-value">{{ $exam->academicYear?->name ?? 'N/A' }}</div>
                                </div>
                                @if($exam->stream)
                                <div class="detail-row">
                                    <div class="detail-label">
                                        <i class="feather-git-branch me-2 text-primary"></i>Branch / Stream
                                    </div>
                                    <div class="detail-value">{{ $exam->stream->name }}</div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Class & Subject --}}
                    <div class="col-lg-6">
                        <div class="detail-card h-100">
                            <div class="detail-card-header">
                                <i class="feather-users me-2"></i> Class & Subject
                            </div>
                            <div class="detail-card-body">
                                <div class="detail-row">
                                    <div class="detail-label">
                                        <i class="feather-hash me-2 text-success"></i>Class
                                    </div>
                                    <div class="detail-value">{{ $exam->class?->name ?? 'N/A' }}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">
                                        <i class="feather-book me-2 text-success"></i>Subject
                                    </div>
                                    <div class="detail-value">{{ $exam->subject?->name ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Schedule --}}
                    <div class="col-lg-6">
                        <div class="detail-card h-100">
                            <div class="detail-card-header">
                                <i class="feather-clock me-2"></i> Schedule
                            </div>
                            <div class="detail-card-body">
                                <div class="detail-row">
                                    <div class="detail-label">
                                        <i class="feather-calendar me-2 text-warning"></i>Exam Date
                                    </div>
                                    <div class="detail-value">
                                        {{ \Carbon\Carbon::parse($exam->exam_date)->format('d M, Y') }}
                                        <small class="text-muted ms-1">({{ \Carbon\Carbon::parse($exam->exam_date)->format('l') }})</small>
                                    </div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">
                                        <i class="feather-clock me-2 text-warning"></i>Start Time
                                    </div>
                                    <div class="detail-value">
                                        {{ \Carbon\Carbon::createFromFormat('H:i:s', $exam->start_time)->format('h:i A') }}
                                    </div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">
                                        <i class="feather-clock me-2 text-warning"></i>End Time
                                    </div>
                                    <div class="detail-value">
                                        {{ \Carbon\Carbon::createFromFormat('H:i:s', $exam->end_time)->format('h:i A') }}
                                    </div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">
                                        <i class="feather-activity me-2 text-warning"></i>Duration
                                    </div>
                                    <div class="detail-value">
                                        @php
                                            $start = \Carbon\Carbon::createFromFormat('H:i:s', $exam->start_time);
                                            $end   = \Carbon\Carbon::createFromFormat('H:i:s', $exam->end_time);
                                            $diff  = $start->diff($end);
                                            $dur   = '';
                                            if ($diff->h) $dur .= $diff->h . ' hr ';
                                            if ($diff->i) $dur .= $diff->i . ' min';
                                        @endphp
                                        <span class="duration-badge">{{ trim($dur) ?: 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Marks Summary --}}
                    <div class="col-lg-6">
                        <div class="detail-card h-100">
                            <div class="detail-card-header">
                                <i class="feather-award me-2"></i> Marks Summary
                            </div>
                            <div class="detail-card-body">
                                <div class="detail-row">
                                    <div class="detail-label">
                                        <i class="feather-award me-2 text-info"></i>Total Marks
                                    </div>
                                    <div class="detail-value fw-bold fs-5 text-info">{{ $exam->total_marks }}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">
                                        <i class="feather-check-circle me-2 text-success"></i>Pass Marks
                                    </div>
                                    <div class="detail-value fw-bold fs-5 text-success">{{ $exam->pass_marks }}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">
                                        <i class="feather-percent me-2 text-secondary"></i>Pass Percentage
                                    </div>
                                    <div class="detail-value">
                                        @php
                                            $pct = $exam->total_marks > 0
                                                ? round(($exam->pass_marks / $exam->total_marks) * 100, 1)
                                                : 0;
                                        @endphp
                                        <div class="marks-bar-wrap">
                                            <div class="marks-bar">
                                                <div class="marks-bar-fill" style="width: {{ $pct }}%"></div>
                                            </div>
                                            <span class="marks-bar-label">{{ $pct }}%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>{{-- end row --}}

                {{-- ✅ Inline Marks Entry Section --}}
                <div id="marksEntrySection" class="mt-4" style="display:none;">
                    <div class="detail-card">
                        <div class="detail-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <span><i class="feather-edit me-2"></i> Enter Marks — {{ $exam->name }}</span>
                            <div class="d-flex align-items-center gap-2 flex-wrap">

                                {{-- ✅ Admission No Filter (replaces Fill Max All) --}}
                                <div class="input-group input-group-sm" style="width:240px;">
                                    <span class="input-group-text bg-white border-end-0 pe-1">
                                        <i class="feather-search text-muted" style="font-size:13px;"></i>
                                    </span>
                                    <input type="text"
                                           id="admissionFilter"
                                           class="form-control border-start-0 ps-1"
                                           placeholder="Filter by Admission No…"
                                           oninput="applyAdmissionFilter(this.value)"
                                           style="font-size:.8125rem;">
                                    <button class="btn btn-outline-secondary" type="button"
                                            onclick="clearAdmissionFilter()"
                                            title="Clear filter">
                                        <i class="feather-x" style="font-size:12px;"></i>
                                    </button>
                                </div>

                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleMarksSection()">
                                    <i class="feather-x me-1"></i> Close
                                </button>
                            </div>
                        </div>
                        <div class="detail-card-body p-3">

                            {{-- Loading --}}
                            <div id="marksLoadingBox" class="text-center py-5 d-none">
                                <div class="spinner-border text-primary" role="status"></div>
                                <p class="text-muted mt-2 mb-0">Loading students…</p>
                            </div>

                            {{-- Empty State --}}
                            <div id="marksEmptyState" class="text-center py-5 d-none">
                                <i class="feather-users text-muted d-block mb-2" style="font-size:2.5rem;"></i>
                                <h6 class="text-muted">No students found for this exam</h6>
                            </div>

                            {{-- Table --}}
                            <div id="marksTableSection" class="d-none">

                                {{-- Top info bar --}}
                                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <small class="text-muted">
                                            Showing: <strong id="marksStudentCount">0</strong>
                                        </small>
                                        <span class="text-muted">|</span>
                                        <small class="text-muted" id="marksPageInfo"></small>
                                    </div>
                                    <small class="text-muted fst-italic">
                                        <i class="feather-info me-1"></i>
                                        Page change hone par marks auto-save hote hain memory mein
                                    </small>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th style="width:40px;">#</th>
                                                <th style="width:110px;">Adm. No</th>
                                                <th style="width:80px;">Roll No</th>
                                                <th>Student Name</th>
                                                <th style="width:160px;">
                                                    Marks
                                                    <small class="text-muted fw-normal">/ {{ $exam->total_marks }}</small>
                                                </th>
                                                <th style="width:110px;">Grade</th>
                                                <th>Remarks</th>
                                                <th style="width:90px;">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="marksTableBody"></tbody>
                                    </table>
                                </div>

                                {{-- Pagination --}}
                                <div id="marksPaginationWrap" class="d-flex align-items-center justify-content-between mt-3 flex-wrap gap-2">
                                    <small class="text-muted" id="marksPaginationInfo"></small>
                                    <nav>
                                        <ul class="pagination pagination-sm mb-0" id="marksPaginationList"></ul>
                                    </nav>
                                </div>

                                {{-- Footer buttons --}}
                                <div class="d-flex justify-content-end gap-2 mt-3 pt-2 border-top">
                                    <button type="button" class="btn btn-outline-secondary" onclick="toggleMarksSection()">
                                        Cancel
                                    </button>
                                    <button type="button" id="marksSaveBtn" class="btn btn-primary px-4" onclick="saveAllMarks()">
                                        <i class="feather-save me-1"></i> Save Marks
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>{{-- end content-area-body --}}
        </div>
    </div>
</div>

<style>
/* ── Hero Card ─────────────────────────────────────── */
.exam-hero-card {
    background: linear-gradient(135deg, #1e3a5f 0%, #0d6efd 100%);
    border-radius: 14px;
    padding: 1.75rem 2rem;
    color: #fff;
    box-shadow: 0 8px 32px rgba(13,110,253,.25);
}
.exam-icon-wrap {
    width: 64px; height: 64px;
    background: rgba(255,255,255,.18);
    border-radius: 16px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.75rem;
    flex-shrink: 0;
    backdrop-filter: blur(4px);
}
.exam-type-badge {
    background: rgba(255,255,255,.22);
    border: 1px solid rgba(255,255,255,.35);
    border-radius: 20px;
    padding: 2px 12px;
    font-size: .75rem;
    font-weight: 600;
    letter-spacing: .4px;
    color: #fff;
}
.exam-mode-badge { border-radius: 20px; padding: 2px 12px; font-size: .75rem; font-weight: 600; }
.mode-online  { background: rgba(13,202,240,.25); border: 1px solid rgba(13,202,240,.5); color: #a5f3fc; }
.mode-offline { background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.3); color: rgba(255,255,255,.85); }
.term-badge {
    background: rgba(255,193,7,.25);
    border: 1px solid rgba(255,193,7,.5);
    border-radius: 20px;
    padding: 2px 12px;
    font-size: .75rem; font-weight: 600; color: #ffe08a;
}
.exam-title   { font-size: 1.5rem; font-weight: 700; color: #fff; }
.exam-subtitle { font-size: .875rem; color: rgba(255,255,255,.75); }

/* Stat Pills */
.stat-pill {
    display: flex; flex-direction: column; align-items: center;
    background: rgba(255,255,255,.15);
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 12px;
    padding: .75rem 1.25rem;
    min-width: 100px;
    backdrop-filter: blur(4px);
}
.stat-pill-label { font-size: .7rem; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; color: rgba(255,255,255,.7); margin-bottom: 4px; }
.stat-pill-value { font-size: 1.5rem; font-weight: 800; color: #fff; line-height: 1; }
.stat-pass .stat-pill-value { color: #a3f7bf; }

/* ── Detail Cards ──────────────────────────────────── */
.detail-card {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,.05);
    transition: box-shadow .2s;
}
.detail-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.1); }
.detail-card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    padding: .875rem 1.25rem;
    font-size: .8125rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: #495057;
    display: flex;
    align-items: center;
}
.detail-card-body { padding: .5rem 0; }
.detail-row {
    display: flex; align-items: center;
    padding: .75rem 1.25rem;
    border-bottom: 1px solid #f1f3f5;
    gap: 1rem;
}
.detail-row:last-child { border-bottom: none; }
.detail-label {
    min-width: 160px; font-size: .8125rem; color: #6c757d;
    font-weight: 500; display: flex; align-items: center; flex-shrink: 0;
}
.detail-value { font-size: .875rem; color: #212529; }

/* Duration badge */
.duration-badge {
    background: #fff3cd; color: #856404;
    border-radius: 20px; padding: 2px 12px;
    font-size: .8125rem; font-weight: 600;
}

/* Marks progress bar */
.marks-bar-wrap { display: flex; align-items: center; gap: 10px; }
.marks-bar { flex: 1; height: 8px; background: #e9ecef; border-radius: 4px; overflow: hidden; max-width: 140px; }
.marks-bar-fill { height: 100%; background: linear-gradient(90deg, #28a745, #5cb85c); border-radius: 4px; transition: width .6s ease; }
.marks-bar-label { font-size: .8125rem; font-weight: 700; color: #28a745; white-space: nowrap; }

/* Marks Entry Table */
#marksEntrySection .table th {
    font-size: .8125rem; white-space: nowrap;
    padding: .75rem 1rem; background: #f8f9fa;
}
#marksEntrySection .table td {
    padding: .6rem 1rem; vertical-align: middle; font-size: .875rem;
}
.marks-input:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 2px rgba(13,110,253,.15);
}

/* Filter input */
#admissionFilter:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 2px rgba(13,110,253,.12);
    outline: none;
}

/* No results row */
.no-filter-results td {
    color: #6c757d;
    font-style: italic;
    text-align: center;
    padding: 2rem 1rem !important;
}

/* Pagination */
#marksPaginationList .page-link {
    border-radius: 6px !important;
    min-width: 32px;
    text-align: center;
    font-size: .8125rem;
    padding: .3rem .55rem;
    border-color: #dee2e6;
    color: #495057;
}
#marksPaginationList .page-item.active .page-link {
    background: #0d6efd;
    border-color: #0d6efd;
    color: #fff;
}
#marksPaginationList .page-item.disabled .page-link { color: #adb5bd; }
#marksPaginationList { gap: 3px; }

.content-area { overflow-y: auto !important; }
</style>

<script>
const EXAM_ID      = {{ $exam->id }};
const TOTAL_MARKS  = {{ $exam->total_marks }};
const STUDENTS_URL = "{{ route('admin.exam_marks.students_by_exam') }}";
const STORE_URL    = "{{ route('admin.exam_marks.store') }}";
const CSRF_TOKEN   = "{{ csrf_token() }}";

const PAGE_SIZE       = 8;
let gradesData        = [];
let marksLoaded       = false;
let allStudents       = [];           // full list from server (deduplicated)
let filteredStudents  = [];           // filtered view — empty = show all
let existingMarks     = {};           // memory store for all pages
let currentPage       = 1;

/* ── Escape helper ── */
function esc(str) {
    if (str == null) return '';
    return String(str)
        .replace(/&/g,'&amp;').replace(/</g,'&lt;')
        .replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}

/* ── Active source (filtered or full) ── */
function activeSource() {
    return filteredStudents.length > 0 ? filteredStudents : allStudents;
}

/* ── Toggle section ── */
function toggleMarksSection() {
    const section = document.getElementById('marksEntrySection');
    const btn     = document.getElementById('toggleMarksBtn');
    const isOpen  = section.style.display !== 'none';

    if (isOpen) {
        section.style.display = 'none';
        btn.innerHTML = '<i class="feather-plus-circle"></i><span>Enter Marks</span>';
        btn.classList.remove('is-open');
    } else {
        section.style.display = '';
        btn.innerHTML = '<i class="feather-x"></i><span>Close</span>';
        btn.classList.add('is-open');
        setTimeout(() => section.scrollIntoView({ behavior: 'smooth', block: 'start' }), 100);
        if (!marksLoaded) loadStudents();
    }
}

/* ── Load students via AJAX ── */
function loadStudents() {
    document.getElementById('marksLoadingBox').classList.remove('d-none');
    document.getElementById('marksTableSection').classList.add('d-none');
    document.getElementById('marksEmptyState').classList.add('d-none');

    fetch(`${STUDENTS_URL}?exam_id=${EXAM_ID}`, {
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('marksLoadingBox').classList.add('d-none');
        gradesData  = data.grades   ?? [];
        allStudents = data.students ?? [];

        // Existing marks memory mein load karo
        existingMarks = {};
        Object.entries(data.existing ?? {}).forEach(([sid, val]) => {
            existingMarks[sid] = { ...val };
        });

        if (!allStudents.length) {
            document.getElementById('marksEmptyState').classList.remove('d-none');
            return;
        }

        marksLoaded      = true;
        filteredStudents = [];   // reset filter on fresh load
        currentPage      = 1;
        updateStudentCountLabel();
        renderPage();
        document.getElementById('marksTableSection').classList.remove('d-none');
    })
    .catch(() => {
        document.getElementById('marksLoadingBox').classList.add('d-none');
        alert('Failed to load students. Please try again.');
    });
}

/* ── Update the "Showing X" label ── */
function updateStudentCountLabel() {
    const el = document.getElementById('marksStudentCount');
    if (!el) return;
    if (filteredStudents.length > 0) {
        el.textContent = `${filteredStudents.length} filtered (of ${allStudents.length} total)`;
    } else {
        el.textContent = `${allStudents.length} students`;
    }
}

/* ── Save current visible page inputs to memory ── */
function saveCurrentPageToMemory() {
    document.querySelectorAll('#marksTableBody .marks-input').forEach(input => {
        const sid = input.dataset.studentId;
        if (!existingMarks[sid]) existingMarks[sid] = {};
        existingMarks[sid].marks_obtained = input.value;

        const gradeEl   = document.getElementById(`grade_${sid}`);
        const remarksEl = document.getElementById(`remarks_${sid}`);
        if (gradeEl)   existingMarks[sid].grade_letter = gradeEl.value;
        if (remarksEl) existingMarks[sid].remarks      = remarksEl.value;
    });
}

/* ── Render current page ── */
function renderPage() {
    const source     = activeSource();
    const total      = source.length;
    const totalPages = Math.ceil(total / PAGE_SIZE) || 1;
    const start      = (currentPage - 1) * PAGE_SIZE;
    const end        = Math.min(start + PAGE_SIZE, total);
    const pageData   = source.slice(start, end);

    // Page info
    document.getElementById('marksPageInfo').textContent =
        total > 0 ? `Page ${currentPage} of ${totalPages}` : '';

    const tbody = document.getElementById('marksTableBody');

    if (!pageData.length) {
        tbody.innerHTML = `<tr class="no-filter-results">
            <td colspan="8">
                <i class="feather-search me-2"></i>
                No student found matching that admission number.
            </td>
        </tr>`;
        renderPagination(0);
        return;
    }

    tbody.innerHTML = pageData.map((s, i) => {
        const ex      = existingMarks[s.student_id] ?? {};
        const marks   = ex.marks_obtained ?? '';
        const grade   = ex.grade_letter   ?? '';
        const remarks = ex.remarks        ?? '';
        const isSaved = (marks !== '' && marks !== null && marks !== undefined);

        const gradeOptions = gradesData.map(g =>
            `<option value="${esc(g.letter)}" ${grade === g.letter ? 'selected' : ''}>${esc(g.letter)}</option>`
        ).join('');

        return `<tr id="row_${s.student_id}">
            <td class="text-muted">${start + i + 1}</td>
            <td class="text-muted" style="font-size:.8125rem;">${esc(s.admission_number ?? '—')}</td>
            <td class="fw-medium">${esc(s.roll_number ?? '—')}</td>
            <td>${esc(s.first_name)} ${esc(s.last_name)}</td>
            <td>
                <input type="number"
                       class="form-control form-control-sm marks-input"
                       id="marks_${s.student_id}"
                       data-student-id="${s.student_id}"
                       value="${isSaved ? marks : ''}"
                       min="0" max="${TOTAL_MARKS}"
                       step="0.01" placeholder="0"
                       style="width:110px;">
            </td>
            <td>
                <select class="form-select form-select-sm"
                        id="grade_${s.student_id}"
                        style="width:100px;">
                    <option value="">—</option>
                    ${gradeOptions}
                </select>
            </td>
            <td>
                <input type="text"
                       class="form-control form-control-sm"
                       id="remarks_${s.student_id}"
                       value="${esc(remarks)}"
                       placeholder="Optional…"
                       maxlength="500">
            </td>
            <td>
                <small class="${isSaved ? 'text-success' : 'text-muted'}"
                       id="status_${s.student_id}">
                    ${isSaved ? '✓ Saved' : '—'}
                </small>
            </td>
        </tr>`;
    }).join('');

    // Auto-grade on input
    tbody.querySelectorAll('.marks-input').forEach(input => {
        input.addEventListener('input', function () {
            const val = parseFloat(this.value);
            const sid = this.dataset.studentId;
            const sel = document.getElementById(`grade_${sid}`);
            if (!sel || isNaN(val)) return;
            const match = gradesData.find(g => val >= g.min_marks && val <= g.max_marks);
            if (match) sel.value = match.letter;
        });
    });

    renderPagination(totalPages);
    updateStudentCountLabel();
}

/* ── Admission No filter ── */
function applyAdmissionFilter(val) {
    saveCurrentPageToMemory();
    const q = val.trim().toLowerCase();
    if (!q) {
        filteredStudents = [];
    } else {
        filteredStudents = allStudents.filter(s =>
            (s.admission_number ?? '').toLowerCase().includes(q)
        );
    }
    currentPage = 1;
    renderPage();
}

function clearAdmissionFilter() {
    const input = document.getElementById('admissionFilter');
    if (input) input.value = '';
    filteredStudents = [];
    currentPage = 1;
    renderPage();
}

/* ── Render pagination ── */
function renderPagination(totalPages) {
    const list = document.getElementById('marksPaginationList');
    const info = document.getElementById('marksPaginationInfo');
    const wrap = document.getElementById('marksPaginationWrap');

    const source = activeSource();
    const start  = (currentPage - 1) * PAGE_SIZE + 1;
    const end    = Math.min(currentPage * PAGE_SIZE, source.length);

    if (source.length === 0) {
        info.textContent = '';
        wrap.style.display = 'none';
        return;
    }

    info.textContent = `Showing ${start}–${end} of ${source.length} students`;

    if (totalPages <= 1) {
        wrap.style.display = 'none';
        list.innerHTML = '';
        return;
    }
    wrap.style.display = '';

    let html = '';

    // Prev button
    html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
        <button class="page-link" onclick="goToPage(${currentPage - 1})">
            <i class="feather-chevron-left" style="font-size:12px;"></i>
        </button>
    </li>`;

    // Page numbers with ellipsis
    for (let p = 1; p <= totalPages; p++) {
        if (p === 1 || p === totalPages || (p >= currentPage - 1 && p <= currentPage + 1)) {
            html += `<li class="page-item ${p === currentPage ? 'active' : ''}">
                <button class="page-link" onclick="goToPage(${p})">${p}</button>
            </li>`;
        } else if (p === currentPage - 2 || p === currentPage + 2) {
            html += `<li class="page-item disabled">
                <span class="page-link">…</span>
            </li>`;
        }
    }

    // Next button
    html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
        <button class="page-link" onclick="goToPage(${currentPage + 1})">
            <i class="feather-chevron-right" style="font-size:12px;"></i>
        </button>
    </li>`;

    list.innerHTML = html;
}

/* ── Go to page ── */
function goToPage(page) {
    const totalPages = Math.ceil(activeSource().length / PAGE_SIZE) || 1;
    if (page < 1 || page > totalPages) return;

    // Current page memory mein save karo
    saveCurrentPageToMemory();

    currentPage = page;
    renderPage();

    // Table top pe scroll karo
    document.getElementById('marksTableSection').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

/* ── Save all marks ── */
function saveAllMarks() {
    const btn = document.getElementById('marksSaveBtn');

    // Pehle current page memory mein save karo
    saveCurrentPageToMemory();

    // Sab students ke marks collect karo (allStudents — not filtered)
    const marks = [];
    allStudents.forEach(s => {
        const ex = existingMarks[s.student_id] ?? {};
        const mv = ex.marks_obtained;
        if (mv === '' || mv === null || mv === undefined) return;
        marks.push({
            student_id:     s.student_id,
            marks_obtained: mv,
            grade_letter:   ex.grade_letter ?? '',
            remarks:        ex.remarks      ?? '',
            mark_date:      new Date().toISOString().split('T')[0],
        });
    });

    if (!marks.length) {
        alert('Please enter marks for at least one student.');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving…';

    fetch(STORE_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ exam_id: EXAM_ID, marks }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.message) {
            // Status badges update karo visible rows ke liye
            marks.forEach(m => {
                const el = document.getElementById(`status_${m.student_id}`);
                if (el) { el.textContent = '✓ Saved'; el.className = 'text-success'; }
            });

            btn.innerHTML = '<i class="feather-check me-1"></i> Saved!';
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-success');

            setTimeout(() => {
                btn.innerHTML = '<i class="feather-save me-1"></i> Save Marks';
                btn.classList.remove('btn-success');
                btn.classList.add('btn-primary');
                btn.disabled = false;
            }, 2000);
        }
    })
    .catch(() => {
        alert('Failed to save. Please try again.');
        btn.disabled = false;
        btn.innerHTML = '<i class="feather-save me-1"></i> Save Marks';
    });
}
</script>
@endsection