@extends('admin_layout')
@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Academics.sidebar')
        <div class="content-area">
            <div class="content-area-header bg-white sticky-top">
                <h5 class="fw-bold mb-0">
                    <a href="{{ route('admin.staff.show', $staff->id) }}" class="btn btn-sm btn-outline-secondary me-3">
                        <i class="feather-arrow-left"></i>
                    </a>
                    Take Attendance - {{ $staff->first_name }} {{ $staff->last_name }}
                </h5>
            </div>
            
            <div class="content-area-body">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h6 class="card-title">Select Class/Stream/Section to Take Attendance</h6>
                    </div>
                    <div class="card-body">
                        @if($classGroups->isEmpty())
                            <div class="text-center py-5">
                                <i class="feather-calendar fs-1 text-muted mb-3"></i>
                                <h5 class="text-muted">No Classes Assigned</h5>
                                <p class="text-muted">This staff member has no scheduled classes.</p>
                            </div>
                        @else
                            <div class="row g-4">
                                @foreach($classGroups as $group)
                                    <div class="col-lg-4 col-md-6">
                                        <div class="card h-100 border-0 shadow-sm hover-shadow">
                                            <div class="card-body p-4 text-center">
                                                <div class="mb-3">
                                                    <span class="badge bg-primary fs-6 px-3 py-2">{{ $group['class']->name }}</span>
                                                    @if($group['stream'])
                                                        <span class="badge bg-success ms-2 fs-6 px-3 py-2">{{ $group['stream']->name }}</span>
                                                    @endif
                                                    @if($group['section'])
                                                        <span class="badge bg-info ms-2 fs-6 px-3 py-2">{{ $group['section']->name }}</span>
                                                    @endif
                                                </div>
                                                <h5 class="mb-2">{{ $group['slot_count'] }} Periods</h5>
                                                <p class="text-muted mb-3 small">Click to mark attendance</p>
                                                <a href="{{ route('admin.staff.attendance', [
                                                    'staff' => $staff->id, 
                                                    'class' => $group['class_id'],
                                                    'stream' => $group['stream_id'] ?? '',
                                                    'section' => $group['section_id'] ?? ''
                                                ]) }}" 
                                                class="btn btn-primary w-100">
                                                    <i class="feather-users me-2"></i>Mark Attendance
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
