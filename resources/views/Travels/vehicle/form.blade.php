@extends('admin_layout')
@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">

<style>
    .vf-wrap * { box-sizing: border-box; }
    .vf-wrap { font-family: 'DM Sans', sans-serif; }

    .vf-page-header {
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
    .vf-page-title {
        font-size: 15px;
        font-weight: 600;
        color: #111827;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .vf-page-title .title-icon {
        width: 28px; height: 28px;
        border-radius: 8px;
        background: #fef3c7;
        display: flex; align-items: center; justify-content: center;
        font-size: 14px;
    }

    .vf-body { padding: 16px; }

    .vf-section-label {
        font-size: 10.5px;
        font-weight: 600;
        letter-spacing: 0.09em;
        text-transform: uppercase;
        color: #9ca3af;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .vf-section-label .s-icon {
        width: 22px; height: 22px;
        border-radius: 6px;
        background: #f1f5f9;
        display: flex; align-items: center; justify-content: center;
        font-size: 12px;
    }

    .vf-preview {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 12px 16px;
        background: #fef3c7;
        border: 1px solid #fde68a;
        border-radius: 12px;
        margin-bottom: 16px;
    }
    .vf-preview-icon { font-size: 28px; line-height: 1; }
    .vf-preview-reg {
        font-size: 15px;
        font-weight: 700;
        color: #92400e;
        font-family: monospace;
        letter-spacing: 1px;
        line-height: 1;
    }
    .vf-preview-type {
        font-size: 11.5px;
        color: #b45309;
        margin-top: 2px;
        font-weight: 500;
    }

    .vf-field { display: flex; flex-direction: column; gap: 5px; }
    .vf-label {
        font-size: 13px;
        font-weight: 500;
        color: #374151;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .vf-required {
        width: 5px; height: 5px;
        border-radius: 50%;
        background: #ef4444;
        display: inline-block;
        flex-shrink: 0;
    }
    .vf-input {
        width: 100%;
        padding: 8px 12px;
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
    .vf-input::placeholder { color: #c4c8d0; }
    .vf-input:hover  { border-color: #c7d2fe; }
    .vf-input:focus  { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.12); }
    .vf-input.is-error { border-color: #ef4444; }

    .vf-type-grid { display: flex; gap: 10px; flex-wrap: wrap; }
    .vf-type-card {
        flex: 1;
        min-width: 90px;
        padding: 10px 8px;
        border: 1.5px solid #e5e7eb;
        border-radius: 12px;
        background: #fff;
        cursor: pointer;
        text-align: center;
        transition: all 0.15s;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 5px;
    }
    .vf-type-card:hover { border-color: #c7d2fe; background: #f5f3ff; }
    .vf-type-card.selected {
        border-color: #4f46e5;
        background: #ede9fe;
        box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
    }
    .vf-type-card .type-emoji { font-size: 20px; }
    .vf-type-card .type-label { font-size: 12px; font-weight: 600; color: #374151; }
    .vf-type-card.selected .type-label { color: #4f46e5; }
    #type-select-hidden { display: none; }

    /* Driver list scroll */
    #driver-list {
        max-height: 260px;
        overflow-y: auto;
        padding-right: 4px;
    }
    #driver-list::-webkit-scrollbar { width: 4px; }
    #driver-list::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
    #driver-list::-webkit-scrollbar-thumb { background: #c7d2fe; border-radius: 4px; }

    .vf-driver-option {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 12px;
        border: 1.5px solid #e5e7eb;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.12s;
        margin-bottom: 6px;
        background: #fff;
    }
    .vf-driver-option:hover { border-color: #c7d2fe; background: #f5f3ff; }
    .vf-driver-option.selected { border-color: #4f46e5; background: #ede9fe; }
    .vf-driver-avatar {
        width: 30px; height: 30px;
        border-radius: 8px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        display: flex; align-items: center; justify-content: center;
        font-size: 11px;
        font-weight: 700;
        color: #fff;
        flex-shrink: 0;
    }
    .vf-driver-name { font-size: 13px; font-weight: 600; color: #111827; }

    .vf-error-msg { font-size: 12px; color: #ef4444; display: flex; align-items: center; gap: 4px; }
    .vf-divider { height: 1px; background: #f1f5f9; margin: 14px 0; }

    .vf-btn-row { display: flex; gap: 10px; align-items: center; }
    .vf-btn-primary {
        padding: 9px 24px;
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
    .vf-btn-primary:hover  { background: #4338ca; }
    .vf-btn-primary:active { transform: scale(0.98); }
    .vf-btn-secondary {
        padding: 9px 18px;
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
        transition: background 0.15s, border-color 0.15s, color 0.15s;
    }
    .vf-btn-secondary:hover { background: #f9fafb; border-color: #d1d5db; color: #374151; }
</style>

<div class="nxl-content without-header nxl-full-content vf-wrap">
    <div class="main-content d-flex">
        @include('Travels.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            <div class="vf-page-header">
                <div class="vf-page-title">
                    <span class="title-icon">🚌</span>
                    {{ !empty($vehicle->id) ? 'Edit Vehicle' : 'Create Vehicle' }}
                </div>
            </div>

            <div class="vf-body">
                @include('elements.flash')

                <div class="card stretch stretch-full">
                    <div class="card-body">

                        {{-- Live Preview --}}
                        <div class="vf-preview">
                            <div class="vf-preview-icon" id="preview-icon">🚌</div>
                            <div>
                                <div class="vf-preview-reg" id="preview-reg">— — —</div>
                                <div class="vf-preview-type" id="preview-type">Select type & enter reg. no.</div>
                            </div>
                        </div>

                        <form action="{{ route('admin.vehicle.store') }}" method="POST">
                            @csrf
                            @if(!empty($vehicle->id))
                                <input type="hidden" name="id" value="{{ $vehicle->id }}">
                            @endif

                            {{-- Vehicle Info --}}
                            <div class="vf-section-label">
                                <span class="s-icon">🚌</span> Vehicle Information
                            </div>

                            {{-- Registration Number — REPLACE the old <input type="text"> with this --}}
                            <div class="vf-field" style="margin-bottom:12px;">
                                <label class="vf-label">
                                    <span class="vf-required"></span> Licence Number
                                </label>
                                <select name="registration_number"
                                        id="reg-input"
                                        class="vf-input {{ $errors->has('registration_number') ? 'is-error' : '' }}"
                                        onchange="updatePreview()"
                                        required>
                                    <option value="">-- Select Driver Licence No --</option>
                                    @foreach ($driverLicenses as $licNo)
                                        <option value="{{ $licNo }}"
                                            {{ old('registration_number', $vehicle->registration_number ?? '') == $licNo ? 'selected' : '' }}>
                                            {{ $licNo }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('registration_number')
                                    <span class="vf-error-msg">⚠️ {{ $message }}</span>
                                @enderror
                            </div>
                            {{-- Vehicle Number --}}
                                <div class="vf-field" style="margin-bottom:12px;">
                                    <label class="vf-label">
                                        <span class="vf-required"></span> Vehicle Number
                                    </label>
                                    <input type="text"
                                        name="vehicle_number"
                                        class="vf-input {{ $errors->has('vehicle_number') ? 'is-error' : '' }}"
                                        value="{{ old('vehicle_number', $vehicle->vehicle_number ?? '') }}"
                                        placeholder="e.g. VH-2024-001" autocomplete="off"
                                        required>
                                    @error('vehicle_number')
                                        <span class="vf-error-msg">⚠️ {{ $message }}</span>
                                    @enderror
                                </div>

                            {{-- Type Cards --}}
                            <div class="vf-field" style="margin-bottom:12px;">
                                <label class="vf-label"><span class="vf-required"></span> Vehicle Type</label>
                                <div class="vf-type-grid">
                                    <div class="vf-type-card" data-type="Bus" onclick="selectType('Bus', this)">
                                        <span class="type-emoji">🚌</span>
                                        <span class="type-label">Bus</span>
                                    </div>
                                    <div class="vf-type-card" data-type="Minivan" onclick="selectType('Minivan', this)">
                                        <span class="type-emoji">🚐</span>
                                        <span class="type-label">Minivan</span>
                                    </div>
                                    <div class="vf-type-card" data-type="van" onclick="selectType('van', this)">
                                        <span class="type-emoji">🚚</span>
                                        <span class="type-label">Van</span>
                                    </div>
                                </div>
                                <select name="type" id="type-select-hidden" required>
                                    <option value="">Select Type</option>
                                    <option value="Bus"     {{ old('type', $vehicle->type ?? '') == 'Bus'     ? 'selected' : '' }}>Bus</option>
                                    <option value="Minivan" {{ old('type', $vehicle->type ?? '') == 'Minivan' ? 'selected' : '' }}>Minivan</option>
                                    <option value="van"     {{ old('type', $vehicle->type ?? '') == 'van'     ? 'selected' : '' }}>Van</option>
                                </select>
                                @error('type')
                                    <span class="vf-error-msg">⚠️ {{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Capacity --}}
                            <div class="vf-field" style="margin-bottom:0; max-width:200px;">
                                <label class="vf-label">Seating Capacity</label>
                                <input type="number"
                                       name="capacity"
                                       class="vf-input {{ $errors->has('capacity') ? 'is-error' : '' }}"
                                       value="{{ old('capacity', $vehicle->capacity ?? '') }}"
                                       placeholder="e.g. 40" autocomplete="off"
                                       min="1">
                                @error('capacity')
                                    <span class="vf-error-msg">⚠️ {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="vf-divider"></div>

                            {{-- Driver --}}
                            <div class="vf-section-label">
                                <span class="s-icon">🧑‍✈️</span> Assign Driver
                            </div>

                            <select name="driver_id" id="driver-select-hidden" style="display:none;">
                                <option value="">No Driver</option>
                                @foreach ($drivers as $d)
                                    <option value="{{ $d->id }}"
                                        {{ old('driver_id', $vehicle->driver_id ?? '') == $d->id ? 'selected' : '' }}>
                                        {{ $d->first_name }} {{ $d->last_name }}
                                    </option>
                                @endforeach
                            </select>

                            <div id="driver-list">
                                <div class="vf-driver-option" data-driver-id="" onclick="selectDriver('', this)">
                                    <div class="vf-driver-avatar" style="background:linear-gradient(135deg,#9ca3af,#6b7280);">—</div>
                                    <div class="vf-driver-name" style="color:#9ca3af;">No Driver Assigned</div>
                                </div>
                                @foreach ($drivers as $d)
                                    @php
                                        $initials = strtoupper(substr($d->first_name, 0, 1)) . strtoupper(substr($d->last_name, 0, 1));
                                    @endphp
                                    <div class="vf-driver-option" data-driver-id="{{ $d->id }}" onclick="selectDriver('{{ $d->id }}', this)">
                                        <div class="vf-driver-avatar">{{ $initials }}</div>
                                        <div>
                                            <div class="vf-driver-name">{{ $d->first_name }} {{ $d->last_name }}</div>
                                            <div style="font-size:11px; color:#9ca3af;">{{ $d->phone ?? '' }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="vf-divider"></div>

                            {{-- Buttons --}}
                            <div class="vf-btn-row">
                                <button type="submit" class="vf-btn-primary">
                                    {{ !empty($vehicle->id) ? '💾 Update Vehicle' : '+ Save Vehicle' }}
                                </button>
                                <a href="{{ route('admin.vehicle.index') }}" class="vf-btn-secondary">Cancel</a>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function selectType(val, el) {
        document.querySelectorAll('.vf-type-card').forEach(c => c.classList.remove('selected'));
        el.classList.add('selected');
        document.getElementById('type-select-hidden').value = val;
        updatePreview();
    }

    function selectDriver(id, el) {
        document.querySelectorAll('.vf-driver-option').forEach(o => o.classList.remove('selected'));
        el.classList.add('selected');
        document.getElementById('driver-select-hidden').value = id;
    }

    function updatePreview() {
    // ✅ use .value directly — works for both input and select
    const reg  = document.getElementById('reg-input').value.trim().toUpperCase();
    const type = document.getElementById('type-select-hidden').value;
    const iconMap = { Bus: '🚌', Minivan: '🚐', van: '🚚' };
    const icon = iconMap[type] || '🚌';
    document.getElementById('preview-icon').textContent = icon;
    document.getElementById('preview-reg').textContent  = reg || '— — —';
    document.getElementById('preview-type').textContent = type ? type + ' vehicle' : 'Select type & enter reg. no.';
}
    window.addEventListener('DOMContentLoaded', () => {
        // Type card auto-select in edit mode
        const currentType = document.getElementById('type-select-hidden').value;
        if (currentType) {
            document.querySelectorAll('.vf-type-card').forEach(card => {
                if (card.dataset.type === currentType) {
                    card.classList.add('selected');
                }
            });
        }

        // Driver auto-select in edit mode
        const currentDriverId = '{{ old('driver_id', $vehicle->driver_id ?? '') }}';
        document.querySelectorAll('.vf-driver-option').forEach(opt => {
            if (opt.dataset.driverId === currentDriverId) {
                opt.classList.add('selected');
            }
        });

        // Agar koi driver selected nahi toh "No Driver" select karo
        const anySelected = document.querySelector('.vf-driver-option.selected');
        if (!anySelected) {
            document.querySelector('.vf-driver-option[data-driver-id=""]').classList.add('selected');
        }

        updatePreview();
    });
</script>

@endsection