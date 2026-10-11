@extends('admin_layout')
@section('content')

<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Academics.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            <!-- HEADER -->
            <div class="content-area-header bg-white sticky-top px-4 py-3 border-bottom shadow-sm mb-2">
                <div class="d-flex align-items-center gap-3 flex-wrap w-100">
                    <div class="flex-shrink-0">
                        <h5 class="fw-bold mb-0 text-dark">Staff List</h5>
                        <small class="text-muted">
                            Showing <span id="visibleCount">{{ $staff->count() }}</span>
                            of {{ $staff->count() }} staff
                        </small>
                    </div>

                    <div class="position-relative flex-grow-1" style="max-width:380px; min-width:180px;">
                        <i class="feather-search position-absolute top-50 translate-middle-y text-muted"
                           style="font-size:13px; left:10px;"></i>
                        <input type="text" id="searchInput"
                            class="form-control form-control-sm ps-4 border-secondary-subtle"
                            placeholder="Search by name or phone…"
                            style="border-radius:6px;">
                    </div>

                    <button type="button" id="clearAllBtn"
                        class="btn btn-outline-secondary btn-sm px-3 flex-shrink-0 d-none">
                        <i class="feather-x me-1"></i> Clear
                    </button>

                    <div class="ms-auto d-flex align-items-center gap-2 flex-shrink-0">
                        {{-- Department filter --}}
                        <select id="filter_department" class="form-select form-select-sm filter-select"
                            style="width:auto; min-width:140px;">
                            <option value="">All Departments</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>

                        {{-- Designation filter --}}
                        <select id="filter_designation" class="form-select form-select-sm filter-select"
                            style="width:auto; min-width:140px;">
                            <option value="">All Designations</option>
                            @foreach ($designations as $desig)
                                <option value="{{ $desig->id }}">{{ $desig->name }}</option>
                            @endforeach
                        </select>

                        {{-- Status filter --}}
                        <select id="filter_status" class="form-select form-select-sm filter-select"
                            style="width:auto; min-width:120px;">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="resigned">Resigned</option>
                        </select>

                        <div class="vr mx-1" style="height:24px;"></div>

                        {{-- View toggle --}}
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-secondary view-toggle"
                                data-view="list" title="List View">
                                <i class="feather-list"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary view-toggle"
                                data-view="grid" title="Grid View">
                                <i class="feather-grid"></i>
                            </button>
                        </div>

                        @if (auth()->user()->hasPermissionTo('Create  Staff'))
                            <a href="{{ route('admin.staff.create') }}"
                               class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1">
                                <i class="feather-plus" style="font-size:13px;"></i>
                                <span>Add Staff</span>
                            </a>
                        @endif
                        <a href="{{ route('admin.staff.import.show') }}" class="btn btn-outline-primary">
                            <i class="feather-upload-cloud me-2"></i>
                            <span>Import Excel</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="content-area-body">
                @include('elements.flash')

                <div class="card stretch stretch-full">
                    <div class="card-body p-0">

                        <!-- ================= LIST VIEW ================= -->
                        <div id="list-view" class="view-mode d-none">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="fw-semibold">Name</th>
                                            <th class="fw-semibold">Designation</th>
                                            <th class="fw-semibold">Department</th>
                                            <th class="fw-semibold">Phone</th>
                                            <th class="fw-semibold">Status</th>
                                            <th class="text-center fw-semibold">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="listTbody">
                                        <!-- Dynamic content -->
                                    </tbody>
                                </table>
                            </div>
                            <div id="listPagination" class="p-3 border-top d-flex justify-content-between align-items-center gap-2 flex-wrap d-none">
                                <small class="text-muted" id="listPaginationInfo"></small>
                                <div class="d-flex gap-1" id="listPaginationBtns"></div>
                            </div>
                        </div>

                        <!-- ================= GRID VIEW ================= -->
                        <div id="grid-view" class="view-mode">
                            <div class="p-4">
                                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="gridContainer">
                                    <!-- Dynamic content -->
                                </div>
                                <div id="gridPagination" class="d-flex justify-content-center mt-4 gap-1 d-none"></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@php
    $staffData = [];
    foreach($staff as $s) {
        $staffData[] = [
            'id'               => $s->id,
            'full_name'        => $s->first_name . ' ' . $s->last_name,
            'first_name'       => $s->first_name,
            'last_name'        => $s->last_name,
            'phone'            => $s->phone,
            'email'            => $s->email,
            'status'           => $s->status,
            'department_id'    => $s->department_id,
            'department_name'  => $s->department?->name ?? 'N/A',
            'designation_id'   => $s->designation_id,
            'designation_name' => $s->designation?->name ?? 'N/A',
            'photo_url'        => $s->photo_url ? asset('public/storage/'.$s->photo_url) : null,
            'show_url'         => route('admin.staff.show', $s->id),
            'edit_url'         => route('admin.staff.edit', $s->id),
            'delete_url'       => route('admin.staff.destroy', $s->id),
        ];
    }
