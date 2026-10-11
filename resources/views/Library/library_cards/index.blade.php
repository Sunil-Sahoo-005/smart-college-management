@extends('admin_layout')
@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Library.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <div class="page-header-left d-flex align-items-center gap-3">
                        <h6 class="mb-0">Library Cards List</h6>
                    </div>

                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                        </div>
                    </div>
                </div>

                <div class="content-area-body">
                    @include('elements.flash')

                    {{-- Stats Row --}}
                    <div class="row g-3 mb-3">
                        <div class="col-6 col-md-3">
                            <div class="card border-0" style="background:#f9fafb;">
                                <div class="card-body py-3 px-3">
                                    <p class="text-muted mb-1" style="font-size:12px;">Total Cards</p>
                                    <h5 class="mb-0 fw-semibold">{{ $totalCards ?? 0 }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card border-0" style="background:#EAF3DE;">
                                <div class="card-body py-3 px-3">
                                    <p class="text-muted mb-1" style="font-size:12px;">Active</p>
                                    <h5 class="mb-0 fw-semibold" style="color:#27500A;">{{ $activeCards ?? 0 }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card border-0" style="background:#FCEBEB;">
                                <div class="card-body py-3 px-3">
                                    <p class="text-muted mb-1" style="font-size:12px;">Suspended</p>
                                    <h5 class="mb-0 fw-semibold" style="color:#791F1F;">{{ $suspendedCards ?? 0 }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card border-0" style="background:#FFF7ED;">
                                <div class="card-body py-3 px-3">
                                    <p class="text-muted mb-1" style="font-size:12px;">Expired</p>
                                    <h5 class="mb-0 fw-semibold" style="color:#9a3412;">{{ $expiredCards ?? 0 }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card stretch stretch-full">
                        {{-- Filter Bar --}}
                        <div class="card-header border-bottom py-3">
                            <form method="GET" action="{{ route('admin.library_cards.index') }}"
                                  class="d-flex flex-wrap align-items-center gap-3 w-100">

                                {{-- Search Section --}}
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-medium" style="font-size:13px; color:#374151;">Search</span>
                                    <div class="position-relative" style="min-width: 300px;">
                                        <i class="feather-search position-absolute" 
                                           style="left: 10px; top: 50%; transform: translateY(-50%); font-size: 14px; color: #9ca3af;"></i>
                                        <input type="text"
                                               name="search"
                                               class="form-control form-control-sm"
                                               style="padding-left: 32px; font-size:13px; height: 34px; border:1px solid #e5e7eb; border-radius:6px; width: 300px;"
                                               placeholder="Search by student name or card number..."
                                               value="{{ $search ?? '' }}">
                                    </div>
                                </div>

                                {{-- Status Filter --}}
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-medium" style="font-size:13px; color:#374151;">Status</span>
                                    <select name="status"
                                            class="form-select form-select-sm"
                                            style="width:120px; font-size:13px; height: 40px; border:1px solid #e5e7eb; background:#f9fafb; border-radius:6px;">
                                        <option value="">All Status</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                                    </select>
                                </div>

                                {{-- Date Range Filter --}}
                                <div class="d-flex align-items-center gap-2 border rounded-2 px-3 py-1"
                                     style="background:#f9fafb; height:34px;">
                                    <input type="date"
                                           name="date_from"
                                           class="border-0 bg-transparent p-0"
                                           style="font-size:12px; outline:none; width:120px; cursor: pointer;"
                                           placeholder="From Date"
                                           value="{{ request('date_from') }}">
                                    <span class="text-muted" style="font-size:12px;">—</span>
                                    <input type="date"
                                           name="date_to"
                                           class="border-0 bg-transparent p-0"
                                           style="font-size:12px; outline:none; width:120px; cursor: pointer;"
                                           placeholder="To Date"
                                           value="{{ request('date_to') }}">
                                </div>

                                {{-- Filter and Clear Buttons --}}
                                <div class="d-flex align-items-center gap-2">
                                    <button type="submit" class="btn btn-sm btn-primary d-flex align-items-center gap-1"
                                            style="height:34px; font-size:13px; padding:0 16px;">
                                        <i class="feather-filter" style="font-size:12px;"></i> Filter
                                    </button>

                                    <a href="{{ route('admin.library_cards.index') }}"
                                       class="btn btn-sm btn-outline-secondary"
                                       style="height:34px; font-size:13px; padding:0 16px;">
                                        Clear
                                    </a>
                                </div>

                            </form>
                        </div>

                        <div class="card-body p-0">
                            @if ($library_cards->isEmpty())
                                <div class="text-center py-5">
                                    <i class="feather-credit-card text-muted mb-2" style="font-size:32px; display:block;"></i>
                                    <p class="text-muted mb-0">No library cards found.</p>
                                    @if(($search ?? '') || request('status') || request('date_from') || request('date_to'))
                                        <a href="{{ route('admin.library_cards.index') }}"
                                           class="btn btn-sm btn-outline-secondary mt-2">
                                            Clear filters
                                        </a>
                                    @endif
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0" id="staffList">
                                        <thead>
                                            <tr style="background:#f9fafb;">
                                                <th style="font-size:11px; font-weight:500; color:#9ca3af; letter-spacing:.04em; padding:12px 16px;">STUDENT</th>
                                                <th style="font-size:11px; font-weight:500; color:#9ca3af; letter-spacing:.04em; padding:12px 16px;">CARD NUMBER</th>
                                                <th style="font-size:11px; font-weight:500; color:#9ca3af; letter-spacing:.04em; padding:12px 16px;">ISSUE DATE</th>
                                                <th style="font-size:11px; font-weight:500; color:#9ca3af; letter-spacing:.04em; padding:12px 16px;">EXPIRY DATE</th>
                                                <th style="font-size:11px; font-weight:500; color:#9ca3af; letter-spacing:.04em; padding:12px 16px;">STATUS</th>
                                                <th class="text-end" style="font-size:11px; font-weight:500; color:#9ca3af; letter-spacing:.04em; padding:12px 16px;">ACTIONS</th>
                                              </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($library_cards as $s)
                                                <tr class="single-item">
                                                    <td style="padding:12px 16px;">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div style="width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:500; background:#E6F1FB; color:#0C447C;">
                                                                {{ strtoupper(substr($s->student->first_name ?? '',0,1).substr($s->student->last_name ?? '',0,1)) }}
                                                            </div>
                                                            <div>
                                                                {{-- Student name link remains always visible (no UI change) --}}
                                                                <a href="{{ route('admin.library_cards.edit', $s) }}"
                                                                   class="text-truncate-1-line fw-medium" 
                                                                   style="font-size:13px; text-decoration:none; color:#374151;">
                                                                    {{ $s->student->first_name ?? 'N/A' }} {{ $s->student->last_name ?? '' }}
                                                                </a>
                                                            </div>
                                                        </div>
                                                      </td>
                                                    <td style="padding:12px 16px;">
                                                        <span style="font-family: monospace; font-size:12px;">{{ $s->card_number }}</span>
                                                      </td>
                                                    <td style="padding:12px 16px; font-size:12px; color:#6b7280;">
                                                        {{ \Carbon\Carbon::parse($s->issue_date)->format('d M Y') }}
                                                      </td>
                                                    <td style="padding:12px 16px; font-size:12px; color:#6b7280;">
                                                        {{ $s->expiry_date ? \Carbon\Carbon::parse($s->expiry_date)->format('d M Y') : '—' }}
                                                      </td>
                                                    <td style="padding:12px 16px;">
                                                        @php
                                                            if($s->status == 'active') {
                                                                $bgColor = '#EAF3DE';
                                                                $textColor = '#27500A';
                                                                $statusText = 'Active';
                                                            } elseif($s->status == 'suspended') {
                                                                $bgColor = '#FCEBEB';
                                                                $textColor = '#791F1F';
                                                                $statusText = 'Suspended';
                                                            } elseif($s->status == 'expired') {
                                                                $bgColor = '#FEF3C7';
                                                                $textColor = '#92400E';
                                                                $statusText = 'Expired';
                                                            } else {
                                                                $bgColor = '#f3f4f6';
                                                                $textColor = '#6b7280';
                                                                $statusText = ucfirst($s->status);
                                                            }
                                                        @endphp
                                                        <span style="font-size:11px; padding:4px 12px; border-radius:99px; font-weight:500;
                                                                     background:{{ $bgColor }};
                                                                     color:{{ $textColor }};">
                                                            {{ $statusText }}
                                                        </span>
                                                      </td>
                                                    <td style="padding:12px 16px;">
                                                        <div class="hstack gap-2 justify-content-end">
                                                            {{-- Edit button permission only --}}
                                                            @if(auth()->user()->hasPermissionTo('Edit Library Card'))
                                                                <a href="{{ route('admin.library_cards.edit', $s) }}"
                                                                   class="avatar-text avatar-sm" 
                                                                   title="Edit">
                                                                    <i class="feather-edit-3"></i>
                                                                </a>
                                                            @endif

                                                            {{-- Delete button permission only --}}
                                                            @if(auth()->user()->hasPermissionTo('Delete Library Card'))
                                                                <form action="{{ route('admin.library_cards.destroy', $s) }}"
                                                                      method="POST"
                                                                      onsubmit="return confirm('Are you sure you want to delete this card?')">
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

                                {{-- Pagination (unchanged) --}}
                                <div class="d-flex align-items-center justify-content-between px-3 py-3 border-top">
                                    <small class="text-muted">
                                        Showing {{ $library_cards->firstItem() }}–{{ $library_cards->lastItem() }}
                                        of {{ $library_cards->total() }} records
                                    </small>

                                    @if ($library_cards->lastPage() > 1)
                                    <div class="d-flex gap-1 align-items-center">
                                        @if ($library_cards->onFirstPage())
                                            <button class="books-page-btn" disabled>‹</button>
                                        @else
                                            <a href="{{ $library_cards->previousPageUrl() }}" class="books-page-btn">‹</a>
                                        @endif

                                        @php
                                            $current = $library_cards->currentPage();
                                            $last    = $library_cards->lastPage();
                                            $pages   = [];

                                            if ($last <= 4) {
                                                for ($i = 1; $i <= $last; $i++) {
                                                    $pages[] = $i;
                                                }
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
                                                <a href="{{ $library_cards->url($page) }}" class="books-page-btn">{{ $page }}</a>
                                            @endif
                                        @endforeach

                                        @if ($library_cards->hasMorePages())
                                            <a href="{{ $library_cards->nextPageUrl() }}" class="books-page-btn">›</a>
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
</div>

<style>
/* Same pagination styles (unchanged) */
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
    const dateTo = document.querySelector('input[name="date_to"]');
    if (dateFrom && dateFrom.value) dateFrom.type = 'date';
    if (dateTo && dateTo.value) dateTo.type = 'date';
});
</script>
@endsection