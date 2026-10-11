@extends('hr.layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('hr.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">
            <div class="content-area-header bg-white sticky-top px-4 py-3 border-bottom shadow-sm mb-2">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div>
                        <h5 class="fw-bold mb-0">Payroll Runs</h5>
                        <small class="text-muted">Run payroll and manage payslips for each month.</small>
                    </div>
                    <a href="{{ route('hr.payrolls.create', $tenantKey) }}" class="btn btn-primary btn-sm">
                        <i class="feather-plus me-1"></i> Run Payroll
                    </a>
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
                                        <th>Month</th>
                                        <th>Year</th>
                                        <th>Status</th>
                                        <th>Processed At</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payrollRuns as $run)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::create()->month($run->month)->format('F') }}</td>
                                            <td>{{ $run->year }}</td>
                                            <td><span class="badge bg-primary">{{ ucfirst($run->status) }}</span></td>
                                            <td>{{ optional($run->processed_at)->format('d M Y H:i') ?: '—' }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('hr.payrolls.show', [$tenantKey, $run]) }}" class="btn btn-sm btn-outline-info">View</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">No payroll runs yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3">{{ $payrollRuns->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
