@extends('admin_layout')

@section('content')

    <div class="nxl-content">
        <!-- [ page-header ] start -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">
                        {{ !empty($driver) ? 'Edit driver' : 'Create driver' }}
                    </h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.driver.index') }}">driver</a></li>
                    <li class="breadcrumb-item active">
                        {{ !empty($driver) ? 'Edit' : 'Create' }}
                    </li>
                </ul>
            </div>
        </div>
        <!-- [ page-header ] end -->

        <div class="main-content">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <form action="{{ route('admin.driver.store') }}" method="POST">
                        @csrf

                        @if(!empty($driver))
                            <input type="hidden" name="id" value="{{ $driver->id }}">
                        @endif
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control"
                                       value="{{ old('first_name', $driver->first_name ?? '') }}" required>
                                @if($errors->first())
                                    <div class="text-danger fs-12 mt-1">{{ $errors->first() }}</div>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control"
                                       value="{{ old('last_name', $driver->last_name ?? '') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone</label>
                                <input type="number" name="phone" class="form-control"
                                       value="{{ old('phone', $driver->phone ?? '') }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">License_number</label>
                                <input type="text" name="license_number" class="form-control"
                                       value="{{ old('license_number', $driver->license_number ?? '') }}">
                            </div>
                        </div>
                            
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                {{ !empty($vehicle) ? 'Update' : 'Save' }}
                            </button>
                            <a href="{{ route('admin.driver.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
