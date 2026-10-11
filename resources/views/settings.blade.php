@extends('admin_layout')

@section('title', 'Portal Settings')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Library.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            {{-- ───────────────────── TOP BAR ───────────────────── --}}
            <div class="content-area-header bg-white sticky-top">
                <h6 class="card-title mb-0">Portal Settings</h6>
            </div>

            <div class="content-area-body">

                {{-- ── Flash Alerts ── --}}
                @if(session('success'))
                    <div class="ps-alert ps-alert-success">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                             viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="ps-alert ps-alert-error">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                             viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                        </svg>
                        <div>
                            <strong>Please fix the following errors:</strong>
                            <ul style="margin:0.3rem 0 0;padding-left:1.1rem">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form id="settingsForm" action="{{ route('settings.update') }}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="ps-layout">

                        {{-- ══════════════ LEFT MAIN ══════════════ --}}
                        <div class="ps-main">

                            {{-- General Information --}}
                            <div class="bk-card">
                                <p class="bk-section-title">General Information</p>

                                <div class="bk-group">
                                    <label class="bk-label">Portal Name <span class="req">*</span></label>
                                    <input type="text" name="portal_name"
                                        class="bk-input @error('portal_name') is-invalid @enderror"
                                        value="{{ old('portal_name', $settings->portal_name ?? '') }}"
                                        placeholder="e.g. My School Portal"
                                        required>
                                    @error('portal_name')
                                        <div class="bk-error">{{ $message }}</div>
                                    @enderror
                                    <span class="bk-hint">This name appears across the portal interface</span>
                                </div>
                            </div>

                            {{-- ── LOGOS: 4-col grid (Primary Current | Primary Upload | Secondary Current | Secondary Upload) ── --}}
                            <div class="ps-four-logo-grid">

                                {{-- Primary Current --}}
                                <div class="bk-card ps-logo-preview-card">
                                    <p class="bk-section-title">Primary — Current</p>
                                    @php $logoSrc = $settings->logo_path ?? $settings->logo_url ?? null; @endphp
                                    @if(!empty($logoSrc))
                                        <div class="ps-current-img-wrap" id="logoCurrentWrap">
                                            <img src="{{ str_starts_with($logoSrc, 'http') ? $logoSrc : Storage::url($logoSrc) }}" alt="Primary Logo">
                                        </div>
                                    @else
                                        <div class="ps-no-image" id="logoCurrentWrap">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                            <span>No image set</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Primary Upload --}}
                                <div class="bk-card ps-logo-upload-card">
                                    <p class="bk-section-title">Primary — Upload</p>
                                    <div class="bk-group">
                                        <div class="bk-upload-zone" id="logoUploadZone" onclick="document.getElementById('logoInput').click()">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto 6px;display:block;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                            <p style="font-size:11px;margin:0;font-weight:500;">Click to upload</p>
                                            <p style="font-size:10px;margin:3px 0 0;opacity:.6;">PNG, JPG · max 2 MB</p>
                                        </div>
                                        <div class="ps-new-img-wrap" id="logoNewWrap">
                                            <img id="logoNewPreview" src="#" alt="New Primary Logo">
                                            <button type="button" class="bk-img-remove" onclick="psRemoveNew('logo')">&#215;</button>
                                        </div>
                                        <input type="file" name="logo" id="logoInput" accept="image/*" style="display:none;" onchange="psPreviewImg(this,'logo')">
                                        <input type="hidden" name="existing_logo" value="{{ $settings->logo_path ?? $settings->logo_url ?? '' }}">
                                        @error('logo')<div class="bk-error">{{ $message }}</div>@enderror
                                        <span class="bk-hint">Main navigation bar</span>
                                    </div>
                                </div>

                                {{-- Secondary Current --}}
                                <div class="bk-card ps-logo-preview-card">
                                    <p class="bk-section-title">Secondary — Current</p>
                                    @php $logooSrc = $settings->logoo_path ?? $settings->logoo_url ?? null; @endphp
                                    @if(!empty($logooSrc))
                                        <div class="ps-current-img-wrap" id="logooCurrentWrap">
                                            <img src="{{ str_starts_with($logooSrc, 'http') ? $logooSrc : Storage::url($logooSrc) }}" alt="Secondary Logo">
                                        </div>
                                    @else
                                        <div class="ps-no-image" id="logooCurrentWrap">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                            <span>No image set</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Secondary Upload --}}
                                <div class="bk-card ps-logo-upload-card">
                                    <p class="bk-section-title">Secondary — Upload</p>
                                    <div class="bk-group">
                                        <div class="bk-upload-zone" id="logooUploadZone" onclick="document.getElementById('logooInput').click()">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto 6px;display:block;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                            <p style="font-size:11px;margin:0;font-weight:500;">Click to upload</p>
                                            <p style="font-size:10px;margin:3px 0 0;opacity:.6;">PNG, JPG · max 2 MB</p>
                                        </div>
                                        <div class="ps-new-img-wrap" id="logooNewWrap">
                                            <img id="logooNewPreview" src="#" alt="New Secondary Logo">
                                            <button type="button" class="bk-img-remove" onclick="psRemoveNew('logoo')">&#215;</button>
                                        </div>
                                        <input type="file" name="logoo" id="logooInput" accept="image/*" style="display:none;" onchange="psPreviewImg(this,'logoo')">
                                        <input type="hidden" name="existing_logoo" value="{{ $settings->logoo_path ?? $settings->logoo_url ?? '' }}">
                                        @error('logoo')<div class="bk-error">{{ $message }}</div>@enderror
                                        <span class="bk-hint">Login pages / alternate</span>
                                    </div>
                                </div>

                            </div>{{-- end ps-four-logo-grid --}}

                        </div>{{-- end ps-main --}}

                        {{-- ══════════════ RIGHT SIDEBAR ══════════════ --}}
                        <div class="bk-sidebar">

                            {{-- Actions --}}
                            <div class="bk-card bk-action-card">
                                <button type="submit" form="settingsForm" class="bk-btn-primary">
                                    Save Settings
                                </button>
                                <a href="{{ url()->previous() }}" class="bk-btn-secondary">
                                    Cancel
                                </a>
                                <p class="bk-required-note">
                                    Fields marked <span class="req">*</span> are required
                                </p>
                            </div>

                        </div>{{-- end bk-sidebar --}}

                    </div>{{-- end ps-layout --}}
                </form>

            </div>{{-- end content-area-body --}}
        </div>{{-- end content-area --}}
    </div>
