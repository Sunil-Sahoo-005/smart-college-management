@extends('admin_layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Academics.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            {{-- ══ STICKY HEADER ══ --}}
            <div class="content-area-header bg-white sticky-top">
                <h5 class="fw-bold mb-0 me-4">
                    <span class="d-block mb-2">{{ !empty($center->id) ? 'Edit Exam Center' : 'New Exam Center' }}</span>
                </h5>
                <div class="page-header-right ms-auto">
                    <a href="{{ route('admin.exam_center.index') }}" class="btn btn-secondary">
                        <i class="feather-arrow-left me-2"></i> Back to List
                    </a>
                </div>
            </div>

            @include('elements.flash')

            <div class="content-area-body">
                <form action="{{ route('admin.exam_center.store') }}" method="POST" id="centerForm">
                    @csrf
                    @if (!empty($center->id))
                        <input type="hidden" name="id" value="{{ $center->id }}">
                    @endif

                    <div class="card stretch stretch-full">
                        <div class="card-body general-info">
                            <div class="mb-5 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <h5 class="fw-bold mb-0 me-4">
                                    <span class="d-block mb-2">Center Info :</span>
                                    <span class="fs-12 fw-normal text-muted">
                                        Define a center with unique code and login credentials.
                                    </span>
                                </h5>
                                <button type="submit" class="btn btn-primary" id="saveCenterBtn">
                                    <i class="feather-save me-1"></i> Save
                                </button>
                            </div>

                            {{-- CENTER CODE --}}
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="code" class="fw-semibold">Center Code:</label>
                                    <span class="d-block fs-12 text-muted mt-1">Unique identifier (auto-uppercased)</span>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-hash"></i></div>
                                        <input type="text"
                                            class="form-control @error('code') is-invalid @enderror"
                                            id="code" name="code"
                                            value="{{ old('code', $center->code ?? '') }}"
                                            placeholder="e.g. CTR-001"
                                            maxlength="50" required autocomplete="off"
                                            style="text-transform:uppercase;">
                                    </div>
                                    @error('code')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- CENTER NAME --}}
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="name" class="fw-semibold">Center Name:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-map-pin"></i></div>
                                        <input type="text"
                                            class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name"
                                            value="{{ old('name', $center->name ?? '') }}"
                                            placeholder="Center full name"
                                            required autocomplete="off">
                                    </div>
                                    @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- USERNAME --}}
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="username" class="fw-semibold">Username:</label>
                                    <span class="d-block fs-12 text-muted mt-1">Used to log in to the exam application</span>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-user"></i></div>
                                        <input type="text"
                                            class="form-control @error('username') is-invalid @enderror"
                                            id="username" name="username"
                                            value="{{ old('username', $center->username ?? '') }}"
                                            placeholder="center_username"
                                            required autocomplete="off">
                                    </div>
                                    @error('username')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- PASSWORD --}}
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="password" class="fw-semibold">Password:</label>
                                    @if (!empty($center->id))
                                        <span class="d-block fs-12 text-muted mt-1">Leave blank to keep existing password</span>
                                    @endif
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-lock"></i></div>
                                        <input type="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            id="password" name="password"
                                            placeholder="{{ !empty($center->id) ? 'Leave blank to keep current' : 'Enter password' }}"
                                            {{ empty($center->id) ? 'required' : '' }}
                                            autocomplete="new-password">
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword"
                                                title="Show / hide password">
                                            <i class="feather-eye" id="eyeIcon"></i>
                                        </button>
                                    </div>
                                    @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- CONFIRM PASSWORD --}}
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="password_confirmation" class="fw-semibold">Confirm Password:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-lock"></i></div>
                                        <input type="password"
                                            class="form-control"
                                            id="password_confirmation" name="password_confirmation"
                                            placeholder="Re-enter password"
                                            autocomplete="new-password">
                                    </div>
                                </div>
                            </div>

                            {{-- DESCRIPTION --}}
                            <div class="row mb-4 align-items-start">
                                <div class="col-lg-4">
                                    <label for="description" class="fw-semibold">Description:</label>
                                    <span class="d-block fs-12 text-muted mt-1">Address or notes about the center</span>
                                </div>
                                <div class="col-lg-8">
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                        id="description" name="description"
                                        rows="3" placeholder="Optional description or address…"
                                        >{{ old('description', $center->description ?? '') }}</textarea>
                                    @error('description')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- STATUS --}}
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="status" class="fw-semibold">Status:</label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-activity"></i></div>
                                        <select class="form-select @error('status') is-invalid @enderror"
                                            id="status" name="status" required>
                                            <option value="active"   {{ old('status', $center->status ?? 'active') === 'active'   ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ old('status', $center->status ?? 'active') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                    @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>

                        </div>
                    </div>

                </form>
            </div>{{-- /content-area-body --}}
        </div>
    </div>
</div>

<script>
// ── Show / hide password ───────────────────────────────────
document.getElementById('togglePassword')?.addEventListener('click', function () {
    const input   = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type        = 'text';
        eyeIcon.className = 'feather-eye-off';
    } else {
        input.type        = 'password';
        eyeIcon.className = 'feather-eye';
    }
});

// ── Auto-uppercase code ────────────────────────────────────
document.getElementById('code')?.addEventListener('input', function () {
    const pos = this.selectionStart;
    this.value = this.value.toUpperCase();
    this.setSelectionRange(pos, pos);
});

// ── Client-side password match check ──────────────────────
document.getElementById('centerForm')?.addEventListener('submit', function (e) {
    const pass    = document.getElementById('password').value;
    const confirm = document.getElementById('password_confirmation').value;
    if (pass && pass !== confirm) {
        e.preventDefault();
        const inp = document.getElementById('password_confirmation');
        inp.classList.add('is-invalid');
        let fb = inp.parentElement.nextElementSibling;
        if (!fb || !fb.classList.contains('invalid-feedback')) {
            fb = document.createElement('div');
            fb.className = 'invalid-feedback d-block';
            inp.parentElement.insertAdjacentElement('afterend', fb);
        }
        fb.textContent = 'Passwords do not match!';
        inp.focus();
    }
});
</script>

<style>
.content-area { overflow-y: auto !important; }
</style>
@endsection