@extends('admin_layout')
@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                            <a href="{{ route('admin.student.records.index', $student->id) }}" class="btn btn-outline-secondary">
                                <i class="feather-arrow-left me-2"></i>Back to Notes
                            </a>
                        </div>
                    </div>
                </div>

                <div class="content-area-body">
                    @include('elements.flash')

                    <form action="{{ route('admin.student.records.store', $student->id) }}"
                          method="POST" enctype="multipart/form-data">
                        @csrf
                        @if (!empty($record->id))
                            <input type="hidden" name="id" value="{{ $record->id }}">
                        @endif

                        <div class="card stretch stretch-full shadow-sm border-0">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="feather-clock me-2 text-primary"></i>
                                    {{ !empty($record->id) ? 'Edit Notes' : 'Add Notes' }} —
                                    {{ $student->first_name }} {{ $student->last_name }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-4">

                                    {{-- Message --}}
                                    <div class="col-md-8">
                                        <label class="form-label fw-semibold">Message</label>
                                        <textarea name="message" class="form-control" rows="6"
                                            placeholder="Write a note or message...">{{ old('message', $record->message ?? '') }}</textarea>
                                        @error('message')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- PDF Upload --}}
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Upload PDF</label>
                                        <input type="file" name="file_path" class="form-control" accept=".pdf">
                                        <small class="text-muted d-block mt-1">PDF only, max 5MB</small>
                                        @if (!empty($record->file_path))
                                            <div class="mt-2">
                                                <span class="text-muted small">Current file: </span>
                                                <a href="{{ asset('storage/' . $record->file_path) }}"
                                                   target="_blank" class="btn btn-sm btn-outline-danger mt-1">
                                                    <i class="feather-file me-1"></i>View PDF
                                                </a>
                                            </div>
                                        @endif
                                        @error('file_path')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                </div>
                            </div>
                            <div class="card-footer d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.student.records.index', $student->id) }}"
                                   class="btn btn-outline-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="feather-save me-2"></i>
                                    {{ !empty($record->id) ? 'Update Record' : 'Save Record' }}
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
