@extends('admin_layout')
@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
:root {
    --ink:      #0f1117;
    --ink-2:    #374151;
    --ink-3:    #6b7280;
    --ink-4:    #9ca3af;
    --surface:  #ffffff;
    --surface-2:#f8fafc;
    --border:   #e5e7eb;
    --border-2: #f1f5f9;
    --accent:   #2563eb;
    --accent-2: #1d4ed8;
    --accent-bg:#eff6ff;
    --green:    #16a34a;
    --green-bg: #f0fdf4;
    --amber:    #d97706;
    --amber-bg: #fffbeb;
    --rose:     #e11d48;
    --rose-bg:  #fff1f2;
    --violet:   #7c3aed;
    --violet-bg:#f5f3ff;
    --teal:     #0d9488;
    --teal-bg:  #f0fdfa;
    --radius:   14px;
    --radius-sm:9px;
    --shadow:   0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.04);
    --shadow-lg:0 8px 32px rgba(0,0,0,.10);
}

.trx * { box-sizing: border-box; margin: 0; padding: 0; }
.trx { font-family: 'Outfit', sans-serif; background: var(--surface-2); min-height: 100vh; color: var(--ink); }

/* ── Page Header ── */
.trx-header {
    background: var(--surface);
    border-bottom: 1px solid var(--border);
    padding: 0 28px;
    height: 58px;
    display: flex; align-items: center; justify-content: space-between;
    position: sticky; top: 0; z-index: 20;
}
.trx-header-left { display: flex; align-items: center; gap: 12px; }
.trx-header-dot {
    width: 8px; height: 8px; border-radius: 50%;
    background: var(--accent); box-shadow: 0 0 0 3px var(--accent-bg);
    animation: pulse-dot 2s ease infinite;
}
@keyframes pulse-dot {
    0%,100% { box-shadow: 0 0 0 3px var(--accent-bg); }
    50%      { box-shadow: 0 0 0 6px rgba(37,99,235,.12); }
}
.trx-header-title { font-size: 15px; font-weight: 700; color: var(--ink); }
.trx-header-sub   { font-size: 12px; color: var(--ink-4); font-weight: 400; }
.trx-actions { display: flex; gap: 10px; }
.trx-btn {
    padding: 8px 18px; border-radius: var(--radius-sm); font-size: 13px;
    font-weight: 600; font-family: 'Outfit', sans-serif; cursor: pointer;
    text-decoration: none; border: none; display: inline-flex; align-items: center; gap: 7px;
    transition: all .15s;
}
.trx-btn-primary { background: var(--accent); color: #fff; }
.trx-btn-primary:hover { background: var(--accent-2); color: #fff; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(37,99,235,.3); }
.trx-btn-ghost { background: var(--surface); color: var(--ink-2); border: 1.5px solid var(--border); }
.trx-btn-ghost:hover { background: var(--surface-2); border-color: #d1d5db; color: var(--ink); }

/* ── Body ── */
.trx-body { padding: 24px 28px; }

/* ── Stat Grid ── */
.trx-stat-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 14px;
    margin-bottom: 24px;
}
.trx-stat {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--radius); padding: 18px 16px;
    display: flex; flex-direction: column; gap: 12px;
    box-shadow: var(--shadow);
    transition: transform .15s, box-shadow .15s;
    position: relative; overflow: hidden;
}
.trx-stat::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    border-radius: var(--radius) var(--radius) 0 0;
}
.trx-stat:hover { transform: translateY(-2px); box-shadow: var(--shadow-lg); }
.trx-stat.c-blue::before   { background: var(--accent); }
.trx-stat.c-green::before  { background: var(--green); }
.trx-stat.c-violet::before { background: var(--violet); }
.trx-stat.c-amber::before  { background: var(--amber); }
.trx-stat.c-teal::before   { background: var(--teal); }
.trx-stat.c-rose::before   { background: var(--rose); }

.trx-stat-top { display: flex; align-items: center; justify-content: space-between; }
.trx-stat-icon {
    width: 36px; height: 36px; border-radius: 9px;
    display: flex; align-items: center; justify-content: center; font-size: 17px;
}
.ic-blue   { background: var(--accent-bg); }
.ic-green  { background: var(--green-bg); }
.ic-violet { background: var(--violet-bg); }
.ic-amber  { background: var(--amber-bg); }
.ic-teal   { background: var(--teal-bg); }
.ic-rose   { background: var(--rose-bg); }

.trx-stat-label { font-size: 11.5px; color: var(--ink-4); font-weight: 500; letter-spacing: .01em; }
.trx-stat-value { font-size: 30px; font-weight: 800; color: var(--ink); line-height: 1; }
.trx-stat-value.small { font-size: 22px; }

/* ── Two-col layout ── */
.trx-row { display: grid; grid-template-columns: 1.6fr 1fr; gap: 20px; margin-bottom: 20px; }
.trx-row-equal { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }

