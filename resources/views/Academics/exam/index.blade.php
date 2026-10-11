@extends('admin_layout')
@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Academics.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            {{-- HEADER --}}
            <div class="content-area-header bg-white px-4 py-3 border-bottom shadow-sm"
                 style="position:sticky; top:0; z-index:200;">
                <div class="d-flex align-items-center gap-3 flex-wrap w-100">

                    <button id="backBtn" class="btn btn-sm btn-outline-secondary d-none flex-shrink-0">
                        <i class="feather-arrow-left me-1"></i> Back
                    </button>

                    <div class="flex-shrink-0">
                        <h5 class="fw-bold mb-0 text-dark" id="pageTitle">Courses</h5>
                        <small class="text-muted" id="pageSubtitle">Select a course to view branches</small>
                    </div>

                    <div class="position-relative flex-grow-1 d-none" id="searchWrap" style="max-width:300px; min-width:160px;">
                        <i class="feather-search position-absolute top-50 translate-middle-y text-muted"
                           style="font-size:13px; left:10px;"></i>
                        <input type="text" id="searchInput"
                            class="form-control form-control-sm ps-4 border-secondary-subtle"
                            placeholder="Search exam…"
                            style="border-radius:6px;">
                    </div>

                    <div class="ms-auto d-flex align-items-center gap-2 flex-shrink-0">
                        @if (auth()->user()->hasPermissionTo('Create Exam list'))
                            <a href="{{ route('admin.exams.create') }}"
                               class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1">
                                <i class="feather-plus" style="font-size:13px;"></i>
                                <span class="d-none d-sm-inline">Create Exam</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- CONTENT --}}
            <div class="content-area-body p-4">
                @include('elements.flash')

                {{-- LEVEL 1: Course Cards --}}
                <div id="courseView">
                    <div class="row g-3" id="courseCardsRow"></div>
                </div>

                {{-- LEVEL 2: Branch Cards --}}
                <div id="branchView" class="d-none">
                    <div class="row g-3" id="branchCardsRow"></div>
                </div>

                {{-- LEVEL 3: Exams Table --}}
                <div id="examView" class="d-none">
                    <div class="card stretch stretch-full">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="fw-semibold">Exam Code</th>
                                            <th class="fw-semibold">Exam Type</th>
                                            <th class="fw-semibold">Name</th>
                                            <th class="fw-semibold">Class – Section</th>
                                            <th class="fw-semibold">Subject</th>
                                            <th class="fw-semibold">Date & Time</th>
                                            <th class="fw-semibold">Marks</th>
                                            <th class="text-center fw-semibold">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="examTbody"></tbody>
                                </table>
                            </div>
                            <div id="examPagination"
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
</div>

