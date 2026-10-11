@extends('admin_layout')
@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')
            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                            @if (auth()->user()->hasPermissionTo('create Term'))
                                <a href="{{ route('admin.terms.create') }}" class="btn btn-primary">
                                    <i class="feather-plus me-2"></i>
                                    <span>Create Stream</span>
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
                                <table class="table table-hover" id="termList">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Academic Year</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($terms as $term)
                                            <tr class="single-item">
                                                <td class="project-name-td">
                                                    <div class="hstack gap-4">
                                                        <div>
                                                            <a href="{{ route('admin.terms.edit', $term) }}"
                                                                class="text-truncate-1-line">
                                                                {{ $term->name }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $term->academicYear?->name ?? 'N/A' }}</td>
                                                <td>{{ $term->start_date->format('Y-m-d') }}</td>
                                                <td>{{ $term->end_date->format('Y-m-d') }}</td>
                                                <td>
                                                    <div class="hstack gap-2 justify-content-center">
                                                       
                                                        <a href="{{ route('admin.terms.edit', $term) }}"
                                                            class="avatar-text avatar-md text-warning" title="Edit Term">
                                                            <i class="feather-edit-3"></i>
                                                        </a>
                                                        <form action="{{ route('admin.terms.destroy', $term) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('Are you sure you want to delete this term?')">
                                                            @csrf @method('DELETE')
                                                            <button type="submit"
                                                                class="avatar-text avatar-md border-0 bg-transparent text-danger"
                                                                title="Delete Term">
                                                                <i class="feather-trash-2"></i>
                                                            </button>
                                                        </form>

                                                        {{-- OPTIONAL: Dropdown for more actions --}}
                                                        {{-- <div class="dropdown">
                                                            <a href="javascript:void(0)" class="avatar-text avatar-md" data-bs-toggle="dropdown">
                                                                <i class="feather-more-horizontal"></i>
                                                            </a>
                                                            <ul class="dropdown-menu">
                                                                <li><a class="dropdown-item" href="#">Timetable</a></li>
                                                                <li><a class="dropdown-item" href="#">Exams</a></li>
                                                            </ul>
                                                        </div> --}}
                                                    </div>
                                                </td>

                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No terms found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    {{ $terms->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
