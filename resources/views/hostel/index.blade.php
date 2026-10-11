@extends('admin_layout')
@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('hostel.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                            <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
                                <i class="feather-plus me-2"></i>
                                <span>Create Student</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="content-area-body">
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
                </div>
            </div>
        </div>
    </div>
@endsection
