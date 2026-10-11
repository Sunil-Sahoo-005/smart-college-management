@extends('admin_layout')

@section('content')
<div class="nxl-content">

    <!-- [ Page Header ] start -->
    <div class="page-header px-4">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">{{ $tenant->name }}</h5>
            </div>
            <ul class="breadcrumb fs-12 text-muted mb-0 ms-3">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.tenants.index') }}">Tenants</a></li>
                <li class="breadcrumb-item">Control Center</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="d-flex align-items-center gap-2">
                <span class="badge {{ $tenant->status === 'active' ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }} px-3 py-2 rounded-pill fw-bold text-uppercase border-0">
                    <i class="feather-{{ $tenant->status === 'active' ? 'check-circle' : 'x-circle' }} me-1"></i> {{ $tenant->status }}
                </span>
                <a href="{{ route('admin.tenants.edit', $tenant->id) }}" class="btn btn-outline-primary btn-sm px-3">
                    <i class="feather-edit me-1"></i>Edit Profile
                </a>
            </div>
        </div>
    </div>

    <div class="main-content px-4">
        
        <!-- Statistics Widgets -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-left: 4px solid #3474ff !important;">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-md bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                                <i class="feather-users fs-4"></i>
                            </div>
                            <div>
                                <p class="fs-12 text-muted text-uppercase mb-1 fw-bold">Students</p>
                                <h4 class="mb-0 fw-bold">{{ number_format($stats['students_count']) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-left: 4px solid #1ccab8 !important;">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-md bg-soft-success text-success rounded-circle d-flex align-items-center justify-content-center me-3">
                                <i class="feather-briefcase fs-4"></i>
                            </div>
                            <div>
                                <p class="fs-12 text-success text-uppercase mb-1 fw-bold">Staff Members</p>
                                <h4 class="mb-0 fw-bold">{{ number_format($stats['staff_count']) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-left: 4px solid #ff5c75 !important;">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-md bg-soft-danger text-danger rounded-circle d-flex align-items-center justify-content-center me-3">
                                <i class="feather-user-check fs-4"></i>
                            </div>
                            <div>
                                <p class="fs-12 text-danger text-uppercase mb-1 fw-bold">Administrators</p>
                                <h4 class="mb-0 fw-bold">{{ $admins->count() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-left: 4px solid #ffb822 !important;">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-md bg-soft-warning text-warning rounded-circle d-flex align-items-center justify-content-center me-3">
                                <i class="feather-calendar fs-4"></i>
                            </div>
                            <div>
                                <p class="fs-12 text-warning text-uppercase mb-1 fw-bold">Academic Years</p>
                                <h4 class="mb-0 fw-bold">{{ $stats['years_count'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Administrators List -->
            <div class="col-xl-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold mb-0">Tenant Administrators</h6>
                        <a href="{{ route('users.create', ['tenant_id' => $tenant->id]) }}" class="btn btn-sm btn-primary">
                            <i class="feather-plus me-1"></i>Add Admin
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light-soft text-muted border-bottom">
                                    <tr class="fs-11 text-uppercase">
                                        <th class="ps-4">Admin Name</th>
                                        <th>Contact</th>
                                        <th>Status</th>
                                        <th class="text-end pe-4">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($admins as $admin)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold me-3">
                                                        {{ strtoupper(substr($admin->first_name,0,1)) }}{{ strtoupper(substr($admin->last_name,0,1)) }}
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 fs-14 fw-semibold text-dark">{{ $admin->first_name }} {{ $admin->last_name }}</h6>
                                                        <span class="fs-12 text-muted">ID: #{{ $admin->id }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fs-13 text-dark mb-1">{{ $admin->email }}</div>
                                                <div class="fs-11 text-muted">{{ $admin->phone ?: 'No phone' }}</div>
                                            </td>
                                            <td>
                                                <span class="badge {{ $admin->status === 'active' ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }} fs-10">
                                                    {{ ucfirst($admin->status) }}
                                                </span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-light-soft px-2" type="button" data-bs-toggle="dropdown">
                                                        <i class="feather-more-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                                        <li><a class="dropdown-item fs-13" href="{{ route('users.edit', $admin->id) }}"><i class="feather-edit me-2"></i>Edit Admin</a></li>
                                                        <li><a class="dropdown-item fs-13 text-danger" href="#"><i class="feather-lock me-2"></i>Reset Password</a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5">
                                                <div class="mb-3">
                                                    <i class="feather-user-x fs-1 text-muted-50"></i>
                                                </div>
                                                <p class="text-muted mb-0">No administrators found for this tenant.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tenant Profile Details -->
            <div class="col-xl-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h6 class="fw-bold mb-0">Institution Profile</h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            @if($tenant->logo_url)
                                <img src="{{ $tenant->logo_url }}" alt="Logo" class="img-fluid rounded mb-3" style="max-height: 80px;">
                            @else
                                <div class="bg-light-soft rounded mx-auto d-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="feather-image fs-1 text-muted-50"></i>
                                </div>
                            @endif
                            <h5 class="fw-bold mb-0">{{ $tenant->name }}</h5>
                            <p class="text-muted fs-12 mb-0">Registered Since {{ $tenant->created_at->format('M Y') }}</p>
                        </div>

                        <div class="list-group list-group-flush border-0">
                            <div class="list-group-item px-0 py-3 border-0 border-bottom">
                                <p class="text-muted fs-11 text-uppercase fw-bold mb-1">Contact Details</p>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="feather-mail text-primary me-2 fs-13"></i>
                                    <span class="fs-13 text-dark">{{ $tenant->email }}</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="feather-phone text-primary me-2 fs-13"></i>
                                    <span class="fs-13 text-dark">{{ $tenant->phone }}</span>
                                </div>
                            </div>
                            <div class="list-group-item px-0 py-3 border-0 border-bottom">
                                <p class="text-muted fs-11 text-uppercase fw-bold mb-1">Address</p>
                                <div class="d-flex align-items-start text-dark fs-13">
                                    <i class="feather-map-pin text-primary mt-1 me-2" style="min-width: 13px"></i>
                                    <span>{{ $tenant->address ?: 'No address specified' }}</span>
                                </div>
                            </div>
                            <div class="list-group-item px-0 py-3 border-0">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <p class="text-muted fs-11 text-uppercase fw-bold mb-1">Currency</p>
                                        <span class="badge bg-light text-dark fs-12 px-2 py-1">{{ $tenant->currency }}</span>
                                    </div>
                                    <div class="col-6">
                                        <p class="text-muted fs-11 text-uppercase fw-bold mb-1">Session Starts</p>
                                        <span class="badge bg-light text-dark fs-12 px-2 py-1">{{ \Carbon\Carbon::create(2026, $tenant->academic_year_start_month, 1)->format('F') }}</span>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <p class="text-muted fs-11 text-uppercase fw-bold mb-1">Timezone</p>
                                    <span class="fs-12 text-dark"><i class="feather-clock me-1 text-muted"></i>{{ $tenant->timezone }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #123458 0%, #1f5f8b 100%);">
                    <div class="card-body p-4 text-white">
                        <h6 class="text-white opacity-75 fw-bold text-uppercase fs-11 mb-3">System Access</h6>
                        <div class="d-grid gap-2">
                            <a href="#" class="btn btn-primary d-flex align-items-center justify-content-center">
                                <i class="feather-monitor me-2"></i>Login as Admin
                            </a>
                            <p class="fs-11 text-white opacity-50 text-center mb-0 mt-2">Impersonation allows you to troubleshooting settings on behalf of this institution.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-light-soft {
        background-color: #f7f9fc !important;
    }
    .bg-soft-primary {
        background-color: rgba(52, 116, 255, 0.08) !important;
    }
    .bg-soft-success {
        background-color: rgba(28, 202, 184, 0.08) !important;
    }
    .bg-soft-danger {
        background-color: rgba(255, 92, 117, 0.08) !important;
    }
    .bg-soft-warning {
        background-color: rgba(255, 184, 34, 0.08) !important;
    }
    .text-muted-50 {
        opacity: 0.5;
    }
    .avatar-md {
        width: 48px;
        height: 48px;
    }
    .avatar-sm {
        width: 38px;
        height: 38px;
    }
    .btn-light-soft {
        background-color: #f5f8fc;
        border: none;
        color: #7d8ea2;
    }
    .btn-light-soft:hover {
        background-color: #eff3f9;
        color: #3474ff;
    }
</style>
@endsection
