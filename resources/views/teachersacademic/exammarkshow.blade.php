@extends('teacher_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
           
            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Exam Marks List</span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                        @if(auth()->user()->hasPermissionTo('Create Exam Marks'))
                            <a href="{{ route('admin.exam_marks.create') }}" class="btn btn-primary">
                                <i class="feather-plus me-2"></i>
                                <span>Enter Marks</span>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="content-area-body">
                    <div class="card stretch stretch-full">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover" id="examMarkList">
                                    <thead>
                                        <tr>
                                            <th>Exam</th>
                                            <th>Student</th>
                                            <th>Marks</th>
                                            <th>Grade</th>
                                            <th>Entered By</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($examMarks as $mark)
                                            <tr class="single-item">
                                                <td class="project-name-td">
                                                    <div class="hstack gap-4">
                                                        <div>
                                                            <a href="{{ route('admin.exam_marks.edit', $mark) }}"
                                                               class="text-truncate-1-line">
                                                                {{ $mark->exam?->name ?? 'N/A' }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    {{ $mark->student?->first_name }} {{ $mark->student?->last_name ?? 'N/A' }}
                                                </td>
                                                <td>{{ number_format($mark->marks_obtained, 2) }}</td>
                                                <td>{{ $mark->grade_letter ?? 'N/A' }}</td>
                                                <td>{{ $mark->enteredBy?->first_name }} {{ $mark->enteredBy?->last_name ?? 'N/A' }}</td>
                                                <td>
                                                    <div class="hstack gap-2 justify-content-end">
                                                        <a href="{{ route('admin.exam_marks.edit', $mark) }}"
                                                           class="avatar-text avatar-md">
                                                            <i class="feather-edit-3"></i>
                                                        </a>
                                                        <div class="dropdown">
                                                            <a href="javascript:void(0)" class="avatar-text avatar-md"
                                                               data-bs-toggle="dropdown" data-bs-offset="0,21">
                                                                <i class="feather-more-horizontal"></i>
                                                            </a>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <a class="dropdown-item"
                                                                       href="{{ route('admin.exam_marks.edit', $mark) }}">
                                                                        <i class="feather-edit-3 me-3"></i>
                                                                        <span>Edit</span>
                                                                    </a>
                                                                </li>
                                                                <li class="dropdown-divider"></li>
                                                                <li>
                                                                    <form action="{{ route('admin.exam_marks.destroy', $mark) }}"
                                                                          method="POST"
                                                                          style="display:inline;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit"
                                                                                class="dropdown-item"
                                                                                onclick="return confirm('Are you sure?')">
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
                                                <td colspan="6" class="text-center">No exam marks found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{ $examMarks->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
