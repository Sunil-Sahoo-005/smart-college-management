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
                        <span class="fw-medium" style="font-size:14px;">Book Fines</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if (auth()->user()->hasPermissionTo('Create Book Fines'))
                            <a href="{{ route('admin.book_fines.create') }}"
                               class="btn btn-sm btn-primary d-flex align-items-center gap-1">
                                <i class="feather-plus" style="font-size:13px;"></i> Add Fine
                            </a>
                        @endif
                    </div>
                </div>

                <div class="content-area-body p-4">
                    @include('elements.flash')

                    {{-- TABLE --}}
                    <div class="books-card">
                        @if ($bookFines->isEmpty())
                            <div class="text-center py-5 px-4">
                                <i class="feather-dollar-sign"
                                   style="font-size:40px; color:#adb5bd; display:block; margin-bottom:12px;"></i>
                                <h6 class="mb-1">No fines found</h6>
                                <p class="text-muted small mb-4">No book fines recorded yet.</p>
                                @if (auth()->user()->hasPermissionTo('Create Book Fines'))
                                    <a href="{{ route('admin.book_fines.create') }}"
                                       class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1">
                                        <i class="feather-plus" style="font-size:12px;"></i> Create first fine
                                    </a>
                                @endif
                            </div>
                        @else
                            <div class="d-flex align-items-center justify-content-between px-3 py-2"
                                 style="border-bottom: 0.5px solid #e9ecef;">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="books-count-badge">{{ number_format($bookFines->total()) }} fines</span>
                                </div>
                                <small class="text-muted">
                                    Showing {{ $bookFines->firstItem() }}–{{ $bookFines->lastItem() }} of {{ $bookFines->total() }} records
                                </small>
                            </div>

                            <div class="table-responsive">
                                <table class="books-table">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Card No.</th>
                                            <th>Amount (₹)</th>
                                            <th>Reason</th>
                                            <th>Due date</th>
                                            <th style="width:80px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($bookFines as $fine)
                                            @php
                                                $editUrl = route('admin.book_fines.edit', $fine->id);
                                                $deleteUrl = route('admin.book_fines.destroy', $fine->id);
                                                $statusColor = $fine->status == 'paid' ? '#EAF3DE' : '#FCEBEB';
                                                $statusTextColor = $fine->status == 'paid' ? '#27500A' : '#791F1F';
                                                $canEdit = auth()->user()->hasPermissionTo('Edit Book Fines');
                                                $canDelete = auth()->user()->hasPermissionTo('Delete Book Fines');
                                            @endphp
                                            <tr onclick="window.location='{{ $editUrl }}'">
                                                <td class="fw-medium">
                                                    {{ $fine->student->first_name ?? '' }} {{ $fine->student->last_name ?? '' }}
                                                </td>
                                                <td>
                                                    @if ($fine->student->libraryCard->card_number ?? false)
                                                        <span class="font-monospace" style="font-size:12px;">{{ $fine->student->libraryCard->card_number }}</span>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td class="fw-semibold">₹ {{ number_format($fine->amount, 2) }}</td>
                                                <td>{{ Str::limit($fine->reason ?? $fine->description ?? 'Book Fine', 40) }}</td>
                                                <td class="text-muted">{{ $fine->due_date ? \Carbon\Carbon::parse($fine->due_date)->format('d M Y') : 'N/A' }}</td>
                                                <td onclick="event.stopPropagation()">
                                                    <div class="hstack gap-2 justify-content-end">
                                                        @if ($canEdit)
                                                            <a href="{{ $editUrl }}" class="books-action-btn" title="Edit">
                                                                <i class="feather-edit-3"></i>
                                                            </a>
                                                        @endif
                                                        @if ($canDelete)
                                                            <form action="{{ $deleteUrl }}" method="POST" class="d-inline"
                                                                  onsubmit="return confirm('Are you sure you want to delete this fine?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="books-action-btn text-danger" title="Delete">
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

                            {{-- SLIDING PAGINATION --}}
                            <div class="d-flex align-items-center justify-content-between px-3 py-3"
                                 style="border-top: 0.5px solid #e9ecef;">
                                <small class="text-muted">
                                    Showing {{ $bookFines->firstItem() }}–{{ $bookFines->lastItem() }}
                                    of {{ $bookFines->total() }} records
                                </small>

                                @if ($bookFines->lastPage() > 1)
                                <div class="d-flex gap-1 align-items-center">
                                    @if ($bookFines->onFirstPage())
                                        <button class="books-page-btn" disabled>‹</button>
                                    @else
                                        <a href="{{ $bookFines->previousPageUrl() }}" class="books-page-btn">‹</a>
                                    @endif

                                    @php
                                        $current = $bookFines->currentPage();
                                        $last    = $bookFines->lastPage();
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
                                            <a href="{{ $bookFines->url($page) }}" class="books-page-btn">{{ $page }}</a>
                                        @endif
                                    @endforeach

                                    @if ($bookFines->hasMorePages())
                                        <a href="{{ $bookFines->nextPageUrl() }}" class="books-page-btn">›</a>
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
        .books-action-btn.text-danger:hover {
            background: #fee2e2;
            color: #b91c1c;
            border-color: #fecaca;
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
        .font-monospace { font-family: monospace; }
    </style>
@endsection