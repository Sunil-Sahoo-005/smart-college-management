@extends('admin_layout')
@section('content')
<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Units</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="#" onclick="backToUnits(); return false;">Units</a></li>
                <li class="breadcrumb-item d-none" id="bc-role"><a href="#" onclick="backToRoles(); return false;" id="bc-role-text"></a></li>
                <li class="breadcrumb-item d-none" id="bc-users" id="bc-users-text"></li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                    @if (auth()->user()->hasPermissionTo('create tenant'))
                    <a href="{{ route('admin.tenants.create') }}" class="btn btn-primary">
                        <i class="feather-plus me-2"></i>
                        <span>Create Unit</span>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="main-content">
        @include('elements.flash')

        {{-- ══════════════════════════════════════════
             LEVEL 1 — UNIT CARDS
        ══════════════════════════════════════════ --}}
        <div id="view-units">
            <div class="row g-4">
                @forelse($tenants as $tenant)
                <div class="col-xxl-4 col-xl-6 col-md-6">
                    <div class="uc-card" onclick="showRoles({{ $tenant->id }})">
                        <div class="d-flex align-items-center gap-3">
                            <div class="uc-av">{{ strtoupper(substr($tenant->name, 0, 2)) }}</div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="d-flex align-items-center justify-content-between gap-2">
                                    <h6 class="uc-name mb-0 text-truncate">{{ $tenant->name }}</h6>
                                    <span class="{{ $tenant->status === 'active' ? 'uc-badge-active' : 'uc-badge-inactive' }}">
                                        {{ ucfirst($tenant->status) }}
                                    </span>
                                </div>
                                @if($tenant->email)
                                <div class="uc-meta mt-1">
                                    <i class="feather-mail"></i> {{ $tenant->email }}
                                </div>
                                @endif
                            </div>
                        </div>
                        <!-- <div class="uc-arrow"><i class="feather-chevron-right"></i></div> -->
                        <div class="uc-actions" onclick="event.stopPropagation()">
                            <a href="{{ route('admin.tenants.edit', $tenant) }}" class="uc-action-btn" title="Edit">
                                <i class="feather-edit-3"></i>
                            </a>
                            <a href="{{ route('admin.tenants.show', $tenant) }}" class="uc-action-btn" title="View">
                                <i class="feather-eye"></i>
                            </a>
                            <form action="{{ route('admin.tenants.destroy', $tenant) }}" method="POST"
                                  class="d-inline" onsubmit="return confirm('Delete this unit?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="uc-action-btn uc-action-danger" title="Delete">
                                    <i class="feather-trash-2"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="feather-inbox" style="font-size:48px;color:#dee2e6;"></i>
                            <h6 class="mt-3 text-muted">No units found</h6>
                            <a href="{{ route('admin.tenants.create') }}" class="btn btn-primary btn-sm mt-2">
                                <i class="feather-plus me-1"></i> Create First Unit
                            </a>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>
            @if($tenants->hasPages())
            <div class="mt-4">{{ $tenants->links() }}</div>
            @endif
        </div>

        {{-- ══════════════════════════════════════════
             LEVEL 2 — ROLE CARDS
        ══════════════════════════════════════════ --}}
        <div id="view-roles" style="display:none;">
            <div class="drill-header mb-4">
                <button class="drill-back-btn" onclick="backToUnits()">
                    <i class="feather-arrow-left"></i> All Units
                </button>
                <div class="drill-info">
                    <div class="uc-av sm" id="r-unit-av"></div>
                    <div>
                        <div class="drill-title" id="r-unit-name"></div>
                        <div class="drill-sub" id="r-unit-email"></div>
                    </div>
                </div>
            </div>
            <div class="row g-4" id="roles-grid"></div>
        </div>

        {{-- ══════════════════════════════════════════
             LEVEL 3 — USERS TABLE
        ══════════════════════════════════════════ --}}
        <div id="view-users" style="display:none;">
            <div class="drill-header mb-4">
                <button class="drill-back-btn" onclick="backToRoles()">
                    <i class="feather-arrow-left"></i> <span id="u-back-label">Roles</span>
                </button>
                <div class="drill-info">
                    <div class="rc-icon-wrap sm" id="u-role-icon"></div>
                    <div>
                        <div class="drill-title" id="u-role-name"></div>
                        <div class="drill-sub" id="u-unit-name"></div>
                    </div>
                </div>
            </div>
            <div id="users-grid"></div>
        </div>

        {{-- ══════════════════════════════════════════
             PHP DATA → JS
        ══════════════════════════════════════════ --}}
        @php
            $roleIcons = [
                'superadmin' => ['icon'=>'feather-star',      'bg'=>'#FFF8E6','color'=>'#BA7517','gbg'=>'linear-gradient(135deg,#FFF8E6,#FEF0C1)'],
                'admin'      => ['icon'=>'feather-shield',    'bg'=>'#E6F1FB','color'=>'#185FA5','gbg'=>'linear-gradient(135deg,#E6F1FB,#C5DCFA)'],
                'teacher'    => ['icon'=>'feather-book',      'bg'=>'#EAF3DE','color'=>'#3B6D11','gbg'=>'linear-gradient(135deg,#EAF3DE,#CEEAB0)'],
                'student'    => ['icon'=>'feather-user',      'bg'=>'#F3EEFE','color'=>'#534AB7','gbg'=>'linear-gradient(135deg,#F3EEFE,#E0D4FD)'],
                'staff'      => ['icon'=>'feather-briefcase', 'bg'=>'#FFF0F0','color'=>'#A32D2D','gbg'=>'linear-gradient(135deg,#FFF0F0,#FDD5D5)'],
            ];
            $tenantsData = [];
            foreach($tenants as $t) {
                $rolesData = [];
                foreach($roles as $r) {
                    $ri = $roleIcons[strtolower($r->name)]
                        ?? ['icon'=>'feather-users','bg'=>'#f0f4f8','color'=>'#6c757d','gbg'=>'linear-gradient(135deg,#f0f4f8,#dee2e6)'];

                    $roleUsers = $r->users()->where('tenant_id', $t->id)->get()
                        ->map(fn($u) => [
                            'name'   => trim(($u->first_name ?? '') . ' ' . ($u->last_name ?? '')),
                            'email'  => $u->email ?? '',
                            'phone'  => $u->phone ?? '',
                            'status' => $u->status ?? 'active',
                            'av'     => strtoupper(substr($u->first_name ?? 'U', 0, 1) . substr($u->last_name ?? '', 0, 1)),
                        ])->values()->toArray();

                    $rolesData[] = [
                        'name'  => $r->name,
                        'count' => count($roleUsers),
                        'icon'  => $ri['icon'],
                        'bg'    => $ri['bg'],
                        'color' => $ri['color'],
                        'gbg'   => $ri['gbg'],
                        'users' => $roleUsers,
                    ];
                }
                $tenantsData[$t->id] = [
                    'name'  => $t->name,
                    'email' => $t->email ?? '',
                    'av'    => strtoupper(substr($t->name, 0, 2)),
                    'roles' => $rolesData,
                ];
            }
        @endphp
        <script>window.__td = @json($tenantsData);</script>
    </div>
