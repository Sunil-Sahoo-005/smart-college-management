@extends('admin_layout')
@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Academics.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            {{-- ══ STICKY HEADER ══ --}}
            <div class="content-area-header bg-white px-4 py-3 border-bottom shadow-sm"
                 style="position:sticky; top:0; z-index:200;">
                <div class="d-flex align-items-center gap-3 flex-wrap w-100">

                    <div class="flex-shrink-0">
                        <h5 class="fw-bold mb-0 text-dark">Center Devices</h5>
                        <small class="text-muted">Registered devices across all exam centers</small>
                    </div>

                    {{-- Center filter --}}
                    <div class="flex-shrink-0" style="min-width:180px;">
                        <select id="centerFilter" class="form-select form-select-sm border-secondary-subtle"
                                style="border-radius:6px;">
                            <option value="">All Centers</option>
                            @foreach ($centers as $center)
                                <option value="{{ $center->id }}" {{ $centerId == $center->id ? 'selected' : '' }}>
                                    {{ $center->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Search --}}
                    <div class="position-relative flex-grow-1 d-none d-md-block" style="max-width:260px; min-width:140px;">
                        <i class="feather-search position-absolute top-50 translate-middle-y text-muted"
                           style="font-size:13px; left:10px;"></i>
                        <input type="text" id="searchInput"
                            class="form-control form-control-sm ps-4 border-secondary-subtle"
                            placeholder="Search devices…" style="border-radius:6px;">
                    </div>

                    <div class="ms-auto d-flex align-items-center gap-2 flex-shrink-0">
                        
                            <a href="{{ route('admin.center_devices.create', $centerId ? ['center_id' => $centerId] : []) }}"
                               class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1">
                                <i class="feather-plus" style="font-size:13px;"></i>
                                <span class="d-none d-sm-inline">Add Device</span>
                            </a>
                        
                    </div>
                </div>
            </div>

            <div class="content-area-body p-4">
                @include('elements.flash')

                {{-- Stats --}}
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="card border-0 h-100" style="background:#e0f2fe; border-radius:14px;">
                            <div class="card-body d-flex align-items-center gap-3 py-3">
                                <div style="width:42px;height:42px;border-radius:12px;background:#0369a120;
                                            display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="feather-monitor" style="color:#0369a1;font-size:18px;"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size:1.5rem;line-height:1;">{{ $devices->count() }}</div>
                                    <small class="text-muted">Total Devices</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border-0 h-100" style="background:#dcfce7; border-radius:14px;">
                            <div class="card-body d-flex align-items-center gap-3 py-3">
                                <div style="width:42px;height:42px;border-radius:12px;background:#15803d20;
                                            display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="feather-check-circle" style="color:#15803d;font-size:18px;"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size:1.5rem;line-height:1;">{{ $devices->where('status','active')->count() }}</div>
                                    <small class="text-muted">Active</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border-0 h-100" style="background:#ffe4e6; border-radius:14px;">
                            <div class="card-body d-flex align-items-center gap-3 py-3">
                                <div style="width:42px;height:42px;border-radius:12px;background:#be123c20;
                                            display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="feather-x-circle" style="color:#be123c;font-size:18px;"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size:1.5rem;line-height:1;">{{ $devices->where('status','inactive')->count() }}</div>
                                    <small class="text-muted">Inactive</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border-0 h-100" style="background:#fef9c3; border-radius:14px;">
                            <div class="card-body d-flex align-items-center gap-3 py-3">
                                <div style="width:42px;height:42px;border-radius:12px;background:#a1620720;
                                            display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="feather-cpu" style="color:#a16207;font-size:18px;"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size:1.5rem;line-height:1;">{{ $centers->count() }}</div>
                                    <small class="text-muted">Centers</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Table --}}
                <div class="card stretch stretch-full">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Device Code</th>
                                        <th>Device Name</th>
                                        <th>Center</th>
                                        <th>Device Identifier</th>
                                        <th>MAC Address</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="devicesTbody">
                                    @forelse ($devices as $i => $device)
                                        <tr id="device-row-{{ $device->id }}"
                                            data-center-id="{{ $device->center_id }}">
                                            <td>{{ $i + 1 }}</td>
                                            <td>
                                                <span class="badge"
                                                    style="background:#e0f2fe;color:#0369a1;border-radius:8px;
                                                           font-size:.78rem;font-weight:700;padding:4px 10px;letter-spacing:.5px;">
                                                    {{ $device->device_code }}
                                                </span>
                                            </td>
                                            <td class="fw-semibold">{{ $device->device_name }}</td>
                                            <td>
                                                <span class="badge"
                                                    style="background:#ede9fe;color:#6d28d9;border-radius:8px;
                                                           font-size:.75rem;padding:4px 10px;">
                                                    {{ $device->center?->name ?? '—' }}
                                                </span>
                                            </td>
                                            <td>
                                                <code style="background:#f1f5f9;padding:2px 8px;border-radius:6px;
                                                             font-size:.78rem;word-break:break-all;">
                                                    {{ $device->device_identifier }}
                                                </code>
                                            </td>
                                            <td class="text-muted" style="font-size:.82rem;">
                                                {{ $device->mac_address ?: '—' }}
                                            </td>
                                            <td class="text-center">
                                                <span class="status-badge-{{ $device->id }} badge
                                                    {{ $device->status === 'active' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}"
                                                    style="border-radius:20px;padding:4px 12px;">
                                                    {{ ucfirst($device->status) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="hstack gap-2 justify-content-center">

                                                 
                                                  
                                                        <a href="{{ route('admin.center_devices.edit', $device) }}"
                                                           class="avatar-text avatar-md text-warning" title="Edit">
                                                            <i class="feather-edit-3"></i>
                                                        </a>
                                                   
                                                        <form action="{{ route('admin.center_devices.destroy', $device) }}"
                                                              method="POST" class="d-inline"
                                                              onsubmit="return confirm('Delete this device?')">
                                                            @csrf @method('DELETE')
                                                            <button type="submit"
                                                                class="avatar-text avatar-md border-0 bg-transparent text-danger"
                                                                title="Delete">
                                                                <i class="feather-trash-2"></i>
                                                            </button>
                                                        </form>
                                                    
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr id="emptyRow">
                                            <td colspan="8" class="text-center py-5">
                                                <i class="feather-monitor text-muted d-block mb-3" style="font-size:3rem;"></i>
                                                <h6 class="text-muted">No devices registered yet</h6>
                                                <p class="text-muted small mb-0">Add a device to an exam center to get started</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>{{-- /content-area-body --}}
        </div>
    </div>
</div>

<script>
const CSRF_TOKEN = '{{ csrf_token() }}';

// ── Center filter → redirect with ?center_id= ─────────────
document.getElementById('centerFilter')?.addEventListener('change', function () {
    const val = this.value;
    const url = new URL(window.location.href);
    if (val) url.searchParams.set('center_id', val);
    else     url.searchParams.delete('center_id');
    window.location.href = url.toString();
});

// ── Live search ────────────────────────────────────────────
document.getElementById('searchInput')?.addEventListener('input', function () {
    const q = this.value.trim().toLowerCase();
    document.querySelectorAll('#devicesTbody tr[id^="device-row-"]').forEach(row => {
        row.style.display = (!q || row.textContent.toLowerCase().includes(q)) ? '' : 'none';
    });
});

// ── Toggle Status ──────────────────────────────────────────
document.querySelectorAll('.toggle-status-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const deviceId = this.dataset.deviceId;
        const self     = this;
        self.disabled  = true;
        self.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        fetch(`/admin/center-devices/${deviceId}/toggle-status`, {
            method:  'POST',
            headers: {
                'X-CSRF-TOKEN':     CSRF_TOKEN,
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type':     'application/json',
            },
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const isActive = data.status === 'active';
                self.disabled  = false;
                self.className = `avatar-text avatar-md border-0 bg-transparent toggle-status-btn ${isActive ? 'text-success' : 'text-secondary'}`;
                self.innerHTML = `<i class="feather-toggle-${isActive ? 'right' : 'left'}"></i>`;

                const badge = document.querySelector(`.status-badge-${deviceId}`);
                if (badge) {
                    badge.className   = `status-badge-${deviceId} badge ${isActive ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'}`;
                    badge.style.cssText = 'border-radius:20px;padding:4px 12px;';
                    badge.textContent = isActive ? 'Active' : 'Inactive';
                }
                showToast(data.message, 'success');
            } else {
                showToast(data.message || 'Failed to update.', 'danger');
                self.disabled  = false;
                self.innerHTML = '<i class="feather-toggle-right"></i>';
            }
        })
        .catch(() => {
            showToast('Network error.', 'danger');
            self.disabled  = false;
            self.innerHTML = '<i class="feather-toggle-right"></i>';
        });
    });
});

