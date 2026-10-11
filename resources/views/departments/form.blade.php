@extends('admin_layout')

@section('content')
    <div class="nxl-content">
        <!-- [ page-header ] start -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">
                        {{ !empty($department) ? 'Edit Department' : 'Create Department' }}
                    </h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.departments.index') }}">Departments</a></li>
                    <li class="breadcrumb-item active">
                        {{ !empty($department) ? 'Edit' : 'Create' }}
                    </li>
                </ul>
            </div>
        </div>
        <!-- [ page-header ] end -->

        <div class="main-content">
            <div class="card stretch stretch-full">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        {{ !empty($department) ? 'Edit: ' . $department->name : 'New Department' }}
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ !empty($department) ? route('admin.departments.store') : route('admin.departments.store') }}" method="POST">
                        @csrf

                        @if(!empty($department))
                            <input type="hidden" name="id" value="{{ $department->id }}">
                        @endif

                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control"
                                   value="{{ old('name', $department->name ?? '') }}" required>
                            @if($errors->first())
                                <div class="text-danger fs-12 mt-1">{{ $errors->first() }}</div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3">
                                {{ old('description', $department->description ?? '') }}
                            </textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                {{ !empty($department) ? 'Update' : 'Save' }}
                            </button>
                            <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
