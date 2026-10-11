@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Travels.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                <h6 class="card-title mb-0">
                                {{ !empty($routestops) ? 'Edit: routestops' . $routestops->name : 'New route stops' }}
                            </h6>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">

                        </div>
                    </div>
                </div>
                <div class="content-area-body">
                @include('elements.flash')
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
                                    {{ $route->name  }}
                                </option>
                            @endforeach
                        </select>

                    </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stop name</label>
                                <input type="text" name="stop_name" class="form-control"
                                       value="{{ old('stop_name', $routestops->stop_name ?? '') }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Arrival time</label>
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
        </div>
    </div>
@endsection
