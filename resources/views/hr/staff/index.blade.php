@extends('hr.layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('hr.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top px-4 py-3 border-bottom shadow-sm mb-2">
                    <div class="d-flex align-items-center gap-3 flex-wrap w-100">
                        <div class="flex-shrink-0">
                            <h5 class="fw-bold mb-0 text-dark">Staff</h5>
                            <small class="text-muted">Showing {{ $staff->count() }} of {{ $staff->total() }} staff</small>
                        </div>
                         
                        <div class="ms-auto d-flex align-items-center gap-2 flex-shrink-0">
                            @if (auth()->user()->hasPermissionTo('Create HrStaff'))
                            <a href="{{ route('hr.staff.create', $tenantKey) }}"
                                class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1">
                                <i class="feather-plus" style="font-size:13px;"></i>
                                <span>Add Staff</span>
                            </a>
                           @endif
                        </div>                     
                    </div>
                </div>

                <div class="content-area-body px-4 py-4">
                    @include('elements.flash')
                    <div class="card stretch stretch-full">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Name</th>
                                            <th>Designation</th>
                                            <th>Department</th>
                                            <th>Phone</th>
                                            <th>Status</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                   <tbody>
                                        @forelse($staff as $item)
                                            <tr>
                                                <td>{{ $item->first_name }} {{ $item->last_name }}</td>
                                                <td>{{ $item->designation?->name ?? 'N/A' }}</td>
                                                <td>{{ $item->department?->name ?? 'N/A' }}</td>
                                                <td>{{ $item->phone }}</td>
                                                <td><span
                                                        class="badge bg-{{ $item->status === 'active' ? 'success' : 'warning' }}">{{ ucfirst($item->status) }}</span>
                                                </td>
                                                <td>
                                                    <div class="hstack gap-2 justify-content-center">
                                                        @if (auth()->user()->hasPermissionTo('View HrStaff'))
                                                        <a href="{{ route('hr.staff.show', [$tenantKey, $item]) }}"
                                                            class="avatar-text avatar-md text-primary" title="View">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        @endif
                                                        
                                                        @if (auth()->user()->hasPermissionTo('Edit HrStaff'))
                                                        <a href="{{ route('hr.staff.edit', [$tenantKey, $item]) }}"
                                                            class="avatar-text avatar-md text-warning" title="Edit">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </a>
                                                        @endif

                                                        @if (auth()->user()->hasPermissionTo('Delete HrStaff'))
                                                        <form action="{{ route('hr.staff.destroy', [$tenantKey, $item]) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('Delete this staff?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="avatar-text avatar-md text-danger" type="submit"
                                                                title="Delete">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-5 text-muted">No staff found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="p-3">
                                {{ $staff->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
