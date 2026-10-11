@extends('hr.layout')

@section('content')
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">{{ $leaveType->exists ? 'Edit Leave Type' : 'Create Leave Type' }}</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('hr.dashboard') }}">HR</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('hr.leave-types.index', $tenantKey) }}">Leave Types</a></li>
                    <li class="breadcrumb-item">{{ $leaveType->exists ? 'Edit' : 'Create' }}</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="row g-4">
                <div class="col-12 col-xxl-8">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div>
                                    <h5 class="mb-1">{{ $leaveType->exists ? 'Edit Leave Type' : 'Create Leave Type' }}</h5>
                                    <div class="text-muted fs-12">Manage leave policy settings for {{ $tenant->name }}</div>
                                </div>
                                <a href="{{ route('hr.leave-types.index', $tenantKey) }}" class="btn btn-light">
                                    Back to List
                                </a>
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ $leaveType->exists ? route('hr.leave-types.update', [$tenantKey, $leaveType->id]) : route('hr.leave-types.store', $tenantKey) }}" method="POST">
                                @csrf
                                @if($leaveType->exists)
                                    @method('PUT')
                                @endif

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Leave Name</label>
                                        <input type="text" name="name" class="form-control" value="{{ old('name', $leaveType->name) }}" placeholder="e.g. Casual Leave" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Allowed Days</label>
                                        <input type="number" name="allowed_days" class="form-control" value="{{ old('allowed_days', $leaveType->allowed_days ?? 0) }}" min="0" placeholder="0" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Applicable To</label>
                                        <select name="applicable_to" class="form-select" required>
                                            <option value="all" {{ old('applicable_to', $leaveType->applicable_to) === 'all' ? 'selected' : '' }}>All</option>
                                            <option value="teaching" {{ old('applicable_to', $leaveType->applicable_to) === 'teaching' ? 'selected' : '' }}>Teaching</option>
                                            <option value="non_teaching" {{ old('applicable_to', $leaveType->applicable_to) === 'non_teaching' ? 'selected' : '' }}>Non Teaching</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="p-3 rounded border h-100">
                                            <label class="form-label d-block mb-3">Settings</label>
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" name="carry_forward" value="1" id="carry_forward" {{ old('carry_forward', $leaveType->carry_forward) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="carry_forward">Carry Forward</label>
                                            </div>

                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="is_paid" value="1" id="is_paid" {{ old('is_paid', $leaveType->exists ? $leaveType->is_paid : true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_paid">Paid Leave</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 mt-4">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                    <a href="{{ route('hr.leave-types.index', $tenantKey) }}" class="btn btn-light">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
