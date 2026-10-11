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
                            <h5 class="fw-bold mb-0 text-dark">Rooms</h5>
                            <small class="text-muted">
                                Showing <span id="visibleCount">{{ $rooms->count() }}</span>
                                of {{ $rooms->count() }} rooms
                            </small>
                        </div>

                        <div class="position-relative flex-grow-1" style="max-width:380px; min-width:180px;">
                            <i class="feather-search position-absolute top-50 translate-middle-y text-muted"
                               style="font-size:13px; left:10px;"></i>
                            <input type="text" id="searchInput"
                                class="form-control form-control-sm ps-4 border-secondary-subtle"
                                placeholder="Search room name…"
                                style="border-radius:6px;">
                        </div>

                        <button type="button" id="clearAllBtn"
                            class="btn btn-outline-secondary btn-sm px-3 flex-shrink-0 d-none">
                            <i class="feather-x me-1"></i> Clear
                        </button>

                        <div class="ms-auto">
                            @if (auth()->user()->hasPermissionTo('create Room'))
                                <a href="{{ route('admin.rooms.create') }}"
                                   class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1">
                                    <i class="feather-plus" style="font-size:13px;"></i>
                                    <span class="d-none d-sm-inline">Add Room</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- QUICK FILTERS --}}
                <div class="card mx-3 mb-3">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center gap-2 flex-wrap">

                            <select id="filter_type" class="form-select form-select-sm filter-select"
                                style="width:auto; min-width:140px;">
                                <option value="">All Types</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                                @endforeach
                            </select>

                            <select id="filter_capacity" class="form-select form-select-sm filter-select"
                                style="width:auto; min-width:150px;">
                                <option value="">Any Capacity</option>
                                <option value="small">Small (≤ 30)</option>
                                <option value="medium">Medium (31–100)</option>
                                <option value="large">Large (100+)</option>
                            </select>

                            <span id="activeFiltersBadge" class="badge bg-primary rounded-pill ms-1 d-none"></span>
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
                                            <th class="fw-semibold">Capacity</th>
                                            <th class="text-center fw-semibold">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="roomTbody">
                                        {{-- Filled by JS --}}
                                    </tbody>
                                </table>
                            </div>

                            <div id="roomPagination"
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

    {{-- JSON payload --}}
    <script>
    const ALL_ROOMS  = @json($roomsJson);
    const CAN_EDIT   = {{ auth()->user()->hasPermissionTo('edit room')   ? 'true' : 'false' }};
    const CAN_DELETE = {{ auth()->user()->hasPermissionTo('Delete Room') ? 'true' : 'false' }};
    const CSRF_TOKEN = '{{ csrf_token() }}';
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        const PAGE_SIZE = 10;
        let filtered    = [...ALL_ROOMS];
        let currentPage = 1;

        const searchInput        = document.getElementById('searchInput');
        const clearAllBtn        = document.getElementById('clearAllBtn');
        const visibleCount       = document.getElementById('visibleCount');
        const activeFiltersBadge = document.getElementById('activeFiltersBadge');
        const roomTbody          = document.getElementById('roomTbody');
        const roomPagination     = document.getElementById('roomPagination');
        const paginationInfo     = document.getElementById('paginationInfo');
        const paginationBtns     = document.getElementById('paginationBtns');
        const filterSelects      = document.querySelectorAll('.filter-select');

        searchInput.addEventListener('input',  () => { currentPage = 1; applyFilters(); });
        filterSelects.forEach(sel => sel.addEventListener('change', () => { currentPage = 1; applyFilters(); }));
        clearAllBtn.addEventListener('click', clearAll);

        function applyFilters() {
            const search   = searchInput.value.trim().toLowerCase();
            const type     = document.getElementById('filter_type').value;
            const capacity = document.getElementById('filter_capacity').value;

            filtered = ALL_ROOMS.filter(r => {
                if (search && !r.name.toLowerCase().includes(search)) return false;
                if (type   && r.type !== type)                          return false;
                if (capacity === 'small'  && !(r.capacity && r.capacity <= 30))           return false;
                if (capacity === 'medium' && !(r.capacity && r.capacity >= 31 && r.capacity <= 100)) return false;
                if (capacity === 'large'  && !(r.capacity && r.capacity > 100))           return false;
                return true;
            });

            let count = (search ? 1 : 0) + (type ? 1 : 0) + (capacity ? 1 : 0);
            if (count > 0) {
                activeFiltersBadge.textContent = count + ' filter' + (count > 1 ? 's' : '') + ' active';
                activeFiltersBadge.classList.remove('d-none');
                clearAllBtn.classList.remove('d-none');
            } else {
                activeFiltersBadge.classList.add('d-none');
                clearAllBtn.classList.add('d-none');
            }

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

            if (paged.length === 0) {
                roomTbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <i class="feather-home text-muted mb-3 d-block" style="font-size:3rem;"></i>
                            <h6 class="text-muted">No rooms found</h6>
                            <p class="text-muted small mb-0">Try adjusting your filters or search criteria</p>
                        </td>
                    </tr>`;
                roomPagination.classList.add('d-none');
                return;
            }

            const typeColor = {
                classroom:  'primary',
                lab:        'info',
                auditorium: 'warning',
                library:    'secondary',
            };

            roomTbody.innerHTML = paged.map(r => {
                const badge = typeColor[r.type] || 'secondary';

                const editBtn = CAN_EDIT
                    ? `<a href="${r.edit_url}" class="avatar-text avatar-md text-warning" title="Edit">
                           <i class="feather-edit-3"></i>
                       </a>`
                    : '';

                const deleteBtn = CAN_DELETE
                    ? `<form action="${r.delete_url}" method="POST" class="d-inline"
                           onsubmit="return confirm('Delete this room?')">
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
                        <td class="fw-medium">
                            <a href="${r.edit_url}" class="text-dark text-decoration-none">${esc(r.name)}</a>
                        </td>
                        <td>
                            <span class="badge bg-${badge}">${esc(capitalize(r.type))}</span>
                        </td>
                        <td>${r.capacity ?? '–'}</td>
                        <td>
                            <div class="hstack gap-2 justify-content-center">
                                ${editBtn}
                                ${deleteBtn}
                            </div>
                        </td>
                    </tr>`;
            }).join('');

            roomPagination.classList.remove('d-none');
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

            if (totalPages <= 1) roomPagination.classList.add('d-none');
        }

        function esc(str) {
            if (str == null) return '';
            return String(str)
                .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                .replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        }
        function capitalize(str) {
            return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
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
    </style>
@endsection