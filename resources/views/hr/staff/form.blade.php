@extends('hr.layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('hr.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">
            <div class="content-area-header bg-white sticky-top px-4 py-3 border-bottom shadow-sm mb-2">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div>
                        <h5 class="fw-bold mb-0">{{ $staff->exists ? 'Edit Staff' : 'Add Staff' }}</h5>
                        <small class="text-muted">HR staff profile form</small>
                    </div>
                    <a href="{{ route('hr.staff.index', $tenantKey) }}" class="btn btn-outline-secondary btn-sm">Back</a>
                </div>
            </div>

            <div class="content-area-body px-4 py-4">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <form method="POST" action="{{ route('hr.staff.store', $tenantKey) }}" enctype="multipart/form-data">
                            @csrf
                            @if($staff->exists)
                                <input type="hidden" name="id" value="{{ $staff->id }}">
                            @endif

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $staff->first_name) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $staff->last_name) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $staff->email) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $staff->phone) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Designation</label>
                                    <select name="designation_id" class="form-select" required>
                                        <option value="">Select</option>
                                        @foreach($designations as $item)
                                            <option value="{{ $item->id }}" @selected(old('designation_id', $staff->designation_id) == $item->id)>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Department</label>
                                    <select name="department_id" class="form-select" required>
                                        <option value="">Select</option>
                                        @foreach($departments as $item)
                                            <option value="{{ $item->id }}" @selected(old('department_id', $staff->department_id) == $item->id)>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select" required>
                                        @foreach(['active','resigned','retired','on_leave'] as $status)
                                            <option value="{{ $status }}" @selected(old('status', $staff->status ?? 'active') == $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Photo</label>
                                    <input type="file" name="staff_photo" class="form-control">
                                </div>
                            </div>

                            <div class="mt-4 d-flex gap-2">
                                <button class="btn btn-primary" type="submit">Save</button>
                                <a href="{{ route('hr.staff.index', $tenantKey) }}" class="btn btn-light">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