// ── Toast ──────────────────────────────────────────────────
function showToast(msg, type = 'info') {
    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:8px;';
        document.body.appendChild(container);
    }
    const toast = document.createElement('div');
    toast.style.cssText = `background:${type==='danger'?'#fee2e2':type==='success'?'#dcfce7':'#e0f2fe'};
        color:${type==='danger'?'#dc2626':type==='success'?'#15803d':'#0369a1'};
        border:1.5px solid ${type==='danger'?'#fca5a5':type==='success'?'#86efac':'#7dd3fc'};
        border-radius:10px;padding:12px 18px;font-size:.875rem;font-weight:500;
        box-shadow:0 4px 16px rgba(0,0,0,.1);max-width:340px;animation:slideInToast .3s ease;`;
    toast.innerHTML = `<i class="feather-${type==='danger'?'alert-circle':type==='success'?'check-circle':'info'} me-2"></i>${msg}`;
    container.appendChild(toast);
    setTimeout(() => { toast.style.opacity='0'; toast.style.transition='opacity .3s'; setTimeout(() => toast.remove(), 300); }, 3500);
}
</script>

<style>
@keyframes slideInToast {
    from { transform: translateX(40px); opacity: 0; }
    to   { transform: translateX(0);    opacity: 1; }
}
.table th { white-space: nowrap; font-size: .8125rem; padding: .75rem 1rem; }
.table td { padding: .75rem 1rem; vertical-align: middle; font-size: .875rem; }
.content-area { overflow-y: auto !important; }
</style>
@endsection