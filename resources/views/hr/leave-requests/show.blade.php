@extends('hr.layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('hr.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">
            <div class="content-area-header bg-white sticky-top px-4 py-3 border-bottom shadow-sm mb-2">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div>
                        <h5 class="fw-bold mb-0">Leave Request Details</h5>
                        <small class="text-muted">{{ $leaveRequest->staff?->first_name }} {{ $leaveRequest->staff?->last_name }}</small>
                    </div>
                    <a href="{{ route('hr.leave-requests.index', $tenantKey) }}" class="btn btn-outline-secondary btn-sm">Back</a>
                </div>
            </div>

            <div class="content-area-body px-4 py-4">
                @include('elements.flash')

                <div class="row g-4">
                    <div class="col-xl-8">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <div class="d-flex justify-content-between flex-wrap gap-2 mb-4">
                                    <div>
                                        <h4 class="mb-1">{{ $leaveRequest->staff?->first_name }} {{ $leaveRequest->staff?->last_name }}</h4>
                                        <div class="text-muted">{{ $leaveRequest->staff?->designation?->name ?? 'Designation' }} · {{ $leaveRequest->leaveType?->name ?? 'Leave Type' }}</div>
                                    </div>
                                    <span class="badge bg-{{ $leaveRequest->status === 'approved' ? 'success' : ($leaveRequest->status === 'rejected' ? 'danger' : ($leaveRequest->status === 'cancelled' ? 'secondary' : 'warning')) }} align-self-start">
                                        {{ ucfirst($leaveRequest->status) }}
                                    </span>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="p-3 rounded bg-light">
                                            <div class="text-muted small">From</div>
                                            <div class="fw-semibold">{{ optional($leaveRequest->from_date)->format('d M Y') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 rounded bg-light">
                                            <div class="text-muted small">To</div>
                                            <div class="fw-semibold">{{ optional($leaveRequest->to_date)->format('d M Y') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 rounded bg-light">
                                            <div class="text-muted small">Days</div>
                                            <div class="fw-semibold">{{ $leaveRequest->total_days }}</div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="p-3 rounded bg-light">
                                            <div class="text-muted small mb-1">Reason</div>
                                            <div>{{ $leaveRequest->reason ?: 'No reason provided.' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Actions</h6>
                                <div class="d-grid gap-2">
                                    <a href="{{ route('hr.leave-requests.edit', [$tenantKey, $leaveRequest]) }}" class="btn btn-outline-primary">Edit Request</a>
                                    @if($leaveRequest->status === 'pending')
                                        <form action="{{ route('hr.leave-requests.approve', [$tenantKey, $leaveRequest]) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="remarks" value="Approved by HR">
                                            <button type="submit" class="btn btn-success w-100">Approve</button>
                                        </form>
                                        <form action="{{ route('hr.leave-requests.reject', [$tenantKey, $leaveRequest]) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="remarks" value="Rejected by HR">
                                            <button type="submit" class="btn btn-danger w-100">Reject</button>
                                        </form>
                                    @endif
                                    <form action="{{ route('hr.leave-requests.destroy', [$tenantKey, $leaveRequest]) }}" method="POST" onsubmit="return confirm('Delete this leave request?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger w-100">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Approval History</h6>
                                <div class="d-grid gap-3">
                                    @forelse($leaveRequest->approvalLogs as $log)
                                        <div class="p-3 rounded border">
                                            <div class="d-flex justify-content-between">
                                                <div class="fw-semibold text-capitalize">{{ $log->action }}</div>
                                                <small class="text-muted">{{ $log->created_at->format('d M Y H:i') }}</small>
                                            </div>
                                            <div class="text-muted small mt-1">By {{ $log->actionedBy?->first_name }} {{ $log->actionedBy?->last_name }}</div>
                                            <div class="mt-1">{{ $log->remarks ?: 'No remarks' }}</div>
                                        </div>
                                    @empty
                                        <div class="text-muted">No approval history yet.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
