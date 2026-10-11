@extends('admin_layout')

@section('content')
    {{-- <main class="nxl-container"> --}}
    <div class="nxl-content">
        <!-- [ page-header ] start -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Routes</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Routes</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <a href="{{ route('admin.routes.create') }}" class="btn btn-primary">
                    <i class="feather-plus me-2"></i>
                    <span>Create Routes</span>
                </a>
            </div>
        </div>
        <!-- [ page-header ] end -->

        <div class="main-content">
            <div class="card stretch stretch-full">
                <div class="card-header">
                    <h6 class="card-title mb-0">Routes List</h6>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if ($routes->isEmpty())
                        <div class="text-center py-4">
                            <p class="text-muted">No Route found.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <!-- ... same header ... -->

                            <table class="table table-hover" id="driverList">
                                <thead>
                                    <tr>
                                        <th>Tenant_id</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Start_point</th>
                                        <th>End_point</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse ($routes as $v)
                                <tr>
                                    <td>{{ $v->tenant_id }}</td>

                                    <td>{{ $v->name }}</td>

                                    <td>{{ $v->description }}</td>

                                    <td>{{ $v->start_point }}</td>

                                    <td>{{ $v->end_point ?? 'N/A' }}</td>

                                    <td class="text-end">
                                        <div class="hstack gap-2 justify-content-end">
                                            <a href="{{ route('admin.routes.edit', $v->id) }}" class="avatar-text avatar-md">
                                                <i class="feather-edit-3"></i>
                                            </a>

                                            <form action="{{ route('admin.routes.destroy', $v->id) }}" method="POST">
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


                            {{ $routes->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{-- </main> --}}
@endsection
