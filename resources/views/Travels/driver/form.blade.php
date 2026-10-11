@extends('admin_layout')
@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">

<style>
    .df-wrap * { box-sizing: border-box; }
    .df-wrap { font-family: 'DM Sans', sans-serif; }

    /* ── Header ── */
    .df-page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 20px;
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .df-page-title {
        font-size: 15px;
        font-weight: 600;
        color: #111827;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .df-page-title .title-icon {
        width: 28px; height: 28px;
        border-radius: 8px;
        background: #dbeafe;
        display: flex; align-items: center; justify-content: center;
        font-size: 14px;
    }

    /* ── Body ── */
    .df-body { padding: 24px; }

    /* ── Form Card ── */
    .df-form-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #f1f5f9;
        overflow: hidden;
        max-width: 680px;
    }

    /* ── Section label ── */
    .df-section-label {
        font-size: 10.5px;
        font-weight: 600;
        letter-spacing: 0.09em;
        text-transform: uppercase;
        color: #9ca3af;
        margin-bottom: 16px;
        padding-bottom: 10px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .df-section-label .s-icon {
        width: 22px; height: 22px;
        border-radius: 6px;
        background: #f1f5f9;
        display: flex; align-items: center; justify-content: center;
        font-size: 12px;
    }

    /* ── Avatar preview ── */
    .df-avatar-preview {
        width: 56px; height: 56px;
        border-radius: 14px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        display: flex; align-items: center; justify-content: center;
        font-size: 20px;
        font-weight: 700;
        color: #fff;
        letter-spacing: 1px;
        flex-shrink: 0;
        transition: all 0.2s;
        margin-bottom: 20px;
    }

    /* ── Grid ── */
    .df-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 16px;
    }
    @media (max-width: 600px) {
        .df-grid-2 { grid-template-columns: 1fr; }
    }

    /* ── Field ── */
    .df-field { display: flex; flex-direction: column; gap: 6px; }
    .df-label {
        font-size: 13px;
        font-weight: 500;
        color: #374151;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .df-required {
        width: 5px; height: 5px;
        border-radius: 50%;
        background: #ef4444;
        display: inline-block;
        flex-shrink: 0;
    }
    .df-input {
        width: 100%;
        padding: 9px 13px;
        font-size: 14px;
        font-family: 'DM Sans', sans-serif;
        border: 1.5px solid #e5e7eb;
        border-radius: 10px;
        background: #fff;
        color: #111827;
        outline: none;
        transition: border-color 0.18s, box-shadow 0.18s;
        appearance: none;
        -webkit-appearance: none;
    }
    .df-input::placeholder { color: #c4c8d0; }
    .df-input:hover  { border-color: #c7d2fe; }
    .df-input:focus  { border-color: #6366f1; box-shadow: 0 0 0 3.5px rgba(99,102,241,0.12); }
    .df-input.is-error { border-color: #ef4444; box-shadow: 0 0 0 3px rgba(239,68,68,0.1); }

    .df-error-msg {
        font-size: 12px;
        color: #ef4444;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* ── Divider ── */
    .df-divider { height: 1px; background: #f1f5f9; margin: 20px 0; }

    /* ── Buttons ── */
    .df-btn-row { display: flex; gap: 10px; align-items: center; }
    .df-btn-primary {
        padding: 10px 26px;
        background: #4f46e5;
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        font-family: 'DM Sans', sans-serif;
        cursor: pointer;
        transition: background 0.15s, transform 0.1s;
        display: inline-flex;
        align-items: center;
        gap: 7px;
    }
    .df-btn-primary:hover  { background: #4338ca; }
    .df-btn-primary:active { transform: scale(0.98); }
    .df-btn-secondary {
        padding: 10px 20px;
        background: transparent;
        color: #6b7280;
        border: 1.5px solid #e5e7eb;
        border-radius: 10px;
        font-size: 14px;
        font-family: 'DM Sans', sans-serif;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        transition: background 0.15s, border-color 0.15s, color 0.15s;
    }
    .df-btn-secondary:hover { background: #f9fafb; border-color: #d1d5db; color: #374151; }

    /* ── Info tip ── */
    .df-tip {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 10px;
        font-size: 12.5px;
        color: #1e40af;
        margin-bottom: 20px;
    }
</style>

<div class="nxl-content without-header nxl-full-content df-wrap">
    <div class="main-content d-flex">
        @include('Travels.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            {{-- ── Page Header ── --}}
            <div class="df-page-header">
                <div class="df-page-title">
                    <span class="title-icon">🧑‍✈️</span>
                    {{ !empty($driver->id) ? 'Edit Driver' : 'Create Driver' }}
                </div>
            </div>

            {{-- ── Body ── --}}
            <div class="df-body">
                @include('elements.flash')

                <div class="card stretch stretch-full">
                    <div class="card-body">

                        {{-- Avatar Preview --}}
                        <div class="df-avatar-preview" id="avatarPreview">🧑‍✈️</div>

                        <form action="{{ route('admin.driver.store') }}" method="POST">
                            @csrf

                            @if(!empty($driver->id))
                                <input type="hidden" name="id" value="{{ $driver->id }}">
                            @endif

                            {{-- ── Personal Info ── --}}
                            <div class="df-section-label">
                                <span class="s-icon">👤</span> Personal Information
                            </div>

                            <div class="df-grid-2">
                                <div class="df-field">
                                    <label class="df-label">
                                        <span class="df-required"></span> First Name
                                    </label>
                                    <input type="text"
                                           name="first_name"
                                           id="first_name"
                                           class="df-input {{ $errors->has('first_name') ? 'is-error' : '' }}"
                                           value="{{ old('first_name', $driver->first_name ?? '') }}"
                                           placeholder="e.g. Rajesh"
                                           oninput="updateAvatar()"
                                           required autocomplete="off">
                                    @error('first_name')
                                        <span class="df-error-msg">⚠️ {{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="df-field">
                                    <label class="df-label">
                                        <span class="df-required"></span> Last Name
                                    </label>
                                    <input type="text"
                                           name="last_name"
                                           id="last_name"
                                           class="df-input {{ $errors->has('last_name') ? 'is-error' : '' }}"
                                           value="{{ old('last_name', $driver->last_name ?? '') }}"
                                           placeholder="e.g. Kumar"
                                           oninput="updateAvatar()"
                                           required autocomplete="off">
                                    @error('last_name')
                                        <span class="df-error-msg">⚠️ {{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="df-divider"></div>

                            {{-- ── Contact & License ── --}}
                            <div class="df-section-label">
                                <span class="s-icon">📋</span> Contact & License
                            </div>

                            <div class="df-grid-2">
                                <div class="df-field">
                                    <label class="df-label">
                                        <span class="df-required"></span> Phone Number
                                    </label>
                                    <input type="number"
                                           name="phone"
                                           class="df-input {{ $errors->has('phone') ? 'is-error' : '' }}"
                                           value="{{ old('phone', $driver->phone ?? '') }}"
                                           placeholder="e.g. 9876543210" 
                                           required autocomplete="off">
                                    @error('phone')
                                        <span class="df-error-msg">⚠️ {{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="df-field">
                                    <label class="df-label">License Number</label>
                                    <input type="text"
                                           name="license_number"
                                           class="df-input {{ $errors->has('license_number') ? 'is-error' : '' }}"
                                           value="{{ old('license_number', $driver->license_number ?? '') }}"
                                           placeholder="e.g. OD1420230012345" autocomplete="off">
                                    @error('license_number')
                                        <span class="df-error-msg">⚠️ {{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="df-divider"></div>

                            {{-- ── Buttons ── --}}
                            <div class="df-btn-row">
                                <button type="submit" class="df-btn-primary">
                                    {{ !empty($driver->id) ? '💾 Update Driver' : '+ Save Driver' }}
                                </button>
                                <a href="{{ route('admin.driver.index') }}" class="df-btn-secondary">
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

<script>
    // Live avatar preview from initials
    function updateAvatar() {
        const first = document.getElementById('first_name').value.trim();
        const last  = document.getElementById('last_name').value.trim();
        const avatar = document.getElementById('avatarPreview');

        if (first || last) {
            const f = first ? first[0].toUpperCase() : '';
            const l = last  ? last[0].toUpperCase()  : '';
            avatar.textContent = f + l;
        } else {
            avatar.textContent = '🧑‍✈️';
        }
    }

    // Init avatar on edit mode
    window.addEventListener('DOMContentLoaded', updateAvatar);
</script>

@endsection