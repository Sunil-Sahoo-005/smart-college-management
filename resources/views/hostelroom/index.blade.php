@extends('admin_layout')

@section('content')
<div class="nxl-content">

    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Hostel Rooms</h5>
            </div>
        </div>

        <div class="page-header-right ms-auto">
            <a href="{{ route('admin.hostelroom.create') }}" class="btn btn-primary">
                <i class="feather-plus me-2"></i> Add Room
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="card stretch stretch-full">
            <div class="card-body">

                @if($hostel->isEmpty())
                    <p class="text-center text-muted">No rooms found.</p>
                @else
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Room Number</th>
                                <th>Type</th>
                                <th>Max Beds</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($hostel as $index => $room)
                                <tr>
                                    <td>{{ $hostel->firstItem() + $index }}</td>
                                    <td>{{ $room->room_number }}</td>
                                    <td>{{ $room->type ?? '-' }}</td>
                                    <td>{{ $room->max_beds }}</td>
                                    <td>
                                        <div class="hstack gap-2 justify-content-end">
                                                    <a href="{{ route('admin.hostelroom.edit', $room) }}"
                                                        class="avatar-text avatar-md">
                                                        <i class="feather-edit-3"></i>
                                                    </a>
                                                    <div class="dropdown">
                                                        <a href="javascript:void(0)" class="avatar-text avatar-md"
                                                            data-bs-toggle="dropdown" data-bs-offset="0,21">
                                                            <i class="feather-more-horizontal"></i>
                                                        </a>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('admin.hostelroom.edit', $room) }}">
                                                                    <i class="feather-edit-3 me-3"></i>
                                                                    <span>Edit</span>
                                                                </a>
                                                            </li>
                                                            <li class="dropdown-divider"></li>
                                                            <li>
                                                                <form action="{{ route('admin.hostelroom.destroy', $room) }}"
                                                                    method="POST" style="display:inline;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="dropdown-item"
                                                                        onclick="return confirm('Are you sure?')">
                                                                        Delete
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{ $hostel->links() }}
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
