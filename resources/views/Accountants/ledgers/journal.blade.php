@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Accountants.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Post Journal Entry</span>
                        <span class="fs-12 fw-normal text-muted">Total debits must always match total credits.</span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                            <a href="{{ route('ledgers.index') }}" class="btn btn-secondary">
                                <i class="feather-arrow-left me-2"></i>Back to Ledgers
                            </a>
                        </div>
                    </div>
                </div>

                <div class="content-area-body">
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('ledgers.journal.store') }}" method="POST" id="journalForm">
                        @csrf

                        <div class="card mb-4">
                            <div class="card-body p-3">
                                <label for="description" class="form-label fw-semibold">Narration</label>
                                <input type="text" id="description" name="description" class="form-control"
                                    value="{{ old('description') }}" placeholder="Correction entry, opening adjustment, etc." required>
                            </div>
                        </div>

                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <h6 class="mb-0">Journal Lines</h6>
                                    <div class="small">
                                        <span class="me-3">Debit: <strong class="text-success" id="totalDr">Rs 0.00</strong></span>
                                        <span class="me-3">Credit: <strong class="text-danger" id="totalCr">Rs 0.00</strong></span>
                                        <span id="diffLabel" class="text-muted">Balanced</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0" id="entryTable">
                                        <thead>
                                            <tr>
                                                <th style="width: 42%">Ledger Account</th>
                                                <th style="width: 18%">Type</th>
                                                <th style="width: 25%">Amount</th>
                                                <th style="width: 15%" class="text-center">Remove</th>
                                            </tr>
                                        </thead>
                                        <tbody id="entryRows">
                                            @for ($i = 0; $i < 2; $i++)
                                                <tr class="entry-row">
                                                    <td>
                                                        <select name="entries[{{ $i }}][ledger_id]" class="form-select form-select-sm" required>
                                                            <option value="">Select Ledger</option>
                                                            @foreach ($ledgers as $ledger)
                                                                <option value="{{ $ledger->id }}">
                                                                    {{ $ledger->name }} ({{ ucfirst($ledger->balance_type) }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="entries[{{ $i }}][type]" class="form-select form-select-sm type-select" required>
                                                            <option value="debit" {{ $i === 0 ? 'selected' : '' }}>Debit</option>
                                                            <option value="credit" {{ $i === 1 ? 'selected' : '' }}>Credit</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="entries[{{ $i }}][amount]" step="0.01" min="0.01"
                                                            class="form-control form-control-sm amount-input" placeholder="0.00" required>
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-sm btn-outline-danger remove-row" {{ $i < 2 ? 'disabled' : '' }}>
                                                            <i class="feather-trash-2"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endfor
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer bg-white d-flex justify-content-between">
                                <button type="button" id="addRow" class="btn btn-outline-primary btn-sm">
                                    <i class="feather-plus me-1"></i>Add Line
                                </button>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('ledgers.index') }}" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Post Entry</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let rowIndex = 2;
        const ledgerOptions = `{!! $ledgers->map(fn($ledger) => "<option value=\"{$ledger->id}\">{$ledger->name} (" . ucfirst($ledger->balance_type) . ")</option>")->implode('') !!}`;

        function recalcJournalTotals() {
            let debit = 0;
            let credit = 0;

            document.querySelectorAll('.entry-row').forEach((row) => {
                const type = row.querySelector('.type-select').value;
                const amount = parseFloat(row.querySelector('.amount-input').value) || 0;

                if (type === 'debit') debit += amount;
                if (type === 'credit') credit += amount;
            });

            document.getElementById('totalDr').textContent = 'Rs ' + debit.toFixed(2);
            document.getElementById('totalCr').textContent = 'Rs ' + credit.toFixed(2);

            const diff = Math.abs(debit - credit);
            const diffLabel = document.getElementById('diffLabel');

            if (diff < 0.005) {
                diffLabel.textContent = 'Balanced';
                diffLabel.className = 'text-success fw-semibold';
            } else {
                diffLabel.textContent = 'Difference: Rs ' + diff.toFixed(2);
                diffLabel.className = 'text-danger fw-semibold';
            }
        }

        function toggleRemoveButtons() {
            const rows = document.querySelectorAll('.entry-row');
            rows.forEach((row) => {
                row.querySelector('.remove-row').disabled = rows.length <= 2;
            });
        }

        document.getElementById('addRow').addEventListener('click', function() {
            const row = document.createElement('tr');
            row.className = 'entry-row';
            row.innerHTML = `
                <td>
                    <select name="entries[${rowIndex}][ledger_id]" class="form-select form-select-sm" required>
                        <option value="">Select Ledger</option>${ledgerOptions}
                    </select>
                </td>
                <td>
                    <select name="entries[${rowIndex}][type]" class="form-select form-select-sm type-select" required>
                        <option value="debit">Debit</option>
                        <option value="credit">Credit</option>
                    </select>
                </td>
                <td>
                    <input type="number" name="entries[${rowIndex}][amount]" step="0.01" min="0.01"
                        class="form-control form-control-sm amount-input" placeholder="0.00" required>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-row">
                        <i class="feather-trash-2"></i>
                    </button>
                </td>
            `;
            document.getElementById('entryRows').appendChild(row);
            rowIndex++;
            toggleRemoveButtons();
            recalcJournalTotals();
        });

        document.getElementById('entryRows').addEventListener('input', function(event) {
            if (event.target.classList.contains('type-select') || event.target.classList.contains('amount-input')) {
                recalcJournalTotals();
            }
        });

        document.getElementById('entryRows').addEventListener('click', function(event) {
            const removeButton = event.target.closest('.remove-row');
            if (removeButton && !removeButton.disabled) {
                removeButton.closest('tr').remove();
                toggleRemoveButtons();
                recalcJournalTotals();
            }
        });

        toggleRemoveButtons();
        recalcJournalTotals();
    </script>
@endpush
