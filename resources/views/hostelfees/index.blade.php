@extends('admin_layout')

@section('content')




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
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Beds</li>
            </ul>
        </div>

        <div class="page-header-right ms-auto">
            <a href="{{ route('admin.hostelfees.create') }}" class="btn btn-primary">
                <i class="feather-plus me-2"></i> Add Bed
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h6 class="card-title mb-0">Bed List</h6>
            </div>

            <div class="card-body">

              

                
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tenant ID</th>
                                    <th>Student ID</th>
                                    <th>Amount</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th width="130">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                    @forelse($fees as $fee)
                                    <tr>
                                        <td>{{ $fee->id }}</td>
                                        <td>{{ $fee->tenant_id }}</td>
                                        <td>{{ $fee->student_id }}</td>
                                        <td>{{ $fee->amount }}</td>
                                        <td>{{ $fee->due_date }}</td>
                                        <td>{{ ucfirst($fee->status) }}</td>
                                        <td>
                                            <a href="{{ route('admin.hostelfees.edit',$fee->id) }}"
                                            class="btn btn-sm btn-warning">Edit</a>

                                            <form action="{{ route('admin.hostelfees.destroy',$fee->id) }}"
                                                method="POST" style="display:inline">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger"
                                                onclick="return confirm('Delete?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                   
                                     @endforeach
                                </tbody>
                        </table>
                    </div>

                  
              

            </div> {{-- card-body --}}
        </div> {{-- card --}}
    </div> {{-- main-content --}}
</div> {{-- nxl-content --}}
@endsection
    



<!-- <div class="container-fluid">
    <h4 class="mb-3">Hostel Fees</h4>

    <a href="{{ route('admin.hostelfees.create') }}" class="btn btn-primary mb-3">
        Add Hostel Fee
    </a>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tenant ID</th>
                <th>Student ID</th>
                <th>Amount</th>
                <th>Due Date</th>
                <th>Status</th>
                <th width="130">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($fees as $fee)
            <tr>
                <td>{{ $fee->id }}</td>
                <td>{{ $fee->tenant_id }}</td>
                <td>{{ $fee->student_id }}</td>
                <td>{{ $fee->amount }}</td>
                <td>{{ $fee->due_date }}</td>
                <td>{{ ucfirst($fee->status) }}</td>
                <td>
                    <a href="{{ route('admin.hostelfees.edit',$fee->id) }}"
                       class="btn btn-sm btn-warning">Edit</a>

                    <form action="{{ route('admin.hostelfees.destroy',$fee->id) }}"
                          method="POST" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger"
                          onclick="return confirm('Delete?')">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">No records found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div> -->
@endsection
