@extends('admin_layout')
@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Academics.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            {{-- ── Header ─────────────────────────────────────────────────── --}}
            <div class="content-area-header bg-white sticky-top shadow-sm">
                <div class="d-flex flex-column">
                    <h5 class="fw-bold mb-0">
                        Manage Semesters/Years &mdash;
                        <span class="text-primary">{{ $courseStream->stream->name }}</span>
                    </h5>
                    <small class="text-muted mt-1 fs-12">
                        <i class="feather-book-open me-1"></i>
                        {{ $courseStream->course->name }}
                        ({{ $courseStream->course->code }},
                        {{ $courseStream->course->duration_years }} yr{{ $courseStream->course->duration_years > 1 ? 's' : '' }})
                    </small>
                </div>

                <div class="page-header-right ms-auto d-flex align-items-center gap-2">
                    <a href="{{ route('admin.courses.index') }}"
                       class="btn btn-outline-secondary btn-sm">
                        <i class="feather-arrow-left me-1"></i> Back
                    </a>
                    @if($availableClasses->count())
                        <button type="button"
                                class="btn btn-primary btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#addClassModal">
                            <i class="feather-plus me-1"></i> Add Semester/Year
                        </button>
                    @endif
                </div>
            </div>

            {{-- ── Body ──────────────────────────────────────────────────── --}}
            <div class="content-area-body p-4 bg-light">
                @include('elements.flash')

                @if($assignedMappings->count())

                    <div class="mb-4 d-flex align-items-center text-muted fs-12 text-uppercase fw-semibold letter-spacing-1">
                        Assigned Semesters/Years ({{ $assignedMappings->count() }})
                        <hr class="flex-grow-1 ms-3 border-secondary opacity-25">
                    </div>

                    @foreach($assignedMappings as $mapping)
                        <div class="card border-0 mb-4 rounded-3 shadow-sm overflow-hidden class-card">

                            {{-- Class card header --}}
                            <div class="card-header bg-white border-bottom py-3 px-4
                                        d-flex justify-content-between align-items-center">

                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-circle bg-primary-subtle text-primary fw-bolder fs-15">
                                        {{ strtoupper(substr($mapping->class->name ?? 'C', 0, 1)) }}
                                    </div>
                                    <div>
                                        <h5 class="mb-0 fs-15 fw-bold text-dark">
                                            {{ $mapping->class->name ?? 'Unknown Class' }}
                                        </h5>
                                        <div class="d-flex gap-2 mt-1">
                                            <span class="badge-stat bg-primary-subtle text-primary">
                                                <i class="feather-users fs-11 me-1"></i>
                                                {{ $mapping->sections->count() }}
                                                {{ Str::plural('Section', $mapping->sections->count()) }}
                                            </span>
                                            <span class="badge-stat bg-warning-subtle text-warning-dark">
                                                <i class="feather-book-open fs-11 me-1"></i>
                                                {{ $mapping->classSubjectMappings->count() }}
                                                {{ Str::plural('Subject', $mapping->classSubjectMappings->count()) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <form action="{{ route('admin.streams.removeClass', [$courseStream, $mapping->class_id]) }}"
                                      method="POST"
                                      onsubmit="return confirm('Remove this class from the branch? Sections and subjects will also be detached.')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger fw-medium px-3 hover-elevate">
                                        <i class="feather-trash-2 me-1"></i> Detach
                                    </button>
                                </form>
                            </div>

                            {{-- Sections + Subjects panels --}}
                            <div class="card-body bg-light p-4">
                                <div class="row g-4">

                                    {{-- ── Sections ──────────────────────────────── --}}
                                    <div class="col-lg-6">
                                        <div class="card h-100 border-0 shadow-sm rounded-3">
                                            <div class="card-header bg-white border-bottom
                                                        d-flex justify-content-between align-items-center py-3 px-3">
                                                <h6 class="fw-bold fs-14 mb-0 text-dark">
                                                    <i class="feather-users me-2 text-primary fs-16"></i>Sections
                                                </h6>
                                                <button type="button"
                                                        class="btn btn-sm btn-primary-subtle text-primary
                                                               fw-medium rounded-pill px-3 py-1
                                                               btn-add-section hover-elevate"
                                                        data-action="{{ route('admin.streams.mappings.sections.store', [$courseStream, $mapping]) }}"
                                                        data-classname="{{ $mapping->class->name }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#addSectionModal">
                                                    <i class="feather-plus me-1"></i> Add
                                                </button>
                                            </div>

                                            <div class="card-body p-0 bg-white">
                                                @if($mapping->sections->count())
                                                    <div class="list-group list-group-flush">
                                                        @foreach($mapping->sections as $section)
                                                            <div class="list-group-item d-flex justify-content-between
                                                                        align-items-center px-4 py-3 border-bottom">
                                                                <div>
                                                                    <span class="fw-bold text-dark fs-13">
                                                                        {{ $section->name }}
                                                                    </span>
                                                                    <small class="d-block text-muted fs-11 mt-1">
                                                                        Max cap: {{ $section->max_strength ?? 'N/A' }}
                                                                    </small>
                                                                </div>
                                                                <form action="{{ route('admin.streams.mappings.sections.destroy', [$courseStream, $mapping, $section]) }}"
                                                                      method="POST"
                                                                      onsubmit="return confirm('Delete this section?');">
                                                                    @csrf @method('DELETE')
                                                                    <button class="btn btn-sm text-danger p-1
                                                                                   bg-danger-subtle rounded
                                                                                   hover-bg-danger transition"
                                                                            title="Delete">
                                                                        <i class="feather-trash-2 fs-14"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    @include('Academics.streams._empty', ['label' => 'No sections yet'])
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- ── Subjects ───────────────────────────────── --}}
                                    <div class="col-lg-6">
                                        <div class="card h-100 border-0 shadow-sm rounded-3">
                                            <div class="card-header bg-white border-bottom
                                                        d-flex justify-content-between align-items-center py-3 px-3">
                                                <h6 class="fw-bold fs-14 mb-0 text-dark">
                                                    <i class="feather-book-open me-2 text-warning fs-16"></i>Subjects
                                                </h6>
                                                <button type="button"
                                                        class="btn btn-sm btn-warning-subtle text-warning-dark
                                                               fw-medium rounded-pill px-3 py-1
                                                               btn-add-subject hover-elevate"
                                                        data-action="{{ route('admin.streams.mappings.subjects.store', [$courseStream, $mapping]) }}"
                                                        data-classname="{{ $mapping->class->name }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#addSubjectModal">
                                                    <i class="feather-plus me-1"></i> Add
                                                </button>
                                            </div>

                                            <div class="card-body p-0 bg-white">
                                                @if($mapping->classSubjectMappings->count())
                                                    <div class="list-group list-group-flush">
                                                        @foreach($mapping->classSubjectMappings as $subMap)
                                                            <div class="list-group-item d-flex justify-content-between
                                                                        align-items-center px-4 py-3 border-bottom">
                                                                <div class="pe-3 flex-grow-1">
                                                                    <div class="d-flex align-items-center
                                                                                justify-content-between mb-1">
                                                                        <span class="fw-bold text-dark fs-13 text-truncate">
                                                                            {{ $subMap->subject->name ?? 'Unknown' }}
                                                                        </span>
                                                                        @if($subMap->is_compulsory)
                                                                            <span class="type-badge bg-success-subtle
                                                                                         text-success border-success-subtle">
                                                                                Required
                                                                            </span>
                                                                        @else
                                                                            <span class="type-badge bg-secondary-subtle
                                                                                         text-secondary border-secondary-subtle">
                                                                                Optional
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                    <small class="text-muted fs-11">
                                                                        Code:
                                                                        <span class="fw-medium text-dark">
                                                                            {{ $subMap->subject->code ?? '—' }}
                                                                        </span>
                                                                    </small>
                                                                </div>
                                                                <form action="{{ route('admin.streams.mappings.subjects.destroy', [$courseStream, $mapping, $subMap]) }}"
                                                                      method="POST"
                                                                      onsubmit="return confirm('Remove this subject?');">
                                                                    @csrf @method('DELETE')
                                                                    <button class="btn btn-sm text-danger p-1
                                                                                   bg-danger-subtle rounded
                                                                                   hover-bg-danger transition"
                                                                            title="Remove">
                                                                        <i class="feather-trash-2 fs-14"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    @include('Academics.streams._empty', ['label' => 'No subjects yet'])
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                </div>{{-- /row --}}
                            </div>{{-- /card-body --}}
                        </div>{{-- /class-card --}}
                    @endforeach

                @else
                    <div class="text-center py-5 mt-4">
                        <div class="empty-state-icon bg-white shadow-sm border rounded-circle
                                    mx-auto mb-3 d-flex align-items-center justify-content-center"
                             style="width: 80px; height: 80px;">
                            <i class="feather-folder-minus text-muted" style="font-size: 2.2rem;"></i>
                        </div>
                        <h5 class="fw-bold mb-2">No Semester/Year Assigned</h5>
                        <p class="text-muted mb-4">This branch has no semesters/years mapped yet.</p>
                        @if($availableClasses->count())
                            <button type="button"
                                    class="btn btn-primary px-4 py-2 rounded-pill shadow-sm hover-elevate"
                                    data-bs-toggle="modal"
                                    data-bs-target="#addClassModal">
                                <i class="feather-plus me-2"></i> Assign First Semester/Year
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════
     MODALS
