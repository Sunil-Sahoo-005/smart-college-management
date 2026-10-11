@extends('admin_layout')
@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Accountants.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                             @if(auth()->user()->hasPermissionTo('Create Student Fee Plans'))
                            <a href="{{ route('admin.student-fee-plans.assign') }}" class="btn btn-primary">
                                <i class="feather-plus me-2"></i>
                                <span>Assign Fee Plan</span>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="content-area-body">
                    <div class="card stretch stretch-full">
                        <div class="card-body p-0">
                            {{-- <div class="card-header ">
                                <form method="GET" class="mb-3">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <select name="student_id" class="form-select">
                                                <option value="">All Students</option>
                                                @foreach ($students as $student)
                                                    <option value="{{ $student->id }}"
                                                        {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                                        {{ $student->first_name }} {{ $student->last_name }}
                                                        ({{ $student->roll_number }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-5">
                                            <select name="fee_structure_id" class="form-select">
                                                <option value="">All Fee Structures</option>
                                                @foreach ($feeStructures as $fs)
                                                    <option value="{{ $fs->id }}"
                                                        {{ request('fee_structure_id') == $fs->id ? 'selected' : '' }}>
                                                        {{ $fs->name }} – {{ $fs->class?->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit" class="btn btn-primary">Filter</button>
                                        </div>
                                    </div>
                                </form>
                            </div> --}}
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Student</th>
                                                <th>Fee Structure</th>
                                                <th>Period</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($plans as $plan)
                                                <tr>
                                                    <td>{{ $plan->student?->first_name }} {{ $plan->student?->last_name }}
                                                        ({{ $plan->student?->roll_number }})</td>
                                                    <td>{{ $plan->feeStructure?->name }}</td>
                                                    <td>
                                                        {{ \Carbon\Carbon::parse($plan->start_date)->format('d M Y') }}
                                                        –
                                                        {{ \Carbon\Carbon::parse($plan->end_date)->format('d M Y') }}
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.student-fee-plans.edit', $plan) }}"
                                                            class="btn btn-sm btn-warning">Edit</a>
                                                        <form method="POST"
                                                            action="{{ route('admin.student-fee-plans.destroy', $plan) }}"
                                                            class="d-inline"
                                                            onsubmit="return confirm('Remove this fee plan?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="btn btn-sm btn-danger">Remove</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                {{ $plans->links() }}
                            {{-- </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
