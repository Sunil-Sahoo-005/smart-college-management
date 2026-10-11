@extends('admin_layout')
@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('hostel.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            {{-- HEADER --}}
            <div class="content-area-header bg-white sticky-top">
                <div class="card-header border-0 d-flex align-items-center">
                    <nav aria-label="breadcrumb" id="breadcrumbNav">
                        <ol class="breadcrumb mb-0" id="breadcrumbList">
                            <li class="breadcrumb-item active">Hostel</li>
                        </ol>
                    </nav>
                </div>
                <div class="page-header-right ms-auto">
                    @if(auth()->user()->hasPermissionTo('Create Hostel'))
                    <a href="{{ route('admin.hostel.create') }}" class="btn btn-primary">
                        <i class="feather-plus me-2"></i> Create Hostel
                    </a>
                    @endif
                </div>
            </div>

            {{-- BODY --}}
            <div class="content-area-body p-4">
                @include('elements.flash')

                {{-- LEVEL 1: Boys / Girls type cards --}}
                <div id="level1">
                    <p class="text-muted mb-3" style="font-size:13px;">Select hostel type to view hostels</p>
                    <div class="row g-3">
                        @php
                            $boysCount  = $hostelsByType->get('boys',  collect())->count();
                            $girlsCount = $hostelsByType->get('girls', collect())->count();
                            $boysRooms  = $hostelsByType->get('boys',  collect())->sum(fn($h) => $h->rooms->count());
                            $girlsRooms = $hostelsByType->get('girls', collect())->sum(fn($h) => $h->rooms->count());
                        @endphp

                        {{-- BOYS --}}
                        @if($boysCount > 0)
                        <div class="col-md-4 col-sm-6">
                            <div class="card border-0 shadow-sm h-100"
                                 style="border-radius:14px; cursor:pointer; transition:box-shadow .2s;"
                                 onmouseover="this.style.boxShadow='0 6px 20px rgba(0,0,0,0.1)'"
                                 onmouseout="this.style.boxShadow=''"
                                 onclick="goLevel2('boys')">
                                <div class="card-body p-4">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mb-3"
                                         style="width:52px; height:52px; background:#eef1fc;">
                                        <i class="feather-home" style="color:#3a3f5c; font-size:22px;"></i>
                                    </div>
                                    <h6 class="fw-medium mb-1">Boys Hostel</h6>
                                    <p class="text-muted mb-3" style="font-size:13px;">
                                        {{ $boysCount }} {{ Str::plural('hostel', $boysCount) }} &bull;
                                        {{ $boysRooms }} {{ Str::plural('room', $boysRooms) }}
                                    </p>
                                    <span style="font-size:12px; color:#3a3f5c; font-weight:500;">
                                        View all <i class="feather-arrow-right" style="font-size:13px;"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- GIRLS --}}
                        @if($girlsCount > 0)
                        <div class="col-md-4 col-sm-6">
                            <div class="card border-0 shadow-sm h-100"
                                 style="border-radius:14px; cursor:pointer; transition:box-shadow .2s;"
                                 onmouseover="this.style.boxShadow='0 6px 20px rgba(0,0,0,0.1)'"
                                 onmouseout="this.style.boxShadow=''"
                                 onclick="goLevel2('girls')">
                                <div class="card-body p-4">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mb-3"
                                         style="width:52px; height:52px; background:#fce7f3;">
                                        <i class="feather-home" style="color:#9d174d; font-size:22px;"></i>
                                    </div>
                                    <h6 class="fw-medium mb-1">Girls Hostel</h6>
                                    <p class="text-muted mb-3" style="font-size:13px;">
                                        {{ $girlsCount }} {{ Str::plural('hostel', $girlsCount) }} &bull;
                                        {{ $girlsRooms }} {{ Str::plural('room', $girlsRooms) }}
                                    </p>
                                    <span style="font-size:12px; color:#9d174d; font-weight:500;">
                                        View all <i class="feather-arrow-right" style="font-size:13px;"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>

                {{-- LEVEL 2: Hostels of selected type --}}
                <div id="level2" style="display:none;">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <button class="btn btn-sm btn-outline-secondary" onclick="goLevel1()">
                            <i class="feather-arrow-left me-1"></i> Back
                        </button>
                        <p id="level2-heading" class="text-muted mb-0" style="font-size:13px;"></p>
                    </div>
                    <div class="row g-3" id="level2-container"></div>
                </div>

                {{-- LEVEL 3: Rooms of selected hostel --}}
                <div id="level3" style="display:none;">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <button class="btn btn-sm btn-outline-secondary" onclick="goLevel2Back()">
                            <i class="feather-arrow-left me-1"></i> Back
                        </button>
                    </div>
                    <div class="card border-0 shadow-sm" style="border-radius:14px; overflow:hidden;">
                        <div class="card-header bg-white border-bottom d-flex align-items-center px-4 py-3">
                            <h6 class="mb-0 fw-medium" id="level3-title"></h6>
                            <a id="level3-view-link" href="#" class="btn btn-sm btn-outline-primary ms-auto">
                                <i class="feather-eye me-1"></i> View Full Detail
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Room No.</th>
                                        <th>Type</th>
                                        <th>Total Beds</th>
                                        <th>Vacant</th>
                                        <th>Occupied</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="level3-tbody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