═══════════════════════════════════════════════════════════════════ --}}

{{-- Add Class Modal --}}
<div class="modal fade" id="addClassModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">
                    Map Semester/Year &mdash;
                    <span class="text-primary">{{ $courseStream->stream->name }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.streams.addClass', $courseStream) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <label for="class_id" class="form-label fw-semibold text-dark">Select Semester/Year</label>
                    <select name="class_id" id="class_id" class="form-select" required>
                        <option value="">— Choose Semester/Year —</option>
                        @foreach($availableClasses as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                    <div class="form-text mt-2">
                        <i class="feather-info me-1"></i>
                        Only semesters/years not yet assigned to this branch are shown.
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Map Semester/Year</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── Add Section Modal (Multiple) ── --}}
<div class="modal fade" id="addSectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">
                    Add Sections &mdash;
                    <span class="section-target-class text-primary fs-14 fw-medium"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="sectionForm" method="POST">
                @csrf
                <div class="modal-body p-4">
                    {{-- Column labels --}}
                    <div class="d-flex align-items-center gap-2 mb-2 px-1">
                        <div class="flex-grow-1">
                            <small class="fw-semibold text-muted text-uppercase"
                                   style="font-size:11px; letter-spacing:.5px;">
                                Section Name <span class="text-danger">*</span>
                            </small>
                        </div>
                        <div style="width:140px; flex-shrink:0;">
                            <small class="fw-semibold text-muted text-uppercase"
                                   style="font-size:11px; letter-spacing:.5px;">
                                Max Capacity
                            </small>
                        </div>
                        <div style="width:36px; flex-shrink:0;"></div>
                    </div>
                    <div id="sectionRowsContainer"></div>
                    <button type="button" id="addSectionRowBtn"
                            class="btn btn-sm btn-outline-primary rounded-pill px-3 mt-2">
                        <i class="feather-plus me-1"></i> Add Another Section
                    </button>
                    <div class="form-text mt-3 text-muted">
                        <i class="feather-info me-1"></i>
                        Duplicate names will be skipped automatically.
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="feather-save me-1"></i> Save Sections
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── Add Subject Modal (Multiple) ── --}}
<div class="modal fade" id="addSubjectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">
                    Assign Subjects &mdash;
                    <span class="subject-target-class text-warning-dark fs-14 fw-medium"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="subjectForm" method="POST">
                @csrf
                <div class="modal-body p-4">

                    {{-- Column labels --}}
                    <div class="d-flex align-items-center gap-2 mb-2 px-1">
                        <div class="flex-grow-1">
                            <small class="fw-semibold text-muted text-uppercase"
                                   style="font-size:11px; letter-spacing:.5px;">
                                Subject <span class="text-danger">*</span>
                            </small>
                        </div>
                        <div style="width:160px; flex-shrink:0;">
                            <small class="fw-semibold text-muted text-uppercase"
                                   style="font-size:11px; letter-spacing:.5px;">
                                Type
                            </small>
                        </div>
                        <div style="width:36px; flex-shrink:0;"></div>
                    </div>

                    {{-- Subject rows container --}}
                    <div id="subjectRowsContainer"></div>

                    <button type="button" id="addSubjectRowBtn"
                            class="btn btn-sm btn-outline-warning rounded-pill px-3 mt-2">
                        <i class="feather-plus me-1"></i> Add Another Subject
                    </button>

                    <div class="form-text mt-3 text-muted">
                        <i class="feather-info me-1"></i>
                        Already assigned subjects will be skipped automatically.
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 text-dark fw-semibold">
                        <i class="feather-save me-1"></i> Assign Subjects
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════
     JS
