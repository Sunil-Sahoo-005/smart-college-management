@extends('hr.layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('hr.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">
            <div class="content-area-header bg-white sticky-top px-4 py-3 border-bottom shadow-sm mb-2">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div>
                        <h5 class="fw-bold mb-0">Run Payroll</h5>
                        <small class="text-muted">Pick a month and the system will build payslips using attendance and leave data.</small>
                    </div>
                    <a href="{{ route('hr.payrolls.index', $tenantKey) }}" class="btn btn-outline-secondary btn-sm">Back</a>
                </div>
            </div>

            <div class="content-area-body px-4 py-4">
                <div class="row g-4">
                    <div class="col-lg-7">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('hr.payrolls.store', $tenantKey) }}">
                                    @csrf

                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="form-label">Month</label>
                                            <select name="month" class="form-select" required>
                                                @foreach(range(1, 12) as $m)
                                                    <option value="{{ $m }}" @selected(old('month', now()->month) == $m)>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Year</label>
                                            <input type="number" name="year" class="form-control" value="{{ old('year', now()->year) }}" required>
                                        </div>
                                    </div>

                                    <div class="alert alert-light border mt-4">
                                        Payroll will use:
                                        <ul class="mb-0 mt-2">
                                            <li>active salary assignments</li>
                                            <li>attendance records for the selected month</li>
                                            <li>approved leave requests</li>
                                            <li>holidays in the selected month</li>
                                        </ul>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">Process Payroll</button>
                                        <a href="{{ route('hr.payrolls.index', $tenantKey) }}" class="btn btn-light">Cancel</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Quick Notes</h6>
                                <div class="text-muted small">
                                    Start payroll only after attendance is finalized for the month. You can publish payslips after reviewing the calculated values.
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
