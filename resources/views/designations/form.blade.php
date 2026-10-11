@extends('admin_layout')

@section('content')
    <div class="nxl-content">
        <!-- [ page-header ] start -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">
                        {{ !empty($designation) ? 'Edit Designation' : 'Create Designation' }}
                    </h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.designations.index') }}">Designations</a></li>
                    <li class="breadcrumb-item active">
                        {{ !empty($designation) ? 'Edit' : 'Create' }}
                    </li>
                </ul>
            </div>
        </div>
        <!-- [ page-header ] end -->

        <div class="main-content">
            <div class="card stretch stretch-full">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        {{ !empty($designation) ? 'Edit: ' . $designation->name : 'New Designation' }}
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.designations.store') }}" method="POST">
                        @csrf

                        @if (!empty($designation))
                            <input type="hidden" name="id" value="{{ $designation->id }}">
                        @endif

                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control"
                                value="{{ old('name', $designation->name ?? '') }}" required>
                            @if ($errors->first())
                                <div class="text-danger fs-12 mt-1">{{ $errors->first() }}</div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3">
                                {{ old('description', $designation->description ?? '') }}
                            </textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                {{ !empty($designation) ? 'Update' : 'Save' }}
                            </button>
                            <a href="{{ route('admin.designations.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
