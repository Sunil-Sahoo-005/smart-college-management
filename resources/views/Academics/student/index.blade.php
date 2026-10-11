@extends('admin_layout')
@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">

                {{-- ════════════════════════════════════════════════════════
                     HEADER  –  title + search + action buttons
                ════════════════════════════════════════════════════════ --}}
                <div class="content-area-header bg-white sticky-top px-4 py-3 border-bottom shadow-sm mb-2">
                    <div class="d-flex align-items-center gap-3 flex-wrap">

                        {{-- Title --}}
                        <div class="flex-shrink-0">
                            <h5 class="fw-bold mb-0 text-dark">Students</h5>
                            <small class="text-muted">
                                Showing <span id="visibleCount">{{ $students->count() }}</span>
                                of {{ $students->count() }} students
                            </small>
                        </div>

                        {{-- Search --}}
                        

                        {{-- Clear all --}}
                        <button type="button" id="clearAllBtn"
                            class="btn btn-outline-secondary btn-sm px-3 flex-shrink-0 d-none">
                            <i class="feather-x me-1"></i> Clear
                        </button>

                        {{-- Divider --}}
                        <div class="vr mx-1 d-none d-md-block" style="height:24px;"></div>

                        {{-- Action Buttons --}}
                        <div class="d-flex align-items-center gap-2 flex-shrink-0 ms-auto">

                            @if (auth()->user()->hasPermissionTo('Create Student'))
                                <a href="{{ route('admin.students.create') }}"
                                    class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1">
                                    <i class="feather-user-plus" style="font-size:13px;"></i>
                                    <span class="d-none d-sm-inline">Add Student</span>
                                </a>

                                {{-- <button type="button"
                                    class="btn btn-outline-success btn-sm d-inline-flex align-items-center gap-1"
                                    data-bs-toggle="modal" data-bs-target="#importStudentsModal">
                                    <i class="feather-upload" style="font-size:13px;"></i>
                                    <span class="d-none d-sm-inline">Import</span>
                                </button> --}}
                                <a href="{{ route('admin.students.import.show') }}"
        class="btn btn-outline-success btn-sm d-inline-flex align-items-center gap-1">
         <i class="feather-upload" style="font-size:13px;"></i>
         <span class="d-none d-sm-inline">Import</span>
     </a>

                                {{-- Export still needs a real form POST since it returns a file --}}
                                <form method="GET" action="{{ route('admin.students.index') }}" id="exportForm">
                                    <input type="hidden" name="export" value="excel">
                                    <button type="submit"
                                        class="btn btn-success btn-sm d-inline-flex align-items-center gap-1"
                                        {{ $students->count() == 0 ? 'disabled' : '' }}
                                        title="{{ $students->count() == 0 ? 'No students to export' : 'Export all students' }}">
                                        <i class="feather-download" style="font-size:13px;"></i>
                                        <span class="d-none d-sm-inline">Export</span>
                                    </button>
                                </form>
                            @endif

                            <div class="vr mx-1" style="height:24px;"></div>

                            {{-- View toggle --}}
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-secondary view-toggle active"
                                    data-view="list" title="List view">
                                    <i class="feather-list"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary view-toggle"
                                    data-view="grid" title="Grid view">
                                    <i class="feather-grid"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ════════════════════════════════════════════════════════
                     QUICK FILTERS ROW  (all client-side)
                ════════════════════════════════════════════════════════ --}}
                <div class="card mx-3 mb-3">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                 <div class="position-relative flex-grow-1" style="max-width:440px; min-width:200px;">
                                    <i class="feather-search position-absolute top-50 translate-middle-y text-muted"
                                        style="font-size:13px; left:10px;"></i>
                                    <input type="text" id="searchInput"
                                        class="form-control form-control-sm ps-4 border-secondary-subtle"
                                        placeholder="Search name, course, admission #, roll #..."
                                        style="border-radius:6px;">
                                </div>
                            {{-- Academic Year --}}
                            <select id="filter_academic_year" class="form-select form-select-sm filter-select"
                                style="width:auto; min-width:150px;">
                                <option value="">All Years</option>
                                @foreach ($academicYears as $year)
                                    <option value="{{ $year->id }}">{{ $year->name }}</option>
                                @endforeach
                            </select>

                            {{-- Course --}}
                            <select id="filter_course" class="form-select form-select-sm filter-select"
                                style="width:auto; min-width:140px;">
                                <option value="">All Courses</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                                @endforeach
                            </select>

                            {{-- Stream/Branch --}}
                            <select id="filter_stream" class="form-select form-select-sm filter-select"
                                style="width:auto; min-width:140px;">
                                <option value="">All Branches</option>
                                @foreach ($streams as $stream)
                                    <option value="{{ $stream->id }}">{{ $stream->name }}</option>
                                @endforeach
                            </select>

                            {{-- Semester/Class --}}
                            <select id="filter_class" class="form-select form-select-sm filter-select"
                                style="width:auto; min-width:130px;">
                                <option value="">All Semesters</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>

                            {{-- Student Type --}}
                            <select id="filter_student_type" class="form-select form-select-sm filter-select"
                                style="width:auto; min-width:130px;">
                                <option value="">All Types</option>
                                @foreach ($studentTypes as $type)
                                    <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                                @endforeach
                            </select>

                            {{-- Residence Type --}}
                            <select id="filter_residence_type" class="form-select form-select-sm filter-select"
                                style="width:auto; min-width:140px;">
                                <option value="">All Residences</option>
                                @foreach ($residenceTypes as $type)
                                    <option value="{{ $type }}">{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                                @endforeach
                            </select>

                            

                            {{-- Status --}}
                            <select id="filter_status" class="form-select form-select-sm filter-select"
                                style="width:auto; min-width:130px;">
                                <option value="">All Status</option>
                                @foreach ($statuses as $s)
                                    <option value="{{ $s }}">{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                                @endforeach
                            </select>

                            {{-- Category --}}
                            <select id="filter_category" class="form-select form-select-sm filter-select"
                                style="width:auto; min-width:120px;">
                                <option value="">All Castes</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat }}">{{ strtoupper($cat) }}</option>
                                @endforeach
                            </select>

                            {{-- Active filter badge (updated by JS) --}}
                            <span id="activeFiltersBadge" class="badge bg-primary rounded-pill ms-1 d-none"></span>
                        </div>
                    </div>
                </div>

                {{-- ════════════════════════════════════════════════════════
                     CONTENT AREA
                ════════════════════════════════════════════════════════ --}}
                <div class="content-area-body">
                    @include('elements.flash')

                    <div class="card stretch stretch-full">
                        <div class="card-body p-0">

                            {{-- ── LIST VIEW ─────────────────────────── --}}
                            <div id="list-view" class="view-mode">
                                <div class="table-responsive">
                                 <div id="proposalList_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                                    <table class="table table-hover mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="fw-semibold">Admission #</th>
                                                <th class="fw-semibold">Name</th>
                                                <th class="fw-semibold">Gender</th>
                                                <th class="fw-semibold">Academic Year</th>
                                                <th class="fw-semibold">Course</th>
                                                <th class="fw-semibold">Branch</th>
                                                <th class="fw-semibold">Student Type</th>
                                                <th class="fw-semibold">Residence Type</th>
                                                <th class="fw-semibold">Semester</th>
                                                <th class="fw-semibold">Status</th>
                                                <th class="text-center fw-semibold">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="listTbody">
                                            {{-- Filled by JS --}}
                                        </tbody>
                                    </table>
                                 </div>
                                </div>
                                {{-- Pagination (client-side) --}}
                                <div id="listPagination" class="p-3 border-top d-flex justify-content-between align-items-center gap-2 flex-wrap d-none">
                                    <small class="text-muted" id="paginationInfo"></small>
                                    <div class="d-flex gap-1" id="paginationBtns"></div>
                                </div>
                            </div>

                            {{-- ── GRID VIEW ─────────────────────────── --}}
                            <div id="grid-view" class="view-mode d-none">
                                <div class="p-4">
                                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="gridContainer">
                                        {{-- Filled by JS --}}
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

    {{-- ════════════════════════════════════════════════════════════════
         IMPORT MODAL
    ════════════════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="importStudentsModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" action="{{ route('admin.students.import') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold">
                            <i class="feather-upload-cloud me-2 text-success"></i>Import Students via Excel
                        </h5>
                        <p class="text-muted small mb-0">Upload an .xlsx file using the correct template format.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-3">
                    <div class="alert alert-info d-flex align-items-center gap-2 py-2 px-3 mb-3" role="alert">
                        <i class="feather-info flex-shrink-0"></i>
                        <div class="small">
                            Don't have the template?
                            <a href="{{ route('admin.students.template') }}" class="fw-semibold text-decoration-none" target="_blank">
                                Download Excel Template <i class="feather-download ms-1" style="font-size:11px;"></i>
                            </a>
                            — fill it and upload here.
                        </div>
                    </div>
                    <div class="border border-2 border-dashed rounded-3 p-4 text-center bg-light" id="dropZone">
                        <i class="feather-file-text text-success mb-2 d-block" style="font-size:2rem;"></i>
                        <p class="fw-semibold mb-1 small">Drag & drop your Excel file here</p>
                        <p class="text-muted small mb-3">or click to browse</p>
                        <input type="file" name="file" id="importFile" class="d-none" accept=".xlsx,.xls" required>
                        <button type="button" class="btn btn-outline-secondary btn-sm"
                            onclick="document.getElementById('importFile').click()">
                            <i class="feather-folder me-1"></i> Browse File
                        </button>
                        <div id="fileName" class="mt-2 text-success small fw-semibold d-none"></div>
                    </div>
                    <small class="text-muted d-block mt-2">Accepted: .xlsx, .xls &nbsp;|&nbsp; Max size: 5 MB</small>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm px-4" id="importBtn" disabled>
                        <i class="feather-upload me-1"></i> Import Students
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════
         ALL STUDENTS JSON  (injected once from PHP)
    ════════════════════════════════════════════════════════════════ --}}
    <script>
    const ALL_STUDENTS = @json($studentsJson);

    // ── Permissions (passed from PHP so JS can conditionally render buttons) ──
    const CAN_EDIT   = {{ auth()->user()->hasPermissionTo('Edit Student')   ? 'true' : 'false' }};
    const CAN_DELETE = {{ auth()->user()->hasPermissionTo('Delete Student') ? 'true' : 'false' }};
    const CSRF_TOKEN = '{{ csrf_token() }}';
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        // ── Constants ──────────────────────────────────────────────────────
        const PAGE_SIZE = 20;

        // ── State ──────────────────────────────────────────────────────────
        let filtered  = [...ALL_STUDENTS];
        let currentPage = 1;
        let currentView = sessionStorage.getItem('studentViewMode') || 'list';

        // ── DOM refs ───────────────────────────────────────────────────────
        const searchInput      = document.getElementById('searchInput');
        const clearAllBtn      = document.getElementById('clearAllBtn');
        const visibleCount     = document.getElementById('visibleCount');
        const activeFiltersBadge = document.getElementById('activeFiltersBadge');

        const listTbody        = document.getElementById('listTbody');
        const gridContainer    = document.getElementById('gridContainer');
        const listPagination   = document.getElementById('listPagination');
        const gridPagination   = document.getElementById('gridPagination');
        const paginationInfo   = document.getElementById('paginationInfo');
        const paginationBtns   = document.getElementById('paginationBtns');

        const filterSelects    = document.querySelectorAll('.filter-select');
        const toggleBtns       = document.querySelectorAll('.view-toggle');

        // ── View toggle ────────────────────────────────────────────────────
        setView(currentView);
        toggleBtns.forEach(btn => btn.addEventListener('click', function () {
            setView(this.dataset.view);
        }));

        function setView(view) {
            currentView = view;
            toggleBtns.forEach(b => b.classList.remove('active'));
            document.querySelector(`[data-view="${view}"]`)?.classList.add('active');
            document.getElementById('list-view').classList.toggle('d-none', view !== 'list');
            document.getElementById('grid-view').classList.toggle('d-none', view !== 'grid');
            sessionStorage.setItem('studentViewMode', view);
            render();
        }

        // ── Filter listeners ───────────────────────────────────────────────
        searchInput.addEventListener('input', () => { currentPage = 1; applyFilters(); });
        filterSelects.forEach(sel => sel.addEventListener('change', () => { currentPage = 1; applyFilters(); }));
        clearAllBtn.addEventListener('click', clearAll);

        // ── Core: apply all active filters ────────────────────────────────
        function applyFilters() {
            const search        = searchInput.value.trim().toLowerCase();
            const academicYear  = document.getElementById('filter_academic_year').value;
            const course        = document.getElementById('filter_course').value;
            const stream        = document.getElementById('filter_stream').value;
            const studentType   = document.getElementById('filter_student_type').value;
            const residenceType = document.getElementById('filter_residence_type').value;
            const classId       = document.getElementById('filter_class').value;
            const status        = document.getElementById('filter_status').value;
            const category      = document.getElementById('filter_category').value;

            filtered = ALL_STUDENTS.filter(s => {
                if (search && !(
                    s.full_name.toLowerCase().includes(search)        ||
                    s.course_name?.toLowerCase().includes(search)     ||
                    s.admission_number?.toLowerCase().includes(search) ||
                    (s.roll_number && String(s.roll_number).toLowerCase().includes(search))
                )) return false;

                if (academicYear  && String(s.academic_year_id) !== academicYear)  return false;
                if (course        && String(s.course_id)        !== course)        return false;
                if (stream        && String(s.stream_id)        !== stream)         return false;
                if (classId       && String(s.class_id)         !== classId)        return false;
                if (studentType   && s.student_type             !== studentType)     return false;
                if (residenceType && s.residence_type           !== residenceType)   return false;
                if (status        && s.status                   !== status)          return false;
                if (category      && s.category                 !== category)        return false;

                return true;
            });

            // Count active filters for the badge
            let count = 0;
            if (search)        count++;
            if (academicYear)  count++;
            if (course)        count++;
            if (stream)        count++;
            if (studentType)   count++;
            if (residenceType) count++;
            if (classId)       count++;
            if (status)        count++;
            if (category)      count++;

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

        // ── Render ─────────────────────────────────────────────────────────
        function render() {
            const totalPages = Math.max(1, Math.ceil(filtered.length / PAGE_SIZE));
            if (currentPage > totalPages) currentPage = totalPages;
            const start  = (currentPage - 1) * PAGE_SIZE;
            const paged  = filtered.slice(start, start + PAGE_SIZE);

            if (currentView === 'list') renderList(paged, start);
            else                        renderGrid(paged, start);

            renderPagination(totalPages);
        }

        // ── List view ──────────────────────────────────────────────────────
        function renderList(paged, start) {
            if (paged.length === 0) {
                listTbody.innerHTML = `
                    <tr>
                        <td colspan="11" class="text-center py-5">
                            <i class="feather-users text-muted mb-3 d-block" style="font-size:3rem;"></i>
                            <h6 class="text-muted">No students found</h6>
                            <p class="text-muted small mb-0">Try adjusting your filters or search criteria</p>
                        </td>
                    </tr>`;
                listPagination.classList.add('d-none');
                return;
            }

            listTbody.innerHTML = paged.map(s => {
                const avatar = s.photo_url
                    ? `<img src="${s.photo_url}" class="rounded-circle" width="32" height="32" style="object-fit:cover;" alt="${esc(s.first_name)}">`
                    : `<div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold"
                            style="width:32px;height:32px;font-size:0.75rem;background:linear-gradient(135deg,#667eea,#764ba2);">
                            ${esc((s.first_name[0]||'') + (s.last_name[0]||'')).toUpperCase()}
                        </div>`;

                const statusColor = { active:'success', transfer_out:'warning', alumni:'info' }[s.status] || 'secondary';
                const typeColor   = s.student_type   === 'lateral'   ? 'warning'  : 'secondary';
                const resColor    = s.residence_type === 'hosteller'  ? 'success'  : 'info';

                const editBtn = CAN_EDIT
                    ? `<a href="${s.edit_url}" class="avatar-text avatar-md text-warning" title="Edit"><i class="feather-edit-3"></i></a>`
                    : '';
                const deleteBtn = CAN_DELETE
                    ? `<form action="${s.delete_url}" method="POST" class="d-inline" onsubmit="return confirm('Delete this student?')">
                           <input type="hidden" name="_token" value="${CSRF_TOKEN}">
                           <input type="hidden" name="_method" value="DELETE">
                           <button type="submit" class="avatar-text avatar-md border-0 bg-transparent text-danger" title="Delete">
                               <i class="feather-trash-2"></i>
                           </button>
                       </form>`
                    : '';

                return `
                    <tr>
                        <td class="fw-medium">
                            <a href="${s.show_url}" class="text-primary text-decoration-none">${esc(s.admission_number)}</a>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                ${avatar}
                                <span class="fw-medium">${esc(s.full_name)}</span>
                            </div>
                        </td>
                        <td>${esc(capitalize(s.gender) || 'N/A')}</td>
                        <td><span class="badge bg-light text-dark">${esc(s.academic_year_name || 'N/A')}</span></td>
                        <td><span class="badge bg-primary text-white">${esc(s.course_name || 'N/A')}</span></td>
                        <td><span class="badge bg-info text-white">${esc(s.stream_name || 'N/A')}</span></td>
                        <td><span class="badge bg-${typeColor}">${esc(capitalize(s.student_type) || 'N/A')}</span></td>
                        <td><span class="badge bg-${resColor}">${esc(capitalize(s.residence_type?.replace('_',' ')) || 'N/A')}</span></td>
                        <td><span class="badge bg-success">${esc(s.class_name || 'N/A')}</span></td>
                        <td><span class="badge bg-${statusColor}">${esc(capitalize(s.status?.replace('_',' ')))}</span></td>
                        <td>
                            <div class="hstack gap-2 justify-content-center">
                                <a href="${s.show_url}" class="avatar-text avatar-md text-primary" title="View"><i class="feather-eye"></i></a>
                                ${editBtn}
                                ${deleteBtn}
                            </div>
                        </td>
                    </tr>`;
            }).join('');

            listPagination.classList.remove('d-none');
            paginationInfo.textContent = `Showing ${start + 1}–${Math.min(start + paged.length, filtered.length)} of ${filtered.length}`;
        }

        // ── Grid view ──────────────────────────────────────────────────────
        function renderGrid(paged, start) {
            if (paged.length === 0) {
                gridContainer.innerHTML = `
                    <div class="col-12 text-center py-5">
                        <i class="feather-users text-muted mb-3 d-block" style="font-size:3rem;"></i>
                        <h5 class="fw-semibold text-muted mb-2">No Students Found</h5>
                        <p class="text-muted mb-0">Try adjusting your filters or search criteria.</p>
                    </div>`;
                gridPagination.classList.add('d-none');
                return;
            }

            gridContainer.innerHTML = paged.map(s => {
                const avatar = s.photo_url
                    ? `<img src="${s.photo_url}" class="rounded-3 border border-2 border-white shadow-sm"
                            width="64" height="64" style="object-fit:cover;" alt="${esc(s.first_name)}">`
                    : `<div class="rounded-3 text-white d-flex align-items-center justify-content-center fw-bold shadow-sm"
                            style="width:64px;height:64px;font-size:1rem;background:linear-gradient(135deg,#667eea,#764ba2);">
                            ${esc((s.first_name[0]||'') + (s.last_name[0]||'')).toUpperCase()}
                        </div>`;

                const typeColor = s.student_type   === 'lateral'  ? 'bg-warning'  : 'bg-secondary';
                const resColor  = s.residence_type === 'hosteller' ? 'bg-success'  : 'bg-info';

                const editBtn = CAN_EDIT
                    ? `<a href="${s.edit_url}" class="btn btn-outline-secondary btn-sm flex-fill">
                           <i class="feather-edit-3 me-1"></i>Edit
                       </a>`
                    : '';
                const deleteBtn = CAN_DELETE
                    ? `<form action="${s.delete_url}" method="POST" class="flex-fill">
                           <input type="hidden" name="_token" value="${CSRF_TOKEN}">
                           <input type="hidden" name="_method" value="DELETE">
                           <button type="submit" class="btn btn-outline-danger btn-sm w-100"
                               onclick="return confirm('Delete ${esc(s.full_name)}?')">
                               <i class="feather-trash-2 me-1"></i>Delete
                           </button>
                       </form>`
                    : '';

                return `
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm student-card overflow-hidden">
                            <div class="card-header bg-white border-bottom p-4 pb-2">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="flex-shrink-0">${avatar}</div>
                                    <div class="flex-grow-1 min-w-0">
                                        <h6 class="mb-1 fw-bold text-dark lh-sm text-truncate">${esc(s.full_name)}</h6>
                                        <div class="hstack gap-1 flex-wrap">
                                            <span class="badge bg-light text-dark px-2 py-1 fw-medium">
                                                <i class="feather-hash me-1 opacity-60" style="font-size:10px;"></i>${esc(s.admission_number)}
                                            </span>
                                            ${s.roll_number ? `<span class="badge bg-primary text-white px-2 py-1 fw-medium">R-${esc(String(s.roll_number))}</span>` : ''}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-4 pt-3">
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block mb-1">Gender</small>
                                        <span class="fw-semibold text-dark small">${esc(capitalize(s.gender) || 'N/A')}</span>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block mb-1">Blood Group</small>
                                        <span class="fw-semibold text-dark small">${esc(s.blood_group || 'N/A')}</span>
                                    </div>
                                    <div class="col-12">
                                        <small class="text-muted d-block mb-1">Date of Birth</small>
                                        <span class="fw-semibold text-dark small">${esc(s.dob || 'N/A')}</span>
                                    </div>
                                    <div class="col-12">
                                        <div class="border-top pt-2 mt-1">
                                            <div class="d-flex flex-column gap-2 bg-light p-3 rounded-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-muted small">Year:</span>
                                                    <span class="badge bg-light text-dark">${esc(s.academic_year_name || 'N/A')}</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-muted small">Course:</span>
                                                    <span class="badge bg-primary text-white">${esc(s.course_name || 'N/A')}</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-muted small">Branch:</span>
                                                    <span class="badge bg-info text-white">${esc(s.stream_name || 'N/A')}</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-muted small">Student Type:</span>
                                                    <span class="badge ${typeColor}">${esc(capitalize(s.student_type) || 'N/A')}</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-muted small">Residence:</span>
                                                    <span class="badge ${resColor}">${esc(capitalize(s.residence_type?.replace('_',' ')) || 'N/A')}</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-muted small">Semester:</span>
                                                    <span class="badge bg-success">${esc(s.class_name || 'N/A')}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-top p-3">
                                <div class="d-grid gap-2">
                                    <a href="${s.show_url}" class="btn btn-primary btn-sm fw-semibold">
                                        <i class="feather-eye me-2"></i>View Profile
                                    </a>
                                    <div class="hstack gap-2">
                                        ${editBtn}
                                        ${deleteBtn}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`;
            }).join('');

            gridPagination.classList.remove('d-none');
        }

        // ── Pagination ─────────────────────────────────────────────────────
        function renderPagination(totalPages) {
            const makeBtn = (label, page, disabled = false, active = false) =>
                `<button class="btn btn-sm ${active ? 'btn-primary' : 'btn-outline-secondary'}"
                    ${disabled ? 'disabled' : ''}
                    data-page="${page}">${label}</button>`;

            let html = '';
            html += makeBtn('&laquo;', currentPage - 1, currentPage === 1);
            for (let p = 1; p <= totalPages; p++) {
                if (totalPages <= 7 || p === 1 || p === totalPages ||
                    Math.abs(p - currentPage) <= 1) {
                    html += makeBtn(p, p, false, p === currentPage);
                } else if (Math.abs(p - currentPage) === 2) {
                    html += `<button class="btn btn-sm btn-outline-secondary" disabled>…</button>`;
                }
            }
            html += makeBtn('&raquo;', currentPage + 1, currentPage === totalPages);

            // List pagination
            paginationBtns.innerHTML = html;
            paginationBtns.querySelectorAll('[data-page]').forEach(btn => {
                btn.addEventListener('click', function () {
                    currentPage = parseInt(this.dataset.page);
                    render();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            });

            // Grid pagination (mirror)
            gridPagination.innerHTML = html;
            gridPagination.querySelectorAll('[data-page]').forEach(btn => {
                btn.addEventListener('click', function () {
                    currentPage = parseInt(this.dataset.page);
                    render();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            });

            if (totalPages <= 1) {
                listPagination.classList.add('d-none');
                gridPagination.classList.add('d-none');
            }
        }

        // ── Helpers ────────────────────────────────────────────────────────
        function esc(str) {
            if (str == null) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }
        function capitalize(str) {
            if (!str) return '';
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        // ── Initial render ─────────────────────────────────────────────────
        applyFilters();

        // ── Import file picker ─────────────────────────────────────────────
        const importFile = document.getElementById('importFile');
        const fileNameEl = document.getElementById('fileName');
        const importBtn  = document.getElementById('importBtn');
        const dropZone   = document.getElementById('dropZone');

        importFile?.addEventListener('change', function () { handleFileSelect(this.files[0]); });

        dropZone?.addEventListener('dragover', e => {
            e.preventDefault();
            dropZone.classList.add('border-success', 'bg-success-subtle');
        });
        dropZone?.addEventListener('dragleave', () => {
            dropZone.classList.remove('border-success', 'bg-success-subtle');
        });
        dropZone?.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.classList.remove('border-success', 'bg-success-subtle');
            const file = e.dataTransfer.files[0];
            if (file) {
                const dt = new DataTransfer();
                dt.items.add(file);
                importFile.files = dt.files;
                handleFileSelect(file);
            }
        });

        function handleFileSelect(file) {
            if (!file) return;
            const validTypes = [
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-excel'
            ];
            if (!validTypes.includes(file.type) && !file.name.match(/\.(xlsx|xls)$/i)) {
                alert('Please upload a valid Excel file (.xlsx or .xls)');
                importFile.value = '';
                return;
            }
            fileNameEl.textContent = '✓ ' + file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
            fileNameEl.classList.remove('d-none');
            importBtn.disabled = false;
        }
    });
    </script>

    <style>
        .filter-select {
            border-radius: 6px;
            font-size: 0.8125rem;
            border-color: #dee2e6;
            background-color: #fff;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .filter-select:hover { border-color: #0d6efd; background-color: #f8f9fa; }
        .filter-select:focus { border-color: #0d6efd; box-shadow: 0 0 0 2px rgba(13,110,253,.15); }
        .table th { white-space:nowrap; font-size:0.8125rem; padding:0.75rem 1rem; }
        .table td { padding:0.75rem 1rem; vertical-align:middle; font-size:0.875rem; }
        .student-card { transition: transform .2s ease, box-shadow .2s ease; }
        .student-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.1) !important; }
        #dropZone { border-style:dashed !important; transition: background .2s ease, border-color .2s ease; cursor:pointer; }
        #dropZone:hover { background:#f0fdf4 !important; border-color:#198754 !important; }
        .content-area-header { box-shadow: 0 2px 8px rgba(0,0,0,.06); }
        .badge { font-weight:500; padding:0.35rem 0.65rem; }
    </style>
@endsection
