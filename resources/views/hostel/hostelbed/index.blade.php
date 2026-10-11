@extends('admin_layout')
@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('hostel.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <!-- Header -->
                <div class="content-area-header bg-white sticky-top">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Hostel Bed</h6>
                    </div>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                            @if (auth()->user()->hasPermissionTo('Create Hostel Bed'))
                                <a href="{{ route('admin.hostelbed.create') }}" class="btn btn-primary">
                                    <i class="feather-plus me-2"></i>
                                    <span>Create Bed</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Body -->
                <div class="content-area-body">
                    @include('elements.flash')
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            @if ($beds->isEmpty())
                                <div class="text-center py-4">
                                    <p class="text-muted">No beds found.</p>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Sl No</th>
                                                <th>Hostel Name</th> <!-- 🔥 FIXED -->
                                                <th>Room Number</th>
                                                <th>Bed Number</th>
                                                <th>Status</th>
                                                <th>Amount</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($beds as $index => $bed)
                                                <tr>
                                                    <td>{{ $beds->firstItem() + $index }}</td>

                                                    {{-- 🔥 FIXED: Show Hostel Name --}}
                                                    <td>
                                                        @if ($bed->room && $bed->room->hostel)
                                                            {{ $bed->room->hostel->name }}
                                                            <small
                                                                class="d-block text-muted">({{ $bed->room->hostel->type }})</small>
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    </td>

                                                    <td>{{ $bed->room->room_number ?? 'N/A' }}</td>
                                                    <td>{{ $bed->bed_number }}</td>
                                                    <td>
                                                        <span
                                                            class="badge bg-{{ $bed->status == 'vacant' ? 'success' : 'danger' }} px-3 py-2">
                                                            {{ ucfirst($bed->status) }}
                                                        </span>
                                                    </td>
                                                    <td>₹{{ number_format($bed->amount, 2) }}</td>
                                                    <td>
                                                        <div class="hstack gap-2 justify-content-end">
                                                            {{-- Edit --}}
                                                            <a href="{{ route('admin.hostelbed.edit', $bed->id) }}"
                                                                class="avatar-text avatar-md text-primary" title="Edit">
                                                                <i class="feather-edit-3"></i>
                                                            </a>
                                                            {{-- Delete --}}
                                                            <form action="{{ route('admin.hostelbed.destroy', $bed->id) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('Are you sure you want to delete this bed?')"
                                                                class="d-inline">
                                                                @csrf @method('DELETE')
                                                                <button type="submit"
                                                                    class="avatar-text avatar-md border-0 bg-transparent text-danger"
                                                                    title="Delete">
                                                                    <i class="feather-trash-2"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!-- Pagination -->
                                <div class="mt-3">
                                    {{ $beds->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
