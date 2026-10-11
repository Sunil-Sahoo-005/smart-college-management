@extends('admin_layout')
@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">

<style>
    .ri-wrap * { box-sizing: border-box; }
    .ri-wrap { font-family: 'DM Sans', sans-serif; }

    /* ── Header ── */
    .ri-page-header {
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
    .ri-page-title { font-size: 15px; font-weight: 600; color: #111827; }
    .ri-btn-create {
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
    .ri-btn-create:hover { background: #4338ca; color: #fff; }
    .ri-btn-create svg { width: 15px; height: 15px; }

    /* ── Body ── */
    .ri-body { padding: 20px; }

    /* ── Filter Card ── */
    .ri-filter-card {
        background: #fff;
        border: 1px solid #f1f5f9;
        border-radius: 14px;
        padding: 16px 18px;
        margin-bottom: 16px;
    }
    .ri-filter-row {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: flex-end;
    }
    .ri-filter-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
        flex: 1;
        min-width: 150px;
    }
    .ri-filter-group label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #9ca3af;
    }
    .ri-filter-input,
    .ri-filter-select {
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
    .ri-filter-input:focus,
    .ri-filter-select:focus { border-color: #a5b4fc; background: #fff; }

    .ri-filter-btns { display: flex; gap: 6px; align-items: flex-end; }
    .ri-btn-filter {
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
    .ri-btn-filter:hover { background: #4338ca; }
    .ri-btn-clear {
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
    .ri-btn-clear:hover { background: #fef2f2; border-color: #fca5a5; color: #ef4444; }
    .ri-btn-filter svg, .ri-btn-clear svg { width: 13px; height: 13px; stroke: currentColor; fill: none; }

    /* ── Active badges ── */
    .ri-badge-row { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 12px; }
    .ri-badge {
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

    /* ── Empty State ── */
    .ri-empty { text-align: center; padding: 60px 20px; color: #9ca3af; }
    .ri-empty p { font-size: 14px; margin: 0 0 12px; }

    /* ── Table ── */
    .ri-table-wrap { overflow-x: auto; border-radius: 12px; border: 1px solid #f1f5f9; }
    table.ri-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
    .ri-table thead tr { background: #f8fafc; border-bottom: 1px solid #f1f5f9; }
    .ri-table thead th {
        padding: 11px 16px;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        color: #9ca3af;
        white-space: nowrap;
        text-align: left;
    }
    .ri-table thead th:last-child { text-align: right; }
    .ri-table tbody tr { border-bottom: 1px solid #f8fafc; transition: background 0.12s; }
    .ri-table tbody tr:last-child { border-bottom: none; }
    .ri-table tbody tr:hover { background: #fafafe; }
    .ri-table td { padding: 13px 16px; color: #374151; vertical-align: middle; }
    .ri-table td:last-child { text-align: right; }

    .vehicle-cell { display: flex; flex-direction: column; gap: 2px; }
    .vehicle-type { font-weight: 600; font-size: 13px; color: #111827; }
    .vehicle-reg  { font-size: 11.5px; color: #9ca3af; }
    .route-name-cell { font-weight: 600; color: #111827; }
    .desc-cell { color: #6b7280; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .loc-cell { display: flex; align-items: center; gap: 6px; color: #374151; font-size: 13px; }
    .loc-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .loc-dot.start { background: #22c55e; }
    .loc-dot.end   { background: #ef4444; }

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
    .action-btn:hover         { background: #f5f3ff; border-color: #c7d2fe; color: #4f46e5; }
    .action-btn.delete:hover  { background: #fef2f2; border-color: #fca5a5; color: #ef4444; }
    .action-btn.view-btn      { color: #0ea5e9; border-color: #bae6fd; }
    .action-btn.view-btn:hover{ background: #f0f9ff; border-color: #7dd3fc; color: #0284c7; }
    .action-btn svg { width: 14px; height: 14px; stroke: currentColor; }

    /* ── Pagination ── */
    .ri-pagination { margin-top: 16px; }
    .ri-pagination .pagination { gap: 4px; }
    .ri-pagination .page-link {
        border-radius: 8px !important;
        font-size: 13px;
        border: 1px solid #e5e7eb;
        color: #374151;
        padding: 5px 11px;
    }
    .ri-pagination .page-item.active .page-link { background: #4f46e5; border-color: #4f46e5; color: #fff; }
    .ri-pagination .page-link:hover { background: #f5f3ff; border-color: #c7d2fe; color: #4f46e5; }
</style>

<div class="nxl-content without-header nxl-full-content ri-wrap">
    <div class="main-content d-flex">
        @include('Travels.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            {{-- ── Page Header ── --}}
            <div class="ri-page-header">
                <div class="ri-page-title">Routes List</div>
                <div>
                    @if(auth()->user()->hasPermissionTo('Create Routes'))
                        <a href="{{ route('admin.routes.create') }}" class="ri-btn-create">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Create Route
                        </a>
                    @endif
                </div>
            </div>

            {{-- ── Content ── --}}
            <div class="ri-body">
                @include('elements.flash')

                {{-- ── Filter Card ── --}}
                <div class="ri-filter-card">
                    <form method="GET" action="{{ route('admin.routes.index') }}">
                        <div class="ri-filter-row">

                            {{-- Search by Route Name --}}
                            <div class="ri-filter-group">
                                <label>Route Name</label>
                                <input type="text"
                                       name="search"
                                       class="ri-filter-input"
                                       placeholder="Search route..." autocomplete="off"
                                       value="{{ request('search') }}">
                            </div>

                            {{-- Filter by Vehicle --}}
                            <div class="ri-filter-group">
                                <label>Vehicle</label>
                                <select name="vehicle_id" class="ri-filter-select" autocomplete="off">
                                    <option value="">All Vehicles</option>
                                    @foreach($vehicles as $vh)
                                        <option value="{{ $vh->id }}"
                                            {{ request('vehicle_id') == $vh->id ? 'selected' : '' }}>
                                            {{ $vh->registration_number }}
                                            @if($vh->type) ({{ ucfirst($vh->type) }}) @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            

                            {{-- Buttons --}}
                            <div class="ri-filter-btns">
                                <button type="submit" class="ri-btn-filter">
                                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                                    </svg>
                                    Filter
                                </button>
                                <a href="{{ route('admin.routes.index') }}" class="ri-btn-clear">
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
                @if(request('search') || request('vehicle_id') || request('start_point') || request('end_point'))
                    <div class="ri-badge-row">
                        @if(request('search'))
                            <span class="ri-badge">🗺️ Route: {{ request('search') }}</span>
                        @endif
                        @if(request('vehicle_id'))
                            @php $selVehicle = $vehicles->find(request('vehicle_id')); @endphp
                            <span class="ri-badge">🚌 Vehicle: {{ $selVehicle?->registration_number ?? request('vehicle_id') }}</span>
                        @endif
                       
                    </div>
                @endif

                {{-- ── Table Card ── --}}
                <div class="card stretch stretch-full">
                    <div class="card-body p-0">

                        @if($routes->isEmpty())
                            <div class="ri-empty">
                                <p>No routes found matching your filters.</p>
                                @if(request('search') || request('vehicle_id') || request('start_point') || request('end_point'))
                                    <a href="{{ route('admin.routes.index') }}" class="ri-btn-clear" style="margin: 0 auto;">
                                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                                            <line x1="18" y1="6" x2="6" y2="18"/>
                                            <line x1="6" y1="6" x2="18" y2="18"/>
                                        </svg>
                                        Clear Filters
                                    </a>
                                @endif
                            </div>
                        @else
                            <div class="ri-table-wrap">
                                <table class="ri-table">
                                    <thead>
                                        <tr>
                                            <th>Vehicle</th>
                                            <th>Route Name</th>
                                            <th>Description</th>
                                            <th>Start Point</th>
                                            <th>End Point</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($routes as $v)
                                        <tr>
                                            {{-- Vehicle --}}
                                            <td>
                                                <div class="vehicle-cell">
                                                    <div class="vehicle-type">{{ $v->vehicle->type ?? 'N/A' }}</div>
                                                    <div class="vehicle-reg">{{ $v->vehicle->registration_number ?? '—' }}</div>
                                                </div>
                                            </td>

                                            {{-- Name --}}
                                            <td><span class="route-name-cell">{{ $v->name }}</span></td>

                                            {{-- Description --}}
                                            <td>
                                                <span class="desc-cell" title="{{ $v->description }}">
                                                    {{ $v->description ?: '—' }}
                                                </span>
                                            </td>

                                            {{-- Start Point --}}
                                            <td>
                                                <div class="loc-cell">
                                                    <span class="loc-dot start"></span>
                                                    {{ $v->start_point ?: 'N/A' }}
                                                </div>
                                            </td>

                                            {{-- End Point --}}
                                            <td>
                                                <div class="loc-cell">
                                                    <span class="loc-dot end"></span>
                                                    {{ $v->end_point ?? 'N/A' }}
                                                </div>
                                            </td>

                                            {{-- Actions --}}
                                            <td>
                                                <div class="action-wrap">
                                                    <a href="{{ route('admin.routes.show', $v->id) }}"
                                                       class="action-btn view-btn" title="View Route">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                            <circle cx="12" cy="12" r="3"/>
                                                        </svg>
                                                    </a>

                                                    @if(auth()->user()->hasPermissionTo('Edit Routes'))
                                                        <a href="{{ route('admin.routes.edit', $v->id) }}"
                                                           class="action-btn" title="Edit">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                            </svg>
                                                        </a>
                                                    @endif

                                                    @if(auth()->user()->hasPermissionTo('Delete Routes'))
                                                        <form action="{{ route('admin.routes.destroy', $v->id) }}"
                                                              method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="action-btn delete"
                                                                    title="Delete"
                                                                    onclick="return confirm('Are you sure you want to delete this route?')">
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

                            <div class="ri-pagination px-3 pb-3">
                                {{ $routes->links() }}
                            </div>
                        @endif

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection