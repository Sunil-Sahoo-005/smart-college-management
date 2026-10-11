@extends('admin_layout')

@section('content')
<div class="nxl-content">

    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">
                    {{ isset($hostelbed) ? 'Edit Bed' : 'Add Bed' }}
                </h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.hostelbed.index') }}">Beds</a>
                </li>
                <li class="breadcrumb-item active">
                    {{ isset($hostelbed) ? 'Edit' : 'Create' }}
                </li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <div class="card stretch stretch-full">

            <div class="card-header">
                <h6 class="card-title mb-0">Bed Details</h6>
            </div>

            <div class="card-body">

                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- ✅ Buttons EXACT SAME STYLE as previous pages -->
                <div class="d-flex gap-2 mb-4">
                    <button type="submit" form="bedForm" class="btn btn-primary">
                        {{ isset($hostelbed) ? 'Update Bed' : 'Create Bed' }}
                    </button>

                    <a href="{{ route('admin.hostelbed.index') }}" class="btn btn-secondary">
                        Back
                    </a>
                </div>

                <!-- ✅ Single Clean Form -->
                <form id="bedForm"
                      method="POST"
                      action="{{ isset($hostelbed)
                                ? route('admin.hostelbed.update', $hostelbed->id)
                                : route('admin.hostelbed.store') }}">
                    @csrf
                    @if(isset($hostelbed))
                        @method('PUT')
                    @endif

                    <div class="row">

                        {{-- Room Select --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Room</label>
                            <select name="room_id" class="form-control" required>
                                <option value="">Select Room</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}"
                                        {{ old('room_id', $hostelbed->room_id ?? '') == $room->id ? 'selected' : '' }}>
                                        {{ $room->room_number }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Bed Number --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Bed Number</label>
                            <input type="text"
                                   name="bed_number"
                                   class="form-control"
                                   value="{{ old('bed_number', $hostelbed->bed_number ?? '') }}"
                                   placeholder="Enter bed number"
                                   required>
                        </div>

                        {{-- Status --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control" required>
                                <option value="vacant"
                                    {{ old('status', $hostelbed->status ?? 'vacant') == 'vacant' ? 'selected' : '' }}>
                                    Vacant
                                </option>
                                <option value="occupied"
                                    {{ old('status', $hostelbed->status ?? '') == 'occupied' ? 'selected' : '' }}>
                                    Occupied
                                </option>
                            </select>
                        </div>

                    </div>
                </form>

            </div> {{-- card-body --}}
        </div> {{-- card --}}
    </div> {{-- main-content --}}
</div> {{-- nxl-content --}}
@endsection