═══════════════════════════════════════════════════════════════════ --}}
<script>
{{-- Pass all subjects to JS for dynamic dropdowns --}}
const ALL_SUBJECTS = @json($allSubjects->map(fn($s) => ['id' => $s->id, 'name' => $s->name, 'code' => $s->code]));
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ══════════════════════════════════════════════════════
    // SECTION MODAL
    // ══════════════════════════════════════════════════════
    const sectionForm        = document.getElementById('sectionForm');
    const sectionTargetLabel = document.querySelector('.section-target-class');
    const sectionRowsCont    = document.getElementById('sectionRowsContainer');
    let   sectionRowIndex    = 0;

    function makeSectionRow(index, removable) {
        const row = document.createElement('div');
        row.className = 'section-row d-flex align-items-center gap-2 mb-2';
        row.innerHTML = `
            <div class="flex-grow-1">
                <input type="text"
                       name="sections[${index}][name]"
                       class="form-control"
                       placeholder="e.g. A, B, Div 1" autocomplete="off"
                       ${index === 0 ? 'required' : ''}>
            </div>
            <div style="width:140px; flex-shrink:0;">
                <input type="number"
                       name="sections[${index}][max_strength]"
                       class="form-control"
                       placeholder="e.g. 60" autocomplete="off"
                       min="1">
            </div>
            <div style="width:36px; flex-shrink:0; text-align:center;">
                ${removable
                    ? `<button type="button"
                               class="btn btn-sm btn-outline-danger rounded p-0 remove-section-row"
                               style="width:32px;height:32px;" title="Remove">
                           <i class="feather-x"></i>
                       </button>`
                    : ''}
            </div>`;
        return row;
    }

    function addFirstSectionRow() {
        sectionRowsCont.innerHTML = '';
        sectionRowIndex = 0;
        sectionRowsCont.appendChild(makeSectionRow(sectionRowIndex++, false));
    }

    document.getElementById('addSectionRowBtn').addEventListener('click', function () {
        sectionRowsCont.appendChild(makeSectionRow(sectionRowIndex++, true));
        sectionRowsCont.querySelector('.section-row:last-child input[type="text"]')?.focus();
    });

    sectionRowsCont.addEventListener('click', function (e) {
        const btn = e.target.closest('.remove-section-row');
        if (btn) btn.closest('.section-row').remove();
    });

    document.querySelectorAll('.btn-add-section').forEach(btn => {
        btn.addEventListener('click', function () {
            sectionForm.action             = this.dataset.action;
            sectionTargetLabel.textContent = this.dataset.classname;
            addFirstSectionRow();
        });
    });

    document.getElementById('addSectionModal').addEventListener('hidden.bs.modal', function () {
        addFirstSectionRow();
        sectionForm.action             = '';
        sectionTargetLabel.textContent = '';
    });

    addFirstSectionRow();

    // ══════════════════════════════════════════════════════
    // SUBJECT MODAL
    // ══════════════════════════════════════════════════════
    const subjectForm        = document.getElementById('subjectForm');
    const subjectTargetLabel = document.querySelector('.subject-target-class');
    const subjectRowsCont    = document.getElementById('subjectRowsContainer');
    let   subjectRowIndex    = 0;

    function buildSubjectOptions() {
        return ALL_SUBJECTS.map(s =>
            `<option value="${s.id}">${s.name}${s.code ? ' (' + s.code + ')' : ''}</option>`
        ).join('');
    }

    function makeSubjectRow(index, removable) {
        const row = document.createElement('div');
        row.className = 'subject-row d-flex align-items-center gap-2 mb-2';
        row.innerHTML = `
            <div class="flex-grow-1">
                <select name="subjects[${index}][subject_id]"
                        class="form-select"
                        ${index === 0 ? 'required' : ''}>
                    <option value="">— Choose Subject —</option>
                    ${buildSubjectOptions()}
                </select>
            </div>
            <div style="width:160px; flex-shrink:0;">
                <select name="subjects[${index}][is_compulsory]" class="form-select">
                    <option value="1">Compulsory</option>
                    <option value="0">Optional</option>
                </select>
            </div>
            <div style="width:36px; flex-shrink:0; text-align:center;">
                ${removable
                    ? `<button type="button"
                               class="btn btn-sm btn-outline-danger rounded p-0 remove-subject-row"
                               style="width:32px;height:32px;" title="Remove">
                           <i class="feather-x"></i>
                       </button>`
                    : ''}
            </div>`;
        return row;
    }

    function addFirstSubjectRow() {
        subjectRowsCont.innerHTML = '';
        subjectRowIndex = 0;
        subjectRowsCont.appendChild(makeSubjectRow(subjectRowIndex++, false));
    }

    document.getElementById('addSubjectRowBtn').addEventListener('click', function () {
        subjectRowsCont.appendChild(makeSubjectRow(subjectRowIndex++, true));
        subjectRowsCont.querySelector('.subject-row:last-child select')?.focus();
    });

    subjectRowsCont.addEventListener('click', function (e) {
        const btn = e.target.closest('.remove-subject-row');
        if (btn) btn.closest('.subject-row').remove();
    });

    document.querySelectorAll('.btn-add-subject').forEach(btn => {
        btn.addEventListener('click', function () {
            subjectForm.action             = this.dataset.action;
            subjectTargetLabel.textContent = this.dataset.classname;
            addFirstSubjectRow();
        });
    });

    document.getElementById('addSubjectModal').addEventListener('hidden.bs.modal', function () {
        addFirstSubjectRow();
        subjectForm.action             = '';
        subjectTargetLabel.textContent = '';
    });

    addFirstSubjectRow();

});
</script>

