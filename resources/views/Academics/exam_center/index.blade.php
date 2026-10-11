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

                    <div class="flex-shrink-0">
                        <h5 class="fw-bold mb-0 text-dark">Exam Centers</h5>
                        
                    </div>

                    <div class="position-relative flex-grow-1 d-none d-md-block" style="max-width:260px;min-width:150px;">
                        <i class="feather-search position-absolute top-50 translate-middle-y text-muted"
                           style="font-size:13px;left:10px;"></i>
                        <input type="text" id="searchInput"
                               class="form-control form-control-sm ps-4 border-secondary-subtle"
                               placeholder="Search centers…" style="border-radius:6px;">
                    </div>

                    <div class="ms-auto">
                        @if(auth()->user()->hasPermissionTo('Create Exam Center'))
                            <a href="{{ route('admin.exam_center.create') }}"
                               class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1">
                                <i class="feather-plus" style="font-size:13px;"></i>
                                <span>New Center</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="content-area-body p-4">
                @include('elements.flash')

                {{-- Stats --}}
                <div class="row g-2 mb-3">
                    <div class="col-4">
                        <div class="card border-0" style="background:#ede9fe;border-radius:12px;">
                            <div class="card-body d-flex align-items-center gap-2 py-2 px-3">
                                <div style="width:34px;height:34px;border-radius:10px;background:#6d28d920;
                                            display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="feather-map-pin" style="color:#6d28d9;font-size:15px;"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size:1.25rem;line-height:1;">{{ $centers->count() }}</div>
                                    <small class="text-muted" style="font-size:.72rem;">Total</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card border-0" style="background:#dcfce7;border-radius:12px;">
                            <div class="card-body d-flex align-items-center gap-2 py-2 px-3">
                                <div style="width:34px;height:34px;border-radius:10px;background:#15803d20;
                                            display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="feather-check-circle" style="color:#15803d;font-size:15px;"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size:1.25rem;line-height:1;">{{ $centers->where('status','active')->count() }}</div>
                                    <small class="text-muted" style="font-size:.72rem;">Active</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card border-0" style="background:#ffe4e6;border-radius:12px;">
                            <div class="card-body d-flex align-items-center gap-2 py-2 px-3">
                                <div style="width:34px;height:34px;border-radius:10px;background:#be123c20;
                                            display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="feather-x-circle" style="color:#be123c;font-size:15px;"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size:1.25rem;line-height:1;">{{ $centers->where('status','inactive')->count() }}</div>
                                    <small class="text-muted" style="font-size:.72rem;">Inactive</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Center Cards --}}
                @if($centers->isEmpty())
                    <div class="text-center py-5">
                        <i class="feather-map-pin text-muted d-block mb-3" style="font-size:3rem;opacity:.4;"></i>
                        <h6 class="text-muted fw-semibold">No exam centers yet</h6>
                        <p class="text-muted small mb-3">Create your first exam center to get started</p>
                        @if(auth()->user()->hasPermissionTo('Create Exam Center'))
                            <a href="{{ route('admin.exam_center.create') }}" class="btn btn-primary btn-sm">
                                <i class="feather-plus me-1"></i> New Center
                            </a>
                        @endif
                    </div>
                @else
                    <div class="row g-2" id="centersGrid">
                        @foreach ($centers as $center)
                            <div class="col-12 col-sm-6 col-lg-4 col-xl-3 center-col"
                                 data-search="{{ strtolower($center->name.' '.$center->code.' '.$center->username) }}">

                                <div class="ec-card card border-0 shadow-sm h-100"
                                     style="border-radius:12px;cursor:pointer;"
                                     onclick="window.location='{{ route('admin.exam_center.devices', $center->id) }}'">

                                    <div class="ec-bar"
                                         style="height:4px;border-radius:12px 12px 0 0;
                                                background:{{ $center->status==='active' ? 'linear-gradient(90deg,#6d28d9,#8b5cf6)' : '#e2e8f0' }};"></div>

                                    <div class="card-body p-2">

                                        <div class="d-flex align-items-start justify-content-between mb-1">
                                            <div style="width:36px;height:36px;border-radius:10px;
                                                        background:{{ $center->status==='active' ? '#ede9fe' : '#f1f5f9' }};
                                                        display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                                <i class="feather-map-pin"
                                                   style="color:{{ $center->status==='active' ? '#6d28d9' : '#94a3b8' }};font-size:16px;"></i>
                                            </div>
                                            <span class="badge {{ $center->status==='active' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}"
                                                  style="border-radius:20px;padding:3px 8px;font-size:.68rem;">
                                                {{ ucfirst($center->status) }}
                                            </span>
                                        </div>

                                        <h6 class="fw-bold mb-1 text-dark" style="font-size:.82rem;line-height:1.3;">
                                            {{ $center->name }}
                                        </h6>

                                        <div class="d-flex align-items-center gap-1 flex-wrap mb-1">
                                            <span class="badge"
                                                  style="background:#ede9fe;color:#6d28d9;border-radius:5px;
                                                         font-size:.67rem;font-weight:700;letter-spacing:.4px;">
                                                {{ $center->code }}
                                            </span>
                                            <small class="text-muted d-flex align-items-center gap-1" style="font-size:.72rem;">
                                                <i class="feather-user" style="font-size:10px;"></i>
                                                <code style="background:#f1f5f9;padding:1px 5px;border-radius:3px;font-size:.7rem;">
                                                    {{ $center->username }}
                                                </code>
                                            </small>
                                        </div>

                                        @if($center->description)
                                            <p class="text-muted mb-1"
                                               style="font-size:.72rem;line-height:1.35;
                                                      display:-webkit-box;-webkit-line-clamp:1;
                                                      -webkit-box-orient:vertical;overflow:hidden;">
                                                {{ $center->description }}
                                            </p>
                                        @endif

                                        <div class="d-flex align-items-center gap-1 mt-1 pt-1"
                                             style="border-top:1px solid #f1f5f9;">
                                            <i class="feather-monitor text-muted" style="font-size:11px;"></i>
                                            <small class="text-muted" style="font-size:.71rem;">
                                                {{ $center->devices_count ?? 0 }} device(s)
                                            </small>
                                            <span class="ms-auto text-muted" style="font-size:.68rem;">
                                                View&nbsp;<i class="feather-chevron-right" style="font-size:10px;"></i>
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Edit / Delete --}}
                                    @if(auth()->user()->hasPermissionTo('Edit Exam Center') || auth()->user()->hasPermissionTo('Delete Exam Center'))
                                        <div class="card-footer bg-transparent border-0 pt-0 pb-2 px-2">
                                            <div class="d-flex gap-1">
                                                @if(auth()->user()->hasPermissionTo('Edit Exam Center'))
                                                    <a href="{{ route('admin.exam_center.edit', $center) }}"
                                                       class="btn btn-sm btn-outline-warning flex-fill"
                                                       style="border-radius:7px;font-size:.72rem;padding:3px 0;"
                                                       onclick="event.stopPropagation()">
                                                        <i class="feather-edit-3 me-1"></i>Edit
                                                    </a>
                                                @endif
                                                @if(auth()->user()->hasPermissionTo('Delete Exam Center'))
                                                    <form action="{{ route('admin.exam_center.destroy', $center) }}"
                                                          method="POST" class="flex-fill"
                                                          onclick="event.stopPropagation()"
                                                          onsubmit="return confirm('Delete this exam center?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit"
                                                                class="btn btn-sm btn-outline-danger w-100"
                                                                style="border-radius:7px;font-size:.72rem;padding:3px 0;">
                                                            <i class="feather-trash-2 me-1"></i>Delete
                                                        </button>
                                                    </form>
                                                @endif
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
    document.querySelectorAll('.center-col').forEach(col => {
        col.style.display = (!q || col.dataset.search.includes(q)) ? '' : 'none';
    });
});
</script>

<style>
.ec-card { transition: transform .18s, box-shadow .18s; }
.ec-card:hover { transform: translateY(-3px); box-shadow: 0 8px 22px rgba(109,40,217,.13) !important; }
.content-area { overflow-y: auto !important; }
</style>
@endsection