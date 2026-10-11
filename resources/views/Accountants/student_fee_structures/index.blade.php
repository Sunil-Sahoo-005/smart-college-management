@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Accountants.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Student Fee Structures</span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        @if(auth()->user()->hasPermissionTo('Create Student Fee Plans'))
                        <a href="{{ route('admin.student_fee_structures.create') }}" class="btn btn-primary">
                            <i class="feather-plus me-2"></i>
                            <span>Create Plan</span>
                        </a>
                        @endif
                    </div>
                </div>

                <div class="content-area-body">
                    @include('elements.flash')
                    <div class="card stretch stretch-full">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Student</th>
                                            <th>Branch</th>
                                            <th>Semester</th>
                                            <th>Academic Year</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($studentFeeStructures as $plan)
                                            <tr>
                                                <td>{{ $plan->student?->first_name }} {{ $plan->student?->last_name }}</td>
                                                <td>{{ $plan->feeStructure?->stream?->name ?? 'N/A' }}</td>
                                                <td>{{ $plan->feeStructure?->class?->name ?? 'N/A' }}</td>
                                                <td>{{ $plan->academicYear?->name ?? 'N/A' }}</td>
                                                <td>
                                                    <div class="hstack gap-2 justify-content-center">
                                                        <a href="{{ route('admin.student_fee_structures.edit_assignment', $plan->id) }}"
                                                            class="avatar-text avatar-md text-warning"
                                                            title="Edit Installment">
                                                            <i class="feather-edit-3"></i>
                                                        </a>
                                                        <form
                                                            action="{{ route('admin.student_fee_structures.destroy', $plan) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('Are you sure you want to delete this student fee structure?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="avatar-text avatar-md text-danger"
                                                                title="Delete Installment">
                                                                <i class="feather-trash-2"></i>
                                                            </button>
                                                        </form>
                                                        {{-- <div class="hstack gap-2">
                                                        <a href="{{ route('admin.student_fee_structures.edit_assignment', $plan->id) }}"
                                                           class="btn btn-sm btn-outline-primary">
                                                            Edit Assignment
                                                        </a>
                                                        <form action="{{ route('admin.student_fee_structures.destroy', $plan) }}"
                                                              method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                    onclick="return confirm('Are you sure?')">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    </div> --}}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No student fee structures found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    {{ $studentFeeStructures->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
