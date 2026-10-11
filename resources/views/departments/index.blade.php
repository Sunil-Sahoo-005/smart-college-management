@extends('admin_layout')

@section('content')
    <div class="nxl-content">
        <!-- [ page-header ] start -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Departments</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Departments</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">
                    <i class="feather-plus me-2"></i>
                    <span>Create Department</span>
                </a>
            </div>
        </div>
        <!-- [ page-header ] end -->

        <div class="main-content">
            <div class="card stretch stretch-full">
                <div class="card-header">
                    <h6 class="card-title mb-0">Departments List</h6>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if ($departments->isEmpty())
                        <div class="text-center py-4">
                            <p class="text-muted">No departments found.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($departments as $department)
                                        <tr>
                                            <td>{{ $department->name }}</td>
                                            <td>{{ Str::limit($department->description, 80) ?? 'N/A' }}</td>
                                            <td class="text-end">
                                                <div class="hstack gap-2 justify-content-end">
                                                    <a href="{{ route('admin.departments.edit', $department) }}"
                                                        class="avatar-text avatar-md">
                                                        <i class="feather-edit-3"></i>
                                                    </a>
                                                    <form action="{{ route('admin.departments.destroy', $department) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="avatar-text avatar-md text-danger"
                                                            onclick="return confirm('Are you sure?')">
                                                            <i class="feather-trash-2"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            {{ $departments->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
