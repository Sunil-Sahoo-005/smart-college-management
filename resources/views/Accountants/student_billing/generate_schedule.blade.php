@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Accountants.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Generate Fee Schedule</span>
                    </h5>
                </div>

                <div class="content-area-body">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            {{-- <form method="POST" action="{{ route('admin.student-fee-plans.generate') }}">
                                @csrf

                                <div class="row mb-3">
                                    <div class="col-md-8">
                                        <label class="form-label">Select Student Fee Plan</label>
                                        <select name="student_fee_structure_id" class="form-select" required>
                                            <option value="">Select Plan</option>
                                            @foreach ($studentFeePlans as $plan)
                                                <option value="{{ $plan->id }}">
                                                    {{ $plan->student?->first_name }} {{ $plan->student?->last_name }}
                                                    → {{ $plan->feeStructure?->name }}
                                                    ({{ $plan->start_date->format('d M Y') }} –
                                                    {{ $plan->end_date->format('d M Y') }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Frequency</label>
                                        <select name="frequency" class="form-select" required>
                                            <option value="monthly">Monthly</option>
                                            <option value="quarterly">Quarterly</option>
                                            <option value="termly">Termly</option>
                                        </select>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    Generate Installments
                                </button>
                            </form> --}}
                            <!-- Preview / Filter Form (GET) -->
                            <form method="GET" action="{{ route('admin.student-fee-plans.generate') }}" class="mb-3">
                                <div class="row">
                                    <div class="col-md-8">
                                        <label class="form-label">Select Student Fee Plan</label>
                                        <select name="student_fee_structure_id" class="form-select"
                                            onchange="this.form.submit()">
                                            <option value="">Select Plan</option>
                                            @foreach ($studentFeePlans as $plan)
                                                <option value="{{ $plan->id }}"
                                                    {{ request('student_fee_structure_id') == $plan->id ? 'selected' : '' }}>
                                                    {{ $plan->student?->first_name }} {{ $plan->student?->last_name }}
                                                    → {{ $plan->feeStructure?->name }}
                                                    ({{ $plan->start_date->format('d M Y') }} –
                                                    {{ $plan->end_date->format('d M Y') }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Frequency</label>
                                        <select name="frequency" class="form-select">
                                            <option value="monthly"
                                                {{ request('frequency') == 'monthly' ? 'selected' : '' }}>
                                                Monthly
                                            </option>
                                            <option value="quarterly"
                                                {{ request('frequency') == 'quarterly' ? 'selected' : '' }}>
                                                Quarterly
                                            </option>
                                            <option value="termly"
                                                {{ request('frequency') == 'termly' ? 'selected' : '' }}>
                                                Termly
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </form>

                            <!-- Generate Installments (POST) -->
                            <form method="POST" action="{{ route('admin.student-fee-plans.generate') }}">
                                @csrf

                                <input type="hidden" name="student_fee_structure_id"
                                    value="{{ request('student_fee_structure_id') }}">

                                <input type="hidden" name="frequency" value="{{ request('frequency') ?? 'monthly' }}">

                                <button type="submit" class="btn btn-primary">
                                    Generate Installments
                                </button>
                            </form>

                        </div>
                    </div>

                    <div class="card stretch stretch-full mt-3">
                        <div class="card-body">
                            <h6 class="card-title">Generated Installments</h6>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Month</th>
                                            <th>Fee Type</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($installments as $inst)
                                            <tr>
                                                <td>{{ $inst->due_date->format('M Y') }}</td>
                                                <td>{{ $inst->feeCategory?->name ?? 'N/A' }}</td>
                                                <td>{{ number_format($inst->amount, 2) }}</td>
                                                <td>
                                                    <span
                                                        class="badge
                                    @if ($inst->status == 'paid') bg-success
                                    @elseif ($inst->status == 'partial') bg-warning
                                    @else bg-danger @endif">
                                                        {{ ucfirst($inst->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">
                                                    No installments generated yet for this plan.
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
    </div>
@endsection
