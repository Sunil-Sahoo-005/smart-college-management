@extends('admin_layout')

@section('content')
    {{-- <main class="nxl-container"> --}}
    <div class="nxl-content">
        <!-- [ page-header ] start -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Driver</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Driver</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <a href="{{ route('admin.driver.create') }}" class="btn btn-primary">
                    <i class="feather-plus me-2"></i>
                    <span>Create Driver</span>
                </a>
            </div>
        </div>
        <!-- [ page-header ] end -->

        <div class="main-content">
            <div class="card stretch stretch-full">
                <div class="card-header">
                    <h6 class="card-title mb-0">Driver List</h6>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if ($driver->isEmpty())
                        <div class="text-center py-4">
                            <p class="text-muted">No driver found.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <!-- ... same header ... -->

                            <table class="table table-hover" id="driverList">
                                <thead>
                                    <tr>
                                        <th>Tenant_id</th>
                                        <th>First_name</th>
                                        <th>Last_name</th>
                                        <th>phone</th>
                                        <th>license_number</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse ($driver as $v)
                                <tr>
                                    <td>{{ $v->tenant_id }}</td>

                                    <td>{{ $v->first_name }}</td>

                                    <td>{{ $v->last_name }}</td>

                                    <td>{{ $v->phone }}</td>

                                    <td>{{ $v->license_number ?? 'N/A' }}</td>

                                    <td class="text-end">
                                        <div class="hstack gap-2 justify-content-end">
                                            <a href="{{ route('admin.driver.edit', $v->id) }}" class="avatar-text avatar-md">
                                                <i class="feather-edit-3"></i>
                                            </a>

                                            <form action="{{ route('admin.driver.destroy', $v->id) }}" method="POST">
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
                                    <td colspan="6" class="text-center">No vehicle found.</td>
                                </tr>
                                @endforelse
                                </tbody>

                            </table>


                            {{ $driver->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{-- </main> --}}
@endsection
