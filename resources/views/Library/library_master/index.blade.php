@extends('admin_layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Library.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            {{-- HEADER --}}
            <div class="content-area-header bg-white sticky-top px-4 py-3 border-bottom shadow-sm mb-2">
                <div class="d-flex align-items-center gap-3 flex-wrap w-100">

                    <div class="flex-shrink-0">
                        <h5 class="fw-bold mb-0 text-dark">Library Master List</h5>
                        <small class="text-muted">
                            Showing <span id="visibleCount">{{ $library_master->count() }}</span>
                            of {{ $library_master->count() }} records
                        </small>
                    </div>

                    {{-- Search by Room Name --}}
                    <div class="position-relative flex-grow-1" style="max-width:380px; min-width:180px;">
                        <i class="feather-search position-absolute top-50 translate-middle-y text-muted"
                           style="font-size:13px; left:10px;"></i>
                        <input type="text" id="searchRoom"
                            class="form-control form-control-sm ps-4 border-secondary-subtle"
                            placeholder="Search by room name…"
                            style="border-radius:6px;">
                    </div>

                    <button type="button" id="clearAllBtn"
                        class="btn btn-outline-secondary btn-sm px-3 flex-shrink-0 d-none">
                        <i class="feather-x me-1"></i> Clear
                    </button>

                    <div class="ms-auto d-flex align-items-center gap-2">
                        @if(auth()->user()->hasPermissionTo('Create Master Data'))
                            <a href="{{ route('admin.library_master.create') }}"
                               class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1">
                                <i class="feather-plus" style="font-size:13px;"></i>
                                <span class="d-none d-sm-inline">Add Master Data</span>
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
                                        <th class="fw-semibold">Name</th>
                                        <th class="fw-semibold">Type</th>
                                        <th class="fw-semibold">Parent</th>
                                        <th class="fw-semibold">Status</th>
                                        <th class="fw-semibold">Created At</th>
                                        <th class="text-end fw-semibold">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="lmTbody">
                                    {{-- Filled by JS --}}
                                </tbody>
                            </table>
                        </div>

                        <div id="lmPagination"
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
const ALL_MASTERS = @json($libraryMasterJson);
const CSRF_TOKEN  = '{{ csrf_token() }}';

// Permissions passed to JS
const CAN_EDIT   = {{ auth()->user()->hasPermissionTo('Edit Master Data') ? 'true' : 'false' }};
const CAN_DELETE = {{ auth()->user()->hasPermissionTo('Delete Master Data') ? 'true' : 'false' }};
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const PAGE_SIZE      = 10;
    let filtered         = [...ALL_MASTERS];
    let currentPage      = 1;

    const searchRoom     = document.getElementById('searchRoom');
    const clearAllBtn    = document.getElementById('clearAllBtn');
    const visibleCount   = document.getElementById('visibleCount');
    const lmTbody        = document.getElementById('lmTbody');
    const lmPagination   = document.getElementById('lmPagination');
    const paginationInfo = document.getElementById('paginationInfo');
    const paginationBtns = document.getElementById('paginationBtns');

    searchRoom.addEventListener('input', () => { currentPage = 1; applyFilters(); });
    clearAllBtn.addEventListener('click', clearAll);

    function applyFilters() {
        const search = searchRoom.value.trim().toLowerCase();

        filtered = ALL_MASTERS.filter(m => {
            if (!search) return true;

            // Room: apna naam match karo
            if (m.type === '1') return m.name.toLowerCase().includes(search);

            // Row: parent_name = Room ka naam
            if (m.type === '2') return m.parent_name.toLowerCase().includes(search);

            // Shelf: parent_sub = Room ka naam (grandparent)
            if (m.type === '3') return m.parent_sub.toLowerCase().includes(search);

            return false;
        });

        const hasFilter = !!search;
        clearAllBtn.classList.toggle('d-none', !hasFilter);
        visibleCount.textContent = filtered.length;
        render();
    }

    function clearAll() {
        searchRoom.value = '';
        currentPage = 1;
        applyFilters();
    }

    function render() {
        const totalPages = Math.max(1, Math.ceil(filtered.length / PAGE_SIZE));
        if (currentPage > totalPages) currentPage = totalPages;
        const start = (currentPage - 1) * PAGE_SIZE;
        const paged = filtered.slice(start, start + PAGE_SIZE);

        if (paged.length === 0) {
            lmTbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <i class="feather-layers text-muted mb-3 d-block" style="font-size:3rem;"></i>
                        <h6 class="text-muted">No records found</h6>
                        <p class="text-muted small mb-0">Try a different search term</p>
                    </td>
                </tr>`;
            lmPagination.classList.add('d-none');
            return;
        }

        lmTbody.innerHTML = paged.map(m => {
            // Build actions HTML based on permissions
            let actionsHtml = '<div class="hstack gap-2 justify-content-end">';

            if (CAN_EDIT) {
                actionsHtml += `
                    <a href="${m.edit_url}" class="avatar-text avatar-md" title="Edit">
                        <i class="feather-edit-3"></i>
                    </a>
                `;
            }

            if (CAN_DELETE) {
                actionsHtml += `
                    <form action="${m.delete_url}" method="POST" class="d-inline"
                          onsubmit="return confirm('Are you sure you want to delete this record?')">
                        <input type="hidden" name="_token" value="${CSRF_TOKEN}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit"
                            class="avatar-text avatar-md border-0 bg-transparent text-danger"
                            title="Delete">
                            <i class="feather-trash-2"></i>
                        </button>
                    </form>
                `;
            }

            actionsHtml += '</div>';

            // If both actions are hidden, show an empty cell with a dash
            if (!CAN_EDIT && !CAN_DELETE) {
                actionsHtml = '<span class="text-muted">—</span>';
            }

            // Name column: keep as link even if edit permission is missing (UI unchanged)
            // But we remove the link if user cannot edit? The original always had an <a>.
            // For consistency with other modules, we keep the link because edit page might still be accessible by URL.
            // The permission only hides the edit icon.
            const nameHtml = `<a href="${m.edit_url}" class="text-dark text-decoration-none">${esc(m.name)}</a>`;

            return `
                <tr>
                    <td class="fw-medium">${nameHtml}</td>
                    <td><span class="badge bg-${m.type_badge}">${esc(m.type_label)}</span></td>
                    <td>
                        ${esc(m.parent_name)}
                        ${m.parent_sub ? `<small class="text-muted d-block">${esc(m.parent_sub)}</small>` : ''}
                    </td>
                    <td><span class="badge bg-${m.status_badge}">${esc(m.status_label)}</span></td>
                    <td>${esc(m.created_at)}</td>
                    <td class="text-end">${actionsHtml}</td>
                </tr>
            `;
        }).join('');

        lmPagination.classList.remove('d-none');
        paginationInfo.textContent =
            `Showing ${start + 1}–${Math.min(start + paged.length, filtered.length)} of ${filtered.length}`;
        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
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

        paginationBtns.innerHTML = html;
        paginationBtns.querySelectorAll('[data-page]').forEach(btn => {
            btn.addEventListener('click', function () {
                currentPage = parseInt(this.dataset.page);
                render();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });

        if (totalPages <= 1) lmPagination.classList.add('d-none');
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
    .table th { white-space:nowrap; font-size:0.8125rem; padding:0.75rem 1rem; }
    .table td { padding:0.75rem 1rem; vertical-align:middle; font-size:0.875rem; }
</style>
@endsection