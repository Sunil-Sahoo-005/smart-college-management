@extends('admin_layout')
@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Academics.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            {{-- HEADER --}}
            <div class="content-area-header bg-white px-4 py-3 border-bottom shadow-sm mb-2"
                 style="position:sticky; top:0; z-index:200;">
                <div class="d-flex align-items-center gap-3 flex-wrap w-100">
                    <div class="flex-shrink-0">
                        {{-- Agar kisi grading system se aaye hain toh uska naam dikhao --}}
                        @if(isset($selectedGradingSystem))
                            <h5 class="fw-bold mb-0 text-dark">
                                {{ $selectedGradingSystem->name }}
                                <span class="text-muted fw-normal">— Grade Levels</span>
                            </h5>
                            <small class="text-muted">
                                <a href="{{ route('admin.grading_systems.index') }}" class="text-muted text-decoration-none">
                                    <i class="feather-arrow-left me-1"></i>Back to Grading Systems
                                </a>
                            </small>
                        @else
                            <h5 class="fw-bold mb-0 text-dark">Grading Level List</h5>
                            <small class="text-muted">
                                Showing <span id="visibleCount">{{ count($gradeLevelsJson) }}</span>
                                of {{ count($gradeLevelsJson) }} grade levels
                            </small>
                        @endif
                    </div>

                    <div class="position-relative flex-grow-1" style="max-width:340px; min-width:160px;">
                        <i class="feather-search position-absolute top-50 translate-middle-y text-muted"
                           style="font-size:13px; left:10px;"></i>
                        <input type="text" id="searchInput"
                            class="form-control form-control-sm ps-4 border-secondary-subtle"
                            placeholder="Search by grade …"
                            style="border-radius:6px;">
                    </div>

                    <button type="button" id="clearAllBtn"
                        class="btn btn-outline-secondary btn-sm px-3 flex-shrink-0 d-none">
                        <i class="feather-x me-1"></i> Clear
                    </button>

                    <div class="ms-auto d-flex align-items-center gap-2 flex-shrink-0 flex-wrap">

                        {{-- Grading System filter — sirf tab dikhao jab koi system selected nahi --}}
                        @if(!isset($selectedGradingSystem))
                            <select id="filter_grading_system" class="form-select form-select-sm filter-select"
                                style="width:auto; min-width:160px;">
                                <option value="">All Grading Systems</option>
                                @foreach ($gradingSystems as $gs)
                                    <option value="{{ $gs->id }}">{{ $gs->name }}</option>
                                @endforeach
                            </select>
                        @else
                            {{-- Hidden select — JS ke liye zaroori --}}
                            <select id="filter_grading_system" class="d-none">
                                <option value="{{ $selectedGradingSystemId }}" selected></option>
                            </select>
                        @endif

                        <div class="vr mx-1" style="height:24px;"></div>

                        @if (auth()->user()->hasPermissionTo('View Grade Levels'))
                            <a href="{{ route('admin.grade_levels.create', isset($selectedGradingSystemId) ? ['grading_system_id' => $selectedGradingSystemId] : []) }}"
                               class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1">
                                <i class="feather-plus" style="font-size:13px;"></i>
                                <span class="d-none d-sm-inline">
                                    @if(isset($selectedGradingSystem))
                                        Add Grade Level
                                    @else
                                        Create Grade Level
                                    @endif
                                </span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- CONTENT --}}
            <div class="content-area-body">
                @include('elements.flash')

                <div class="card stretch stretch-full">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        {{-- Agar system selected hai toh Grading System column hide karo --}}
                                        @if(!isset($selectedGradingSystem))
                                            <th class="fw-semibold">Grading System</th>
                                        @endif
                                        <th class="fw-semibold">Range</th>
                                        <th class="fw-semibold">Grade</th>
                                        <th class="fw-semibold">Remarks</th>
                                        <th class="text-center fw-semibold">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="listTbody"></tbody>
                            </table>
                        </div>

                        <div id="tablePagination"
                             class="p-3 border-top d-flex justify-content-between align-items-center gap-2 flex-wrap d-none">
                            <small class="text-muted" id="paginationInfo"></small>
                            <div class="d-flex gap-1" id="paginationBtns"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const ALL_DATA              = @json($gradeLevelsJson);