/* ── Cards ── */
.trx-card {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow);
}
.trx-card-head {
    padding: 14px 18px; border-bottom: 1px solid var(--border-2);
    display: flex; align-items: center; justify-content: space-between;
}
.trx-card-title { font-size: 13px; font-weight: 700; color: var(--ink); display: flex; align-items: center; gap: 8px; }
.trx-card-title-dot {
    width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0;
}
.dot-blue   { background: var(--accent); }
.dot-green  { background: var(--green); }
.dot-violet { background: var(--violet); }
.dot-amber  { background: var(--amber); }

.trx-card-link {
    font-size: 12px; color: var(--accent); text-decoration: none; font-weight: 600;
    padding: 4px 10px; border-radius: 6px; background: var(--accent-bg);
    transition: background .15s;
}
.trx-card-link:hover { background: #dbeafe; }

/* ── Table ── */
.trx-table { width: 100%; border-collapse: collapse; }
.trx-table thead th {
    padding: 9px 16px; font-size: 10.5px; font-weight: 600;
    letter-spacing: .07em; text-transform: uppercase;
    color: var(--ink-4); background: var(--surface-2);
    border-bottom: 1px solid var(--border-2); text-align: left;
}
.trx-table tbody tr {
    border-bottom: 1px solid var(--border-2);
    transition: background .1s;
}
.trx-table tbody tr:last-child { border-bottom: none; }
.trx-table tbody tr:hover { background: #fafbff; }
.trx-table tbody td { padding: 11px 16px; font-size: 13px; color: var(--ink-2); vertical-align: middle; }
.trx-table .td-name { font-weight: 600; color: var(--ink); }
.trx-table .td-muted { color: var(--ink-4); font-size: 12px; }
.trx-table .empty-row td { text-align: center; color: var(--ink-4); padding: 32px; font-size: 13px; }

/* ── Badge ── */
.trx-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600;
}
.b-green  { background: var(--green-bg);  color: var(--green); }
.b-blue   { background: var(--accent-bg); color: var(--accent); }
.b-gray   { background: #f3f4f6; color: var(--ink-4); }
.b-amber  { background: var(--amber-bg);  color: var(--amber); }
.b-violet { background: var(--violet-bg); color: var(--violet); }
.b-teal   { background: var(--teal-bg);   color: var(--teal); }

/* ── Avatar ── */
.trx-avatar {
    width: 30px; height: 30px; border-radius: 8px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 700; color: #fff; flex-shrink: 0;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
}

/* ── Stagger animation ── */
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
}
.trx-stat { animation: fadeUp .3s ease both; }
.trx-stat:nth-child(1) { animation-delay: .04s; }
.trx-stat:nth-child(2) { animation-delay: .08s; }
.trx-stat:nth-child(3) { animation-delay: .12s; }
.trx-stat:nth-child(4) { animation-delay: .16s; }
.trx-stat:nth-child(5) { animation-delay: .20s; }
.trx-stat:nth-child(6) { animation-delay: .24s; }
.trx-card { animation: fadeUp .35s ease both; animation-delay: .28s; }

@media (max-width: 1100px) {
    .trx-stat-grid { grid-template-columns: repeat(3, 1fr); }
    .trx-row       { grid-template-columns: 1fr; }
    .trx-row-equal { grid-template-columns: 1fr; }
}
@media (max-width: 640px) {
    .trx-stat-grid { grid-template-columns: repeat(2, 1fr); }
    .trx-body { padding: 16px; }
}
</style>

<div class="nxl-content without-header nxl-full-content trx">
    <div class="main-content d-flex">
        @include('Travels.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit" style="flex:1;overflow-y:auto;">

            {{-- ── Header ── --}}
            <div class="trx-header">
                <div class="trx-header-left">
                    <div class="trx-header-dot"></div>
                    <div>
                        <div class="trx-header-title">Transportation</div>
                        <div class="trx-header-sub">Live overview</div>
                    </div>
                </div>
                <div class="trx-actions">
                    <a href="{{ route('admin.vehicle.create') }}" class="trx-btn trx-btn-primary">
                        + Add Vehicle
                    </a>
                    <a href="{{ route('admin.routes.create') }}" class="trx-btn trx-btn-ghost">
                        + Add Route
                    </a>
                </div>
            </div>

            <div class="trx-body">

                {{-- ── 6 Stat Cards ── --}}
                <div class="trx-stat-grid">

                    <div class="trx-stat c-blue">
                        <div class="trx-stat-top">
                            <div class="trx-stat-label">Vehicles</div>
                            <div class="trx-stat-icon ic-blue">🚌</div>
                        </div>
                        <div class="trx-stat-value">{{ $totalVehicles }}</div>
                    </div>

                    <div class="trx-stat c-green">
                        <div class="trx-stat-top">
                            <div class="trx-stat-label">Drivers</div>
                            <div class="trx-stat-icon ic-green">🧑‍✈️</div>
                        </div>
                        <div class="trx-stat-value">{{ $totalDrivers }}</div>
                    </div>

                    <div class="trx-stat c-violet">
                        <div class="trx-stat-top">
                            <div class="trx-stat-label">Routes</div>
                            <div class="trx-stat-icon ic-violet">🗺️</div>
                        </div>
                        <div class="trx-stat-value">{{ $totalRoutes }}</div>
                    </div>

                    <div class="trx-stat c-amber">
                        <div class="trx-stat-top">
                            <div class="trx-stat-label">Students</div>
                            <div class="trx-stat-icon ic-amber">🎓</div>
                        </div>
                        <div class="trx-stat-value">{{ $totalTransportStudents }}</div>
                    </div>

                    <div class="trx-stat c-teal">
                        <div class="trx-stat-top">
                            <div class="trx-stat-label">Total Stops</div>
                            <div class="trx-stat-icon ic-teal">📍</div>
                        </div>
                        <div class="trx-stat-value">{{ $totalStops }}</div>
                    </div>

                    <div class="trx-stat c-rose">
                        <div class="trx-stat-top">
                            <div class="trx-stat-label">Fee Collected</div>
                            <div class="trx-stat-icon ic-rose">💰</div>
                        </div>
                        <div class="trx-stat-value small">₹{{ number_format($totalTransportFee, 0) }}</div>
                    </div>

                </div>

                {{-- ── Routes (wide) + Vehicles (narrow) ── --}}
                <div class="trx-row">

                    <div class="trx-card">
                        <div class="trx-card-head">
                            <div class="trx-card-title">
                                <span class="trx-card-title-dot dot-violet"></span>
                                Recent Routes
                            </div>
                            <a href="{{ route('admin.routes.index') }}" class="trx-card-link">View all</a>
                        </div>
                        <table class="trx-table">
                            <thead>
                                <tr>
                                    <th>Route</th>
                                    <th>From → To</th>
                                    <th>Vehicle</th>
                                    <th>Stops</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentRoutes as $route)
                                    <tr>
                                        <td class="td-name">{{ $route->name }}</td>
                                        <td>
                                            <span>{{ $route->start_point ?? '—' }}</span>
                                            <span style="color:var(--ink-4);margin:0 4px;">→</span>
                                            <span>{{ $route->end_point ?? '—' }}</span>
                                        </td>
                                        <td>
                                            @if($route->vehicle)
                                                <span class="trx-badge b-blue">{{ $route->vehicle->registration_number }}</span>
                                            @else
                                                <span class="trx-badge b-gray">Unassigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="trx-badge b-teal">{{ $route->stops_count ?? 0 }} stops</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row"><td colspan="4">No routes found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="trx-card">
                        <div class="trx-card-head">
                            <div class="trx-card-title">
                                <span class="trx-card-title-dot dot-blue"></span>
                                Recent Vehicles
                            </div>
                            <a href="{{ route('admin.vehicle.index') }}" class="trx-card-link">View all</a>
                        </div>
                        <table class="trx-table">
                            <thead>
                                <tr>
                                    <th>Reg No</th>
                                    <th>Type</th>
                                    <th>Driver</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentVehicles as $vehicle)
                                    <tr>
                                        <td class="td-name">{{ $vehicle->registration_number }}</td>
                                        <td>
                                            <span class="trx-badge b-violet">{{ ucfirst($vehicle->type ?? '—') }}</span>
                                        </td>
                                        <td>{{ $vehicle->driver?->full_name ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr class="empty-row"><td colspan="3">No vehicles found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>

                {{-- ── Student Mappings (full width) ── --}}
                <div class="trx-card">
                    <div class="trx-card-head">
                        <div class="trx-card-title">
                            <span class="trx-card-title-dot dot-amber"></span>
                            Recent Student Mappings
                        </div>
                        <a href="{{ route('admin.studentroutemapping.index') }}" class="trx-card-link">View all</a>
                    </div>
                    <table class="trx-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Route</th>
                                <th>Boarding Stop</th>
                                <th>Fee</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentMappings as $map)
                                <tr>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:10px;">
                                            <div class="trx-avatar">
                                                {{ strtoupper(substr($map->student->first_name ?? 'S', 0, 1)) }}{{ strtoupper(substr($map->student->last_name ?? '', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="td-name">{{ $map->student->first_name ?? '' }} {{ $map->student->last_name ?? '—' }}</div>
                                                <div class="td-muted">{{ $map->student->admission_number ?? '' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $map->route->name ?? '—' }}</td>
                                    <td>{{ $map->stop?->stop_name ?? '—' }}</td>
                                    <td>
                                        @if($map->transport_fee)
                                            <span class="trx-badge b-green">₹{{ number_format($map->transport_fee, 0) }}</span>
                                        @else
                                            <span class="trx-badge b-gray">Not set</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr class="empty-row"><td colspan="4">No student mappings yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>{{-- end trx-body --}}
        </div>
    </div>
</div>

@endsection