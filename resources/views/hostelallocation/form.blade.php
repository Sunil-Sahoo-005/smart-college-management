@extends('admin_layout')

@section('content')
<div class="nxl-content">

    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">
                    {{ isset($allocation) ? 'Edit Allocation' : 'Add Allocation' }}
                </h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.hostelallocation.index') }}">Allocation</a></li>
                <li class="breadcrumb-item active">
                    {{ isset($allocation) ? 'Edit' : 'Create' }}
                </li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <div class="card stretch stretch-full">

            {{-- 🔥 CARD HEADER WITH BUTTONS (REFERENCE STYLE) --}}
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="card-title mb-0">Allocation Details</h6>

                <div class="d-flex gap-2">
                    <a href="{{ route('admin.hostelallocation.index') }}"
                       class="btn btn-secondary btn-sm">
                        Back
                    </a>

                    <button type="submit"
                            form="allocationForm"
                            class="btn btn-primary btn-sm">
                        {{ isset($allocation) ? 'Update' : 'Save' }}
                    </button>
                </div>
            </div>

            <div class="card-body">

                <form id="allocationForm"
                      method="POST"
                      action="{{ isset($allocation)
                                ? route('admin.hostelallocation.update', $allocation->id)
                                : route('admin.hostelallocation.store') }}">
                    @csrf
                    @if(isset($allocation))
                        @method('PUT')
                    @endif

                    <div class="row">

                        {{-- Student --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Student</label>
                            <select name="student_id" class="form-control" required>
                                <option value="">Select Student</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}"
                                        {{ old('student_id', $allocation->student_id ?? '') == $student->id ? 'selected' : '' }}>
                                        {{ $student->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Room --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Room</label>
                            <select name="room_id" class="form-control" required>
                                <option value="">Select Room</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}"
                                        {{ old('room_id', $allocation->room_id ?? '') == $room->id ? 'selected' : '' }}>
                                        {{ $room->room_number }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Bed --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Bed</label>
                            <select name="bed_id" class="form-control" required>
                                <option value="">Select Bed</option>
                                @foreach($beds as $bed)
                                    <option value="{{ $bed->id }}"
                                        {{ old('bed_id', $allocation->bed_id ?? '') == $bed->id ? 'selected' : '' }}>
                                        {{ $bed->bed_number }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- From Date --}}
                        <div class="col-md-3 mb-3">
                            <label class="form-label">From Date</label>
                            <input type="date"
                                   name="from_date"
                                   class="form-control"
                                   value="{{ old('from_date', $allocation->from_date ?? '') }}">
                        </div>

                        {{-- To Date --}}
                        <div class="col-md-3 mb-3">
                            <label class="form-label">To Date</label>
                            <input type="date"
                                   name="to_date"
                                   class="form-control"
                                   value="{{ old('to_date', $allocation->to_date ?? '') }}">
                        </div>

                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
