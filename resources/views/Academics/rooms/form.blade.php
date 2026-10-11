@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">
                            {{ !empty($room->id) ? 'Edit Room' : 'Create Room' }}
                        </span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                            <a href="{{ route('admin.rooms.index') }}" class="btn btn-secondary">
                                <i class="feather-arrow-left me-2"></i>
                                <span>Back to List</span>
                            </a>
                        </div>
                    </div>
                </div>
@include('elements.flash')
                <div class="content-area-body">
                    <div class="card stretch stretch-full">
                        <div class="card-body general-info">
                            <div class="mb-5 d-flex align-items-center justify-content-between">
                                <h5 class="fw-bold mb-0 me-4">
                                    <span class="d-block mb-2">Room Info :</span>
                                    <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                        Define room name, type, and capacity
                                    </span>
                                </h5>
                                <button type="submit" form="roomForm" class="btn btn-primary">Save</button>
                            </div>

                            <form id="roomForm" action="{{ route('admin.rooms.store') }}" method="POST">
                                @csrf
                                @if (!empty($room->id))
                                    <input type="hidden" name="id" value="{{ $room->id }}">
                                @endif

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="name" class="fw-semibold">Name:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-hash"></i></div>
                                            <input type="text"
                                                   class="form-control @error('name') is-invalid @enderror"
                                                   id="name" name="name"
                                                   value="{{ old('name', $room->name ?? '') }}"
                                                   placeholder="Room Name" required autocomplete="off">
                                        </div>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="type" class="fw-semibold">Type:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-home"></i></div>
                                            <select class="form-select @error('type') is-invalid @enderror"
                                                    id="type" name="type" required autocomplete="off">
                                                <option value="">Select Type</option>
                                                @foreach($types as $type)
                                                    <option value="{{ $type }}"
                                                            {{ old('type', $room->type ?? '') == $type ? 'selected' : '' }}>
                                                        {{ ucfirst($type) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="capacity" class="fw-semibold">Capacity (Optional):</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-users"></i></div>
                                            <input type="number"
                                                   class="form-control @error('capacity') is-invalid @enderror"
                                                   id="capacity" name="capacity"
                                                   value="{{ old('capacity', $room->capacity ?? '') }}"
                                                   placeholder="Capacity" autocomplete="off">
                                        </div>
                                        @error('capacity')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
