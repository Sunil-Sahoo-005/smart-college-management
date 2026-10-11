@extends('admin_layout')

@section('content')

    <div class="nxl-content">
        <!-- [ page-header ] start -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">
                        {{ !empty($routes) ? 'Edit routes' : 'Create routes' }}
                    </h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.routes.index') }}">routes</a></li>
                    <li class="breadcrumb-item active">
                        {{ !empty($routes) ? 'Edit' : 'Create' }}
                    </li>
                </ul>
            </div>
        </div>
        <!-- [ page-header ] end -->

        <div class="main-content">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <form action="{{ route('admin.routes.store') }}" method="POST">
                        @csrf

                        @if(!empty($routes))
                            <input type="hidden" name="id" value="{{ $routes->id }}">
                        @endif
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control"
                                       value="{{ old('name', $routes->name ?? '') }}" required>
                                @if($errors->first())
                                    <div class="text-danger fs-12 mt-1">{{ $errors->first() }}</div>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Description</label>
                                <input type="text" name="description" class="form-control"
                                       value="{{ old('description', $routes->description ?? '') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Start_point</label>
                                <input type="text" name="start_point" class="form-control"
                                       value="{{ old('start_point', $routes->start_point ?? '') }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">End_point</label>
                                <input type="text" name="end_point" class="form-control"
                                       value="{{ old('end_point', $routes->end_point ?? '') }}">
                            </div>
                        </div>
                            
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                {{ !empty($routes) ? 'Update' : 'Save' }}
                            </button>
                            <a href="{{ route('admin.routes.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
