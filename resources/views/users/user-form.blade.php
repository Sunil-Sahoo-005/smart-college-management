@extends('admin_layout')

@section('content')
    <div class="nxl-content">
        <!-- [ page-header ] start -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">{{ !empty($user->id) ? 'Edit User' : 'Create User' }}</h5>
                </div>
                <ul class="breadcrumb">
                    {{-- <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li> --}}
                    <li class="breadcrumb-item">{{ !empty($user->id) ? 'Edit User' : 'Create User' }}</li>
                </ul>
            </div>
        </div>
        <!-- [ page-header ] end -->

        <!-- [ Main Content ] start -->
        <div class="main-content">
            <div class="row">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="col-lg-12">
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf
                        @if (!empty($user->id))
                            <input type="hidden" name="id" value="{{ $user->id }}">
                        @endif

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
                                        <label for="tenant_id" class="fw-semibold">Tenant:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-building"></i></div>
                                            <select class="form-select @error('tenant_id') is-invalid @enderror"
                                                id="tenant_id" name="tenant_id" required>
                                                <option value="">Select Tenant</option>
                                                @foreach ($tenants as $tenant)
                                                    <option value="{{ $tenant->id }}"
                                                        {{ old('tenant_id', $user->tenant_id ?? '') == $tenant->id ? 'selected' : '' }}>
                                                        {{ $tenant->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('tenant_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="first_name" class="fw-semibold">First Name:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-user"></i></div>
                                            <input type="text"
                                                class="form-control @error('first_name') is-invalid @enderror"
                                                id="first_name" name="first_name"
                                                value="{{ old('first_name', $user->first_name ?? '') }}"
                                                placeholder="First Name" required autocomplete="off">
                                        </div>
                                        @error('first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="last_name" class="fw-semibold">Last Name:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-user"></i></div>
                                            <input type="text"
                                                class="form-control @error('last_name') is-invalid @enderror" id="last_name"
                                                name="last_name" value="{{ old('last_name', $user->last_name ?? '') }}"
                                                placeholder="Last Name" required autocomplete="off">
                                        </div>
                                        @error('last_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="email" class="fw-semibold">Email:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-mail"></i></div>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                id="email" name="email"
                                                value="{{ old('email', $user->email ?? '') }}" placeholder="Email"
                                                required autocomplete="off">
                                        </div>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="phone" class="fw-semibold">Phone:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-phone"></i></div>
                                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                                id="phone" name="phone"
                                                value="{{ old('phone', $user->phone ?? '') }}" placeholder="Phone"
                                                required autocomplete="off">
                                        </div>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                @if (empty($user->id))
                                    <div class="row mb-4 align-items-center">
                                        <div class="col-lg-4">
                                            <label for="password" class="fw-semibold">Password:</label>
                                        </div>
                                        <div class="col-lg-8">
                                            <div class="input-group">
                                                <div class="input-group-text"><i class="feather-lock"></i></div>
                                                <input type="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    id="password" name="password" placeholder="Password" required autocomplete="off">
                                            </div>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @endif
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label class="fw-semibold">User Type:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-shield"></i></div>
                                            <select class="form-select @error('user_type') is-invalid @enderror"
                                                name="user_type" required autocomplete="off">
                                                <option value="">Select User Type</option>
                                                <option value="admin"
                                                    {{ old('user_type', $user->role ?? '') == 'admin' ? 'selected' : '' }}>
                                                    Admin</option>
                                                <option value="academic"
                                                    {{ old('user_type', $user->role ?? '') == 'academic' ? 'selected' : '' }}>
                                                    Academic</option>
                                                <option value="accountant"
                                                    {{ old('user_type', $user->role ?? '') == 'accountant' ? 'selected' : '' }}>
                                                    Accountant</option>
                                                <option value="librarian"
                                                    {{ old('user_type', $user->role ?? '') == 'librarian' ? 'selected' : '' }}>
                                                    Librarian</option>
                                                 <option value="travels"
                                                    {{ old('user_type', $user->role ?? '') == 'travels' ? 'selected' : '' }}>
                                                    Transportation</option>
                                                <option value="hostel"
                                                        {{ old('user_type', $user->role ?? '') == 'hostel' ? 'selected' : '' }}>
                                                    Hostel</option>

                                            </select>
                                        </div>
                                        @error('user_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="role" class="fw-semibold">Role:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-shield"></i></div>
                                            <select class="form-select @error('role_id') is-invalid @enderror"
                                                id="role_id" name="role_id" required autocomplete="off">
                                                <option value="">Select Role</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->id }}"
                                                        {{ old('role_id', $userRoleId ?? '') == $role->id ? 'selected' : '' }}>
                                                        {{ $role->name }} ({{ $role->module ?? 'All' }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('role')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="status" class="fw-semibold">Status:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="status"
                                                name="status"
                                                {{ old('status', $user->status ?? 'active') == 'active' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="status">Active</label>
                                        </div>
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