</div>

{{-- ═══════════════════════ STYLES ═══════════════════════ --}}
<style>
    /* ── Layout ── */
    .ps-layout {
        display: grid;
        grid-template-columns: 1fr 256px;
        gap: 16px;
        align-items: start;
    }

    @media (max-width: 960px) {
        .ps-layout { grid-template-columns: 1fr; }
    }

    .ps-main {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    /* ── 4-col logo grid: [Primary Current][Primary Upload][Secondary Current][Secondary Upload] ── */
    .ps-four-logo-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
    }

    @media (max-width: 900px) {
        .ps-four-logo-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 540px) {
        .ps-four-logo-grid { grid-template-columns: 1fr; }
    }

    /* ── Current image display inside preview card ── */
    .ps-current-img-wrap {
        width: 100%;
        height: 80px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #f9fafb;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        padding: 8px;
        box-sizing: border-box;
    }

    .ps-current-img-wrap img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        border-radius: 6px;
    }

    /* ── No image placeholder ── */
    .ps-no-image {
        width: 100%;
        height: 80px;
        border: 1.5px dashed #e5e7eb;
        border-radius: 10px;
        background: #fafafa;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        color: #d1d5db;
    }

    .ps-no-image span {
        font-size: 11px;
        color: #9ca3af;
    }

    /* ── New upload preview (inside upload card) ── */
    .ps-new-img-wrap {
        display: none;
        position: relative;
        margin-top: 8px;
    }

    .ps-new-img-wrap img {
        width: 100%;
        height: 100px;
        object-fit: contain;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        background: #f9fafb;
        display: block;
        padding: 6px;
        box-sizing: border-box;
    }

    /* ── Card (reusing bk-card from library) ── */
    .bk-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
    }

    /* ── Logo cards are more compact ── */
    .ps-logo-preview-card,
    .ps-logo-upload-card {
        padding: 0.75rem 1rem;
    }

    /* ── Section title ── */
    .bk-section-title {
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #9ca3af;
        margin: 0 0 1rem;
        padding-bottom: 0.625rem;
        border-bottom: 1px solid #f3f4f6;
    }

    /* ── Form group / label / input / hint / error ── */
    .bk-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .bk-label {
        font-size: 12px;
        font-weight: 500;
        color: #6b7280;
    }

    .req { color: #E24B4A; margin-left: 2px; }

    .bk-input {
        height: 36px;
        padding: 0 10px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 13px;
        color: #111827;
        background: #fff;
        outline: none;
        width: 100%;
        box-sizing: border-box;
        transition: border-color 0.15s, box-shadow 0.15s;
    }

    .bk-input:hover  { border-color: #d1d5db; }
    .bk-input:focus  { border-color: #378ADD; box-shadow: 0 0 0 3px rgba(55,138,221,.12); }
    .bk-input.is-invalid { border-color: #E24B4A; }

    .bk-hint  { font-size: 11px; color: #9ca3af; line-height: 1.4; }
    .bk-error { font-size: 11px; color: #E24B4A; margin-top: 2px; }

    /* ── Upload zone ── */
    .bk-upload-zone {
        display: block;
        width: 100%;
        border: 1.5px dashed #e5e7eb;
        border-radius: 10px;
        padding: 12px 10px;
        text-align: center;
        cursor: pointer;
        background: transparent;
        color: #6b7280;
        font-size: 13px;
        transition: background 0.15s, border-color 0.15s;
        box-sizing: border-box;
    }

    .bk-upload-zone:hover {
        background: #f9fafb;
        border-color: #378ADD;
    }

    .bk-img-remove {
        position: absolute;
        top: -7px;
        right: -7px;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #E24B4A;
        border: 2px solid #fff;
        cursor: pointer;
        color: #fff;
        font-size: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
        padding: 0;
    }

    /* ── Action card ── */
    .bk-action-card {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .bk-btn-primary {
        width: 100%;
        height: 38px;
        background: #378ADD;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.15s;
    }

    .bk-btn-primary:hover { background: #185FA5; }

    .bk-btn-secondary {
        display: block;
        width: 100%;
        height: 38px;
        background: transparent;
        color: #6b7280;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        text-align: center;
        text-decoration: none;
        line-height: 38px;
        transition: all 0.15s;
    }

    .bk-btn-secondary:hover { background: #f9fafb; color: #374151; }

    .bk-required-note {
        font-size: 11px;
        color: #9ca3af;
        text-align: center;
        margin: 4px 0 0;
    }

    /* ── Sidebar ── */
    .bk-sidebar {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    /* ── Flash alerts ── */
    .ps-alert {
        display: flex;
        align-items: flex-start;
        gap: 0.6rem;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        margin-bottom: 14px;
    }

    .ps-alert svg { flex-shrink: 0; margin-top: 1px; }

    .ps-alert-success {
        background: #edfaf3;
        color: #1a6b42;
        border: 1px solid #b2e8cc;
    }

    .ps-alert-error {
        background: #fff0f0;
        color: #c0392b;
        border: 1px solid #f5b7b1;
    }
</style>

{{-- ═══════════════════════ SCRIPTS ═══════════════════════ --}}
<script>
    function psPreviewImg(input, key) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        if (file.size > 2 * 1024 * 1024) {
            alert('Image must be under 2 MB');
            input.value = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = function (e) {
            // Update right card (new upload preview)
            const newWrap = document.getElementById(key + 'NewWrap');
            const newImg  = document.getElementById(key + 'NewPreview');
            const zone    = document.getElementById(key + 'UploadZone');
            newImg.src            = e.target.result;
            newWrap.style.display = 'block';
            if (zone) zone.style.display = 'none';

            // Update left card (current image preview) live
            const currentWrap = document.getElementById(key + 'CurrentWrap');
            if (currentWrap) {
                // Replace no-image placeholder or update existing img
                let existingImg = currentWrap.querySelector('img');
                if (existingImg) {
                    existingImg.src = e.target.result;
                } else {
                    currentWrap.innerHTML = '<img src="' + e.target.result + '" alt="Preview" style="max-width:100%;max-height:100%;object-fit:contain;border-radius:6px;">';
                }
                currentWrap.className = 'ps-current-img-wrap';
            }
        };
        reader.readAsDataURL(file);
    }

    function psRemoveNew(key) {
        const newWrap = document.getElementById(key + 'NewWrap');
        const input   = document.getElementById(key + 'Input');
        const zone    = document.getElementById(key + 'UploadZone');
        newWrap.style.display = 'none';
        input.value           = '';
        if (zone) zone.style.display = 'block';
    }
</script>
@endsection