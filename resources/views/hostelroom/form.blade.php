@extends('admin_layout')

@section('content')
    <div class="nxl-content">

        <div class="page-header">
            <div class="page-header-title">
                <h5>Create Room</h5>
            </div>
        </div>
 
        <div class="main-content">
            <div class="card stretch stretch-full">
                <div class="card-body">

                    <div class="d-flex gap-2 mb-4">
                        <button type="submit" form="roomForm" class="btn btn-primary">
                            Save
                        </button>

                        <a href="{{ route('admin.hostelroom.index') }}" class="btn btn-secondary">
                            Back
                        </a>
                    </div>


                    <form id="roomForm" action="{{ route('admin.hostelroom.store') }}" method="POST">
                        @csrf

                        {{-- Room Number --}}
                        <div class="mb-3">
                            <label class="form-label">Room Number</label>
                            <input type="text" name="room_number" class="form-control" required>
                        </div>

                        {{-- Hostel --}}
                        <div class="mb-3">
                            <label class="form-label">Hostel</label>
                            <select name="hostel_id" class="form-control" required>
                                <option value="">Select Hostel</option>
                                @foreach ($hostels as $hostel)
                                    <option value="{{ $hostel->id }}">
                                        {{ $hostel->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Room Type --}}
                        <div class="mb-3">
                            <label class="form-label">Room Type</label>
                            <select name="type" class="form-control">
                                <option value="">Select</option>
                                <option value="Single">Single</option>
                                <option value="Double">Double</option>
                            </select>
                        </div>

                        {{-- Max Beds --}}
                        <div class="mb-3">
                            <label class="form-label">Maximum Beds</label>
                            <input type="number" name="max_beds" class="form-control" min="1" required>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
