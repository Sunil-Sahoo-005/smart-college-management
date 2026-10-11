@extends('admin_layout')

@section('content')
    {{-- <main class="nxl-container"> --}}
    <div class="nxl-content">
        <!-- [ page-header ] start -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Staff</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Staff</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <a href="{{ route('admin.staff.create') }}" class="btn btn-primary">
                    <i class="feather-plus me-2"></i>
                    <span>Create Staff</span>
                </a>
            </div>
        </div>
        <!-- [ page-header ] end -->

        <div class="main-content">
            <div class="card stretch stretch-full">
                <div class="card-header">
                    <h6 class="card-title mb-0">Staff List</h6>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if ($staff->isEmpty())
                        <div class="text-center py-4">
                            <p class="text-muted">No staff found.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <!-- ... same header ... -->

                            <table class="table table-hover" id="staffList">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Designation</th>
                                        <th>Department</th>
                                        <th>Phone</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($staff as $s)
                                        <tr class="single-item">
                                            <td class="project-name-td">
                                                <div class="hstack gap-4">
                                                    <div>
                                                        <a href="{{ route('admin.staff.edit', $s) }}"
                                                            class="text-truncate-1-line">
                                                            {{ $s->first_name }} {{ $s->last_name }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $s->designation?->name ?? 'N/A' }}</td>
                                            <td>{{ $s->department?->name ?? 'N/A' }}</td>
                                            <td>{{ $s->phone }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $s->status == 'active' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($s->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="hstack gap-2 justify-content-end">
                                                    <a href="{{ route('admin.staff.edit', $s) }}"
                                                        class="avatar-text avatar-md">
                                                        <i class="feather-edit-3"></i>
                                                    </a>
                                                    <div class="dropdown">
                                                        <a href="javascript:void(0)" class="avatar-text avatar-md"
                                                            data-bs-toggle="dropdown" data-bs-offset="0,21">
                                                            <i class="feather-more-horizontal"></i>
                                                        </a>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('admin.staff.edit', $s) }}">
                                                                    <i class="feather-edit-3 me-3"></i>
                                                                    <span>Edit</span>
                                                                </a>
                                                            </li>
                                                            <li class="dropdown-divider"></li>
                                                            <li>
                                                                <form action="{{ route('admin.staff.destroy', $s) }}"
                                                                    method="POST" style="display:inline;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="dropdown-item"
                                                                        onclick="return confirm('Are you sure?')">
                                                                        Delete
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No staff found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>


                            {{ $staff->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{-- </main> --}}
@endsection
