@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Accountants.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Scholarships List</span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                            {{-- @if(auth()->user()->hasPermissionTo('Create Scholarships')) --}}
                                <a href="{{ route('scholarships.create') }}" class="btn btn-primary">
                                    <i class="feather-plus me-2"></i>
                                    <span>Create Scholarship</span>
                                </a>
                            {{-- @endif --}}
                        </div>
                    </div>
                </div>

                <div class="content-area-body">
                    @include('elements.flash')
                    <div class="card stretch stretch-full">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover" id="scholarshipList">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Value</th>
                                            <th>Source</th>
                                            <th>Caste Category</th>
                                            <th>Status</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($scholarships as $scholarship)
                                            <tr class="single-item">
                                                <td class="project-name-td">
                                                    <div class="hstack gap-4">
                                                        <div>
                                                            @if(auth()->user()->hasPermissionTo('Edit Scholarships'))
                                                                <a href="{{ route('scholarships.edit', $scholarship->id) }}"
                                                                   class="text-truncate-1-line fw-semibold">
                                                                    {{ $scholarship->name }}
                                                                </a>
                                                            @else
                                                                <span class="text-truncate-1-line fw-semibold">{{ $scholarship->name }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $scholarship->type == 'percentage' ? 'bg-soft-info text-info' : 'bg-soft-warning text-warning' }}">
                                                        {{ ucfirst($scholarship->type) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($scholarship->type == 'percentage')
                                                        {{ number_format($scholarship->value, 0) }}%
                                                    @else
                                                        ₹{{ number_format($scholarship->value, 2) }}
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge {{ $scholarship->source == 'government' ? 'bg-soft-success text-success' : ($scholarship->source == 'nsp' ? 'bg-soft-primary text-primary' : ($scholarship->source == 'college' ? 'bg-soft-dark text-dark' : 'bg-soft-danger text-danger')) }}">
                                                        {{ strtoupper($scholarship->source) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-soft-secondary text-secondary">
                                                        {{ $scholarship->caste_category }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($scholarship->is_active)
                                                        <span class="badge bg-success text-white">Active</span>
                                                    @else
                                                        <span class="badge bg-danger text-white">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="hstack gap-2 justify-content-center">
                                                        {{-- @if(auth()->user()->hasPermissionTo('Edit Scholarships')) --}}
                                                            <a href="{{ route('scholarships.edit', $scholarship->id) }}"
                                                               class="avatar-text avatar-md text-warning" title="Edit Scholarship">
                                                                <i class="feather-edit-3"></i>
                                                            </a>
                                                        {{-- @endif --}}

                                                        {{-- @if(auth()->user()->hasPermissionTo('Delete Scholarships')) --}}
                                                            <form action="{{ route('scholarships.destroy', $scholarship->id) }}" method="POST" class="d-inline"
                                                                  onsubmit="return confirm('Are you sure you want to delete this scholarship?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="avatar-text avatar-md text-danger border-0 bg-transparent"
                                                                        title="Delete Scholarship">
                                                                    <i class="feather-trash-2"></i>
                                                                </button>
                                                            </form>
                                                        {{-- @endif --}}
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted">
                                                    <i class="feather-award fs-1 mb-3 d-block opacity-50"></i>
                                                    No scholarships found.
                                                    {{-- @if(auth()->user()->hasPermissionTo('Create Scholarships')) --}}
                                                        <div class="mt-3">
                                                            <a href="{{ route('scholarships.create') }}" class="btn btn-sm btn-primary">
                                                                Create your first scholarship
                                                            </a>
                                                        </div>
                                                    {{-- @endif --}}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{ $scholarships->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
