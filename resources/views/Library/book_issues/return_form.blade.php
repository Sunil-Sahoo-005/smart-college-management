@extends('admin_layout')
@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Library.sidebar')
        <div class="content-area" data-scrollbar-target="#psScrollbarInit">
            <div class="content-area-header bg-white sticky-top">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">
                        <i class="feather-corner-up-left me-2 text-success"></i>Return Books
                    </h6>
                    <a href="{{ route('admin.book_issues.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="feather-list me-1"></i>All Issues
                    </a>
                </div>
            </div>

            <div class="content-area-body">
                @include('elements.flash')

                <div class="row g-4">
                    {{-- LEFT: Borrower Selection --}}
                    <div class="col-lg-4">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 small"><i class="feather-user me-2"></i>Select Borrower</h6>
                            </div>
                            <div class="card-body p-3">

                                {{-- Student Card --}}
                                <div class="mb-2">
                                    <label class="form-label small mb-1 fw-semibold">Student Card #</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" id="cardNumber" class="form-control"
                                               placeholder="e.g. SC-00123" oninput="searchStudent()">
                                    </div>
                                    <div id="studentInfo" class="mt-2" style="display:none;"></div>
                                </div>

                                {{-- Divider --}}
                                <div class="text-center text-muted my-3">
                                    <div style="border-top:1px solid #e5e7eb; position:relative;">
                                        <span class="px-2 bg-white text-muted small"
                                              style="position:absolute;top:-10px;left:50%;transform:translateX(-50%);">OR</span>
                                    </div>
                                </div>

                                {{-- Staff Dropdown --}}
                                <div class="mb-3" style="margin-top:10px;">
                                    <label class="form-label small mb-1 fw-semibold">Staff Member</label>
                                    <select id="staffSelect" class="form-select form-select-sm" onchange="selectStaff(this.value)">
                                        <option value="">Choose staff member...</option>
                                        @foreach($staffs as $staff)
                                            <option value="{{ $staff->id }}">
                                                {{ $staff->first_name }} {{ $staff->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Borrower Info --}}
                                <div id="borrowerInfo" style="display:none;">
                                    <hr class="my-3">
                                    <div class="d-flex align-items-center gap-2 p-2 rounded-3 mb-3"
                                         style="background:#f0fdf4; border:1px solid #bbf7d0;">
                                        <div id="borrowerAvatar"
                                             class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                             style="width:36px;height:36px;font-size:13px;flex-shrink:0;">JS</div>
                                        <div class="flex-grow-1">
                                            <div id="borrowerName" class="fw-semibold small"></div>
                                            <div id="borrowerMeta" class="text-muted" style="font-size:11px;"></div>
                                        </div>
                                        <span class="badge bg-success" style="font-size:10px;">Selected</span>
                                    </div>

                                    {{-- Change Borrower + Load Books side by side --}}
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-outline-danger btn-sm flex-fill" onclick="clearBorrower()">
                                            <i class="feather-refresh-cw me-1"></i>Change Borrower
                                        </button>
                                        <button class="btn btn-primary btn-sm flex-fill" id="loadBooksBtn" onclick="loadBorrowerBooks()">
                                            <i class="feather-book-open me-1"></i>Load Issued Books
                                        </button>
                                    </div>
                                </div>

                                {{-- Load hint when no borrower selected --}}
                                <div class="mt-3" id="loadHintBox">
                                    <button class="btn btn-primary w-100 btn-sm" disabled>
                                        <i class="feather-book-open me-1"></i>Load Their Issued Books
                                    </button>
                                    <div class="text-muted small text-center mt-1">
                                        Select student OR staff first
                                    </div>
                                </div>

                                <input type="hidden" id="studentId">
                                <input type="hidden" id="staffId">
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT: Issued Books --}}
                    <div class="col-lg-8">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="feather-book-open me-2"></i>
                                    Issued Books
                                    <span id="booksCount" class="badge bg-primary ms-2">0</span>
                                </h6>
                            </div>
                            <div class="card-body p-0">
                                <div id="booksContainer" style="min-height:450px;max-height:500px;overflow-y:auto;">
                                    <div class="text-center py-5 text-muted">
                                        <i class="feather-user-plus display-4 mb-3 opacity-50"></i>
                                        <h6>Select a borrower first</h6>
                                        <p class="mb-0 small">Search student/staff → Load Books → Select to return</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Selected Books Tray --}}
                            <div class="card-footer bg-light border-top" id="selectedTray" style="display:none;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">Selected Books
                                        <span id="selectedCount" class="badge bg-success">0</span>
                                    </h6>
                                    <button class="btn btn-sm btn-outline-danger" onclick="clearSelected()">
                                        <i class="feather-trash-2 me-1"></i>Clear All
                                    </button>
                                </div>
                                <div id="selectedBooksList" class="small"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="card mt-4">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div id="previewText" class="text-muted" style="display:none;">
                                <strong>Returning <span id="previewCount">0</span> books</strong>
                                <span class="text-success ms-2">→ Ready to return!</span>
                            </div>
                            <button class="btn btn-success px-4 py-2" id="returnBtn" onclick="submitReturn()" disabled>
                                <i class="feather-check-circle me-2"></i>Return Selected Books
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Hidden Form --}}
<form id="returnForm" method="POST" action="{{ route('admin.book_issues.return.store') }}" style="display:none;">
    @csrf
    <input type="hidden" name="student_id" id="fStudentId">
    <input type="hidden" name="staff_id" id="fStaffId">
    <input type="hidden" name="books" id="fBooks">
