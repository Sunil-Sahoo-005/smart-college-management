@extends('admin_layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Academics.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">
            <div class="content-area-header bg-white sticky-top">
                <h5 class="fw-bold mb-0 me-4">Practical Timetables</h5>
                <div class="page-header-right ms-auto">
                    <a href="{{ route('practical_timetables.create') }}" class="btn btn-primary">
                        <i class="feather-plus me-2"></i>Add Practical Timetable
                    </a>
                </div>
            </div>

            <div class="content-area-body">
                @include('elements.flash')
                <div class="row g-3">
                    @forelse($timetables as $timetable)
                        <div class="col-md-6 col-xl-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="mb-1">{{ $timetable->name ?: 'Practical Timetable' }}</h6>
                                    <div class="text-muted small mb-2">{{ $timetable->practicalGroup->name ?? '-' }}</div>
                                    <div class="small">
                                        {{ $timetable->slots->count() }} slot(s)
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info mb-0">No practical timetables created yet.</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
