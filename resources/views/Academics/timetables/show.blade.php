{{-- @extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">{{ $timetable->name }}</span>
                        <small>{{ $timetable->class->name }} - {{ $timetable->section->name }}</small>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <a href="{{ route('admin.timetables.edit', $timetable) }}" class="btn btn-primary">
                            <i class="feather-edit-2 me-2"></i>Edit Timetable
                        </a>
                        <a href="{{ route('admin.timetables.index') }}" class="btn btn-secondary">
                            Back to List
                        </a>
                    </div>
                </div>

                <div class="content-area-body">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <p><strong>Class:</strong> {{ $timetable->class->name }}</p>
                                    <p><strong>Section:</strong> {{ $timetable->section->name }}</p>
                                    <p><strong>Academic Year:</strong> {{ $timetable->academicYear->name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Total Slots:</strong> <span class="badge bg-info">{{ $timetable->slots->count() }}</span></p>
                                    <p><strong>Created:</strong> {{ $timetable->created_at->format('d M Y') }}</p>
                                </div>
                            </div>

                            <!-- Weekly Timetable Grid -->
                            <div class="table-responsive">
                                <table class="table table-bordered timetable-grid">
                                    <thead>
                                        <tr>
                                            <th>Period</th>
                                            @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                                                <th>{{ $day }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($periods as $period)
                                            <tr>
                                                <td><strong>{{ $period->name }}</strong></td>
                                                @foreach([1,2,3,4,5,6] as $dayOfWeek)
                                                    @php
                                                        $slot = $timetable->slots
                                                            ->where('period_id', $period->id)
                                                            ->where('day_of_week', $dayOfWeek)
                                                            ->first();
                                                    @endphp
                                                    <td class="slot-cell">
                                                        @if($slot)
                                                            <div class="slot-info">
                                                                <strong>{{ $slot->subject->name }}</strong><br>
                                                                <small>{{ $slot->staff->first_name ?? 'N/A' }} {{ $slot->staff->last_name}}</small><br>
                                                                <small class="text-muted">{{ $slot->room->name ?? '' }}</small>
                                                            </div>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    .timetable-grid th { background: #f8f9fa; font-weight: 600; }
    .slot-cell { min-height: 80px; vertical-align: top; padding: 8px; }
    .slot-info { font-size: 0.875rem; }
    .slot-info strong { color: #2563eb; }
    </style>
@endsection --}}


@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">{{ $timetable->name }}</span>
                        <small>{{ $timetable->class?->name ?? 'N/A' }} - {{ $timetable->section?->name ?? 'N/A' }}</small>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <a href="{{ route('admin.timetables.edit', $timetable) }}" class="btn btn-primary">
                            <i class="feather-edit-2 me-2"></i>Edit Timetable
                        </a>
                        <a href="{{ route('admin.timetables.index') }}" class="btn btn-secondary">
                            Back to List
                        </a>
                    </div>
                </div>

                <div class="content-area-body">
                    <div class="card stretch stretch-full mb-4">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="d-flex flex-wrap gap-4">
                                        <div class="info-group">
                                            <label class="text-muted fs-11 text-uppercase fw-bold mb-1 d-block">Course & Branch</label>
                                            <div class="fw-bold text-dark">{{ $timetable->course?->name ?? 'N/A' }}</div>
                                            <div class="fs-12 text-muted">{{ $timetable->stream?->name ?? 'N/A' }}</div>
                                        </div>
                                        <div class="info-group border-start ps-4">
                                            <label class="text-muted fs-11 text-uppercase fw-bold mb-1 d-block">Semester & Section</label>
                                            <div class="fw-bold text-dark">{{ $timetable->class?->name ?? 'N/A' }}</div>
                                            <div class="fs-12 text-muted">Section: {{ $timetable->section?->name ?? 'N/A' }}</div>
                                        </div>
                                        <div class="info-group border-start ps-4">
                                            <label class="text-muted fs-11 text-uppercase fw-bold mb-1 d-block">Entry Type</label>
                                            <span class="badge bg-soft-primary text-primary text-capitalize">{{ $timetable->entry_type }} Entry</span>
                                        </div>
                                        <div class="info-group border-start ps-4">
                                            <label class="text-muted fs-11 text-uppercase fw-bold mb-1 d-block">Academic Year</label>
                                            <div class="fs-13 fw-semibold">
                                                @if($timetable->academicYears->isNotEmpty())
                                                    {{ $timetable->academicYears->pluck('name')->join(', ') }}
                                                @else
                                                    {{ $timetable->academicYear?->name ?? 'N/A' }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-md-end border-start">
                                    <div class="d-inline-block text-start">
                                        <div class="fs-11 text-muted text-uppercase fw-bold mb-1">Statistics</div>
                                        <div class="d-flex gap-3">
                                            <div>
                                                <div class="fw-bold text-dark">{{ $timetable->slots->count() }}</div>
                                                <div class="fs-11 text-muted">Total Slots</div>
                                            </div>
                                            <div class="border-start ps-3">
                                                <div class="fw-bold text-dark">{{ $timetable->created_at->format('d M Y') }}</div>
                                                <div class="fs-11 text-muted">Created Date</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Weekly Timetable Grid -->
                    <div class="card stretch stretch-full mb-3">
                        <div class="card-body">
                            <h6 class="card-title">Weekly Schedule</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered timetable-grid">
                                    <thead>
                                        <tr>
                                            <th>Period</th>
                                            @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                                                <th>{{ $day }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($periods as $period)
                                            <tr>
                                                <td><strong>{{ $period->name }}</strong></td>
                                                @foreach ([1,2,3,4,5,6] as $dayOfWeek)
                                                    @php
                                                        $slot = $timetable->slots
                                                            ->where('period_id', $period->id)
                                                            ->where('day_of_week', $dayOfWeek)
                                                            ->first();
                                                    @endphp
                                                    <td class="slot-cell">
                                                        @if($slot)
                                                            <div class="slot-info">
                                                                <strong>{{ $slot->subject?->name ?? 'N/A' }}</strong><br>
                                                                <small>{{ $slot->staff?->first_name ?? 'N/A' }}</small><br>
                                                                <small class="text-muted">{{ $slot->room?->name ?? '' }}</small>
                                                            </div>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .timetable-grid th { 
            background: #f8f9fa; 
            font-weight: 700; 
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
            color: #6b7280;
            border-bottom: 2px solid #e5e7eb;
        }
        .slot-cell { 
            min-height: 100px; 
            vertical-align: top; 
            padding: 12px; 
            background: #fff;
            transition: background 0.2s;
        }
        .slot-cell:hover {
            background: #f9fafb;
        }
        .slot-info { 
            font-size: 0.8125rem; 
        }
        .slot-info strong { 
            color: #2563eb; 
            display: block;
            margin-bottom: 4px;
            font-size: 0.875rem;
        }
        .slot-info small {
            display: block;
            line-height: 1.4;
            color: #4b5563;
        }
        .text-muted { color: #9ca3af !important; }
        
        .badge.bg-soft-primary {
            background-color: #eff6ff;
            color: #2563eb;
            font-weight: 600;
        }
    </style>
@endsection
