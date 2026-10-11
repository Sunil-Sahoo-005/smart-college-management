@extends('admin_layout')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Subscriptions</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Subscriptions</li>
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
                    <div class="dropdown">
                        <a class="btn btn-icon btn-light-brand" data-bs-toggle="dropdown" data-bs-offset="0, 10"
                           data-bs-auto-close="outside">
                            <i class="feather-paperclip"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:void(0);" class="dropdown-item">
                                <i class="bi bi-filetype-pdf me-3"></i>
                                <span>PDF</span>
                            </a>
                            <a href="javascript:void(0);" class="dropdown-item">
                                <i class="bi bi-filetype-csv me-3"></i>
                                <span>CSV</span>
                            </a>
                            <a href="javascript:void(0);" class="dropdown-item">
                                <i class="bi bi-filetype-xml me-3"></i>
                                <span>XML</span>
                            </a>
                            <a href="javascript:void(0);" class="dropdown-item">
                                <i class="bi bi-filetype-txt me-3"></i>
                                <span>Text</span>
                            </a>
                            <a href="javascript:void(0);" class="dropdown-item">
                                <i class="bi bi-filetype-exe me-3"></i>
                                <span>Excel</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="javascript:void(0);" class="dropdown-item">
                                <i class="bi bi-printer me-3"></i>
                                <span>Print</span>
                            </a>
                        </div>
                    </div>
                    <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-primary">
                        <i class="feather-plus me-2"></i>
                        <span>Create Subscription</span>
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

    <!-- [ Main Content ] start -->
    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card stretch stretch-full">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover" id="subscriptionList">
                                <thead>
                                    <tr>
                                        <th>Tenant</th>
                                        <th>Plan</th>
                                        <th>Start Date</th>
                                        <th>Expiry Date</th>
                                        <th>Status</th>
                                        <th>Payment Method</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($subscriptions as $subscription)
                                        <tr class="single-item">
                                            <td class="project-name-td">
                                                <div class="hstack gap-4">
                                                    <div>
                                                        <a href="{{ route('admin.tenants.show', $subscription->tenant) }}"
                                                           class="text-truncate-1-line">
                                                            {{ $subscription->tenant->name }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $subscription->plan->name }}</td>
                                            <td>{{ $subscription->starts_at->format('Y-m-d') }}</td>
                                            <td>{{ $subscription->expires_at->format('Y-m-d') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $subscription->status == 'active' ? 'success' : ($subscription->status == 'cancelled' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($subscription->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $subscription->payment_method ?? 'N/A' }}</td>
                                            <td>
                                                <div class="hstack gap-2 justify-content-end">
                                                    <a href="{{ route('admin.subscriptions.show', $subscription) }}"
                                                       class="avatar-text avatar-md">
                                                        <i class="feather-eye"></i>
                                                    </a>
                                                    <div class="dropdown">
                                                        <a href="javascript:void(0)" class="avatar-text avatar-md"
                                                           data-bs-toggle="dropdown" data-bs-offset="0,21">
                                                            <i class="feather-more-horizontal"></i>
                                                        </a>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a class="dropdown-item"
                                                                   href="{{ route('admin.subscriptions.edit', $subscription) }}">
                                                                    <i class="feather-edit-3 me-3"></i>
                                                                    <span>Edit</span>
                                                                </a>
                                                            </li>
                                                            <li class="dropdown-divider"></li>
                                                            <li>
                                                                <form action="{{ route('admin.subscriptions.destroy', $subscription) }}"
                                                                      method="POST"
                                                                      style="display:inline;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="dropdown-item">
                                                                        Delete
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No subscriptions found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
</div>
@endsection
