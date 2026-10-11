@extends('admin_layout')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">
                    {{ !empty($section) ? 'Edit Section' : 'Create Section' }}
                </h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.sections.index') }}">Sections</a></li>
                <li class="breadcrumb-item">
                    {{ !empty($section) ? 'Edit Section' : 'Create Section' }}
                </li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="page-header-right-items">
                <div class="d-flex d-md-none">
                    <a href="javascript:void(0)" class="page-header-right-close-toggle">
                        <i class="feather-arrow-left me-2"></i>
                        <span>Back</span>
                    </a>
                </div>
                <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                    <a href="{{ route('admin.sections.index') }}" class="btn btn-secondary">
                        <i class="feather-arrow-left me-2"></i>
                        <span>Back to List</span>
                    </a>
                </div>
            </div>
            <div class="d-md-none d-flex align-items-center">
                <a href="javascript:void(0)" class="page-header-right-open-toggle">
                    <i class="feather-align-right fs-20"></i>
                </a>
            </div>
        </div>
    </div>
    <!-- [ page-header ] end -->

    <!-- [ Main Content ] start -->
    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <form action="{{ route('admin.sections.store') }}" method="POST">
                    @csrf
                    @if (!empty($section->id))
                        <input type="hidden" name="id" value="{{ $section->id }}">
                    @endif

                    <div class="card stretch stretch-full">
                        <div class="card-body general-info">
                            <div class="mb-5 d-flex align-items-center justify-content-between">
                                <h5 class="fw-bold mb-0 me-4">
                                    <span class="d-block mb-2">Section Info :</span>
                                    <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                        Basic details of the section
                                    </span>
                                </h5>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="class_id" class="fw-semibold">Class:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-hash"></i></div>
                                        <select class="form-select @error('class_id') is-invalid @enderror"
                                                id="class_id"
                                                name="class_id"
                                                required>
                                            <option value="">Select Class</option>
                                            @foreach($classes as $class)
                                                <option value="{{ $class->id }}"
                                                        {{ old('class_id', $section->class_id ?? '') == $class->id ? 'selected' : '' }}>
                                                    {{ $class->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('class_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="name" class="fw-semibold">Name:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-hash"></i></div>
                                        <input type="text"
                                               class="form-control @error('name') is-invalid @enderror"
                                               id="name"
                                               name="name"
                                               value="{{ old('name', $section->name ?? '') }}"
                                               placeholder="Section name (e.g., A, B, C)"
                                               required>
                                    </div>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="max_strength" class="fw-semibold">Max Strength:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-users"></i></div>
                                        <input type="number"
                                               class="form-control @error('max_strength') is-invalid @enderror"
                                               id="max_strength"
                                               name="max_strength"
                                               value="{{ old('max_strength', $section->max_strength ?? '') }}"
                                               min="1"
                                               placeholder="Maximum number of students">
                                    </div>
                                    @error('max_strength')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
</div>
@endsection
