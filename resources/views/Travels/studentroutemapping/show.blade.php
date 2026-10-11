@extends('admin_layout')
@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

<style>
    .show-wrap * { box-sizing: border-box; }
    .show-wrap { font-family: 'DM Sans', sans-serif; }

    .show-page-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 20px; background: #fff;
        border-bottom: 1px solid #f1f5f9;
        position: sticky; top: 0; z-index: 10;
    }
    .show-page-title {
        font-size: 15px; font-weight: 600; color: #111827;
        display: flex; align-items: center; gap: 8px;
    }
    .show-page-title .title-icon {
        width: 28px; height: 28px; border-radius: 8px; background: #fce7f3;
        display: flex; align-items: center; justify-content: center; font-size: 14px;
    }
    .show-header-actions { display: flex; align-items: center; gap: 8px; }
    .show-btn {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 8px 16px; border-radius: 10px; font-size: 13px; font-weight: 600;
        font-family: 'DM Sans', sans-serif; text-decoration: none; cursor: pointer;
        border: none; transition: all 0.15s;
    }
    .show-btn-back { background: #f9fafb; color: #374151; border: 1.5px solid #e5e7eb; }
    .show-btn-back:hover { background: #f3f4f6; color: #111827; }
    .show-btn-edit { background: #4f46e5; color: #fff; }
    .show-btn-edit:hover { background: #4338ca; color: #fff; }
    .show-btn svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2; }

    .show-body { padding: 20px; }

    .show-layout {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 16px;
        align-items: start;
    }
    @media (max-width: 900px) {
        .show-layout { grid-template-columns: 1fr; }
    }

    .show-left { display: flex; flex-direction: column; gap: 14px; }

    .show-card {
        background: #fff; border: 1px solid #f1f5f9;
        border-radius: 16px; overflow: hidden;
        transition: box-shadow 0.18s;
    }
    .show-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.06); }
    .show-card-header {
        display: flex; align-items: center; gap: 10px;
        padding: 12px 16px; background: #fafbfc;
        border-bottom: 1px solid #f8fafc;
    }
    .show-card-header-icon {
        width: 30px; height: 30px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 15px; flex-shrink: 0;
    }
    .show-card-header-title {
        font-size: 11px; font-weight: 600; letter-spacing: 0.07em;
        text-transform: uppercase; color: #6b7280;
    }
    .show-card-body { padding: 16px; }

    .student-hero { display: flex; align-items: center; gap: 14px; }
    .student-hero-avatar {
        width: 54px; height: 54px; border-radius: 15px;
        background: linear-gradient(135deg, #ec4899, #f43f5e);
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; font-weight: 700; color: #fff; flex-shrink: 0;
        box-shadow: 0 4px 14px rgba(236,72,153,0.35);
    }
    .student-hero-name { font-size: 16px; font-weight: 700; color: #111827; }
    .student-hero-badge {
        display: inline-flex; align-items: center; gap: 5px;
        margin-top: 5px; padding: 3px 10px;
        background: #fce7f3; border-radius: 20px;
        font-size: 11px; font-weight: 600; color: #be185d;
    }

    .info-row {
        display: flex; align-items: flex-start;
        padding: 10px 0; border-bottom: 1px solid #f8fafc; gap: 10px;
    }
    .info-row:last-child { border-bottom: none; padding-bottom: 0; }
    .info-row:first-child { padding-top: 0; }
    .info-row-icon {
        width: 28px; height: 28px; border-radius: 7px;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; flex-shrink: 0; margin-top: 1px;
    }
    .info-row-label { font-size: 10.5px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 2px; }
    .info-row-value { font-size: 13px; font-weight: 600; color: #111827; }

    .badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 11px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .badge-stop  { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
    .badge-route { background: #ede9fe; border: 1px solid #c4b5fd; color: #6d28d9; }

    /* Multi-route list */
    .route-list { display: flex; flex-direction: column; gap: 8px; }
    .route-item {
        display: flex; align-items: center; gap: 10px;
        padding: 10px 12px; border-radius: 10px;
        border: 1.5px solid #e5e7eb; background: #fafafa;
    }
    .route-item.active-route {
        border-color: #c4b5fd; background: #ede9fe;
    }
    .route-color-dot {
        width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0;
    }
    .route-item-name { font-size: 13px; font-weight: 600; color: #111827; flex: 1; }
    .route-item-reg  { font-size: 11px; color: #6b7280; font-family: monospace; }
    .route-active-badge {
        font-size: 10px; font-weight: 700; padding: 2px 8px;
        background: #4f46e5; color: #fff; border-radius: 20px;
    }

    .bus-visual {
        display: flex; align-items: center; gap: 12px;
        padding: 12px 14px;
        background: linear-gradient(135deg, #fffbeb, #fef3c7);
        border: 1px solid #fde68a; border-radius: 11px;
    }
    .bus-visual-icon {
        width: 42px; height: 42px; border-radius: 11px;
        background: #fff; border: 1px solid #fde68a;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; flex-shrink: 0;
    }
    .bus-visual-reg  { font-size: 15px; font-weight: 700; color: #111827; font-family: monospace; }
    .bus-visual-type { font-size: 11.5px; color: #92400e; margin-top: 2px; font-weight: 500; }

    .fee-display {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 16px;
        background: linear-gradient(135deg, #fefce8, #fef9c3);
        border: 1px solid #fde68a; border-radius: 11px;
    }
    .fee-display-label  { font-size: 11px; font-weight: 600; color: #92400e; text-transform: uppercase; letter-spacing: 0.07em; }
    .fee-display-amount { font-size: 22px; font-weight: 700; color: #78350f; }
    .fee-display-icon   { font-size: 26px; }

    /* Map */
    .map-card {
        background: #fff; border: 1px solid #f1f5f9;
        border-radius: 16px; overflow: hidden;
        box-shadow: 0 2px 12px rgba(0,0,0,0.05);
        position: sticky; top: 80px;
    }
    .map-card-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 12px 18px; background: #fafbfc;
        border-bottom: 1px solid #f1f5f9; flex-wrap: wrap; gap: 10px;
    }
    .map-card-title {
        display: flex; align-items: center; gap: 9px;
        font-size: 13px; font-weight: 600; color: #111827;
    }
    .map-card-title-icon {
        width: 30px; height: 30px; border-radius: 8px; background: #ede9fe;
        display: flex; align-items: center; justify-content: center; font-size: 15px;
    }
    .map-legend { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
    .legend-item { display: flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 500; color: #6b7280; }
    .legend-dot  { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }

    /* Dynamic route legend items */
    .route-legends { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .route-legend-item {
        display: flex; align-items: center; gap: 5px;
        font-size: 11px; font-weight: 500; color: #374151;
        padding: 3px 8px; border-radius: 20px; border: 1px solid #e5e7eb;
        background: #f9fafb;
    }
    .route-legend-line {
        width: 18px; height: 3px; border-radius: 2px; flex-shrink: 0;
    }

    #route-map { width: 100%; height: 500px; z-index: 1; }

    /* Map loading overlay */
    .map-loading-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(255,255,255,0.82);
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        z-index: 999; border-radius: 0;
        gap: 10px;
        pointer-events: none;
        transition: opacity 0.3s;
    }
    .map-loading-overlay.hidden { opacity: 0; pointer-events: none; }
    .map-loading-spinner {
        width: 36px; height: 36px;
        border: 3px solid #e5e7eb;
        border-top-color: #4f46e5;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    .map-loading-text { font-size: 12px; font-weight: 600; color: #6b7280; font-family: 'DM Sans', sans-serif; }
    @keyframes spin { to { transform: rotate(360deg); } }

    .map-wrapper { position: relative; }

    .stop-list {
        padding: 12px 16px; border-top: 1px solid #f1f5f9;
        display: flex; flex-wrap: wrap; gap: 7px;
    }
    .stop-chip {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 3px 10px; border-radius: 20px;
        font-size: 11.5px; font-weight: 500;
        background: #f8fafc; border: 1px solid #e5e7eb; color: #6b7280;
    }
    .stop-chip.assigned {
        background: #fee2e2; border-color: #fca5a5;
        color: #dc2626; font-weight: 700;
    }

    .show-empty { color: #9ca3af; font-size: 13px; font-style: italic; }
</style>

<div class="nxl-content without-header nxl-full-content show-wrap">
    <div class="main-content d-flex">
        @include('Travels.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            {{-- Header --}}
            <div class="show-page-header">
                <div class="show-page-title">
                    <span class="title-icon">🗺️</span>
                    Mapping Details
                </div>
                <div class="show-header-actions">
                    <a href="{{ route('admin.studentroutemapping.index') }}" class="show-btn show-btn-back">
                        <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
                        Back
                    </a>
                    <a href="{{ route('admin.studentroutemapping.edit', $mapping->id) }}" class="show-btn show-btn-edit">
                        <svg viewBox="0 0 24 24">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                        Edit
                    </a>
                </div>
            </div>

            <div class="show-body">
                <div class="show-layout">

                    {{-- ── LEFT: Info Cards ── --}}
                    <div class="show-left">

                        {{-- Student --}}
                        <div class="show-card">
                            <div class="show-card-header">
                                <div class="show-card-header-icon" style="background:#fce7f3;">🎓</div>
                                <div class="show-card-header-title">Student</div>
                            </div>
                            <div class="show-card-body">
                                @if($mapping->student)
                                    @php
                                        $initials = strtoupper(substr($mapping->student->first_name ?? '?', 0, 1))
                                                  . strtoupper(substr($mapping->student->last_name  ?? '', 0, 1));
                                    @endphp
                                    <div class="student-hero">
                                        <div class="student-hero-avatar">{{ $initials }}</div>
                                        <div>
                                            <div class="student-hero-name">
                                                {{ $mapping->student->first_name }} {{ $mapping->student->last_name }}
                                            </div>
                                            <div class="student-hero-badge">🎓 Student</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="show-empty">Student not found</span>
                                @endif
                            </div>
                        </div>

                        {{-- Stop --}}
                        <div class="show-card">
                            <div class="show-card-header">
                                <div class="show-card-header-icon" style="background:#fee2e2;">📍</div>
                                <div class="show-card-header-title">Boarding Stop</div>
                            </div>
                            <div class="show-card-body">
                                @if($mapping->stop)
                                    <div class="info-row">
                                        <div class="info-row-icon" style="background:#fee2e2;">📍</div>
                                        <div>
                                            <div class="info-row-label">Stop Name</div>
                                            <div class="info-row-value">
                                                <span class="badge" style="background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;">
                                                    📍 {{ $mapping->stop->stop_name }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    @if($mapping->stop->arrival_time)
                                    <div class="info-row">
                                        <div class="info-row-icon" style="background:#f0fdf4;">⏰</div>
                                        <div>
                                            <div class="info-row-label">Arrival Time</div>
                                            <div class="info-row-value">{{ $mapping->stop->arrival_time }}</div>
                                        </div>
                                    </div>
                                    @endif
                                    @if($mapping->stop->order !== null)
                                    <div class="info-row">
                                        <div class="info-row-icon" style="background:#eff6ff;">🔢</div>
                                        <div>
                                            <div class="info-row-label">Stop Order</div>
                                            <div class="info-row-value">#{{ $mapping->stop->order }}</div>
                                        </div>
                                    </div>
                                    @endif
                                @else
                                    <span class="show-empty">No stop assigned</span>
                                @endif
                            </div>
                        </div>

                        {{-- Routes --}}
                        <div class="show-card">
                            <div class="show-card-header">
                                <div class="show-card-header-icon" style="background:#ede9fe;">🛣️</div>
                                <div class="show-card-header-title">
                                    Routes via this Stop
                                    @if($allRoutesForStop->count() > 1)
                                        <span style="margin-left:6px;background:#fef3c7;color:#92400e;padding:1px 7px;border-radius:20px;font-size:10px;">
                                            {{ $allRoutesForStop->count() }} routes
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="show-card-body">
                                @php
                                    $routeColors = ['#4f46e5','#16a34a','#ea580c','#0891b2','#9333ea','#db2777','#b45309','#0f766e'];
                                @endphp
                                @if($allRoutesForStop->count() > 0)
                                    <div class="route-list">
                                        @foreach($allRoutesForStop as $i => $route)
                                            @php $color = $routeColors[$i % count($routeColors)]; @endphp
                                            <div class="route-item {{ $mapping->route_id == $route->id ? 'active-route' : '' }}">
                                                <div class="route-color-dot" style="background:{{ $color }};"></div>
                                                <div style="flex:1;">
                                                    <div class="route-item-name">{{ $route->name }}</div>
                                                    @if($route->vehicle)
                                                        <div class="route-item-reg">🚌 {{ $route->vehicle->registration_number }}</div>
                                                    @endif
                                                </div>
                                                @if($mapping->route_id == $route->id)
                                                    <span class="route-active-badge">Assigned</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="show-empty">No routes found</span>
                                @endif
                            </div>
                        </div>

                        {{-- Bus --}}
                        <div class="show-card">
                            <div class="show-card-header">
                                <div class="show-card-header-icon" style="background:#fef3c7;">🚌</div>
                                <div class="show-card-header-title">Assigned Bus</div>
                            </div>
                            <div class="show-card-body">
                                @if($mapping->route && $mapping->route->vehicle)
                                    <div class="bus-visual">
                                        <div class="bus-visual-icon">🚌</div>
                                        <div>
                                            <div class="bus-visual-reg">{{ $mapping->route->vehicle->registration_number }}</div>
                                            <div class="bus-visual-type">{{ $mapping->route->vehicle->type }}</div>
                                        </div>
                                    </div>
                                    @if($mapping->route->vehicle->capacity)
                                    <div class="info-row" style="margin-top:12px;">
                                        <div class="info-row-icon" style="background:#f0f9ff;">💺</div>
                                        <div>
                                            <div class="info-row-label">Capacity</div>
                                            <div class="info-row-value">{{ $mapping->route->vehicle->capacity }} seats</div>
                                        </div>
                                    </div>
                                    @endif
                                @else
                                    <span class="show-empty">No bus assigned</span>
                                @endif
                            </div>
                        </div>

                        {{-- Fee --}}
                        <div class="show-card">
                            <div class="show-card-header">
                                <div class="show-card-header-icon" style="background:#fefce8;">💰</div>
                                <div class="show-card-header-title">Transport Fee</div>
                            </div>
                            <div class="show-card-body">
                                @if($mapping->transport_fee)
                                    <div class="fee-display">
                                        <div>
                                            <div class="fee-display-label">Total Fee</div>
                                            <div class="fee-display-amount">₹{{ number_format($mapping->transport_fee, 2) }}</div>
                                        </div>
                                        <div class="fee-display-icon">💰</div>
                                    </div>
                                @else
                                    <span class="show-empty">No fee assigned</span>
                                @endif
                            </div>
                        </div>

                    </div>{{-- end show-left --}}

                    {{-- ── RIGHT: Map ── --}}
                    <div class="map-card">
                        <div class="map-card-header">
                            <div class="map-card-title">
                                <div class="map-card-title-icon">🗺️</div>
                                Route Map
                            </div>
                            <div class="map-legend">
                                <div class="route-legends" id="route-legends"></div>
                                <div class="legend-item">
                                    <div class="legend-dot" style="background:#dc2626;width:14px;height:14px;border:2px solid #fff;box-shadow:0 1px 4px rgba(0,0,0,0.2);"></div>
                                    Your Stop
                                </div>
                            </div>
                        </div>

                        <div class="map-wrapper">
                            {{-- Loading overlay --}}
                            <div class="map-loading-overlay" id="map-loading">
                                <div class="map-loading-spinner"></div>
                                <div class="map-loading-text">Loading road routes…</div>
                            </div>
                            <div id="route-map"></div>
                        </div>

                        {{-- Stop chips for assigned route --}}
                        @if($mapping->route && $mapping->route->stops->count())
                        <div class="stop-list">
                            @foreach($mapping->route->stops->sortBy('order') as $stop)
                                @php $isAssigned = $mapping->stop_id == $stop->id; @endphp
                                <span class="stop-chip {{ $isAssigned ? 'assigned' : '' }}">
                                    {{ $isAssigned ? '📍' : '•' }}
                                    {{ $stop->stop_name }}
                                    @if($isAssigned) ← Your Stop @endif
                                </span>
                            @endforeach
                        </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/leaflet-polylineoffset@1.1.1/leaflet.polylineoffset.js"></script>
<script>
document.addEventListener('DOMContentLoaded', async function () {

    const assignedStopId  = {{ $mapping->stop_id ?? 'null' }};
    const assignedRouteId = {{ $mapping->route_id ?? 'null' }};

    const assignedStopLat = {{ $mapping->stop->lat ?? 'null' }};
    const assignedStopLng = {{ $mapping->stop->lng ?? 'null' }};

    const routesMapData = @json($routesMapData);

    const ROUTE_COLORS = ['#4f46e5','#16a34a','#ea580c','#0891b2','#9333ea','#db2777','#b45309','#0f766e'];

    // ── Map init ──────────────────────────────────────────────────
    let centerLat = 20.5937, centerLng = 78.9629, zoom = 5;

    if (assignedStopLat && assignedStopLng) {
        centerLat = assignedStopLat;
        centerLng = assignedStopLng;
        zoom = 13;
    } else if (routesMapData.length > 0 && routesMapData[0].stops.length > 0) {
        centerLat = routesMapData[0].stops[0].lat;
        centerLng = routesMapData[0].stops[0].lng;
        zoom = 12;
    }

    const map = L.map('route-map').setView([centerLat, centerLng], zoom);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors', maxZoom: 19
    }).addTo(map);

    // ── OSRM road-based route fetcher ────────────────────────────
    /**
     * waypoints: [[lat, lng], [lat, lng], ...]
     * Returns road-following [lat, lng] array from OSRM.
     * Falls back to null on error (caller will use straight line).
     *
     * NOTE: OSRM public server has a limit of ~10,000 req/day.
     * For production, self-host: https://github.com/Project-OSRM/osrm-backend
     */
    async function fetchRoadCoords(waypoints) {
        // Filter out any null/invalid points
        const validPoints = waypoints.filter(([lat, lng]) => lat != null && lng != null);
        if (validPoints.length < 2) return null;

        // OSRM expects lng,lat order (opposite of Leaflet)
        const coordStr = validPoints.map(([lat, lng]) => `${lng},${lat}`).join(';');

        const url = `https://router.project-osrm.org/route/v1/driving/${coordStr}`
                  + `?overview=full&geometries=geojson&continue_straight=false`;

        try {
            const res = await fetch(url, { signal: AbortSignal.timeout(8000) });
            if (!res.ok) return null;

            const data = await res.json();
            if (data.code !== 'Ok' || !data.routes || !data.routes[0]) return null;

            // Convert GeoJSON [lng, lat] → Leaflet [lat, lng]
            return data.routes[0].geometry.coordinates.map(([lng, lat]) => [lat, lng]);

        } catch (err) {
            console.warn('OSRM fetch failed, falling back to straight line:', err.message);
            return null;
        }
    }

    // ── Icon factory ──────────────────────────────────────────────
    function makeIcon(color, label, size) {
        return L.divIcon({
            className: '',
            html: `<div style="
                width:${size}px;height:${size}px;border-radius:50%;
                background:${color};border:3px solid #fff;
                box-shadow:0 2px 8px rgba(0,0,0,0.3);
                display:flex;align-items:center;justify-content:center;
                font-size:${Math.round(size*0.38)}px;color:#fff;font-weight:700;
                font-family:'DM Sans',sans-serif;
            ">${label}</div>`,
            iconSize:   [size, size],
            iconAnchor: [size/2, size/2],
            popupAnchor:[0, -(size/2 + 4)]
        });
    }

    function makeRedPin() {
        return L.divIcon({
            className: '',
            html: `<div style="position:relative;width:44px;height:44px;">
                <div style="
                    position:absolute;top:0;left:0;
                    width:44px;height:44px;border-radius:50%;
                    background:rgba(220,38,38,0.25);
                    animation:pulse 1.5s ease-out infinite;
                "></div>
                <div style="
                    position:absolute;top:7px;left:7px;
                    width:30px;height:30px;border-radius:50%;
                    background:#dc2626;border:3px solid #fff;
                    box-shadow:0 3px 10px rgba(220,38,38,0.5);
                    display:flex;align-items:center;justify-content:center;
                    font-size:14px;
                ">📍</div>
            </div>
            <style>
                @keyframes pulse {
                    0%   { transform: scale(0.8); opacity: 0.8; }
                    100% { transform: scale(2);   opacity: 0; }
                }
            </style>`,
            iconSize:   [44, 44],
            iconAnchor: [22, 22],
            popupAnchor:[0, -26]
        });
    }

    const allBounds    = [];
    const legendContainer = document.getElementById('route-legends');

    // ── Offset: multiple routes same road pe ho to side-by-side dikhao ──
    // e.g. 5 routes → offsets: -8, -4, 0, +4, +8  (pixels)
    const totalRoutes = routesMapData.length;
    const OFFSET_STEP = 4; // pixels gap between each route
    function getOffset(idx) {
        if (totalRoutes <= 1) return 0;
        const mid = (totalRoutes - 1) / 2;
        return Math.round((idx - mid) * OFFSET_STEP);
    }

    // ── Draw all routes (with road-based paths) ───────────────────
    const drawPromises = routesMapData.map(async function(route, idx) {
        const color      = ROUTE_COLORS[idx % ROUTE_COLORS.length];
        const isAssigned = route.id == assignedRouteId;
        const offset     = getOffset(idx); // negative = left, positive = right

        // ── Legend item ──
        const legendEl = document.createElement('div');
        legendEl.className = 'route-legend-item';
        legendEl.innerHTML = `<div class="route-legend-line" style="background:${color};"></div>${route.name}${isAssigned ? ' ✓' : ''}`;
        legendContainer.appendChild(legendEl);

        // ── Collect all waypoints for OSRM ──
        // Order: start → stops (sorted by order) → end
        const waypoints = []; // [[lat, lng], ...]

        if (route.start_lat && route.start_lng) {
            waypoints.push([route.start_lat, route.start_lng]);
        }

        // Sort stops by order before adding
        const sortedStops = [...route.stops].sort((a, b) => (a.order ?? 0) - (b.order ?? 0));
        sortedStops.forEach(stop => {
            if (stop.lat && stop.lng) waypoints.push([stop.lat, stop.lng]);
        });

        if (route.end_lat && route.end_lng) {
            waypoints.push([route.end_lat, route.end_lng]);
        }

        // ── Add Start marker ──
        if (route.start_lat && route.start_lng) {
            allBounds.push([route.start_lat, route.start_lng]);
            L.marker([route.start_lat, route.start_lng], { icon: makeIcon(color, 'S', 28) })
                .addTo(map)
                .bindPopup(`<b style="color:${color}">🟢 Start</b><br><span style="font-size:12px">${route.name}</span>`);
        }

        // ── Add Stop markers (skip assigned stop — red pin added later) ──
        sortedStops.forEach(function(stop) {
            if (!stop.lat || !stop.lng) return;
            allBounds.push([stop.lat, stop.lng]);

            const isThisAssigned = (stop.id == assignedStopId);
            if (!isThisAssigned) {
                L.marker([stop.lat, stop.lng], { icon: makeIcon(color, '•', 22) })
                    .addTo(map)
                    .bindPopup(`<div style="font-family:'DM Sans',sans-serif;">
                        <div style="font-size:12px;font-weight:600;color:#111827;">${stop.name}</div>
                        <div style="font-size:11px;color:#9ca3af;">Stop #${stop.order} — ${route.name}</div>
                        ${stop.arrival ? `<div style="font-size:11px;color:#6b7280;margin-top:3px;">⏰ ${stop.arrival}</div>` : ''}
                    </div>`);
            }
        });

        // ── Add End marker ──
        if (route.end_lat && route.end_lng) {
            allBounds.push([route.end_lat, route.end_lng]);
            L.marker([route.end_lat, route.end_lng], { icon: makeIcon(color, 'E', 28) })
                .addTo(map)
                .bindPopup(`<b style="color:${color}">🔴 End</b><br><span style="font-size:12px">${route.name}</span>`);
        }

        // ── Fetch road-based coordinates from OSRM ──
        if (waypoints.length >= 2) {
            // OSRM has a max of 100 waypoints per request
            // If more than 100, chunk and stitch
            let roadCoords = null;

            if (waypoints.length <= 100) {
                roadCoords = await fetchRoadCoords(waypoints);
            } else {
                // Chunk into groups of 100 with 1-point overlap and stitch
                roadCoords = [];
                for (let i = 0; i < waypoints.length; i += 99) {
                    const chunk = waypoints.slice(i, i + 100);
                    const chunkCoords = await fetchRoadCoords(chunk);
                    if (chunkCoords) {
                        // Remove first point of subsequent chunks to avoid duplicates
                        roadCoords = roadCoords.concat(i === 0 ? chunkCoords : chunkCoords.slice(1));
                    } else {
                        // One chunk failed — fall back to straight lines for whole route
                        roadCoords = null;
                        break;
                    }
                }
            }

            // Draw polyline — road-based if available, straight-line fallback
            const lineCoords = roadCoords || waypoints;
            const isFallback = !roadCoords;

            // leaflet-polylineoffset plugin use karo —
            // `offset` option se line ko pixels mein left/right shift karo
            // Isse same road pe multiple routes side-by-side dikhti hain
            L.polyline(lineCoords, {
                color:     color,
                weight:    isAssigned ? 6 : 4,
                opacity:   isAssigned ? 1 : 0.75,
                dashArray: null,      // sab solid — koi dotted nahi
                lineJoin:  'round',
                lineCap:   'round',
                offset:    offset     // ← yahi magic hai: har route ko shift karo
            }).addTo(map)
            .bindTooltip(route.name + (isFallback ? ' (approx.)' : ''), {
                permanent: false, sticky: true,
                className: 'leaflet-tooltip',
                direction: 'top'
            });
        }
    });

    // ── Wait for all routes to be drawn ──────────────────────────
    await Promise.all(drawPromises);

    // ── Red pin for assigned stop (drawn last so it's on top) ────
    if (assignedStopLat && assignedStopLng) {
        let assignedStopName = '', assignedArrival = '';
        routesMapData.forEach(r => r.stops.forEach(s => {
            if (s.id == assignedStopId) {
                assignedStopName = s.name;
                assignedArrival  = s.arrival || '';
            }
        }));

        L.marker([assignedStopLat, assignedStopLng], { icon: makeRedPin(), zIndexOffset: 1000 })
            .addTo(map)
            .bindPopup(`<div style="font-family:'DM Sans',sans-serif;min-width:160px;">
                <div style="font-size:13px;font-weight:700;color:#dc2626;margin-bottom:4px;">📍 ${assignedStopName}</div>
                <div style="font-size:11px;background:#fee2e2;color:#dc2626;padding:2px 8px;border-radius:12px;display:inline-block;font-weight:600;">Your Boarding Stop</div>
                ${assignedArrival ? `<div style="font-size:11px;color:#6b7280;margin-top:5px;">⏰ ${assignedArrival}</div>` : ''}
            </div>`)
            .openPopup();

        allBounds.push([assignedStopLat, assignedStopLng]);
    }

    // ── Fit bounds after everything is drawn ─────────────────────
    if (allBounds.length > 0) {
        map.fitBounds(allBounds, { padding: [40, 40] });
    }

    // ── Hide loading overlay ──────────────────────────────────────
    const loadingEl = document.getElementById('map-loading');
    if (loadingEl) {
        loadingEl.classList.add('hidden');
        setTimeout(() => loadingEl.remove(), 400);
    }
});
</script>

@endsection