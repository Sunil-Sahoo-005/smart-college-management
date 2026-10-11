@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Accountants.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Expense Records</span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                            @if (auth()->user()->hasPermissionTo('Create Expense Records'))
                                <a href="{{ route('expense-records.create') }}" class="btn btn-primary">
                                    <i class="feather-plus me-2"></i>Create Expense
                                </a>
                            @endif
                            @if (auth()->user()->hasPermissionTo('Export Expense Records'))
                                <button class="btn btn-success" onclick="exportSelected()">
                                    <i class="feather-download me-2"></i>Export Selected
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="content-area-body">
                    @include('elements.flash')
                    <div class="card mb-4">
                        <div class="card-body p-3">
                            <form method="GET" class="row g-3">
                                <div class="col-md-3">
                                    <select name="head_id" class="form-select">
                                        <option value="">All Categories</option>
                                        @foreach ($expenseHeads as $head)
                                            <option value="{{ $head->id }}"
                                                {{ request('head_id') == $head->id ? 'selected' : '' }}>
                                                {{ $head->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="date" name="date_from" class="form-control"
                                        value="{{ request('date_from') }}" max="{{ now()->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-3">
                                    <input type="date" name="date_to" class="form-control"
                                        value="{{ request('date_to') }}" max="{{ now()->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-outline-primary w-100">
                                        <i class="feather-filter me-1"></i>Filter
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card stretch stretch-full">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <form id="exportForm">
                                    <table class="table table-hover" id="expenseRecordList">
                                        <thead>
                                            <tr>
                                                @if(auth()->user()->hasPermissionTo('Export Expense Records'))
                                                    <th><input type="checkbox" id="selectAll"></th>
                                                @endif
                                                <th>Date</th>
                                                <th>Category</th>
                                                <th>Amount</th>
                                                <th>Description</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($expenseRecords as $record)
                                                <tr class="single-item">
                                                    @if(auth()->user()->hasPermissionTo('Export Expense Records'))
                                                        <td>
                                                            <input type="checkbox" name="ids[]" value="{{ $record->id }}"
                                                                class="record-checkbox">
                                                        </td>
                                                    @endif
                                                    <td>{{ $record->date->format('d M Y') }}</td>
                                                    <td>
                                                        <span class="badge bg-danger">{{ $record->expenseHead->name }}</span>
                                                    </td>
                                                    <td>
                                                        <strong>₹{{ number_format($record->amount, 2) }}</strong>
                                                    </td>
                                                    <td>{{ Str::limit($record->description, 50) }}</td>
                                                    <td>
                                                        <div class="hstack gap-2 justify-content-center">
                                                            @if(auth()->user()->hasPermissionTo('View Expense Records'))
                                                                <a href="{{ route('expense-records.receipt', $record) }}"
                                                                    class="avatar-text avatar-md text-primary"
                                                                    title="Download Receipt" target="_blank">
                                                                    <i class="feather-file-text"></i>
                                                                </a>
                                                            @endif

                                                            @if(auth()->user()->hasPermissionTo('Edit Expense Records'))
                                                                <a href="{{ route('expense-records.edit', $record) }}"
                                                                    class="avatar-text avatar-md text-warning" title="Edit">
                                                                    <i class="feather-edit-3"></i>
                                                                </a>
                                                            @endif

                                                            @if(auth()->user()->hasPermissionTo('Delete Expense Records'))
                                                                <form action="{{ route('expense-records.destroy', $record) }}"
                                                                    method="POST" class="d-inline">
                                                                    @csrf @method('DELETE')
                                                                    <button type="submit"
                                                                        class="avatar-text avatar-md text-danger border-0 bg-transparent"
                                                                        onclick="return confirm('Are you sure you want to delete this expense record?')">
                                                                        <i class="feather-trash-2"></i>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-5 text-muted">
                                                        <i class="feather-trending-down fs-1 mb-3 d-block opacity-50"></i>
                                                        No expense records found.
                                                        @if(auth()->user()->hasPermissionTo('Create Expense Records'))
                                                            <div class="mt-3">
                                                                <a href="{{ route('expense-records.create') }}" class="btn btn-sm btn-primary">
                                                                    Create your first expense record
                                                                </a>
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{ $expenseRecords->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        @if(auth()->user()->hasPermissionTo('Export Expense Records'))
        document.getElementById('selectAll').addEventListener('change', function() {
            document.querySelectorAll('.record-checkbox').forEach(cb => cb.checked = this.checked);
        });

        function exportSelected() {
            const selected = Array.from(document.querySelectorAll('.record-checkbox:checked'))
                .map(cb => cb.value).join(',');

            if (selected) {
                window.location.href = `{{ route('expense-records.export') }}?ids=${selected}`;
            } else {
                alert('Please select records to export');
            }
        }
        @endif
    </script>
@endsection

@push('scripts')
@endpush