const CAN_EDIT              = {{ auth()->user()->hasPermissionTo('Edit Grade Levels')   ? 'true' : 'false' }};
const CAN_DELETE            = {{ auth()->user()->hasPermissionTo('Delete Grade Levels') ? 'true' : 'false' }};
const CSRF_TOKEN            = '{{ csrf_token() }}';
const SHOW_SYSTEM_COLUMN    = {{ isset($selectedGradingSystem) ? 'false' : 'true' }};
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const PAGE_SIZE           = 20;
    let filtered              = [...ALL_DATA];
    let currentPage           = 1;

    const searchInput         = document.getElementById('searchInput');
    const clearAllBtn         = document.getElementById('clearAllBtn');
    const visibleCount        = document.getElementById('visibleCount');
    const filterGradingSystem = document.getElementById('filter_grading_system');
    const filterSelects       = document.querySelectorAll('.filter-select');

    searchInput.addEventListener('input',  () => { currentPage = 1; applyFilters(); });
    filterSelects.forEach(sel => sel.addEventListener('change', () => { currentPage = 1; applyFilters(); }));
    clearAllBtn.addEventListener('click', clearAll);

    // Agar grading system pre-selected hai toh filter auto-apply hoga
    function applyFilters() {
        const search        = searchInput.value.trim().toLowerCase();
        const gradingSystem = filterGradingSystem?.value || '';

        filtered = ALL_DATA.filter(g => {
            if (search        && !g.grade_letter.toLowerCase().includes(search))  return false;
            if (gradingSystem && String(g.grading_system_id) !== gradingSystem)   return false;
            return true;
        });

        const hasFilter = search;
        clearAllBtn.classList.toggle('d-none', !hasFilter);
        if (visibleCount) visibleCount.textContent = filtered.length;
        render();
    }

    function clearAll() {
        searchInput.value = '';
        filterSelects.forEach(sel => sel.value = '');
        currentPage = 1;
        applyFilters();
    }

    function render() {
        const totalPages = Math.max(1, Math.ceil(filtered.length / PAGE_SIZE));
        if (currentPage > totalPages) currentPage = totalPages;
        const start = (currentPage - 1) * PAGE_SIZE;
        const paged = filtered.slice(start, start + PAGE_SIZE);
        renderTable(paged, start);
        renderPagination(totalPages, start, paged.length);
    }

    function renderTable(paged, start) {
        const tbody      = document.getElementById('listTbody');
        const pagination = document.getElementById('tablePagination');
        const colspan    = SHOW_SYSTEM_COLUMN ? 5 : 4;

        if (paged.length === 0) {
            tbody.innerHTML = `
                <tr><td colspan="${colspan}" class="text-center py-5">
                    <i class="feather-bar-chart-2 text-muted mb-3 d-block" style="font-size:3rem;"></i>
                    <h6 class="text-muted">No grade levels found</h6>
                    <p class="text-muted small mb-0">Try adjusting your filters</p>
                </td></tr>`;
            pagination.classList.add('d-none');
            return;
        }

        tbody.innerHTML = paged.map(g => {
            const systemCol = SHOW_SYSTEM_COLUMN
                ? `<td><span class="fw-medium">${esc(g.grading_system_name)}</span></td>`
                : '';

            const editBtn = CAN_EDIT
                ? `<a href="${g.edit_url}" class="avatar-text avatar-md text-warning" title="Edit">
                       <i class="feather-edit-3"></i>
                   </a>`
                : '';

            const deleteBtn = CAN_DELETE
                ? `<form action="${g.delete_url}" method="POST" class="d-inline"
                       onsubmit="return confirm('Are you sure you want to delete this grade level?')">
                       <input type="hidden" name="_token" value="${CSRF_TOKEN}">
                       <input type="hidden" name="_method" value="DELETE">
                       <button type="submit" class="avatar-text avatar-md border-0 bg-transparent text-danger" title="Delete">
                           <i class="feather-trash-2"></i>
                       </button>
                   </form>`
                : '';

            return `
                <tr>
                    ${systemCol}
                    <td>${esc(g.min_score)} – ${esc(g.max_score)}</td>
                    <td><span class="badge bg-info-subtle text-info fw-semibold">${esc(g.grade_letter)}</span></td>
                    <td class="text-muted">${esc(g.remarks)}</td>
                    <td>
                        <div class="hstack gap-2 justify-content-center">
                            ${editBtn}
                            ${deleteBtn}
                        </div>
                    </td>
                </tr>`;
        }).join('');

        pagination.classList.remove('d-none');
        document.getElementById('paginationInfo').textContent =
            `Showing ${start + 1}–${Math.min(start + paged.length, filtered.length)} of ${filtered.length}`;
    }

    function renderPagination(totalPages, start, pagedLength) {
        if (totalPages <= 1) {
            document.getElementById('tablePagination')?.classList.add('d-none');
            return;
        }

        const makeBtn = (label, page, disabled = false, active = false) =>
            `<button class="btn btn-sm ${active ? 'btn-primary' : 'btn-outline-secondary'}"
                ${disabled ? 'disabled' : ''} data-page="${page}">${label}</button>`;

        let html = makeBtn('&laquo;', currentPage - 1, currentPage === 1);
        for (let p = 1; p <= totalPages; p++) {
            if (totalPages <= 7 || p === 1 || p === totalPages || Math.abs(p - currentPage) <= 1) {
                html += makeBtn(p, p, false, p === currentPage);
            } else if (Math.abs(p - currentPage) === 2) {
                html += `<button class="btn btn-sm btn-outline-secondary" disabled>…</button>`;
            }
        }
        html += makeBtn('&raquo;', currentPage + 1, currentPage === totalPages);

        const btnsEl = document.getElementById('paginationBtns');
        btnsEl.innerHTML = html;
        btnsEl.querySelectorAll('[data-page]').forEach(btn => {
            btn.addEventListener('click', function () {
                currentPage = parseInt(this.dataset.page);
                render();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });
    }

    function esc(str) {
        if (str == null) return '';
        return String(str)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    applyFilters();
});
</script>

<style>
    .filter-select { border-radius:6px; font-size:0.8125rem; border-color:#dee2e6; cursor:pointer; transition:all .2s; }
    .filter-select:hover { border-color:#0d6efd; background-color:#f8f9fa; }
    .filter-select:focus { border-color:#0d6efd; box-shadow:0 0 0 2px rgba(13,110,253,.15); }
    .table th { white-space:nowrap; font-size:0.8125rem; padding:0.75rem 1rem; }
    .table td { padding:0.75rem 1rem; vertical-align:middle; font-size:0.875rem; }
    .content-area { overflow-y: auto !important; }
</style>
@endsection