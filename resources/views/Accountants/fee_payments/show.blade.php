@extends('admin_layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Accountants.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">
            <div class="content-area-header bg-white sticky-top">
                <div class="d-flex align-items-center">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Payment Receipt #{{ $feePayment->receipt_number }}</span>
                    </h5>
                    <div class="ms-auto">
                        <a href="" class="btn btn-secondary me-2">
                            <i class="feather-arrow-left me-2"></i>Back to List
                        </a>
                        <a href="{{ route('admin.fee_payments.receipt', $feePayment) }}" class="btn btn-primary" target="_blank">
                            <i class="feather-printer me-2"></i>Download PDF
                        </a>
                    </div>
                </div>
            </div>

            <div class="content-area-body">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="card stretch stretch-full">
                            <div class="card-body p-0">
                                <div class="p-5">
                                    <!-- Header -->
                                    <div class="text-center mb-5">
                                        <h2 class="fw-bold text-primary mb-2">{{ config('app.name', 'Your School') }}</h2>
                                        <p class="mb-1">{{ Auth::user()->tenant->name ?? 'School Name' }}</p>
                                        <p class="small text-muted mb-4">Receipt Generated: {{ now()->format('d M Y H:i') }}</p>
                                        
                                        <div class="row g-0 justify-content-center">
                                            <div class="col-auto">
                                                <span class="badge bg-success fs-5 px-4 py-3">{{ strtoupper($feePayment->payment_method) }} RECEIPT</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Details -->
                                    <div class="row mb-5">
                                        <div class="col-md-6">
                                            <h6 class="fw-bold mb-3 border-bottom pb-2">Student Information</h6>
                                            <div class="ps-3">
                                                <p class="mb-1"><strong>Name:</strong> {{ $feePayment->student->first_name ?? '' }} {{ $feePayment->student->last_name ?? '' }}</p>
                                                <p class="mb-1"><strong>Roll No:</strong> {{ $feePayment->student->roll_no ?? $feePayment->student->roll_number ?? 'N/A' }}</p>
                                                <p class="mb-0"><strong>Receipt No:</strong> <span class="text-primary">#{{ $feePayment->receipt_number }}</span></p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="fw-bold mb-3 border-bottom pb-2">Payment Information</h6>
                                            <div class="ps-3">
                                                <p class="mb-1"><strong>Date:</strong> {{ $feePayment->payment_date->format('d M Y') }}</p>
                                                <p class="mb-1"><strong>Amount:</strong> <span class="fs-4 fw-bold text-success">₹{{ number_format($feePayment->amount_paid, 2) }}</span></p>
                                                @if($feePayment->transaction_id)
                                                    <p class="mb-1"><strong>Transaction ID:</strong> {{ $feePayment->transaction_id }}</p>
                                                @endif
                                                @if($feePayment->gateway_name)
                                                    <p class="mb-0"><strong>Bank/Gateway:</strong> {{ $feePayment->gateway_name }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Installment Details -->
                                    <div class="mb-5">
                                        <h6 class="fw-bold mb-3 border-bottom pb-2">Installment Paid</h6>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Fee Category</th>
                                                        <th>Amount Paid</th>
                                                        <th>Due Date</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if($feePayment->installment)
                                                        <tr>
                                                            <td>{{ $feePayment->installment->feeCategory->name ?? 'General Fee' }}</td>
                                                            <td class="fw-bold text-success">₹{{ number_format($feePayment->installment->amount, 2) }}</td>
                                                            <td>{{ $feePayment->installment->due_date->format('d M Y') }}</td>
                                                            <td><span class="badge bg-success">PAID</span></td>
                                                        </tr>
                                                    @else
                                                        <tr>
                                                            <td colspan="4" class="text-center text-muted">Installment details not available</td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                                <tfoot>
                                                    <tr class="table-primary">
                                                        <th colspan="3" class="text-end">TOTAL AMOUNT:</th>
                                                        <th class="fw-bold fs-4 text-success">₹{{ number_format($feePayment->amount_paid, 2) }}</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Footer -->
                                    <div class="text-center mt-5 pt-4 border-top">
                                        <div class="mb-3">
                                            <p class="fs-5 fw-bold text-primary mb-2">Thank you for your payment!</p>
                                            <p class="text-muted">This receipt serves as official payment confirmation</p>
                                        </div>
                                        <div class="small text-muted">
                                            <p>This is a computer-generated receipt. No signature required.</p>
                                            <p>Generated on {{ now()->format('d M Y H:i:s') }}</p>
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
</div>
@endsection