<script>
const ALL_DATA    = @json($examsJson);
const ALL_STREAMS = @json($streams->map(fn($s) => ['id' => $s->id, 'name' => $s->name]));
const ALL_COURSES = @json($coursesJson);
const CAN_EDIT    = {{ auth()->user()->hasPermissionTo('Edit Exam list')   ? 'true' : 'false' }};
const CAN_DELETE  = {{ auth()->user()->hasPermissionTo('Delete Exam list') ? 'true' : 'false' }};
const CSRF_TOKEN  = '{{ csrf_token() }}';
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const PAGE_SIZE = 20;

    // State
    let level             = 'course';   // 'course' | 'branch' | 'exam'
    let currentCourseId   = null;
    let currentCourseName = '';
    let currentBranchId   = null;
    let currentBranchName = '';
    let filteredExams     = [];
    let currentPage       = 1;

    // DOM
    const courseView   = document.getElementById('courseView');
    const branchView   = document.getElementById('branchView');
    const examView     = document.getElementById('examView');
    const backBtn      = document.getElementById('backBtn');
    const pageTitle    = document.getElementById('pageTitle');
    const pageSubtitle = document.getElementById('pageSubtitle');
    const searchWrap   = document.getElementById('searchWrap');
    const searchInput  = document.getElementById('searchInput');

    backBtn.addEventListener('click', goBack);
    searchInput.addEventListener('input', () => { currentPage = 1; applySearch(); });

    // ── Go Back ────────────────────────────────────────────────
    function goBack() {
        if (level === 'exam') {
            showBranchView(currentCourseId, currentCourseName);
        } else if (level === 'branch') {
            showCourseView();
        }
    }

    // ── LEVEL 1: Course View ───────────────────────────────────
    function showCourseView() {
        level = 'course';
        courseView.classList.remove('d-none');
        branchView.classList.add('d-none');
        examView.classList.add('d-none');
        backBtn.classList.add('d-none');
        searchWrap.classList.add('d-none');
        searchInput.value        = '';
        pageTitle.textContent    = 'Courses';
        pageSubtitle.textContent = 'Select a course to view branches';
        currentCourseId          = null;
        currentBranchId          = null;
        renderCourseCards();
    }

    function renderCourseCards() 
    {
           const row = document.getElementById('courseCardsRow');

    // ✅ course_id se directly count karo
    const countMap = {};
    ALL_DATA.forEach(e => {
        if (e.course_id) {
            countMap[e.course_id] = (countMap[e.course_id] || 0) + 1;
        }
    });

    let courses = ALL_COURSES.map(c => ({
        id:      c.id,
        name:    c.name,
        code:    c.code,
        streams: c.streams,
        count:   countMap[c.id] || 0,
    }));
 

        if (!courses.length) {
            row.innerHTML = `
                <div class="col-12 text-center py-5">
                    <i class="feather-book text-muted d-block mb-3" style="font-size:3rem;"></i>
                    <h6 class="text-muted">No courses found</h6>
                </div>`;
            return;
        }

        const colors = [
            { bg: '#ede9fe', icon: '#6d28d9', border: '#c4b5fd' },
            { bg: '#e0f2fe', icon: '#0369a1', border: '#7dd3fc' },
            { bg: '#dcfce7', icon: '#15803d', border: '#86efac' },
            { bg: '#fef9c3', icon: '#a16207', border: '#fde047' },
            { bg: '#ffe4e6', icon: '#be123c', border: '#fda4af' },
            { bg: '#f0fdf4', icon: '#166534', border: '#bbf7d0' },
            { bg: '#fff7ed', icon: '#c2410c', border: '#fed7aa' },
            { bg: '#fdf4ff', icon: '#7e22ce', border: '#e9d5ff' },
        ];

        row.innerHTML = courses.map((c, i) => {
            const col = colors[i % colors.length];
            const branchCount = (c.streams || []).length;
            return `
            <div class="col-sm-6 col-md-4 col-xl-3">
                <div class="course-card h-100" data-course-id="${c.id}" data-course-name="${esc(c.name)}"
                     style="background:${col.bg}; border:1.5px solid ${col.border}; border-radius:14px;
                            padding:1.5rem; cursor:pointer; transition:all .22s ease;">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width:46px;height:46px;border-radius:12px;
                                    background:${col.icon}20;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="feather-book-open" style="color:${col.icon};font-size:20px;"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark" style="font-size:.95rem;line-height:1.3;">${esc(c.name)}</div>
                            ${c.code ? `<small style="color:${col.icon};font-size:.75rem;font-weight:600;">${esc(c.code)}</small>` : ''}
                        </div>
                    </div>
                  <div class="d-flex align-items-center justify-content-between">
                        <div style="font-size:.8rem;color:${col.icon};opacity:.85;">
                            ${branchCount} Branch${branchCount !== 1 ? 'es' : ''}
                        </div>
                        <div style="width:32px;height:32px;border-radius:50%;background:${col.icon};
                                    display:flex;align-items:center;justify-content:center;">
                            <i class="feather-arrow-right" style="color:#fff;font-size:14px;"></i>
                        </div>
                    </div>
                </div>
            </div>`;
        }).join('');

        row.querySelectorAll('.course-card').forEach(card => {
            card.addEventListener('mouseenter', function () {
                this.style.transform = 'translateY(-4px)';
                this.style.boxShadow = '0 8px 24px rgba(0,0,0,.1)';
            });
            card.addEventListener('mouseleave', function () {
                this.style.transform = '';
                this.style.boxShadow = '';
            });
            card.addEventListener('click', function () {
                showBranchView(this.dataset.courseId, this.dataset.courseName);
            });
        });
    }

    // ── LEVEL 2: Branch View ───────────────────────────────────
    function showBranchView(courseId, courseName) {
        level             = 'branch';
        currentCourseId   = courseId;
        currentCourseName = courseName;
        currentBranchId   = null;

        courseView.classList.add('d-none');
        branchView.classList.remove('d-none');
        examView.classList.add('d-none');
        backBtn.classList.remove('d-none');
        searchWrap.classList.add('d-none');
        searchInput.value        = '';
        pageTitle.textContent    = courseName;
        pageSubtitle.textContent = 'Select a branch to view exams';

        renderBranchCards(courseId);
    }

    function renderBranchCards(courseId) {
        const row = document.getElementById('branchCardsRow');

        // Find the course's streams
        const course = ALL_COURSES.find(c => String(c.id) === String(courseId));
        const courseStreamIds = (course?.streams || []).map(s => s.id);

        // Count exams for each stream in this course
        // const countMap = {};
        // ALL_DATA.forEach(e => {
        //     const key = e.stream_id ?? '__none__';
        //     if (e.stream_id && courseStreamIds.includes(e.stream_id)) {
        //         countMap[e.stream_id] = (countMap[e.stream_id] || 0) + 1;
        //     } else if (!e.stream_id) {
        //         // exams with no stream — show under this course only if course has no streams defined
        //         // (optional: remove this block if you don't want unassigned exams here)
        //     }
        // });
        const countMap = {};
            ALL_DATA.forEach(e => {
                // ✅ course_id match karo pehle, phir stream count karo
                if (String(e.course_id) === String(courseId) && e.stream_id) {
                    countMap[e.stream_id] = (countMap[e.stream_id] || 0) + 1;
                }
            });

        const branches = (course?.streams || []).map(s => ({
            id:    s.id,
            name:  s.name,
            count: countMap[s.id] || 0,
        }));

        if (!branches.length) {
            row.innerHTML = `
                <div class="col-12 text-center py-5">
                    <i class="feather-git-branch text-muted d-block mb-3" style="font-size:3rem;"></i>
                    <h6 class="text-muted">No branches found for this course</h6>
                </div>`;
            return;
        }

        const colors = [
            { bg: '#e8f4fd', icon: '#1a7fd4', border: '#bee3f8' },
            { bg: '#fef3e2', icon: '#d97706', border: '#fde68a' },
            { bg: '#ecfdf5', icon: '#059669', border: '#a7f3d0' },
            { bg: '#fdf2f8', icon: '#9333ea', border: '#e9d5ff' },
            { bg: '#fff1f2', icon: '#e11d48', border: '#fecdd3' },
            { bg: '#f0fdf4', icon: '#16a34a', border: '#bbf7d0' },
            { bg: '#eff6ff', icon: '#2563eb', border: '#bfdbfe' },
            { bg: '#fefce8', icon: '#ca8a04', border: '#fef08a' },
        ];

        row.innerHTML = branches.map((b, i) => {
            const c = colors[i % colors.length];
            return `
            <div class="col-sm-6 col-md-4 col-xl-3">
                <div class="branch-card h-100" data-branch-id="${b.id}" data-branch-name="${esc(b.name)}"
                     style="background:${c.bg}; border:1.5px solid ${c.border}; border-radius:14px;
                            padding:1.5rem; cursor:pointer; transition:all .22s ease;">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width:46px;height:46px;border-radius:12px;
                                    background:${c.icon}20;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="feather-git-branch" style="color:${c.icon};font-size:20px;"></i>
                        </div>
                        <div class="fw-bold text-dark" style="font-size:.95rem;line-height:1.3;">${esc(b.name)}</div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span style="font-size:1.75rem;font-weight:700;color:${c.icon};line-height:1;">${b.count}</span>
                            <span class="text-muted ms-1" style="font-size:.8rem;">Exam${b.count !== 1 ? 's' : ''}</span>
                        </div>
                        <div style="width:32px;height:32px;border-radius:50%;background:${c.icon};
                                    display:flex;align-items:center;justify-content:center;">
                            <i class="feather-arrow-right" style="color:#fff;font-size:14px;"></i>
                        </div>
                    </div>
                </div>
            </div>`;
        }).join('');

        row.querySelectorAll('.branch-card').forEach(card => {
            card.addEventListener('mouseenter', function () {
                this.style.transform = 'translateY(-4px)';
                this.style.boxShadow = '0 8px 24px rgba(0,0,0,.1)';
            });
            card.addEventListener('mouseleave', function () {
                this.style.transform = '';
                this.style.boxShadow = '';
            });
            card.addEventListener('click', function () {
                openBranch(this.dataset.branchId, this.dataset.branchName);
            });
        });
    }

    // ── LEVEL 3: Exam Table ────────────────────────────────────
    function openBranch(branchId, branchName) {
        level             = 'exam';
        currentBranchId   = branchId;
        currentBranchName = branchName;
        currentPage       = 1;
        searchInput.value = '';

        branchView.classList.add('d-none');
        examView.classList.remove('d-none');
        searchWrap.classList.remove('d-none');

        pageTitle.textContent    = branchName;
        pageSubtitle.textContent = currentCourseName;

        applySearch();
    }

    // function applySearch() {
    //     const search = searchInput.value.trim().toLowerCase();

    //     let base = ALL_DATA.filter(e => {
    //         return String(e.stream_id ?? '__none__') === String(currentBranchId);
    //     });

    //     if (search) base = base.filter(e => e.name.toLowerCase().includes(search));

    //     filteredExams = base;
    //     render();
    // }
    function applySearch() {
    const search = searchInput.value.trim().toLowerCase();

    let base = ALL_DATA.filter(e => {
        // ✅ course_id aur stream_id dono se filter karo
        return String(e.course_id ?? '') === String(currentCourseId)
            && String(e.stream_id ?? '__none__') === String(currentBranchId);
    });

    if (search) base = base.filter(e => e.name.toLowerCase().includes(search));

    filteredExams = base;
    render();
}

    // ── Render Table ───────────────────────────────────────────
    function render() {
        const totalPages = Math.max(1, Math.ceil(filteredExams.length / PAGE_SIZE));
        if (currentPage > totalPages) currentPage = totalPages;
        const start = (currentPage - 1) * PAGE_SIZE;
        const paged = filteredExams.slice(start, start + PAGE_SIZE);
        renderTable(paged, start);
        renderPagination(totalPages, start, paged.length);
    }

    function renderTable(paged, start) {
        const tbody      = document.getElementById('examTbody');
        const pagination = document.getElementById('examPagination');

        if (!paged.length) {
            tbody.innerHTML = `
                <tr><td colspan="7" class="text-center py-5">
                    <i class="feather-book-open text-muted mb-3 d-block" style="font-size:3rem;"></i>
                    <h6 class="text-muted">No exams found</h6>
                    <p class="text-muted small mb-0">No exams in this branch yet</p>
                </td></tr>`;
            pagination.classList.add('d-none');
            return;
        }

        tbody.innerHTML = paged.map(e => {
            const viewBtn = `<a href="${e.show_url}" class="avatar-text avatar-md text-info" title="View Details">
                                <i class="feather-eye"></i></a>`;
            const marksBtn = `<a href="${e.marks_url}" class="avatar-text avatar-md text-success" title="Student Marks">
                                <i class="feather-bar-chart-2"></i></a>`;
            const editBtn = CAN_EDIT
                ? `<a href="${e.edit_url}" class="avatar-text avatar-md text-warning" title="Edit">
                       <i class="feather-edit-3"></i></a>` : '';
            const deleteBtn = CAN_DELETE
                ? `<form action="${e.delete_url}" method="POST" class="d-inline"
                       onsubmit="return confirm('Are you sure you want to delete this exam?')">
                       <input type="hidden" name="_token" value="${CSRF_TOKEN}">
                       <input type="hidden" name="_method" value="DELETE">
                       <button type="submit" class="avatar-text avatar-md border-0 bg-transparent text-danger" title="Delete">
                           <i class="feather-trash-2"></i></button></form>` : '';

            return `
                <tr>
                    <td><span class="badge bg-soft-primary text-primary fw-bold">${esc(e.exam_code)}</span></td>
                    <td><a href="${e.edit_url}" class="fw-medium">${esc(e.exam_type_name)}</a></td>
                    <td>${esc(e.name)}</td>
                    <td>${esc(e.class_name)} – ${esc(e.section_name)}</td>
                    <td>${esc(e.subject_name)}</td>
                    <td>${esc(e.exam_date)}<br><small class="text-muted">${esc(e.start_time)} → ${esc(e.end_time)}</small></td>
                    <td>${esc(e.total_marks)} / ${esc(e.pass_marks)}</td>
                    <td><div class="hstack gap-2 justify-content-center">
                        ${viewBtn}${marksBtn}${editBtn}${deleteBtn}
                    </div></td>
                </tr>`;
        }).join('');

        pagination.classList.toggle('d-none', filteredExams.length <= PAGE_SIZE);
        document.getElementById('paginationInfo').textContent =
            `Showing ${start + 1}–${Math.min(start + paged.length, filteredExams.length)} of ${filteredExams.length}`;
    }

    function renderPagination(totalPages, start, pagedLen) {
        if (totalPages <= 1) {
            document.getElementById('examPagination').classList.add('d-none');
            return;
        }
        const makeBtn = (label, page, disabled = false, active = false) =>
            `<button class="btn btn-sm ${active ? 'btn-primary' : 'btn-outline-secondary'}"
                ${disabled ? 'disabled' : ''} data-page="${page}">${label}</button>`;

        let html = makeBtn('&laquo;', currentPage - 1, currentPage === 1);
        for (let p = 1; p <= totalPages; p++) {
            if (totalPages <= 7 || p === 1 || p === totalPages || Math.abs(p - currentPage) <= 1)
                html += makeBtn(p, p, false, p === currentPage);
            else if (Math.abs(p - currentPage) === 2)
                html += `<button class="btn btn-sm btn-outline-secondary" disabled>…</button>`;
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
        if (str == null) return '—';
        return String(str)
            .replace(/&/g,'&amp;').replace(/</g,'&lt;')
            .replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    }

    // ── Init ───────────────────────────────────────────────────
    showCourseView();
});
</script>

<style>
    .course-card:active, .branch-card:active { transform: scale(.97) !important; }
    .table th { white-space: nowrap; font-size: 0.8125rem; padding: 0.75rem 1rem; }
    .table td { padding: 0.75rem 1rem; vertical-align: middle; font-size: 0.875rem; }
    .content-area { overflow-y: auto !important; }
</style>
@endsection