@extends('admin_layout')

@section('content')
    <div class="nxl-content">
        <!-- [ page-header ] start -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">User</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item">Create User</li>
                </ul>
            </div>
        </div>
        <!-- [ page-header ] end -->

        <!-- [ Main Content ] start -->
        <div class="main-content">
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf
                        <div class="card stretch stretch-full">
                            <div class="card-body general-info">
                                <div class="mb-5 d-flex align-items-center justify-content-between">
                                    <h5 class="fw-bold mb-0 me-4">
                                        <span class="d-block mb-2">User Info :</span>
                                        <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                            General information for your user
                                        </span>
                                    </h5>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="Username" class="fw-semibold">Username:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-user"></i></div>
                                            <input type="text"
                                                class="form-control @error('Username') is-invalid @enderror" id="Username"
                                                name="Username" value="{{ old('Username') }}" placeholder="Username"
                                                required autocomplete="off">
                                        </div>
                                        @error('Username')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="Email" class="fw-semibold">Email:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-mail"></i></div>
                                            <input type="email" class="form-control @error('Email') is-invalid @enderror"
                                                id="Email" name="Email" value="{{ old('Email') }}"
                                                placeholder="Email" required autocomplete="off">
                                        </div>
                                        @error('Email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="PasswordHash" class="fw-semibold">Password:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-lock"></i></div>
                                            <input type="password"
                                                class="form-control @error('PasswordHash') is-invalid @enderror"
                                                id="PasswordHash" name="PasswordHash" placeholder="Password" required autocomplete="off">
                                        </div>
                                        @error('PasswordHash')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="RoleId" class="fw-semibold">Role:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-shield"></i></div>
                                            <select class="form-select @error('RoleId') is-invalid @enderror" id="RoleId"
                                                name="RoleId" autocomplete="off">
                                                <option value="">Select Role</option>
                                                {{-- @foreach ($roles as $role)
                                                    <option value="{{ $role->RoleId }}"
                                                        {{ old('RoleId') == $role->RoleId ? 'selected' : '' }}>
                                                        {{ $role->RoleName }}
                                                    </option>
                                                @endforeach --}}
                                            </select>
                                        </div>
                                        @error('RoleId')
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
