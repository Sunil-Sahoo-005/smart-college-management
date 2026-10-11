@extends('admin_layout')
@section('content')

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">

<style>
    .route-form-wrap * { box-sizing: border-box; }
    .route-form-wrap { font-family: 'DM Sans', sans-serif; }

    .rf-section-label {
        font-size: 10.5px;
        font-weight: 600;
        letter-spacing: 0.09em;
        text-transform: uppercase;
        color: #9ca3af;
        margin-bottom: 14px;
        padding-bottom: 10px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .rf-section-label .section-icon {
        width: 22px; height: 22px;
        border-radius: 6px;
        display: flex; align-items: center; justify-content: center;
        font-size: 12px;
        background: #f1f5f9;
    }
    .rf-field { margin-bottom: 0; }
    .rf-label {
        font-size: 13px;
        font-weight: 500;
        color: #374151;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .rf-required {
        width: 5px; height: 5px;
        border-radius: 50%;
        background: #ef4444;
        display: inline-block;
        flex-shrink: 0;
    }
    .rf-input {
        width: 100%;
        padding: 9px 13px;
        font-size: 14px;
        font-family: 'DM Sans', sans-serif;
        border: 1.5px solid #e5e7eb;
        border-radius: 10px;
        background: #fff;
        color: #111827;
        outline: none;
        transition: border-color 0.18s, box-shadow 0.18s;
        appearance: none;
        -webkit-appearance: none;
    }
    .rf-input::placeholder { color: #c4c8d0; }
    .rf-input:hover  { border-color: #c7d2fe; }
    .rf-input:focus  { border-color: #6366f1; box-shadow: 0 0 0 3.5px rgba(99,102,241,0.12); }
    .rf-divider { height: 1px; background: #f1f5f9; margin: 22px 0; }

    /* ── Stop rows ── */
    .stops-list { display: flex; flex-direction: column; gap: 10px; margin-bottom: 10px; }
    .stop-row {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 14px;
        border: 1.5px solid #e5e7eb;
        border-radius: 12px;
        background: #fff;
        transition: border-color 0.15s;
    }
    .stop-row:hover { border-color: #c7d2fe; }
    .stop-loc-wrap   { flex: 2; position: relative; min-width: 0; }
    .stop-time-wrap  { flex: 0 0 130px; }
    .stop-order-wrap { flex: 0 0 90px; position: relative; }
    .stop-order-wrap .rf-input {
        text-align: center;
        font-weight: 700;
        color: #4f46e5;
        padding-right: 28px;
    }
    .stop-order-wrap .rf-input.duplicate {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239,68,68,0.12);
        color: #ef4444;
    }
    .duplicate-icon {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 14px;
        display: none;
        pointer-events: none;
    }
    .stop-order-wrap .rf-input.duplicate ~ .duplicate-icon { display: block; }

    /* Apply Order button */
    .btn-apply-order {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 18px;
        background: #4f46e5;
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 13.5px;
        font-weight: 600;
        font-family: 'DM Sans', sans-serif;
        cursor: pointer;
        transition: background 0.14s;
        margin-top: 4px;
    }
    .btn-apply-order:hover { background: #4338ca; }

    /* Remove button */
    .btn-remove {
        width: 30px; height: 30px;
        border-radius: 8px;
        border: 1.5px solid #fee2e2;
        background: #fff;
        color: #ef4444;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        flex-shrink: 0;
        transition: background 0.12s, border-color 0.12s;
    }
    .btn-remove:hover { background: #fee2e2; border-color: #fca5a5; }

    /* Add stop button */
    .btn-add-stop {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 18px;
        background: transparent;
        color: #4f46e5;
        border: 1.5px dashed #a5b4fc;
        border-radius: 10px;
        font-size: 13.5px;
        font-weight: 600;
        font-family: 'DM Sans', sans-serif;
        cursor: pointer;
        transition: background 0.14s, border-color 0.14s;
        margin-top: 4px;
        width: 100%;
        justify-content: center;
    }
    .btn-add-stop:hover { background: #f5f3ff; border-color: #818cf8; }

    /* ── Manual Modal ── */
    .manual-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.4);
        z-index: 99999;
        align-items: center;
        justify-content: center;
    }
    .manual-modal-overlay.show { display: flex; }
    .manual-modal {
        background: #fff;
        border-radius: 16px;
        padding: 28px;
        width: 100%;
        max-width: 520px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        font-family: 'DM Sans', sans-serif;
    }
    .manual-modal-title {
        font-size: 15px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .manual-modal-close {
        width: 28px; height: 28px;
        border-radius: 8px;
        border: 1.5px solid #e5e7eb;
        background: #fff;
        color: #6b7280;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        font-size: 14px;
    }
    .manual-modal-close:hover { background: #fee2e2; border-color: #fca5a5; color: #ef4444; }
    .modal-field { margin-bottom: 14px; }
    .modal-field label {
        font-size: 13px;
        font-weight: 500;
        color: #374151;
        margin-bottom: 6px;
        display: block;
    }

    /* Side by side lat/lng */
    .modal-row {
        display: flex;
        gap: 12px;
    }
    .modal-row .modal-field { flex: 1; }

    /* Mini map inside modal */
    #modal-map {
        height: 220px;
        width: 100%;
        border-radius: 10px;
        border: 1.5px solid #e5e7eb;
        margin-bottom: 14px;
        z-index: 0;
    }
    .modal-btn-row { display: flex; gap: 8px; margin-top: 4px; }
    .modal-btn-add {
        flex: 1;
        padding: 10px;
        background: #4f46e5;
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        font-family: 'DM Sans', sans-serif;
        cursor: pointer;
        transition: background 0.15s;
    }
    .modal-btn-add:hover { background: #4338ca; }
    .modal-btn-cancel {
        padding: 10px 18px;
        background: transparent;
        color: #6b7280;
        border: 1.5px solid #e5e7eb;
        border-radius: 10px;
        font-size: 14px;
        font-family: 'DM Sans', sans-serif;
        cursor: pointer;
    }
    .modal-btn-cancel:hover { background: #f9fafb; }

    /* Map */
    #map {
        height: 400px;
        width: 100%;
        border-radius: 12px;
        border: 1.5px solid #e5e7eb;
        z-index: 0;
        overflow: hidden;
    }
    .rf-btn-row { display: flex; gap: 10px; margin-top: 4px; }
    .rf-btn-primary {
        padding: 10px 26px;
        background: #4f46e5;
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        font-family: 'DM Sans', sans-serif;
        cursor: pointer;
        transition: background 0.15s, transform 0.1s;
    }
    .rf-btn-primary:hover  { background: #4338ca; }
    .rf-btn-primary:active { transform: scale(0.98); }
    .rf-btn-secondary {
        padding: 10px 20px;
        background: transparent;
        color: #6b7280;
        border: 1.5px solid #e5e7eb;
        border-radius: 10px;
        font-size: 14px;
        font-family: 'DM Sans', sans-serif;
        cursor: pointer;
        transition: background 0.15s, border-color 0.15s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }
    .rf-btn-secondary:hover { background: #f9fafb; border-color: #d1d5db; color: #374151; }
</style>

<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Travels.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">
            <div class="content-area-header bg-white sticky-top">
                <h6 class="card-title mb-0">
                    {{ isset($routes->id) ? 'Edit Route' : 'Create Route' }}
                </h6>
            </div>

            <div class="content-area-body">
                @include('elements.flash')

                <div class="card stretch stretch-full">
                    <div class="card-body route-form-wrap">
                        <form method="POST" action="{{ route('admin.routes.store') }}">
                            @csrf

                            @if(isset($routes->id))
                                <input type="hidden" name="id" value="{{ $routes->id }}">
                            @endif

                            {{-- ── ROUTE DETAILS ── --}}
                            <div class="rf-section-label">
                                <span class="section-icon">📋</span> Route Details
                            </div>

                            <div style="max-width: 420px;">
                                <div class="rf-field mb-3">
                                    <label class="rf-label"><span class="rf-required"></span> Route Name</label>
                                    <input type="text" name="name" class="rf-input"
                                           value="{{ old('name', $routes->name ?? '') }}"
                                           placeholder="e.g. Bhubaneswar → Puri" required autocomplete="off">
                                </div>
                                <div class="rf-field mb-3">
                                    <label class="rf-label">Description</label>
                                    <input type="text" name="description" class="rf-input"
                                           value="{{ old('description', $routes->description ?? '') }}"
                                           placeholder="Optional description" autocomplete="off">
                                </div>
                                <div class="rf-field mb-3">
                                    <label class="rf-label"><span class="rf-required"></span> Vehicle</label>
                                    <select name="vehicle_id" class="rf-input" required>
                                        <option value="">-- Select Vehicle --</option>
                                        @foreach($vehicle as $v)
                                            <option value="{{ $v->id }}"
                                                {{ old('vehicle_id', $routes->vehicle_id ?? '') == $v->id ? 'selected' : '' }}>
                                                {{ $v->type }} — {{ $v->registration_number }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="rf-divider"></div>

                            {{-- ── STOPS ── --}}
                            <div class="rf-section-label">
                                <span class="section-icon">📍</span> Stops
                            </div>

                            {{-- Stops list --}}
                            <div id="stops-container" class="stops-list"></div>

                            {{-- Apply Order + Add Stop buttons --}}
                            <div style="display:flex; gap:10px; margin-top:10px; flex-wrap:wrap;">
                                <button type="button" class="btn-apply-order" onclick="applyOrder()">
                                    ✅ Apply Order & Redraw Map
                                </button>
                                <div id="tab-manual-btn">
                                    <button type="button" class="btn-add-stop" onclick="openManualModal()" style="margin-top:0;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <line x1="12" y1="5" x2="12" y2="19"/>
                                            <line x1="5" y1="12" x2="19" y2="12"/>
                                        </svg>
                                        ✏️ Add Stop Manually
                                    </button>
                                </div>
                            </div>

                            <div class="rf-divider"></div>

                            {{-- ── MAP ── --}}
                            <div class="rf-section-label">
                                <span class="section-icon">🗺️</span> Map Preview
                            </div>
                            <div id="map" class="mb-4"></div>

                            <div class="rf-divider"></div>

                            {{-- ── BUTTONS ── --}}
                            <div class="rf-btn-row">
                                <button type="submit" class="rf-btn-primary">
                                    {{ isset($routes->id) ? '💾 Update Route' : '+ Save Route' }}
                                </button>
                                <a href="{{ route('admin.routes.index') }}" class="rf-btn-secondary">Cancel</a>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Manual Entry Modal with mini map ── --}}
<div class="manual-modal-overlay" id="manualModal">
    <div class="manual-modal">
        <div class="manual-modal-title">
            ✏️ Add Stop Manually
            <button type="button" class="manual-modal-close" onclick="closeManualModal()">✕</button>
        </div>

        {{-- Order No + Stop Name + Time --}}
        <div class="modal-row">
            <div class="modal-field" style="flex: 0 0 90px;">
                <label>Order No <span style="color:#ef4444;">*</span></label>
                <input type="number" id="manual_order" class="rf-input" placeholder="e.g. 1" min="1"
                       style="text-align:center; font-weight:700; color:#4f46e5;" autocomplete="off">
            </div>
            <div class="modal-field">
                <label>Stop Name <span style="color:#ef4444;">*</span></label>
                <input type="text" id="manual_name" class="rf-input" placeholder="e.g. Bhubaneswar Bus Stand" autocomplete="off">
            </div>
            <div class="modal-field" style="flex: 0 0 120px;">
                <label>Arrival Time</label>
                <input type="time" id="manual_time" class="rf-input" autocomplete="off">
            </div>
        </div>

        {{-- Lat + Lng side by side --}}
        <div class="modal-row">
            <div class="modal-field">
                <label>Latitude <span style="color:#ef4444;">*</span></label>
                <input type="number" id="manual_lat" class="rf-input"
                       placeholder="e.g. 20.2961" step="any"
                       oninput="updateModalMap()" autocomplete="off">
            </div>
            <div class="modal-field">
                <label>Longitude <span style="color:#ef4444;">*</span></label>
                <input type="number" id="manual_lng" class="rf-input"
                       placeholder="e.g. 85.8245" step="any" autocomplete="off">
                       oninput="updateModalMap()">
            </div>
        </div>

        {{-- Mini map preview inside modal --}}
        <div id="modal-map"></div>

        <div class="modal-btn-row">
            <button type="button" class="modal-btn-add" onclick="addManualStop()">+ Add Stop</button>
            <button type="button" class="modal-btn-cancel" onclick="closeManualModal()">Cancel</button>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const existingStops = @json($routes->stops ?? []);

    // ── Main map ─────────────────────────────────────────────────
    const map = L.map('map').setView([20.2961, 85.8245], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    let routeLine   = null;
    let stopMarkers = [];

    // Modal mini map
    let modalMap    = null;
    let modalMarker = null;

    // ── Modal open ───────────────────────────────────────────────
    function openManualModal() {
        document.getElementById('manualModal').classList.add('show');
        document.getElementById('manual_name').value = '';
        document.getElementById('manual_lat').value  = '';
        document.getElementById('manual_lng').value  = '';
        document.getElementById('manual_time').value = '';

        // Auto-suggest next order
        const existingOrders = [...document.querySelectorAll('.stop-order-input')]
            .map(i => parseInt(i.value)).filter(v => !isNaN(v));
        const nextOrder = existingOrders.length > 0 ? Math.max(...existingOrders) + 1 : 0;
        document.getElementById('manual_order').value = nextOrder;

        setTimeout(() => {
            if (!modalMap) {
                modalMap = L.map('modal-map').setView([20.2961, 85.8245], 12);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap'
                }).addTo(modalMap);
            } else {
                modalMap.invalidateSize();
            }
        }, 100);
    }

    // ── Modal close ──────────────────────────────────────────────
    function closeManualModal() {
        document.getElementById('manualModal').classList.remove('show');
        if (modalMarker) { modalMarker.remove(); modalMarker = null; }
    }

    // ── Real-time modal map update ───────────────────────────────
    function updateModalMap() {
        const lat = parseFloat(document.getElementById('manual_lat').value);
        const lng = parseFloat(document.getElementById('manual_lng').value);

        if (!modalMap || isNaN(lat) || isNaN(lng)) return;
        if (lat < -90 || lat > 90 || lng < -180 || lng > 180) return;

        if (modalMarker) {
            modalMarker.setLatLng([lat, lng]);
        } else {
            modalMarker = L.marker([lat, lng], {
                icon: L.divIcon({
                    html: `<div style="background:#4f46e5;color:white;border-radius:50%;
                                width:30px;height:30px;display:flex;align-items:center;justify-content:center;
                                font-weight:700;font-size:14px;border:2.5px solid white;
                                box-shadow:0 3px 8px rgba(0,0,0,.25)">📍</div>`,
                    className: '', iconSize: [30,30], iconAnchor: [15,15]
                })
            }).addTo(modalMap);
        }
        modalMap.setView([lat, lng], 14);
    }

    // ── Manual stop add ──────────────────────────────────────────
    function addManualStop() {
        const orderVal = document.getElementById('manual_order').value;
        const name     = document.getElementById('manual_name').value.trim();
        const lat      = document.getElementById('manual_lat').value.trim();
        const lng      = document.getElementById('manual_lng').value.trim();
        const time     = document.getElementById('manual_time').value;

        if (orderVal === '' || parseInt(orderVal) < 0) { alert('Order No required! (0 se start hota hai)'); return; }
        if (!name)        { alert('Stop name required!'); return; }
        if (!lat || !lng) { alert('Latitude aur Longitude required!'); return; }

        addStopRow({ order: parseInt(orderVal), name, lat, lng, time });
        checkDuplicates();
        closeManualModal();
    }

    // Modal background click se close
    document.getElementById('manualModal').addEventListener('click', function(e) {
        if (e.target === this) closeManualModal();
    });

    // ── Add stop row ─────────────────────────────────────────────
    function addStopRow(prefill = {}) {
        const container = document.getElementById('stops-container');
        const index     = document.querySelectorAll('.stop-row').length;
        const orderVal  = (prefill.order !== undefined && prefill.order !== null) ? prefill.order : '';

        const div = document.createElement('div');
        div.classList.add('stop-row');
        div.innerHTML = `
            <div class="stop-order-wrap">
                <input type="number" class="rf-input stop-order-input"
                       name="stops[${index}][order]"
                       value="${orderVal}"
                       min="0" placeholder="0"
                       title="Order No"
                       oninput="checkDuplicates()">
                <span class="duplicate-icon" title="Duplicate order!">⚠️</span>
            </div>
            <div class="stop-loc-wrap">
                <input type="text" class="rf-input"
                       value="${prefill.name || ''}"
                       placeholder="Stop name…" readonly
                       style="background:#f9fafb; cursor:default;">
                <input type="hidden" name="stops[${index}][name]" value="${prefill.name || ''}">
                <input type="hidden" name="stops[${index}][lat]"  value="${prefill.lat  || ''}">
                <input type="hidden" name="stops[${index}][lng]"  value="${prefill.lng  || ''}">
            </div>
            <div class="stop-time-wrap">
                <input type="time" name="stops[${index}][time]"
                       value="${prefill.time || ''}"
                       class="rf-input" title="Arrival time">
            </div>
            <button type="button" class="btn-remove" onclick="removeRow(this)" title="Remove stop">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6"  y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        `;
        container.appendChild(div);
    }

    // ── Remove row ───────────────────────────────────────────────
    function removeRow(btn) {
        btn.closest('.stop-row').remove();
        reindexStops();
        checkDuplicates();
        drawRoute();
    }

    // ── Reindex input names after remove ─────────────────────────
    function reindexStops() {
        document.querySelectorAll('.stop-row').forEach((row, i) => {
            row.querySelector('.stop-order-input').name          = `stops[${i}][order]`;
            row.querySelector('input[name$="[name]"]').name      = `stops[${i}][name]`;
            row.querySelector('input[name$="[lat]"]').name       = `stops[${i}][lat]`;
            row.querySelector('input[name$="[lng]"]').name       = `stops[${i}][lng]`;
            row.querySelector('input[name$="[time]"]').name      = `stops[${i}][time]`;
        });
    }

    // ── Duplicate check ───────────────────────────────────────────
    function checkDuplicates() {
        const inputs = [...document.querySelectorAll('.stop-order-input')];
        const values = inputs.map(i => i.value.trim());
        inputs.forEach((input, idx) => {
            const val   = input.value.trim();
            const isDup = val !== '' && values.indexOf(val) !== idx;
            input.classList.toggle('duplicate', isDup);
        });
    }

    // ── Apply Order — sort DOM rows by order value, then redraw ──
    // FIX: setTimeout(drawRoute, 100) taaki DOM pehle settle ho
    function applyOrder() {
        const container = document.getElementById('stops-container');
        const rows      = [...container.querySelectorAll('.stop-row')];

        rows.sort((a, b) => {
            const oa = parseInt(a.querySelector('.stop-order-input').value);
            const ob = parseInt(b.querySelector('.stop-order-input').value);
            if (isNaN(oa)) return 1;
            if (isNaN(ob)) return -1;
            return oa - ob;
        });

        rows.forEach(row => container.appendChild(row));
        reindexStops();
        checkDuplicates();
        setTimeout(drawRoute, 100); // ✅ FIX 1: DOM settle hone ke baad draw karo
    }

    // ── Helpers ──────────────────────────────────────────────────
    function getStopLabel(index, total) {
        if (index === 0)         return 'S';
        if (index === total - 1) return 'E';
        return index + 1;
    }

    function getMarkerColor(index, total) {
        if (index === 0)         return '#22c55e';
        if (index === total - 1) return '#ef4444';
        return '#4f46e5';
    }

    // ── Draw route ───────────────────────────────────────────────
    // FIX: DOM order pe depend nahi, order input values se khud sort karta hai
    function drawRoute() {
        // ✅ FIX 2 (MAIN FIX): Rows ko order input value se sort karo
        const allRows = [...document.querySelectorAll('.stop-row')];
        const rows = allRows.sort((a, b) => {
            const oa = parseInt(a.querySelector('.stop-order-input').value);
            const ob = parseInt(b.querySelector('.stop-order-input').value);
            if (isNaN(oa)) return 1;
            if (isNaN(ob)) return -1;
            return oa - ob;
        });

        const total = rows.length;
        let coords  = [];

        // Clear old markers
        stopMarkers.forEach(m => m.remove());
        stopMarkers = [];

        rows.forEach((row, i) => {
            const lat  = row.querySelector('input[name$="[lat]"]').value;
            const lng  = row.querySelector('input[name$="[lng]"]').value;
            const name = row.querySelector('input[name$="[name]"]').value;

            if (lat && lng) {
                coords.push(`${lng},${lat}`);
                const icon = L.divIcon({
                    html: `<div style="background:${getMarkerColor(i, total)};color:white;border-radius:50%;
                                width:30px;height:30px;display:flex;align-items:center;justify-content:center;
                                font-weight:700;font-size:12px;border:2.5px solid white;
                                box-shadow:0 3px 8px rgba(0,0,0,.25)">${getStopLabel(i, total)}</div>`,
                    className: '', iconSize: [30,30], iconAnchor: [15,15]
                });
                const marker = L.marker([parseFloat(lat), parseFloat(lng)], { icon })
                    .addTo(map)
                    .bindPopup(`<b>${getStopLabel(i, total)}:</b> ${name}`);
                stopMarkers.push(marker);
            }
        });

        if (coords.length < 2) {
            if (routeLine) { routeLine.remove(); routeLine = null; }
            return;
        }
        if (routeLine) routeLine.remove();

        fetch(`https://router.project-osrm.org/route/v1/driving/${coords.join(';')}?geometries=geojson`)
            .then(r => r.json())
            .then(data => {
                if (!data.routes || data.routes.length === 0) return;
                const latlngs = data.routes[0].geometry.coordinates.map(c => [c[1], c[0]]);
                routeLine = L.polyline(latlngs, {
                    color: '#4f46e5', weight: 5, opacity: 0.9
                }).addTo(map);
                map.fitBounds(routeLine.getBounds(), { padding: [40, 40] });
            })
            .catch(err => console.error('OSRM error:', err));
    }

    // ── Init — existing stops load karo (edit mode) ──────────────
    // FIX: timeout 500 → 800ms taaki map fully ready ho
    if (existingStops.length > 0) {
        existingStops.forEach(stop => addStopRow({
            order: stop.order,
            name:  stop.stop_name,
            lat:   stop.lat,
            lng:   stop.lng,
            time:  stop.arrival_time
        }));
        checkDuplicates();
        setTimeout(drawRoute, 800); // ✅ FIX 3: Map ready hone ka time do
    }
</script>

@endsection