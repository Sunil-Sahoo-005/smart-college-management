@extends('admin_layout')

@section('content')
<div class="container-fluid">

    <h4 class="mb-3">
        {{ isset($hostelfee) ? 'Edit Hostel Fee' : 'Add Hostel Fee' }}
    </h4>

    <div class="card">
        <div class="card-body">
            <div class="d-flex gap-2 mb-4">
                <button type="submit" form="hostelFeeForm" class="btn btn-primary">
                    {{ isset($hostelfee) ? 'Update' : 'Save' }}
                </button>

                <a href="{{ route('admin.hostelfees.index') }}" class="btn btn-secondary">
                    Back
                </a>
            </div>

            <form id="hostelFeeForm" method="POST"
                action="{{ isset($hostelfee)
                    ? route('admin.hostelfees.store', $hostelfee->id)
                    : route('admin.hostelfees.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Tenant ID</label>
                    <input type="number" name="tenant_id" class="form-control"
                        value="{{ $hostelfee->tenant_id ?? old('tenant_id') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Student ID</label>
                    <input type="number" name="student_id" class="form-control"
                        value="{{ $hostelfee->student_id ?? old('student_id') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Amount</label>
                    <input type="number" step="0.01" name="amount" class="form-control"
                        value="{{ $hostelfee->amount ?? old('amount') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Due Date</label>
                    <input type="date" name="due_date" class="form-control"
                        value="{{ $hostelfee->due_date ?? old('due_date') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="unpaid"
                            {{ (isset($hostelfee) && $hostelfee->status == 'unpaid') ? 'selected' : '' }}>
                            Unpaid
                        </option>
                        <option value="paid"
                            {{ (isset($hostelfee) && $hostelfee->status == 'paid') ? 'selected' : '' }}>
                            Paid
                        </option>
                    </select>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection
