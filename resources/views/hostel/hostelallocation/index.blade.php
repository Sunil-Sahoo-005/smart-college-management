@extends('admin_layout')
@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('hostel.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            <!-- HEADER -->
            <div class="content-area-header bg-white sticky-top">
                <div class="card-header">
                    <h6 class="card-title mb-0">Hostel Allocation List</h6>
                </div>

                <div class="page-header-right ms-auto">
                    <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                        @if(auth()->user()->hasPermissionTo('Create Hostel Allocation'))
                        <a href="{{ route('admin.hostelallocation.create') }}" class="btn btn-primary">
                            <i class="feather-plus me-2"></i>
                            <span>Add Allocation</span>
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- BODY -->
            <div class="content-area-body">
            @include('elements.flash')
                <div class="card stretch stretch-full">
                    <div class="card-body">

                        @if($allocations->isEmpty())
                            <div class="text-center py-4">
                                <p class="text-muted">No allocations found.</p>
                            </div>
                        @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Room</th>
                                        <th>Bed</th>
                                        <th>From Date</th>
                                        <th>To Date</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allocations as $row)
                                    <tr>
                                        <td>{{ $row->student->first_name ?? 'N/A' }} {{ $row->student->last_name ?? 'N/A' }}</td>
                                        <td>{{ $row->room->room_number ?? 'N/A' }}</td>
                                        <td>{{ $row->bed->bed_number ?? 'N/A' }}</td>
                                        <td>{{ $row->from_date }}</td>
                                        <td>{{ $row->to_date ?? '-' }}</td>

                                        <!-- ACTIONS -->
                                    <td>
                                        <div class="hstack gap-2 justify-content-end">

                                            {{-- Edit Icon --}}
                                            @if(auth()->user()->hasPermissionTo('Edit Hostel Allocation'))
                                                <a href="{{ route('admin.hostelallocation.edit', $row->id) }}"
                                                   class="avatar-text avatar-md"
                                                   title="Edit">
                                                    <i class="feather-edit-3"></i>
                                                </a>
                                            @endif

                                            {{-- Delete Icon --}}
                                            @if(auth()->user()->hasPermissionTo('Delete Hostel Allocation'))
                                                <form action="{{ route('admin.hostelallocation.destroy', $row->id) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('Are you sure?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="avatar-text avatar-md border-0 bg-transparent text-danger"
                                                            title="Delete">
                                                        <i class="feather-trash-2"></i>
                                                    </button>
                                                </form>
                                            @endif

                                        </div>
                                    </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- Pagination -->
                            <div class="mt-3">
                                {{ $allocations->links() }}
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection