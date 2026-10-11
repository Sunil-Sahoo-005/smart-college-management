@extends('admin_layout')

@section('content')

<div class="nxl-content">
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">
                    {{ !empty($studentroutemapping) ? 'Edit Student Route Mapping' : 'Create Student Route Mapping' }}
                </h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.studentroutemapping.index') }}">Student Route Mapping</a></li>
                <li class="breadcrumb-item active">
                    {{ !empty($studentroutemapping) ? 'Edit' : 'Create' }}
                </li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <div class="card stretch stretch-full">
            <div class="card-body">
                <form action="{{ route('admin.studentroutemapping.store') }}" method="POST">
                    @csrf

                    @if(!empty($studentroutemapping))
                        <input type="hidden" name="id" value="{{ $studentroutemapping->id }}">
                    @endif

                    {{-- Student --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Student</label>
                        <select name="student_id" class="form-control" required>
                            <option value="">Select Student</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}"
                                    {{ old('student_id', $studentroutemapping->student_id ?? '') == $student->id ? 'selected' : '' }}>
                                    {{ $student->first_name }}
                                    {{ $student->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Route --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Route</label>
                        <select name="route_id" class="form-control" required>
                            <option value="">Select Route</option>
                            @foreach($routes as $route)
                                <option value="{{ $route->id }}"
                                    {{ old('route_id', $studentroutemapping->route_id ?? '') == $route->id ? 'selected' : '' }}>
                                    {{ $route->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Stop (optional) --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Stop</label>
                        <select name="stop_id" class="form-control">
                            <option value="">Select Stop</option>
                            @foreach($routestops as $stop)
                                <option value="{{ $stop->id}}" {{ old('stop_id', $studentroutemapping->stop->id?? '') == $stop->id ? 'selected' : '' }}>
                                    {{ $stop->stop_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    

                    {{-- Transport Fee --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Transport Fee</label>
                        <input type="number" step="0.01" name="transport_fee" class="form-control"
                               value="{{ old('transport_fee', $studentroutemapping->transport_fee ?? 0) }}">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            {{ !empty($studentroutemapping) ? 'Update' : 'Save' }}
                        </button>
                        <a href="{{ route('admin.studentroutemapping.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@endsection
