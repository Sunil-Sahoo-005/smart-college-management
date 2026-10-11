@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Accountants.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Collect Fee Payment</span>
                    </h5>
                </div>

                <div class="content-area-body">
                    <div class="row g-4">
                        <!-- LEFT COLUMN: Filters -->
                        <div class="col-lg-4">
                            <div class="card stretch stretch-full h-100">
                                <div class="card-body">
                                    <h6 class="card-title mb-4">Select Student</h6>
                                    
                                    <form method="GET" action="{{ route('admin.payments.collect') }}">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form-label">Branch (Stream)</label>
                                                <select name="stream_id" class="form-select" onchange="this.form.submit()">
                                                    <option value="">Select Branch</option>
                                                    @foreach ($streams as $stream)
                                                        <option value="{{ $stream->id }}" {{ request('stream_id') == $stream->id ? 'selected' : '' }}>
                                                            {{ $stream->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label">Academic Year</label>
                                                <select name="academic_year_id" class="form-select" onchange="this.form.submit()">
                                                    <option value="">Select Year</option>
                                                    @foreach ($academicYears as $year)
                                                        <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                                                            {{ $year->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            @if ($students->isNotEmpty())
                                                <div class="col-12">
                                                    <label class="form-label">Student</label>
                                                    <select name="student_id" class="form-select" onchange="this.form.submit()">
                                                        <option value="">Select Student</option>
                                                        @foreach ($students as $s)
                                                            <option value="{{ $s->id }}" {{ request('student_id') == $s->id ? 'selected' : '' }}>
                                                                {{ $s->first_name }} {{ $s->last_name }} ({{ $s->roll_number ?? 'N/A' }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT COLUMN: Installments + Payment -->
                        <div class="col-lg-8">
                            @if ($installments->isNotEmpty())
                                <form method="POST" action="{{ route('admin.payments.collect.store') }}">
                                    @csrf
                                    <input type="hidden" name="student_id" value="{{ request('student_id') }}">
                                    <input type="hidden" name="stream_id" value="{{ request('stream_id') }}">
                                    <input type="hidden" name="academic_year_id" value="{{ request('academic_year_id') }}">

                                    <!-- Outstanding Installments -->
                                    <div class="card stretch stretch-full mb-4">
                                        <div class="card-body p-0">
                                            <div class="px-3 pt-3 d-flex justify-content-between align-items-center">
                                                <h6 class="card-title mb-0">Outstanding Installments ({{ $installments->whereIn('status', ['unpaid', 'partial'])->count() }})</h6>
                                                <div class="form-check">
                                                    <input type="checkbox" id="select-all" class="form-check-input">
                                                    <label class="form-check-label" for="select-all">Select All</label>
                                                </div>
                                            </div>

                                            @foreach ($installments->whereIn('status', ['unpaid', 'partial']) as $installment)
                                                <div class="border-bottom px-3 py-3">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div class="form-check me-3">
                                                            <input type="checkbox"
                                                                   name="installment_ids[]"
                                                                   value="{{ $installment->id }}"
                                                                   class="form-check-input installment-checkbox mt-1"
                                                                   data-amount="{{ $installment->amount }}">
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1">{{ $installment->feeCategory?->name ?? 'N/A' }}</h6>
                                                            <div class="row g-2 small text-muted">
                                                                <div class="col-6">
                                                                    <strong>Due:</strong> {{ $installment->due_date }}
                                                                </div>
                                                                <div class="col-6 text-end">
                                                                    <strong>₹{{ number_format($installment->amount, 0) }}</strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <span class="badge bg-{{ $installment->status === 'partial' ? 'warning' : 'danger' }} ms-2">
                                                            {{ ucfirst($installment->status) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Payment Form -->
                                    <div class="card stretch stretch-full">
                                        <div class="card-body">
                                            <h6 class="card-title mb-4">Payment Details</h6>
                                            
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label class="form-label">Total Selected</label>
                                                    <input type="text" id="total-amount" class="form-control fw-bold fs-5 text-success" value="₹0" readonly>
                                                    <input type="hidden" name="amount_paid" id="amount-paid">
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label">Payment Method</label>
                                                    <select name="payment_method" id="payment_method" class="form-select" required>
                                                        <option value="cash">Cash</option>
                                                        <option value="online">Online</option>
                                                        <option value="bank_transfer">Bank Transfer</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label">Payment Date</label>
                                                    <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                                </div>

                                                <!-- Bank Account Selection (shown only when bank transfer is selected) -->
                                                <div class="col-md-12" id="bank-account-section" style="display: none;">
                                                    <label class="form-label">Select Bank Account</label>
                                                    <select name="bank_account_id" id="bank_account_id" class="form-select">
                                                        <option value="">Select Bank Account</option>
                                                        @if(isset($bankAccounts) && $bankAccounts->isNotEmpty())
                                                            @foreach($bankAccounts as $bank)
                                                                <option value="{{ $bank->id }}">
                                                                    {{ $bank->bank_name }} - {{ $bank->account_number }} 
                                                                    @if($bank->ifsc_code) (IFSC: {{ $bank->ifsc_code }}) @endif
                                                                </option>
                                                            @endforeach
                                                        @else
                                                            <option value="" disabled>No active bank accounts found. Please add bank accounts first.</option>
                                                        @endif
                                                    </select>
                                                    {{-- <small class="text-muted text-danger">* Select the bank account where this payment will be deposited</small> --}}
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Receipt Number</label>
                                                    <input type="text" name="receipt_number" class="form-control" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Transaction ID</label>
                                                    <input type="text" name="transaction_id" class="form-control">
                                                </div>

                                                <!-- Gateway Name (will be auto-filled from bank account selection) -->
                                                <div class="col-md-12" id="gateway-name-section" style="display: none;">
                                                    <label class="form-label">Gateway / Bank Name</label>
                                                    <input type="text" name="gateway_name" id="gateway_name" class="form-control" readonly>
                                                    <small class="text-muted">This will be auto-filled when you select a bank account</small>
                                                </div>
                                            </div>

                                            <div class="mt-4">
                                                <button type="submit" class="btn btn-primary btn-lg px-5" id="pay-btn" disabled>
                                                    <i class="fas fa-credit-card me-2"></i>Record Payment
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Paid Installments -->
                                    @if ($paidInstallments->isNotEmpty())
                                        <div class="card stretch stretch-full mt-4">
                                            <div class="card-body p-0">
                                                <h6 class="card-title px-3 pt-3">Paid Installments ({{ $paidInstallments->count() }})</h6>

                                                @foreach ($paidInstallments as $installment)
                                                    <div class="border-bottom px-3 py-3">
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div>
                                                                <h6 class="mb-1">{{ $installment->feeCategory?->name ?? 'N/A' }}</h6>
                                                                <div class="row g-2 small text-muted">
                                                                    <div class="col-6">
                                                                        <strong>Due:</strong> {{ $installment->due_date }}
                                                                    </div>
                                                                    <div class="col-6 text-end">
                                                                        <strong>₹{{ number_format($installment->amount, 0) }}</strong>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <span class="badge bg-success">
                                                                Paid on {{ $installment->updated_at->format('d M Y') }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </form>
                                
                            @else
                                <div class="card stretch stretch-full h-100">
                                    <div class="card-body text-center py-5">
                                        <i class="fas fa-search display-1 text-muted mb-3"></i>
                                        <h5 class="text-muted">Select Branch → Year → Student</h5>
                                        <p class="text-muted">to view installments and record payments</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateTotal() {
            let total = 0;
            document.querySelectorAll('.installment-checkbox:checked').forEach(cb => {
                total += parseFloat(cb.dataset.amount);
            });

            const formatted = '₹' + total.toLocaleString('en-IN', { maximumFractionDigits: 0 });
            document.getElementById('total-amount').value = formatted;
            document.getElementById('amount-paid').value = total;

            const payBtn = document.getElementById('pay-btn');
            payBtn.disabled = total === 0;
            if (total > 0) {
                payBtn.classList.remove('btn-primary');
                payBtn.classList.add('btn-success');
            } else {
                payBtn.classList.remove('btn-success');
                payBtn.classList.add('btn-primary');
            }
        }

        // Handle payment method change
        document.addEventListener('DOMContentLoaded', function() {
            const paymentMethod = document.getElementById('payment_method');
            const bankSection = document.getElementById('bank-account-section');
            const gatewaySection = document.getElementById('gateway-name-section');
            const bankAccountSelect = document.getElementById('bank_account_id');
            const gatewayNameInput = document.getElementById('gateway_name');
            
            if (paymentMethod) {
                paymentMethod.addEventListener('change', function() {
                    if (this.value === 'bank_transfer') {
                        bankSection.style.display = 'block';
                        gatewaySection.style.display = 'block';
                        if (bankAccountSelect) {
                            bankAccountSelect.required = true;
                        }
                        // Check if bank account is selected and update gateway name
                        if (bankAccountSelect && bankAccountSelect.value) {
                            const selectedOption = bankAccountSelect.options[bankAccountSelect.selectedIndex];
                            if (selectedOption && selectedOption.value) {
                                gatewayNameInput.value = selectedOption.text;
                            }
                        }
                    } else {
                        bankSection.style.display = 'none';
                        gatewaySection.style.display = 'none';
                        if (bankAccountSelect) {
                            bankAccountSelect.required = false;
                        }
                        gatewayNameInput.value = '';
                    }
                });
            }
            
            // Handle bank account selection
            if (bankAccountSelect) {
                bankAccountSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption && selectedOption.value) {
                        // Set gateway name to bank account details
                        gatewayNameInput.value = selectedOption.text;
                    } else {
                        gatewayNameInput.value = '';
                    }
                });
            }
            
            // Select All functionality
            const selectAll = document.getElementById('select-all');
            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    document.querySelectorAll('.installment-checkbox').forEach(cb => {
                        cb.checked = this.checked;
                    });
                    updateTotal();
                });
            }
            
            // Installment checkbox change handler
            document.querySelectorAll('.installment-checkbox').forEach(cb => {
                cb.addEventListener('change', updateTotal);
            });
            
            // Initial check for payment method
            if (paymentMethod && paymentMethod.value === 'bank_transfer') {
                const event = new Event('change');
                paymentMethod.dispatchEvent(event);
            }
            
            updateTotal();
        });
        
        // Form validation before submit
        document.querySelector('form[action="{{ route('admin.payments.collect.store') }}"]')?.addEventListener('submit', function(e) {
            const paymentMethod = document.getElementById('payment_method').value;
            const bankAccountSelect = document.getElementById('bank_account_id');
            
            if (paymentMethod === 'bank_transfer') {
                if (!bankAccountSelect || !bankAccountSelect.value) {
                    e.preventDefault();
                    alert('Please select a bank account for bank transfer payment.');
                    return false;
                }
            }
            
            const selectedInstallments = document.querySelectorAll('.installment-checkbox:checked');
            if (selectedInstallments.length === 0) {
                e.preventDefault();
                alert('Please select at least one installment to pay.');
                return false;
            }
            
            return true;
        });
    </script>
@endsection