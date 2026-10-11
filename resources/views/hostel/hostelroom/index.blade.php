@extends('admin_layout')
@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('hostel.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">

                {{-- HEADER --}}
                <div class="content-area-header bg-white sticky-top d-flex align-items-center">
                    <div class="card-header border-0">
                        <h6 class="card-title mb-0">Hostel Room</h6>
                    </div>

                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                            @if(auth()->user()->hasPermissionTo('Create Hostel Room'))
                            <a href="{{ route('admin.hostelroom.create') }}" class="btn btn-primary">
                                <i class="feather-plus me-2"></i>
                                <span>Create Room</span>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- BODY --}}
                <div class="content-area-body">
                @include('elements.flash')
                    <div class="card stretch stretch-full">
                        <div class="card-body">

                            @if($rooms->isEmpty())
                                <div class="text-center py-4">
                                    <p class="text-muted">No rooms found.</p>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead>
                                             <tr>
                                                <th>Sl No</th>
                                                <th>Room Number</th>
                                                <th>Hostel Name</th>
                                                <th>Hostel Type</th>
                                                <th>Room Type</th>
                                                <th>Max Beds</th>
                                                <th class="text-end">Actions</th>
                                             </tr>
                                        </thead>

                                        <tbody>
                                            @foreach($rooms as $index => $room)
                                                <tr>
                                                    <td>{{ $rooms->firstItem() + $index }}</td>

                                                    <td>
                                                        <a href="{{ route('admin.hostelroom.edit', $room) }}">
                                                            {{ $room->room_number }}
                                                        </a>
                                                    </td>

                                                    <td>
                                                        <a href="{{ route('admin.hostel.edit', $room->hostel) }}">
                                                            {{ $room->hostel->name ?? '-' }}
                                                        </a>
                                                    </td>

                                                    <td>
                                                        @if($room->hostel->type == 'boys')
                                                            <span class="badge bg-primary">Boys Hostel</span>
                                                        @elseif($room->hostel->type == 'girls')
                                                            <span class="badge bg-danger">Girls Hostel</span>
                                                        @else
                                                            <span class="badge bg-secondary">Mixed Hostel</span>
                                                        @endif
                                                    </td>

                                                    <td>
                                                        <span class="badge {{ $room->type == 'ac' ? 'bg-success' : 'bg-warning' }}">
                                                            {{ $room->type == 'ac' ? 'AC Room' : 'Non-AC Room' }}
                                                        </span>
                                                    </td>

                                                    <td>{{ $room->max_beds }}</td>

                                                    {{-- ACTIONS --}}
                                                    <td class="text-end">
                                                        <div class="hstack gap-2 justify-content-end">

                                                            {{-- Edit Icon --}}
                                                            @if(auth()->user()->hasPermissionTo('Edit Hostel Room'))
                                                                <a href="{{ route('admin.hostelroom.edit', $room) }}"
                                                                   class="avatar-text avatar-md"
                                                                   title="Edit">
                                                                    <i class="feather-edit-3"></i>
                                                                </a>
                                                            @endif

                                                            {{-- Delete Icon --}}
                                                            @if(auth()->user()->hasPermissionTo('Delete Hostel Room'))
                                                                <form action="{{ route('admin.hostelroom.destroy', $room) }}"
                                                                      method="POST"
                                                                      onsubmit="return confirm('Are you sure you want to delete this room?')">
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

                                    {{-- PAGINATION --}}
                                    <div class="mt-3">
                                        {{ $rooms->links() }}
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