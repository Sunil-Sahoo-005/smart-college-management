@extends('hr.layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('hr.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">
            <div class="content-area-header bg-white sticky-top px-4 py-3 border-bottom shadow-sm mb-2">
                <div class="d-flex align-items-center justify-content-between w-100 flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold mb-0">{{ $assignment->exists ? 'Edit Salary Assignment' : 'New Salary Assignment' }}</h5>
                        <small class="text-muted">Search by employee ID or name to load staff details first.</small>
                    </div>
                    <a href="{{ route('hr.staff-salary-assignments.index', $tenantKey) }}" class="btn btn-outline-secondary btn-sm">Back</a>
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

                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Assignment Form</h6>

                                <div class="mb-3">
                                    <label class="form-label">Find Employee</label>
                                    <div class="input-group">
                                        <input type="text" id="employeeSearch" class="form-control" placeholder="Enter employee ID or name">
                                        <button type="button" class="btn btn-outline-primary" id="fetchEmployeeBtn">Fetch</button>
                                    </div>
                                    <div class="form-text">Search first, then the form will fill automatically.</div>
                                </div>

                                <form method="POST" action="{{ $assignment->exists ? route('hr.staff-salary-assignments.update', [$tenantKey, $assignment]) : route('hr.staff-salary-assignments.store', $tenantKey) }}" id="assignmentForm">
                                    @csrf
                                    @if($assignment->exists)
                                        @method('PUT')
                                    @endif

                                    <input type="hidden" name="staff_id" id="staff_id" value="{{ old('staff_id', $assignment->staff_id) }}">

                                    <div class="mb-3">
                                        <label class="form-label">Selected Employee</label>
                                        <input type="text" class="form-control" id="selectedEmployee" value="{{ old('employee_display', $assignment->staff?->employee_id ? $assignment->staff?->employee_id . ' - ' . $assignment->staff?->first_name . ' ' . $assignment->staff?->last_name : '') }}" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Salary Structure</label>
                                        <select name="salary_structure_id" id="salary_structure_id" class="form-select" required>
                                            <option value="">Select Structure</option>
                                            @foreach($salaryStructures as $structure)
                                                <option value="{{ $structure->id }}" @selected(old('salary_structure_id', $assignment->salary_structure_id) == $structure->id)>{{ $structure->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                <div class="mb-3">
                                    <label class="form-label">Gross Salary</label>
                                    <input type="number" min="0" step="0.01" name="gross_salary" id="gross_salary" class="form-control" value="{{ old('gross_salary', $assignment->gross_salary) }}" required>
                                </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Effective From</label>
                                            <input type="date" name="effective_from" class="form-control" value="{{ old('effective_from', optional($assignment->effective_from)->format('Y-m-d') ?: now()->toDateString()) }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Effective To</label>
                                            <input type="date" name="effective_to" class="form-control" value="{{ old('effective_to', optional($assignment->effective_to)->format('Y-m-d')) }}">
                                        </div>
                                    </div>

                                    <div class="form-check form-switch mt-4 mb-4">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $assignment->exists ? $assignment->is_active : true) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="is_active">Active Assignment</label>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">Save</button>
                                        <a href="{{ route('hr.staff-salary-assignments.index', $tenantKey) }}" class="btn btn-light">Cancel</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Employee Details</h6>
                                <div id="employeeDetails" class="text-muted">
                                    Search an employee to see their details here.
                                </div>

                                <hr>

                                <h6 class="fw-bold mb-3">Current Salary Assignment</h6>
                                <div id="assignmentDetails" class="text-muted">
                                    No employee loaded yet.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('fetchEmployeeBtn').addEventListener('click', fetchEmployee);
document.getElementById('employeeSearch').addEventListener('keydown', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        fetchEmployee();
    }
});

function fetchEmployee() {
    const q = document.getElementById('employeeSearch').value.trim();
    if (!q) return;

    const url = new URL("{{ route('hr.staff-salary-assignments.lookup', $tenantKey) }}", window.location.origin);
    url.searchParams.set('q', q);

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (!data.staff) {
                document.getElementById('employeeDetails').innerHTML = '<div class="alert alert-warning mb-0">No employee found.</div>';
                document.getElementById('assignmentDetails').innerHTML = '<div class="text-muted">No assignment found.</div>';
                return;
            }

            const staff = data.staff;
            const assignment = data.assignment;

            document.getElementById('staff_id').value = staff.id;
            document.getElementById('selectedEmployee').value = `${staff.employee_id} - ${staff.name}`;

            document.getElementById('employeeDetails').innerHTML = `
                <div class="p-3 rounded bg-light border">
                    <div class="fw-semibold mb-1">${staff.name}</div>
                    <div class="text-muted small">Employee ID: ${staff.employee_id}</div>
                    <div class="text-muted small">Phone: ${staff.phone ?? 'N/A'}</div>
                    <div class="text-muted small">Department: ${staff.department ?? 'N/A'}</div>
                    <div class="text-muted small">Designation: ${staff.designation ?? 'N/A'}</div>
                </div>
            `;

            if (assignment) {
                document.getElementById('salary_structure_id').value = assignment.salary_structure_id;
                document.getElementById('assignmentDetails').innerHTML = `
                    <div class="p-3 rounded bg-light border">
                        <div class="fw-semibold mb-1">${assignment.salary_structure_name ?? 'N/A'}</div>
                        <div class="text-muted small">Gross Salary: ₹ ${parseFloat(assignment.gross_salary).toFixed(2)}</div>
                        <div class="text-muted small">Effective From: ${assignment.effective_from ?? '-'}</div>
                        <div class="text-muted small">Effective To: ${assignment.effective_to ?? '-'}</div>
                        <div class="text-muted small">Status: ${assignment.is_active ? 'Active' : 'Inactive'}</div>
                    </div>
                `;
                document.getElementById('gross_salary').value = assignment.gross_salary;
                document.querySelector('[name="effective_from"]').value = assignment.effective_from ?? document.querySelector('[name="effective_from"]').value;
                document.querySelector('[name="effective_to"]').value = assignment.effective_to ?? '';
                document.querySelector('[name="is_active"]').checked = !!assignment.is_active;
            } else {
                document.getElementById('assignmentDetails').innerHTML = '<div class="alert alert-info mb-0">No active salary assignment found for this employee.</div>';
            }
        })
        .catch(() => {
            document.getElementById('employeeDetails').innerHTML = '<div class="alert alert-danger mb-0">Unable to fetch employee data.</div>';
        });
}
</script>
@endsection
