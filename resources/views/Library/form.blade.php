@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Library.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">

                        </div>
                    </div>
                </div>
                <div class="content-area-body">
                    <div class="card stretch stretch-full">
                        <div class="card-body general-info">
                            <div class="mb-5 d-flex align-items-center justify-content-between">
                                <h5 class="fw-bold mb-0 me-4">
                                    <span class="d-block mb-2">Class–Stream Mapping Info :</span>
                                    <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                        Link a class with a stream
                                    </span>
                                </h5>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ old('name', $designation->name ?? '') }}" required autocomplete="off" placeholder="Enter name">
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

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
