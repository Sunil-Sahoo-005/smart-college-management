@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Accountants.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Assign Fee Plan</span>
                    </h5>
                </div>

                <div class="content-area-body">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <form method="GET" class="mb-3">
                                <div class="row">
                                    <div class="col-md-8">
                                        <input type="text" name="search" class="form-control"
                                               placeholder="Search Student (name / roll)"
                                               value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary">Search</button>
                                    </div>
                                </div>
                            </form>

                            <form method="POST" action="{{ route('admin.student-fee-plans.assign') }}">
                                @csrf

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Student</label>
                                        <select name="student_id" class="form-select" required>
                                            <option value="">Select Student</option>
                                            @foreach ($students as $student)
                                                <option value="{{ $student->id }}">
                                                    {{ $student->first_name }} {{ $student->last_name }}
                                                    ({{ $student->roll_number ?? 'N/A' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Fee Structure</label>
                                        <select name="fee_structure_id" class="form-select" required>
                                            <option value="">Select Fee Structure</option>
                                            @foreach ($feeStructures as $fs)
                                                <option value="{{ $fs->id }}">
                                                    {{ $fs->name }} – {{ $fs->class?->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Start Date</label>
                                        <input type="date" name="start_date" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">End Date</label>
                                        <input type="date" name="end_date" class="form-control" required>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    Assign Fee Plan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