{{-- ══════════════════════════════════════════════════════════════════
     STYLES
═══════════════════════════════════════════════════════════════════ --}}
<style>
    .hover-elevate        { transition: transform .2s ease, box-shadow .2s ease; }
    .hover-elevate:hover  { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,.08); }
    .transition           { transition: all .2s ease; }

    .avatar-circle {
        width: 38px; height: 38px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 15px; flex-shrink: 0;
    }

    .badge-stat {
        display: inline-flex; align-items: center;
        padding: 2px 8px; border-radius: 50rem;
        font-size: 11px; font-weight: 500;
    }

    .type-badge {
        display: inline-block; padding: 2px 8px; border-radius: 50rem;
        font-size: 10px; font-weight: 600;
        border: 1px solid transparent; white-space: nowrap; flex-shrink: 0;
    }

    .bg-primary-subtle   { background-color: #e0e7ff !important; }
    .bg-warning-subtle   { background-color: #fef3c7 !important; }
    .bg-success-subtle   { background-color: #dcfce7 !important; }
    .bg-secondary-subtle { background-color: #f1f5f9 !important; }
    .bg-danger-subtle    { background-color: #fee2e2 !important; }

    .text-primary      { color: #4f46e5 !important; }
    .text-warning-dark { color: #b45309 !important; }
    .text-success      { color: #166534 !important; }
    .text-secondary    { color: #475569 !important; }

    .hover-bg-danger:hover { background-color: #ef4444 !important; color: #fff !important; }

    .btn-primary-subtle { background-color: #e0e7ff; border: 1px solid #c7d2fe; transition: background .2s; }
    .btn-primary-subtle:hover { background-color: #c7d2fe; }

    .btn-warning-subtle { background-color: #fef3c7; border: 1px solid #fde68a; transition: background .2s; }
    .btn-warning-subtle:hover { background-color: #fde68a; }

    .fs-10{font-size:10px} .fs-11{font-size:11px} .fs-12{font-size:12px}
    .fs-13{font-size:13px} .fs-14{font-size:14px} .fs-15{font-size:15px} .fs-16{font-size:16px}

    .letter-spacing-1 { letter-spacing: .5px; }
    .cursor-pointer   { cursor: pointer; }
    .class-card       { border: 1px solid rgba(0,0,0,.07) !important; }
</style>
@endsection