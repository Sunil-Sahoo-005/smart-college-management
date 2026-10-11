@extends('hr.layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">

        <div class="main-content  d-flex">
            @include('hr.sidebar')
            <div class="content-area" data-scrollbar-target="#psScrollbarInit">


                <div class="content-area-header bg-white px-4 py-3 border-bottom shadow-sm mb-2">
                    <div class="page-header-left d-flex align-items-center">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Leave Types</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('hr.dashboard') }}">HR</a></li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('hr.tenant.dashboard', $tenantKey) }}">{{ $tenant->name }}</a></li>
                            <li class="breadcrumb-item">Leave Types</li>
                        </ul>
                    </div>
                    <div class="page-header-right ms-auto">
                        @if (auth()->user()->hasPermissionTo('Create LeaveTypes'))
                        <a href="{{ route('hr.leave-types.create', $tenantKey) }}" class="btn btn-primary">
                            <i class="feather-plus me-2"></i> Add Leave Type
                        </a>
                        @endif
                    </div>
                </div>
                @include('elements.flash')
                <div class="content-area-body">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Days</th>
                                            <th>Carry Forward</th>
                                            <th>Paid</th>
                                            <th>Applicable To</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($leaveTypes as $leaveType)
                                            <tr>
                                                <td>{{ $loop->iteration + ($leaveTypes->currentPage() - 1) * $leaveTypes->perPage() }}
                                                </td>
                                                <td>{{ $leaveType->name }}</td>
                                                <td>{{ $leaveType->allowed_days }}</td>
                                                <td>{{ $leaveType->carry_forward ? 'Yes' : 'No' }}</td>
                                                <td>{{ $leaveType->is_paid ? 'Yes' : 'No' }}</td>
                                                <td>{{ str_replace('_', ' ', ucfirst($leaveType->applicable_to)) }}</td>
                                                <td class="text-center">
                                                    <div class="hstack gap-2 justify-content-center">
                                                        @if (auth()->user()->hasPermissionTo('Edit LeaveTypes'))
                                                        <a href="{{ route('hr.leave-types.edit', [$tenantKey, $leaveType->id]) }}"
                                                            class="avatar-text avatar-md text-warning"><i class="feather-edit"></i></a>
                                                        @endif

                                                        @if (auth()->user()->hasPermissionTo('Delete LeaveTypes'))
                                                        <form
                                                            action="{{ route('hr.leave-types.destroy', [$tenantKey, $leaveType->id]) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('Delete this leave type?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="avatar-text avatar-md text-danger"><i class="feather-trash-2"></i></button>
                                                        </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted">No leave types found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                {{ $leaveTypes->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
