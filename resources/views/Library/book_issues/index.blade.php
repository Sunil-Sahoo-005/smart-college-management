@extends('admin_layout')
@section('content')

<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">

        @include('Library.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            {{-- Header --}}
            <div class="content-area-header bg-white sticky-top">
                <div class="page-header-left d-flex align-items-center gap-3">
                    <h6 class="mb-0">Book Issues</h6>
                </div>
                <div class="page-header-right ms-auto">
                    @if(auth()->user()->hasPermissionTo('Create Book Issues'))
                    <a href="{{ route('admin.book_issues.create') }}" class="btn btn-primary btn-sm">
                        <i class="feather-plus me-1"></i> Add Issue
                    </a>
                    @endif
                </div>
            </div>

            <div class="content-area-body">
                @include('elements.flash')

                {{-- Stats Row --}}
                <div class="row g-3 mb-3">
                    <div class="col-6 col-md-4">
                        <div class="card border-0" style="background:#f9fafb;">
                            <div class="card-body py-3 px-3">
                                <p class="text-muted mb-1" style="font-size:12px;">Total Books</p>
                                <h5 class="mb-0 fw-semibold">{{ $totalBooks ?? 0 }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="card border-0" style="background:#E6F1FB;">
                            <div class="card-body py-3 px-3">
                                <p class="text-muted mb-1" style="font-size:12px;">Total Issued</p>
                                <h5 class="mb-0 fw-semibold" style="color:#0C447C;">
                                    {{ $totalIssued ?? 0 }}
                                </h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="card border-0" style="background:#EAF3DE;">
                            <div class="card-body py-3 px-3">
                                <p class="text-muted mb-1" style="font-size:12px;">Returned</p>
                                <h5 class="mb-0 fw-semibold" style="color:#27500A;">
                                    {{ $totalReturned ?? 0 }}
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card stretch stretch-full">

                    {{-- Filter Bar --}}
                    <div class="card-header border-bottom py-3">
                        <form method="GET" action="{{ route('admin.book_issues.index') }}"
                              class="d-flex flex-wrap align-items-center gap-3 w-100">

                            {{-- Search --}}
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-medium" style="font-size:13px; color:#374151;">Search</span>
                                <div class="position-relative" style="min-width: 320px;">
                                    <i class="feather-search position-absolute"
                                       style="left: 10px; top: 50%; transform: translateY(-50%); font-size: 14px; color: #9ca3af;"></i>
                                    <input type="text"
                                           name="search"
                                           class="form-control form-control-sm"
                                           style="padding-left: 32px; font-size:13px; height: 34px; border:1px solid #e5e7eb; border-radius:6px; width: 320px;"
                                           placeholder="Search by book title, ISBN, author, student name, staff name, email..."
                                           value="{{ $search }}">
                                </div>
                            </div>

                            {{-- Category Filter --}}
                            <div class="d-flex align-items-center gap-2">
                                <select name="category"
                                        class="form-select form-select-sm"
                                        style="width:160px; font-size:13px; height: 45px; border:1px solid #e5e7eb; background:#f9fafb; border-radius:6px;">
                                    <option value="">All Categories</option>
                                    @foreach($categories ?? [] as $category)
                                    <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Status Filter --}}
                            <div class="d-flex align-items-center gap-2">
                                <select name="book_status"
                                        class="form-select form-select-sm"
                                        style="width:140px; font-size:13px; height: 45px; border:1px solid #e5e7eb; background:#f9fafb; border-radius:6px;">
                                    <option value="">Any Status</option>
                                    <option value="issued"   {{ request('book_status') == 'issued'   ? 'selected' : '' }}>Issued</option>
                                    <option value="returned" {{ request('book_status') == 'returned' ? 'selected' : '' }}>Returned</option>
                                </select>
                            </div>

                            {{-- Date Range Filter --}}
                            <div class="d-flex align-items-center gap-2 border rounded-2 px-3 py-1"
                                 style="background:#f9fafb; height:34px;">
                                <input type="date"
                                       name="date_from"
                                       class="border-0 bg-transparent p-0"
                                       style="font-size:12px; outline:none; width:120px; cursor: pointer;"
                                       value="{{ request('date_from') }}">
                                <span class="text-muted" style="font-size:12px;">—</span>
                                <input type="date"
                                       name="date_to"
                                       class="border-0 bg-transparent p-0"
                                       style="font-size:12px; outline:none; width:120px; cursor: pointer;"
                                       value="{{ request('date_to') }}">
                            </div>

                            {{-- Buttons --}}
                            <div class="d-flex align-items-center gap-2">
                                <button type="submit" class="btn btn-sm btn-primary d-flex align-items-center gap-1"
                                        style="height:34px; font-size:13px; padding:0 16px;">
                                    <i class="feather-filter" style="font-size:12px;"></i> Search
                                </button>
                                <a href="{{ route('admin.book_issues.index') }}"
                                   class="btn btn-sm btn-outline-secondary"
                                   style="height:34px; font-size:13px; padding:0 16px;">
                                    Clear
                                </a>
                            </div>

                        </form>
                    </div>

                    <div class="card-body p-0">
                        @if ($book_issues->isEmpty())
                            <div class="text-center py-5">
                                <i class="feather-book-open text-muted mb-2" style="font-size:32px; display:block;"></i>
                                <p class="text-muted mb-0">No book issues found.</p>
                                @if($search || request('status') || request('date_from') || request('date_to'))
                                    <a href="{{ route('admin.book_issues.index') }}"
                                       class="btn btn-sm btn-outline-secondary mt-2">
                                        Clear filters
                                    </a>
                                @endif
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr style="background:#f9fafb;">
                                            <th class="ps-3" style="font-size:11px; font-weight:500; color:#9ca3af; letter-spacing:.04em; padding:10px 12px;">BOOK</th>
                                            <th style="font-size:11px; font-weight:500; color:#9ca3af; letter-spacing:.04em; padding:10px 12px;">BORROWER</th>
                                            <th style="font-size:11px; font-weight:500; color:#9ca3af; letter-spacing:.04em; padding:10px 12px;">ISSUE DATE</th>
                                            <th style="font-size:11px; font-weight:500; color:#9ca3af; letter-spacing:.04em; padding:10px 12px;">DUE DATE</th>
                                            <th style="font-size:11px; font-weight:500; color:#9ca3af; letter-spacing:.04em; padding:10px 12px;">RETURN DATE</th>
                                            <th style="font-size:11px; font-weight:500; color:#9ca3af; letter-spacing:.04em; padding:10px 12px;">STATUS</th>
                                            <th class="text-end pe-3" style="font-size:11px; font-weight:500; color:#9ca3af; letter-spacing:.04em; padding:10px 12px;">ACTIONS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($book_issues as $issue)
                                        <tr>
                                            <td class="ps-3">
                                                <p class="mb-0 fw-medium" style="font-size:13px;">
                                                    {{ $issue->book->title ?? '-' }}
                                                </p>
                                                <small class="text-muted">{{ $issue->book->author ?? '' }}</small>
                                                <br>
                                                <small class="text-muted" style="font-size:10px;">{{ $issue->book->edition ?? '' }} edition</small>
                                            </td>

                                            <td>
                                                @php
                                                    $borrower = $issue->student ?? $issue->staff;
                                                    $isStaff  = is_null($issue->student);
                                                    $initials = $borrower
                                                        ? strtoupper(substr($borrower->first_name,0,1).substr($borrower->last_name,0,1))
                                                        : '?';
                                                    $name = $borrower
                                                        ? $borrower->first_name.' '.$borrower->last_name
                                                        : '-';
                                                @endphp
                                                <div class="d-flex align-items-center gap-2">
                                                    <div style="width:26px; height:26px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:500; flex-shrink:0;
                                                         background:{{ $isStaff ? '#EAF3DE' : '#E6F1FB' }};
                                                         color:{{ $isStaff ? '#27500A' : '#0C447C' }};">
                                                        {{ $initials }}
                                                    </div>
                                                    <div>
                                                        <p class="mb-0" style="font-size:12px;">{{ $name }}</p>
                                                        <small class="text-muted">{{ $isStaff ? 'Staff' : 'Student' }}</small>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="text-muted" style="font-size:12px;">
                                                {{ \Carbon\Carbon::parse($issue->issue_date)->format('d M Y') }}
                                            </td>

                                            <td style="font-size:12px;">
                                                <span style="{{ $issue->isOverdue() ? 'color:#791F1F; font-weight:500;' : 'color:#6b7280;' }}">
                                                    {{ \Carbon\Carbon::parse($issue->due_date)->format('d M Y') }}
                                                </span>
                                            </td>

                                            <td class="text-muted" style="font-size:12px;">
                                                {{ $issue->return_date ? \Carbon\Carbon::parse($issue->return_date)->format('d M Y') : '—' }}
                                            </td>

                                            <td>
                                                @php
                                                    $badge = match($issue->status) {
                                                        'issued'   => ['bg:#E6F1FB', 'color:#0C447C'],
                                                        'returned' => ['bg:#EAF3DE', 'color:#27500A'],
                                                        'overdue'  => ['bg:#FCEBEB', 'color:#791F1F'],
                                                        default    => ['bg:#f3f4f6', 'color:#6b7280'],
                                                    };
                                                @endphp
                                                <span style="font-size:11px; padding:3px 10px; border-radius:99px; font-weight:500;
                                                             background:{{ str_replace('bg:', '', $badge[0]) }};
                                                             color:{{ str_replace('color:', '', $badge[1]) }};">
                                                    {{ ucfirst($issue->status) }}
                                                </span>
                                            </td>

                                            <td class="text-end pe-3">
                                                <div class="hstack gap-1 justify-content-end">
                                                    @if(auth()->user()->hasPermissionTo('Edit Book Issues'))
                                                        <a href="{{ route('admin.book_issues.edit', $issue) }}"
                                                           class="avatar-text avatar-sm" title="Edit">
                                                            <i class="feather-edit-3"></i>
                                                        </a>
                                                    @endif
                                                    @if(auth()->user()->hasPermissionTo('Delete Book Issues'))
                                                        <form action="{{ route('admin.book_issues.destroy', $issue) }}"
                                                              method="POST"
                                                              onsubmit="return confirm('Delete this record?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="avatar-text avatar-sm border-0 bg-transparent text-danger"
                                                                    title="Delete">
                                                                <i class="feather-trash-2"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- SLIDING PAGINATION (same as books index) --}}
                            <div class="d-flex align-items-center justify-content-between px-3 py-3 border-top">
                                <small class="text-muted">
                                    Showing {{ $book_issues->firstItem() }}–{{ $book_issues->lastItem() }}
                                    of {{ $book_issues->total() }} records
                                </small>

                                @if ($book_issues->lastPage() > 1)
                                <div class="d-flex gap-1 align-items-center">
                                    {{-- Previous --}}
                                    @if ($book_issues->onFirstPage())
                                        <button class="books-page-btn" disabled>‹</button>
                                    @else
                                        <a href="{{ $book_issues->previousPageUrl() }}" class="books-page-btn">‹</a>
                                    @endif

                                    @php
                                        $current = $book_issues->currentPage();
                                        $last    = $book_issues->lastPage();
                                        $pages   = [];

                                        if ($last <= 4) {
                                            for ($i = 1; $i <= $last; $i++) $pages[] = $i;
                                        } else {
                                            $pages[] = $current;
                                            if ($current + 1 <= $last) $pages[] = $current + 1;
                                            $lastTwo = [$last - 1, $last];
                                            foreach ($lastTwo as $p) {
                                                if (!in_array($p, $pages) && $p >= 1) $pages[] = $p;
                                            }
                                            $pages = array_unique($pages);
                                            sort($pages);
                                            $temp = [];
                                            $prev = null;
                                            foreach ($pages as $page) {
                                                if ($prev !== null && $page - $prev > 1) $temp[] = '...';
                                                $temp[] = $page;
                                                $prev = $page;
                                            }
                                            $pages = $temp;
                                        }
                                    @endphp

                                    @foreach ($pages as $page)
                                        @if ($page === '...')
                                            <span class="books-page-dots">…</span>
                                        @elseif ($page == $current)
                                            <button class="books-page-btn books-page-active">{{ $page }}</button>
                                        @else
                                            <a href="{{ $book_issues->url($page) }}" class="books-page-btn">{{ $page }}</a>
                                        @endif
                                    @endforeach

                                    {{-- Next --}}
                                    @if ($book_issues->hasMorePages())
                                        <a href="{{ $book_issues->nextPageUrl() }}" class="books-page-btn">›</a>
                                    @else
                                        <button class="books-page-btn" disabled>›</button>
                                    @endif
                                </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Pagination styles (same as books index) */
.books-page-btn {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 30px; height: 30px; padding: 0 8px;
    border-radius: 6px;
    border: 0.5px solid #dee2e6;
    background: transparent; color: #495057;
    font-size: 13px; text-decoration: none;
    cursor: pointer;
    transition: background .12s, color .12s, border-color .12s;
}
.books-page-btn:hover:not(:disabled) {
    background: #E6F1FB; color: #185FA5; border-color: #B5D4F4;
}
.books-page-btn:disabled { opacity: 0.4; cursor: not-allowed; }
.books-page-active {
    background: #185FA5 !important;
    color: #fff !important;
    border-color: #185FA5 !important;
}
.books-page-dots {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 30px; height: 30px;
    font-size: 13px; color: #adb5bd;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateFrom = document.querySelector('input[name="date_from"]');
    const dateTo   = document.querySelector('input[name="date_to"]');
    if (dateFrom && !dateFrom.value) dateFrom.value = '';
    if (dateTo   && !dateTo.value)   dateTo.value   = '';
});
</script>
@endsection