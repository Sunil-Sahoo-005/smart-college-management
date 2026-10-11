@extends('admin_layout')

@section('content')

    <div class="nxl-content">
        <!-- [ page-header ] start -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">
                        {{ !empty($routestops) ? 'Edit routestops' : 'Create routestops' }}
                    </h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.routestops.index') }}">Routestops</a></li>
                    <li class="breadcrumb-item active">
                        {{ !empty($routestops) ? 'Edit' : 'Create' }}
                    </li>
                </ul>
            </div>
        </div>
        <!-- [ page-header ] end -->

        <div class="main-content">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <form action="{{ route('admin.routestops.store') }}" method="POST">
                        @csrf

                        @if(!empty($routestops))
                            <input type="hidden" name="id" value="{{ $routestops->id }}">
                        @endif
                        
                        <div class="form-group">
                            <label>Route</label>
                            <select name="route_id" class="form-control" required>
                            <option value="">Select Route</option>

                            @foreach ($route_id as $route)
                                <option value="{{ $route->id }}">
                                    {{ $route->name }}
                                </option>
                            @endforeach
                        </select>

                    </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stop_name</label>
                                <input type="text" name="stop_name" class="form-control"
                                       value="{{ old('stop_name', $routestops->stop_name ?? '') }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Arrival_time</label>
                                <input type="time" name="arrival_time" class="form-control"
                                       value="{{ old('arrival_time', $routestops->arrival_time ?? '') }}">
                            </div>
                        </div>
                            
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                {{ !empty($routestops) ? 'Update' : 'Save' }}
                            </button>
                            <a href="{{ route('admin.routestops.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
