@extends('admin_layout')
@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">

                {{-- HEADER --}}
                <div class="content-area-header bg-white sticky-top px-4 py-3 border-bottom shadow-sm mb-2">
                    <div class="d-flex align-items-center gap-3 flex-wrap w-100">

                        <div class="flex-shrink-0">
                            <h5 class="fw-bold mb-0 text-dark">Notice Board</h5>
                            <small class="text-muted">
                                Showing <span id="visibleCount">{{ $notices->count() }}</span>
                                of {{ $notices->count() }} notices
                            </small>
                        </div>

                        <div class="position-relative flex-grow-1" style="max-width:380px; min-width:180px;">
                            <i class="feather-search position-absolute top-50 translate-middle-y text-muted"
                               style="font-size:13px; left:10px;"></i>
                            <input type="text" id="searchInput"
                                class="form-control form-control-sm ps-4 border-secondary-subtle"
                                placeholder="Search by title…"
                                style="border-radius:6px;">
                        </div>

                        <button type="button" id="clearAllBtn"
                            class="btn btn-outline-secondary btn-sm px-3 flex-shrink-0 d-none">
                            <i class="feather-x me-1"></i> Clear
                        </button>

                        <div class="ms-auto d-flex align-items-center gap-2">

                            {{-- Date from --}}
                            <input type="date" id="filter_date_from"
                                class="form-control form-control-sm filter-input"
                                style="width:auto; min-width:140px;"
                                title="Notice date from">

                            <span class="text-muted small">to</span>

                            {{-- Date to --}}
                            <input type="date" id="filter_date_to"
                                class="form-control form-control-sm filter-input"
                                style="width:auto; min-width:140px;"
                                title="Notice date to">

                            @if (auth()->user()->hasPermissionTo('Create  Notice Board'))
                                <a href="{{ route('admin.notice_boards.create') }}"
                                   class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1">
                                    <i class="feather-plus" style="font-size:13px;"></i>
                                    <span class="d-none d-sm-inline">Add Notice</span>
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
                                            <th class="fw-semibold">#</th>
                                            <th class="fw-semibold">Title</th>
                                            <th class="fw-semibold">Notice Date</th>
                                            <th class="fw-semibold">File</th>
                                            <th class="fw-semibold">Created At</th>
                                            <th class="text-center fw-semibold">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="noticeTbody">
                                        {{-- Filled by JS --}}
                                    </tbody>
                                </table>
                            </div>

                            <div id="noticePagination"
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
    const ALL_NOTICES = @json($noticesJson);
    const CSRF_TOKEN  = '{{ csrf_token() }}';
    const CAN_EDIT    = {{ auth()->user()->hasPermissionTo('Edit  Notice Board')   ? 'true' : 'false' }};
    const CAN_DELETE  = {{ auth()->user()->hasPermissionTo('Delete  Notice Board') ? 'true' : 'false' }};
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        const PAGE_SIZE    = 25;
        let filtered       = [...ALL_NOTICES];
        let currentPage    = 1;

        const searchInput      = document.getElementById('searchInput');
        const clearAllBtn      = document.getElementById('clearAllBtn');
        const filterFrom       = document.getElementById('filter_date_from');
        const filterTo         = document.getElementById('filter_date_to');
        const visibleCount     = document.getElementById('visibleCount');
        const noticeTbody      = document.getElementById('noticeTbody');
        const noticePagination = document.getElementById('noticePagination');
        const paginationInfo   = document.getElementById('paginationInfo');
        const paginationBtns   = document.getElementById('paginationBtns');

        searchInput.addEventListener('input', () => { currentPage = 1; applyFilters(); });
        filterFrom.addEventListener('change', () => { currentPage = 1; applyFilters(); });
        filterTo.addEventListener('change',   () => { currentPage = 1; applyFilters(); });
        clearAllBtn.addEventListener('click', clearAll);

        function applyFilters() {
            const search   = searchInput.value.trim().toLowerCase();
            const dateFrom = filterFrom.value;
            const dateTo   = filterTo.value;

            filtered = ALL_NOTICES.filter(n => {
                if (search && !n.title.toLowerCase().includes(search)) return false;
                if (dateFrom && n.notice_date_raw && n.notice_date_raw < dateFrom) return false;
                if (dateTo   && n.notice_date_raw && n.notice_date_raw > dateTo)   return false;
                return true;
            });

            const hasFilter = search || dateFrom || dateTo;
            clearAllBtn.classList.toggle('d-none', !hasFilter);
            visibleCount.textContent = filtered.length;
            render();
        }

        function clearAll() {
            searchInput.value = '';
            filterFrom.value  = '';
            filterTo.value    = '';
            currentPage = 1;
            applyFilters();
        }

        function render() {
            const totalPages = Math.max(1, Math.ceil(filtered.length / PAGE_SIZE));
            if (currentPage > totalPages) currentPage = totalPages;
            const start = (currentPage - 1) * PAGE_SIZE;
            const paged = filtered.slice(start, start + PAGE_SIZE);

            if (paged.length === 0) {
                noticeTbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="feather-bell-off text-muted mb-3 d-block" style="font-size:3rem;"></i>
                            <h6 class="text-muted">No notices found</h6>
                            <p class="text-muted small mb-0">Try a different search term or date range</p>
                        </td>
                    </tr>`;
                noticePagination.classList.add('d-none');
                return;
            }

            noticeTbody.innerHTML = paged.map((n, i) => {
                const fileBtn = n.file_url
                    ? `<a href="${n.file_url}" target="_blank"
                           class="avatar-text avatar-md text-danger" title="View File">
                           <i class="feather-file-text"></i>
                       </a>`
                    : `<span class="text-muted">–</span>`;

                const editBtn = CAN_EDIT
                    ? `<a href="${n.edit_url}" class="avatar-text avatar-md text-warning" title="Edit">
                           <i class="feather-edit-3"></i>
                       </a>`
                    : '';

                const deleteBtn = CAN_DELETE
                    ? `<form action="${n.delete_url}" method="POST" class="d-inline"
                             onsubmit="return confirm('Delete this notice?')">
                           <input type="hidden" name="_token" value="${CSRF_TOKEN}">
                           <input type="hidden" name="_method" value="DELETE">
                           <button type="submit"
                               class="avatar-text avatar-md border-0 bg-transparent text-danger"
                               title="Delete">
                               <i class="feather-trash-2"></i>
                           </button>
                       </form>`
                    : '';

                return `
                    <tr>
                        <td>${start + i + 1}</td>
                        <td class="fw-medium">
                            <a href="${n.edit_url}" class="text-dark text-decoration-none">${esc(n.title)}</a>
                        </td>
                        <td>${esc(n.notice_date)}</td>
                        <td>${fileBtn}</td>
                        <td>${esc(n.created_at)}</td>
                        <td>
                            <div class="hstack gap-2 justify-content-center">
                                ${editBtn}
                                ${deleteBtn}
                            </div>
                        </td>
                    </tr>`;
            }).join('');

            noticePagination.classList.remove('d-none');
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

            if (totalPages <= 1) noticePagination.classList.add('d-none');
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
        .filter-input { border-radius:6px; font-size:0.8125rem; border-color:#dee2e6; }
        .filter-input:focus { border-color:#0d6efd; box-shadow:0 0 0 2px rgba(13,110,253,.15); }
        .table th { white-space:nowrap; font-size:0.8125rem; padding:0.75rem 1rem; }
        .table td { padding:0.75rem 1rem; vertical-align:middle; font-size:0.875rem; }
    </style>
@endsection