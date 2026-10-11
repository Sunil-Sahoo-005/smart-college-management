@extends('admin_layout')
@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')

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
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover" id="studentList">
                                    <thead>
                                        <tr>
                                            <th>Admission #</th>
                                            <th>Name</th>
                                            <th>Gender</th>
                                            <th>DOB</th>
                                            <th>Status</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($students as $student)
                                            <tr class="single-item">
                                                <td class="project-name-td">
                                                    <div class="hstack gap-4">
                                                        <div>
                                                            <a href="{{ route('admin.students.show', $student) }}"
                                                                class="text-truncate-1-line">
                                                                {{ $student->admission_number }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                                <td>{{ ucfirst($student->gender ?? 'N/A') }}</td>
                                                <td>{{ $student->dob ? $student->dob->format('Y-m-d') : 'N/A' }}</td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $student->status == 'active' ? 'success' : ($student->status == 'transfer_out' ? 'warning' : 'secondary') }}">
                                                        {{ ucfirst($student->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="hstack gap-2 justify-content-end">
                                                        <a href="{{ route('admin.students.show', $student) }}"
                                                            class="avatar-text avatar-md">
                                                            <i class="feather-eye"></i>
                                                        </a>
                                                        <div class="dropdown">
                                                            <a href="javascript:void(0)" class="avatar-text avatar-md"
                                                                data-bs-toggle="dropdown" data-bs-offset="0,21">
                                                                <i class="feather-more-horizontal"></i>
                                                            </a>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <a class="dropdown-item"
                                                                        href="{{ route('admin.students.edit', $student) }}">
                                                                        <i class="feather-edit-3 me-3"></i>
                                                                        <span>Edit</span>
                                                                    </a>
                                                                </li>
                                                                <li class="dropdown-divider"></li>
                                                                <li>
                                                                    <form
                                                                        action="{{ route('admin.students.destroy', $student) }}"
                                                                        method="POST" style="display:inline;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="dropdown-item">
                                                                            Delete
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No students found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
