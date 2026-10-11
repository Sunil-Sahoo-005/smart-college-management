@extends('admin_layout')
@section('content')
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Roles</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Roles</li>
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

                        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                            <i class="feather-plus me-2"></i>
                            <span>Create Role</span>
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
        <div class="main-content">
            <div class="row">
                <div class="col-lg-12">
                    @include('elements.flash')
                    <div class="card stretch stretch-full">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover" id="roleList">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Guard</th>
                                            <th>Module</th>
                                            <th>Permissions</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($roles as $role)
                                            <tr class="single-item">
                                                <td class="project-name-td">
                                                    <div class="hstack gap-4">
                                                        <div>
                                                            <a href="{{ route('admin.roles.show', $role) }}"
                                                                class="text-truncate-1-line">
                                                                {{ $role->name }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $role->guard_name }}</td>
                                                <td>{{ $role->module ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="badge bg-primary">{{ $role->permissions->count() }}
                                                        permissions</span>
                                                </td>
                                                <td>
                                                    <div class="hstack gap-2 justify-content-center">

                                                        <a href="{{ route('admin.roles.show', $role) }}"
                                                            class="avatar-text avatar-md text-primary" title="View">
                                                            <i class="feather-eye"></i>
                                                        </a>

                                                        <a href="{{ route('admin.roles.edit', $role) }}"
                                                            class="avatar-text avatar-md text-warning" title="Edit">
                                                            <i class="feather-edit-3"></i>
                                                        </a>

                                                        <form action="{{ route('admin.roles.destroy', $role) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('Delete this role?')">
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
                                            <tr>
                                                <td colspan="5" class="text-center">No roles found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
