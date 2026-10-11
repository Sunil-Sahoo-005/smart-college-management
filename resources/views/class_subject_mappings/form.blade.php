@extends('admin_layout')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">
                    {{ !empty($mapping) ? 'Edit Class–Subject Mapping' : 'Create Class–Subject Mapping' }}
                </h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.class_subject_mappings.index') }}">Class–Subject Mappings</a></li>
                <li class="breadcrumb-item">
                    {{ !empty($mapping) ? 'Edit Mapping' : 'Create Mapping' }}
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
                    <a href="{{ route('admin.class_subject_mappings.index') }}" class="btn btn-secondary">
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
                <form action="{{ route('admin.class_subject_mappings.store') }}" method="POST">
                    @csrf
                    @if (!empty($mapping->id))
                        <input type="hidden" name="id" value="{{ $mapping->id }}">
                    @endif

                    <div class="card stretch stretch-full">
                        <div class="card-body general-info">
                            <div class="mb-5 d-flex align-items-center justify-content-between">
                                <h5 class="fw-bold mb-0 me-4">
                                    <span class="d-block mb-2">Class–Subject Mapping Info :</span>
                                    <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                        Link a subject to a class
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
                                                        {{ old('class_id', $mapping->class_id ?? '') == $class->id ? 'selected' : '' }}>
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
                                    <label for="subject_id" class="fw-semibold">Subject:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-book"></i></div>
                                        <select class="form-select @error('subject_id') is-invalid @enderror"
                                                id="subject_id"
                                                name="subject_id"
                                                required>
                                            <option value="">Select Subject</option>
                                            @foreach($subjects as $subject)
                                                <option value="{{ $subject->id }}"
                                                        {{ old('subject_id', $mapping->subject_id ?? '') == $subject->id ? 'selected' : '' }}>
                                                    {{ $subject->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('subject_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="is_compulsory" class="fw-semibold">Compulsory:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-check-circle"></i></div>
                                        <select class="form-select @error('is_compulsory') is-invalid @enderror"
                                                id="is_compulsory"
                                                name="is_compulsory"
                                                required>
                                            <option value="1" {{ old('is_compulsory', $mapping->is_compulsory ?? true) ? 'selected' : '' }}>
                                                Yes
                                            </option>
                                            <option value="0" {{ old('is_compulsory', $mapping->is_compulsory ?? true) ? '' : 'selected' }}>
                                                No
                                            </option>
                                        </select>
                                    </div>
                                    @error('is_compulsory')
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
