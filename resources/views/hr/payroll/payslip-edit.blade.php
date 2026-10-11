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
                        <h5 class="fw-bold mb-1 text-dark">Adjust Payslip</h5>
                        <small class="text-muted">
                            Adjusting payroll parameters for {{ $payslip->staff?->first_name }} {{ $payslip->staff?->last_name }}
                        </small>
                    </div>
                    <a href="{{ route('hr.payslips.show', [$tenantKey, $payslip]) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="feather-arrow-left me-1"></i>Cancel
                    </a>
                </div>
            </div>

            <!-- CONTENT BODY -->
            <div class="content-area-body px-4 pb-4">
                @include('elements.flash')

                <form method="POST" action="{{ route('hr.payslips.update', [$tenantKey, $payslip]) }}" class="row g-4">
                    @csrf
                    @method('PUT')

                    <!-- Left Card: Attendance override -->
                    <div class="col-xl-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header border-0 bg-transparent pt-4 px-4">
                                <h6 class="fw-bold text-dark mb-0"><i class="feather-calendar me-2 text-primary"></i>Attendance Overrides</h6>
                            </div>
                            <div class="card-body p-4 pt-2">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-semibold">Working Days</label>
                                        <input type="number" name="working_days" class="form-control" value="{{ old('working_days', $payslip->working_days) }}" required min="0">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-semibold">Present Days</label>
                                        <input type="number" name="present_days" class="form-control" value="{{ old('present_days', $payslip->present_days) }}" required min="0">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-semibold">Absent Days</label>
                                        <input type="number" name="absent_days" class="form-control" value="{{ old('absent_days', $payslip->absent_days) }}" required min="0">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-semibold">Leave Days</label>
                                        <input type="number" name="leave_days" class="form-control" value="{{ old('leave_days', $payslip->leave_days) }}" required min="0">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-semibold">Half Days</label>
                                        <input type="number" name="half_days" class="form-control" value="{{ old('half_days', $payslip->half_days) }}" required min="0">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-semibold">Late Marks</label>
                                        <input type="number" name="late_marks" class="form-control" value="{{ old('late_marks', $payslip->late_marks) }}" required min="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Card: Amount Overrides -->
                    <div class="col-xl-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header border-0 bg-transparent pt-4 px-4">
                                <h6 class="fw-bold text-dark mb-0"><i class="feather-dollar-sign me-2 text-success"></i>Financial Parameters Override</h6>
                            </div>
                            <div class="card-body p-4 pt-2">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label text-muted small fw-semibold">Base / Gross Salary (Non-editable)</label>
                                        <input type="text" class="form-control bg-light" value="₹ {{ number_format($payslip->gross_salary, 2) }}" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-semibold">Bonus Amount</label>
                                        <div class="input-group">
                                            <span class="input-group-text">₹</span>
                                            <input type="number" step="0.01" name="bonus_amount" class="form-control" value="{{ old('bonus_amount', $payslip->bonus_amount) }}" required min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-semibold">Arrear Amount</label>
                                        <div class="input-group">
                                            <span class="input-group-text">₹</span>
                                            <input type="number" step="0.01" name="arrear_amount" class="form-control" value="{{ old('arrear_amount', $payslip->arrear_amount) }}" required min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-semibold">Overtime Amount</label>
                                        <div class="input-group">
                                            <span class="input-group-text">₹</span>
                                            <input type="number" step="0.01" name="overtime_amount" class="form-control" value="{{ old('overtime_amount', $payslip->overtime_amount) }}" required min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-semibold">Loss of Pay (LOP) Deduction</label>
                                        <div class="input-group">
                                            <span class="input-group-text">₹</span>
                                            <input type="number" step="0.01" name="lop_amount" class="form-control" value="{{ old('lop_amount', $payslip->lop_amount) }}" required min="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="feather-save me-1"></i>Save Changes & Recalculate
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
