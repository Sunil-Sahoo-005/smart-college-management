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
                        <h5 class="fw-bold mb-0 text-dark">Group Masters</h5>
                        <small class="text-muted">
                            Showing <span id="visibleCount">{{ count($groupMastersJson) }}</span>
                            of {{ count($groupMastersJson) }} groups
                        </small>
                    </div>

                    <div class="position-relative flex-grow-1" style="max-width:380px; min-width:180px;">
                        <i class="feather-search position-absolute top-50 translate-middle-y text-muted"
                           style="font-size:13px; left:10px;"></i>
                        <input type="text" id="searchInput"
                            class="form-control form-control-sm ps-4 border-secondary-subtle"
                            placeholder="Search by group name…"
                            style="border-radius:6px;">
                    </div>

                    <button type="button" id="clearAllBtn"
                        class="btn btn-outline-secondary btn-sm px-3 flex-shrink-0 d-none">
                        <i class="feather-x me-1"></i> Clear
                    </button>

                    <div class="ms-auto d-flex align-items-center gap-2 flex-shrink-0 flex-wrap">

                        {{-- Status filter --}}
                        <select id="filter_status" class="form-select form-select-sm filter-select"
                            style="width:auto; min-width:130px;">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>

                        <div class="vr mx-1" style="height:24px;"></div>

                        @if (auth()->user()->hasPermissionTo('Create Group Master'))
                            <a href="{{ route('group_master.create') }}"
                               class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1">
                                <i class="feather-plus" style="font-size:13px;"></i>
                                <span class="d-none d-sm-inline">Add Group</span>
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
                                        <th class="fw-semibold">Academic Group Name</th>
                                        <th class="fw-semibold">Strength</th>
                                        <th class="fw-semibold">Created Date</th>
                                        <th class="fw-semibold">Status</th>
                                        <th class="text-end fw-semibold">Actions</th>
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
const ALL_DATA   = @json($groupMastersJson);
const CSRF_TOKEN = '{{ csrf_token() }}';
const CAN_EDIT   = {{ auth()->user()->hasPermissionTo('Edit Group Master')   ? 'true' : 'false' }};
const CAN_DELETE = {{ auth()->user()->hasPermissionTo('Delete Group Master') ? 'true' : 'false' }};
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const PAGE_SIZE    = 20;
    let filtered       = [...ALL_DATA];
    let currentPage    = 1;

    const searchInput   = document.getElementById('searchInput');
    const clearAllBtn   = document.getElementById('clearAllBtn');
    const visibleCount  = document.getElementById('visibleCount');
    const filterStatus  = document.getElementById('filter_status');
    const filterSelects = document.querySelectorAll('.filter-select');

    searchInput.addEventListener('input',  () => { currentPage = 1; applyFilters(); });
    filterSelects.forEach(sel => sel.addEventListener('change', () => { currentPage = 1; applyFilters(); }));
    clearAllBtn.addEventListener('click', clearAll);

    function applyFilters() {
        const search = searchInput.value.trim().toLowerCase();
        const status = filterStatus.value;

        filtered = ALL_DATA.filter(g => {
            if (search && !g.name.toLowerCase().includes(search)) return false;
            if (status && g.status !== status)                    return false;
            return true;
        });

        const hasFilter = search || status;
        clearAllBtn.classList.toggle('d-none', !hasFilter);
        visibleCount.textContent = filtered.length;
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

        if (paged.length === 0) {
            tbody.innerHTML = `
                <tr><td colspan="5" class="text-center py-5">
                    <i class="feather-users text-muted mb-3 d-block" style="font-size:3rem;"></i>
                    <h6 class="text-muted">No group records found</h6>
                    <p class="text-muted small mb-0">Try adjusting your filters</p>
                </td></tr>`;
            pagination.classList.add('d-none');
            return;
        }

        const statusMap = {
            active:    '<span class="badge bg-success">Active</span>',
            inactive:  '<span class="badge bg-secondary">Inactive</span>',
        };

        tbody.innerHTML = paged.map(g => {

            const editBtn = CAN_EDIT
                ? `<a href="${g.edit_url}" class="avatar-text avatar-md text-warning" title="Edit">
                       <i class="feather-edit-3"></i>
                   </a>`
                : '';

            const deleteBtn = CAN_DELETE
                ? `<form action="${g.delete_url}" method="POST" class="d-inline"
                       onsubmit="return confirm('Delete this group?')">
                       <input type="hidden" name="_token" value="${CSRF_TOKEN}">
                       <input type="hidden" name="_method" value="DELETE">
                       <button type="submit" class="avatar-text avatar-md border-0 bg-transparent text-danger" title="Delete">
                           <i class="feather-trash-2"></i>
                       </button>
                   </form>`
                : '';

            return `
                <tr>
                    <td>
                        <a href="${g.edit_url}" class="fw-medium">
                            ${esc(g.name)}
                        </a>
                    </td>
                    <td>${esc(g.strength)}</td>
                    <td class="text-muted">${esc(g.date_formatted)}</td>
                    <td>${statusMap[g.status] ?? '<span class="badge bg-secondary">' + esc(g.status) + '</span>'}</td>
                    <td>
                        <div class="hstack gap-2 justify-content-end">
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