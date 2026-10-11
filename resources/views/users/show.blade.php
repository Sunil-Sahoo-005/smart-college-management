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
                    <li class="breadcrumb-item">View</li>
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
                        <a href="javascript:void(0);" class="btn btn-icon btn-light-brand">
                            <i class="feather-printer"></i>
                        </a>
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-icon btn-light-brand">
                            <i class="feather-edit"></i>
                        </a>
                        <div class="dropdown">
                            <a class="btn btn-icon btn-light-brand" data-bs-toggle="dropdown" data-bs-offset="0, 10" data-bs-auto-close="outside">
                                <i class="feather-more-horizontal"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <i class="feather-user-x me-3"></i>
                                    <span>Disable User</span>
                                </a>
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <i class="feather-delete me-3"></i>
                                    <span>Mark as Inactive</span>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <i class="feather-trash-2 me-3"></i>
                                    <span>Delete User</span>
                                </a>
                            </div>
                        </div>
                        <a href="javascript:void(0);" class="btn btn-primary successAlertMessage">
                            <i class="feather-plus me-2"></i>
                            <span>Assign Role</span>
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

        <div class="bg-white py-3 border-bottom rounded-0 p-md-0 mb-0">
            <div class="d-md-none d-flex">
                <a href="javascript:void(0)" class="page-content-left-open-toggle">
                    <i class="feather-align-left fs-20"></i>
                </a>
            </div>
            <div class="d-flex align-items-center justify-content-between">
                <div class="nav-tabs-wrapper page-content-left-sidebar-wrapper">
                    <div class="d-flex d-md-none">
                        <a href="javascript:void(0)" class="page-content-left-close-toggle">
                            <i class="feather-arrow-left me-2"></i>
                            <span>Back</span>
                        </a>
                    </div>
                    <ul class="nav nav-tabs nav-tabs-custom-style" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profileTab">Profile</button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- [ Main Content ] start -->
        <div class="main-content">
            <div class="tab-content">
                <div class="tab-pane fade show active" id="profileTab" role="tabpanel">
                    <div class="card card-body lead-info">
                        <div class="mb-4 d-flex align-items-center justify-content-between">
                            <h5 class="fw-bold mb-0">
                                <span class="d-block mb-2">User Information :</span>
                                <span class="fs-12 fw-normal text-muted d-block">
                                    Following information for your user
                                </span>
                            </h5>
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-light-brand">Edit User</a>
                        </div>

                        <div class="row mb-4">
                            <div class="col-lg-2 fw-medium">Username</div>
                            <div class="col-lg-10">
                                <a href="javascript:void(0);">{{ $user->Username }}</a>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-lg-2 fw-medium">Email</div>
                            <div class="col-lg-10">
                                <a href="mailto:{{ $user->Email }}">{{ $user->Email }}</a>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-lg-2 fw-medium">Role</div>
                            <div class="col-lg-10">
                                <a href="javascript:void(0);">
                                    {{ $user->role->RoleName ?? 'N/A' }}
                                </a>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-lg-2 fw-medium">Created At</div>
                            <div class="col-lg-10">
                                <a href="javascript:void(0);">
                                    {{ $user->created_at->format('Y-m-d H:i') }}
                                </a>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-lg-2 fw-medium">Updated At</div>
                            <div class="col-lg-10">
                                <a href="javascript:void(0);">
                                    {{ $user->updated_at->format('Y-m-d H:i') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
@endsection
