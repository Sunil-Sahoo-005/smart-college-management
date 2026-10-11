@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Branch Info :</span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                            <a href="{{ route('admin.streams.index') }}" class="btn btn-secondary">
                                <i class="feather-arrow-left me-2"></i>
                                <span>Back to List</span>
                            </a>
                        </div>
                    </div>
                </div>

                @include('elements.flash')

                <div class="content-area-body">
                    <form action="{{ route('admin.streams.store') }}" method="POST">
                        @csrf
                        @if (!empty($stream->id))
                            <input type="hidden" name="id" value="{{ $stream->id }}">
                        @endif

                        <div class="card stretch stretch-full">
                            <div class="card-body general-info">
                                <div class="mb-5 d-flex align-items-center justify-content-between">
                                    <h5 class="fw-bold mb-0 me-4"></h5>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>

                                {{-- Branch Name --}}
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="name" class="fw-semibold">Name:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-hash"></i></div>
                                            <input type="text"
                                                class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name"
                                                value="{{ old('name', $stream->name ?? '') }}"
                                                placeholder="Branch name (e.g., Science, Commerce, Arts)"
                                                required autocomplete="off">
                                        </div>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Branch Code --}}
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="code" class="fw-semibold">Branch Code:</label>
                                        <small class="text-muted d-block">Auto-generated from name</small>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-code"></i></div>
                                            <input type="text"
                                                class="form-control @error('code') is-invalid @enderror"
                                                id="code" name="code"
                                                value="{{ old('code', $stream->code ?? '') }}"
                                                placeholder="e.g., SC, CS, FAS"
                                                {{ !empty($stream->id) ? 'readonly' : 'required' }} autocomplete="off">
                                            @if(empty($stream->id))
                                            <button type="button" class="btn btn-outline-secondary" id="resetCodeBtn" title="Re-generate from name">
                                                <i class="feather-refresh-cw"></i>
                                            </button>
                                            @endif
                                        </div>
                                        @error('code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const nameInput    = document.getElementById('name');
        const codeInput    = document.getElementById('code');
        const resetCodeBtn = document.getElementById('resetCodeBtn');

        // Edit mode — code is readonly, skip all JS
        if (codeInput && codeInput.hasAttribute('readonly')) return;

        function generateCode(nameVal) {
            const words = nameVal.trim().split(/\s+/).filter(Boolean);
            if (words.length === 0) return '';
            if (words.length === 1) return words[0].substring(0, 2).toUpperCase();
            return words.slice(0, 3).map(w => w[0]).join('').toUpperCase();
        }

        nameInput.addEventListener('input', function () {
            if (codeInput.dataset.manualEdit === 'true') return;
            codeInput.value = generateCode(this.value);
        });

        codeInput.addEventListener('input', function () {
            this.dataset.manualEdit = 'true';
        });

        codeInput.addEventListener('blur', function () {
            if (this.value.trim() === '') {
                this.dataset.manualEdit = 'false';
                codeInput.value = generateCode(nameInput.value);
            }
        });

        if (resetCodeBtn) {
            resetCodeBtn.addEventListener('click', function () {
                codeInput.dataset.manualEdit = 'false';
                codeInput.value = generateCode(nameInput.value);
            });
        }
    });
    </script>
@endsection