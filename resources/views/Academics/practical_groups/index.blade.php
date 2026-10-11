@extends('admin_layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Academics.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">
            <div class="content-area-header bg-white sticky-top">
                <h5 class="fw-bold mb-0 me-4">Practical Groups</h5>
                <div class="page-header-right ms-auto">
                    @if (auth()->user()->hasPermissionTo('Create Practical Groups'))
                        <a href="{{ route('practical_groups.create') }}" class="btn btn-primary">
                            <i class="feather-plus me-2"></i>Add Practical Group
                        </a>
                    @endif
                </div>
            </div>

            <div class="content-area-body">
                {{-- Stats row --}}
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-4">
                        <div class="tt-stat-card">
                            <div class="tt-stat-icon" style="background:#eff6ff; color:#2563eb;">
                                <i class="feather-layers"></i>
                            </div>
                            <div>
                                <div class="tt-stat-val">{{ $practicalGroups->total() }}</div>
                                <div class="tt-stat-lbl">Total groups</div>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($practicalGroups->isEmpty())
                    <div class="tt-empty-state">
                        <div class="tt-empty-icon">
                            <i class="feather-folder" style="font-size: 32px; opacity: 0.3;"></i>
                        </div>
                        <h6 class="fw-semibold text-muted mb-1">No practical groups found</h6>
                        <p class="text-muted small mb-3">Create your first practical group.</p>
                        @if (auth()->user()->hasPermissionTo('Create Practical Groups'))
                            <a href="{{ route('practical_groups.create') }}" class="btn btn-primary btn-sm">
                                <i class="feather-plus me-1"></i>Create Group
                            </a>
                        @endif
                    </div>
                @else
                    <div class="tt-cards-grid">
                        @foreach ($practicalGroups as $index => $group)
                            @php
                                $colors = ['#2563eb', '#16a34a', '#d97706', '#9333ea', '#dc2626', '#0891b2'];
                                $accentColor = $colors[$index % count($colors)];
                            @endphp
                            <div class="tt-card" style="animation-delay: {{ $index * 40 }}ms">
                                <div class="tt-card-accent" style="background: {{ $accentColor }};"></div>
                                <div class="tt-card-top">
                                    <div class="tt-card-class">{{ $group->name }}</div>
                                    <span class="tt-section-pill">
                                        <i class="feather-check-circle" style="font-size:10px;"></i>
                                        {{ ucfirst($group->status) }}
                                    </span>
                                </div>
                                <div class="tt-card-meta">
                                    <div class="tt-meta-row">
                                        <i class="feather-layers"></i>
                                        Group: {{ $group->groupMaster?->name ?? '—' }}
                                    </div>
                                    <div class="tt-meta-row">
                                        <i class="feather-book"></i>
                                        Course: {{ $group->course?->name ?? '—' }}
                                    </div>
                                    <div class="tt-meta-row">
                                        <i class="feather-git-branch"></i>
                                        Branch: {{ $group->stream?->name ?? '—' }}
                                    </div>
                                    <div class="tt-meta-row">
                                        <i class="feather-calendar"></i>
                                        Academic Years: {{ $group->academicYears->pluck('name')->join(', ') }}
                                    </div>
                                    <div class="tt-meta-row">
                                        <i class="feather-users"></i>
                                        Students: {{ $group->students->count() }}
                                    </div>
                                </div>
                                <div class="tt-card-actions">
                                    @if (auth()->user()->hasPermissionTo('Edit Practical Groups'))
                                        <a href="{{ route('practical_groups.edit', $group) }}" class="tt-action edit">
                                            <i class="feather-edit-2"></i> Edit
                                        </a>
                                    @endif
                                    @if (auth()->user()->hasPermissionTo('Delete Practical Groups'))
                                        <form action="{{ route('practical_groups.destroy', $group) }}" method="POST"
                                              onsubmit="return confirm('Delete this group?')"
                                              style="display:inline-block; width:100%;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="tt-action"
                                                    style="background:#fee2e2; border-color:#fecaca; color:#b91c1c; width:100%;">
                                                <i class="feather-trash-2"></i> Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        {{ $practicalGroups->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .tt-stat-card { display:flex; align-items:center; gap:14px; background:#fff; border:0.5px solid #e5e7eb; border-radius:12px; padding:14px 16px; }
    .tt-stat-icon { width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0; }
    .tt-stat-val  { font-size:18px; font-weight:600; color:#111827; line-height:1.1; }
    .tt-stat-lbl  { font-size:12px; color:#6b7280; margin-top:2px; }

    .tt-cards-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:14px; }

    .tt-card { background:#fff; border:0.5px solid #e5e7eb; border-radius:12px; padding:1.1rem 1.2rem; position:relative; overflow:hidden; transition:border-color .15s, transform .15s; animation:ttFadeUp .3s ease both; display:flex; flex-direction:column; gap:10px; }
    .tt-card:hover { border-color:#d1d5db; transform:translateY(-2px); }
    @keyframes ttFadeUp { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }

    .tt-card-accent { position:absolute; top:0; left:0; width:3px; height:100%; border-radius:12px 0 0 12px; }
    .tt-card-top { display:flex; align-items:flex-start; justify-content:space-between; gap:8px; }
    .tt-card-class { font-size:15px; font-weight:600; color:#111827; }
    .tt-section-pill { display:inline-flex; align-items:center; gap:4px; font-size:11px; font-weight:500; background:#eff6ff; padding:3px 10px; border-radius:20px; white-space:nowrap; }
    .tt-card-meta { display:flex; flex-direction:column; gap:5px; }
    .tt-meta-row { display:flex; align-items:center; gap:7px; font-size:12px; color:#6b7280; }
    .tt-meta-row i { font-size:12px; opacity:.6; width:14px; }
    .tt-card-actions { display:flex; gap:8px; margin-top:4px; }
    .tt-action { flex:1; display:inline-flex; align-items:center; justify-content:center; gap:5px; padding:6px 8px; border-radius:7px; font-size:12px; font-weight:500; text-decoration:none; border:1px solid #e5e7eb; color:#374151; background:#fff; transition:all .12s; cursor:pointer; }
    .tt-action.edit { background:#eff6ff; border-color:#bfdbfe; color:#1d4ed8; }
    .tt-action.edit:hover { background:#dbeafe; text-decoration:none; color:#1d4ed8; }

    .tt-empty-state { text-align:center; padding:4rem 1rem; }
    .tt-empty-icon { width:72px; height:72px; border-radius:50%; background:#f3f4f6; display:flex; align-items:center; justify-content:center; margin:0 auto 16px; }
</style>
@endsection