@endphp

<script>
// Pass data from PHP to JavaScript
const ALL_STAFF   = @json($staffData);
const CSRF_TOKEN  = '{{ csrf_token() }}';
const CAN_EDIT    = {{ auth()->user()->hasPermissionTo('Edit  Staff')   ? 'true' : 'false' }};
const CAN_DELETE  = {{ auth()->user()->hasPermissionTo('Delete  Staff') ? 'true' : 'false' }};
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const PAGE_SIZE = 12;
    let filtered    = [...ALL_STAFF];
    let currentPage = 1;
    let currentView = sessionStorage.getItem('staffViewMode') || 'list';

    const searchInput       = document.getElementById('searchInput');
    const clearAllBtn       = document.getElementById('clearAllBtn');
    const visibleCount      = document.getElementById('visibleCount');
    const filterDepartment  = document.getElementById('filter_department');
    const filterDesignation = document.getElementById('filter_designation');
    const filterStatus      = document.getElementById('filter_status');
    const filterSelects     = document.querySelectorAll('.filter-select');
    const toggleBtns        = document.querySelectorAll('.view-toggle');

    // ── View toggle ──────────────────────────────────────────────
    setView(currentView);
    toggleBtns.forEach(btn => btn.addEventListener('click', () => setView(btn.dataset.view)));

    function setView(view) {
        currentView = view;
        toggleBtns.forEach(b => b.classList.remove('active'));
        const activeBtn = document.querySelector(`[data-view="${view}"]`);
        if (activeBtn) activeBtn.classList.add('active');

        document.getElementById('list-view').classList.toggle('d-none', view !== 'list');
        document.getElementById('grid-view').classList.toggle('d-none', view !== 'grid');

        sessionStorage.setItem('staffViewMode', view);
        render();
    }

    // ── Event listeners ──────────────────────────────────────────
    searchInput.addEventListener('input', () => { currentPage = 1; applyFilters(); });
    filterSelects.forEach(sel => sel.addEventListener('change', () => { currentPage = 1; applyFilters(); }));
    clearAllBtn.addEventListener('click', clearAll);

    // ── Filter logic ─────────────────────────────────────────────
    function applyFilters() {
        const search      = searchInput.value.trim().toLowerCase();
        const department  = filterDepartment.value;
        const designation = filterDesignation.value;
        const status      = filterStatus.value;

        filtered = ALL_STAFF.filter(s => {
            if (search && !(
                s.full_name.toLowerCase().includes(search) ||
                (s.phone && s.phone.toLowerCase().includes(search))
            )) return false;
            if (department  && String(s.department_id)  !== department)  return false;
            if (designation && String(s.designation_id) !== designation) return false;
            if (status      && s.status !== status)                      return false;
            return true;
        });

        const hasFilter = search || department || designation || status;
        clearAllBtn.classList.toggle('d-none', !hasFilter);
        visibleCount.textContent = filtered.length;
        currentPage = 1;
        render();
    }

    function clearAll() {
        searchInput.value = '';
        filterSelects.forEach(sel => sel.value = '');
        currentPage = 1;
        applyFilters();
    }

    // ── Render dispatcher ────────────────────────────────────────
    function render() {
        const totalPages = Math.max(1, Math.ceil(filtered.length / PAGE_SIZE));
        if (currentPage > totalPages) currentPage = totalPages;
        const start = (currentPage - 1) * PAGE_SIZE;
        const paged = filtered.slice(start, start + PAGE_SIZE);

        if (currentView === 'list') {
            renderList(paged, start);
        } else {
            renderGrid(paged, start);
        }

        renderPagination(totalPages);
    }

    // ── List View ────────────────────────────────────────────────
    function renderList(paged, start) {
        const tbody         = document.getElementById('listTbody');
        const paginationDiv = document.getElementById('listPagination');

        if (paged.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <i class="feather-users text-muted mb-3 d-block" style="font-size:3rem;"></i>
                        <h6 class="text-muted">No staff found</h6>
                        <p class="text-muted small mb-0">Try adjusting your filters</p>
                    </td>
                </tr>`;
            paginationDiv.classList.add('d-none');
            return;
        }

        tbody.innerHTML = paged.map(s => {
            const avatar = s.photo_url
                ? `<img src="${s.photo_url}" class="rounded-circle me-2" width="32" height="32" style="object-fit:cover;">`
                : `<div class="rounded-circle text-white d-inline-flex align-items-center justify-content-center fw-bold me-2"
                        style="width:32px;height:32px;font-size:0.75rem;background:linear-gradient(135deg,#667eea,#764ba2);">
                        ${escapeHtml((s.first_name?.charAt(0) || '') + (s.last_name?.charAt(0) || '')).toUpperCase()}
                    </div>`;

            const statusColor = s.status === 'active' ? 'success' : 'warning';
            const statusText  = s.status === 'active' ? 'Active'  : 'Resigned';

            // ── ACTION BUTTONS WITH PERMISSION CHECK ──
            const editBtn = CAN_EDIT
                ? `<a href="${s.edit_url}" class="text-warning" title="Edit">
                       <i class="feather-edit-3"></i>
                   </a>`
                : '';

            const deleteBtn = CAN_DELETE
                ? `<form action="${s.delete_url}" method="POST" class="d-inline"
                         onsubmit="return confirm('Are you sure you want to delete ${escapeHtml(s.full_name)}?');">
                       <input type="hidden" name="_token" value="${CSRF_TOKEN}">
                       <input type="hidden" name="_method" value="DELETE">
                       <button type="submit" class="border-0 bg-transparent text-danger p-0" title="Delete" style="cursor:pointer;">
                           <i class="feather-trash-2"></i>
                       </button>
                   </form>`
                : '';

            return `
                <tr>
                    <td>
                        <a href="${s.show_url}" class="text-decoration-none text-dark">
                            <div class="d-flex align-items-center">
                                ${avatar}
                                <span class="fw-medium">${escapeHtml(s.full_name)}</span>
                            </div>
                        </a>
                    </td>
                    <td>${escapeHtml(s.designation_name)}</td>
                    <td>${escapeHtml(s.department_name)}</td>
                    <td>${escapeHtml(s.phone)}</td>
                    <td><span class="badge bg-${statusColor}">${statusText}</span></td>
                    <td class="text-center">
                        <div class="d-flex gap-2 justify-content-center">
                            <a href="${s.show_url}" class="text-info" title="View">
                                <i class="feather-eye"></i>
                            </a>
                            ${editBtn}
                            ${deleteBtn}
                        </div>
                    </td>
                </tr>`;
        }).join('');

        paginationDiv.classList.remove('d-none');
        document.getElementById('listPaginationInfo').textContent =
            `Showing ${start + 1}–${Math.min(start + paged.length, filtered.length)} of ${filtered.length}`;
    }

    // ── Grid View ────────────────────────────────────────────────
    function renderGrid(paged, start) {
        const container     = document.getElementById('gridContainer');
        const paginationDiv = document.getElementById('gridPagination');

        if (paged.length === 0) {
            container.innerHTML = `
                <div class="col-12 text-center py-5">
                    <i class="feather-users text-muted mb-3 d-block" style="font-size:3rem;"></i>
                    <h5 class="fw-semibold text-muted mb-2">No Staff Found</h5>
                    <p class="text-muted mb-0">Try adjusting your filters.</p>
                </div>`;
            paginationDiv.classList.add('d-none');
            return;
        }

        container.innerHTML = paged.map(s => {
            const avatar = s.photo_url
                ? `<img src="${s.photo_url}" class="rounded-3 shadow-sm" width="64" height="64" style="object-fit:cover;">`
                : `<div class="rounded-3 text-white d-flex align-items-center justify-content-center fw-bold"
                        style="width:64px;height:64px;background:linear-gradient(135deg,#667eea,#764ba2);">
                        ${escapeHtml((s.first_name?.charAt(0) || '') + (s.last_name?.charAt(0) || '')).toUpperCase()}
                    </div>`;

            const statusColor = s.status === 'active' ? 'success' : 'warning';
            const statusText  = s.status === 'active' ? 'Active'  : 'Resigned';

            // ── ACTION BUTTONS WITH PERMISSION CHECK ──
            // Determine width class based on how many buttons are shown
            const bothVisible  = CAN_EDIT && CAN_DELETE;
            const btnWidthClass = bothVisible ? 'w-50' : 'w-100';

            const editBtn = CAN_EDIT
                ? `<a href="${s.edit_url}" class="btn btn-outline-secondary btn-sm ${btnWidthClass}">Edit</a>`
                : '';

            const deleteBtn = CAN_DELETE
                ? `<form action="${s.delete_url}" method="POST" class="${btnWidthClass}"
                         onsubmit="return confirm('Delete ${escapeHtml(s.full_name)}?');">
                       <input type="hidden" name="_token" value="${CSRF_TOKEN}">
                       <input type="hidden" name="_method" value="DELETE">
                       <button type="submit" class="btn btn-outline-danger btn-sm w-100">Delete</button>
                   </form>`
                : '';

            // Only show the bottom action row if at least one button is visible
            const actionRow = (CAN_EDIT || CAN_DELETE)
                ? `<div class="d-flex gap-2">${editBtn}${deleteBtn}</div>`
                : '';

            return `
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm student-card">
                        <div class="card-header bg-white p-4 pb-2">
                            <div class="d-flex gap-3">
                                <div class="flex-shrink-0">${avatar}</div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-1">${escapeHtml(s.full_name)}</h6>
                                    <span class="badge bg-light text-dark">${escapeHtml(s.designation_name)}</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4 pt-2">
                            <div class="mb-2">
                                <small class="text-muted">Department</small>
                                <div class="fw-semibold">${escapeHtml(s.department_name)}</div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Phone</small>
                                <div class="fw-semibold">${escapeHtml(s.phone) || 'N/A'}</div>
                            </div>
                            <span class="badge bg-${statusColor}">${statusText}</span>
                        </div>
                        <div class="card-footer bg-white border-top p-3">
                            <div class="d-grid gap-2">
                                <a href="${s.show_url}" class="btn btn-primary btn-sm fw-semibold">
                                    <i class="feather-eye me-1"></i>View Profile
                                </a>
                                ${actionRow}
                            </div>
                        </div>
                    </div>
                </div>`;
        }).join('');

        paginationDiv.classList.remove('d-none');
    }

    // ── Pagination ───────────────────────────────────────────────
    function renderPagination(totalPages) {
        const makeBtn = (label, page, disabled = false, active = false) =>
            `<button class="btn btn-sm ${active ? 'btn-primary' : 'btn-outline-secondary'}"
                ${disabled ? 'disabled' : ''} data-page="${page}">${label}</button>`;

        let html = makeBtn('«', currentPage - 1, currentPage === 1);

        for (let p = 1; p <= totalPages; p++) {
            if (totalPages <= 7 || p === 1 || p === totalPages || Math.abs(p - currentPage) <= 1) {
                html += makeBtn(p, p, false, p === currentPage);
            } else if (Math.abs(p - currentPage) === 2) {
                html += `<button class="btn btn-sm btn-outline-secondary" disabled>…</button>`;
            }
        }

        html += makeBtn('»', currentPage + 1, currentPage === totalPages);

        const listBtns = document.getElementById('listPaginationBtns');
        const gridBtns = document.getElementById('gridPagination');

        [listBtns, gridBtns].forEach(container => {
            if (!container) return;
            container.innerHTML = html;
            container.querySelectorAll('[data-page]').forEach(btn => {
                btn.addEventListener('click', e => {
                    e.preventDefault();
                    currentPage = parseInt(btn.dataset.page);
                    render();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            });
        });

        const listPagination = document.getElementById('listPagination');
        const gridPagination = document.getElementById('gridPagination');

        if (totalPages <= 1) {
            listPagination?.classList.add('d-none');
            gridPagination?.classList.add('d-none');
        } else {
            if (currentView === 'list') listPagination?.classList.remove('d-none');
            if (currentView === 'grid') gridPagination?.classList.remove('d-none');
        }
    }

    // ── Helper ───────────────────────────────────────────────────
    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    // ── Init ─────────────────────────────────────────────────────
    applyFilters();
});
</script>

<style>
    .filter-select {
        border-radius: 6px;
        font-size: 0.8125rem;
        border-color: #dee2e6;
        cursor: pointer;
        transition: all .2s;
    }
    .filter-select:hover {
        border-color: #0d6efd;
        background-color: #f8f9fa;
    }
    .filter-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 2px rgba(13,110,253,.15);
    }
    .table th {
        white-space: nowrap;
        font-size: 0.8125rem;
        padding: 0.75rem 1rem;
    }
    .table td {
        padding: 0.75rem 1rem;
        vertical-align: middle;
        font-size: 0.875rem;
    }
    .student-card {
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .student-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,.1) !important;
    }
    .view-toggle.active {
        background-color: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }
    .view-toggle.active i {
        color: white;
    }
    .btn-group .btn {
        transition: all 0.2s;
    }
</style>

@endsection