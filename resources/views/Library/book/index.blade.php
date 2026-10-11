@extends('admin_layout')
@section('content')

<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Library.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            {{-- TOP BAR --}}
            <div class="content-area-header bg-white sticky-top border-bottom px-4"
                 style="height:56px; display:flex; align-items:center; justify-content:space-between;">

                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted small">Library</span>
                    <span class="text-muted small">/</span>
                    <span class="fw-medium" style="font-size:14px;">Books</span>
                </div>

                <div class="d-flex align-items-center gap-2">
                    @if (auth()->user()->hasPermissionTo('View Book'))
                        <a href="{{ route('admin.books.scan') }}"
                           class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1">
                            <i class="feather-camera" style="font-size:13px;"></i> Scan barcode
                        </a>
                    @endif
                    <a href="{{ route('admin.books.import') }}"
                       class="btn btn-sm btn-outline-success d-flex align-items-center gap-1">
                        <i class="feather-upload" style="font-size:13px;"></i> Import
                    </a>
                    @if (auth()->user()->hasPermissionTo('Create Book'))
                        <a href="{{ route('admin.books.create') }}"
                           class="btn btn-sm btn-primary d-flex align-items-center gap-1">
                            <i class="feather-plus" style="font-size:13px;"></i> Add book
                        </a>
                    @endif
                </div>
            </div>

            <div class="content-area-body p-4">
                @include('elements.flash')

                {{-- STAT CARDS --}}
                <div class="row g-3 mb-4">

                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="books-stat-card">
                            <div class="stat-icon-wrap stat-icon-default">
                                <i class="feather-book-open"></i>
                            </div>
                            <div class="stat-value">{{ number_format($statsData['total']) }}</div>
                            <div class="stat-label">Total Books</div>
                            <div class="stat-sub">across all categories</div>
                        </div>
                    </div>

                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="books-stat-card stat-blue">
                            <div class="stat-icon-wrap stat-icon-blue">
                                <i class="feather-check-circle"></i>
                            </div>
                            <div class="stat-value">{{ number_format($statsData['available']) }}</div>
                            <div class="stat-label">Available</div>
                            <div class="stat-sub">
                                {{ $statsData['total'] > 0
                                    ? number_format(($statsData['available'] / $statsData['total']) * 100, 1)
                                    : 0 }}% of total
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="books-stat-card stat-purple">
                            <div class="stat-icon-wrap stat-icon-purple">
                                <i class="feather-share-2"></i>
                            </div>
                            <div class="stat-value">{{ number_format($statsData['issued']) }}</div>
                            <div class="stat-label">Issued</div>
                            <div class="stat-sub">
                                {{ $statsData['total'] > 0
                                    ? number_format(($statsData['issued'] / $statsData['total']) * 100, 1)
                                    : 0 }}% of total
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="books-stat-card stat-orange">
                            <div class="stat-icon-wrap stat-icon-orange">
                                <i class="feather-alert-triangle"></i>
                            </div>
                            <div class="stat-value">{{ number_format($statsData['damaged']) }}</div>
                            <div class="stat-label">Damaged</div>
                            <div class="stat-sub">needs attention</div>
                        </div>
                    </div>

                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="books-stat-card stat-red">
                            <div class="stat-icon-wrap stat-icon-red">
                                <i class="feather-x-circle"></i>
                            </div>
                            <div class="stat-value">{{ number_format($statsData['lost']) }}</div>
                            <div class="stat-label">Lost</div>
                            <div class="stat-sub">reported missing</div>
                        </div>
                    </div>

                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="books-stat-card stat-teal">
                            <div class="stat-icon-wrap stat-icon-teal">
                                <i class="feather-tool"></i>
                            </div>
                            <div class="stat-value">{{ number_format($statsData['under_repair']) }}</div>
                            <div class="stat-label">Under Repair</div>
                            <div class="stat-sub">being restored</div>
                        </div>
                    </div>

                </div>

                {{-- FILTERS --}}
                <div class="books-card mb-4">
                    <form method="GET" class="row g-3 align-items-end p-3">

                        <div class="col-12 col-md-3">
                            <label class="books-label">Search</label>
                            <div class="position-relative">
                                <input type="text" name="search"
                                       class="books-input"
                                       value="{{ request('search') }}"
                                       placeholder="Title, author, ISBN…">
                                <i class="feather-search books-input-icon"></i>
                            </div>
                        </div>

                        <div class="col-6 col-md-2">
                            <label class="books-label">Category</label>
                            <select name="category" class="books-input">
                                <option value="">All categories</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat }}"
                                        {{ request('category') == $cat ? 'selected' : '' }}>
                                        {{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-6 col-md-3">
                            <label class="books-label">Academic group</label>
                            <select name="academic_group" class="books-input">
                                <option value="">All groups</option>
                                @foreach ($academicGroups as $id => $label)
                                    <option value="{{ $id }}"
                                        {{ request('academic_group') == $id ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-6 col-md-2">
                            <label class="books-label">Status</label>
                            <select name="status" class="books-input">
                                <option value="">Any status</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}"
                                        {{ request('status') == $status ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-6 col-md-2 d-flex gap-2">
                            <button type="submit"
                                    class="btn btn-primary btn-sm d-flex align-items-center gap-1 flex-grow-1 justify-content-center">
                                <i class="feather-filter" style="font-size:12px;"></i> Search
                            </button>
                            <a href="{{ route('admin.books.index') }}"
                               class="btn btn-sm btn-outline-secondary">
                                Clear
                            </a>
                        </div>

                    </form>
                </div>

                {{-- TABLE --}}
                <div class="books-card">

                    @if ($books->isEmpty())
                        <div class="text-center py-5 px-4">
                            <i class="feather-book-open"
                               style="font-size:40px; color:#adb5bd; display:block; margin-bottom:12px;"></i>
                            <h6 class="mb-1">No books found</h6>
                            <p class="text-muted small mb-4">Try adjusting your search or filters</p>
                            @if (auth()->user()->hasPermissionTo('Create Book'))
                                <a href="{{ route('admin.books.create') }}"
                                   class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1">
                                    <i class="feather-plus" style="font-size:12px;"></i> Add first book
                                </a>
                            @endif
                        </div>

                    @else

                        <div class="d-flex align-items-center justify-content-between px-3 py-2"
                             style="border-bottom: 0.5px solid #e9ecef;">
                            <div class="d-flex align-items-center gap-2">
                                <span class="books-count-badge">{{ number_format($books->total()) }} books</span>
                                @if (request()->hasAny(['search','category','academic_group','status']))
                                    <span class="text-muted" style="font-size:12px;">filtered</span>
                                @endif
                            </div>
                            <small class="text-muted">
                                Showing {{ $books->firstItem() }}–{{ $books->lastItem() }} of {{ $books->total() }} records
                            </small>
                        </div>

                        <div class="table-responsive">
                            <table class="books-table">
                                <thead>
                                    <tr>
                                        <th style="width:52px;">Cover</th>
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>Publisher</th>
                                        <th>Edition</th>
                                        <th>Category</th>
                                        <th>Academic group</th>
                                        <th>Total</th>
                                        <th>Available</th>
                                        <th style="width:48px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($books as $book)
                                        @php
                                            $total   = $book->total_copies_actual ?: 1;
                                            $pct     = round(($book->available_copies / $total) * 100);
                                            $showUrl = route('admin.books.show', base64_encode($book->book_key));
                                        @endphp
                                        <tr onclick="window.location='{{ $showUrl }}'">

                                            <td>
                                                @if ($book->book_image)
                                                    <img src="{{ Storage::url($book->book_image) }}"
                                                         alt="Cover"
                                                         class="books-cover-img">
                                                @else
                                                    <div class="books-cover-placeholder">
                                                        <i class="feather-book"></i>
                                                    </div>
                                                @endif
                                            </td>

                                            <td>
                                                <div class="books-title">{{ Str::limit($book->title, 45) }}</div>
                                                @if ($pct < 100)
                                                    <div class="books-avail-warn">{{ $pct }}% available</div>
                                                @endif
                                            </td>

                                            <td class="text-muted">{{ Str::limit($book->author, 22) }}</td>
                                            <td class="text-muted">{{ Str::limit($book->publisher, 16) }}</td>
                                            <td class="text-muted">{{ $book->edition }}</td>

                                            <td>
                                                @if ($book->category)
                                                    <span class="books-badge books-badge-category">{{ $book->category }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>

                                            <td>
                                                @if ($book->academicGroup)
                                                    <span class="books-badge books-badge-group">{{ $book->academicGroup->name }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>

                                            <td><strong>{{ $book->total_copies_actual }}</strong></td>

                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="books-avail-num {{ $book->available_copies == 0 ? 'text-danger' : '' }}">
                                                        {{ $book->available_copies }}
                                                    </span>
                                                    <span class="text-muted" style="font-size:11px;">/ {{ $book->total_copies_actual }}</span>
                                                    <div class="books-avail-bar">
                                                        <div class="books-avail-fill {{ $book->available_copies == 0 ? 'fill-danger' : '' }}"
                                                             style="width:{{ $pct }}%"></div>
                                                    </div>
                                                </div>
                                            </td>

                                            <td onclick="event.stopPropagation()">
                                                <a href="{{ $showUrl }}"
                                                   class="books-action-btn" title="View">
                                                    <i class="feather-eye"></i>
                                                </a>
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- SLIDING PAGINATION (FIXED) --}}
                        <div class="d-flex align-items-center justify-content-between px-3 py-3"
                             style="border-top: 0.5px solid #e9ecef;">
                            <small class="text-muted">
                                Showing {{ $books->firstItem() }}–{{ $books->lastItem() }}
                                of {{ $books->total() }} records
                            </small>

                            @if ($books->lastPage() > 1)
                            <div class="d-flex gap-1 align-items-center">

                                {{-- Previous button --}}
                                @if ($books->onFirstPage())
                                    <button class="books-page-btn" disabled>‹</button>
                                @else
                                    <a href="{{ $books->previousPageUrl() }}" class="books-page-btn">‹</a>
                                @endif

                                @php
                                    $current = $books->currentPage();
                                    $last    = $books->lastPage();
                                    $pages   = [];

                                    if ($last <= 4) {
                                        for ($i = 1; $i <= $last; $i++) {
                                            $pages[] = $i;
                                        }
                                    } else {
                                        // current and next
                                        $pages[] = $current;
                                        if ($current + 1 <= $last) {
                                            $pages[] = $current + 1;
                                        }
                                        // last two
                                        $lastTwo = [$last - 1, $last];
                                        foreach ($lastTwo as $p) {
                                            if (!in_array($p, $pages) && $p >= 1) {
                                                $pages[] = $p;
                                            }
                                        }

                                        $pages = array_unique($pages);
                                        sort($pages);

                                        $temp = [];
                                        $prev = null;
                                        foreach ($pages as $page) {
                                            if ($prev !== null && $page - $prev > 1) {
                                                $temp[] = '...';
                                            }
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
                                        <a href="{{ $books->url($page) }}" class="books-page-btn">{{ $page }}</a>
                                    @endif
                                @endforeach

                                {{-- Next button --}}
                                @if ($books->hasMorePages())
                                    <a href="{{ $books->nextPageUrl() }}" class="books-page-btn">›</a>
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

<style>
.books-label {
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #6c757d;
    display: block;
    margin-bottom: 5px;
}
.books-input {
    width: 100%;
    height: 34px;
    padding: 0 10px;
    font-size: 13px;
    border: 0.5px solid #dee2e6;
    border-radius: 6px;
    background: #f8f9fa;
    color: #212529;
    transition: border-color .15s;
    appearance: auto;
}
.books-input:focus { outline: none; border-color: #185FA5; background: #fff; }
.books-input-icon {
    position: absolute;
    right: 10px; top: 50%;
    transform: translateY(-50%);
    font-size: 13px;
    color: #adb5bd;
    pointer-events: none;
}

.books-stat-card {
    background: #fff;
    border: 0.5px solid #e9ecef;
    border-radius: 12px;
    padding: 16px 16px 14px;
    position: relative;
    overflow: hidden;
    transition: box-shadow .15s, transform .15s;
}
.books-stat-card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,.07);
    transform: translateY(-2px);
}
.stat-icon-wrap {
    width: 34px; height: 34px;
    border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    font-size: 15px;
    margin-bottom: 10px;
}
.stat-icon-default { background: #f1f5f9; color: #64748b; }
.stat-icon-blue    { background: #dbeafe; color: #1d4ed8; }
.stat-icon-purple  { background: #ede9fe; color: #6d28d9; }
.stat-icon-orange  { background: #fff7ed; color: #c2410c; }
.stat-icon-red     { background: #fee2e2; color: #b91c1c; }
.stat-icon-teal    { background: #ccfbf1; color: #0f766e; }
.books-stat-card .stat-value {
    font-size: 24px; font-weight: 700;
    line-height: 1; color: #1e293b; margin-bottom: 3px;
}
.books-stat-card .stat-label { font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 2px; }
.books-stat-card .stat-sub   { font-size: 10px; color: #adb5bd; }
.books-stat-card.stat-blue   .stat-value { color: #185FA5; }
.books-stat-card.stat-purple .stat-value { color: #534AB7; }
.books-stat-card.stat-orange .stat-value { color: #C05621; }
.books-stat-card.stat-red    .stat-value { color: #A32D2D; }
.books-stat-card.stat-teal   .stat-value { color: #0F766E; }

.books-card {
    background: #fff;
    border: 0.5px solid #e9ecef;
    border-radius: 10px;
    overflow: hidden;
}
.books-count-badge {
    display: inline-flex; align-items: center;
    font-size: 11px; font-weight: 600;
    padding: 3px 10px; border-radius: 20px;
    background: #E6F1FB; color: #185FA5;
}

.books-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.books-table thead th {
    font-size: 10px; font-weight: 600;
    text-transform: uppercase; letter-spacing: .07em;
    color: #6c757d; background: #f8f9fa;
    padding: 10px 12px;
    border-bottom: 0.5px solid #e9ecef;
    white-space: nowrap;
}
.books-table tbody tr {
    border-bottom: 0.5px solid #f0f0f0;
    cursor: pointer;
    transition: background .12s;
}
.books-table tbody tr:last-child { border-bottom: none; }
.books-table tbody tr:hover { background: #f0f7ff; }
.books-table tbody td { padding: 11px 12px; vertical-align: middle; }

.books-cover-img {
    width: 36px; height: 46px;
    border-radius: 4px; object-fit: cover;
    border: 0.5px solid #e9ecef; display: block;
}
.books-cover-placeholder {
    width: 36px; height: 46px;
    border-radius: 4px;
    background: #f8f9fa;
    border: 0.5px solid #e9ecef;
    display: flex; align-items: center; justify-content: center;
    color: #adb5bd; font-size: 15px;
}

.books-title      { font-weight: 500; line-height: 1.4; color: #212529; }
.books-avail-warn { font-size: 11px; color: #BA7517; margin-top: 2px; }

.books-badge {
    display: inline-flex; align-items: center;
    font-size: 11px; font-weight: 500;
    padding: 2px 8px; border-radius: 20px; white-space: nowrap;
}
.books-badge-category { background: #f8f9fa; color: #495057; border: 0.5px solid #dee2e6; }
.books-badge-group    { background: #E6F1FB; color: #0C447C; }

.books-avail-num  { font-weight: 500; color: #3B6D11; font-size: 13px; }
.books-avail-bar  { width: 40px; height: 3px; background: #e9ecef; border-radius: 2px; overflow: hidden; }
.books-avail-fill { height: 100%; background: #639922; border-radius: 2px; }
.books-avail-fill.fill-danger { background: #E24B4A; }

.books-action-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 30px; height: 30px;
    border-radius: 6px;
    border: 0.5px solid #dee2e6;
    background: transparent; color: #6c757d;
    text-decoration: none;
    transition: background .12s, color .12s, border-color .12s;
    font-size: 14px;
}
.books-action-btn:hover {
    background: #E6F1FB; color: #185FA5; border-color: #B5D4F4;
}

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

@endsection