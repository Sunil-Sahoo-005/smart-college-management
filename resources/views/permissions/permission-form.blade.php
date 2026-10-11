@extends('admin_layout')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">{{ !empty($permission->id) ? 'Edit Permission' : 'Create Permission' }}</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.permissions.index') }}">Permissions</a></li>
                <li class="breadcrumb-item">{{ !empty($permission->id) ? 'Edit Permission' : 'Create Permission' }}</li>
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
                    <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">
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
        @include('elements.flash')
        <div class="row">
            <div class="col-lg-12">
                <form action="{{ route('admin.permissions.store') }}" method="POST">
                    @csrf
                    @if (!empty($permission->id))
                        <input type="hidden" name="id" value="{{ $permission->id }}">
                    @endif

                    <div class="card stretch stretch-full">
                        <div class="card-body general-info">
                            <div class="mb-5 d-flex align-items-center justify-content-between">
                                <h5 class="fw-bold mb-0 me-4">
                                    <span class="d-block mb-2">Permission Info :</span>
                                    <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                        Define a permission name and module
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
                                        <input type="text"
                                               class="form-control @error('name') is-invalid @enderror"
                                               id="name"
                                               name="name"
                                               value="{{ old('name', $permission->name ?? '') }}"
                                               placeholder="Permission Name"
                                               required autocomplete="off">
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
                                               id="guard_name"
                                               name="guard_name"
                                               value="{{ old('guard_name', $permission->guard_name ?? 'web') }}"
                                               placeholder="Guard (web, api)"
                                               required autocomplete="off">
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
                                        <input type="text"
                                               class="form-control @error('module') is-invalid @enderror"
                                               id="module"
                                               name="module"
                                               value="{{ old('module', $permission->module ?? '') }}"
                                               placeholder="Module (students, fees, attendance, etc.)" autocomplete="off">
                                    </div>
                                    @error('module')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
@endsection
