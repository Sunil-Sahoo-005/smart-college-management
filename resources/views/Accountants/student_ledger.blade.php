@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Accountants.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top px-4">
                    <div class="page-header-left d-flex align-items-center">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Student Ledger</h5>
                        </div>
                        <ul class="breadcrumb fs-12 text-muted mb-0 ms-3">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item">Student Account Summary</li>
                        </ul>
                    </div>
                </div>

                <div class="content-area-body px-4">
            
            <div class="row">
                <!-- Search Section -->
                <div class="col-12">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body py-4">
                            <form action="{{ route('admin.student-ledger.index') }}" method="GET"
                                class="row align-items-end g-3">
                                <div class="col-md-9 col-lg-7">
                                    <label for="admission_no"
                                        class="form-label fw-bold small text-uppercase text-muted">Enter Admission
                                        Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0 text-secondary">
                                            <i class="feather-user"></i>
                                        </span>
                                        <input type="text" name="admission_no" id="admission_no"
                                            class="form-control form-control-lg border-start-0 ps-0"
                                            placeholder="e.g. ST/2026/000001" value="{{ $admissionNo ?? '' }}" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-2">
                                    <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm"
                                        style="height: 52px">
                                        <i class="feather-search me-2"></i>Search
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                @if (isset($student) && $student)
                    <!-- Student Profile Card -->
                    <div class="col-xl-4 col-lg-5">
                        <div class="card h-100 shadow-sm overflow-hidden border-0">
                            <div class="card-body p-0">
                                <!-- Premium Header Gradient -->
                                <div class="profile-header bg-primary py-5 px-4 text-center text-white"
                                    style="background: linear-gradient(135deg, #4481eb 0%, #04befe 100%);">
                                    <div class="mb-4">
                                        @if ($student->photo_url)
                                            <img src="{{ asset('storage/' . $student->photo_url) }}" alt="Photo"
                                                class="rounded-circle border border-4 border-white shadow"
                                                style="width: 140px; height: 140px; object-fit: cover;">
                                        @else
                                            <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center border border-4 border-white shadow text-primary"
                                                style="width: 140px; height: 140px;">
                                                <i class="feather-user fs-1" style="font-size: 60px !important;"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <h4 class="mb-1 fw-bold text-white">{{ $student->first_name }} {{ $student->last_name }}
                                    </h4>
                                    <p class="mb-0 text-white-50 fs-13">{{ $student->admission_number }}</p>
                                    <div class="mt-3">
                                        <span
                                            class="badge bg-white text-primary px-3 py-2 rounded-pill fw-bold text-uppercase shadow-sm"
                                            style="font-size: 11px;">
                                            <i class="feather-check-circle me-1"></i> {{ ucfirst($student->status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <div class="mb-4">
                                        <h6 class="text-uppercase fs-12 fw-bold text-muted mb-3">Academic Info</h6>
                                        <div class="d-flex align-items-center mb-3">
                                            <div
                                                class="avatar-xs flex-shrink-0 bg-soft-info text-info rounded me-3 d-flex align-items-center justify-content-center">
                                                <i class="feather-book-open"></i>
                                            </div>
                                            <div>
                                                <p class="mb-1 text-muted fs-12">Class & Section</p>
                                                <h6 class="mb-0 fw-semibold">
                                                    {{ $student->activeEnrollment->class->name ?? 'N/A' }} /
                                                    {{ $student->activeEnrollment->section->name ?? 'N/A' }}</h6>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-0">
                                            <div
                                                class="avatar-xs flex-shrink-0 bg-soft-warning text-warning rounded me-3 d-flex align-items-center justify-content-center">
                                                <i class="feather-git-merge"></i>
                                            </div>
                                            <div>
                                                <p class="mb-1 text-muted fs-12">Stream / Branch</p>
                                                <h6 class="mb-0 fw-semibold">
                                                    {{ $student->activeEnrollment->stream->name ?? 'N/A' }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="my-4 opacity-10">
                                    <div>
                                        <h6 class="text-uppercase fs-12 fw-bold text-muted mb-3">Personal Details</h6>
                                        <div class="row g-3">
                                            <div class="col-6">
                                                <p class="mb-1 text-muted fs-12">Roll Number</p>
                                                <h6 class="mb-0 fw-semibold">{{ $student->roll_number ?? 'N/A' }}</h6>
                                            </div>
                                            <div class="col-6">
                                                <p class="mb-1 text-muted fs-12">Category</p>
                                                <h6 class="mb-0 fw-semibold">{{ strtoupper($student->category ?? 'N/A') }}
                                                </h6>
                                            </div>
                                            <div class="col-12">
                                                <p class="mb-1 text-muted fs-12">Emergency Contact</p>
                                                <h6 class="mb-0 fw-semibold text-primary"><i
                                                        class="feather-phone-call me-1 fs-13"></i>
                                                    {{ $student->emergency_contact_phone ?? 'N/A' }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-light-soft border-0 p-3 pt-0">
                                <a href="{{ route('admin.students.show', $student->id) }}"
                                    class="btn btn-outline-primary w-100 btn-sm">
                                    <i class="feather-external-link me-2"></i>Full Details
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Summary and Tabs -->
                    <div class="col-xl-8 col-lg-7">
                        <!-- Statistics Widgets -->
                        <div class="row g-3 mb-4">
                            <div class="col-sm-4">
                                <div class="card border-0 shadow-sm h-100 overflow-hidden"
                                    style="background: #fff; border-left: 5px solid #4481eb !important;">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <p class="fs-12 text-muted text-uppercase mb-1 fw-bold">Total Fees</p>
                                                <h3 class="mb-0 fw-bold">₹{{ number_format($summary['total_amount'], 2) }}
                                                </h3>
                                            </div>
                                            <div
                                                class="avatar-sm bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="fs-4 feather-dollar-sign"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="card border-0 shadow-sm h-100 overflow-hidden"
                                    style="background: #fff; border-left: 5px solid #1ccab8 !important;">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <p class="fs-12 text-success text-uppercase mb-1 fw-bold">Paid Amount</p>
                                                <h3 class="mb-0 fw-bold text-success">
                                                    ₹{{ number_format($summary['paid_amount'], 2) }}</h3>
                                            </div>
                                            <div
                                                class="avatar-sm bg-soft-success text-success rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="fs-4 feather-check-circle"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="card border-0 shadow-sm h-100 overflow-hidden"
                                    style="background: #fff; border-left: 5px solid #ff5c75 !important;">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <p class="fs-12 text-danger text-uppercase mb-1 fw-bold">Pending balance
                                                </p>
                                                <h3 class="mb-0 fw-bold text-danger">
                                                    ₹{{ number_format($summary['pending_amount'], 2) }}</h3>
                                            </div>
                                            <div
                                                class="avatar-sm bg-soft-danger text-danger rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="fs-4 feather-alert-circle"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="card border-0 shadow-sm h-100 overflow-hidden"
                                    style="background: #fff; border-left: 5px solid #0ab39c !important;">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <p class="fs-12 text-uppercase mb-1 fw-bold" style="color: #0ab39c;">Scholarship Balance</p>
                                                <h3 class="mb-0 fw-bold" style="color: #0ab39c;">
                                                    ₹{{ number_format($summary['scholarship_balance'] ?? 0, 2) }}
                                                </h3>
                                            </div>
                                            <div class="avatar-sm bg-soft-info text-info rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="fs-4 feather-award"></i>
                                            </div>
                                        </div>
                                        <div class="mt-2 fs-12 text-muted">
                                            Approved: ₹{{ number_format($summary['scholarship_approved'] ?? 0, 2) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabs for detailed info -->
                        <div class="card border-0 shadow-sm overflow-hidden">
                            <div class="card-header border-bottom p-0 bg-white">
                                <ul class="nav nav-tabs nav-tabs-custom nav-primary nav-justified border-bottom-0"
                                    role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active py-3 fw-bold" data-bs-toggle="tab"
                                            href="#installments-tab" role="tab">
                                            <i class="feather-list me-2"></i>Installments Ledger
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link py-3 fw-bold" data-bs-toggle="tab" href="#structures-tab"
                                            role="tab">
                                            <i class="feather-layers me-2"></i>Fee Structures
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link py-3 fw-bold" data-bs-toggle="tab" href="#promotions-tab"
                                            role="tab">
                                            <i class="feather-trending-up me-2"></i>Promotions
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link py-3 fw-bold" data-bs-toggle="tab" href="#scholarships-tab"
                                            role="tab">
                                            <i class="feather-award me-2"></i>Scholarships
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body px-0 pt-0 bg-white">
                                <div class="tab-content text-muted p-0">
                                    <!-- Installments Tab -->
                                    <div class="tab-pane active" id="installments-tab" role="tabpanel">
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead class="bg-light-soft text-muted border-bottom">
                                                    <tr class="fs-12 text-uppercase">
                                                        <th class="ps-4 py-3">Fee Concept</th>
                                                        <th class="py-3">Due Date</th>
                                                        <th class="py-3">Amount</th>
                                                        <th class="py-3">Status</th>
                                                        <th class="text-end pe-4 py-3">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($student->feeInstallments as $installment)
                                                        <tr>
                                                            <td class="ps-4">
                                                                <div class="d-flex align-items-center">
                                                                    <div
                                                                        class="avatar-xs flex-shrink-0 bg-soft-info text-info rounded me-3 d-flex align-items-center justify-content-center fw-bold fs-10">
                                                                        {{ substr($installment->feeCategory->name, 0, 2) }}
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <h6 class="mb-0 fs-14 fw-semibold text-dark">
                                                                            {{ $installment->feeCategory->name }}</h6>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="fs-13">
                                                                {{ $installment->due_date->format('d M, Y') }}
                                                                @if ($installment->due_date < now() && $installment->status != 'paid')
                                                                    <span class="text-danger ms-1"
                                                                        data-bs-toggle="tooltip" title="Overdue">
                                                                        <i class="feather-alert-triangle fs-12"></i>
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            <td class="fw-bold text-dark">
                                                                ₹{{ number_format($installment->amount, 2) }}</td>
                                                            <td>
                                                                @if ($installment->status == 'paid')
                                                                    <span
                                                                        class="badge bg-soft-success text-success px-3 py-1 rounded-pill fw-medium">Paid</span>
                                                                @elseif($installment->status == 'partial')
                                                                    <span
                                                                        class="badge bg-soft-warning text-warning px-3 py-1 rounded-pill fw-medium">Partial</span>
                                                                @else
                                                                    <span
                                                                        class="badge bg-soft-danger text-danger px-3 py-1 rounded-pill fw-medium">Unpaid</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-end pe-4">
                                                                @if ($installment->status != 'paid')
                                                                    <a href="{{ route('admin.payments.collect', [
                                                                        'student_id' => $student->id,
                                                                        'stream_id' => $student->activeEnrollment->stream_id ?? '',
                                                                        'academic_year_id' => $student->activeEnrollment->academic_year_id ?? '',
                                                                    ]) }}"
                                                                        class="btn btn-sm btn-primary px-3 shadow-none">
                                                                        <i class="feather-credit-card me-1 fs-12"></i> Pay
                                                                    </a>
                                                                @else
                                                                    <a href="{{ route('admin.fee_payments.receipt', $installment->latestPayment->id ?? 0) }}"
                                                                        class="btn btn-sm btn-soft-info px-3"
                                                                        target="_blank">
                                                                        <i class="feather-printer me-1 fs-12"></i> Receipt
                                                                    </a>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center py-5">
                                                                <div class="py-3">
                                                                    <img src="{{ asset('assets/images/empty.svg') }}"
                                                                        alt="Empty" class="img-fluid mb-3"
                                                                        style="max-width: 150px; opacity: 0.5;">
                                                                    <p class="text-muted mb-0">No installments generated
                                                                        for this student yet.</p>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Fee Structures Tab -->
                                    <div class="tab-pane" id="structures-tab" role="tabpanel">
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead class="bg-light-soft text-muted border-bottom">
                                                    <tr class="fs-12 text-uppercase">
                                                        <th class="ps-4 py-3">Fee Structure Title</th>
                                                        <th class="py-3">Session</th>
                                                        <th class="py-3">Valid Range</th>
                                                        <th class="py-3">Current Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($feeStructures as $sf)
                                                        <tr>
                                                            <td class="ps-4">
                                                                <h6 class="mb-0 fw-semibold text-dark">
                                                                    {{ $sf->feeStructure->name }}</h6>
                                                            </td>
                                                            <td class="fs-13 text-muted">{{ $sf->academicYear->name }}
                                                            </td>
                                                            <td class="fs-13 text-muted">
                                                                <span
                                                                    class="text-dark">{{ $sf->start_date ? $sf->start_date->format('M Y') : '-' }}</span>
                                                                to
                                                                <span
                                                                    class="text-dark">{{ $sf->end_date ? $sf->end_date->format('M Y') : '-' }}</span>
                                                            </td>
                                                            <td>
                                                                @if ($sf->status == 'generated')
                                                                    <span
                                                                        class="badge bg-soft-success text-success px-2 py-1 rounded border border-success-20">
                                                                        <i class="feather-check-circle me-1"></i> Generated
                                                                    </span>
                                                                @else
                                                                    <div class="d-flex align-items-center gap-2">
                                                                        <span
                                                                            class="badge bg-soft-secondary text-secondary px-2 py-1 rounded border border-secondary-20 text-uppercase fs-10">Assigned</span>
                                                                        <form
                                                                            action="{{ route('admin.student-fee-assignments.generate', $sf->id) }}"
                                                                            method="POST" class="d-inline">
                                                                            @csrf
                                                                            <button type="submit"
                                                                                class="btn btn-sm btn-primary shadow-none py-1 px-2"
                                                                                style="font-size: 10px">
                                                                                <i class="feather-play me-1"></i> Generate
                                                                            </button>
                                                                        </form>
                                                                    </div>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="4" class="text-center py-5">
                                                                <p class="text-muted mb-0">No active fee structure
                                                                    assignments found.</p>
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Promotions Tab -->
                                    <div class="tab-pane" id="promotions-tab" role="tabpanel">
                                        <div class="p-4">
                                            <div
                                                class="vertical-timeline position-relative ms-3 border-start border-2 border-primary-20">
                                                @forelse($promotions as $promo)
                                                    <div class="timeline-item position-relative mb-5 ps-4">
                                                        <div class="timeline-dot position-absolute bg-primary rounded-circle border border-4 border-white shadow-sm"
                                                            style="left: -13px; top: 0; width: 24px; height: 24px;"></div>
                                                        <div
                                                            class="timeline-card p-3 border rounded shadow-sm bg-light-soft">
                                                            <div
                                                                class="d-flex justify-content-between align-items-center mb-2">
                                                                <h6 class="fw-bold mb-0 text-dark">
                                                                    {{ $promo->academicYear->name }}</h6>
                                                                <span
                                                                    class="badge bg-primary text-white fs-10 px-2 rounded">PROMOTED</span>
                                                            </div>
                                                            <div class="fs-13">
                                                                <div class="row g-2">
                                                                    <div class="col-sm-5">
                                                                        <p
                                                                            class="mb-0 text-muted fs-11 text-uppercase fw-bold">
                                                                            From</p>
                                                                        <p class="mb-0 fw-medium text-dark">
                                                                            {{ $promo->from_full }}</p>
                                                                    </div>
                                                                    <div class="col-sm-2 text-center align-self-center">
                                                                        <i
                                                                            class="feather-chevron-right fs-4 text-primary opacity-50"></i>
                                                                    </div>
                                                                    <div class="col-sm-5 text-sm-end">
                                                                        <p
                                                                            class="mb-0 text-muted fs-11 text-uppercase fw-bold">
                                                                            To</p>
                                                                        <p class="mb-0 fw-bold text-primary">
                                                                            {{ $promo->to_full }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div
                                                                class="mt-3 pt-2 border-top border-light d-flex justify-content-between">
                                                                <p class="mb-0 fs-11 text-muted"><i
                                                                        class="feather-calendar me-1"></i>
                                                                    {{ $promo->created_at->format('d M, Y \a\t h:i A') }}
                                                                </p>
                                                                @if ($promo->remarks)
                                                                    <p class="mb-0 fs-11 text-muted fw-italic">
                                                                        {{ $promo->remarks }}</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="text-center py-4">
                                                        <i class="feather-trending-up fs-1 mb-2 text-muted-50"></i>
                                                        <p class="text-muted mb-0">No promotion history recorded yet.</p>
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Scholarships Tab -->
                                    <div class="tab-pane" id="scholarships-tab" role="tabpanel">
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead class="bg-light-soft text-muted border-bottom">
                                                    <tr class="fs-12 text-uppercase">
                                                        <th class="ps-4 py-3">Scholarship</th>
                                                        <th class="py-3">Applied On</th>
                                                        <th class="py-3">Approved</th>
                                                        <th class="py-3">Used</th>
                                                        <th class="py-3">Balance</th>
                                                        <th class="py-3">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($scholarships as $studentScholarship)
                                                        <tr>
                                                            <td class="ps-4">
                                                                <h6 class="mb-0 fw-semibold text-dark">
                                                                    {{ $studentScholarship->scholarship->name ?? 'N/A' }}
                                                                </h6>
                                                                <div class="fs-12 text-muted">
                                                                    {{ ucfirst($studentScholarship->scholarship->type ?? 'N/A') }}
                                                                </div>
                                                            </td>
                                                            <td class="fs-13 text-muted">
                                                                {{ optional($studentScholarship->applied_on)->format('d M, Y') ?? '-' }}
                                                            </td>
                                                            <td class="fw-bold text-dark">
                                                                ₹{{ number_format($studentScholarship->approved_amount, 2) }}
                                                            </td>
                                                            <td class="fw-bold text-danger">
                                                                ₹{{ number_format($studentScholarship->used_amount, 2) }}
                                                            </td>
                                                            <td class="fw-bold text-success">
                                                                ₹{{ number_format($studentScholarship->balance, 2) }}
                                                            </td>
                                                            <td>
                                                                @php
                                                                    $scholarshipStatusMap = [
                                                                        'pending' => 'bg-soft-warning text-warning',
                                                                        'approved' => 'bg-soft-success text-success',
                                                                        'rejected' => 'bg-soft-danger text-danger',
                                                                        'completed' => 'bg-soft-info text-info',
                                                                    ];
                                                                    $scholarshipBadge = $scholarshipStatusMap[$studentScholarship->status] ?? 'bg-soft-secondary text-secondary';
                                                                @endphp
                                                                <span class="badge {{ $scholarshipBadge }}">
                                                                    {{ ucfirst($studentScholarship->status) }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        @if($studentScholarship->transactions->isNotEmpty())
                                                            <tr>
                                                                <td colspan="6" class="ps-4 pb-4 pt-0">
                                                                    <div class="ms-2 mt-2 p-3 rounded border bg-light-soft">
                                                                        <div class="fs-12 fw-bold text-uppercase text-muted mb-2">
                                                                            Transactions
                                                                        </div>
                                                                        <div class="d-flex flex-wrap gap-2">
                                                                            @foreach($studentScholarship->transactions as $transaction)
                                                                                <span class="badge bg-white text-dark border">
                                                                                    ₹{{ number_format($transaction->amount_used, 2) }}
                                                                                    @if($transaction->installment_id)
                                                                                        | Inst #{{ $transaction->installment_id }}
                                                                                    @endif
                                                                                </span>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @empty
                                                        <tr>
                                                            <td colspan="6" class="text-center py-5">
                                                                <p class="text-muted mb-0">No scholarship records found for this student.</p>
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
                @elseif(isset($admissionNo) && $admissionNo)
                    <!-- No results found logic handled by redirect in controller, but as a fallback -->
                    <div class="col-12 py-5 text-center">
                        <div class="card border-0 shadow-sm py-5 mx-auto" style="max-width: 600px;">
                            <div class="card-body">
                                <div class="mb-4">
                                    <span
                                        class="avatar-lg bg-soft-danger text-danger rounded-circle d-inline-flex align-items-center justify-content-center"
                                        style="width: 100px; height: 100px;">
                                        <i class="feather-user-x fs-1"></i>
                                    </span>
                                </div>
                                <h3 class="fw-bold text-dark">Student Not Found</h3>
                                <p class="text-muted fs-16 mb-4">We searched the records but couldn't find a student with
                                    admission number <strong>"{{ $admissionNo }}"</strong>. Please check for typos and try
                                    again.</p>
                                <a href="{{ route('admin.student-ledger.index') }}" class="btn btn-primary px-4">
                                    <i class="feather-arrow-left me-2"></i>Back to Search
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Initial Welcome State -->
                    <div class="col-12 py-5 text-center">
                        <div class="py-5">
                            <div class="mb-4">
                                <div class="avatar-lg bg-soft-primary text-primary rounded-circle d-inline-flex align-items-center justify-content-center shadow-lg"
                                    style="width: 120px; height: 120px; animation: pulse 2s infinite;">
                                    <i class="feather-search fs-1" style="font-size: 50px !important;"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold text-dark mb-2">Welcome to Student Ledger</h2>
                            <p class="text-muted fs-18 mx-auto" style="max-width: 600px;">Quickly access financial
                                snapshots, fee installments, and academic progress for any student. Perfect for quick
                                accountant lookups.</p>

                            <div class="mt-5 d-flex justify-content-center gap-4 flex-wrap">
                                <div class="text-center border rounded p-3 bg-white shadow-sm" style="min-width: 150px;">
                                    <i class="feather-credit-card fs-4 text-primary d-block mb-2"></i>
                                    <span class="fw-semibold text-dark">One-Click Payments</span>
                                </div>
                                <div class="text-center border rounded p-3 bg-white shadow-sm" style="min-width: 150px;">
                                    <i class="feather-file-text fs-4 text-success d-block mb-2"></i>
                                    <span class="fw-semibold text-dark">Instant Receipts</span>
                                </div>
                                <div class="text-center border rounded p-3 bg-white shadow-sm" style="min-width: 150px;">
                                    <i class="feather-trending-up fs-4 text-info d-block mb-2"></i>
                                    <span class="fw-semibold text-dark">Promotion History</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Page Styles -->
    <style>
        /* Premium Color Palette */
        :root {
            --nxl-primary: #3474ff;
            --nxl-success: #1ccab8;
            --nxl-danger: #ff5c75;
            --nxl-warning: #ffb822;
            --nxl-info: #0ab39c;
        }

        .bg-light-soft {
            background-color: #f7f9fc !important;
        }

        .bg-soft-primary {
            background-color: rgba(52, 116, 255, 0.08) !important;
            color: var(--nxl-primary);
        }

        .bg-soft-success {
            background-color: rgba(28, 202, 184, 0.08) !important;
            color: var(--nxl-success);
        }

        .bg-soft-danger {
            background-color: rgba(255, 92, 117, 0.08) !important;
            color: var(--nxl-danger);
        }

        .bg-soft-warning {
            background-color: rgba(255, 184, 34, 0.08) !important;
            color: var(--nxl-warning);
        }

        .bg-soft-info {
            background-color: rgba(10, 179, 156, 0.08) !important;
            color: var(--nxl-info);
        }

        .btn-soft-info {
            background-color: rgba(10, 179, 156, 0.1);
            color: #0ab39c;
            border: none;
        }

        .btn-soft-info:hover {
            background-color: #0ab39c;
            color: #fff;
        }

        /* Tabs Styling */
        .nav-tabs-custom .nav-link {
            border-bottom: 2px solid transparent;
            transition: all 0.3s;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
            color: #7d8ea2;
        }

        .nav-tabs-custom .nav-link.active {
            color: var(--nxl-primary);
            border-bottom: 2px solid var(--nxl-primary);
            background: transparent;
        }

        /* Avatar Sizing */
        .avatar-xs {
            height: 2rem;
            width: 2rem;
        }

        .avatar-sm {
            height: 3rem;
            width: 3rem;
        }

        .avatar-lg {
            height: 5rem;
            width: 5rem;
        }

        /* Timeline Animations */
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(52, 116, 255, 0.4);
            }

            70% {
                box-shadow: 0 0 0 20px rgba(52, 116, 255, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(52, 116, 255, 0);
            }
        }

        .primary-20 {
            color: rgba(52, 116, 255, 0.2);
        }

        .border-primary-20 {
            border-color: rgba(52, 116, 255, 0.1) !important;
        }

        .timeline-card {
            transition: transform 0.2s ease-in-out;
        }

        .timeline-card:hover {
            transform: translateX(5px);
        }

        .form-control-lg {
            font-size: 1rem;
            padding-top: 12px;
            padding-bottom: 12px;
        }
    </style>
@endsection
