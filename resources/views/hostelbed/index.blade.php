@extends('admin_layout')

@section('content')
<div class="nxl-content">

    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Hostel Beds</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">Home</a>
                </li>
                <li class="breadcrumb-item">Beds</li>
            </ul>
        </div>

        <div class="page-header-right ms-auto">
            <a href="{{ route('admin.hostelbed.create') }}" class="btn btn-primary">
                <i class="feather-plus me-2"></i>
                Add Bed
            </a>
        </div>
    </div>
    <!-- End Page Header -->

    <div class="main-content">
        <div class="card stretch stretch-full">

            <div class="card-header">
                <h6 class="card-title mb-0">Bed List</h6>
            </div>

            <div class="card-body">

                {{-- Success / Error Messages --}}
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if($beds->isEmpty())
                    <div class="text-center py-5">
                        <p class="text-muted">No beds found.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>sl no</th>
                                    <th>Room Number</th>
                                    <th>Bed Number</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($beds as $index => $bed)
                                    <tr>
                                        <td>{{ $beds->firstItem() + $index }}</td>

                                        <td>{{ $bed->room->room_number ?? 'N/A' }}</td>

                                        <td>{{ $bed->bed_number }}</td>

                                        <td>
                                            <span class="badge bg-{{ $bed->status == 'vacant' ? 'success' : 'danger' }}">
                                                {{ ucfirst($bed->status) }}
                                            </span>
                                        </td>

                                        <!-- ✅ Actions EXACT SAME as Hostel List -->
                                        <td>
                                            <div class="d-flex justify-content-end align-items-center gap-2">
                                                <a href="{{ route('admin.hostelbed.edit', $bed->id) }}"
                                                   class="btn btn-sm btn-primary">
                                                    Edit
                                                </a>

                                                <form action="{{ route('admin.hostelbed.destroy', $bed->id) }}"
                                                      method="POST" class="m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Are you sure you want to delete this bed?')">
                                                        Delete
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

            </div> {{-- card-body --}}
        </div> {{-- card --}}
    </div> {{-- main-content --}}
</div> {{-- nxl-content --}}
@endsection
