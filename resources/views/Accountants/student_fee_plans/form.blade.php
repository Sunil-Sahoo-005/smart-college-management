@extends('admin_layout')

@section('content')
    <div class="nxl-content">
        <div class="main-content">
            <div class="page-header">
                <h4>{{ isset($plan) ? 'Edit' : 'Assign' }} Fee Plan</h4>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST"
                          action="{{ isset($plan)
                              ? route('admin.student-fee-plans.update', $plan)
                              : route('admin.student-fee-plans.store') }}">
                        @csrf
                        @if (isset($plan))
                            @method('PUT')
                        @endif

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Student</label>
                                <select name="student_id" class="form-select" required>
                                    <option value="">Select Student</option>
                                    @foreach ($students as $student)
                                        <option value="{{ $student->id }}"
                                            {{ (isset($plan) ? $plan->student_id : old('student_id')) == $student->id ? 'selected' : '' }}>
                                            {{ $student->first_name }} {{ $student->last_name }} ({{ $student->roll_number }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fee Structure</label>
                                <select name="fee_structure_id" class="form-select" required>
                                    <option value="">Select Fee Structure</option>
                                    @foreach ($feeStructures as $fs)
                                        <option value="{{ $fs->id }}"
                                            {{ (isset($plan) ? $plan->fee_structure_id : old('fee_structure_id')) == $fs->id ? 'selected' : '' }}>
                                            {{ $fs->name }} – {{ $fs->class?->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control" required
                                       value="{{ isset($plan) ? $plan->start_date : old('start_date') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control" required
                                       value="{{ isset($plan) ? $plan->end_date : old('end_date') }}">
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                {{ isset($plan) ? 'Update' : 'Assign' }} Plan
                            </button>
                            <a href="{{ route('admin.student-fee-plans.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
