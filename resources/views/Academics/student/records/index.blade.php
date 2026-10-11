@extends('admin_layout')
@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                            <a href="{{ route('admin.students.show', $student->id) }}" class="btn btn-outline-secondary">
                                <i class="feather-arrow-left me-2"></i>Back to Student
                            </a>
                            <a href="{{ route('admin.student.records.create', $student->id) }}" class="btn btn-primary">
                                <i class="feather-plus me-2"></i>Add Note
                            </a>
                        </div>
                    </div>
                </div>

                <div class="content-area-body">
                    @include('elements.flash')

                    <div class="card stretch stretch-full">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h6 class="card-title mb-0">
                                <i class="feather-clock me-2 text-primary"></i>
                                Notes — {{ $student->first_name }} {{ $student->last_name }}
                            </h6>
                        </div>
                        <div class="card-body">
                            @if ($records->count())
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Message</th>
                                                <th>File</th>
                                                <th>Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($records as $index => $record)
                                                <tr>
                                                    <td>{{ $records->firstItem() + $index }}</td>
                                                    <td>{{ $record->message ?? '—' }}</td>
                                                    <td>
                                                        @if ($record->file_path)
                                                            <a href="{{ asset('storage/' . $record->file_path) }}"
                                                               target="_blank" class="btn btn-sm btn-outline-danger">
                                                                <i class="feather-file me-1"></i>View PDF
                                                            </a>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $record->created_at->format('d M Y, h:i A') }}</td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <a href="{{ route('admin.student.records.edit', [$student->id, $record->id]) }}"
                                                               class="btn btn-sm btn-outline-primary">
                                                                <i class="feather-edit-2 me-1"></i>Edit
                                                            </a>
                                                            <form action="{{ route('admin.student.records.destroy', $record->id) }}"
                                                                  method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                    onclick="return confirm('Delete this record?')">
                                                                    <i class="feather-trash-2 me-1"></i>Delete
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3">
                                    {{ $records->links() }}
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <div class="mb-3" style="width:64px;height:64px;border-radius:16px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;margin:auto;">
                                        <i class="feather-inbox fs-3 text-muted"></i>
                                    </div>
                                    <h6 class="fw-semibold mb-1">No Notes Found</h6>
                                    <p class="text-muted fs-12 mb-3">No Notes have been added yet.</p>
                                    <a href="{{ route('admin.student.records.create', $student->id) }}"
                                       class="btn btn-primary btn-sm">
                                        <i class="feather-plus me-1"></i>Add Notes
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
