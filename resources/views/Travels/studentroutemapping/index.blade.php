@extends('admin_layout')
@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">

<style>
    .srm-wrap * { box-sizing: border-box; }
    .srm-wrap { font-family: 'DM Sans', sans-serif; }

    .srm-page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 20px;
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .srm-page-title {
        font-size: 15px; font-weight: 600; color: #111827;
        display: flex; align-items: center; gap: 8px;
    }
    .srm-page-title .title-icon {
        width: 28px; height: 28px; border-radius: 8px;
        background: #fce7f3;
        display: flex; align-items: center; justify-content: center; font-size: 14px;
    }
    .srm-btn-create {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 8px 18px; background: #4f46e5; color: #fff;
        border: none; border-radius: 10px; font-size: 13.5px; font-weight: 600;
        font-family: 'DM Sans', sans-serif; text-decoration: none; cursor: pointer;
        transition: background 0.15s, transform 0.1s;
    }
    .srm-btn-create:hover  { background: #4338ca; color: #fff; }
    .srm-btn-create:active { transform: scale(0.97); }
    .srm-btn-create svg { width: 15px; height: 15px; }

    .srm-body { padding: 20px; }

    .srm-empty { text-align: center; padding: 60px 20px; color: #9ca3af; }
    .srm-empty-icon { font-size: 40px; margin-bottom: 12px; }
    .srm-empty p { font-size: 14px; margin: 0; }

    .srm-table-wrap { overflow-x: auto; border-radius: 12px; border: 1px solid #f1f5f9; }
    table.srm-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
    .srm-table thead tr { background: #f8fafc; border-bottom: 1px solid #f1f5f9; }
    .srm-table thead th {
        padding: 11px 16px; font-size: 11px; font-weight: 600;
        letter-spacing: 0.07em; text-transform: uppercase; color: #9ca3af;
        white-space: nowrap; text-align: left;
    }
    .srm-table thead th:last-child { text-align: right; }
    .srm-table tbody tr { border-bottom: 1px solid #f8fafc; transition: background 0.12s; }
    .srm-table tbody tr:last-child { border-bottom: none; }
    .srm-table tbody tr:hover { background: #fafafe; }
    .srm-table td { padding: 13px 16px; color: #374151; vertical-align: middle; }
    .srm-table td:last-child { text-align: right; }

    /* Student cell */
    .student-cell { display: flex; align-items: center; gap: 10px; }
    .student-avatar {
        width: 36px; height: 36px; border-radius: 10px;
        background: linear-gradient(135deg, #ec4899, #f43f5e);
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 700; color: #fff; flex-shrink: 0;
    }
    .student-name { font-weight: 600; font-size: 13.5px; color: #111827; }
    .student-sub  { font-size: 11.5px; color: #9ca3af; margin-top: 1px; }

    /* Stop badge */
    .stop-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 10px; background: #f0fdf4; border: 1px solid #bbf7d0;
        border-radius: 20px; font-size: 12px; font-weight: 600; color: #15803d;
    }
    .stop-badge.empty { background: #f9fafb; border-color: #e5e7eb; color: #9ca3af; font-weight: 400; }

    /* Route badge */
    .route-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 10px; background: #ede9fe; border: 1px solid #c4b5fd;
        border-radius: 20px; font-size: 12px; font-weight: 600; color: #6d28d9;
    }

    /* Fee badge */
    .fee-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 4px 10px; background: #fefce8; border: 1px solid #fde68a;
        border-radius: 20px; font-size: 12px; font-weight: 700; color: #92400e;
    }

    /* Actions */
    .action-wrap { display: flex; align-items: center; gap: 6px; justify-content: flex-end; }
    .action-btn {
        width: 32px; height: 32px; border-radius: 8px;
        display: inline-flex; align-items: center; justify-content: center;
        border: 1px solid #e5e7eb; background: #fff; color: #6b7280;
        text-decoration: none; cursor: pointer;
        transition: background 0.12s, border-color 0.12s, color 0.12s;
    }
    .action-btn:hover        { background: #f5f3ff; border-color: #c7d2fe; color: #4f46e5; }
    .action-btn.view:hover   { background: #f0fdf4; border-color: #86efac; color: #16a34a; }
    .action-btn.delete:hover { background: #fef2f2; border-color: #fca5a5; color: #ef4444; }
    .action-btn svg { width: 14px; height: 14px; stroke: currentColor; }

    /* Pagination */
    .srm-pagination { margin-top: 16px; }
    .srm-pagination .pagination { gap: 4px; }
    .srm-pagination .page-link {
        border-radius: 8px !important; font-size: 13px;
        border: 1px solid #e5e7eb; color: #374151; padding: 5px 11px;
    }
    .srm-pagination .page-item.active .page-link { background: #4f46e5; border-color: #4f46e5; color: #fff; }
    .srm-pagination .page-link:hover { background: #f5f3ff; border-color: #c7d2fe; color: #4f46e5; }
</style>

<div class="nxl-content without-header nxl-full-content srm-wrap">
    <div class="main-content d-flex">
        @include('Travels.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            <div class="srm-page-header">
                <div class="srm-page-title">
                    
                    Student Route Mapping
                </div>
                @if(auth()->user()->hasPermissionTo('Create Student Route Mapping'))
                <a href="{{ route('admin.studentroutemapping.create') }}" class="srm-btn-create">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Assign Stop
                </a>
                @endif
            </div>

            <div class="srm-body">
                @include('elements.flash')
                {{-- ── Filter Bar ── --}}
<div style="
    background:#fff;
    border:1px solid #f1f5f9;
    border-radius:14px;
    padding:16px 18px;
    margin-bottom:16px;
    font-family:'DM Sans',sans-serif;
">
    <form method="GET" action="{{ route('admin.studentroutemapping.index') }}">
        <div style="display:flex; flex-wrap:wrap; gap:10px; align-items:flex-end;">

            {{-- Student Name --}}
            <div style="display:flex; flex-direction:column; gap:4px; flex:1; min-width:180px;">
                <label style="font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.06em; color:#9ca3af;">
                    Student Name
                </label>
                <input type="text"
                       name="search"
                       placeholder="First or last name..." autocomplete="off"
                       value="{{ request('search') }}" 
                       style="height:36px; padding:0 11px; border:1px solid #e5e7eb; border-radius:9px; font-size:13px; font-family:'DM Sans',sans-serif; color:#111827; background:#fafafa; outline:none; width:100%;">
            </div>

            {{-- Boarding Stop --}}
            <div style="display:flex; flex-direction:column; gap:4px; flex:1; min-width:180px;">
                <label style="font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.06em; color:#9ca3af;">
                    Boarding Stop
                </label>
                <input type="text"
                       name="stop"
                       placeholder="Stop name..." autocomplete="off"
                       value="{{ request('stop') }}"
                       style="height:36px; padding:0 11px; border:1px solid #e5e7eb; border-radius:9px; font-size:13px; font-family:'DM Sans',sans-serif; color:#111827; background:#fafafa; outline:none; width:100%;">
            </div>

            {{-- Buttons --}}
            <div style="display:flex; gap:6px; align-items:flex-end;">
                <button type="submit" style="
                    height:36px; padding:0 16px;
                    background:#4f46e5; color:#fff;
                    border:none; border-radius:9px;
                    font-size:13px; font-weight:600;
                    font-family:'DM Sans',sans-serif;
                    cursor:pointer;
                    display:inline-flex; align-items:center; gap:6px;
                ">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                    </svg>
                    Filter
                </button>
                <a href="{{ route('admin.studentroutemapping.index') }}" style="
                    height:36px; padding:0 14px;
                    background:#fff; color:#6b7280;
                    border:1px solid #e5e7eb; border-radius:9px;
                    font-size:13px; font-family:'DM Sans',sans-serif;
                    cursor:pointer;
                    display:inline-flex; align-items:center; gap:5px;
                    text-decoration:none;
                ">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                    Clear
                </a>
            </div>

        </div>
    </form>
</div>

{{-- ── Active Badges ── --}}
@if(request('search') || request('stop'))
    <div style="display:flex; flex-wrap:wrap; gap:6px; margin-bottom:12px;">
        @if(request('search'))
            <span style="display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:500; background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;">
                👤 Student: {{ request('search') }}
            </span>
        @endif
        @if(request('stop'))
            <span style="display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:500; background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0;">
                📍 Stop: {{ request('stop') }}
            </span>
        @endif
    </div>
@endif

                <div class="card stretch stretch-full">
                    <div class="card-body p-0">

                       @if ($studentroutemapping->isEmpty())
    <div class="srm-empty">
        <div class="srm-empty-icon"></div>
        <p>No mappings found{{ request('search') || request('stop') ? ' matching your filters' : '. Start by assigning a stop to a student!' }}</p>
        @if(request('search') || request('stop'))
            <a href="{{ route('admin.studentroutemapping.index') }}"
               style="display:inline-flex; align-items:center; gap:5px; margin-top:8px; padding:6px 14px; border:1px solid #e5e7eb; border-radius:9px; font-size:13px; color:#6b7280; text-decoration:none; background:#fff;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
                Clear Filters
            </a>
        @endif
    </div>
                        @else
                            <div class="srm-table-wrap">
                                <table class="srm-table">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Boarding Stop</th>
                                            <th>Route</th>
                                            <th>Transport Fee</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @forelse ($studentroutemapping as $v)
                                        @php
                                            $initials = strtoupper(substr($v->student->first_name ?? '?', 0, 1))
                                                      . strtoupper(substr($v->student->last_name  ?? '',  0, 1));
                                        @endphp
                                        <tr>
                                            {{-- Student --}}
                                            <td>
                                                <div class="student-cell">
                                                    <div class="student-avatar">{{ $initials }}</div>
                                                    <div>
                                                        <div class="student-name">
                                                            {{ $v->student->first_name ?? '' }} {{ $v->student->last_name ?? '' }}
                                                        </div>
                                                        <div class="student-sub">Student</div>
                                                    </div>
                                                </div>
                                            </td>

                                            {{-- Stop --}}
                                            <td>
                                                @if($v->stop)
                                                    <span class="stop-badge">📍 {{ $v->stop->stop_name }}</span>
                                                @else
                                                    <span class="stop-badge empty">Not assigned</span>
                                                @endif
                                            </td>

                                            {{-- Route --}}
                                            <td>
                                                @if($v->route)
                                                    <span class="route-badge"> {{ $v->route->name }}</span>
                                                @else
                                                    <span style="color:#9ca3af;">—</span>
                                                @endif
                                            </td>

                                            {{-- Fee --}}
                                            <td>
                                                @if($v->transport_fee)
                                                    <span class="fee-badge">₹{{ number_format($v->transport_fee, 2) }}</span>
                                                @else
                                                    <span style="color:#9ca3af;">—</span>
                                                @endif
                                            </td>

                                            {{-- Actions --}}
                                            <td>
                                                <div class="action-wrap">

                                                    {{-- 👁 Eye / View button --}}
                                                    <a href="{{ route('admin.studentroutemapping.show', $v->id) }}"
                                                       class="action-btn view" title="View Details">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                            <circle cx="12" cy="12" r="3"/>
                                                        </svg>
                                                    </a>

                                                    {{-- Edit button (permission check) --}}
                                                    @if(auth()->user()->hasPermissionTo('Edit Student Route Mapping'))
                                                        <a href="{{ route('admin.studentroutemapping.edit', $v->id) }}"
                                                           class="action-btn" title="Edit">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                            </svg>
                                                        </a>
                                                    @endif

                                                    {{-- Delete button (permission check) --}}
                                                    @if(auth()->user()->hasPermissionTo('Delete Student Route Mapping'))
                                                        <form action="{{ route('admin.studentroutemapping.destroy', $v->id) }}"
                                                              method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="action-btn delete" title="Delete"
                                                                    onclick="return confirm('Are you sure?')">
                                                                <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                                                                    <polyline points="3 6 5 6 21 6"/>
                                                                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                                                    <path d="M10 11v6M14 11v6"/>
                                                                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @endif

                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4" style="color:#9ca3af;">
                                                No mappings found.
                                            </td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="srm-pagination px-3 pb-3">
                                {{ $studentroutemapping->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection