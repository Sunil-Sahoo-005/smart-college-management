@extends('admin_layout')
@section('content')

<div class="nxl-content without-header nxl-full-content">
<div class="main-content d-flex">
@include('hostel.sidebar')

<div class="content-area">
<div class="card stretch stretch-full">

<div class="card-header">
    <h6 class="card-title mb-0">
        {{ $hostelfee ? 'Edit Hostel Fee' : 'Create Hostel Fee' }}
    </h6>
</div>

<div class="card-body">

<div class="d-flex gap-2 mb-4">
    <button type="submit" form="feeForm" class="btn btn-primary">
        {{ $hostelfee ? 'Update' : 'Save' }}
    </button>

    <a href="{{ route('admin.hostelfees.index') }}" class="btn btn-secondary">
        Back
    </a>
</div>

<form id="feeForm" method="POST"
      action="{{ route('admin.hostelfees.store') }}">
@csrf

@if($hostelfee)
<input type="hidden" name="id" value="{{ $hostelfee->id }}">
@endif

<div class="row">

<div class="col-md-6 mb-3">
    <label class="form-label">Student</label>
    <select name="student_id" class="form-control" required>
        <option value="">Select Student</option>
        @foreach($students as $student)
            <option value="{{ $student->id }}"
                {{ old('student_id', $hostelfee->student_id ?? '') == $student->id ? 'selected' : '' }}>
                {{ $student->first_name }} {{ $student->last_name }}
            </option>
        @endforeach
    </select>
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Amount</label>
    <input type="number" step="0.01" name="amount"
           class="form-control"
           value="{{ old('amount', $hostelfee->amount ?? '') }}"
           required>
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Due Date</label>
    <input type="date" name="due_date"
           class="form-control"
           value="{{ old('due_date', $hostelfee->due_date ?? '') }}"
           required>
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Status</label>
    <select name="status" class="form-control" required>
        <option value="unpaid"
            {{ old('status', $hostelfee->status ?? '') == 'unpaid' ? 'selected' : '' }}>
            Unpaid
        </option>
        <option value="paid"
            {{ old('status', $hostelfee->status ?? '') == 'paid' ? 'selected' : '' }}>
            Paid
        </option>
    </select>
</div>

</div>
</form>

</div>
</div>
</div>
</div>
</div>

@endsection
