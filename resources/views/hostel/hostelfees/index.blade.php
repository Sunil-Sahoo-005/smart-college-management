@extends('admin_layout')
@section('content')

<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('hostel.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            <!-- HEADER -->
            <div class="content-area-header bg-white sticky-top">
                <div class="card-header">
                    <h6 class="card-title mb-0">Hostel Fees</h6>
                </div>

                <div class="page-header-right ms-auto">
                    <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                        <a href="{{ route('admin.hostelfees.create') }}" class="btn btn-primary">
                            <i class="feather-plus me-2"></i>
                            <span>Add Fee</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- BODY -->
            <div class="content-area-body">
                <div class="card stretch stretch-full">
                    <div class="card-body">

                        {{-- Flash Messages --}}
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        @if($fees->isEmpty())
                            <div class="text-center py-4">
                                <p class="text-muted">No hostel fees found.</p>
                            </div>
                        @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Amount</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($fees as $row)
                                    <tr>
                                        <td>
                                            {{ $row->student->first_name ?? 'N/A' }}
                                            {{ $row->student->last_name ?? '' }}
                                        </td>

                                        <td>₹ {{ number_format($row->amount, 2) }}</td>

                                        <td>{{ $row->due_date }}</td>

                                        <td>
                                            <span class="badge {{ $row->status === 'paid' ? 'bg-success' : 'bg-warning' }}">
                                                {{ ucfirst($row->status) }}
                                            </span>
                                        </td>

                                        <!-- ACTIONS -->
                                      <td>
                                        <div class="hstack gap-2 justify-content-end">

                                            {{-- Edit Icon --}}
                                            <a href="{{ route('admin.hostelfees.edit', $row->id) }}"
                                            class="avatar-text avatar-md"
                                            title="Edit">
                                                <i class="feather-edit-3"></i>
                                            </a>

                                            {{-- Delete Icon --}}
                                            <form action="{{ route('admin.hostelfees.destroy', $row->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')

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
                        @endif

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
