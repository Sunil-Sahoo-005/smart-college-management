@extends('admin_layout')

@section('content')
    <div class="nxl-content">
        <!-- [ page-header ] start -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">{{ !empty($role->id) ? 'Edit Role' : 'Create Role' }}</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
                    <li class="breadcrumb-item">{{ !empty($role->id) ? 'Edit Role' : 'Create Role' }}</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <div class="page-header-right-items">
                    <div class="d-flex d-md-none">
                        <a href="javascript:void(0)" class="page-header-right-close-toggle">
                            <i class="feather-arrow-left me-2"></i>
                            <span>Back</span>
                        </a>
                    </div>
                    <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                            <i class="feather-arrow-left me-2"></i>
                            <span>Back to List</span>
                        </a>
                    </div>
                </div>
                <div class="d-md-none d-flex align-items-center">
                    <a href="javascript:void(0)" class="page-header-right-open-toggle">
                        <i class="feather-align-right fs-20"></i>
                    </a>
                </div>
            </div>
        </div>
        <!-- [ page-header ] end -->

        <!-- [ Main Content ] start -->
        <div class="main-content">
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('admin.roles.store') }}" method="POST">
                        @csrf
                        @if (!empty($role->id))
                            <input type="hidden" name="id" value="{{ $role->id }}">
                        @endif

                        <div class="card stretch stretch-full">
                            <div class="card-body general-info">
                                <div class="mb-5 d-flex align-items-center justify-content-between">
                                    <h5 class="fw-bold mb-0 me-4">
                                        <span class="d-block mb-2">Role Info :</span>
                                        <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                            Role and permissions
                                        </span>
                                    </h5>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="name" class="fw-semibold">Name:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-tag"></i></div>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name" value="{{ old('name', $role->name ?? '') }}"
                                                placeholder="Role Name" required autocomplete="off">
                                        </div>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="guard_name" class="fw-semibold">Guard:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-shield"></i></div>
                                            <input type="text"
                                                class="form-control @error('guard_name') is-invalid @enderror"
                                                id="guard_name" name="guard_name"
                                                value="{{ old('guard_name', $role->guard_name ?? 'web') }}"
                                                placeholder="Guard (web, api)" required autocomplete="off">
                                        </div>
                                        @error('guard_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="module" class="fw-semibold">Module:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-layers"></i></div>
                                            <input type="text" class="form-control @error('module') is-invalid @enderror"
                                                id="module" name="module"
                                                value="{{ old('module', $role->module ?? '') }}"
                                                placeholder="Module (students, fees, attendance, etc.)" autocomplete="off">
                                        </div>
                                        @error('module')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label class="fw-semibold">Permissions:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="row">
                                        @foreach ($permissions->groupBy('module') as $module => $modulePermissions)
                                            <div class="col-lg-12 mb-3">
                                                <h6>{{ $module ?? 'Other' }}</h6>
                                                <div class="row">
                                                    @foreach ($modulePermissions as $permission)
                                                        <div class="col-lg-4">
                                                            <div class="form-check">
                                                                <input class="form-check-input"
                                                                       type="checkbox"
                                                                       name="permissions[]"
                                                                       value="{{ $permission->id }}"
                                                                       id="permission_{{ $permission->id }}"
                                                                       {{ in_array($permission->id, old('permissions', $role->permissions->pluck('id')->toArray())) ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                       for="permission_{{ $permission->id }}">
                                                                    {{ $permission->name }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div> --}}
                                <div class="row mb-4 align-items-start">
                                    <div class="col-lg-4">
                                        <label class="fw-semibold">Permissions:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <!-- Global Select All / Deselect -->
                                        <div class="mb-3 d-flex flex-wrap gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                onclick="selectAllPermissions(this)">
                                                Select All
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                                onclick="deselectAllPermissions(this)">
                                                Deselect All
                                            </button>
                                        </div>

                                        <div class="row g-3">
                                            @foreach ($permissions->groupBy('module') as $module => $modulePermissions)
                                                <div class="col-lg-12">
                                                    <div class="card border-1 shadow-sm">
                                                        <div
                                                            class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                                                            <h6 class="mb-0">{{ $module ?? 'Other' }}</h6>
                                                            <div class="btn-group btn-group-sm">
                                                                <button type="button" class="btn btn-outline-primary"
                                                                    onclick="selectAllInModule(this, '{{ $module }}')">
                                                                    Select All
                                                                </button>
                                                                <button type="button" class="btn btn-outline-secondary"
                                                                    onclick="deselectAllInModule(this, '{{ $module }}')">
                                                                    Deselect
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="card-body p-3">
                                                            <div class="row g-2">
                                                                @foreach ($modulePermissions as $permission)
                                                                    <div class="col-lg-4 col-md-6">
                                                                        <div class="form-check">
                                                                            <input
                                                                                class="form-check-input permission-checkbox"
                                                                                type="checkbox" name="permissions[]"
                                                                                value="{{ $permission->id }}"
                                                                                id="permission_{{ $permission->id }}"
                                                                                data-module="{{ $module }}"
                                                                                {{ in_array($permission->id, old('permissions', $role->permissions->pluck('id')->toArray())) ? 'checked' : '' }}>
                                                                            <label class="form-check-label"
                                                                                for="permission_{{ $permission->id }}">
                                                                                {{ $permission->name }}
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
    <script>
        function selectAllPermissions(btn) {
            document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = true);
        }

        function deselectAllPermissions(btn) {
            document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);
        }

        function selectAllInModule(btn, module) {
            document.querySelectorAll(`.permission-checkbox[data-module="${module}"]`).forEach(cb => cb.checked = true);
        }

        function deselectAllInModule(btn, module) {
            document.querySelectorAll(`.permission-checkbox[data-module="${module}"]`).forEach(cb => cb.checked = false);
        }
    </script>
@endsection
