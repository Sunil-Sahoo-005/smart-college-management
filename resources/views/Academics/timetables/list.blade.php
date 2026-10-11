@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Timetables</span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <a href="{{ route('admin.timetables.create') }}" class="btn btn-primary">
                            <i class="feather-plus me-2"></i>
                            Create Timetable
                        </a>
                    </div>
                </div>

                <div class="content-area-body">
                    <!-- Filters -->
                    <form method="GET" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <select name="class_id" class="form-select">
                                    <option value="">All Classes</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select name="section_id" class="form-select">
                                    <option value="">All Sections</option>
                                    @foreach ($sections as $section)
                                        <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>
                                            {{ $section->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </form>

                    <div class="card stretch stretch-full">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Class</th>
                                            <th>Section</th>
                                            <th>Slots</th>
                                            <th>Created</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($timetables as $timetable)
                                            <tr>
                                                <td>{{ $timetable->name }}</td>
                                                <td>{{ $timetable->class->name }}</td>
                                                <td>{{ $timetable->section->name }}</td>
                                                <td>
                                                    <span class="badge bg-info">{{ $timetable->slots->count() }}</span>
                                                </td>
                                                <td>{{ $timetable->created_at->format('d M Y') }}</td>
                                                <td class="text-end">
                                                    <div class="hstack gap-2">
                                                        {{-- <a href="{{ route('admin.timetables.show', $timetable) }}" class="btn btn-sm btn-outline-primary">
                                                            View
                                                        </a> --}}
                                                        <a href="{{ route('admin.timetables.edit', $timetable) }}" class="btn btn-sm btn-outline-success">
                                                            Edit
                                                        </a>
                                                        <form action="{{ route('admin.timetables.destroy', $timetable) }}" method="POST" class="d-inline">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">
                                                    No timetables found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{ $timetables->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
