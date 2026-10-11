@extends('hr.layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('hr.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">
            <div class="content-area-header bg-white sticky-top px-4 py-3 border-bottom shadow-sm ">
                <div class="d-flex align-items-center gap-3 flex-wrap w-100">
                    <div>
                        <h5 class="fw-bold mb-0">Attendance</h5>
                        <small class="text-muted">Default view shows today. Filter by date, staff, department, and designation.</small>
                    </div>
                    <div class="ms-auto d-flex gap-2">
                            @if (auth()->user()->hasPermissionTo('View Attendance'))
                            <a href="{{ route('hr.attendance.export', array_merge(['tenantKey' => $tenantKey], request()->only(['date', 'search', 'department_id', 'designation_id', 'status']))) }}"
                            class="btn btn-outline-success btn-sm">
                                <i class="feather-download me-1"></i> Export
                            </a>
                            @endif
                        </div>
                </div>
            </div>

            <div class="content-area-body px-4 py-4">
                @include('elements.flash')

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('import_errors'))
                    <div class="alert alert-warning">
                        <div class="fw-semibold mb-2">Import issues</div>
                        <ul class="mb-0">
                            @foreach(session('import_errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row g-4">
                    <div class="col-md-3">
                        <div class="card shadow-sm border-0"><div class="card-body">
                            <div class="text-muted small text-uppercase fw-bold">Present</div>
                            <h3 class="mb-0">{{ $stats['present'] ?? 0 }}</h3>
                        </div></div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm border-0"><div class="card-body">
                            <div class="text-muted small text-uppercase fw-bold">Absent</div>
                            <h3 class="mb-0">{{ $stats['absent'] ?? 0 }}</h3>
                        </div></div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm border-0"><div class="card-body">
                            <div class="text-muted small text-uppercase fw-bold">Half Day</div>
                            <h3 class="mb-0">{{ $stats['half_day'] ?? 0 }}</h3>
                        </div></div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm border-0"><div class="card-body">
                            <div class="text-muted small text-uppercase fw-bold">Leave</div>
                            <h3 class="mb-0">{{ $stats['leave'] ?? 0 }}</h3>
                        </div></div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <form method="GET" action="{{ route('hr.attendance.index', $tenantKey) }}">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-2">
                                    <label class="form-label">Date</label>
                                    <input type="date" name="date" class="form-control" value="{{ $date }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Staff Search</label>
                                    <input type="text" name="search" class="form-control" placeholder="Name or phone" value="{{ request('search') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Department</label>
                                    <select name="department_id" class="form-select">
                                        <option value="">All</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}" @selected(request('department_id') == $dept->id)>{{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Designation</label>
                                    <select name="designation_id" class="form-select">
                                        <option value="">All</option>
                                        @foreach($designations as $desig)
                                            <option value="{{ $desig->id }}" @selected(request('designation_id') == $desig->id)>{{ $desig->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="">All</option>
                                        @foreach(['present','absent','half_day','leave','holiday','week_off'] as $s)
                                            <option value="{{ $s }}" @selected(request('status') == $s)>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-1 d-grid">
                                    <button class="btn btn-primary">Filter</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-xl-8">
                        <div class="card shadow-sm border-0">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Employee</th>
                                                <th>Department</th>
                                                <th>Designation</th>
                                                <th>Check In/Out</th>
                                                <th>Status</th>
                                                <th>Source</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($attendances as $attendance)
                                                <tr>
                                                    <td>
                                                        <div class="fw-semibold">{{ $attendance->staff?->first_name }} {{ $attendance->staff?->last_name }}</div>
                                                        <div class="text-muted small">{{ $attendance->staff?->phone }}</div>
                                                    </td>
                                                    <td>{{ $attendance->staff?->department?->name ?? 'N/A' }}</td>
                                                    <td>{{ $attendance->staff?->designation?->name ?? 'N/A' }}</td>
                                                    <td>
                                                        <div>{{ $attendance->check_in ?: '--' }} / {{ $attendance->check_out ?: '--' }}</div>
                                                        <small class="text-muted">{{ $attendance->working_minutes }} mins</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $attendance->status === 'present' ? 'success' : ($attendance->status === 'absent' ? 'danger' : 'warning') }}">
                                                            {{ ucfirst(str_replace('_', ' ', $attendance->status)) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ ucfirst($attendance->source) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-5 text-muted">No staff found for the selected filters.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="p-3">
                                    {{ $attendances->links() }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4">
                        @if (auth()->user()->hasPermissionTo('Create Attendance'))
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h6 class="fw-bold mb-1">Bulk Excel Import</h6>
                                        <small class="text-muted">Download the template first, then fill in the rows.</small>
                                    </div>
                                </div>

                                <a href="{{ route('hr.attendance.template', $tenantKey) }}" class="btn btn-outline-primary w-100 mb-3">
                                    <i class="feather-download me-1"></i> Download Template
                                </a>

                                <form method="POST" action="{{ route('hr.attendance.import', $tenantKey) }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Excel File</label>
                                        <input type="file" name="import_file" class="form-control" accept=".xlsx,.xls,.csv" required>
                                    </div>
                                    <div class="alert alert-light border small mb-3">
                                        Use the template format. The first row contains headers, and sample rows show how to mark present and absent employees.
                                        Use <code>employee_id</code> in the first column instead of a database id.
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">Import Attendance</button>
                                </form>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
