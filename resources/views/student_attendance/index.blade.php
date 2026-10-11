@extends('admin_layout')

@section('content')
<div class="nxl-content">
    <!-- [ page-header ] start -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Student Attendance</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Student Attendance</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                <a href="{{ route('admin.student_attendance.create') }}" class="btn btn-primary">
                    <i class="feather-plus me-2"></i>
                    <span>Mark Attendance</span>
                </a>
            </div>
        </div>
    </div>
    <!-- [ page-header ] end -->

    <!-- [ Main Content ] start -->
    <div class="main-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="card stretch stretch-full">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover" id="attendanceList">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Class</th>
                                        <th>Section</th>
                                        <th>Date</th>
                                        <th>Period</th>
                                        <th>Status</th>
                                        <th>Marked By</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($attendanceRecords as $record)
                                        <tr class="single-item">
                                            <td class="project-name-td">
                                                <div class="hstack gap-4">
                                                    <div>
                                                        <a href="#" class="text-truncate-1-line">
                                                            {{ $record->student?->first_name ?? 'N/A' }} {{ $record->student?->last_name ?? '' }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $record->class?->name ?? 'N/A' }}</td>
                                            <td>{{ $record->section?->name ?? 'N/A' }}</td>
                                            <td>{{ $record->date->format('d/m/Y') }}</td>
                                            <td>{{ $record->period?->name ?? 'Full Day' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $record->status == 'present' ? 'success' : ($record->status == 'late' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($record->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $record->markedBy?->name ?? 'N/A' }}</td>
                                            <td>
                                                <div class="hstack gap-2 justify-content-end">
                                                    <button class="avatar-text avatar-md btn-edit-attendance" 
                                                            data-id="{{ $record->id }}"
                                                            data-student="{{ $record->student_id }}"
                                                            data-class="{{ $record->class_id }}"
                                                            data-section="{{ $record->section_id }}"
                                                            data-date="{{ $record->date->format('Y-m-d') }}"
                                                            data-period="{{ $record->period_id }}"
                                                            data-status="{{ $record->status }}"
                                                            data-remarks="{{ $record->remarks ?? '' }}">
                                                        <i class="feather-edit-3"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No attendance records found.</td>
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
    <!-- [ Main Content ] end -->

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
</div>
@endsection
