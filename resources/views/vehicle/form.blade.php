@extends('admin_layout')

@section('content')

    <div class="nxl-content">
        <!-- [ page-header ] start -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">
                        {{ !empty($vehicle) ? 'Edit vehicle' : 'Create vehicle' }}
                    </h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.vehicle.index') }}">vehicle</a></li>
                    <li class="breadcrumb-item active">
                        {{ !empty($vehicle) ? 'Edit' : 'Create' }}
                    </li>
                </ul>
            </div>
        </div>
        <!-- [ page-header ] end -->

        <div class="main-content">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <form action="{{ route('admin.vehicle.store') }}" method="POST">
                        @csrf

                        @if(!empty($vehicle))
                            <input type="hidden" name="id" value="{{ $vehicle->id }}">
                        @endif
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Registration_number</label>
                                <input type="number" name="registration_number" class="form-control"
                                       value="{{ old('registration_number', $vehicle->registration_number ?? '') }}" required>
                            </div>  
                            
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                     <label class="form-label">Type</label>
                                    <select name="type" class="form-control" required>
                                        <option value="">Select Type</option>
                                        <option value="Bus" {{ old('type', $vehicle->type ?? '') == 'Bus' ? 'selected' : '' }}>Bus</option>
                                        <option value="Minivan" {{ old('type', $vehicle->type ?? '') == 'Minivan' ? 'selected' : '' }}>Minivan</option>
                                        <option value="van" {{ old('type', $vehicle->type ?? '') == 'van' ? 'selected' : '' }}>van</option>
                                    </select>
                                </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Capacity</label>
                                <input type="num" name="capacity" class="form-control"
                                       value="{{ old('capacity', $vehicle->capacity ?? '') }}">
                            </div>
                        </div>
                    

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Driver_id</label>
                                <input type="text" name="driver_id" class="form-control"
                                       value="{{ old('driver_id', $vehicle->driver_id ?? '') }}">
                            </div>
                            
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                {{ !empty($vehicle) ? 'Update' : 'Save' }}
                            </button>
                            <a href="{{ route('admin.vehicle.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
