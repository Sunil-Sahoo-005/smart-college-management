@extends('admin_layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Academics.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">
            <div class="content-area-header bg-white sticky-top shadow-sm">
                <h5 class="fw-bold mb-0 me-4">
                    <span class="d-block mb-2">Courses & Streams Overview</span>
                </h5>
                
                <div class="page-header-right ms-auto">
                    @if(auth()->user()->hasPermissionTo('Create Courses'))
                    <a href="{{ route('admin.courses.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="feather-plus"></i>
                        <span>Add Course</span>
                    </a>
                    @endif
                </div>

            </div>

            @include('elements.flash')

            <div class="content-area-body p-4 bg-light">
                <div class="row g-4">
                    @forelse($courses as $course)
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="card h-100 shadow-sm border-0 rounded-3 hover-elevate">

                                {{-- Card Header --}}
                                <div class="card-header bg-gradient-primary border-0 rounded-top-3 p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0 text-white fw-bold text-truncate" title="{{ $course->name }}">
                                            {{ $course->name }}
                                        </h5>

                                        <div class="dropdown">
                                            <a href="#" class="text-white opacity-75 hover-opacity-100 rounded p-1"
                                               data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="feather-more-vertical"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                @if(auth()->user()->hasPermissionTo('Edit Course'))
                                                <li>
                                                    
                                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 fw-semibold text-secondary"
                                                       href="{{ route('admin.courses.edit', $course->id) }}">
                                                        <i class="feather-edit text-primary"></i> Edit Course
                                                    </a>
                                                </li>
                                                @endif
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('admin.courses.destroy', $course->id) }}"
                                                          method="POST"
                                                          onsubmit="return confirm('Are you sure you want to delete this course?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        @if(auth()->user()->hasPermissionTo('Delete Course'))
                                                        <button type="submit"
                                                                class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger fw-semibold hover-bg-danger-light">
                                                            <i class="feather-trash-2"></i> Delete
                                                        </button>
                                                        @endif
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="mt-2 text-white-50 small d-flex gap-3">
                                        <span><i class="feather-code me-1"></i>{{ $course->code }}</span>
                                        <span><i class="feather-clock me-1"></i>{{ $course->duration_years }} Yrs</span>
                                    </div>
                                    <div class="mt-2 text-white-50 small d-flex flex-wrap gap-3">
                                        <span><i class="feather-users me-1"></i>{{ $course->strength }} Seats</span>
                                        @if ($course->is_lateral_entry)
                                            <span><i class="feather-percent me-1"></i>{{ rtrim(rtrim(number_format($course->lateral_seat_percentage, 2), '0'), '.') }}% Lateral</span>
                                        @else
                                            <span><i class="feather-slash me-1"></i>No Lateral</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Card Body: Stream Pills --}}
                                <div class="card-body p-3 bg-white">
                                    <h6 class="text-muted fw-semibold mb-3 fs-12 text-uppercase letter-spacing-1">
                                        <i class="feather-git-branch me-1"></i> Streams / Branches
                                    </h6>

                                    <div class="d-flex flex-wrap gap-2">
                                        {{--
                                            $course->courseStreams is a collection of CourseStream (pivot) models.
                                            Each has: ->id (the course_stream row id), ->stream (the Stream model).
                                            The manage route takes the CourseStream id so it knows which
                                            course + stream combination to manage.
                                        --}}
                                        @forelse($course->courseStreams as $courseStream)
                                            <a href="{{ route('admin.streams.manage', $courseStream->id) }}"
                                               class="stream-pill text-decoration-none">
                                                {{ $courseStream->stream->name }}
                                                <i class="feather-arrow-right ms-1 hover-arrow"></i>
                                            </a>
                                        @empty
                                            <div class="text-muted fst-italic small w-100 bg-light p-2 border-dashed rounded text-center">
                                                No streams assigned yet
                                            </div>
                                        @endforelse
                                    </div>
                                </div>

                            </div>
                        </div>
                    @empty
                        <div class="col-12 py-5 text-center">
                            <div class="empty-state">
                                <div class="empty-state-icon bg-white shadow-sm border rounded-circle mx-auto mb-3
                                            d-flex align-items-center justify-content-center"
                                     style="width: 80px; height: 80px;">
                                    <i class="feather-book-open text-muted ms-1" style="font-size: 2rem;"></i>
                                </div>
                                <h5 class="fw-bold mb-2">No Courses Found</h5>
                                <p class="text-muted mb-4">You haven't added any courses yet.</p>
                                <a href="{{ route('admin.courses.create') }}"
                                   class="btn btn-primary px-4 py-2 rounded-pill">
                                    <i class="feather-plus me-2"></i> Add First Course
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
    }
    .hover-elevate {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .hover-elevate:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(79, 70, 229, 0.15) !important;
        z-index: 2;
    }
    .stream-pill {
        display: inline-flex;
        align-items: center;
        padding: 0.4rem 0.85rem;
        background-color: #f8fafc;
        color: #334155;
        border-radius: 50rem;
        font-size: 0.85rem;
        font-weight: 500;
        transition: all 0.2s ease;
        border: 1px solid #e2e8f0;
    }
    .stream-pill .hover-arrow {
        opacity: 0;
        transform: translateX(-5px);
        transition: all 0.2s ease;
        font-size: 0.75rem;
    }
    .stream-pill:hover {
        background-color: #e0e7ff;
        color: #4f46e5;
        border-color: #c7d2fe;
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(79, 70, 229, 0.1);
    }
    .stream-pill:hover .hover-arrow {
        opacity: 1;
        transform: translateX(0);
    }
    .hover-bg-danger-light:hover {
        background-color: #fee2e2;
        color: #dc2626 !important;
    }
    .hover-opacity-100 {
        transition: all 0.2s ease;
    }
    .hover-opacity-100:hover {
        opacity: 1 !important;
        background-color: rgba(255, 255, 255, 0.25);
    }
    .letter-spacing-1 { letter-spacing: 0.5px; }
    .fs-12 { font-size: 12px; }
    .border-dashed { border: 1px dashed #cbd5e1; }
</style>
@endsection
