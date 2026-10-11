@extends('hr.layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('hr.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top px-4 py-3 border-bottom shadow-sm mb-2">
                    <div class="d-flex align-items-center gap-3 flex-wrap w-100">
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">Leave Requests</h5>
                            <small class="text-muted">Manage and approve employee leave requests</small>
                        </div>
                        <div class="ms-auto">
                            @if (auth()->user()->hasPermissionTo('Create LeaveRequest'))
                            <a href="{{ route('hr.leave-requests.create', $tenantKey) }}" class="btn btn-primary btn-sm">
                                <i class="feather-plus me-1"></i> New Request
                            </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="content-area-body px-4 py-4">
                    @include('elements.flash')

                    <div class="card shadow-sm border-0">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Employee</th>
                                            <th>Leave Type</th>
                                            <th>Dates</th>
                                            <th>Days</th>
                                            <th>Status</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($leaveRequests as $request)
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold">{{ $request->staff?->first_name }}
                                                        {{ $request->staff?->last_name }}</div>
                                                    <div class="text-muted small">
                                                        {{ $request->staff?->designation?->name ?? 'Designation' }}</div>
                                                </td>
                                                <td>{{ $request->leaveType?->name ?? 'N/A' }}</td>
                                                <td>
                                                    {{ optional($request->from_date)->format('d M Y') }} -
                                                    {{ optional($request->to_date)->format('d M Y') }}
                                                </td>
                                                <td>{{ $request->total_days }}</td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $request->status === 'approved' ? 'success' : ($request->status === 'rejected' ? 'danger' : ($request->status === 'cancelled' ? 'secondary' : 'warning')) }}">
                                                        {{ ucfirst($request->status) }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                        <div class="hstack gap-2 justify-content-center">
                                                            @if (auth()->user()->hasPermissionTo('View LeaveRequest'))
                                                            <a href="{{ route('hr.leave-requests.show', [$tenantKey, $request]) }}"
                                                                class="avatar-text avatar-md text-primary" title="View">
                                                                <i class="bi bi-eye"></i>
                                                            </a>
                                                            @endif

                                                            @if (auth()->user()->hasPermissionTo('Edit LeaveRequest'))
                                                            <a href="{{ route('hr.leave-requests.edit', [$tenantKey, $request]) }}"
                                                                class="avatar-text avatar-md text-warning" title="Edit">
                                                                <i class="bi bi-pencil-square"></i>
                                                            </a>
                                                            @endif

                                                            @if ($request->status === 'pending')
                                                                @if (auth()->user()->hasPermissionTo('Edit LeaveRequest'))
                                                                <form
                                                                    action="{{ route('hr.leave-requests.approve', [$tenantKey, $request]) }}"
                                                                    method="POST" class="d-inline">
                                                                    @csrf
                                                                    <input type="hidden" name="remarks" value="Approved from list">
                                                                    <button type="submit"
                                                                        class="btn btn-sm btn-outline-success">Approve</button>
                                                                </form>
                                                                <form
                                                                    action="{{ route('hr.leave-requests.reject', [$tenantKey, $request]) }}"
                                                                    method="POST" class="d-inline">
                                                                    @csrf
                                                                    <input type="hidden" name="remarks" value="Rejected from list">
                                                                    <button type="submit"
                                                                        class="btn btn-sm btn-outline-danger">Reject</button>
                                                                </form>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-5 text-muted">No leave requests
                                                    found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-3">
                                {{ $leaveRequests->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
