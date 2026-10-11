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
                            <h5 class="fw-bold mb-0 text-dark">Academic Years</h5>
                            <small class="text-muted">
                                Showing <span id="visibleCount">{{ $academicYears->count() }}</span>
                                of {{ $academicYears->count() }} records
                            </small>
                        </div>

                        <div class="position-relative flex-grow-1" style="max-width:380px; min-width:180px;">
                            <i class="feather-search position-absolute top-50 translate-middle-y text-muted"
                                style="font-size:13px; left:10px;"></i>
                            <input type="text" id="searchInput"
                                class="form-control form-control-sm ps-4 border-secondary-subtle"
                                placeholder="Search by name…" style="border-radius:6px;">
                        </div>

                        <button type="button" id="clearAllBtn"
                            class="btn btn-outline-secondary btn-sm px-3 flex-shrink-0 d-none">
                            <i class="feather-x me-1"></i> Clear
                        </button>

                        <div class="ms-auto">
                            @if (auth()->user()->hasPermissionTo('Create Academic Year'))
                                <a href="{{ route('admin.academic_years.create') }}"
                                    class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1">
                                    <i class="feather-plus" style="font-size:13px;"></i>
                                    <span class="d-none d-sm-inline">Add Academic Year</span>
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
                                            <th class="fw-semibold">Start Year</th>
                                            <th class="fw-semibold">End Year</th>
                                            <th class="text-center fw-semibold">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ayTbody">
                                        {{-- Filled by JS --}}
                                    </tbody>
                                </table>
                            </div>

                            <div id="ayPagination"
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
        const ALL_AY = @json($academicYearsJson);
        const CAN_EDIT = {{ auth()->user()->hasPermissionTo('Edit Academic Year') ? 'true' : 'false' }};
        const CAN_DELETE = {{ auth()->user()->hasPermissionTo('Delete Academic Year') ? 'true' : 'false' }};
        const CSRF_TOKEN = '{{ csrf_token() }}';
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const PAGE_SIZE = 10;
            let filtered = [...ALL_AY];
            let currentPage = 1;

            const searchInput = document.getElementById('searchInput');
            const clearAllBtn = document.getElementById('clearAllBtn');
            const visibleCount = document.getElementById('visibleCount');
            const ayTbody = document.getElementById('ayTbody');
            const ayPagination = document.getElementById('ayPagination');
            const paginationInfo = document.getElementById('paginationInfo');
            const paginationBtns = document.getElementById('paginationBtns');

            searchInput.addEventListener('input', () => {
                currentPage = 1;
                applyFilters();
            });
            clearAllBtn.addEventListener('click', clearAll);

            function applyFilters() {
                const search = searchInput.value.trim().toLowerCase();

                filtered = ALL_AY.filter(ay => {
                    if (search && !ay.name.toLowerCase().includes(search)) return false;
                    return true;
                });

                if (search) {
                    clearAllBtn.classList.remove('d-none');
                } else {
                    clearAllBtn.classList.add('d-none');
                }

                visibleCount.textContent = filtered.length;
                render();
            }

            function clearAll() {
                searchInput.value = '';
                currentPage = 1;
                applyFilters();
            }

            function render() {
                const totalPages = Math.max(1, Math.ceil(filtered.length / PAGE_SIZE));
                if (currentPage > totalPages) currentPage = totalPages;
                const start = (currentPage - 1) * PAGE_SIZE;
                const paged = filtered.slice(start, start + PAGE_SIZE);

                if (paged.length === 0) {
                    ayTbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="feather-calendar text-muted mb-3 d-block" style="font-size:3rem;"></i>
                            <h6 class="text-muted">No academic years found</h6>
                            <p class="text-muted small mb-0">Try a different search term</p>
                        </td>
                    </tr>`;
                    ayPagination.classList.add('d-none');
                    return;
                }

                ayTbody.innerHTML = paged.map(ay => {
                   

                    const editBtn = CAN_EDIT ?
                        `<a href="${ay.edit_url}" class="avatar-text avatar-md text-warning" title="Edit">
                           <i class="feather-edit-3"></i>
                       </a>` :
                        '';

                    const deleteBtn = CAN_DELETE ?
                        `<form action="${ay.delete_url}" method="POST" class="d-inline"
                           onsubmit="return confirm('Delete this academic year?')">
                           <input type="hidden" name="_token" value="${CSRF_TOKEN}">
                           <input type="hidden" name="_method" value="DELETE">
                           <button type="submit"
                               class="avatar-text avatar-md border-0 bg-transparent text-danger"
                               title="Delete">
                               <i class="feather-trash-2"></i>
                           </button>
                       </form>` :
                        '';

                    return `
                    <tr>
                        <td class="fw-medium">
                            <a href="${ay.edit_url}" class="text-dark text-decoration-none">${esc(ay.name)}</a>
                        </td>
                        <td>${esc(ay.start_year)}</td>
                        <td>${esc(ay.end_year)}</td>
                        <td>
                            <div class="hstack gap-2 justify-content-center">
                                ${editBtn}
                                ${deleteBtn}
                            </div>
                        </td>
                    </tr>`;
                }).join('');

                ayPagination.classList.remove('d-none');
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
                    btn.addEventListener('click', function() {
                        currentPage = parseInt(this.dataset.page);
                        render();
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    });
                });

                if (totalPages <= 1) ayPagination.classList.add('d-none');
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
    </style>
@endsection
