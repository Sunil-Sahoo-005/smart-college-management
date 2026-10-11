@extends('hr.layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('hr.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">
            <div class="content-area-header bg-white sticky-top px-4 py-3 border-bottom shadow-sm mb-2">
                <div class="d-flex align-items-center justify-content-between w-100 flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold mb-0">Payroll Run - {{ \Carbon\Carbon::create()->month($payrollRun->month)->format('F') }} {{ $payrollRun->year }}</h5>
                        <small class="text-muted">Review payslips, publish them, and record payments.</small>
                    </div>
                    <a href="{{ route('hr.payrolls.index', $tenantKey) }}" class="btn btn-outline-secondary btn-sm">Back</a>
                </div>
            </div>

            <div class="content-area-body px-4 py-4">
                @include('elements.flash')

                <div class="row g-4">
                    <div class="col-md-3"><div class="card shadow-sm border-0"><div class="card-body"><div class="text-muted small">Payslips</div><h3 class="mb-0">{{ $payrollRun->payslips->count() }}</h3></div></div></div>
                    <div class="col-md-3"><div class="card shadow-sm border-0"><div class="card-body"><div class="text-muted small">Published</div><h3 class="mb-0">{{ $payrollRun->payslips->where('is_published', true)->count() }}</h3></div></div></div>
                    <div class="col-md-3"><div class="card shadow-sm border-0"><div class="card-body"><div class="text-muted small">Total Net Salary</div><h3 class="mb-0">₹ {{ number_format($payrollRun->payslips->sum('net_salary'), 2) }}</h3></div></div></div>
                    <div class="col-md-3"><div class="card shadow-sm border-0"><div class="card-body"><div class="text-muted small">Status</div><h3 class="mb-0">{{ ucfirst($payrollRun->status) }}</h3></div></div></div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Employee</th>
                                        <th>Gross</th>
                                        <th>Working</th>
                                        <th>Deductions</th>
                                        <th>Net</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payrollRun->payslips as $payslip)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $payslip->staff?->first_name }} {{ $payslip->staff?->last_name }}</div>
                                                <div class="text-muted small">{{ $payslip->staff?->employee_id }}</div>
                                            </td>
                                            <td>₹ {{ number_format((float) $payslip->gross_salary, 2) }}</td>
                                            <td>{{ $payslip->present_days }} present / {{ $payslip->absent_days }} absent</td>
                                            <td>₹ {{ number_format((float) $payslip->total_deductions, 2) }}</td>
                                            <td>₹ {{ number_format((float) $payslip->net_salary, 2) }}</td>
                                            <td><span class="badge bg-{{ $payslip->is_published ? 'success' : 'secondary' }}">{{ $payslip->is_published ? 'Published' : 'Draft' }}</span></td>
                                            <td class="text-center">
                                                <div class="hstack gap-2 justify-content-center">
                                                <a href="{{ route('hr.payslips.show', [$tenantKey, $payslip]) }}" class="btn btn-sm btn-outline-info me-1">Review</a>
                                                @if(! $payslip->is_published)
                                                <form action="{{ route('hr.payslips.publish', [$tenantKey, $payslip]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button class="btn btn-sm btn-outline-success" type="submit">Publish</button>
                                                </form>
                                                @endif
                                                <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#payment-{{ $payslip->id }}">Payment</button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="collapse" id="payment-{{ $payslip->id }}">
                                            <td colspan="7" class="bg-light">
                                                <form method="POST" action="{{ route('hr.payslips.payment.store', [$tenantKey, $payslip]) }}" class="row g-3 align-items-end p-3">
                                                    @csrf
                                                    <div class="col-md-2">
                                                        <label class="form-label">Date</label>
                                                        <input type="date" name="payment_date" class="form-control" value="{{ now()->toDateString() }}" required>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label">Amount</label>
                                                        <input type="number" step="0.01" name="amount" class="form-control" value="{{ $payslip->net_salary }}" required>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label">Mode</label>
                                                        <select name="payment_mode" class="form-select">
                                                            <option value="bank_transfer">Bank Transfer</option>
                                                            <option value="cash">Cash</option>
                                                            <option value="cheque">Cheque</option>
                                                            <option value="upi">UPI</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label">Reference</label>
                                                        <input type="text" name="transaction_reference" class="form-control">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label">Status</label>
                                                        <select name="status" class="form-select">
                                                            <option value="pending">Pending</option>
                                                            <option value="paid">Paid</option>
                                                            <option value="failed">Failed</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2 d-grid">
                                                        <button type="submit" class="btn btn-primary">Save Payment</button>
                                                    </div>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5 text-muted">No payslips generated.</td>
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