</form>

<script>
let selectedBooks    = [];
let currentBorrower  = null;
let currentBooksList = [];

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('cardNumber').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') searchStudent();
    });
});

function searchStudent() {
    const card = document.getElementById('cardNumber').value.trim();
    if (!card) return;

    document.getElementById('studentInfo').style.display = 'block';
    document.getElementById('studentInfo').innerHTML =
        '<div class="text-center p-2 small"><div class="spinner-border spinner-border-sm me-2"></div>Searching...</div>';

    fetch(`{{ route('admin.book_issues.return.search_student') }}?card_number=${encodeURIComponent(card)}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('studentId').value = data.student.id;
                document.getElementById('staffId').value   = '';
                document.getElementById('staffSelect').value = '';

                currentBorrower = {
                    name: data.student.name,
                    meta: `Card: ${data.student.card_number}${data.student.expiry_date ? ' · Expires ' + data.student.expiry_date : ''}`
                };

                showBorrowerInfo();
                document.getElementById('studentInfo').innerHTML =
                    '<div class="alert alert-success small p-1 mb-0">Student found ✓</div>';
            } else {
                hideBorrowerInfo();
                document.getElementById('studentInfo').innerHTML =
                    `<div class="alert alert-warning small p-2 mb-0">${data.message}</div>`;
            }
        }).catch(() => {
            document.getElementById('studentInfo').innerHTML =
                '<div class="alert alert-danger small p-2 mb-0">Search failed</div>';
        });
}

function selectStaff(staffId) {
    if (!staffId) {
        document.getElementById('staffId').value   = '';
        document.getElementById('studentId').value = '';
        hideBorrowerInfo();
        return;
    }

    document.getElementById('staffId').value    = staffId;
    document.getElementById('studentId').value  = '';
    document.getElementById('cardNumber').value = '';
    document.getElementById('studentInfo').style.display = 'none';

    const staffName = document.querySelector(`#staffSelect option[value="${staffId}"]`).textContent.trim();
    currentBorrower = { name: staffName, meta: 'Staff member' };

    showBorrowerInfo();
}

function showBorrowerInfo() {
    const initials = currentBorrower.name.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase();
    document.getElementById('borrowerAvatar').textContent = initials;
    document.getElementById('borrowerName').textContent   = currentBorrower.name;
    document.getElementById('borrowerMeta').textContent   = currentBorrower.meta;
    document.getElementById('borrowerInfo').style.display = 'block';
    document.getElementById('loadHintBox').style.display  = 'none';
}

function hideBorrowerInfo() {
    currentBorrower = null;
    document.getElementById('borrowerInfo').style.display = 'none';
    document.getElementById('loadHintBox').style.display  = 'block';
}

function loadBorrowerBooks() {
    document.getElementById('booksContainer').innerHTML =
        '<div class="text-center py-4"><div class="spinner-border text-primary" style="width:3rem;height:3rem;"></div><p class="mt-3 small">Loading issued books...</p></div>';

    fetch(`{{ route('admin.book_issues.return.search_books') }}?` + new URLSearchParams({
        student_id: document.getElementById('studentId').value,
        staff_id:   document.getElementById('staffId').value
    }))
    .then(res => res.json())
    .then(data => {
        if (data.error) {
            document.getElementById('booksContainer').innerHTML =
                `<div class="alert alert-warning text-center py-5"><i class="feather-alert-triangle me-2"></i>${data.error}</div>`;
            return;
        }
        currentBooksList = data;
        renderBooksList(data);
    }).catch(() => {
        document.getElementById('booksContainer').innerHTML =
            '<div class="alert alert-danger text-center py-5"><i class="feather-alert-circle me-2"></i>Failed to load books</div>';
    });
}

function renderBooksList(books) {
    const container = document.getElementById('booksContainer');
    document.getElementById('booksCount').textContent = books.length;

    if (!books || books.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5 text-muted">
                <i class="feather-book-open display-4 mb-3 opacity-50"></i>
                <h6 class="mb-2">No issued books found</h6>
                <p class="mb-0 small">This borrower has no active book issues</p>
            </div>`;
        document.getElementById('selectedTray').style.display = 'none';
        return;
    }

    container.innerHTML = books.map(book => {
        const img = book.book_image ?
            `<img src="${book.book_image}" style="width:50px;height:65px;object-fit:cover;border-radius:6px;">` :
            '<div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:50px;height:65px;"><i class="feather-book text-muted fs-4"></i></div>';

        const isSelected = selectedBooks.some(b => b.id === book.id);

        return `
        <div class="card mb-2 book-item ${isSelected ? 'border-success' : 'border-light'}">
            <div class="card-body p-3">
                <div class="d-flex align-items-start gap-3">
                    <div>${img}</div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1 fw-semibold">${book.title}</h6>
                        <div class="small text-muted mb-2">${book.author} • Copy #${book.copy_number}</div>
                        <div class="d-flex flex-wrap gap-1">
                            <span class="badge bg-light text-dark">Due ${book.due_date}</span>
                        </div>
                    </div>
                    <button class="btn btn-sm ${isSelected ? 'btn-success' : 'btn-outline-success'} ms-auto"
                            onclick="toggleBook(${JSON.stringify(book).replace(/"/g, '&quot;')})">
                        ${isSelected ? '✓ Selected' : '+ Return'}
                    </button>
                </div>
            </div>
        </div>`;
    }).join('');

    document.getElementById('selectedTray').style.display = 'block';
    renderSelectedList();
    updateFooter();
}

function toggleBook(book) {
    const index = selectedBooks.findIndex(b => b.id === book.id);
    if (index > -1) selectedBooks.splice(index, 1);
    else selectedBooks.push(book);

    renderBooksList(currentBooksList);
    renderSelectedList();
    updateFooter();
}

function renderSelectedList() {
    const container = document.getElementById('selectedBooksList');
    document.getElementById('selectedCount').textContent = selectedBooks.length;

    if (selectedBooks.length === 0) {
        container.innerHTML = '<div class="text-muted small text-center py-3">No books selected for return</div>';
        return;
    }

    container.innerHTML = selectedBooks.map(b => `
        <div class="d-flex justify-content-between align-items-center p-2 bg-white border rounded mb-1">
            <div class="d-flex align-items-center gap-2 flex-grow-1">
                <div class="bg-primary text-white rounded px-2 py-1 small fw-bold">${b.title.slice(0,2).toUpperCase()}</div>
                <div class="small">
                    <div class="fw-semibold">${b.title}</div>
                    <div class="text-muted">Copy #${b.copy_number}</div>
                </div>
            </div>
            <button class="btn btn-sm btn-outline-danger ms-1" onclick="removeBook(${b.id})">×</button>
        </div>
    `).join('');
}

function removeBook(id) {
    selectedBooks = selectedBooks.filter(b => b.id !== id);
    renderBooksList(currentBooksList);
    renderSelectedList();
    updateFooter();
}

function updateFooter() {
    const ready = selectedBooks.length > 0 && currentBorrower;
    const btn   = document.getElementById('returnBtn');

    if (ready) {
        document.getElementById('previewCount').textContent  = selectedBooks.length;
        document.getElementById('previewText').style.display = 'block';
        btn.disabled  = false;
        btn.innerHTML = `<i class="feather-check-circle me-2"></i>Return ${selectedBooks.length} Book${selectedBooks.length !== 1 ? 's' : ''}`;
    } else {
        document.getElementById('previewText').style.display = 'none';
        btn.disabled  = true;
        btn.innerHTML = '<i class="feather-check-circle me-2"></i>Return Selected Books';
    }
}

function submitReturn() {
    if (selectedBooks.length === 0)  return alert('Please select books to return');
    if (!currentBorrower)            return alert('Please select borrower first');

    document.getElementById('fStudentId').value = document.getElementById('studentId').value;
    document.getElementById('fStaffId').value   = document.getElementById('staffId').value;
    document.getElementById('fBooks').value      = JSON.stringify(selectedBooks.map(b => ({ book_id: b.id })));
    document.getElementById('returnForm').submit();
}

function clearBorrower() {
    currentBorrower  = null;
    selectedBooks    = [];
    currentBooksList = [];

    document.getElementById('studentId').value    = '';
    document.getElementById('staffId').value      = '';
    document.getElementById('cardNumber').value   = '';
    document.getElementById('staffSelect').value  = '';
    document.getElementById('studentInfo').style.display = 'none';

    hideBorrowerInfo();

    document.getElementById('booksContainer').innerHTML = `
        <div class="text-center py-5 text-muted">
            <i class="feather-user-plus display-4 mb-3 opacity-50"></i>
            <h6 class="mb-2">Select a borrower first</h6>
            <p class="mb-0 small">1. Search student OR select staff<br>2. Click "Load Their Issued Books"<br>3. Select books to return</p>
        </div>`;
    document.getElementById('selectedTray').style.display = 'none';
    updateFooter();
}

function clearSelected() {
    selectedBooks = [];
    renderSelectedList();
    updateFooter();
    renderBooksList(currentBooksList);
}
</script>

<style>
.book-item { transition: all 0.2s ease; }
.book-item:hover { transform: translateY(-2px); }
#booksContainer { background: #f8fafc; }
.btn:disabled { opacity: 0.6; cursor: not-allowed; }
</style>
@endsection