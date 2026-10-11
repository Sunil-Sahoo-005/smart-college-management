@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Accountants.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Scholarship Assignments</span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                            <a href="{{ route('scholarships.assign.form') }}" class="btn btn-primary">
                                <i class="feather-plus me-2"></i>
                                <span>Assign Scholarship</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="content-area-body">
                    @include('elements.flash')

                    <div class="card stretch stretch-full mb-3">
                        <div class="card-body">
                            <form method="GET" action="{{ route('scholarships.list') }}" class="row g-3 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Search Student / Admission No.</label>
                                    <input
                                        type="text"
                                        name="student_search"
                                        class="form-control"
                                        value="{{ request('student_search') }}"
                                        placeholder="Type student name or admission number"
                                    >
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Scholarship</label>
                                    <select name="scholarship_id" class="form-control">
                                        <option value="">All Scholarships</option>
                                        @foreach($scholarships as $scholarship)
                                            <option value="{{ $scholarship->id }}" {{ request('scholarship_id') == $scholarship->id ? 'selected' : '' }}>
                                                {{ $scholarship->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label fw-semibold">Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                </div>

                                <div class="col-md-2 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-fill">Filter</button>
                                    <a href="{{ route('scholarships.list') }}" class="btn btn-light flex-fill">Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card stretch stretch-full">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover" id="scholarshipAssignmentList">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Student</th>
                                            <th>Admission No.</th>
                                            <th>Scholarship</th>
                                            <th>Approved Amount</th>
                                            <th>Used Amount</th>
                                            <th>Balance</th>
                                            <th>Status</th>
                                            <th>Assigned On</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($assignments as $assignment)
                                            <tr class="single-item">
                                                <td>{{ $loop->iteration }}</td>
                                                <td class="project-name-td">
                                                    <div class="hstack gap-3">
                                                        <div class="avatar-image avatar-md">
                                                            <div class="avatar-text avatar-md bg-soft-primary text-primary">
                                                                {{ strtoupper(substr($assignment->student->first_name ?? $assignment->student->name ?? 'S', 0, 1)) }}
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <span class="fw-semibold text-truncate-1-line">
                                                                {{ $assignment->student->first_name ?? '' }} {{ $assignment->student->last_name ?? $assignment->student->name ?? 'N/A' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-soft-secondary text-secondary">
                                                        {{ $assignment->student->admission_number ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="fw-semibold">{{ $assignment->scholarship->name ?? 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    <span class="text-dark fw-semibold">₹{{ number_format($assignment->approved_amount, 2) }}</span>
                                                </td>
                                                <td>
                                                    <span class="text-danger">₹{{ number_format($assignment->used_amount ?? 0, 2) }}</span>
                                                </td>
                                                <td>
                                                    <span class="text-success fw-semibold">₹{{ number_format($assignment->balance ?? 0, 2) }}</span>
                                                </td>
                                                <td>
                                                    @php
                                                        $statusMap = [
                                                            'approved'  => 'bg-soft-success text-success',
                                                            'pending'   => 'bg-soft-warning text-warning',
                                                            'rejected'  => 'bg-soft-danger text-danger',
                                                            'cancelled' => 'bg-soft-secondary text-secondary',
                                                        ];
                                                        $badgeClass = $statusMap[$assignment->status] ?? 'bg-soft-secondary text-secondary';
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }}">
                                                        {{ ucfirst($assignment->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $assignment->created_at->format('d M Y') }}</td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        @if($assignment->status === 'pending')
                                                            <form action="{{ route('scholarships.approve', $assignment->id) }}" method="POST" onsubmit="return confirm('Approve this scholarship and clear pending installments?');">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-primary">
                                                                    Approve
                                                                </button>
                                                            </form>

                                                            <form action="{{ route('scholarships.reject', $assignment->id) }}" method="POST" onsubmit="return confirm('Reject this scholarship application?');">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-danger">
                                                                    Reject
                                                                </button>
                                                            </form>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center py-5 text-muted">
                                                    <i class="feather-award d-block fs-1 mb-3 opacity-50"></i>
                                                    No scholarship assignments found.
                                                    <div class="mt-3">
                                                        <a href="{{ route('scholarships.assign.form') }}" class="btn btn-sm btn-primary">
                                                            Assign your first scholarship
                                                        </a>
                                                    </div>
                                                </td>
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
