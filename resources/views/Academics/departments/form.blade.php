@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <div class="card-header">
                            <h6 class="card-title mb-0">
                                {{ !empty($department) ? 'Edit: ' . $department->name : 'New Department' }}
                            </h6>
                        </div>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                        <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">
                                <i class="feather-arrow-left me-2"></i>
                                <span>Back to List</span>
                            </a>
                        </div>
                    </div>
                </div>
                @include('elements.flash')
                <div class="content-area-body">
                    <div class="card stretch stretch-full">
                        
                        <div class="card-body">
                            <form
                                action="{{ !empty($department) ? route('admin.departments.store') : route('admin.departments.store') }}"
                                method="POST">
                                @csrf

                                @if (!empty($department))
                                    <input type="hidden" name="id" value="{{ $department->id }}">
                                @endif

                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name', $department->name ?? '') }}" required autocomplete="off"
                                        placeholder="Department name (e.g., Computer Science, Physics)">
                                    @if ($errors->first())
                                        <div class="text-danger fs-12 mt-1">{{ $errors->first() }}</div>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="3" autocomplete="off"
                                        placeholder="Department description">
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
        </div>
    </div>
@endsection