.hostel-card-actions {
    position: absolute;
    top: 10px;
    right: 12px;
    display: flex;
    gap: 6px;
    opacity: 0;
    transition: opacity .2s;
    z-index: 10;
}
.hostel-card-wrapper:hover .hostel-card-actions {
    opacity: 1;
}
.hostel-action-btn {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 14px;
    text-decoration: none;
    transition: border-color .15s, background .15s;
}
.hostel-action-btn:hover {
    border-color: #d1d5db;
    background: #f9fafb;
}
.hostel-action-btn.edit { color: #d97706; }
.hostel-action-btn.del  { color: #dc2626; }
</style>

<script>
const hostelData        = @json($hostelsByType);

// ── Route base URLs (fixed for {id}/edit pattern) ──
const routeShowHostel   = "{{ route('admin.hostel.show',        0) }}".replace('/0', '');
const routeEditHostel   = "{{ route('admin.hostel.edit',        0) }}".replace('0/edit', '');
const routeDeleteHostel = "{{ route('admin.hostel.destroy',     0) }}".replace('/0', '');
const routeEditRoom     = "{{ route('admin.hostelroom.edit',    0) }}".replace('0/edit', '');
const routeDeleteRoom   = "{{ route('admin.hostelroom.destroy', 0) }}".replace('/0', '');
const csrfToken         = "{{ csrf_token() }}";

// ── Permissions ──
const canEditHostel   = {{ auth()->user()->hasPermissionTo('Edit Hostel')        ? 'true' : 'false' }};
const canDeleteHostel = {{ auth()->user()->hasPermissionTo('Delete Hostel')      ? 'true' : 'false' }};
const canEditRoom     = {{ auth()->user()->hasPermissionTo('Edit Hostel Room')   ? 'true' : 'false' }};
const canDeleteRoom   = {{ auth()->user()->hasPermissionTo('Delete Hostel Room') ? 'true' : 'false' }};

let currentType   = null;
let currentHostel = null;

/* ═══════════════════════════ LEVEL 1 ═══════════════════════════ */
function goLevel1() {
    document.getElementById('level1').style.display = 'block';
    document.getElementById('level2').style.display = 'none';
    document.getElementById('level3').style.display = 'none';
    updateBreadcrumb(['Hostel']);
}

/* ═══════════════════════════ LEVEL 2 ═══════════════════════════ */
function goLevel2(type) {
    currentType = type;
    const hostels = hostelData[type] || [];
    const label   = type === 'boys' ? 'Boys Hostel' : 'Girls Hostel';
    const color   = type === 'boys' ? '#3a3f5c' : '#9d174d';
    const bg      = type === 'boys' ? '#eef1fc'  : '#fce7f3';

    document.getElementById('level2-heading').textContent =
        label + ' — Select a hostel to view its rooms';

    const container = document.getElementById('level2-container');
    container.innerHTML = '';

    hostels.forEach(h => {
        const rooms        = h.rooms || [];
        const totalBeds    = rooms.reduce((s, r) => s + (r.beds_count || r.beds?.length || 0), 0);
        const vacantBeds   = rooms.reduce((s, r) => s + (r.vacant_beds_count  || 0), 0);

        // ── Correct URLs ──
        const editUrl   = routeEditHostel   + h.id + '/edit';
        const deleteUrl = routeDeleteHostel + '/' + h.id;

        // ── Action buttons ──
        let actionsHtml = '';
        if (canEditHostel) {
            actionsHtml += `
                <a href="${editUrl}"
                   class="hostel-action-btn edit"
                   title="Edit Hostel"
                   onclick="event.stopPropagation()">
                    <i class="feather-edit-3"></i>
                </a>`;
        }
        if (canDeleteHostel) {
            actionsHtml += `
                <form action="${deleteUrl}" method="POST" class="d-inline"
                      onsubmit="return confirm('Are you sure you want to delete this hostel?')"
                      onclick="event.stopPropagation()">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="hostel-action-btn del" title="Delete Hostel">
                        <i class="feather-trash-2"></i>
                    </button>
                </form>`;
        }

        const col = document.createElement('div');
        col.className = 'col-md-4 col-sm-6';
        col.innerHTML = `
            <div class="card border-0 shadow-sm h-100 hostel-card-wrapper"
                 style="border-radius:14px; cursor:pointer; transition:box-shadow .2s;"
                 onmouseover="this.style.boxShadow='0 6px 20px rgba(0,0,0,0.1)'"
                 onmouseout="this.style.boxShadow=''"
                 onclick="goLevel3(${JSON.stringify(h).replace(/"/g,'&quot;')}, '${type}')">

                <div class="card-body p-4" style="position:relative;">

                    {{-- Hostel Action Buttons --}}
                    <div class="hostel-card-actions" onclick="event.stopPropagation()">
                        ${actionsHtml}
                    </div>

                    {{-- Icon + Name --}}
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:42px; height:42px; background:${bg};">
                            <i class="feather-home" style="color:${color}; font-size:18px;"></i>
                        </div>
                        <div style="min-width:0;">
                            <div class="fw-medium" style="font-size:14px;">${escapeHtml(h.name)}</div>
                            ${h.address
                                ? `<div class="text-muted text-truncate" style="font-size:12px; max-width:180px;">
                                      <i class="feather-map-pin" style="font-size:11px;"></i> ${escapeHtml(h.address)}
                                   </div>`
                                : ''}
                            ${h.caretaker
                                ? `<div class="text-muted" style="font-size:12px;">
                                      <i class="feather-user" style="font-size:11px;"></i> ${escapeHtml(h.caretaker)}
                                   </div>`
                                : ''}
                        </div>
                    </div>

                    {{-- Stats --}}
                    <div class="row g-2 text-center mb-3">
                        <div class="col-4">
                            <div style="background:#f3f4f6; border-radius:8px; padding:8px 4px;">
                                <div class="fw-medium" style="font-size:18px;">${rooms.length}</div>
                                <div class="text-muted" style="font-size:11px;">Rooms</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div style="background:#f3f4f6; border-radius:8px; padding:8px 4px;">
                                <div class="fw-medium" style="font-size:18px;">${totalBeds}</div>
                                <div class="text-muted" style="font-size:11px;">Beds</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div style="background:#f3f4f6; border-radius:8px; padding:8px 4px;">
                                <div class="fw-medium" style="font-size:18px; color:#16a34a;">${vacantBeds}</div>
                                <div class="text-muted" style="font-size:11px;">Vacant</div>
                            </div>
                        </div>
                    </div>

                    {{-- View Link --}}
                    <span style="font-size:12px; color:${color}; font-weight:500;">
                        View rooms <i class="feather-arrow-right" style="font-size:13px;"></i>
                    </span>

                </div>
            </div>`;
        container.appendChild(col);
    });

    document.getElementById('level1').style.display = 'none';
    document.getElementById('level2').style.display = 'block';
    document.getElementById('level3').style.display = 'none';
    updateBreadcrumb(['Hostel', label]);
}

/* ═══════════════════════════ LEVEL 2 BACK ═══════════════════════════ */
function goLevel2Back() {
    document.getElementById('level2').style.display = 'block';
    document.getElementById('level3').style.display = 'none';
    const label = currentType === 'boys' ? 'Boys Hostel' : 'Girls Hostel';
    updateBreadcrumb(['Hostel', label]);
}

/* ═══════════════════════════ LEVEL 3 ═══════════════════════════ */
function goLevel3(hostel, type) {
    currentHostel = hostel;
    const label   = type === 'boys' ? 'Boys Hostel' : 'Girls Hostel';
    const showUrl = routeShowHostel + '/' + hostel.id;

    document.getElementById('level3-title').textContent = hostel.name + ' — Rooms';
    document.getElementById('level3-view-link').href    = showUrl;

    const tbody = document.getElementById('level3-tbody');
    tbody.innerHTML = '';

    const rooms = hostel.rooms || [];
    if (rooms.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center text-muted py-4">
                    No rooms found for this hostel.
                </td>
            </tr>`;
    } else {
        rooms.forEach((room, i) => {
            const total    = room.beds_count          || room.beds?.length || 0;
            const vacant   = room.vacant_beds_count   || 0;
            const occupied = room.occupied_beds_count || 0;

            const typeBadge = room.type === 'ac'
                ? '<span class="badge bg-success" style="font-size:10px;">AC</span>'
                : '<span class="badge bg-warning text-dark" style="font-size:10px;">Non-AC</span>';

            const vacantCell = vacant > 0
                ? `<span class="text-success fw-medium">${vacant}</span>`
                : `<span class="text-danger fw-medium">${vacant}</span>`;

            // ── Room action buttons ──
            let actionsHtml = `<div class="hstack gap-2 justify-content-end">`;

            actionsHtml += `
                <a href="${showUrl}"
                   class="avatar-text avatar-sm"
                   title="View Hostel Detail">
                    <i class="feather-eye"></i>
                </a>`;

            if (canEditRoom) {
                // ── Fixed: /hostelroom/{id}/edit ──
                const editUrl = routeEditRoom + room.id + '/edit';
                actionsHtml += `
                    <a href="${editUrl}"
                       class="avatar-text avatar-sm text-warning"
                       title="Edit Room">
                        <i class="feather-edit-3"></i>
                    </a>`;
            }

            if (canDeleteRoom) {
                const deleteUrl = routeDeleteRoom + '/' + room.id;
                actionsHtml += `
                    <form action="${deleteUrl}" method="POST" class="d-inline"
                          onsubmit="return confirm('Are you sure you want to delete this room?')">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit"
                                class="avatar-text avatar-sm border-0 bg-transparent text-danger"
                                title="Delete Room">
                            <i class="feather-trash-2"></i>
                        </button>
                    </form>`;
            }

            actionsHtml += `</div>`;

            tbody.innerHTML += `
                <tr>
                    <td>${i + 1}</td>
                    <td class="fw-medium">${escapeHtml(room.room_number)}</td>
                    <td>${typeBadge}</td>
                    <td>${total}</td>
                    <td>${vacantCell}</td>
                    <td>${occupied}</td>
                    <td class="text-end">${actionsHtml}</td>
                </tr>`;
        });
    }

    document.getElementById('level2').style.display = 'none';
    document.getElementById('level3').style.display = 'block';
    updateBreadcrumb(['Hostel', label, hostel.name]);
}

/* ═══════════════════════════ HELPERS ═══════════════════════════ */
function updateBreadcrumb(items) {
    const ol = document.getElementById('breadcrumbList');
    ol.innerHTML = items.map((item, i) =>
        i === items.length - 1
            ? `<li class="breadcrumb-item active">${escapeHtml(item)}</li>`
            : `<li class="breadcrumb-item text-muted">${escapeHtml(item)}</li>`
    ).join('');
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

// URL params se state restore (back/forward navigation)
document.addEventListener('DOMContentLoaded', function () {
    const params   = new URLSearchParams(window.location.search);
    const type     = params.get('type');
    const hostelId = params.get('hostel_id');

    if (type) {
        goLevel2(type);
        if (hostelId) {
            const hostels = hostelData[type] || [];
            const hostel  = hostels.find(h => String(h.id) === String(hostelId));
            if (hostel) {
                setTimeout(() => goLevel3(hostel, type), 50);
            }
        }
    }
});
</script>
@endsection