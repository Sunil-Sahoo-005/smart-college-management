@extends('admin_layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Academics.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            {{-- ══ STICKY HEADER ══ --}}
            <div class="content-area-header bg-white sticky-top">
                <h5 class="fw-bold mb-0 me-4">
                    <span class="d-block mb-2">{{ !empty($device->id) ? 'Edit Device' : 'Register New Device' }}</span>
                </h5>
               <div class="page-header-right ms-auto">
                    <a href="{{ route('admin.exam_center.devices', ['id' => $selectedCenterId]) }}"
                         class="btn btn-secondary">
                        <i class="feather-arrow-left me-2"></i> Back to List
                    </a>
               </div>
            </div>

            @include('elements.flash')

            <div class="content-area-body">
                <form action="{{ route('admin.center_devices.store') }}" method="POST" id="deviceForm">
                    @csrf
                    @if (!empty($device->id))
                        <input type="hidden" name="id" value="{{ $device->id }}">
                    @endif

                    <div class="card stretch stretch-full">
                        <div class="card-body general-info">
                            <div class="mb-5 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <h5 class="fw-bold mb-0 me-4">
                                    <span class="d-block mb-2">Device Info :</span>
                                    <span class="fs-12 fw-normal text-muted">
                                        Register a device and link it to an exam center.
                                    </span>
                                </h5>
                                <button type="submit" class="btn btn-primary">
                                    <i class="feather-save me-1"></i> Save
                                </button>
                            </div>

                           
                           {{-- CENTER --}}
<div class="row mb-4 align-items-center">
    <div class="col-lg-4">
        <label for="center_id" class="fw-semibold">Exam Center:</label>
    </div>
    <div class="col-lg-8">
        @php $isEdit = !empty($device->id); @endphp

        {{-- Hidden field to always submit the center ID (works even when select is disabled) --}}
        @if($isEdit)
            <input type="hidden" name="center_id" value="{{ old('center_id', $selectedCenterId ?? $device->center_id ?? '') }}">
        @endif

        <div class="input-group">
            <div class="input-group-text"><i class="feather-map-pin"></i></div>
            <select class="form-select @error('center_id') is-invalid @enderror"
                id="center_id" name="center_id"
                {{ $isEdit ? 'disabled' : '' }}
                {{ $isEdit ? '' : 'required' }}>
                <option value="">Select Center</option>
                @foreach ($centers as $center)
                    <option value="{{ $center->id }}"
                        {{ old('center_id', $selectedCenterId ?? $device->center_id ?? '') == $center->id ? 'selected' : '' }}>
                        {{ $center->name }} ({{ $center->code }})
                    </option>
                @endforeach
            </select>
        </div>
        @if($isEdit)
            <!-- <small class="text-muted d-block mt-1">The center cannot be changed after creation.</small> -->
        @endif
        @error('center_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>
</div>

                            {{-- DEVICE NAME --}}
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="device_name" class="fw-semibold">Device Name:</label>
                                    <span class="d-block fs-12 text-muted mt-1">Friendly label for this device</span>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-monitor"></i></div>
                                        <input type="text"
                                            class="form-control @error('device_name') is-invalid @enderror"
                                            id="device_name" name="device_name"
                                            value="{{ old('device_name', $device->device_name ?? '') }}"
                                            placeholder="e.g. Lab PC 01"
                                            required autocomplete="off">
                                    </div>
                                    @error('device_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            {{-- DEVICE CODE --}}
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="device_code" class="fw-semibold">Device Code:</label>
                                    <span class="d-block fs-12 text-muted mt-1">Unique visible code (auto-uppercased)</span>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-hash"></i></div>
                                        <input type="text"
                                            class="form-control @error('device_code') is-invalid @enderror"
                                            id="device_code" name="device_code"
                                            value="{{ old('device_code', $device->device_code ?? '') }}"
                                            placeholder="e.g. DEV-001"
                                            maxlength="100" required autocomplete="off"
                                            style="text-transform:uppercase;">
                                    </div>
                                    @error('device_code')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            
                            {{-- DEVICE IDENTIFIER --}}
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="device_identifier" class="fw-semibold">Device Identifier:</label>
                                    <span class="d-block fs-12 text-muted mt-1">Auto-generated ID by the exam app</span>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-cpu"></i></div>
                                        <input type="text"
                                            class="form-control @error('device_identifier') is-invalid @enderror"
                                            id="device_identifier" name="device_identifier"
                                            value="{{ old('device_identifier', $device->device_identifier ?? '') }}"
                                            placeholder="Unique hardware/app identifier"
                                            required autocomplete="off">
                                        <button class="btn btn-outline-secondary" type="button" id="generateIdBtn"
                                                title="Generate random identifier">
                                            <i class="feather-refresh-cw"></i>
                                        </button>
                                    </div>
                                    @error('device_identifier')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- MAC ADDRESS --}}
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label for="mac_address" class="fw-semibold">MAC Address:</label>
                                    <span class="d-block fs-12 text-muted mt-1">Optional hardware address</span>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-wifi"></i></div>
                                        <input type="text"
                                            class="form-control @error('mac_address') is-invalid @enderror"
                                            id="mac_address" name="mac_address"
                                            value="{{ old('mac_address', $device->mac_address ?? '') }}"
                                            placeholder="e.g. AA:BB:CC:DD:EE:FF"
                                            maxlength="100" autocomplete="off">
                                    </div>
                                    @error('mac_address')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
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
                                            <option value="active"   {{ old('status', $device->status ?? 'active') === 'active'   ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ old('status', $device->status ?? 'active') === 'inactive' ? 'selected' : '' }}>Inactive</option>
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
// ── Auto-uppercase device code ─────────────────────────────
document.getElementById('device_code')?.addEventListener('input', function () {
    const pos = this.selectionStart;
    this.value = this.value.toUpperCase();
    this.setSelectionRange(pos, pos);
});

// ── Generate random device identifier ─────────────────────
document.getElementById('generateIdBtn')?.addEventListener('click', function () {
    const inp = document.getElementById('device_identifier');
    if (inp.value && !confirm('Replace current identifier with a new one?')) return;
    const arr = new Uint8Array(16);
    crypto.getRandomValues(arr);
    inp.value = Array.from(arr, b => b.toString(16).padStart(2, '0')).join('').toUpperCase();
});

// ── MAC address auto-format ────────────────────────────────
document.getElementById('mac_address')?.addEventListener('input', function () {
    let val = this.value.replace(/[^a-fA-F0-9]/g, '').toUpperCase();
    val = val.match(/.{1,2}/g)?.join(':') ?? val;
    if (val.length > 17) val = val.substring(0, 17);
    const pos = this.selectionStart;
    this.value = val;
});
</script>

<style>
.content-area { overflow-y: auto !important; }
</style>
@endsection