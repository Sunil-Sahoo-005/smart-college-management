@extends('admin_layout')
@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">

<style>
    .vi-wrap * { box-sizing: border-box; }
    .vi-wrap { font-family: 'DM Sans', sans-serif; }

    /* ── Header ── */
    .vi-page-header {
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
    .vi-page-title { font-size: 15px; font-weight: 600; color: #111827; }
    .vi-btn-create {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 8px 18px;
        background: #4f46e5;
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 13.5px;
        font-weight: 600;
        font-family: 'DM Sans', sans-serif;
        text-decoration: none;
        cursor: pointer;
        transition: background 0.15s, transform 0.1s;
    }
    .vi-btn-create:hover { background: #4338ca; color: #fff; }
    .vi-btn-create svg { width: 15px; height: 15px; }

    /* ── Body ── */
    .vi-body { padding: 20px; }

    /* ── Filter Card ── */
    .vi-filter-card {
        background: #fff;
        border: 1px solid #f1f5f9;
        border-radius: 14px;
        padding: 16px 18px;
        margin-bottom: 16px;
    }
    .vi-filter-row {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: flex-end;
    }
    .vi-filter-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
        flex: 1;
        min-width: 160px;
    }
    .vi-filter-group label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #9ca3af;
    }
    .vi-filter-input,
    .vi-filter-select {
        height: 36px;
        padding: 0 11px;
        border: 1px solid #e5e7eb;
        border-radius: 9px;
        font-size: 13px;
        font-family: 'DM Sans', sans-serif;
        color: #111827;
        background: #fafafa;
        outline: none;
        transition: border-color 0.15s, background 0.15s;
        width: 100%;
    }
    .vi-filter-input:focus,
    .vi-filter-select:focus { border-color: #a5b4fc; background: #fff; }

    .vi-filter-btns { display: flex; gap: 6px; align-items: flex-end; }
    .vi-btn-filter {
        height: 36px;
        padding: 0 16px;
        background: #4f46e5;
        color: #fff;
        border: none;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 600;
        font-family: 'DM Sans', sans-serif;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background 0.15s;
    }
    .vi-btn-filter:hover { background: #4338ca; }
    .vi-btn-clear {
        height: 36px;
        padding: 0 14px;
        background: #fff;
        color: #6b7280;
        border: 1px solid #e5e7eb;
        border-radius: 9px;
        font-size: 13px;
        font-family: 'DM Sans', sans-serif;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: background 0.12s, border-color 0.12s;
        text-decoration: none;
    }
    .vi-btn-clear:hover { background: #fef2f2; border-color: #fca5a5; color: #ef4444; }
    .vi-btn-filter svg, .vi-btn-clear svg { width: 13px; height: 13px; stroke: currentColor; fill: none; }

    /* ── Active badges ── */
    .vi-badge-row { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 12px; }
    .vi-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    /* ── Empty ── */
    .vi-empty { text-align: center; padding: 60px 20px; color: #9ca3af; }
    .vi-empty p { font-size: 14px; margin: 0 0 12px; }

    /* ── Table ── */
    .vi-table-wrap { overflow-x: auto; border-radius: 12px; border: 1px solid #f1f5f9; }
    table.vi-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
    .vi-table thead tr { background: #f8fafc; border-bottom: 1px solid #f1f5f9; }
    .vi-table thead th {
        padding: 11px 16px;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        color: #9ca3af;
        white-space: nowrap;
        text-align: left;
    }
    .vi-table thead th:last-child { text-align: right; }
    .vi-table tbody tr { border-bottom: 1px solid #f8fafc; transition: background 0.12s; }
    .vi-table tbody tr:last-child { border-bottom: none; }
    .vi-table tbody tr:hover { background: #fafafe; }
    .vi-table td { padding: 13px 16px; color: #374151; vertical-align: middle; }
    .vi-table td:last-child { text-align: right; }

    /* ── Vehicle cell ── */
    .vehicle-cell { display: flex; align-items: center; gap: 10px; }
    .vehicle-icon-box {
        width: 36px; height: 36px;
        border-radius: 10px;
        background: #f1f5f9;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .vehicle-icon-box svg { width: 18px; height: 18px; stroke: #6b7280; fill: none; stroke-width: 1.8; }
    .vehicle-reg  { font-weight: 700; font-size: 13.5px; color: #111827; font-family: monospace; letter-spacing: 0.5px; }
    .vehicle-type-sub { font-size: 11.5px; color: #9ca3af; margin-top: 1px; }

    /* ── Type badge ── */
    .type-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .type-badge.bus     { background: #ede9fe; color: #6d28d9; }
    .type-badge.minivan { background: #dbeafe; color: #1d4ed8; }
    .type-badge.van     { background: #dcfce7; color: #15803d; }
    .type-badge.default { background: #f1f5f9; color: #64748b; }

    /* ── Capacity badge ── */
    .capacity-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        color: #374151;
    }

    /* ── Driver cell ── */
    .driver-cell { display: flex; align-items: center; gap: 8px; }
    .driver-avatar-sm {
        width: 28px; height: 28px;
        border-radius: 8px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        display: flex; align-items: center; justify-content: center;
        font-size: 11px;
        font-weight: 700;
        color: #fff;
        flex-shrink: 0;
    }
    .driver-name-sm { font-size: 13px; font-weight: 500; color: #374151; }

    /* ── Actions ── */
    .action-wrap { display: flex; align-items: center; gap: 6px; justify-content: flex-end; }
    .action-btn {
        width: 32px; height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center; justify-content: center;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #6b7280;
        text-decoration: none;
        cursor: pointer;
        transition: background 0.12s, border-color 0.12s, color 0.12s;
    }
    .action-btn:hover        { background: #f5f3ff; border-color: #c7d2fe; color: #4f46e5; }
    .action-btn.delete:hover { background: #fef2f2; border-color: #fca5a5; color: #ef4444; }
    .action-btn svg { width: 14px; height: 14px; stroke: currentColor; }

    /* ── Pagination ── */
    .vi-pagination { margin-top: 16px; }
    .vi-pagination .pagination { gap: 4px; }
    .vi-pagination .page-link {
        border-radius: 8px !important;
        font-size: 13px;
        border: 1px solid #e5e7eb;
        color: #374151;
        padding: 5px 11px;
    }
    .vi-pagination .page-item.active .page-link { background: #4f46e5; border-color: #4f46e5; color: #fff; }
    .vi-pagination .page-link:hover { background: #f5f3ff; border-color: #c7d2fe; color: #4f46e5; }
</style>

<div class="nxl-content without-header nxl-full-content vi-wrap">
    <div class="main-content d-flex">
        @include('Travels.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            {{-- ── Page Header ── --}}
            <div class="vi-page-header">
                <div class="vi-page-title">Vehicle List</div>
                @if(auth()->user()->hasPermissionTo('Create Vehicles'))
                    <a href="{{ route('admin.vehicle.create') }}" class="vi-btn-create">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <line x1="12" y1="5" x2="12" y2="19"/>
                            <line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                        Create Vehicle
                    </a>
                @endif
            </div>

            {{-- ── Content ── --}}
            <div class="vi-body">
                @include('elements.flash')

                {{-- ── Filter Card ── --}}
                <div class="vi-filter-card">
                    <form method="GET" action="{{ route('admin.vehicle.index') }}">
                        <div class="vi-filter-row">

                            {{-- Search by Reg Number --}}
                            <div class="vi-filter-group">
                                <label>Registration No</label>
                                <input type="text"
                                       name="search"
                                       class="vi-filter-input"
                                       placeholder="e.g. MH12AB..." autocomplete="off"
                                       value="{{ request('search') }}">
                            </div>

                            {{-- Filter by Type --}}
                            <div class="vi-filter-group">
                                <label>Vehicle Type</label>
                                <select name="type" class="vi-filter-select">
                                    <option value="">All Types</option>
                                    @foreach($types as $type)
                                        <option value="{{ $type }}"
                                            {{ request('type') == $type ? 'selected' : '' }}>
                                            {{ ucfirst($type) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            

                            {{-- Buttons --}}
                            <div class="vi-filter-btns">
                                <button type="submit" class="vi-btn-filter">
                                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                                    </svg>
                                    Filter
                                </button>
                                <a href="{{ route('admin.vehicle.index') }}" class="vi-btn-clear">
                                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                                        <line x1="18" y1="6" x2="6" y2="18"/>
                                        <line x1="6" y1="6" x2="18" y2="18"/>
                                    </svg>
                                    Clear
                                </a>
                            </div>

                        </div>
                    </form>
                </div>

                {{-- ── Active Filter Badges ── --}}
                @if(request('search') || request('type') || request('driver'))
                    <div class="vi-badge-row">
                        @if(request('search'))
                            <span class="vi-badge">🚌 Reg: {{ request('search') }}</span>
                        @endif
                        @if(request('type'))
                            <span class="vi-badge">🏷️ Type: {{ ucfirst(request('type')) }}</span>
                        @endif
                       
                    </div>
                @endif

                {{-- ── Table Card ── --}}
                <div class="card stretch stretch-full">
                    <div class="card-body p-0">

                        @if($vehicle->isEmpty())
                            <div class="vi-empty">
                                <p>No vehicles found matching your filters.</p>
                                @if(request('search') || request('type') || request('driver'))
                                    <a href="{{ route('admin.vehicle.index') }}" class="vi-btn-clear" style="margin: 0 auto;">
                                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                                            <line x1="18" y1="6" x2="6" y2="18"/>
                                            <line x1="6" y1="6" x2="18" y2="18"/>
                                        </svg>
                                        Clear Filters
                                    </a>
                                @endif
                            </div>
                        @else
                            <div class="vi-table-wrap">
                                <table class="vi-table">
                                    <thead>
                                        <tr>
                                            <th>Licence No</th>
                                            <th>Vehicle No</th>
                                            <th>Type</th>
                                            <th>Capacity</th>
                                            <th>Driver</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($vehicle as $v)
                                        @php
                                            $typeClass = match(strtolower($v->type ?? '')) {
                                                'bus'     => 'bus',
                                                'minivan' => 'minivan',
                                                'van'     => 'van',
                                                default   => 'default'
                                            };
                                            $driverName     = ($v->driver->first_name ?? '') . ' ' . ($v->driver->last_name ?? '');
                                            $driverInitials = strtoupper(substr($v->driver->first_name ?? '?', 0, 1))
                                                            . strtoupper(substr($v->driver->last_name  ?? '',  0, 1));
                                        @endphp
                                        <tr>
                                            {{-- License No --}}
                                            <td>
                                                <div class="vehicle-cell">
                                                    <div class="vehicle-icon-box">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8">
                                                            <rect x="2" y="7" width="20" height="11" rx="2"/>
                                                            <path d="M6 7V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v2"/>
                                                            <circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/>
                                                            <path d="M7 16H5v-3m14 3h2v-3"/>
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <div class="vehicle-reg">{{ $v->registration_number }}</div>
                                                        <div class="vehicle-type-sub">{{ $v->type ?? 'N/A' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            {{-- Vehicle No --}}
                                                <td>
                                                    @if($v->vehicle_number)
                                                        <span style="font-size:13px; font-weight:600; color:#374151; font-family:monospace;">
                                                            {{ $v->vehicle_number }}
                                                        </span>
                                                    @else
                                                        <span style="color:#9ca3af;">—</span>
                                                    @endif
                                                </td>

                                            {{-- Type --}}
                                            <td>
                                                <span class="type-badge {{ $typeClass }}">
                                                    {{ $v->type ?? 'N/A' }}
                                                </span>
                                            </td>

                                            {{-- Capacity --}}
                                            <td>
                                                @if($v->capacity)
                                                    <span class="capacity-badge">{{ $v->capacity }} seats</span>
                                                @else
                                                    <span style="color:#9ca3af;">—</span>
                                                @endif
                                            </td>

                                            {{-- Driver --}}
                                            <td>
                                                @if($v->driver)
                                                    <div class="driver-cell">
                                                        <div class="driver-avatar-sm">{{ $driverInitials }}</div>
                                                        <span class="driver-name-sm">{{ trim($driverName) }}</span>
                                                    </div>
                                                @else
                                                    <span style="color:#9ca3af;font-size:13px;">Not assigned</span>
                                                @endif
                                            </td>

                                            {{-- Actions --}}
                                            <td>
                                                <div class="action-wrap">
                                                    @if(auth()->user()->hasPermissionTo('Edit Vehicles'))
                                                        <a href="{{ route('admin.vehicle.edit', $v->id) }}"
                                                           class="action-btn" title="Edit">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                            </svg>
                                                        </a>
                                                    @endif
                                                    @if(auth()->user()->hasPermissionTo('Delete Vehicles'))
                                                        <form action="{{ route('admin.vehicle.destroy', $v->id) }}"
                                                              method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="action-btn delete"
                                                                    title="Delete"
                                                                    onclick="return confirm('Are you sure you want to delete this vehicle?')">
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
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="vi-pagination px-3 pb-3">
                                {{ $vehicle->links() }}
                            </div>
                        @endif

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection