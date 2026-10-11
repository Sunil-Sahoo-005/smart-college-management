@extends('hr.layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('hr.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">
            <!-- HEADER -->
            <div class="content-area-header bg-white sticky-top px-4 py-3 border-bottom shadow-sm mb-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 w-100">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">Payslip Review</h5>
                        <small class="text-muted">
                            {{ $payslip->staff?->first_name }} {{ $payslip->staff?->last_name }} · 
                            {{ \Carbon\Carbon::create()->month($payslip->payrollRun->month)->format('F') }} {{ $payslip->payrollRun->year }}
                        </small>
                    </div>
                    <div class="d-flex align-items-center gap-2 no-print">
                        <a href="{{ route('hr.payrolls.show', [$tenantKey, $payslip->payroll_run_id]) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="feather-arrow-left me-1"></i>Back to Payroll Run
                        </a>
                        @if(!$payslip->is_published)
                            <a href="{{ route('hr.payslips.edit', [$tenantKey, $payslip]) }}" class="btn btn-primary btn-sm">
                                <i class="feather-edit-3 me-1"></i>Adjust / Edit
                            </a>
                        @endif
                        <button onclick="window.print()" class="btn btn-outline-primary btn-sm">
                            <i class="feather-printer me-1"></i>Print
                        </button>
                    </div>
                </div>
            </div>

            <!-- CONTENT BODY -->
            <div class="content-area-body px-4 pb-4">
                @include('elements.flash')

                <div class="row g-4">
                    <!-- Left column (Employee Info & Summary) -->
                    <div class="col-xl-4">
                        <!-- Employee Info Card -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body p-4">
                                <div class="text-center mb-4">
                                    @if ($payslip->staff?->photo_url)
                                        <img src="{{ asset('storage/' . $payslip->staff->photo_url) }}" class="rounded-circle mb-3 shadow-xs" style="width:90px; height:90px; object-fit:cover;">
                                    @else
                                        <div class="avatar-text bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold mx-auto mb-3" style="width:90px; height:90px; font-size:28px;">
                                            {{ substr($payslip->staff?->first_name, 0, 1) }}{{ substr($payslip->staff?->last_name, 0, 1) }}
                                        </div>
                                    @endif
                                    <h5 class="fw-bold mb-1 text-dark">{{ $payslip->staff?->first_name }} {{ $payslip->staff?->last_name }}</h5>
                                    <span class="badge bg-soft-secondary text-muted px-2 py-1 fs-10">{{ $payslip->staff?->designation?->name ?? 'Faculty' }}</span>
                                </div>

                                <ul class="list-group list-group-flush mb-0">
                                    <li class="list-group-item px-0 py-2 border-0 d-flex justify-content-between fs-12">
                                        <span class="text-muted">Employee ID</span>
                                        <span class="fw-semibold text-dark">{{ $payslip->staff?->employee_id }}</span>
                                    </li>
                                    <li class="list-group-item px-0 py-2 border-0 d-flex justify-content-between fs-12">
                                        <span class="text-muted">Department</span>
                                        <span class="fw-semibold text-dark">{{ $payslip->staff?->department?->name ?? 'N/A' }}</span>
                                    </li>
                                    <li class="list-group-item px-0 py-2 border-0 d-flex justify-content-between fs-12">
                                        <span class="text-muted">Payslip Status</span>
                                        <span class="badge bg-{{ $payslip->is_published ? 'success' : 'warning' }}">
                                            {{ $payslip->is_published ? 'Published' : 'Draft' }}
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Attendance Summary Card -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header border-0 bg-transparent pt-4 px-4">
                                <h6 class="fw-bold text-dark mb-0"><i class="feather-calendar me-2 text-primary"></i>Attendance Info</h6>
                            </div>
                            <div class="card-body p-4 pt-2">
                                <div class="row text-center g-2">
                                    <div class="col-4 border-end">
                                        <h4 class="fw-bold mb-0 text-dark">{{ $payslip->working_days }}</h4>
                                        <small class="text-muted fs-9 text-uppercase">Working Days</small>
                                    </div>
                                    <div class="col-4 border-end">
                                        <h4 class="fw-bold mb-0 text-success">{{ $payslip->present_days }}</h4>
                                        <small class="text-muted fs-9 text-uppercase">Present</small>
                                    </div>
                                    <div class="col-4">
                                        <h4 class="fw-bold mb-0 text-danger">{{ $payslip->absent_days }}</h4>
                                        <small class="text-muted fs-9 text-uppercase">Absent</small>
                                    </div>
                                </div>
                                <hr class="opacity-10 my-3">
                                <ul class="list-group list-group-flush mb-0">
                                    <li class="list-group-item px-0 py-2 border-0 d-flex justify-content-between fs-12">
                                        <span class="text-muted">Leaves Taken</span>
                                        <span class="fw-semibold text-dark">{{ $payslip->leave_days }}</span>
                                    </li>
                                    <li class="list-group-item px-0 py-2 border-0 d-flex justify-content-between fs-12">
                                        <span class="text-muted">Half-Days</span>
                                        <span class="fw-semibold text-dark">{{ $payslip->half_days }}</span>
                                    </li>
                                    <li class="list-group-item px-0 py-2 border-0 d-flex justify-content-between fs-12">
                                        <span class="text-muted">Late Marks</span>
                                        <span class="fw-semibold text-dark">{{ $payslip->late_marks }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Net Salary Overview -->
                        <div class="card border-0 shadow-sm bg-primary text-white overflow-hidden" style="background-image: linear-gradient(135deg, #1e3a8a, #2563eb);">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <span class="fs-11 opacity-75">NET PAYABLE SALARY</span>
                                    <i class="feather-award fs-3 opacity-50"></i>
                                </div>
                                <h2 class="fw-bold mb-3">₹ {{ number_format($payslip->net_salary, 2) }}</h2>
                                <div class="border-top border-white border-opacity-10 pt-3">
                                    <div class="d-flex justify-content-between fs-11 opacity-75 mb-1">
                                        <span>Total Earnings:</span>
                                        <span>₹ {{ number_format($payslip->total_earnings, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between fs-11 opacity-75">
                                        <span>Total Deductions:</span>
                                        <span>₹ {{ number_format($payslip->total_deductions, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column (Salary Details & Breakup) -->
                    <div class="col-xl-8">
                        <!-- Earnings & Deductions Tables -->
                        <div class="row g-4">
                            <!-- Earnings Card -->
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-header border-0 bg-transparent pt-4 px-4 d-flex align-items-center justify-content-between">
                                        <h6 class="fw-bold text-success mb-0"><i class="feather-plus-circle me-2"></i>Earnings</h6>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="px-4">Component</th>
                                                    <th class="text-end px-4">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="px-4 fw-semibold text-dark">Gross Basic Salary</td>
                                                    <td class="text-end px-4 text-dark">₹ {{ number_format($payslip->gross_salary, 2) }}</td>
                                                </tr>
                                                @foreach($payslip->earnings_breakdown ?? [] as $earn)
                                                    <tr>
                                                        <td class="px-4 text-muted fs-12">{{ $earn['label'] ?? 'Earning' }}</td>
                                                        <td class="text-end px-4 text-success">+ ₹ {{ number_format((float)($earn['amount'] ?? 0), 2) }}</td>
                                                    </tr>
                                                @endforeach
                                                @if($payslip->bonus_amount > 0)
                                                    <tr>
                                                        <td class="px-4 text-muted fs-12">Bonus</td>
                                                        <td class="text-end px-4 text-success">+ ₹ {{ number_format($payslip->bonus_amount, 2) }}</td>
                                                    </tr>
                                                @endif
                                                @if($payslip->arrear_amount > 0)
                                                    <tr>
                                                        <td class="px-4 text-muted fs-12">Arrears</td>
                                                        <td class="text-end px-4 text-success">+ ₹ {{ number_format($payslip->arrear_amount, 2) }}</td>
                                                    </tr>
                                                @endif
                                                @if($payslip->overtime_amount > 0)
                                                    <tr>
                                                        <td class="px-4 text-muted fs-12">Overtime</td>
                                                        <td class="text-end px-4 text-success">+ ₹ {{ number_format($payslip->overtime_amount, 2) }}</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                            <tfoot class="table-light">
                                                <tr>
                                                    <th class="px-4 text-dark">Total Earnings</th>
                                                    <th class="text-end px-4 text-success">₹ {{ number_format($payslip->total_earnings, 2) }}</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Deductions Card -->
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-header border-0 bg-transparent pt-4 px-4 d-flex align-items-center justify-content-between">
                                        <h6 class="fw-bold text-danger mb-0"><i class="feather-minus-circle me-2"></i>Deductions</h6>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="px-4">Component</th>
                                                    <th class="text-end px-4">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $hasLop = false; @endphp
                                                @foreach($payslip->deductions_breakdown ?? [] as $ded)
                                                    @if(isset($ded['key']) && $ded['key'] === 'lop')
                                                        @php $hasLop = true; @endphp
                                                    @endif
                                                    <tr>
                                                        <td class="px-4 text-muted fs-12">{{ $ded['label'] ?? 'Deduction' }}</td>
                                                        <td class="text-end px-4 text-danger">- ₹ {{ number_format((float)($ded['amount'] ?? 0), 2) }}</td>
                                                    </tr>
                                                @endforeach
                                                @if(!$hasLop && $payslip->lop_amount > 0)
                                                    <tr>
                                                        <td class="px-4 text-muted fs-12">Loss of Pay</td>
                                                        <td class="text-end px-4 text-danger">- ₹ {{ number_format($payslip->lop_amount, 2) }}</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                            <tfoot class="table-light">
                                                <tr>
                                                    <th class="px-4 text-dark">Total Deductions</th>
                                                    <th class="text-end px-4 text-danger">₹ {{ number_format($payslip->total_deductions, 2) }}</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
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
