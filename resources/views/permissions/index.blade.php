@extends('admin_layout')
@section('content')
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Permissions</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Permissions</li>
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
                        <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">
                            <i class="feather-plus me-2"></i>
                            <span>Create Permission</span>
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
                                <table class="table table-hover" id="permissionList">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Guard</th>
                                            <th>Module</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($permissions as $permission)
                                            <tr class="single-item">
                                                <td class="project-name-td">
                                                    <div class="hstack gap-4">
                                                        <div>
                                                            <a href="{{ route('admin.permissions.show', $permission) }}"
                                                                class="text-truncate-1-line">
                                                                {{ $permission->name }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $permission->guard_name }}</td>
                                                <td>{{ $permission->module ?? 'N/A' }}</td>
                                                <td>
                                                    <div class="hstack gap-2 justify-content-center">
                                                        <a href="{{ route('admin.permissions.show', $permission) }}"
                                                            class="avatar-text avatar-md text-primary"
                                                            title="View Permission">
                                                            <i class="feather-eye"></i>
                                                        </a>

                                                        <a href="{{ route('admin.permissions.edit', $permission) }}"
                                                            class="avatar-text avatar-md text-warning"
                                                            title="Edit Permission">
                                                            <i class="feather-edit-3"></i>
                                                        </a>

                                                        <form action="{{ route('admin.permissions.destroy', $permission) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('Delete this permission?')">
                                                            @csrf @method('DELETE')
                                                            <button type="submit"
                                                                class="avatar-text avatar-md border-0 bg-transparent text-danger"
                                                                title="Delete Permission">
                                                                <i class="feather-trash-2"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>

                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">No permissions found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    {{ $permissions->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
