@extends('admin_layout')

@section('content')
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Users</h5>
                </div>
                <ul class="breadcrumb">
                    {{-- <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li> --}}
                    <li class="breadcrumb-item">Users</li>
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
                        @if (auth()->user()->hasPermissionTo('create user'))
                            <a href="{{ route('users.create') }}" class="btn btn-primary">
                                <i class="feather-plus me-2"></i>
                                <span>Create User</span>
                            </a>
                        @endif
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
                                <table class="table table-hover" id="userList">
                                    <thead>
                                        <tr>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Tenant</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($users as $user)
                                            <tr class="single-item">
                                                <td class="project-name-td">
                                                    <div class="hstack gap-4">
                                                        <div>
                                                            <a href="{{ route('users.show', $user) }}"
                                                                class="text-truncate-1-line">
                                                                {{ $user->first_name }} {{ $user->last_name }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $user->email }}</td>
                                                <td>{{ $user->tenant ? $user->tenant->name : '-' }}</td>
                                                <td>{{ ucfirst($user->role) }}</td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $user->status == 'active' ? 'success' : 'danger' }}">
                                                        {{ ucfirst($user->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="hstack gap-2 justify-content-center">
                                                        @if (auth()->user()->hasPermissionTo('view user'))
                                                            <a href="{{ route('users.show', $user) }}"
                                                                class="avatar-text avatar-md text-primary" title="View">
                                                                <i class="feather-eye"></i>
                                                            </a>
                                                        @endif
                                                        @if (auth()->user()->hasPermissionTo('edit user'))
                                                            <a href="{{ route('users.edit', $user) }}"
                                                                class="avatar-text avatar-md text-warning" title="Edit">
                                                                <i class="feather-edit-3"></i>
                                                            </a>
                                                        @endif
                                                        @if (auth()->user()->hasPermissionTo('delete user'))
                                                            <form action="{{ route('users.destroy', $user) }}"
                                                                method="POST" class="d-inline"
                                                                onsubmit="return confirm('Delete this user?')">
                                                                @csrf @method('DELETE')
                                                                <button type="submit"
                                                                    class="avatar-text avatar-md border-0 bg-transparent text-danger"
                                                                    title="Delete">
                                                                    <i class="feather-trash-2"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No users found.</td>
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
