@extends('admin_layout')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">
                    {{ !empty($gradeLevel) ? 'Edit Grade Level' : 'Create Grade Level' }}
                </h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.grade_levels.index') }}">Grade Levels</a></li>
                <li class="breadcrumb-item">
                    {{ !empty($gradeLevel) ? 'Edit Grade Level' : 'Create Grade Level' }}
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
                    <a href="{{ route('admin.grade_levels.index') }}" class="btn btn-secondary">
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
                <form action="{{ route('admin.grade_levels.store') }}" method="POST">
                    @csrf
                    @if (!empty($gradeLevel->id))
                        <input type="hidden" name="id" value="{{ $gradeLevel->id }}">
                    @endif

                    <div class="card stretch stretch-full">
                        <div class="card-body general-info">
                            <div class="mb-5 d-flex align-items-center justify-content-between">
                                <h5 class="fw-bold mb-0 me-4">
                                    <span class="d-block mb-2">Grade Level Info :</span>
                                    <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                        Define a grade range for a grading system
                                    </span>
                                </h5>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="grading_system_id" class="fw-semibold">Grading System:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-hash"></i></div>
                                        <select class="form-select @error('grading_system_id') is-invalid @enderror"
                                                id="grading_system_id"
                                                name="grading_system_id"
                                                required>
                                            <option value="">Select Grading System</option>
                                            @foreach($gradingSystems as $gradingSystem)
                                                <option value="{{ $gradingSystem->id }}"
                                                        {{ old('grading_system_id', $gradeLevel->grading_system_id ?? '') == $gradingSystem->id ? 'selected' : '' }}>
                                                    {{ $gradingSystem->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('grading_system_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="min_score" class="fw-semibold">Minimum Score:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-percent"></i></div>
                                        <input type="number"
                                               step="0.01"
                                               class="form-control @error('min_score') is-invalid @enderror"
                                               id="min_score"
                                               name="min_score"
                                               value="{{ old('min_score', $gradeLevel->min_score ?? 0) }}"
                                               min="0"
                                               max="100"
                                               placeholder="Minimum score (0–100)"
                                               required>
                                    </div>
                                    @error('min_score')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="max_score" class="fw-semibold">Maximum Score:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-percent"></i></div>
                                        <input type="number"
                                               step="0.01"
                                               class="form-control @error('max_score') is-invalid @enderror"
                                               id="max_score"
                                               name="max_score"
                                               value="{{ old('max_score', $gradeLevel->max_score ?? 100) }}"
                                               min="0"
                                               max="100"
                                               placeholder="Maximum score (0–100)"
                                               required>
                                    </div>
                                    @error('max_score')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="grade_letter" class="fw-semibold">Grade Letter:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-hash"></i></div>
                                        <input type="text"
                                               class="form-control @error('grade_letter') is-invalid @enderror"
                                               id="grade_letter"
                                               name="grade_letter"
                                               value="{{ old('grade_letter', $gradeLevel->grade_letter ?? '') }}"
                                               placeholder="Grade letter (e.g., A, B, C)"
                                               required>
                                    </div>
                                    @error('grade_letter')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="remarks" class="fw-semibold">Remarks:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-align-left"></i></div>
                                        <textarea class="form-control @error('remarks') is-invalid @enderror"
                                                  id="remarks"
                                                  name="remarks"
                                                  rows="2"
                                                  placeholder="Remarks (optional)">{{ old('remarks', $gradeLevel->remarks ?? '') }}</textarea>
                                    </div>
                                    @error('remarks')
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
