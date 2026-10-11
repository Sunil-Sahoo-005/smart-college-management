@extends('admin_layout')

@section('content')
    <div class="nxl-content">

        {{-- Page header --}}
        <div class="page-header mb-1">
            <div class="page-header-left">
                <h5 class="m-b-4 fw-semibold">Platform control center</h5>
                <span class="fs-12 text-muted">All tenants &bull; Super admin view</span>
            </div>
            <div class="page-header-right ms-auto">
                <a href="{{ route('admin.tenants.create') }}" class="btn btn-primary btn-sm px-3">+ Add tenant</a>
            </div>
        </div>
        <div class="main-content">

        {{-- ── Stat cards ─────────────────────────────────────────────────── --}}
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 stat-card h-100">
                    <div class="card-body p-3">
                        <p class="text-muted fs-12 mb-1">Active tenants</p>
                        <h4 class="fw-semibold mb-1">{{ number_format($totalTenants) }}</h4>
                        <span class="fs-12 text-success">↑ {{ $newTenantsThisMonth }} this month</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 stat-card h-100">
                    <div class="card-body p-3">
                        <p class="text-muted fs-12 mb-1">Total students (all)</p>
                        <h4 class="fw-semibold mb-1">{{ number_format($totalStudents) }}</h4>
                        <span class="fs-12 text-muted">Across all institutions</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 stat-card h-100">
                    <div class="card-body p-3">
                        <p class="text-muted fs-12 mb-1">Platform users</p>
                        <h4 class="fw-semibold mb-1">{{ number_format($totalUsers) }}</h4>
                        <span class="fs-12 text-success">↑ {{ $newUsersThisWeek }} this week</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 stat-card h-100">
                    <div class="card-body p-3">
                        <p class="text-muted fs-12 mb-1">Fee collected ({{ now()->year }})</p>
                        <h4 class="fw-semibold mb-1">₹{{ number_format($totalFeeCollected / 10000000, 2) }}Cr</h4>
                        <span class="fs-12 text-muted">All tenants combined</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Mid panels ───────────────────────────────────────────────────── --}}
        <div class="row g-3 mb-3">

            {{-- Tenant student distribution --}}
            <div class="col-xl-4">
                <div class="card border-0 h-100">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h6 class="fw-semibold mb-0 fs-13">Tenants by student count</h6>
                    </div>
                    <div class="card-body">
                        @php $maxS = $tenantBars->max('student_count') ?: 1; @endphp
                        @foreach ($tenantBars as $t)
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="text-muted fs-11" style="width:80px;text-align:right;flex-shrink:0">
                                    {{ \Illuminate\Support\Str::limit($t['name'], 12, '') }}
                                </span>
                                <div class="flex-grow-1" style="background:#f0f0f0;border-radius:4px;height:7px">
                                    <div
                                        style="width:{{ round(($t['student_count'] / $maxS) * 100) }}%;background:{{ $t['bar_color'] }};height:7px;border-radius:4px">
                                    </div>
                                </div>
                                <span class="fs-11 fw-semibold" style="width:38px;text-align:right">
                                    {{ number_format($t['student_count']) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Module health --}}
            <div class="col-xl-4">
                <div class="card border-0 h-100">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h6 class="fw-semibold mb-0 fs-13">Module health</h6>
                    </div>
                    <div class="card-body p-0">
                        @foreach ($moduleHealth as $mod)
                            <div class="d-flex align-items-center gap-3 px-4 py-2 panel-row">
                                <span class="status-dot bg-success"></span>
                                <span class="fs-13 flex-grow-1">{{ $mod['name'] }}</span>
                                <span class="badge bg-success bg-opacity-10 text-success fs-11">Online</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Backup status --}}
            <div class="col-xl-4">
                <div class="card border-0 h-100">
                    <div
                        class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between py-3">
                        <h6 class="fw-semibold mb-0 fs-13">Latest backups</h6>
                        <a href="" class="fs-12 text-primary">View all</a>
                    </div>
                    <div class="card-body p-0">
                        {{-- @foreach ($backups as $b)
                            <div class="d-flex align-items-center gap-3 px-4 py-3 panel-row">
                                <span
                                    class="status-dot {{ $b['status'] === 'success' ? 'bg-success' : 'bg-danger' }}"></span>
                                <div class="flex-grow-1">
                                    <p class="fs-13 fw-semibold mb-0">
                                        {{ \Illuminate\Support\Str::limit($b['tenant_name'], 22) }}</p>
                                    <span class="text-muted fs-12">{{ $b['backup_type'] }} &bull;
                                        {{ $b['size_mb'] }}</span>
                                </div>
                                <span class="fs-11 text-muted">{{ $b['time'] }}</span>
                            </div>
                        @endforeach --}}
                    </div>
                </div>
            </div>
        </div>

        {{-- ── All tenants table ────────────────────────────────────────────── --}}
        <div class="card border-0 mb-3">
            <div class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between py-3">
                <h6 class="fw-semibold mb-0 fs-13">All tenants</h6>
                <div class="d-flex gap-2">
                    <input type="text" id="tenantSearch" class="form-control form-control-sm fs-12"
                        placeholder="Search tenants..." style="width:200px" oninput="filterTenants(this.value)">
                    <a href="{{ route('admin.tenants.create') }}" class="btn btn-sm btn-outline-primary fs-12">+
                        Add</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div id="tenantTableBody">
                    @foreach ($tenants as $t)
                        <div class="tenant-row d-flex align-items-center gap-3 px-4 py-3 panel-row"
                            data-name="{{ strtolower($t['name']) }}">
                            <div class="t-avatar"
                                style="background:{{ $t['status'] === 'active' ? '#E6F1FB' : '#FCEBEB' }};color:{{ $t['status'] === 'active' ? '#185FA5' : '#A32D2D' }}">
                                {{ $t['initials'] }}
                            </div>
                            <div class="flex-grow-1">
                                <p class="fw-semibold fs-13 mb-0">{{ $t['name'] }}</p>
                                <span class="text-muted fs-12">
                                    {{ $t['city'] }} &bull;
                                    {{ number_format($t['student_count']) }} students &bull;
                                    {{ $t['staff_count'] }} staff &bull;
                                    {{ $t['currency'] }}
                                </span>
                            </div>
                            <span
                                class="badge {{ $t['status'] === 'active' ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }} fs-11">
                                {{ ucfirst($t['status']) }}
                            </span>
                            <a href="{{ route('admin.tenants.show', $t['id']) }}"
                                class="btn btn-sm btn-outline-secondary fs-12 py-1 px-3">Manage</a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── Audit logs + System users ────────────────────────────────────── --}}
        <div class="row g-3">

            <div class="col-xl-6">
                <div class="card border-0 h-100">
                    <div
                        class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between py-3">
                        <h6 class="fw-semibold mb-0 fs-13">Recent audit logs</h6>
                        <a href="" class="fs-12 text-primary">View all</a>
                    </div>
                    <div class="card-body p-0">
                        {{-- @forelse ($auditLogs as $log)
                            <div class="d-flex align-items-start gap-3 px-4 py-3 panel-row">
                                <div
                                    class="audit-icon
                                {{ $log['type'] === 'add' ? 'audit-add' : ($log['type'] === 'del' ? 'audit-del' : 'audit-edit') }}">
                                    {{ $log['type'] === 'add' ? '+' : ($log['type'] === 'del' ? '×' : '~') }}
                                </div>
                                <div class="flex-grow-1">
                                    <p class="fs-13 fw-semibold mb-0">{{ ucfirst($log['action']) }} on
                                        {{ $log['entity_type'] }}</p>
                                    <span class="text-muted fs-12">
                                        {{ $log['tenant_name'] }} &bull; {{ $log['user_name'] }} &bull;
                                        {{ $log['time'] }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-4 text-muted fs-13">No audit logs found.</div>
                        @endforelse --}}
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card border-0 h-100">
                    <div
                        class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between py-3">
                        <h6 class="fw-semibold mb-0 fs-13">System users (super admin)</h6>
                        <a href="{{ route('users.create') }}" class="fs-12 text-primary">+ Add user</a>
                    </div>
                    <div class="card-body p-0">
                        @forelse ($systemUsers as $u)
                            <div class="d-flex align-items-center gap-3 px-4 py-3 panel-row">
                                <div class="initials-avatar bg-primary bg-opacity-10 text-primary">
                                    {{ $u['initials'] }}
                                </div>
                                <div class="flex-grow-1">
                                    <p class="fw-semibold fs-13 mb-0">{{ $u['name'] }}</p>
                                    <span class="text-muted fs-12">{{ $u['email'] }} &bull; {{ $u['last'] }}</span>
                                </div>
                                <span class="badge bg-primary bg-opacity-10 text-primary fs-11">Super admin</span>
                            </div>
                        @empty
                            <div class="px-4 py-4 text-muted fs-13">No system users found.</div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>{{-- end row --}}
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .stat-card {
            background: #f8f9fa;
            border-radius: 12px;
        }

        .panel-row {
            border-bottom: 1px solid #f0f0f0;
        }

        .panel-row:last-child {
            border-bottom: none;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            flex-shrink: 0;
        }

        .t-avatar {
            width: 36px;
            height: 36px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
            flex-shrink: 0;
        }

        .initials-avatar {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
            flex-shrink: 0;
        }

        .audit-icon {
            width: 28px;
            height: 28px;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 600;
            flex-shrink: 0;
        }

        .audit-add {
            background: #d1e7dd;
            color: #0a3622;
        }

        .audit-del {
            background: #f8d7da;
            color: #58151c;
        }

        .audit-edit {
            background: #cfe2ff;
            color: #084298;
        }

        .fs-11 {
            font-size: 11px !important;
        }

        .fs-12 {
            font-size: 12px !important;
        }

        .fs-13 {
            font-size: 13px !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function filterTenants(q) {
            q = q.toLowerCase();
            document.querySelectorAll('.tenant-row').forEach(row => {
                row.style.display = row.dataset.name.includes(q) ? '' : 'none';
            });
        }
    </script>
@endpush
