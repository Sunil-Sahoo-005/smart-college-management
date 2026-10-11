@extends('admin_layout')

@section('content')
    {{-- <main class="nxl-container"> --}}
    <div class="nxl-content">
        <!-- [ page-header ] start -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Student Route Mapping</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Student Route Mapping</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <a href="{{ route('admin.studentroutemapping.create') }}" class="btn btn-primary">
                    <i class="feather-plus me-2"></i>
                    <span>Create Student Route</span>
                </a>
            </div>
        </div>
        <!-- [ page-header ] end -->

        <div class="main-content">
            <div class="card stretch stretch-full">
                <div class="card-header">
                    <h6 class="card-title mb-0">Student Route List</h6>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if ($studentroutemapping->isEmpty())
                        <div class="text-center py-4">
                            <p class="text-muted">No Route found.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <!-- ... same header ... -->

                            <table class="table table-hover" id="studentroutemappingList">
                                <thead>
                                    <tr>
                                        
                                        <th>Student_id</th>
                                        <th>Route_id</th>
                                        <th>Stop_name</th>
                                        <th>Transport_fee</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse ($studentroutemapping as $v)
                                <tr>
                                   

                                    <td>{{ $v->student->first_name }}{{ $v->student->last_name }}</td>

                                    <td>{{ $v->route_id }}</td>

                                    <td>{{ $v->routestops->id ?? "N/A" }}</td>

                                    <td>{{ $v->transport_fee ?? 'N/A' }}</td>

                                    <td class="text-end">
                                        <div class="hstack gap-2 justify-content-end">
                                            <a href="{{ route('admin.studentroutemapping.edit', $v->id) }}" class="avatar-text avatar-md">
                                                <i class="feather-edit-3"></i>
                                            </a>

                                            <form action="{{ route('admin.studentroutemapping.destroy', $v->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="avatar-text avatar-md border-0"
                                                    onclick="return confirm('Are you sure?')">
                                                    <i class="feather-trash-2"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No routes found.</td>
                                </tr>
                                @endforelse
                                </tbody>

                            </table>


                            {{ $studentroutemapping->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{-- </main> --}}
@endsection
