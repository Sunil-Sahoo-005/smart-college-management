@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Student Class Enrollments</span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                            @if (auth()->user()->hasPermissionTo('Create Student Class Enrollments'))
                                <a href="{{ route('admin.student_class_enrollments.create') }}" class="btn btn-primary">
                                    <i class="feather-plus me-2"></i>
                                    <span>Enroll Student</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="content-area-body">
                    @include('elements.flash')
                    <div class="card stretch stretch-full">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover" id="studentEnrollmentList">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Academic Year</th>
                                            <th>Student Type</th>
                                            <th>Residence Type</th>
                                            <th>Branch</th>
                                            <th>Semester</th>
                                            <th>Section</th>                                           
                                            <th>Date of Admission</th>
                                            <th>Date of leaving</th>
                                            <th>Status</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($enrollments as $enrollment)
                                            <tr class="single-item">
                                                <td class="project-name-td">
                                                    <div class="hstack gap-4">
                                                        <div>
                                                            <a href="{{ route('admin.student_class_enrollments.edit', $enrollment) }}"
                                                                class="text-truncate-1-line">
                                                                {{ $enrollment->student?->first_name ?? 'N/A' }}
                                                                {{ $enrollment->student?->last_name ?? '' }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>                                               
                                                <td>{{ $enrollment->academicYear?->name ?? 'N/A' }}</td>
                                                 {{-- Student Type Badge --}}
                                                <td>
                                                    @if ($enrollment->student_type)
                                                        <span class="badge bg-{{ $enrollment->student_type == 'regular' ? 'info' : 'purple' }}">
                                                            {{ ucfirst($enrollment->student_type) }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">–</span>
                                                    @endif
                                                </td>
                                                 </td>
                                                 {{-- Residence Type Badge --}}
                                                <td>
                                                    @if ($enrollment->residence_type)
                                                        <span class="badge bg-{{ $enrollment->residence_type == 'day_scholar' ? 'teal' : 'orange' }}">
                                                            {{ $enrollment->residence_type == 'day_scholar' ? 'Day Scholar' : 'Hosteller' }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">–</span>
                                                    @endif
                                                </td>

                                                <td>{{ $enrollment->stream?->name ?? 'N/A' }}</td>
                                                <td>{{ $enrollment->class?->name ?? 'N/A' }}</td>
                                                <td>{{ $enrollment->section?->name ?? 'N/A' }}</td>
                                                <td>{{ $enrollment->dateof_admission ? $enrollment->dateof_admission->format('d/m/Y') : '–' }}
                                                </td>
                                                {{-- Date of Leaving --}}
                                                <td>
                                                    @if ($enrollment->date_of_leaving)
                                                        <span class="text-danger fw-semibold">
                                                            {{ $enrollment->date_of_leaving->format('d/m/Y') }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">–</span>
                                                    @endif
                                                </td>

                                                <td>
                                                    <span
                                                        class="badge bg-{{ $enrollment->status == 'active' ? 'success' : ($enrollment->status == 'promoted' ? 'primary' : ($enrollment->status == 'transferred' ? 'warning' : 'secondary')) }}">
                                                        {{ ucfirst($enrollment->status) }}
                                                    </span>
                                               
                                                <td>
                                                    <div class="hstack gap-2 justify-content-center">
                                                        <a href="{{ route('admin.student_class_enrollments.edit', $enrollment) }}"
                                                            class="avatar-text avatar-md text-warning"
                                                            title="Edit Enrollment">
                                                            <i class="feather-edit-3"></i>
                                                        </a>
                                                        <form
                                                            action="{{ route('admin.student_class_enrollments.destroy', $enrollment) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('Are you sure you want to delete this enrollment?')">
                                                            @csrf @method('DELETE')
                                                            <button type="submit"
                                                                class="avatar-text avatar-md border-0 bg-transparent text-danger"
                                                                title="Delete Enrollment">
                                                                <i class="feather-trash-2"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                                
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center">No enrollments found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    {{ $enrollments->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
