@extends('admin_layout')

@section('content')
    <main class="nxl-container">
        <div class="nxl-content">
            <!-- [ page-header ] start -->
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10">
                            {{ !empty($staff) ? 'Edit Staff' : 'Create Staff' }}
                        </h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.staff.index') }}">Staff</a></li>
                        <li class="breadcrumb-item active">
                            {{ !empty($staff) ? 'Edit' : 'Create' }}
                        </li>
                    </ul>
                </div>
            </div>
            <!-- [ page-header ] end -->

            <div class="main-content">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            {{ !empty($staff) ? 'Edit: ' . $staff->first_name . ' ' . $staff->last_name : 'New Staff' }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.staff.store') }}" method="POST">
                            @csrf

                            @if (!empty($staff))
                                <input type="hidden" name="id" value="{{ $staff->id }}">
                            @endif

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="first_name" class="form-control"
                                        value="{{ old('first_name', $staff->first_name ?? '') }}" required>
                                    @if ($errors->first())
                                        <div class="text-danger fs-12 mt-1">{{ $errors->first() }}</div>
                                    @endif
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="last_name" class="form-control"
                                        value="{{ old('last_name', $staff->last_name ?? '') }}" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control"
                                        value="{{ old('email', $staff->email ?? '') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" class="form-control"
                                        value="{{ old('phone', $staff->phone ?? '') }}" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Gender</label>
                                    <select name="gender" class="form-select">
                                        <option value="">Select Gender</option>
                                        <option value="male"
                                            {{ old('gender', $staff->gender ?? '') == 'male' ? 'selected' : '' }}>Male
                                        </option>
                                        <option value="female"
                                            {{ old('gender', $staff->gender ?? '') == 'female' ? 'selected' : '' }}>Female
                                        </option>
                                        <option value="other"
                                            {{ old('gender', $staff->gender ?? '') == 'other' ? 'selected' : '' }}>Other
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Date of Birth</label>
                                    <input type="date" name="dob" class="form-control"
                                        value="{{ old('dob', $staff?->dob?->format('Y-m-d')) }}">

                                    {{-- <input type="date" name="dob" class="form-control"
                                       value="{{ old('dob', $staff->dob?->format('Y-m-d') ?? '') }}"> --}}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Blood Group</label>
                                    <input type="text" name="blood_group" class="form-control"
                                        value="{{ old('blood_group', $staff->blood_group ?? '') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Religion</label>
                                    <input type="text" name="religion" class="form-control"
                                        value="{{ old('religion', $staff->religion ?? '') }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nationality</label>
                                    <input type="text" name="nationality" class="form-control"
                                        value="{{ old('nationality', $staff->nationality ?? '') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select" required>
                                        <option value="active"
                                            {{ old('status', $staff->status ?? 'active') == 'active' ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="resigned"
                                            {{ old('status', $staff->status ?? 'active') == 'resigned' ? 'selected' : '' }}>
                                            Resigned</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Designation</label>
                                    <select name="designation_id" class="form-select" required>
                                        <option value="">Select Designation</option>
                                        @foreach ($designations as $d)
                                            <option value="{{ $d->id }}"
                                                {{ old('designation_id', $staff->designation_id ?? '') == $d->id ? 'selected' : '' }}>
                                                {{ $d->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Department</label>
                                    <select name="department_id" class="form-select" required>
                                        <option value="">Select Department</option>
                                        @foreach ($departments as $d)
                                            <option value="{{ $d->id }}"
                                                {{ old('department_id', $staff->department_id ?? '') == $d->id ? 'selected' : '' }}>
                                                {{ $d->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Joining Date</label>
                                    <input type="date" name="joining_date" class="form-control"
                                        value="{{ old('joining_date', $staff?->joining_date?->format('Y-m-d')) }}">

                                    {{-- <input type="date" name="joining_date" class="form-control"
                                       value="{{ old('joining_date', $staff->joining_date?->format('Y-m-d') ?? '') }}" required> --}}
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Resignation Date</label>
                                    <input type="date" name="resignation_date" class="form-control"
                                        value="{{ old('resignation_date', $staff?->resignation_date?->format('Y-m-d')) }}">

                                    {{-- <input type="date" name="resignation_date" class="form-control"
                                       value="{{ old('resignation_date', $staff->resignation_date?->format('Y-m-d') ?? '') }}"> --}}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">PAN</label>
                                    <input type="text" name="pan" class="form-control"
                                        value="{{ old('pan', $staff->pan ?? '') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Aadhaar</label>
                                    <input type="text" name="aadhaar" class="form-control"
                                        value="{{ old('aadhaar', $staff->aadhaar ?? '') }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Bank Account Name</label>
                                    <input type="text" name="bank_account_name" class="form-control"
                                        value="{{ old('bank_account_name', $staff->bank_account_name ?? '') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Bank Account Number</label>
                                    <input type="text" name="bank_account_number" class="form-control"
                                        value="{{ old('bank_account_number', $staff->bank_account_number ?? '') }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">IFSC</label>
                                    <input type="text" name="ifsc" class="form-control"
                                        value="{{ old('ifsc', $staff->ifsc ?? '') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Photo URL</label>
                                    <input type="text" name="photo_url" class="form-control"
                                        value="{{ old('photo_url', $staff->photo_url ?? '') }}">
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    {{ !empty($staff) ? 'Update' : 'Save' }}
                                </button>
                                <a href="{{ route('admin.staff.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
