@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Accountants.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Collect Fees</span>
                    </h5>
                </div>

                <div class="content-area-body">
                    <!-- 1. Search Form -->
                    <form method="GET" action="{{ route('admin.collect-fees') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Search Student (name / roll)" value="{{ request('search') }}">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">Search</button>
                            </div>
                        </div>
                    </form>

                    <!-- 2. Show matching students (if search) -->
                    @if ($students->isNotEmpty())
                        <div class="card stretch stretch-full mb-3">
                            <div class="card-body">
                                <h6 class="card-title">Select Student</h6>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Roll</th>
                                                <th>Admission</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($students as $s)
                                                <tr>
                                                    <td>{{ $s->first_name }} {{ $s->last_name }}</td>
                                                    <td>{{ $s->roll_number ?? 'N/A' }}</td>
                                                    <td>{{ $s->admission_number }}</td>
                                                    <td class="text-end">
                                                        <a href="{{ route('admin.collect-fees', ['student_id' => $s->id]) }}"
                                                            class="btn btn-sm btn-primary">
                                                            Select
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- 3. Student + Payment Form -->
                    @if ($student)
                        <div class="row">
                            <!-- Left: Student Info -->
                            <div class="col-xl-4">
                                <div class="card stretch stretch-full">
                                    <div class="card-body">
                                        <h6 class="card-title">Student Info</h6>
                                        <p><strong>Name:</strong> {{ $student->first_name }} {{ $student->last_name }}</p>
                                        <p><strong>Roll:</strong> {{ $student->roll_number ?? 'N/A' }}</p>
                                        <p><strong>Admission:</strong> {{ $student->admission_number }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Middle: Pending Fees -->
                            <div class="col-xl-4">
                                <div class="card stretch stretch-full">
                                    <div class="card-body">
                                        <h6 class="card-title">Pending Fees</h6>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Select</th>
                                                        <th>Month</th>
                                                        <th>Fee Type</th>
                                                        <th>Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($installments as $inst)
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" name="installment_ids[]"
                                                                    value="{{ $inst->id }}" class="form-check-input">
                                                            </td>
                                                            <td>{{ $inst->due_date->format('M Y') }}</td>
                                                            <td>{{ $inst->feeCategory?->name ?? 'N/A' }}</td>
                                                            <td>{{ number_format($inst->amount, 2) }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="4" class="text-center text-muted">
                                                                No pending dues.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Payment Details -->
                            <div class="col-xl-4">
                                <div class="card stretch stretch-full">
                                    <div class="card-body">
                                        <h6 class="card-title">Payment Details</h6>
                                        <form method="POST" action="{{ route('admin.collect-fees') }}" onsubmit="return validateInstallments()">
                                            @csrf
                                            <input type="hidden" name="student_id" value="{{ $student->id }}">

                                            <div class="mb-3">
                                                <label class="form-label">Payment Method</label>
                                                <select name="payment_method" class="form-select" required>
                                                    <option value="cash">Cash</option>
                                                    <option value="online">Online</option>
                                                    <option value="bank_transfer">Bank Transfer</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Amount Paid</label>
                                                <input type="number" step="0.01" name="amount_paid" class="form-control"
                                                    required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Payment Date</label>
                                                <input type="date" name="payment_date" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Receipt Number</label>
                                                <input type="text" name="receipt_number" class="form-control" required>
                                            </div>
                                            <button type="submit" class="btn btn-primary">
                                                Collect Payment
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function validateInstallments() {
            const checked = document.querySelectorAll('input[name="installment_ids[]"]:checked');
            if (checked.length === 0) {
                alert('Please select at least one installment.');
                return false;
            }
            return true;
        }
    </script>
@endsection