</div>

<style>
/* ── Unit Card ── */
.uc-card {
    position:relative; background:#fff; border:0.5px solid #e4eaf3;
    border-radius:14px; padding:18px 18px 58px;
    cursor:pointer; transition:box-shadow .2s,border-color .2s,transform .15s; overflow:hidden;
}
.uc-card:hover { box-shadow:0 8px 28px rgba(24,95,165,.12); border-color:#99c4f0; transform:translateY(-2px); }
.uc-card:active { transform:translateY(0); }

.uc-av { width:46px;height:46px;border-radius:10px;background:#E6F1FB;color:#185FA5;font-size:16px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.uc-av.sm { width:36px;height:36px;font-size:12px;border-radius:8px; }
.uc-name { font-size:14px;font-weight:600;color:#1a2332; }
.uc-meta { font-size:11px;color:#8a96a6;display:flex;align-items:center;gap:4px; }
.uc-meta i { font-size:11px;color:#185FA5; }
.uc-badge-active { font-size:10px;font-weight:500;padding:3px 10px;border-radius:20px;background:#EAF3DE;color:#3B6D11;white-space:nowrap; }
.uc-badge-inactive { font-size:10px;font-weight:500;padding:3px 10px;border-radius:20px;background:#FCEBEB;color:#A32D2D;white-space:nowrap; }

.uc-arrow { position:absolute;top:18px;right:16px;width:28px;height:28px;background:#E6F1FB;color:#185FA5;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;transition:background .2s,transform .2s; }
.uc-card:hover .uc-arrow { background:#185FA5;color:#fff;transform:translateX(2px); }

.uc-actions { position:absolute;bottom:0;left:0;right:0;background:#f8fbff;border-top:0.5px solid #e4eaf3;padding:10px 16px;display:flex;gap:6px; }
.uc-action-btn { width:30px;height:30px;border-radius:7px;background:#fff;border:0.5px solid #dee2e6;display:inline-flex;align-items:center;justify-content:center;font-size:13px;color:#6c757d;cursor:pointer;text-decoration:none;transition:all .15s; }
.uc-action-btn:hover { background:#E6F1FB;color:#185FA5;border-color:#b5d4f4; }
.uc-action-danger:hover { background:#FCEBEB!important;color:#A32D2D!important;border-color:#f7c1c1!important; }

/* ── Drill Header ── */
.drill-header { display:flex;align-items:center;gap:14px;flex-wrap:wrap; }
.drill-back-btn { display:inline-flex;align-items:center;gap:7px;padding:8px 18px;border-radius:8px;background:#fff;border:0.5px solid #dee2e6;font-size:13px;font-weight:500;color:#495057;cursor:pointer;transition:all .15s; }
.drill-back-btn:hover { background:#E6F1FB;color:#185FA5;border-color:#b5d4f4; }
.drill-info { display:flex;align-items:center;gap:10px; }
.drill-title { font-size:15px;font-weight:600;color:#1a2332; }
.drill-sub { font-size:12px;color:#8a96a6; }

/* ── Role Card ── */
.rc-card { background:#fff;border:0.5px solid #e4eaf3;border-radius:14px;padding:22px 20px;display:block;cursor:pointer;transition:box-shadow .2s,border-color .2s,transform .15s;height:100%; }
.rc-card:hover { box-shadow:0 8px 28px rgba(24,95,165,.12);border-color:#99c4f0;transform:translateY(-2px); }
.rc-icon-wrap { width:54px;height:54px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:22px;margin-bottom:14px; }
.rc-icon-wrap.sm { width:36px;height:36px;border-radius:9px;font-size:16px;margin-bottom:0; }
.rc-role-name { font-size:15px;font-weight:600;color:#1a2332;margin-bottom:4px; }
.rc-user-count { font-size:28px;font-weight:700;color:#185FA5;line-height:1;margin-bottom:2px; }
.rc-user-lbl { font-size:11px;color:#adb5bd;text-transform:uppercase;letter-spacing:.05em; }
.rc-arrow { float:right;width:30px;height:30px;border-radius:50%;background:#E6F1FB;color:#185FA5;display:flex;align-items:center;justify-content:center;font-size:13px;margin-top:-4px;transition:background .15s,transform .15s; }
.rc-card:hover .rc-arrow { background:#185FA5;color:#fff;transform:translateX(3px); }

/* ── Users Table ── */
.user-table-wrap { background:#fff;border:0.5px solid #e4eaf3;border-radius:14px;overflow:hidden; }
.user-table { width:100%;border-collapse:collapse; }
.user-table thead tr { background:#f8fbff;border-bottom:0.5px solid #e4eaf3; }
.user-table thead th { padding:12px 16px;font-size:11px;font-weight:600;color:#adb5bd;text-transform:uppercase;letter-spacing:.06em; }
.user-table tbody tr { border-bottom:0.5px solid #f0f4f8;transition:background .12s; }
.user-table tbody tr:last-child { border-bottom:none; }
.user-table tbody tr:hover { background:#f8fbff; }
.user-table tbody td { padding:12px 16px;font-size:13px;color:#495057;vertical-align:middle; }
.user-av { width:34px;height:34px;border-radius:8px;background:#E6F1FB;color:#185FA5;font-size:12px;font-weight:700;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0; }
.user-name { font-weight:600;color:#1a2332; }
.user-email { font-size:11px;color:#8a96a6;margin-top:1px; }
.u-badge-active { font-size:10px;font-weight:500;padding:3px 10px;border-radius:20px;background:#EAF3DE;color:#3B6D11; }
.u-badge-inactive { font-size:10px;font-weight:500;padding:3px 10px;border-radius:20px;background:#FCEBEB;color:#A32D2D; }
.empty-state { text-align:center;padding:52px 16px;color:#adb5bd; }
.empty-state i { font-size:42px;display:block;margin-bottom:10px; }

/* ── Animations ── */
#view-units,#view-roles,#view-users { animation:fadeIn .22s ease; }
@keyframes fadeIn { from{opacity:0;transform:translateX(12px)} to{opacity:1;transform:translateX(0)} }
</style>

<script>
var __tid = null; // current tenant id
var __ridx = null; // current role index

/* ─── Level 2: Show Roles ─── */
function showRoles(tenantId) {
    var t = window.__td[tenantId];
    if (!t) return;
    __tid = tenantId;

    document.getElementById('r-unit-av').textContent    = t.av;
    document.getElementById('r-unit-name').textContent  = t.name;
    document.getElementById('r-unit-email').textContent = t.email;

    var html = '';
    if (!t.roles || t.roles.length === 0) {
        html = '<div class="col-12"><div class="empty-state"><i class="feather-shield-off"></i>No roles found</div></div>';
    } else {
        t.roles.forEach(function(r, idx) {
            html +=
                '<div class="col-xxl-3 col-xl-4 col-md-6">' +
                  '<div class="rc-card" onclick="showUsers(' + tenantId + ',' + idx + ')">' +
                    '<div class="d-flex justify-content-between align-items-start">' +
                      '<div class="rc-icon-wrap" style="background:' + r.gbg + ';color:' + r.color + ';">' +
                        '<i class="' + r.icon + '"></i>' +
                      '</div>' +
                      '<div class="rc-arrow"><i class="feather-chevron-right"></i></div>' +
                    '</div>' +
                    '<div class="rc-role-name">' + cap(r.name) + '</div>' +
                    '<div class="rc-user-count">' + r.count + '</div>' +
                    '<div class="rc-user-lbl">Users</div>' +
                  '</div>' +
                '</div>';
        });
    }
    document.getElementById('roles-grid').innerHTML = html;
    switchView('view-roles');
}

/* ─── Level 3: Show Users ─── */
function showUsers(tenantId, roleIdx) {
    var t = window.__td[tenantId];
    var r = t.roles[roleIdx];
    __tid  = tenantId;
    __ridx = roleIdx;

    document.getElementById('u-back-label').textContent = t.name + ' — Roles';
    document.getElementById('u-role-name').textContent  = cap(r.name);
    document.getElementById('u-unit-name').textContent  = t.name;

    var iconEl = document.getElementById('u-role-icon');
    iconEl.style.background = r.gbg;
    iconEl.style.color      = r.color;
    iconEl.innerHTML = '<i class="' + r.icon + '"></i>';

    var html = '';
    if (!r.users || r.users.length === 0) {
        html = '<div class="user-table-wrap"><div class="empty-state"><i class="feather-users"></i>No users in this role</div></div>';
    } else {
        html = '<div class="user-table-wrap"><table class="user-table">' +
            '<thead><tr>' +
              '<th>#</th><th>User</th><th>Phone</th><th>Status</th>' +
            '</tr></thead><tbody>';
        r.users.forEach(function(u, i) {
            html += '<tr>' +
                '<td style="color:#adb5bd;font-size:12px;">' + (i+1) + '</td>' +
                '<td><div class="d-flex align-items-center gap-2">' +
                    '<div class="user-av">' + u.av + '</div>' +
                    '<div><div class="user-name">' + u.name + '</div>' +
                    '<div class="user-email">' + u.email + '</div></div>' +
                '</div></td>' +
                '<td>' + (u.phone || '—') + '</td>' +
                '<td><span class="' + (u.status === 'active' ? 'u-badge-active' : 'u-badge-inactive') + '">' + cap(u.status) + '</span></td>' +
            '</tr>';
        });
        html += '</tbody></table></div>';
    }
    document.getElementById('users-grid').innerHTML = html;
    switchView('view-users');
}

/* ─── Navigation ─── */
function backToUnits() { switchView('view-units'); }
function backToRoles()  { showRoles(__tid); }

function switchView(id) {
    ['view-units','view-roles','view-users'].forEach(function(v) {
        var el = document.getElementById(v);
        el.style.display = (v === id) ? 'block' : 'none';
        if (v === id) {
            el.style.animation = 'none';
            requestAnimationFrame(function() { el.style.animation = ''; });
        }
    });
}

function cap(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }
</script>
@endsection