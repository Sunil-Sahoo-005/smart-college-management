@extends('admin_layout')
@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Academics.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            {{-- ══ STICKY HEADER ══ --}}
            <div class="content-area-header bg-white px-4 py-3 border-bottom shadow-sm"
                 style="position:sticky;top:0;z-index:200;">
                <div class="d-flex align-items-center gap-3 flex-wrap w-100">

                    {{-- Back --}}
                    <a href="{{ route('admin.exam_center.index') }}"
                       class="btn btn-sm btn-light flex-shrink-0"
                       style="border-radius:8px;padding:5px 10px;">
                        <i class="feather-arrow-left" style="font-size:13px;"></i>
                    </a>

                    {{-- Center info --}}
                    <div class="flex-shrink-0">
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="fw-bold mb-0 text-dark">{{ $center->name }}</h5>
                            <span class="badge"
                                  style="background:#ede9fe;color:#6d28d9;border-radius:6px;
                                         font-size:.72rem;font-weight:700;letter-spacing:.4px;">
                                {{ $center->code }}
                            </span>
                            <span class="badge {{ $center->status==='active' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}"
                                  style="border-radius:20px;padding:3px 9px;font-size:.7rem;">
                                {{ ucfirst($center->status) }}
                            </span>
                        </div>
                        
                    </div>

                    {{-- Search --}}
                    <div class="position-relative flex-grow-1 d-none d-md-block" style="max-width:250px;min-width:140px;">
                        <i class="feather-search position-absolute top-50 translate-middle-y text-muted"
                           style="font-size:13px;left:10px;"></i>
                        <input type="text" id="searchInput"
                               class="form-control form-control-sm ps-4 border-secondary-subtle"
                               placeholder="Search devices…" style="border-radius:6px;">
                    </div>

                    <div class="ms-auto">
                        @if(auth()->user()->hasPermissionTo('Create New Device'))
                        <a href="{{ route('admin.center_devices.create', ['center_id' => $center->id]) }}"
                           class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1">
                            <i class="feather-plus" style="font-size:13px;"></i>
                            <span>New Device</span>
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="content-area-body p-4">
                @include('elements.flash')

                {{-- Stats --}}
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-4">
                        <div class="card border-0 h-100" style="background:#e0f2fe;border-radius:14px;">
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
                    <div class="col-6 col-md-4">
                        <div class="card border-0 h-100" style="background:#dcfce7;border-radius:14px;">
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
                    <div class="col-6 col-md-4">
                        <div class="card border-0 h-100" style="background:#ffe4e6;border-radius:14px;">
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
                </div>

                {{-- Device Cards --}}
                @if($devices->isEmpty())
                    <div class="text-center py-5">
                        <i class="feather-monitor text-muted d-block mb-3" style="font-size:3rem;opacity:.4;"></i>
                        <h6 class="text-muted fw-semibold">No devices registered yet</h6>
                        <p class="text-muted small mb-3">Add the first device for <strong>{{ $center->name }}</strong></p>
                        <a href="{{ route('admin.center_devices.create', ['center_id' => $center->id]) }}"
                           class="btn btn-primary btn-sm">
                            <i class="feather-plus me-1"></i> New Device
                        </a>
                    </div>
                @else
                    <div class="row g-3" id="devicesGrid">
                        @foreach ($devices as $device)
                            <div class="col-12 col-sm-6 col-lg-4 col-xl-3 device-col"
                                 data-search="{{ strtolower($device->device_name.' '.$device->device_code.' '.$device->device_identifier.' '.($device->mac_address ?? '')) }}">

                                <div class="dv-card card border-0 shadow-sm h-100"
                                     style="border-radius:16px;">

                                    {{-- Top accent bar --}}
                                    <div style="height:5px;border-radius:16px 16px 0 0;
                                                background:{{ $device->status==='active' ? 'linear-gradient(90deg,#0369a1,#38bdf8)' : '#e2e8f0' }};"></div>

                                    <div class="card-body p-3">

                                        {{-- Icon + status --}}
                                        <div class="d-flex align-items-start justify-content-between mb-2">
                                            <div style="width:44px;height:44px;border-radius:12px;
                                                        background:{{ $device->status==='active' ? '#e0f2fe' : '#f1f5f9' }};
                                                        display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                                <i class="feather-monitor"
                                                   style="color:{{ $device->status==='active' ? '#0369a1' : '#94a3b8' }};font-size:20px;"></i>
                                            </div>
                                            <span class="badge {{ $device->status==='active' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}"
                                                  style="border-radius:20px;padding:4px 10px;font-size:.71rem;">
                                                {{ ucfirst($device->status) }}
                                            </span>
                                        </div>

                                        {{-- Device name --}}
                                        <h6 class="fw-bold mb-1 text-dark" style="font-size:.88rem;line-height:1.35;">
                                            {{ $device->device_name }}
                                        </h6>

                                        {{-- Device code badge --}}
                                        <span class="badge"
                                              style="background:#e0f2fe;color:#0369a1;border-radius:6px;
                                                     font-size:.7rem;font-weight:700;letter-spacing:.5px;">
                                            {{ $device->device_code }}
                                        </span>

                                        {{-- Device Identifier --}}
                                        <div class="mt-2">
                                            <small class="text-muted d-flex align-items-center gap-1" style="font-size:.74rem;">
                                                <i class="feather-cpu" style="font-size:11px;flex-shrink:0;"></i>
                                                <code style="background:#f1f5f9;padding:1px 6px;border-radius:4px;
                                                             font-size:.72rem;white-space:nowrap;overflow:hidden;
                                                             text-overflow:ellipsis;max-width:160px;display:inline-block;">
                                                    {{ $device->device_identifier }}
                                                </code>
                                            </small>
                                        </div>

                                        {{-- MAC --}}
                                        @if($device->mac_address)
                                            <div class="mt-1">
                                                <small class="text-muted d-flex align-items-center gap-1" style="font-size:.74rem;">
                                                    <i class="feather-wifi" style="font-size:11px;flex-shrink:0;"></i>
                                                    <span>{{ $device->mac_address }}</span>
                                                </small>
                                            </div>
                                        @endif

                                    </div>

                                    {{-- Edit / Delete --}}
                                    @if(auth()->user()->hasPermissionTo('Edit New Device') || auth()->user()->hasPermissionTo('Delete New Device'))
                                    <div class="card-footer bg-transparent border-0 pt-0 pb-3 px-3">
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.center_devices.edit', $device) }}"
                                               class="btn btn-sm btn-outline-warning flex-fill"
                                               style="border-radius:8px;font-size:.76rem;">
                                                <i class="feather-edit-3 me-1"></i>Edit
                                            </a>
                                            <form action="{{ route('admin.center_devices.destroy', $device) }}"
                                                  method="POST" class="flex-fill"
                                                  onsubmit="return confirm('Delete this device?')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger w-100"
                                                        style="border-radius:8px;font-size:.76rem;">
                                                    <i class="feather-trash-2 me-1"></i>Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('searchInput')?.addEventListener('input', function () {
    const q = this.value.trim().toLowerCase();
    document.querySelectorAll('.device-col').forEach(col => {
        col.style.display = (!q || col.dataset.search.includes(q)) ? '' : 'none';
    });
});
</script>

<style>
.dv-card { transition: transform .18s, box-shadow .18s; }
.dv-card:hover { transform: translateY(-4px); box-shadow: 0 10px 28px rgba(3,105,161,.13) !important; }
.content-area { overflow-y: auto !important; }
</style>
@endsection