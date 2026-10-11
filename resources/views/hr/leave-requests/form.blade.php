@extends('hr.layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('hr.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">
            <div class="content-area-header bg-white sticky-top px-4 py-3 border-bottom shadow-sm mb-2">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div>
                        <h5 class="fw-bold mb-0">{{ $leaveRequest->exists ? 'Edit Leave Request' : 'New Leave Request' }}</h5>
                        <small class="text-muted">Create or update a staff leave request</small>
                    </div>
                    <a href="{{ route('hr.leave-requests.index', $tenantKey) }}" class="btn btn-outline-secondary btn-sm">Back</a>
                </div>
            </div>

            <div class="content-area-body px-4 py-4">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <form method="POST" action="{{ $leaveRequest->exists ? route('hr.leave-requests.update', [$tenantKey, $leaveRequest]) : route('hr.leave-requests.store', $tenantKey) }}">
                            @csrf
                            @if($leaveRequest->exists)
                                @method('PUT')
                            @endif

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label">Employee</label>
                                    <select name="staff_id" class="form-select" required>
                                        <option value="">Select Staff</option>
                                        @foreach($staffList as $staff)
                                            <option value="{{ $staff->id }}" @selected(old('staff_id', $leaveRequest->staff_id) == $staff->id)>
                                                {{ $staff->first_name }} {{ $staff->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Leave Type</label>
                                    <select name="leave_type_id" class="form-select" required>
                                        <option value="">Select Leave Type</option>
                                        @foreach($leaveTypes as $type)
                                            <option value="{{ $type->id }}" @selected(old('leave_type_id', $leaveRequest->leave_type_id) == $type->id)>{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">From Date</label>
                                    <input type="date" name="from_date" class="form-control" value="{{ old('from_date', optional($leaveRequest->from_date)->format('Y-m-d')) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">To Date</label>
                                    <input type="date" name="to_date" class="form-control" value="{{ old('to_date', optional($leaveRequest->to_date)->format('Y-m-d')) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Total Days</label>
                                    <input type="number" step="0.5" min="0.5" name="total_days" class="form-control" value="{{ old('total_days', $leaveRequest->total_days ?? 1) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Half Day</label>
                                    <select name="half_day" class="form-select">
                                        @foreach(['none' => 'None', 'first' => 'First Half', 'second' => 'Second Half'] as $value => $label)
                                            <option value="{{ $value }}" @selected(old('half_day', $leaveRequest->half_day ?? 'none') == $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'cancelled' => 'Cancelled'] as $value => $label)
                                            <option value="{{ $value }}" @selected(old('status', $leaveRequest->status ?? 'pending') == $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Reason</label>
                                    <textarea name="reason" class="form-control" rows="4" placeholder="Leave reason">{{ old('reason', $leaveRequest->reason) }}</textarea>
                                </div>
                            </div>

                            <div class="mt-4 d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Save Request</button>
                                <a href="{{ route('hr.leave-requests.index', $tenantKey) }}" class="btn btn-light">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
