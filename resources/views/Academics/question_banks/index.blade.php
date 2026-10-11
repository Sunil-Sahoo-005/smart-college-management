@extends('admin_layout')
@section('content')

{{-- QUESTIONS VIEW MODAL --}}
<div class="modal fade" id="questionsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <div>
                    <h5 class="modal-title mb-0" id="questionsModalTitle">Questions</h5>
                    <small id="questionsModalSubtitle" class="text-white-50"></small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="d-flex align-items-center gap-3 flex-wrap px-4 py-3 border-bottom bg-light sticky-top">
                    <div>
                        <span class="text-muted small">Total Questions:</span>
                        <span class="fw-bold text-dark ms-1" id="totalQCount">—</span>
                    </div>
                    <div class="vr"></div>
                    <div>
                        <span class="text-muted small">Total Marks:</span>
                        <span class="fw-bold text-success ms-1" id="totalMarks">—</span>
                    </div>
                    <div class="vr"></div>
                    <div>
                        <span class="text-muted small">Total Neg. Marks:</span>
                        <span class="fw-bold text-danger ms-1" id="totalNegMarks">—</span>
                    </div>
                </div>
                <div id="modalLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="text-muted mt-2">Loading questions...</p>
                </div>
                <div id="modalTableWrap" class="d-none">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th width="5%">Sl NO</th>
                                    <th width="12%">Type</th>
                                    <th width="45%">Question</th>
                                    <th width="8%">Marks</th>
                                    <th width="8%">Neg. Marks</th>
                                    <th width="8%">Status</th>
                                    <th width="14%">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="modalTbody"></tbody>
                        </table>
                    </div>
                </div>
                <div id="modalEmpty" class="text-center py-5 d-none">
                    <i class="feather-inbox text-muted d-block mb-2" style="font-size:2.5rem;"></i>
                    <p class="text-muted">No questions added yet.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- MAIN CONTENT --}}
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
                        <h5 class="fw-bold mb-0 text-dark" id="pageTitle">Question Paper</h5>
                        <small class="text-muted" id="pageSubtitle">Select a course to view branches</small>
                    </div>

                    <div class="position-relative flex-grow-1 d-none" id="searchWrap" style="max-width:300px; min-width:160px;">
                        <i class="feather-search position-absolute top-50 translate-middle-y text-muted"
                           style="font-size:13px; left:10px;"></i>
                        <input type="text" id="searchInput"
                            class="form-control form-control-sm ps-4 border-secondary-subtle"
                            placeholder="Search question paper…"
                            style="border-radius:6px;">
                    </div>

                    <div class="ms-auto d-flex align-items-center gap-2 flex-shrink-0">
                        @if (auth()->user()->hasPermissionTo('Create Question Papers'))
                            <a href="{{ route('admin.question_banks.create') }}"
                               id="createQuestionPaperBtn"
                               class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1">
                                <i class="feather-plus" style="font-size:13px;"></i>
                                <span class="d-none d-sm-inline">Create Question Paper</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- CONTENT --}}
            <div class="content-area-body p-4">
                @include('elements.flash')

                {{-- Course Cards --}}
                <div id="courseView">
                    <div class="row g-3" id="courseCardsRow"></div>
                </div>

                {{-- Branch Cards --}}
                <div id="branchView" class="d-none">
                    <div class="row g-3" id="branchCardsRow"></div>
                </div>

                {{-- Question Papers Table --}}
                <div id="qpView" class="d-none">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Sl NO</th>
                                            <th>Name</th>
                                            <th>Class</th>
                                            <th>Subject</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="qpTbody"></tbody>
                                </table>
                            </div>
                            <div id="qpPagination"
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
const ALL_DATA    = @json($questionBanksJson);
const ALL_COURSES = @json($coursesJson);
const CAN_EDIT    = {{ auth()->user()->hasPermissionTo('Edit Question Paper')   ? 'true' : 'false' }};
const CAN_DELETE  = {{ auth()->user()->hasPermissionTo('Delete Question Paper') ? 'true' : 'false' }};
const CSRF_TOKEN  = '{{ csrf_token() }}';
const QUESTIONS_API_URL = "{{ route('admin.question_paper_types.questions') }}";
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const PAGE_SIZE  = 20;
    let level        = 'course';
    let currentCourseId = null, currentCourseName = '';
    let currentBranchId = null, currentBranchName = '';
    let filteredQPs  = [], currentPage = 1;

    const courseView   = document.getElementById('courseView');
    const branchView   = document.getElementById('branchView');
    const qpView       = document.getElementById('qpView');
    const backBtn      = document.getElementById('backBtn');
    const pageTitle    = document.getElementById('pageTitle');
    const pageSubtitle = document.getElementById('pageSubtitle');
    const searchWrap   = document.getElementById('searchWrap');
    const searchInput  = document.getElementById('searchInput');
    const createBtn    = document.getElementById('createQuestionPaperBtn');

    backBtn.addEventListener('click', goBack);
    searchInput.addEventListener('input', () => { currentPage = 1; applySearch(); });

    // ── NAVIGATION ───────────────────────────────────────────────
    function goBack() {
        if (level === 'papers') showBranchView(currentCourseId, currentCourseName);
        else if (level === 'branch') showCourseView();
    }

    function showCourseView() {
        level = 'course';
        courseView.classList.remove('d-none');
        branchView.classList.add('d-none');
        qpView.classList.add('d-none');
        backBtn.classList.add('d-none');
        searchWrap.classList.add('d-none');
        searchInput.value = '';
        pageTitle.textContent    = 'Question Paper';
        pageSubtitle.textContent = 'Select a course to view branches';
        currentCourseId = null; currentBranchId = null;
        if (createBtn) createBtn.href = "{{ route('admin.question_banks.create') }}";
        renderCourseCards();
    }

    function showBranchView(courseId, courseName) {
        level = 'branch';
        currentCourseId = courseId; currentCourseName = courseName; currentBranchId = null;
        courseView.classList.add('d-none');
        branchView.classList.remove('d-none');
        qpView.classList.add('d-none');
        backBtn.classList.remove('d-none');
        searchWrap.classList.add('d-none');
        searchInput.value = '';
        pageTitle.textContent    = courseName;
        pageSubtitle.textContent = 'Select a branch to view question papers';
        if (createBtn) createBtn.href = "{{ route('admin.question_banks.create') }}";
        renderBranchCards(courseId);
    }

    function openBranch(branchId, branchName) {
        level = 'papers';
        currentBranchId = branchId; currentBranchName = branchName;
        currentPage = 1; searchInput.value = '';
        if (createBtn && currentCourseId && branchId)
            createBtn.href = "{{ route('admin.question_banks.create') }}?course_id=" + currentCourseId + "&stream_id=" + branchId;
        branchView.classList.add('d-none');
        qpView.classList.remove('d-none');
        searchWrap.classList.remove('d-none');
        pageTitle.textContent    = branchName;
        pageSubtitle.textContent = currentCourseName;
        applySearch();
    }

   function applySearch() {
    const search = searchInput.value.trim().toLowerCase();
    
    // 👈 course_id bhi filter karo sirf stream_id nahi
    let base = ALL_DATA.filter(e => 
        String(e.stream_id  ?? '__none__') === String(currentBranchId) &&
        String(e.course_id  ?? '__none__') === String(currentCourseId)  // 👈 ADD
    );
    
    if (search) base = base.filter(e => e.name.toLowerCase().includes(search));
    filteredQPs = base;
    render();
}

    // ── RENDER COURSE CARDS ──────────────────────────────────────
    function renderCourseCards() {
    const row = document.getElementById('courseCardsRow');
    const countMap   = {};
    const countedIds = new Set();

    ALL_DATA.forEach(e => {
        if (countedIds.has(e.id)) return;
        
        const cid = String(e.course_id ?? ''); // 👈 Direct course_id from DB
        
        if (cid) {
            countMap[cid] = (countMap[cid] || 0) + 1;
            countedIds.add(e.id);
        }
    });

        const courses = ALL_COURSES.map(c => ({
            id:      c.id,
            name:    c.name,
            streams: c.streams,
            count:   countMap[String(c.id)] || 0,
        }));

        if (!courses.length) {
            row.innerHTML = `<div class="col-12 text-center py-5">
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
            const col         = colors[i % colors.length];
            const branchCount = (c.streams || []).length;
            return `<div class="col-sm-6 col-md-4 col-xl-3">
                <div class="course-card h-100"
                     data-course-id="${c.id}"
                     data-course-name="${esc(c.name)}"
                     style="background:${col.bg};border:1.5px solid ${col.border};border-radius:14px;padding:1.5rem;cursor:pointer;transition:all .22s ease;">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width:46px;height:46px;border-radius:12px;background:${col.icon}20;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="feather-file-text" style="color:${col.icon};font-size:20px;"></i>
                        </div>
                        <div class="fw-bold text-dark" style="font-size:.95rem;line-height:1.3;">${esc(c.name)}</div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div style="font-size:.75rem;color:${col.icon};opacity:.75;margin-top:2px;">${branchCount} Branch${branchCount !== 1 ? 'es' : ''}</div>
                        </div>
                        <div style="width:32px;height:32px;border-radius:50%;background:${col.icon};display:flex;align-items:center;justify-content:center;">
                            <i class="feather-arrow-right" style="color:#fff;font-size:14px;"></i>
                        </div>
                    </div>
                </div>
            </div>`;
        }).join('');

        row.querySelectorAll('.course-card').forEach(card => {
            card.addEventListener('click', function () {
                showBranchView(this.dataset.courseId, this.dataset.courseName);
            });
        });
    }

    // ── RENDER BRANCH CARDS ──────────────────────────────────────
   function renderBranchCards(courseId) {
    const row    = document.getElementById('branchCardsRow');
    const course = ALL_COURSES.find(c => String(c.id) === String(courseId));

    const courseStreamIds = (course?.streams || []).map(s => String(s.id));
    const countMap = {};

    ALL_DATA.forEach(e => {
        if (
            e.stream_id &&
            courseStreamIds.includes(String(e.stream_id)) &&
            String(e.course_id) === String(courseId)
        ) {
            countMap[String(e.stream_id)] = (countMap[String(e.stream_id)] || 0) + 1;
        }
    });

   
    const branches = (course?.streams || []).map(s => ({
        id:    s.id,
        name:  s.name,
        count: countMap[String(s.id)] || 0,
    }));

    if (!branches.length) {
        row.innerHTML = `<div class="col-12 text-center py-5">
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
        return `<div class="col-sm-6 col-md-4 col-xl-3">
            <div class="branch-card h-100"
                 data-branch-id="${b.id}"
                 data-branch-name="${esc(b.name)}"
                 style="background:${c.bg};border:1.5px solid ${c.border};border-radius:14px;padding:1.5rem;cursor:pointer;transition:all .22s ease;">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width:46px;height:46px;border-radius:12px;background:${c.icon}20;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="feather-git-branch" style="color:${c.icon};font-size:20px;"></i>
                    </div>
                    <div class="fw-bold text-dark" style="font-size:.95rem;line-height:1.3;">${esc(b.name)}</div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span style="font-size:1.75rem;font-weight:700;color:${c.icon};line-height:1;">${b.count}</span>
                        <span class="text-muted ms-1" style="font-size:.8rem;">Paper${b.count !== 1 ? 's' : ''}</span>
                    </div>
                    <div style="width:32px;height:32px;border-radius:50%;background:${c.icon};display:flex;align-items:center;justify-content:center;">
                        <i class="feather-arrow-right" style="color:#fff;font-size:14px;"></i>
                    </div>
                </div>
            </div>
        </div>`;
    }).join('');

    row.querySelectorAll('.branch-card').forEach(card => {
        card.addEventListener('click', function () {
            openBranch(this.dataset.branchId, this.dataset.branchName);
        });
    });
}

    // ── RENDER QP TABLE ──────────────────────────────────────────
    function render() {
        const totalPages = Math.max(1, Math.ceil(filteredQPs.length / PAGE_SIZE));
        if (currentPage > totalPages) currentPage = totalPages;
        const start = (currentPage - 1) * PAGE_SIZE;
        renderTable(filteredQPs.slice(start, start + PAGE_SIZE), start);
        renderPagination(totalPages);
    }

    function renderTable(paged, start) {
        const tbody      = document.getElementById('qpTbody');
        const pagination = document.getElementById('qpPagination');

        if (!paged.length) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center py-5">
                <i class="feather-file-text text-muted mb-3 d-block" style="font-size:3rem;"></i>
                <h6 class="text-muted">No question papers found</h6>
            </td></tr>`;
            pagination.classList.add('d-none');
            return;
        }

        tbody.innerHTML = paged.map((e, i) => {
            const statusBadge = e.status
                ? `<span class="badge bg-success">Active</span>`
                : `<span class="badge bg-danger">Inactive</span>`;

            const viewBtn = `<button class="avatar-text avatar-md text-info border-0 bg-transparent view-questions-btn"
                                data-qb-id="${e.id}" data-qb-name="${esc(e.name)}" title="View Questions">
                                <i class="feather-eye"></i></button>`;

            const nextBtn = `<a href="${e.next_url}" class="avatar-text avatar-md text-primary" title="Add Questions">
                                <i class="feather-arrow-right-circle"></i></a>`;

            const editBtn = CAN_EDIT
                ? `<a href="${e.edit_url}" class="avatar-text avatar-md text-warning" title="Edit">
                        <i class="feather-edit-3"></i></a>`
                : '';

            const deleteBtn = CAN_DELETE
                ? `<form action="${e.delete_url}" method="POST" class="d-inline"
                        onsubmit="return confirm('Delete this question paper?')">
                        <input type="hidden" name="_token" value="${CSRF_TOKEN}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="avatar-text avatar-md border-0 bg-transparent text-danger" title="Delete">
                            <i class="feather-trash-2"></i></button>
                    </form>`
                : '';

            return `<tr>
                <td>${start + i + 1}</td>
                <td class="fw-medium">${esc(e.name)}</td>
                <td><small class="text-muted">${esc(e.class_name)}</small></td>
                <td><small class="text-muted">${esc(e.subject_name)}</small></td>
                <td><small class="text-muted">${e.description ? esc(e.description) : '—'}</small></td>
                <td>${statusBadge}</td>
                <td><div class="hstack gap-2 justify-content-center">${viewBtn}${nextBtn}${editBtn}${deleteBtn}</div></td>
            </tr>`;
        }).join('');

        document.querySelectorAll('.view-questions-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                openQuestionsModal(this.dataset.qbId, this.dataset.qbName);
            });
        });

        pagination.classList.toggle('d-none', filteredQPs.length <= PAGE_SIZE);
        document.getElementById('paginationInfo').textContent =
            `Showing ${start + 1}–${Math.min(start + paged.length, filteredQPs.length)} of ${filteredQPs.length}`;
    }

    function renderPagination(totalPages) {
        if (totalPages <= 1) { document.getElementById('qpPagination').classList.add('d-none'); return; }
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

    // ── QUESTIONS MODAL ──────────────────────────────────────────
    function openQuestionsModal(qbId, qbName) {
        document.getElementById('questionsModalTitle').textContent    = qbName;
        document.getElementById('questionsModalSubtitle').textContent = 'Question Bank Questions';
        document.getElementById('totalQCount').textContent   = '—';
        document.getElementById('totalMarks').textContent    = '—';
        document.getElementById('totalNegMarks').textContent = '—';
        document.getElementById('modalLoading').classList.remove('d-none');
        document.getElementById('modalTableWrap').classList.add('d-none');
        document.getElementById('modalEmpty').classList.add('d-none');

        const modal = new bootstrap.Modal(document.getElementById('questionsModal'));
        modal.show();

        fetch(`${QUESTIONS_API_URL}?question_bank_id=${qbId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('modalLoading').classList.add('d-none');
            const questions = data.questions;

            if (!questions || !questions.length) {
                document.getElementById('totalQCount').textContent   = '0';
                document.getElementById('totalMarks').textContent    = '0';
                document.getElementById('totalNegMarks').textContent = '0';
                document.getElementById('modalEmpty').classList.remove('d-none');
                return;
            }

            let sumMarks = 0, sumNeg = 0;
            questions.forEach(q => {
                sumMarks += parseFloat(q.mark           || 0);
                sumNeg   += parseFloat(q.negative_marks  || 0);
            });
            document.getElementById('totalQCount').textContent   = questions.length;
            document.getElementById('totalMarks').textContent    = sumMarks;
            document.getElementById('totalNegMarks').textContent = sumNeg;
            document.getElementById('modalTableWrap').classList.remove('d-none');

            const typeColors = { mcq: 'primary', true_false: 'info', short_answer: 'warning', long_answer: 'secondary' };
            const typeLabels = { mcq: 'MCQ', true_false: 'True/False', short_answer: 'Short Ans', long_answer: 'Long Ans' };

            document.getElementById('modalTbody').innerHTML = questions.map((q, i) => {
                const typeBadge   = `<span class="badge bg-${typeColors[q.type] ?? 'secondary'}">${typeLabels[q.type] ?? q.type}</span>`;
                const statusBadge = q.is_active
                    ? `<span class="badge bg-success">Active</span>`
                    : `<span class="badge bg-danger">Inactive</span>`;

                let optionsHtml = '';
                if (q.type === 'mcq' && q.options?.length) {
                    optionsHtml = '<div class="mt-1">' + q.options.map(opt =>
                        `<small class="${opt.is_correct ? 'text-success fw-bold' : 'text-muted'}">
                            <i class="feather-${opt.is_correct ? 'check-circle' : 'circle'} me-1"></i>${esc(opt.option_text)}
                        </small><br>`
                    ).join('') + '</div>';
                }
                if (q.type === 'true_false' && q.options?.length) {
                    const correct = q.options.find(o => o.is_correct);
                    optionsHtml = `<small class="text-muted ms-1">Correct: <strong>${esc(correct?.option_text ?? '—')}</strong></small>`;
                }

                const editUrl   = "{{ url('admin/question-paper-types') }}/" + q.id + "/edit";
                const deleteUrl = "{{ url('admin/question-paper-types') }}/" + q.id;

                return `<tr>
                    <td>${i + 1}</td>
                    <td>${typeBadge}</td>
                    <td>
                        <div class="text-truncate" style="max-width:300px;" title="${esc(q.question)}">${esc(q.question)}</div>
                        ${optionsHtml}
                    </td>
                    <td><span class="badge bg-success">${q.mark}</span></td>
                    <td><span class="badge ${q.negative_marks > 0 ? 'bg-danger' : 'bg-light text-dark'}">${q.negative_marks ?? 0}</span></td>
                    <td>${statusBadge}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="${editUrl}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="feather-edit"></i></a>
                            <form action="${deleteUrl}" method="POST" onsubmit="return confirm('Delete this question?')">
                                <input type="hidden" name="_token" value="${CSRF_TOKEN}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="feather-trash-2"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>`;
            }).join('');
        })
        .catch(() => {
            document.getElementById('modalLoading').classList.add('d-none');
            document.getElementById('modalEmpty').classList.remove('d-none');
            document.getElementById('modalEmpty').innerHTML = '<p class="text-danger py-4">Failed to load questions.</p>';
        });
    }

    // ── UTILITY ──────────────────────────────────────────────────
    function esc(str) {
        if (str == null) return '—';
        return String(str)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    showCourseView();
});
</script>

<style>
    .course-card, .branch-card { cursor:pointer; transition:all .22s ease; }
    .course-card:hover, .branch-card:hover { transform:translateY(-4px); box-shadow:0 8px 24px rgba(0,0,0,.1); }
    .course-card:active, .branch-card:active { transform:scale(.97) !important; }
    .table th { white-space:nowrap; font-size:0.8125rem; padding:0.75rem 1rem; }
    .table td { padding:0.75rem 1rem; vertical-align:middle; font-size:0.875rem; }
    .content-area { overflow-y:auto !important; }
    .avatar-text { display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; transition:all 0.2s; cursor:pointer; }
    .avatar-text:hover { background-color:rgba(0,0,0,0.05); }
    .hstack { display:flex; flex-direction:row; align-items:center; }
    .gap-2 { gap:0.5rem; }
    .gap-3 { gap:1rem; }
</style>

@endsection