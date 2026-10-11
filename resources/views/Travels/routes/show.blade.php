@extends('admin_layout')
@section('content')

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">

<style>
    :root {
        --indigo:     #4f46e5;
        --indigo-dark:#3730a3;
        --indigo-soft:#ede9fe;
        --green:      #22c55e;
        --red:        #ef4444;
        --slate-900:  #0f172a;
        --slate-700:  #334155;
        --slate-500:  #64748b;
        --slate-300:  #cbd5e1;
        --slate-100:  #f1f5f9;
        --slate-50:   #f8fafc;
        --white:      #ffffff;
    }

    .sv-wrap * { box-sizing: border-box; margin: 0; padding: 0; }
    .sv-wrap {
        font-family: 'DM Sans', sans-serif;
        background: var(--slate-50);
        min-height: 100vh;
    }

    /* ══════════════════════════════
       TOP BAR
    ══════════════════════════════ */
    .sv-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 28px;
        background: var(--white);
        border-bottom: 1px solid var(--slate-100);
        position: sticky;
        top: 0;
        z-index: 100;
    }
    .sv-topbar-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .sv-back-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 14px;
        background: var(--slate-50);
        border: 1.5px solid var(--slate-300);
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        color: var(--slate-700);
        text-decoration: none;
        font-family: 'DM Sans', sans-serif;
        transition: all 0.15s;
    }
    .sv-back-btn:hover {
        background: var(--white);
        border-color: #a5b4fc;
        color: var(--indigo);
    }
    .sv-back-btn svg { width: 14px; height: 14px; stroke: currentColor; }

    .sv-breadcrumb {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: var(--slate-500);
    }
    .sv-breadcrumb a {
        color: var(--slate-500);
        text-decoration: none;
        transition: color 0.12s;
    }
    .sv-breadcrumb a:hover { color: var(--indigo); }
    .sv-breadcrumb .sep { color: var(--slate-300); }
    .sv-breadcrumb .current { color: var(--slate-900); font-weight: 600; }

    .sv-topbar-right {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .sv-edit-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 8px 18px;
        background: var(--indigo);
        color: var(--white);
        border: none;
        border-radius: 10px;
        font-size: 13.5px;
        font-weight: 600;
        font-family: 'DM Sans', sans-serif;
        text-decoration: none;
        cursor: pointer;
        transition: background 0.15s, transform 0.1s;
    }
    .sv-edit-btn:hover  { background: var(--indigo-dark); color: var(--white); }
    .sv-edit-btn:active { transform: scale(0.97); }
    .sv-edit-btn svg { width: 14px; height: 14px; stroke: currentColor; }

    /* ══════════════════════════════
       MAIN LAYOUT  —  2 columns
       Left: info panel (360px)
       Right: map (fills rest)
    ══════════════════════════════ */
    .sv-layout {
        display: flex;
        height: calc(100vh - 57px); /* full viewport minus topbar */
        overflow: hidden;
    }

    /* ── LEFT PANEL ── */
    .sv-panel {
        width: 360px;
        flex-shrink: 0;
        background: var(--white);
        border-right: 1px solid var(--slate-100);
        overflow-y: auto;
        display: flex;
        flex-direction: column;
    }

    /* Panel header */
    .sv-panel-header {
        padding: 22px 22px 18px;
        border-bottom: 1px solid var(--slate-100);
        background: var(--white);
        position: sticky;
        top: 0;
        z-index: 2;
    }
    .sv-route-name {
        font-family: 'Syne', sans-serif;
        font-size: 20px;
        font-weight: 800;
        color: var(--slate-900);
        line-height: 1.2;
        margin-bottom: 10px;
        letter-spacing: -0.3px;
    }

    /* Start → End path */
    .sv-path {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        background: var(--slate-50);
        border-radius: 10px;
        border: 1px solid var(--slate-100);
    }
    .sv-path-point {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12.5px;
        font-weight: 600;
        color: var(--slate-700);
        flex: 1;
        min-width: 0;
    }
    .sv-path-point span {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .sv-dot {
        width: 9px; height: 9px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .sv-dot.s { background: var(--green);  box-shadow: 0 0 0 3px rgba(34,197,94,0.18); }
    .sv-dot.e { background: var(--red);    box-shadow: 0 0 0 3px rgba(239,68,68,0.18); }
    .sv-path-arrow {
        color: var(--slate-300);
        font-size: 16px;
        flex-shrink: 0;
    }

    /* Stats strip */
    .sv-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        padding: 16px 22px;
        border-bottom: 1px solid var(--slate-100);
    }
    .sv-stat-card {
        padding: 12px 14px;
        background: var(--slate-50);
        border-radius: 10px;
        border: 1px solid var(--slate-100);
    }
    .sv-stat-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--slate-500);
        margin-bottom: 5px;
    }
    .sv-stat-val {
        font-size: 15px;
        font-weight: 700;
        color: var(--slate-900);
        font-family: 'Syne', sans-serif;
    }
    .sv-stat-icon { font-size: 16px; margin-bottom: 6px; display: block; }

    /* Vehicle card */
    .sv-vehicle {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 22px;
        border-bottom: 1px solid var(--slate-100);
    }
    .sv-vehicle-icon {
        width: 42px; height: 42px;
        border-radius: 12px;
        background: var(--indigo-soft);
        display: flex; align-items: center; justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .sv-vehicle-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--slate-500);
        margin-bottom: 3px;
    }
    .sv-vehicle-type {
        font-size: 14px;
        font-weight: 700;
        color: var(--slate-900);
    }
    .sv-vehicle-reg {
        font-size: 12px;
        color: var(--slate-500);
        margin-top: 1px;
    }

    /* Description */
    .sv-desc {
        padding: 14px 22px;
        border-bottom: 1px solid var(--slate-100);
        font-size: 13px;
        color: var(--slate-500);
        line-height: 1.6;
    }
    .sv-desc-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--slate-500);
        margin-bottom: 5px;
    }

    /* Section heading */
    .sv-section-heading {
        padding: 14px 22px 10px;
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.09em;
        color: var(--slate-500);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .sv-stop-count {
        background: var(--indigo-soft);
        color: var(--indigo);
        font-size: 10px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 20px;
    }

    /* Stops timeline */
    .sv-stops { padding: 0 22px 24px; }
    .sv-stop-item {
        display: flex;
        gap: 14px;
        position: relative;
    }

    /* Vertical connector line */
    .sv-stop-item:not(:last-child) .sv-stop-line-wrap::after {
        content: '';
        position: absolute;
        left: 13px;
        top: 30px;
        bottom: -10px;
        width: 2px;
        background: var(--slate-100);
        z-index: 0;
    }

    .sv-stop-line-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        padding-top: 3px;
        flex-shrink: 0;
    }
    .sv-stop-badge {
        width: 28px; height: 28px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 11px;
        font-weight: 800;
        color: var(--white);
        background: var(--indigo);
        z-index: 1;
        flex-shrink: 0;
        font-family: 'Syne', sans-serif;
        border: 2px solid var(--white);
        box-shadow: 0 2px 8px rgba(79,70,229,0.25);
    }
    .sv-stop-badge.first { background: var(--green);  box-shadow: 0 2px 8px rgba(34,197,94,0.3); }
    .sv-stop-badge.last  { background: var(--red);    box-shadow: 0 2px 8px rgba(239,68,68,0.3); }

    .sv-stop-content {
        flex: 1;
        padding: 0 0 18px;
        min-width: 0;
    }
    .sv-stop-name {
        font-size: 13.5px;
        font-weight: 700;
        color: var(--slate-900);
        margin-bottom: 3px;
        line-height: 1.3;
    }
    .sv-stop-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }
    .sv-stop-time {
        font-size: 11.5px;
        color: var(--indigo);
        font-weight: 600;
        background: var(--indigo-soft);
        padding: 2px 8px;
        border-radius: 6px;
    }
    .sv-stop-coords {
        font-size: 11px;
        color: var(--slate-300);
        font-family: monospace;
    }

    /* Empty stops */
    .sv-no-stops {
        padding: 20px 22px;
        text-align: center;
        color: var(--slate-300);
        font-size: 13px;
    }

    /* ── RIGHT MAP ── */
    .sv-map-wrap {
        flex: 1;
        position: relative;
        min-width: 0;
    }
    #sv-map {
        width: 100%;
        height: 100%;
        z-index: 0;
    }

    /* Map overlay badge — route name */
    .sv-map-badge {
        position: absolute;
        top: 16px;
        left: 16px;
        z-index: 400;
        background: var(--white);
        border-radius: 12px;
        padding: 10px 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        font-family: 'Syne', sans-serif;
        font-size: 14px;
        font-weight: 800;
        color: var(--slate-900);
        display: flex;
        align-items: center;
        gap: 8px;
        pointer-events: none;
    }
    .sv-map-badge-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        background: var(--indigo);
        animation: pulse 1.8s ease-in-out infinite;
    }
    @keyframes pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(79,70,229,0.4); }
        50%       { box-shadow: 0 0 0 6px rgba(79,70,229,0); }
    }

    /* Legend on map */
    .sv-map-legend {
        position: absolute;
        bottom: 24px;
        left: 16px;
        z-index: 400;
        background: var(--white);
        border-radius: 10px;
        padding: 10px 14px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
        gap: 6px;
        pointer-events: none;
    }
    .sv-legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        font-weight: 600;
        color: var(--slate-700);
    }
    .sv-legend-dot {
        width: 10px; height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    /* Scrollbar styling for panel */
    .sv-panel::-webkit-scrollbar { width: 4px; }
    .sv-panel::-webkit-scrollbar-track { background: transparent; }
    .sv-panel::-webkit-scrollbar-thumb { background: var(--slate-100); border-radius: 4px; }
    .sv-panel::-webkit-scrollbar-thumb:hover { background: var(--slate-300); }

    /* Animate panel items in */
    .sv-panel-header  { animation: fadeUp 0.3s ease both; }
    .sv-stats         { animation: fadeUp 0.35s ease both; }
    .sv-vehicle       { animation: fadeUp 0.4s ease both; }
    .sv-desc          { animation: fadeUp 0.45s ease both; }
    .sv-section-heading { animation: fadeUp 0.5s ease both; }
    .sv-stops         { animation: fadeUp 0.55s ease both; }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* Loading overlay */
    .sv-map-loading {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 500;
        flex-direction: column;
        gap: 12px;
        font-family: 'DM Sans', sans-serif;
        color: var(--slate-700);
    }
    .sv-map-loading.hide {
        display: none;
    }
    .sv-spinner {
        width: 40px;
        height: 40px;
        border: 3px solid var(--slate-100);
        border-top-color: var(--indigo);
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    .sv-error-message {
        position: absolute;
        bottom: 80px;
        right: 20px;
        background: #fff3e0;
        border-left: 4px solid #f59e0b;
        padding: 12px 16px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        font-size: 13px;
        color: #92400e;
        z-index: 400;
        max-width: 280px;
        pointer-events: none;
    }
</style>

<div class="sv-wrap">
    <div class="main-content d-flex">
        @include('Travels.sidebar')

        <div style="flex:1; display:flex; flex-direction:column; min-width:0; overflow:hidden;">

            {{-- ── TOP BAR ── --}}
            <div class="sv-topbar">
                <div class="sv-topbar-left">
                    <a href="{{ route('admin.routes.index') }}" class="sv-back-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5">
                            <polyline points="15 18 9 12 15 6"/>
                        </svg>
                        Back
                    </a>
                    <div class="sv-breadcrumb">
                        <a href="{{ route('admin.routes.index') }}">Routes</a>
                        <span class="sep">›</span>
                        <span class="current">{{ $route->name }}</span>
                    </div>
                </div>
                <div class="sv-topbar-right">
                    <a href="{{ route('admin.routes.edit', $route->id) }}" class="sv-edit-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                        Edit Route
                    </a>
                </div>
            </div>

            {{-- ── MAIN LAYOUT ── --}}
            <div class="sv-layout">

                {{-- ── LEFT PANEL ── --}}
                <div class="sv-panel">

                    {{-- Route name + path --}}
                    <div class="sv-panel-header">
                        <div class="sv-route-name">{{ $route->name }}</div>
                        <div class="sv-path">
                            <div class="sv-path-point">
                                <span class="sv-dot s"></span>
                                <span>{{ $route->start_point ?: 'Start' }}</span>
                            </div>
                            <span class="sv-path-arrow">→</span>
                            <div class="sv-path-point">
                                <span class="sv-dot e"></span>
                                <span>{{ $route->end_point ?: 'End' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Stats --}}
                    <div class="sv-stats">
                        <div class="sv-stat-card">
                            <span class="sv-stat-icon">📏</span>
                            <div class="sv-stat-label">Distance</div>
                            <div class="sv-stat-val">
                                {{ $route->route_distance ? $route->route_distance . ' km' : '—' }}
                            </div>
                        </div>
                        <div class="sv-stat-card">
                            <span class="sv-stat-icon">⏱️</span>
                            <div class="sv-stat-label">Duration</div>
                            <div class="sv-stat-val">
                                {{ $route->route_duration ? $route->route_duration . ' min' : '—' }}
                            </div>
                        </div>
                        <div class="sv-stat-card">
                            <span class="sv-stat-icon">📍</span>
                            <div class="sv-stat-label">Total Stops</div>
                            <div class="sv-stat-val">{{ $route->stops->count() }}</div>
                        </div>
                        <div class="sv-stat-card">
                            <span class="sv-stat-icon">🚦</span>
                            <div class="sv-stat-label">Status</div>
                            <div class="sv-stat-val" style="font-size:13px;color:#22c55e;">Active</div>
                        </div>
                    </div>

                    {{-- Vehicle --}}
                    <div class="sv-vehicle">
                        <div class="sv-vehicle-icon">🚌</div>
                        <div>
                            <div class="sv-vehicle-label">Assigned Vehicle</div>
                            <div class="sv-vehicle-type">{{ $route->vehicle->type ?? 'N/A' }}</div>
                            <div class="sv-vehicle-reg">{{ $route->vehicle->registration_number ?? '—' }}</div>
                        </div>
                    </div>

                    {{-- Description --}}
                    @if($route->description)
                    <div class="sv-desc">
                        <div class="sv-desc-label">Description</div>
                        {{ $route->description }}
                    </div>
                    @endif

                    {{-- Stops --}}
                    <div class="sv-section-heading">
                        <span>🛑 Stops</span>
                        <span class="sv-stop-count">{{ $route->stops->count() }} stops</span>
                    </div>

                    @if($route->stops->isEmpty())
                        <div class="sv-no-stops">No stops added to this route.</div>
                    @else
                        <div class="sv-stops">
                            @foreach($route->stops->sortBy('order') as $i => $stop)
                                @php
                                    $total     = $route->stops->count();
                                    $isFirst   = $loop->first;
                                    $isLast    = $loop->last;
                                    $badgeClass = $isFirst ? 'first' : ($isLast ? 'last' : '');
                                    $badgeLabel = $isFirst ? 'S' : ($isLast ? 'E' : $loop->iteration);
                                @endphp
                                <div class="sv-stop-item">
                                    <div class="sv-stop-line-wrap">
                                        <div class="sv-stop-badge {{ $badgeClass }}">{{ $badgeLabel }}</div>
                                    </div>
                                    <div class="sv-stop-content">
                                        <div class="sv-stop-name">{{ $stop->stop_name }}</div>
                                        <div class="sv-stop-meta">
                                            @if($stop->arrival_time)
                                                <span class="sv-stop-time">🕐 {{ $stop->arrival_time }}</span>
                                            @endif
                                            @if($stop->lat && $stop->lng)
                                                <span class="sv-stop-coords">
                                                    {{ number_format($stop->lat, 4) }}, {{ number_format($stop->lng, 4) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>{{-- end sv-panel --}}

                {{-- ── RIGHT MAP ── --}}
                <div class="sv-map-wrap">
                    {{-- Loading overlay --}}
                    <div class="sv-map-loading" id="map-loading">
                        <div class="sv-spinner"></div>
                        <div>Loading route...</div>
                    </div>

                    {{-- Map name badge --}}
                    <div class="sv-map-badge">
                        <span class="sv-map-badge-dot"></span>
                        {{ $route->name }}
                    </div>

                    {{-- Legend --}}
                    <div class="sv-map-legend">
                        <div class="sv-legend-item">
                            <span class="sv-legend-dot" style="background:#22c55e;"></span> Start Point
                        </div>
                        <div class="sv-legend-item">
                            <span class="sv-legend-dot" style="background:#ef4444;"></span> End Point
                        </div>
                        <div class="sv-legend-item">
                            <span class="sv-legend-dot" style="background:#4f46e5;"></span> Stop
                        </div>
                    </div>

                    <div id="sv-map"></div>
                </div>

            </div>{{-- end sv-layout --}}

        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Pass stops from PHP to JS
    const stopsData = @json($route->stops->sortBy('order')->values());
    const loadingOverlay = document.getElementById('map-loading');

    // ── Init map ─────────────────────────────────────────────────
    const map = L.map('sv-map', {
        zoomControl: true,
    }).setView([20.2961, 85.8245], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a>',
        maxZoom: 19,
    }).addTo(map);

    // ── Helper: marker color & label ────────────────────────────
    function getColor(i, total) {
        if (i === 0)         return '#22c55e';
        if (i === total - 1) return '#ef4444';
        return '#4f46e5';
    }
    function getLabel(i, total) {
        if (i === 0)         return 'S';
        if (i === total - 1) return 'E';
        return i + 1;
    }

    // ── Place markers ────────────────────────────────────────────
    const total   = stopsData.length;
    let   coords  = [];
    let   markers = [];
    let   validStops = 0;

    stopsData.forEach((stop, i) => {
        const lat = parseFloat(stop.lat);
        const lng = parseFloat(stop.lng);
        if (isNaN(lat) || isNaN(lng) || !stop.lat || !stop.lng) {
            console.warn('Invalid coordinates for stop:', stop.stop_name);
            return;
        }

        validStops++;
        coords.push(`${lng},${lat}`);

        const color = getColor(i, total);
        const label = getLabel(i, total);

        const icon = L.divIcon({
            html: `<div style="
                background:${color};
                color:white;
                border-radius:50%;
                width:34px;height:34px;
                display:flex;align-items:center;justify-content:center;
                font-weight:800;font-size:13px;
                border:3px solid white;
                box-shadow:0 4px 12px rgba(0,0,0,0.25);
                font-family:'Syne',sans-serif;
            ">${label}</div>`,
            className: '',
            iconSize:   [34, 34],
            iconAnchor: [17, 17],
        });

        const popupContent = `
            <div style="font-family:'DM Sans',sans-serif;min-width:160px;">
                <div style="font-weight:700;font-size:13px;color:#0f172a;margin-bottom:4px;">
                    ${stop.stop_name || '—'}
                </div>
                ${stop.arrival_time ? `<div style="font-size:12px;color:#4f46e5;font-weight:600;">🕐 ${stop.arrival_time}</div>` : ''}
                ${(stop.lat && stop.lng) ? `<div style="font-size:11px;color:#94a3b8;margin-top:3px;">${parseFloat(stop.lat).toFixed(4)}, ${parseFloat(stop.lng).toFixed(4)}</div>` : ''}
            </div>`;

        const marker = L.marker([lat, lng], { icon })
            .addTo(map)
            .bindPopup(popupContent, { maxWidth: 220 });
        markers.push(marker);
    });

    // Check if we have enough valid stops
    if (validStops === 0) {
        loadingOverlay.classList.add('hide');
        map.setView([20.2961, 85.8245], 10);
        const errorDiv = document.createElement('div');
        errorDiv.className = 'sv-error-message';
        errorDiv.innerHTML = '⚠️ No valid coordinates found for this route. Please add location data to stops.';
        document.querySelector('.sv-map-wrap').appendChild(errorDiv);
        setTimeout(() => errorDiv.remove(), 5000);
    } 
    // ── Draw route line via OSRM ─────────────────────────────────
    else if (coords.length >= 2) {
        const osrmUrl = `https://router.project-osrm.org/route/v1/driving/${coords.join(';')}?geometries=geojson&overview=full`;
        
        fetch(osrmUrl, {
            method: 'GET',
            mode: 'cors',
            headers: {
                'Accept': 'application/json',
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            loadingOverlay.classList.add('hide');
            
            if (!data.routes || data.routes.length === 0) {
                throw new Error('No route found by OSRM');
            }
            
            const latlngs = data.routes[0].geometry.coordinates.map(c => [c[1], c[0]]);
            
            if (latlngs.length < 2) {
                throw new Error('Invalid route geometry');
            }

            // Shadow line (thicker, lighter)
            L.polyline(latlngs, {
                color: 'rgba(79,70,229,0.15)',
                weight: 11,
                lineCap: 'round',
                lineJoin: 'round',
            }).addTo(map);

            // Main line
            const routeLine = L.polyline(latlngs, {
                color:     '#4f46e5',
                weight:    5,
                opacity:   0.95,
                lineCap:   'round',
                lineJoin:  'round',
            }).addTo(map);

            map.fitBounds(routeLine.getBounds(), { padding: [60, 60] });
        })
        .catch(error => {
            console.error('OSRM Error:', error);
            loadingOverlay.classList.add('hide');
            
            // Fallback: straight dashed line
            const latlngs = stopsData
                .filter(s => s.lat && s.lng && !isNaN(parseFloat(s.lat)) && !isNaN(parseFloat(s.lng)))
                .map(s => [parseFloat(s.lat), parseFloat(s.lng)]);
            
            if (latlngs.length >= 2) {
                const fallbackLine = L.polyline(latlngs, {
                    color:     '#f59e0b',
                    weight:    4,
                    opacity:   0.8,
                    dashArray: '10, 8',
                }).addTo(map);
                
                map.fitBounds(fallbackLine.getBounds(), { padding: [60, 60] });
                
                // Show warning message
                const warningDiv = document.createElement('div');
                warningDiv.className = 'sv-error-message';
                warningDiv.innerHTML = '⚠️ Could not calculate optimal route. Showing straight line between stops.';
                document.querySelector('.sv-map-wrap').appendChild(warningDiv);
                setTimeout(() => warningDiv.remove(), 5000);
            } else if (markers.length === 1) {
                map.setView(markers[0].getLatLng(), 14);
            } else {
                // Just fit all markers
                const group = L.featureGroup(markers);
                map.fitBounds(group.getBounds(), { padding: [60, 60] });
            }
        });
    } else if (markers.length === 1) {
        loadingOverlay.classList.add('hide');
        map.setView(markers[0].getLatLng(), 14);
    } else if (markers.length > 1) {
        loadingOverlay.classList.add('hide');
        const group = L.featureGroup(markers);
        map.fitBounds(group.getBounds(), { padding: [60, 60] });
    } else {
        loadingOverlay.classList.add('hide');
    }
</script>

@endsection