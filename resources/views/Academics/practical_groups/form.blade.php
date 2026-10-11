@extends('admin_layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Academics.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            {{-- ── HEADER ── --}}
            <div class="content-area-header bg-white sticky-top">
                <h5 class="fw-bold mb-0 me-4">
                    <span class="d-block mb-2">
                        {{ empty($practicalGroup) ? 'Create Practical Group' : 'Edit Practical Group' }}
                    </span>
                </h5>
                <div class="page-header-right ms-auto">
                    <a href="{{ route('practical_groups.index') }}" class="btn btn-secondary">
                        Back to List
                    </a>
                </div>
            </div>

            {{-- ── BODY ── --}}
            <div class="content-area-body">
                @include('elements.flash')

                <form method="POST"
                      action="{{ empty($practicalGroup) ? route('practical_groups.store') : route('practical_groups.update', $practicalGroup) }}">
                    @csrf
                    @if (!empty($practicalGroup)) @method('PUT') @endif

                    {{-- Student assignment error --}}
                    @error('student_ids')
                        <div class="alert alert-danger py-2 px-3 mb-3" style="font-size:13px;">
                            <i class="feather-alert-circle me-1"></i> {{ $message }}
                        </div>
                    @enderror

                    {{-- Hidden student inputs injected by JS --}}
                    <div id="selectedStudentInputs"></div>

                    {{-- ══ Card 1 – Basic Info ══ --}}
                    <div class="card stretch stretch-full mb-3">
                        <div class="card-body">
                            <h6 class="card-title">Basic Information</h6>

                            <div class="row">

                                {{-- Course --}}
                                <div class="col-md-2">
                                    <label class="form-label">
                                        Course <span id="stream-loading" style="display:none;">🔄</span>
                                    </label>
                                    <select name="course_id" id="course_id" class="form-select" required>
                                        <option value="">Select Course</option>
                                        @foreach ($courses as $course)
                                            <option value="{{ $course->id }}"
                                                {{ old('course_id', $practicalGroup->course_id ?? '') == $course->id ? 'selected' : '' }}>
                                                {{ $course->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Branch --}}
                                <div class="col-md-2">
                                    <label class="form-label">
                                        Branch <span id="branch-loading" style="display:none;">🔄</span>
                                    </label>
                                    <select name="stream_id" id="stream_id" class="form-select" required disabled>
                                        <option value="">Select Branch</option>
                                        @if (!empty($practicalGroup))
                                            @foreach ($streams as $stream)
                                                <option value="{{ $stream->id }}"
                                                    {{ old('stream_id', $practicalGroup->stream_id ?? '') == $stream->id ? 'selected' : '' }}>
                                                    {{ $stream->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                {{-- Academic Year (Select2 multi) --}}
                                <div class="col-md-2">
                                    <label class="form-label">Academic Year</label>
                                    <select name="academic_year_ids[]" id="academic_year_ids"
                                            class="form-select select-multi" multiple required>
                                        @foreach ($academicYears as $year)
                                            @php
                                                $selYears = !empty($practicalGroup)
                                                    ? $practicalGroup->academicYears->pluck('id')->toArray()
                                                    : [];
                                                $isSelected = in_array($year->id, old('academic_year_ids', $selYears));
                                            @endphp
                                            <option value="{{ $year->id }}" {{ $isSelected ? 'selected' : '' }}>
                                                {{ $year->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Group Master --}}
                                <div class="col-md-2">
                                    <label class="form-label">Group Name</label>
                                    <select name="group_master_id" id="group_master_id" class="form-select" required>
                                        <option value="">Select Group</option>
                                        @foreach ($groupMasters as $gm)
                                            <option value="{{ $gm->id }}"
                                                data-strength="{{ $gm->strength ?? 0 }}" 
                                                {{ old('group_master_id', $practicalGroup->group_master_id ?? '') == $gm->id ? 'selected' : '' }}>
                                                {{ $gm->name }}{{ $gm->strength ? ' (Strength: '.$gm->strength.')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
</div>{{-- /row --}}
                        </div>
                    </div>{{-- /card 1 --}}

                    {{-- ══ Card 2 – Timetable Prototype ══ --}}
                    <div class="card stretch stretch-full mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-3">
                                <div>
                                    <h6 class="card-title mb-1">Timetable Prototype</h6>
                                    <p class="text-muted mb-0" style="font-size:12px;">
                                        This is a draft timetable builder for practical groups.
                                        It keeps the schedule as JSON for now.
                                    </p>
                                </div>
                                <button type="button" id="addTimetableRowBtn" class="btn btn-sm btn-outline-primary">
                                    <i class="feather-plus me-1"></i> Add Slot
                                </button>
                            </div>

                            <input type="hidden" name="timetable_json" id="timetable_json" value="{{ old('timetable_json') }}">

                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:16%;">Day</th>
                                            <th style="width:12%;">Start</th>
                                            <th style="width:12%;">End</th>
                                            <th style="width:16%;">Room / Lab</th>
                                            <th style="width:20%;">Teacher</th>
                                            <th style="width:18%;">Topic / Subject</th>
                                            <th style="width:6%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="timetableRows">
                                        <tr class="timetable-empty-row">
                                            <td colspan="7" class="text-center text-muted py-4">
                                                No timetable slots added yet. Click <strong>Add Slot</strong> to build one.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="alert alert-info py-2 px-3 mt-3 mb-0" style="font-size:13px;">
                                <i class="feather-info me-1"></i>
                                Next step: move this JSON into a dedicated timetable table when you’re ready to persist it.
                            </div>
                        </div>
                    </div>{{-- /card 2 --}}

                    {{-- ══ Card 3 – Assign Students ══ --}}
                    <div class="card stretch stretch-full mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="card-title mb-0">
                                    Assign Students
                                    <span class="ms-1 text-muted" style="font-weight:400; text-transform:none; font-size:12px; letter-spacing:0;">
                                        (Selected: <span id="selectedCount">0</span>
                                        <span id="strengthInfo" class="text-warning ms-1" style="display:none;">
                                            / Max: <span id="groupStrength">0</span>
                                        </span>)
                                    </span>
                                </h6>
                                <button type="button" id="fetchStudentsBtn" class="btn btn-sm btn-primary" disabled>
                                    <i class="feather-refresh-cw me-1"></i> Load / Refresh Students
                                </button>
                            </div>

                            {{-- Group strength warning --}}
                            <div id="strengthWarning" class="alert alert-warning py-2 px-3 mb-3" style="display:none; font-size:13px;">
                                <i class="feather-alert-triangle me-1"></i>
                                <span id="strengthWarningText"></span>
                            </div>

                            <div id="studentPanel" class="pg-student-panel">

                                {{-- Left – available --}}
                                <div class="pg-student-col">
                                    <div class="pg-col-head">
                                        Available Students
                                        <span id="availableCount" class="badge bg-secondary ms-2">0</span>
                                    </div>
                                    <div class="pg-search-wrap">
                                        <i class="feather-search"></i>
                                        <input type="text" id="searchAvailable" placeholder="Search by name or roll…">
                                    </div>
                                    <div id="availableList" class="pg-list">
                                        <div class="pg-empty-state">
                                            <i class="feather-users"></i>
                                            <p>Fill course, branch, year &amp; group above,<br>then click <strong>Load / Refresh Students</strong>.</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Divider --}}
                                <div class="pg-divider">
                                    <div class="pg-divider-line"></div>
                                    <span class="pg-divider-icon"><i class="feather-chevrons-right"></i></span>
                                    <div class="pg-divider-line"></div>
                                </div>

                                {{-- Right – selected group --}}
                                <div class="pg-student-col">
                                    <div class="pg-col-head">
                                        Group Members
                                        <span id="selectedCountBadge" class="badge bg-primary ms-2">0</span>
                                    </div>
                                    <div class="pg-search-wrap">
                                        <i class="feather-search"></i>
                                        <input type="text" id="searchSelected" placeholder="Search…">
                                    </div>
                                    <div id="selectedList" class="pg-list">
                                        <div class="pg-empty-state">
                                            <i class="feather-user-plus"></i>
                                            <p>No students added yet.<br>Click <i class="feather-plus-circle"></i> to add.</p>
                                        </div>
                                    </div>
                                </div>

                            </div>{{-- /pg-student-panel --}}
                        </div>
                    </div>{{-- /card 2 --}}

                    <button type="submit" class="btn btn-primary">
                        {{ empty($practicalGroup) ? 'Save Practical Group' : 'Update Practical Group' }}
                    </button>

                </form>
            </div>{{-- /content-area-body --}}
        </div>{{-- /content-area --}}
    </div>
</div>

{{-- Pre-selected students (edit mode) --}}
@php
    $preSelectedStudents = [];
    if (!empty($practicalGroup)) {
        $preSelectedStudents = $practicalGroup->students->map(fn($s) => [
            'id'          => $s->id,
            'first_name'  => $s->first_name,
            'last_name'   => $s->last_name,
            'roll_number' => $s->roll_number,
        ])->toArray();
    }
@endphp

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
/* ── Select2 match timetable style ── */
.select2-container--default .select2-selection--multiple {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    min-height: 38px;
}
.select2-container--default.select2-container--focus .select2-selection--multiple {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13,110,253,.25);
}
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: transparent;
    border: 1px solid #2563eb;
    color: #2563eb;
    border-radius: 4px;
    padding: 2px 6px;
    margin-top: 6px;
    font-size: 13px;
    font-weight: 500;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: #2563eb;
    margin-right: 5px;
    border-right: none;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
    background-color: #eff6ff;
    color: #1d4ed8;
}

/* ── Card title match timetable ── */
.card-title {
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.025em;
    font-weight: 700;
    color: #475569;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 8px;
}
.card-title::before {
    content: '';
    display: inline-block;
    width: 4px;
    height: 16px;
    background: #2563eb;
    border-radius: 2px;
}

/* ── Student panel ── */
.pg-student-panel {
    display: grid;
    grid-template-columns: 1fr 32px 1fr;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    overflow: hidden;
    min-height: 380px;
}
.pg-student-col { display: flex; flex-direction: column; background: #fff; }
.pg-col-head {
    display: flex; align-items: center;
    padding: 10px 14px;
    font-size: 12px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.03em;
    color: #475569; background: #f8f9fa;
    border-bottom: 1px solid #e5e7eb;
}
.pg-search-wrap {
    display: flex; align-items: center; gap: 8px;
    padding: 7px 12px; border-bottom: 1px solid #f3f4f6; background: #fff;
}
.pg-search-wrap i { font-size: 12px; color: #9ca3af; flex-shrink: 0; }
.pg-search-wrap input { flex:1; border:none; outline:none; font-size:13px; color:#374151; background:transparent; }
.pg-list { flex:1; overflow-y:auto; max-height:320px; padding: 3px 0; }
.pg-student-row {
    display:flex; align-items:center; gap:9px;
    padding:7px 12px; transition:background .1s;
}
.pg-student-row:hover { background:#f8fafc; }
.pg-student-row.selected:hover { background:#eff6ff; }
.pg-avatar {
    width:30px; height:30px; border-radius:50%;
    background:#dbeafe; color:#1d4ed8;
    display:flex; align-items:center; justify-content:center;
    font-size:10px; font-weight:700; flex-shrink:0;
}
.pg-avatar.selected-av { background:#dcfce7; color:#15803d; }
.pg-serial { width:18px; font-size:11px; color:#9ca3af; text-align:right; flex-shrink:0; }
.pg-info { flex:1; min-width:0; }
.pg-name { display:block; font-size:13px; font-weight:500; color:#111827; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.pg-roll { display:block; font-size:11px; color:#9ca3af; }
.pg-action-btn {
    flex-shrink:0; background:none; border:none; cursor:pointer;
    padding:2px; font-size:17px; line-height:1; transition:transform .1s, color .1s;
}
.pg-action-btn.add    { color:#16a34a; } .pg-action-btn.add:hover    { color:#15803d; transform:scale(1.15); }
.pg-action-btn.remove { color:#dc2626; } .pg-action-btn.remove:hover { color:#b91c1c; transform:scale(1.15); }
.pg-action-btn:disabled { color:#d1d5db; cursor:not-allowed; transform:none !important; }
.pg-divider {
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    background:#f8f9fa; border-left:1px solid #e5e7eb; border-right:1px solid #e5e7eb;
    gap:6px; padding:12px 0;
}
.pg-divider-line { flex:1; width:1px; background:#e5e7eb; min-height:20px; }
.pg-divider-icon { font-size:13px; color:#9ca3af; }
.pg-empty-state {
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    gap:8px; padding:36px 20px; text-align:center; color:#9ca3af;
}
.pg-empty-state i { font-size:28px; opacity:.3; }
.pg-empty-state p { font-size:12px; margin:0; line-height:1.6; }
.timetable-row input,
.timetable-row select {
    font-size: 13px;
}
.timetable-row .form-control,
.timetable-row .form-select {
    min-height: 34px;
}
.timetable-remove-btn {
    border: 0;
    background: transparent;
    color: #dc2626;
    font-size: 16px;
}
.timetable-remove-btn:hover {
    color: #b91c1c;
}
@media(max-width:640px){
    .pg-student-panel { grid-template-columns:1fr; grid-template-rows:1fr auto 1fr; }
    .pg-divider { flex-direction:row; height:32px; border-top:1px solid #e5e7eb; border-bottom:1px solid #e5e7eb; border-left:none; border-right:none; }
    .pg-divider-icon { transform:rotate(90deg); }
    .pg-divider-line { flex:1; height:1px; width:auto; min-height:unset; }
}
</style>
@endpush

@push('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
const GET_STREAMS_URL  = "{{ route('practical_groups.getStreams', ':id') }}";
const GET_STUDENTS_URL = "{{ route('practical_groups.getStudents') }}";
const PRE_SELECTED     = @json($preSelectedStudents);
const CURRENT_GROUP_ID = {{ !empty($practicalGroup) ? $practicalGroup->id : 'null' }};
let timetableRows = [];

try {
    const rawTimetable = @json(old('timetable_json'));
    timetableRows = rawTimetable ? JSON.parse(rawTimetable) : [];
} catch (e) {
    timetableRows = [];
}

let allAvailable = [];
let selected     = [];
let groupStrength = 0;

/* ── Select2 init ── */
$(document).ready(function () {
    $('#academic_year_ids').select2({
        placeholder: "Select Academic Year(s)",
        allowClear: true
    });

    // Trigger stream load if course pre-selected (edit mode)
    const courseEl = document.getElementById('course_id');
    if (courseEl.value) {
        loadStreams(courseEl.value, {{ old('stream_id', $practicalGroup->stream_id ?? 0) }});
    }

    // Check if all fields are pre-filled (edit mode) → auto-enable Load button
    checkFetchReady();

    // Academic year change → check ready
    $('#academic_year_ids').on('change', checkFetchReady);
});

/* ── Check if all 4 fields filled → enable Load button ── */
function checkFetchReady() {
    const courseId   = document.getElementById('course_id').value;
    const streamId   = document.getElementById('stream_id').value;
    const yearIds    = $('#academic_year_ids').val();
    const groupId    = document.getElementById('group_master_id').value;
    const ready      = courseId && streamId && yearIds && yearIds.length > 0 && groupId;
    document.getElementById('fetchStudentsBtn').disabled = !ready;
}

/* ── Course → Streams ── */
document.getElementById('course_id').addEventListener('change', function () {
    loadStreams(this.value, null);
    checkFetchReady();
});

document.getElementById('stream_id').addEventListener('change', checkFetchReady);

document.getElementById('group_master_id').addEventListener('change', function () {
    const selectedOpt = this.options[this.selectedIndex];
    groupStrength = parseInt(selectedOpt?.getAttribute('data-strength') || '0', 10);

    if (groupStrength > 0) {
        document.getElementById('groupStrength').textContent = groupStrength;
        document.getElementById('strengthInfo').style.display = 'inline';
    } else {
        document.getElementById('strengthInfo').style.display = 'none';
    }
    checkFetchReady();
    updateStrengthWarning();
});

function loadStreams(courseId, preselectStreamId) {
    const streamEl  = document.getElementById('stream_id');
    const loadingEl = document.getElementById('stream-loading');

    streamEl.innerHTML = '<option value="">Select Branch</option>';
    streamEl.disabled  = true;
    document.getElementById('fetchStudentsBtn').disabled = true;

    if (!courseId) return;

    loadingEl.style.display = 'inline';
    fetch(GET_STREAMS_URL.replace(':id', courseId))
        .then(r => r.json())
        .then(data => {
            data.forEach(s => {
                const opt = new Option(s.name, s.id, false, s.id == preselectStreamId);
                streamEl.add(opt);
            });
            streamEl.disabled = false;
            checkFetchReady();
        })
        .finally(() => loadingEl.style.display = 'none');
}

/* ── Load Students ── */
document.getElementById('fetchStudentsBtn').addEventListener('click', loadStudents);

function loadStudents() {
    const courseId = document.getElementById('course_id').value;
    const streamId = document.getElementById('stream_id').value;
    const yearIds  = $('#academic_year_ids').val();

    if (!courseId || !streamId || !yearIds || yearIds.length === 0) {
        alert('Please select Course, Branch and at least one Academic Year first.');
        return;
    }

    const btn = document.getElementById('fetchStudentsBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Loading…';

    const groupId  = document.getElementById('group_master_id').value;

    const params = new URLSearchParams();
    params.append('course_id', courseId);
    params.append('stream_id', streamId);
    yearIds.forEach(y => params.append('academic_year_ids[]', y));
    if (groupId)         params.append('group_master_id', groupId);
    if (CURRENT_GROUP_ID) params.append('exclude_group_id', CURRENT_GROUP_ID);

    fetch(`${GET_STUDENTS_URL}?${params}`)
        .then(r => r.json())
        .then(data => {
            allAvailable = data;

            // Pre-assigned students (same group mein already saved) → auto right panel
            const preAssigned = data.filter(s => s.pre_assigned);
            const fetchedIds  = data.map(s => s.id);

            // Pehle se selected mein jo fetched list mein nahi hain unhe hatao
            selected = selected.filter(s => fetchedIds.includes(s.id));

            // Pre-assigned jo abhi selected mein nahi hain unhe add karo
            preAssigned.forEach(s => {
                if (!selected.find(x => x.id === s.id)) selected.push(s);
            });

            renderBothLists();
        })
        .catch(() => alert('Failed to load students. Please try again.'))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="feather-refresh-cw me-1"></i> Load / Refresh Students';
        });
}

/* ── Strength warning ── */
function updateStrengthWarning() {
    const warnEl = document.getElementById('strengthWarning');
    const warnText = document.getElementById('strengthWarningText');
    if (groupStrength > 0 && selected.length > groupStrength) {
        warnText.textContent = `Group strength is ${groupStrength} but you have selected ${selected.length} students. Please remove ${selected.length - groupStrength} student(s).`;
        warnEl.style.display = 'block';
    } else if (groupStrength > 0 && selected.length === groupStrength) {
        warnText.textContent = `Group is full (${selected.length}/${groupStrength} students).`;
        warnEl.style.display = 'block';
        warnEl.className = 'alert alert-success py-2 px-3 mb-3';
    } else {
        warnEl.style.display = 'none';
        warnEl.className = 'alert alert-warning py-2 px-3 mb-3';
    }
}

/* ── Render ── */
function renderBothLists() { renderAvailable(); renderSelected(); syncHiddenInputs(); updateStrengthWarning(); }

function renderAvailable() {
    const search      = document.getElementById('searchAvailable').value.trim().toLowerCase();
    const selectedIds = selected.map(s => s.id);
    const list        = document.getElementById('availableList');
    const countEl     = document.getElementById('availableCount');

    const filtered = allAvailable.filter(s => {
        if (selectedIds.includes(s.id)) return false;
        if (!search) return true;
        return `${s.first_name} ${s.last_name} ${s.roll_number ?? ''}`.toLowerCase().includes(search);
    });

    countEl.textContent = filtered.length;

    if (filtered.length === 0) {
        list.innerHTML = `<div class="pg-empty-state"><i class="feather-user-check"></i>
            <p>${allAvailable.length === 0 ? 'No students loaded yet.' : 'All students assigned or no match.'}</p></div>`;
        return;
    }

    const atCapacity = groupStrength > 0 && selected.length >= groupStrength;

    list.innerHTML = filtered.map(s => `
        <div class="pg-student-row" data-id="${s.id}">
            <div class="pg-avatar">${initials(s.first_name, s.last_name)}</div>
            <div class="pg-info">
                <span class="pg-name">${esc(s.first_name)} ${esc(s.last_name)}</span>
                <span class="pg-roll">${esc(s.roll_number ?? '')}</span>
            </div>
            <button type="button" class="pg-action-btn add" onclick="addStudent(${s.id})"
                title="${atCapacity ? 'Group is at capacity' : 'Add to group'}"
                ${atCapacity ? 'disabled' : ''}>
                <i class="feather-plus-circle"></i>
            </button>
        </div>`).join('');
}

function renderSelected() {
    const search  = document.getElementById('searchSelected').value.trim().toLowerCase();
    const list    = document.getElementById('selectedList');

    document.getElementById('selectedCount').textContent      = selected.length;
    document.getElementById('selectedCountBadge').textContent = selected.length;

    if (selected.length === 0) {
        list.innerHTML = `<div class="pg-empty-state"><i class="feather-user-plus"></i>
            <p>No students added yet.<br>Click <i class="feather-plus-circle"></i> to add.</p></div>`;
        return;
    }

    const filtered = selected.filter(s =>
        !search || `${s.first_name} ${s.last_name} ${s.roll_number ?? ''}`.toLowerCase().includes(search)
    );

    if (filtered.length === 0) {
        list.innerHTML = `<div class="pg-empty-state"><i class="feather-search"></i><p>No match found.</p></div>`;
        return;
    }

    list.innerHTML = filtered.map((s, idx) => `
        <div class="pg-student-row selected" data-id="${s.id}">
            <div class="pg-serial">${idx + 1}</div>
            <div class="pg-avatar selected-av">${initials(s.first_name, s.last_name)}</div>
            <div class="pg-info">
                <span class="pg-name">${esc(s.first_name)} ${esc(s.last_name)}</span>
                <span class="pg-roll">${esc(s.roll_number ?? '')}</span>
            </div>
            <button type="button" class="pg-action-btn remove" onclick="removeStudent(${s.id})" title="Remove">
                <i class="feather-minus-circle"></i>
            </button>
        </div>`).join('');
}

function syncHiddenInputs() {
    document.getElementById('selectedStudentInputs').innerHTML =
        selected.map(s => `<input type="hidden" name="student_ids[]" value="${s.id}">`).join('');
}

function renderTimetableRows() {
    const tbody = document.getElementById('timetableRows');

    if (!timetableRows.length) {
        tbody.innerHTML = `
            <tr class="timetable-empty-row">
                <td colspan="7" class="text-center text-muted py-4">
                    No timetable slots added yet. Click <strong>Add Slot</strong> to build one.
                </td>
            </tr>`;
        document.getElementById('timetable_json').value = '';
        return;
    }

    tbody.innerHTML = timetableRows.map((row, index) => `
        <tr class="timetable-row" data-index="${index}">
            <td>
                <select class="form-select form-select-sm" data-field="day">
                    ${['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'].map(day =>
                        `<option value="${day}" ${row.day === day ? 'selected' : ''}>${day}</option>`
                    ).join('')}
                </select>
            </td>
            <td><input type="time" class="form-control form-control-sm" data-field="start_time" value="${row.start_time ?? ''}"></td>
            <td><input type="time" class="form-control form-control-sm" data-field="end_time" value="${row.end_time ?? ''}"></td>
            <td><input type="text" class="form-control form-control-sm" data-field="room" value="${esc(row.room ?? '')}" placeholder="Lab 101"></td>
            <td><input type="text" class="form-control form-control-sm" data-field="teacher" value="${esc(row.teacher ?? '')}" placeholder="Optional"></td>
            <td><input type="text" class="form-control form-control-sm" data-field="subject" value="${esc(row.subject ?? '')}" placeholder="Practical topic"></td>
            <td class="text-end">
                <button type="button" class="timetable-remove-btn" onclick="removeTimetableRow(${index})" title="Remove slot">
                    <i class="feather-trash-2"></i>
                </button>
            </td>
        </tr>
    `).join('');

    tbody.querySelectorAll('input, select').forEach(input => {
        input.addEventListener('input', syncTimetableState);
        input.addEventListener('change', syncTimetableState);
    });

    syncTimetableState();
}

function syncTimetableState() {
    const tbody = document.getElementById('timetableRows');
    timetableRows = [...tbody.querySelectorAll('.timetable-row')].map(rowEl => {
        const get = field => rowEl.querySelector(`[data-field="${field}"]`)?.value ?? '';
        return {
            day: get('day'),
            start_time: get('start_time'),
            end_time: get('end_time'),
            room: get('room'),
            teacher: get('teacher'),
            subject: get('subject'),
        };
    });
    document.getElementById('timetable_json').value = JSON.stringify(timetableRows);
}

function addTimetableRow(data = {}) {
    if (document.querySelector('.timetable-empty-row')) {
        document.querySelector('.timetable-empty-row').remove();
    }
    timetableRows.push({
        day: 'Monday',
        start_time: '',
        end_time: '',
        room: '',
        teacher: '',
        subject: '',
        ...data,
    });
    renderTimetableRows();
}

function removeTimetableRow(index) {
    timetableRows.splice(index, 1);
    renderTimetableRows();
}

function addStudent(id) {
    // Enforce group strength cap
    if (groupStrength > 0 && selected.length >= groupStrength) {
        alert(`This group has a maximum strength of ${groupStrength}. Please remove a student first.`);
        return;
    }
    const s = allAvailable.find(s => s.id === id);
    if (!s || selected.find(x => x.id === id)) return;
    selected.push(s);
    renderBothLists();
}

function removeStudent(id) {
    selected = selected.filter(s => s.id !== id);
    renderBothLists();
}

document.getElementById('searchAvailable').addEventListener('input', renderAvailable);
document.getElementById('searchSelected').addEventListener('input', renderSelected);

function initials(f, l) { return ((f?.[0] ?? '') + (l?.[0] ?? '')).toUpperCase(); }
function esc(str) {
    if (str == null) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

/* ── Init edit mode ── */
if (PRE_SELECTED.length) {
    selected = PRE_SELECTED;
    // allAvailable starts empty on edit page load;
    // pre-selected students appear in the right panel.
    // When user clicks Load Students, allAvailable gets populated and they merge.
    renderBothLists();
}

renderTimetableRows();
document.getElementById('addTimetableRowBtn').addEventListener('click', () => addTimetableRow());

/* ── Init group strength from pre-selected group (edit mode) ── */
(function initGroupStrength() {
    const groupEl = document.getElementById('group_master_id');
    if (groupEl.value) {
        const opt = groupEl.options[groupEl.selectedIndex];
        groupStrength = parseInt(opt?.getAttribute('data-strength') || '0', 10);
        if (groupStrength > 0) {
            document.getElementById('groupStrength').textContent = groupStrength;
            document.getElementById('strengthInfo').style.display = 'inline';
        }
    }
})();
</script>
@endpush
@endsection
