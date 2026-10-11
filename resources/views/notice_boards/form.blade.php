@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">
                            {{ isset($notice) ? 'Edit Notice' : 'Add New Notice' }}
                        </span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                            <a href="{{ route('admin.notice_boards.index') }}" class="btn btn-secondary">
                                <i class="feather-arrow-left me-2"></i>
                                <span>Back to Notice Board</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="content-area-body">
                    @include('elements.flash')

                    <div class="row justify-content-center">
                        <div class="col-12">
                            <div class="card stretch stretch-full">
                                <div class="card-header">
                                    <h5 class="card-title fw-semibold mb-0">
                                        <i class="feather-bell me-2 text-primary"></i>
                                        {{ isset($notice) ? 'Edit Notice Details' : 'Notice Details' }}
                                    </h5>
                                </div>
                                <div class="card-body">

                                    <form
                                        action="{{ isset($notice) ? route('admin.notice_boards.update', $notice) : route('admin.notice_boards.store') }}"
                                        method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        @if (isset($notice))
                                            @method('PUT')
                                        @endif

                                        {{-- Row 1: Notice Date + File Upload side by side --}}
                                        <div class="row mb-4">

                                            {{-- Notice Date --}}
                                            <div class="col-md-6">
                                                <label for="notice_date" class="form-label fw-semibold">
                                                    Notice Date <span class="text-danger">*</span>
                                                </label>
                                                <input
                                                    type="date"
                                                    class="form-control @error('notice_date') is-invalid @enderror"
                                                    id="notice_date"
                                                    name="notice_date"
                                                    value="{{ old('notice_date', isset($notice) && $notice->notice_date ? \Carbon\Carbon::parse($notice->notice_date)->format('Y-m-d') : '') }}"
                                                    required>
                                                @error('notice_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- File Upload --}}
                                            <div class="col-md-6">
                                                <label for="file_path" class="form-label fw-semibold">
                                                    Upload File (PDF)
                                                    @if (!isset($notice))
                                                        <span class="text-danger">*</span>
                                                    @endif
                                                </label>

                                                {{-- Show existing file if editing --}}
                                                @if (isset($notice) && $notice->file_path)
                                                    <div class="mb-2">
                                                        <a href="{{ asset('storage/' . $notice->file_path) }}"
                                                            target="_blank"
                                                            class="btn btn-sm btn-soft-danger">
                                                            <i class="feather-file-text me-1"></i> View Current PDF
                                                        </a>
                                                        <small class="text-muted ms-2">Upload new file to replace</small>
                                                    </div>
                                                @endif

                                                <input
                                                    type="file"
                                                    class="form-control @error('file_path') is-invalid @enderror"
                                                    id="file_path"
                                                    name="file_path"
                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                                    {{ !isset($notice) ? 'required' : '' }}>
                                                <small class="text-muted d-block mt-1">
                                                    Allowed: PDF, DOC, DOCX, JPG, PNG (Max: 5MB)
                                                </small>
                                                @error('file_path')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                        </div>

                                        {{-- Row 2: Title as Textarea --}}
                                        <div class="mb-4">
                                            <label for="title" class="form-label fw-semibold">
                                                Title <span class="text-danger">*</span>
                                            </label>
                                            <textarea
                                                class="form-control @error('title') is-invalid @enderror"
                                                id="title"
                                                name="title"
                                                rows="4"
                                                placeholder="Enter notice title"
                                                required autocomplete="off">
                                                {{ old('title', $notice->title ?? '') }}
                                            </textarea>
                                            @error('title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Submit Buttons --}}
                                        <div class="d-flex align-items-center gap-3 mt-4">
                                            <button type="submit" class="btn btn-primary px-4">
                                                <i class="feather-save me-2"></i>
                                                {{ isset($notice) ? 'Update Notice' : 'Save Notice' }}
                                            </button>
                                            <a href="{{ route('admin.notice_boards.index') }}"
                                                class="btn btn-secondary px-4">
                                                <i class="feather-x me-2"></i>
                                                Cancel
                                            </a>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
