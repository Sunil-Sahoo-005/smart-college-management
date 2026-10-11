@extends('hr.layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('hr.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">
            <div class="content-area-header bg-white sticky-top px-4 py-3 border-bottom shadow-sm mb-2">
                <div class="d-flex align-items-center justify-content-between w-100 flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold mb-0">Salary Assignments</h5>
                        <small class="text-muted">Filter and manage staff salary allocations.</small>
                    </div>
                    @if (auth()->user()->hasPermissionTo('Create SalaryAssignments'))
                    <a href="{{ route('hr.staff-salary-assignments.create', $tenantKey) }}" class="btn btn-primary btn-sm">
                        <i class="feather-plus me-1"></i> New Assignment
                    </a>
                    @endif
                </div>
            </div>

            <div class="content-area-body px-4 py-4">
                @include('elements.flash')

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <form method="GET" action="{{ route('hr.staff-salary-assignments.index', $tenantKey) }}">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label">Search</label>
                                    <input type="text" name="search" class="form-control" placeholder="Employee ID or name" value="{{ request('search') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Salary Structure</label>
                                    <select name="salary_structure_id" class="form-select">
                                        <option value="">All</option>
                                        @foreach($salaryStructures as $structure)
                                            <option value="{{ $structure->id }}" @selected(request('salary_structure_id') == $structure->id)>{{ $structure->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="">All</option>
                                        <option value="active" @selected(request('status') == 'active')>Active</option>
                                        <option value="inactive" @selected(request('status') == 'inactive')>Inactive</option>
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex gap-2">
                                    <button class="btn btn-primary flex-grow-1">Filter</button>
                                    <a href="{{ route('hr.staff-salary-assignments.index', $tenantKey) }}" class="btn btn-light">Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Employee</th>
                                        <th>Salary Structure</th>
                                        <th>Gross Salary</th>
                                        <th>Effective</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($assignments as $assignment)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $assignment->staff?->first_name }} {{ $assignment->staff?->last_name }}</div>
                                                <div class="text-muted small">{{ $assignment->staff?->employee_id }} · {{ $assignment->staff?->designation?->name ?? 'N/A' }}</div>
                                            </td>
                                            <td>{{ $assignment->salaryStructure?->name ?? 'N/A' }}</td>
                                            <td>₹ {{ number_format((float) $assignment->gross_salary, 2) }}</td>
                                            <td>
                                                {{ optional($assignment->effective_from)->format('d M Y') }}
                                                @if($assignment->effective_to)
                                                    <span class="text-muted">to {{ optional($assignment->effective_to)->format('d M Y') }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $assignment->is_active ? 'success' : 'secondary' }}">
                                                    {{ $assignment->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="hstack gap-2 justify-content-center">
                                                    @if (auth()->user()->hasPermissionTo('Edit SalaryAssignments'))
                                                    <a href="{{ route('hr.staff-salary-assignments.edit', [$tenantKey, $assignment]) }}"
                                                    class="avatar-text avatar-md text-warning"
                                                    title="Edit">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    @endif

                                                    @if (auth()->user()->hasPermissionTo('Delete SalaryAssignments'))
                                                    <form action="{{ route('hr.staff-salary-assignments.destroy', [$tenantKey, $assignment]) }}"
                                                        method="POST"
                                                        class="d-inline"
                                                        onsubmit="return confirm('Delete this assignment?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="avatar-text avatar-md text-danger border-0 bg-transparent"
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
                                            <td colspan="6" class="text-center py-5 text-muted">No salary assignments found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3">
                            {{ $assignments->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
