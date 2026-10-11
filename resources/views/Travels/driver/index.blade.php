@extends('admin_layout')
@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">

<style>
    .di-wrap * { box-sizing: border-box; }
    .di-wrap { font-family: 'DM Sans', sans-serif; }

    /* ── Header ── */
    .di-page-header {
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
    .di-page-title {
        font-size: 15px;
        font-weight: 600;
        color: #111827;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .di-btn-create {
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
    .di-btn-create:hover { background: #4338ca; color: #fff; }
    .di-btn-create:active { transform: scale(0.97); }
    .di-btn-create svg { width: 15px; height: 15px; }

    /* ── Body ── */
    .di-body { padding: 20px; }

    /* ── Filter Card ── */
    .di-filter-card {
        background: #fff;
        border: 1px solid #f1f5f9;
        border-radius: 14px;
        padding: 16px 18px;
        margin-bottom: 16px;
    }
    .di-filter-row {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: flex-end;
    }
    .di-filter-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
        flex: 1;
        min-width: 160px;
    }
    .di-filter-group label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #9ca3af;
    }
    .di-filter-input {
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
    .di-filter-input:focus { border-color: #a5b4fc; background: #fff; }

    .di-filter-btns { display: flex; gap: 6px; align-items: flex-end; }
    .di-btn-filter {
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
        text-decoration: none;
    }
    .di-btn-filter:hover { background: #4338ca; color: #fff; }
    .di-btn-clear {
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
    .di-btn-clear:hover { background: #fef2f2; border-color: #fca5a5; color: #ef4444; }
    .di-btn-filter svg, .di-btn-clear svg { width: 13px; height: 13px; stroke: currentColor; }

    /* ── Active filter badges ── */
    .di-badge-row { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 12px; }
    .di-badge {
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
    .di-empty { text-align: center; padding: 60px 20px; color: #9ca3af; }
    .di-empty-icon { font-size: 40px; margin-bottom: 12px; }
    .di-empty p { font-size: 14px; margin: 0 0 12px; }

    /* ── Table ── */
    .di-table-wrap { overflow-x: auto; border-radius: 12px; border: 1px solid #f1f5f9; }
    table.di-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
    .di-table thead tr { background: #f8fafc; border-bottom: 1px solid #f1f5f9; }
    .di-table thead th {
        padding: 11px 16px;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        color: #9ca3af;
        white-space: nowrap;
        text-align: left;
    }
    .di-table thead th:last-child { text-align: right; }
    .di-table tbody tr { border-bottom: 1px solid #f8fafc; transition: background 0.12s; }
    .di-table tbody tr:last-child { border-bottom: none; }
    .di-table tbody tr:hover { background: #fafafe; }
    .di-table td { padding: 13px 16px; color: #374151; vertical-align: middle; }
    .di-table td:last-child { text-align: right; }

    /* ── Driver cell ── */
    .driver-cell { display: flex; align-items: center; gap: 10px; }
    .driver-avatar {
        width: 36px; height: 36px;
        border-radius: 10px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        display: flex; align-items: center; justify-content: center;
        font-size: 14px;
        font-weight: 700;
        color: #fff;
        flex-shrink: 0;
        letter-spacing: 0.5px;
    }
    .driver-name { font-weight: 600; font-size: 13.5px; color: #111827; }
    .driver-sub  { font-size: 11.5px; color: #9ca3af; margin-top: 1px; }

    /* ── Phone cell ── */
    .phone-cell {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: #374151;
    }
    .phone-icon {
        width: 20px; height: 20px;
        border-radius: 6px;
        background: #f0fdf4;
        display: flex; align-items: center; justify-content: center;
        font-size: 11px;
    }

    /* ── License badge ── */
    .license-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        color: #1d4ed8;
        font-family: monospace;
        letter-spacing: 0.5px;
    }
    .license-badge.empty {
        background: #f9fafb;
        border-color: #e5e7eb;
        color: #9ca3af;
        font-family: 'DM Sans', sans-serif;
        font-weight: 400;
        letter-spacing: 0;
    }

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
        font-size: 14px;
    }
    .action-btn:hover        { background: #f5f3ff; border-color: #c7d2fe; color: #4f46e5; }
    .action-btn.delete:hover { background: #fef2f2; border-color: #fca5a5; color: #ef4444; }
    .action-btn svg { width: 14px; height: 14px; stroke: currentColor; }

    /* ── Pagination ── */
    .di-pagination { margin-top: 16px; }
    .di-pagination .pagination { gap: 4px; }
    .di-pagination .page-link {
        border-radius: 8px !important;
        font-size: 13px;
        border: 1px solid #e5e7eb;
        color: #374151;
        padding: 5px 11px;
    }
    .di-pagination .page-item.active .page-link { background: #4f46e5; border-color: #4f46e5; color: #fff; }
    .di-pagination .page-link:hover { background: #f5f3ff; border-color: #c7d2fe; color: #4f46e5; }
</style>

<div class="nxl-content without-header nxl-full-content di-wrap">
    <div class="main-content d-flex">
        @include('Travels.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            {{-- ── Page Header ── --}}
            <div class="di-page-header">
                <div class="di-page-title">Driver List</div>
                @if(auth()->user()->hasPermissionTo('Create Driver'))
                    <a href="{{ route('admin.driver.create') }}" class="di-btn-create">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <line x1="12" y1="5" x2="12" y2="19"/>
                            <line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                        Create Driver
                    </a>
                @endif
            </div>

            {{-- ── Content ── --}}
            <div class="di-body">
                @include('elements.flash')

                {{-- ── Filter Card ── --}}
                <div class="di-filter-card">
                    <form method="GET" action="{{ route('admin.driver.index') }}">
                        <div class="di-filter-row">

                            {{-- Search by Name --}}
                            <div class="di-filter-group">
                                <label>Driver Name</label>
                                <input type="text"
                                       name="search"
                                       class="di-filter-input"
                                       placeholder="First or last name..." autocomplete="off"
                                       value="{{ request('search') }}">
                            </div>

                            {{-- Search by Phone --}}
                            <div class="di-filter-group">
                                <label>Phone</label>
                                <input type="text"
                                       name="phone"
                                       class="di-filter-input"
                                       placeholder="Phone number..." autocomplete="off"
                                       value="{{ request('phone') }}">
                            </div>

                            {{-- Search by License --}}
                            <div class="di-filter-group">
                                <label>License No</label>
                                <input type="text"
                                       name="license"
                                       class="di-filter-input"
                                       placeholder="License number..." autocomplete="off"
                                       value="{{ request('license') }}">
                            </div>

                            {{-- Buttons --}}
                            <div class="di-filter-btns">
                                <button type="submit" class="di-btn-filter">
                                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                                    </svg>
                                    Filter
                                </button>
                                <a href="{{ route('admin.driver.index') }}" class="di-btn-clear">
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
                @if(request('search') || request('phone') || request('license'))
                    <div class="di-badge-row">
                        @if(request('search'))
                            <span class="di-badge">👤 Name: {{ request('search') }}</span>
                        @endif
                        @if(request('phone'))
                            <span class="di-badge">📞 Phone: {{ request('phone') }}</span>
                        @endif
                        @if(request('license'))
                            <span class="di-badge">🪪 License: {{ request('license') }}</span>
                        @endif
                    </div>
                @endif

                {{-- ── Table Card ── --}}
                <div class="card stretch stretch-full">
                    <div class="card-body p-0">

                        @if($driver->isEmpty())
                            <div class="di-empty">
                                <div class="di-empty-icon">🚗</div>
                                <p>No drivers found matching your filters.</p>
                                @if(request('search') || request('phone') || request('license'))
                                    <a href="{{ route('admin.driver.index') }}" class="di-btn-clear" style="margin: 0 auto;">
                                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                                            <line x1="18" y1="6" x2="6" y2="18"/>
                                            <line x1="6" y1="6" x2="18" y2="18"/>
                                        </svg>
                                        Clear Filters
                                    </a>
                                @endif
                            </div>
                        @else
                            <div class="di-table-wrap">
                                <table class="di-table">
                                    <thead>
                                        <tr>
                                            <th>Driver</th>
                                            <th>Phone</th>
                                            <th>License No</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($driver as $v)
                                        <tr>
                                            {{-- Driver Name + Avatar --}}
                                            <td>
                                                <div class="driver-cell">
                                                    <div class="driver-avatar">
                                                        {{ strtoupper(substr($v->first_name, 0, 1)) }}{{ strtoupper(substr($v->last_name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div class="driver-name">{{ $v->first_name }} {{ $v->last_name }}</div>
                                                        <div class="driver-sub">Driver</div>
                                                    </div>
                                                </div>
                                            </td>

                                            {{-- Phone --}}
                                            <td>
                                                <div class="phone-cell">
                                                    <span class="phone-icon">📞</span>
                                                    {{ $v->phone ?: '—' }}
                                                </div>
                                            </td>

                                            {{-- License --}}
                                            <td>
                                                @if($v->license_number)
                                                    <span class="license-badge">🪪 {{ $v->license_number }}</span>
                                                @else
                                                    <span class="license-badge empty">N/A</span>
                                                @endif
                                            </td>

                                            {{-- Actions --}}
                                            <td>
                                                <div class="action-wrap">
                                                    @if(auth()->user()->hasPermissionTo('Edit Driver'))
                                                        <a href="{{ route('admin.driver.edit', $v->id) }}"
                                                           class="action-btn" title="Edit">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                            </svg>
                                                        </a>
                                                    @endif

                                                    @if(auth()->user()->hasPermissionTo('Delete Driver'))
                                                        <form action="{{ route('admin.driver.destroy', $v->id) }}"
                                                              method="POST"
                                                              style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="action-btn delete"
                                                                    title="Delete"
                                                                    onclick="return confirm('Are you sure you want to delete this driver?')">
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

                            {{-- Pagination --}}
                            <div class="di-pagination px-3 pb-3">
                                {{ $driver->links() }}
                            </div>
                        @endif

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection