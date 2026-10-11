@extends('teacher_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
         
            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Exam List</span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                        @if(auth()->user()->hasPermissionTo('Create Exam list'))
                            <a href="{{ route('admin.exams.create') }}" class="btn btn-primary">
                                <i class="feather-plus me-2"></i>
                                <span>Create Exam</span>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="content-area-body">
                    <div class="card stretch stretch-full">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover" id="examList">
                                    <thead>
                                        <tr>
                                            <th>Exam Type</th>
                                            <th>Name</th>
                                            <th>Class–Section</th>
                                            <th>Subject</th>
                                            <th>Date & Time</th>
                                            <th>Marks</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($exams as $exam)
                                            <tr class="single-item">
                                                <td class="project-name-td">
                                                    <div class="hstack gap-4">
                                                        <div>
                                                            <a href="{{ route('admin.exams.edit', $exam) }}"
                                                               class="text-truncate-1-line">
                                                                {{ $exam->examType?->name ?? 'N/A' }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $exam->name }}</td>
                                                <td>
                                                    {{ $exam->class?->name ?? 'N/A' }} –
                                                    {{ $exam->section?->name ?? 'N/A' }}
                                                </td>
                                                <td>{{ $exam->subject?->name ?? 'N/A' }}</td>
                                                <td>
                                                    {{ $exam->exam_date }}
                                                    <br>
                                                    <small>{{ $exam->start_time }} → {{ $exam->end_time }}</small>
                                                </td>
                                                <td>
                                                    {{ $exam->total_marks }} / {{ $exam->pass_marks }}
                                                </td>
                                                <td>
                                                    <div class="hstack gap-2 justify-content-end">
                                                        <a href="{{ route('admin.exams.edit', $exam) }}"
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
                                                                       href="{{ route('admin.exams.edit', $exam) }}">
                                                                        <i class="feather-edit-3 me-3"></i>
                                                                        <span>Edit</span>
                                                                    </a>
                                                                </li>
                                                                <li class="dropdown-divider"></li>
                                                                <li>
                                                                    <form action="{{ route('admin.exams.destroy', $exam) }}"
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
                                                <td colspan="7" class="text-center">No exams found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{ $exams->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
