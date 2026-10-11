@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Semester–Branch Mapping Info ::</span>

                    </h5>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                        <a href="{{ route('admin.class_stream_mappings.index') }}" class="btn btn-secondary">
                                <i class="feather-arrow-left me-2"></i>
                                <span>Back to List</span>
                            </a>
                        </div>
                    </div>
                </div>
                @include('elements.flash')
                <div class="content-area-body">
                    <form action="{{ route('admin.class_stream_mappings.store') }}" method="POST">
                        @csrf
                        @if (!empty($mapping->id))
                            <input type="hidden" name="id" value="{{ $mapping->id }}">
                        @endif

                        <div class="card stretch stretch-full">
                            <div class="card-body general-info">
                                <div class="mb-5 d-flex align-items-center justify-content-between">
                                    <h5 class="fw-bold mb-0 me-4">
                                        {{-- <span class="d-block mb-2">Class–Stream Mapping Info :</span>
                                        <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                            Link a class with a stream
                                        </span> --}}
                                    </h5>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="stream_id" class="fw-semibold">Branch:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-hash"></i></div>
                                            <select class="form-select @error('stream_id') is-invalid @enderror"
                                                id="stream_id" name="stream_id" required>
                                                <option value="">Select Branch</option>
                                                @foreach ($streams as $stream)
                                                    <option value="{{ $stream->id }}"
                                                        {{ old('stream_id', $mapping->stream_id ?? '') == $stream->id ? 'selected' : '' }}>
                                                        {{ $stream->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('stream_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="class_id" class="fw-semibold">Semester:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-hash"></i></div>
                                            <select class="form-select @error('class_id') is-invalid @enderror"
                                                id="class_id" name="class_id" required>
                                                <option value="">Select Semester</option>
                                                @foreach ($classes as $class)
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


                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
