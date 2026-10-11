@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Accountants.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Receipt History</span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                            @if (auth()->user()->hasPermissionTo('Create Fee Payment'))
                                <a href="{{ route('admin.payments.collect') }}" class="btn btn-primary">
                                    <i class="feather-plus me-2"></i>
                                    <span>Record Payment</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="content-area-body">
                    <form method="GET" class="mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <select name="student_id" class="form-select">
                                    <option value="">All Students</option>
                                    @foreach ($students as $student)
                                        <option value="{{ $student->id }}"
                                            {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                            {{ $student->first_name }} {{ $student->last_name }}
                                            ({{ $student->roll_no ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="date" name="payment_date" class="form-control"
                                    value="{{ request('payment_date') }}">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="receipt_number" class="form-control" placeholder="Receipt No."
                                    value="{{ request('receipt_number') }}">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </form>

                    @include('elements.flash')

                    <div class="card stretch stretch-full">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover" id="feePaymentList">
                                    <thead>
                                        <tr>
                                            <th>Receipt No.</th>
                                            <th>Student</th>
                                            <th>Method</th>
                                            <th>Amount</th>
                                            <th>Payment Date</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($payments as $payment)
                                            <tr class="single-item">
                                                <td class="project-name-td">
                                                    <div class="hstack gap-4">
                                                        <div>
                                                            @if(auth()->user()->hasPermissionTo('View Fee Payment'))
                                                                <a href="{{ route('admin.fee_payments.show', $payment->id) }}" 
                                                                   class="text-truncate-1-line">
                                                                    {{ $payment->receipt_number }}
                                                                </a>
                                                            @else
                                                                <span class="text-truncate-1-line">{{ $payment->receipt_number }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    {{ $payment->student?->first_name }}
                                                    {{ $payment->student?->last_name }}
                                                    ({{ $payment->student?->roll_no ?? 'N/A' }})
                                                </td>
                                                <td>
                                                    <span class="badge bg-soft-{{ $payment->payment_method == 'cash' ? 'success' : ($payment->payment_method == 'online' ? 'primary' : 'warning') }} text-{{ $payment->payment_method == 'cash' ? 'success' : ($payment->payment_method == 'online' ? 'primary' : 'warning') }}">
                                                        {{ ucfirst($payment->payment_method) }}
                                                    </span>
                                                </td>
                                                <td>{{ number_format($payment->amount_paid, 2) }}</td>
                                                <td>{{ $payment->payment_date->format('d M Y') }}</td>
                                                <td>
                                                    <div class="hstack gap-2 justify-content-center">
                                                        @if(auth()->user()->hasPermissionTo('View Fee Payment'))
                                                            <a href="{{ route('admin.fee_payments.receipt', $payment->id) }}"
                                                               class="avatar-text avatar-md" title="Download Receipt"
                                                               target="_blank">
                                                                <i class="feather-printer"></i>
                                                            </a>
                                                            <a href="{{ route('admin.fee_payments.show', $payment->id) }}"
                                                               class="avatar-text avatar-md text-primary" title="View Details">
                                                                <i class="feather-eye"></i>
                                                            </a>
                                                        @endif

                                                        {{-- Edit and Delete actions – uncomment if routes exist --}}
                                                        {{-- 
                                                        @if(auth()->user()->hasPermissionTo('Edit Fee Payment'))
                                                            <a href="{{ route('admin.fee_payments.edit', $payment->id) }}"
                                                               class="avatar-text avatar-md text-warning" title="Edit Payment">
                                                                <i class="feather-edit-3"></i>
                                                            </a>
                                                        @endif

                                                        @if(auth()->user()->hasPermissionTo('Delete Fee Payment'))
                                                            <form action="{{ route('admin.fee_payments.destroy', $payment->id) }}" 
                                                                  method="POST" class="d-inline"
                                                                  onsubmit="return confirm('Are you sure you want to delete this payment record?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" 
                                                                        class="avatar-text avatar-md text-danger border-0 bg-transparent"
                                                                        title="Delete Payment">
                                                                    <i class="feather-trash-2"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                        --}}
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">
                                                    No receipts found.
                                                    @if(auth()->user()->hasPermissionTo('Create  Fee Payment'))
                                                        <div class="mt-2">
                                                            <a href="{{ route('admin.payments.collect') }}" class="btn btn-sm btn-primary">
                                                                Record your first payment
                                                            </a>
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{ $payments->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection