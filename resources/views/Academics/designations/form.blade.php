@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <div class="card-header">
                            <h6 class="card-title mb-0">
                                {{ !empty($designation) ? 'Edit: ' . $designation->name : 'New Designation' }}
                            </h6>
                        </div>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                        <a href="{{ route('admin.designations.index') }}" class="btn btn-secondary">
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
                            <form action="{{ route('admin.designations.store') }}" method="POST">
                                @csrf

                                @if (!empty($designation))
                                    <input type="hidden" name="id" value="{{ $designation->id }}">
                                @endif

                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name', $designation->name ?? '') }}" required autocomplete="off">
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
        </div>
    </div>
@endsection
