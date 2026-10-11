@extends('admin_layout')
@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

<style>
    .srf-wrap * { box-sizing: border-box; }
    .srf-wrap { font-family: 'DM Sans', sans-serif; }

    .srf-page-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 20px; background: #fff;
        border-bottom: 1px solid #f1f5f9;
        position: sticky; top: 0; z-index: 10;
    }
    .srf-page-title { font-size: 15px; font-weight: 600; color: #111827; }

    .srf-body { padding: 24px; display: flex; gap: 24px; align-items: flex-start; }
    .srf-form-col { flex: 0 0 520px; }
    .srf-info-col { flex: 1; min-width: 0; }

    .srf-section-label {
        font-size: 10.5px; font-weight: 600; letter-spacing: 0.09em;
        text-transform: uppercase; color: #9ca3af;
        margin-bottom: 16px; padding-bottom: 10px;
        border-bottom: 1px solid #f1f5f9;
        display: flex; align-items: center; gap: 8px;
    }
    .step-badge {
        width: 20px; height: 20px; border-radius: 50%;
        background: #4f46e5; color: #fff;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 10px; font-weight: 700; flex-shrink: 0;
    }

    .srf-field { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; }
    .srf-label { font-size: 13px; font-weight: 500; color: #374151; display: flex; align-items: center; gap: 5px; }
    .srf-required { width: 5px; height: 5px; border-radius: 50%; background: #ef4444; display: inline-block; }
    .srf-input {
        width: 100%; padding: 9px 13px; font-size: 14px;
        font-family: 'DM Sans', sans-serif;
        border: 1.5px solid #e5e7eb; border-radius: 10px;
        background: #fff; color: #111827; outline: none;
        transition: border-color 0.18s, box-shadow 0.18s;
        appearance: none; -webkit-appearance: none;
    }
    .srf-input::placeholder { color: #c4c8d0; }
    .srf-input:hover  { border-color: #c7d2fe; }
    .srf-input:focus  { border-color: #6366f1; box-shadow: 0 0 0 3.5px rgba(99,102,241,0.12); }
    .srf-input.is-error { border-color: #ef4444; box-shadow: 0 0 0 3px rgba(239,68,68,0.1); }

    .srf-error-msg { font-size: 12px; color: #ef4444; display: flex; align-items: center; gap: 4px; margin-top: 2px; }

    /* Search Button */
    .search-btn {
        padding: 9px 18px; background: #4f46e5; color: #fff;
        border: none; border-radius: 10px; font-size: 14px;
        font-weight: 600; cursor: pointer; white-space: nowrap;
        font-family: 'DM Sans', sans-serif; transition: background 0.15s;
    }
    .search-btn:hover { background: #4338ca; }
    .search-btn:disabled { background: #a5b4fc; cursor: not-allowed; }

    /* Loading */
    .srf-loading { display: none; align-items: center; gap: 8px; font-size: 13px; color: #6b7280; padding: 10px 0; }
    .srf-loading.show { display: flex; }
    .srf-spinner {
        width: 16px; height: 16px;
        border: 2px solid #e5e7eb; border-top-color: #4f46e5;
        border-radius: 50%; animation: spin 0.7s linear infinite; flex-shrink: 0;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    @keyframes fadeSlideIn {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* Route Cards */
    .route-select-wrap {
        display: none; flex-direction: column; gap: 6px;
        margin-bottom: 16px; padding: 14px 16px;
        background: #fffbeb; border: 1.5px solid #fde68a;
        border-radius: 12px; animation: fadeSlideIn 0.2s ease;
    }
    .route-select-wrap.show { display: flex; }
    .route-select-label { font-size: 12px; font-weight: 600; color: #92400e; display: flex; align-items: center; gap: 6px; }

    .route-info-card {
        display: none; align-items: center; gap: 10px;
        padding: 10px 14px; background: #f0fdf4; border: 1.5px solid #86efac;
        border-radius: 10px; margin-bottom: 16px;
        font-size: 13px; font-weight: 600; color: #15803d;
        animation: fadeSlideIn 0.2s ease;
    }
    .route-info-card.show { display: flex; }

    .no-route-warn {
        display: none; align-items: center; gap: 8px;
        padding: 10px 14px; background: #fef2f2; border: 1.5px solid #fca5a5;
        border-radius: 10px; margin-bottom: 16px;
        font-size: 13px; color: #b91c1c; font-weight: 500;
        animation: fadeSlideIn 0.2s ease;
    }
    .no-route-warn.show { display: flex; }

    /* Fee */
    .fee-wrap { position: relative; display: flex; align-items: center; }
    .fee-prefix { position: absolute; left: 13px; font-size: 14px; font-weight: 600; color: #6b7280; pointer-events: none; }
    .fee-wrap .srf-input { padding-left: 28px; }

    .srf-divider { height: 1px; background: #f1f5f9; margin: 20px 0; }

    /* Buttons */
    .srf-btn-row { display: flex; gap: 10px; align-items: center; }
    .srf-btn-primary {
        padding: 10px 26px; background: #4f46e5; color: #fff; border: none;
        border-radius: 10px; font-size: 14px; font-weight: 600;
        font-family: 'DM Sans', sans-serif; cursor: pointer;
        transition: background 0.15s, transform 0.1s;
        display: inline-flex; align-items: center; gap: 7px;
    }
    .srf-btn-primary:hover  { background: #4338ca; }
    .srf-btn-primary:active { transform: scale(0.98); }
    .srf-btn-secondary {
        padding: 10px 20px; background: transparent; color: #6b7280;
        border: 1.5px solid #e5e7eb; border-radius: 10px; font-size: 14px;
        font-family: 'DM Sans', sans-serif; cursor: pointer; text-decoration: none;
        display: inline-flex; align-items: center;
        transition: background 0.15s, border-color 0.15s;
    }
    .srf-btn-secondary:hover { background: #f9fafb; border-color: #d1d5db; color: #374151; }

    .srf-alert-error {
        padding: 12px 16px; background: #fef2f2; border: 1px solid #fecaca;
        border-radius: 10px; margin-bottom: 20px; font-size: 13px; color: #b91c1c;
    }
    .srf-alert-error ul { margin: 6px 0 0 16px; padding: 0; }
    .srf-alert-error li { margin-bottom: 3px; }

    /* ── Student Info Panel (right side) ── */
    .stu-panel {
        display: none;
        background: #fff; border: 1.5px solid #e5e7eb;
        border-radius: 16px; overflow: hidden;
        animation: fadeSlideIn 0.25s ease;
        position: sticky; top: 70px;
    }
    .stu-panel.show { display: block; }

    .stu-panel-header {
        display: flex; align-items: center; gap: 14px;
        padding: 18px 20px;
        background: linear-gradient(135deg, #fdf4ff, #fce7f3);
        border-bottom: 1px solid #f0abfc;
    }
    .stu-avatar {
        width: 52px; height: 52px; border-radius: 14px;
        background: linear-gradient(135deg, #ec4899, #f43f5e);
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; font-weight: 700; color: #fff; flex-shrink: 0;
    }
    .stu-name { font-size: 16px; font-weight: 700; color: #831843; }
    .stu-admission { font-size: 12px; color: #be185d; margin-top: 3px; }
    .stu-found-badge {
        margin-left: auto; background: #d1fae5; color: #065f46;
        padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600;
        flex-shrink: 0;
    }

    .stu-panel-body { padding: 16px 20px; }

    .stu-grid {
        display: grid; grid-template-columns: 1fr 1fr; gap: 0;
    }
    .stu-row {
        display: contents;
    }
    .stu-cell {
        padding: 9px 0; border-bottom: 1px solid #f1f5f9;
    }
    .stu-cell:nth-child(odd)  { padding-right: 16px; }
    .stu-cell:nth-child(even) { padding-left: 16px; border-left: 1px solid #f1f5f9; }
    .stu-cell-label {
        font-size: 10px; font-weight: 600; letter-spacing: 0.06em;
        text-transform: uppercase; color: #9ca3af; margin-bottom: 3px;
    }
    .stu-cell-value {
        font-size: 13px; font-weight: 500; color: #111827;
    }
    .stu-cell-value.empty { color: #d1d5db; }

    .stu-section-divider {
        font-size: 10px; font-weight: 600; letter-spacing: 0.08em;
        text-transform: uppercase; color: #9ca3af;
        padding: 12px 0 8px; grid-column: 1 / -1;
        border-bottom: 1px solid #f1f5f9; margin-bottom: 2px;
    }
</style>

<div class="nxl-content without-header nxl-full-content srf-wrap">
    <div class="main-content d-flex">
        @include('Travels.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            <div class="srf-page-header">
                <div class="srf-page-title">
                    {{ !empty($studentroutemapping->id) ? 'Edit Mapping' : 'Assign Stop to Student' }}
                </div>
            </div>

            <div class="srf-body">
                @include('elements.flash')

                @if ($errors->any())
                    <div class="srf-alert-error" style="width:100%;">
                        <strong>Please fix the following errors:</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- LEFT: Form --}}
                <div class="srf-form-col">
                    <div class="card stretch stretch-full">
                        <div class="card-body">

                            <form action="{{ route('admin.studentroutemapping.store') }}" method="POST" id="srm-form">
                                @csrf
                                @if(!empty($studentroutemapping->id))
                                    <input type="hidden" name="id" value="{{ $studentroutemapping->id }}">
                                @endif

                                <input type="hidden" name="student_id" id="student_id"
                                       value="{{ old('student_id', $studentroutemapping->student_id ?? '') }}">
                                <input type="hidden" name="route_id" id="selected_route_id"
                                       value="{{ old('route_id', $studentroutemapping->route_id ?? '') }}">

                                {{-- STEP 1 --}}
                                <div class="srf-section-label">
                                    <span class="step-badge">1</span> Search Student by Registration Number
                                </div>

                                <div class="srf-field">
                                    <label class="srf-label">
                                        <span class="srf-required"></span> Registration / Admission Number
                                    </label>
                                    <div style="display:flex; gap:10px;">
                                        <input type="text" id="reg_number_input"
                                               class="srf-input"
                                               placeholder="e.g. BA/2026/000001" autocomplete="off"
                                               value="{{ isset($studentroutemapping->student) ? $studentroutemapping->student->admission_number : '' }}"
                                               style="flex:1;">
                                        <button type="button" class="search-btn" id="search-btn" onclick="searchStudent()">
                                            Search
                                        </button>
                                    </div>
                                    <span class="srf-error-msg" id="reg-error" style="display:none;"></span>
                                </div>

                                <div class="srf-loading" id="student-loading">
                                    <div class="srf-spinner"></div>
                                    Searching student...
                                </div>

                                @error('student_id')
                                    <span class="srf-error-msg">{{ $message }}</span>
                                @enderror

                                <div class="srf-divider"></div>

                                {{-- STEP 2 --}}
                                <div class="srf-section-label">
                                    <span class="step-badge">2</span> Select Boarding Stop
                                </div>
                                <div class="srf-field">
                                    <label class="srf-label">
                                        <span class="srf-required"></span> Boarding Stop
                                    </label>
                                    <select name="stop_id" id="stop_id"
                                            class="srf-input @error('stop_id') is-error @enderror"
                                            required
                                            onchange="onStopChange(this.value)">
                                        <option value="">-- Select Stop --</option>
                                        @foreach($allStops as $stop)
                                            <option value="{{ $stop->id }}"
                                                {{ old('stop_id', $studentroutemapping->stop_id ?? '') == $stop->id ? 'selected' : '' }}>
                                                {{ $stop->stop_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('stop_id')
                                        <span class="srf-error-msg">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="srf-loading" id="route-loading">
                                    <div class="srf-spinner"></div>
                                    Loading routes for this stop...
                                </div>

                                <div class="route-info-card" id="route-info-card">
                                    <span id="route-info-text">Route auto-selected</span>
                                </div>

                                <div class="route-select-wrap" id="route-select-wrap">
                                    <div class="route-select-label">
                                        Multiple routes found — please select one
                                    </div>
                                    <select id="route_dropdown" class="srf-input"
                                            onchange="onRouteDropdownChange(this.value)">
                                        <option value="">-- Select Route --</option>
                                    </select>
                                    @error('route_id')
                                        <span class="srf-error-msg">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="no-route-warn" id="no-route-warn">
                                    No bus/route found for this stop.
                                    <small>Please ensure a route exists with this stop and has a vehicle assigned.</small>
                                </div>

                                <div class="srf-divider"></div>

                                {{-- STEP 3 --}}
                                <div class="srf-section-label">
                                    <span class="step-badge">3</span> Transport Fee
                                </div>
                                <div class="srf-field" style="max-width: 220px;">
                                    <label class="srf-label">Fee Amount</label>
                                    <div class="fee-wrap">
                                        <span class="fee-prefix">₹</span>
                                        <input type="number" step="0.01" name="transport_fee"
                                               class="srf-input @error('transport_fee') is-error @enderror"
                                               value="{{ old('transport_fee', $studentroutemapping->transport_fee ?? '') }}"
                                               placeholder="0.00" min="0">
                                    </div>
                                    @error('transport_fee')
                                        <span class="srf-error-msg">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="srf-divider"></div>

                                <div class="srf-btn-row">
                                    <button type="submit" class="srf-btn-primary"
                                            onclick="return validateBeforeSubmit()">
                                        {{ !empty($studentroutemapping->id) ? 'Update Mapping' : 'Save Mapping' }}
                                    </button>
                                    <a href="{{ route('admin.studentroutemapping.index') }}" class="srf-btn-secondary">
                                        Cancel
                                    </a>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

                {{-- RIGHT: Student Info Panel --}}
                <div class="srf-info-col">
                    <div class="stu-panel @if(!empty($studentroutemapping->student)) show @endif" id="stu-panel">

                        <div class="stu-panel-header">
                            <div class="stu-avatar" id="stu-avatar">
                                @if(!empty($studentroutemapping->student))
                                    {{ strtoupper(substr($studentroutemapping->student->first_name,0,1)) }}{{ strtoupper(substr($studentroutemapping->student->last_name,0,1)) }}
                                @endif
                            </div>
                            <div>
                                <div class="stu-name" id="stu-name">
                                    @if(!empty($studentroutemapping->student))
                                        {{ $studentroutemapping->student->first_name }} {{ $studentroutemapping->student->last_name }}
                                    @endif
                                </div>
                                <div class="stu-admission" id="stu-admission">
                                    @if(!empty($studentroutemapping->student))
                                        {{ $studentroutemapping->student->admission_number }}
                                    @endif
                                </div>
                            </div>
                            <div class="stu-found-badge">Found</div>
                        </div>

                        <div class="stu-panel-body">
                            <div class="stu-grid">

                                <div class="stu-section-divider">Personal Info</div>

                                <div class="stu-cell">
                                    <div class="stu-cell-label">Gender</div>
                                    <div class="stu-cell-value" id="stu-gender">—</div>
                                </div>
                                <div class="stu-cell">
                                    <div class="stu-cell-label">Date of Birth</div>
                                    <div class="stu-cell-value" id="stu-dob">—</div>
                                </div>
                                <div class="stu-cell">
                                    <div class="stu-cell-label">Blood Group</div>
                                    <div class="stu-cell-value" id="stu-blood">—</div>
                                </div>
                                <div class="stu-cell">
                                    <div class="stu-cell-label">Category</div>
                                    <div class="stu-cell-value" id="stu-category">—</div>
                                </div>
                                <div class="stu-cell">
                                    <div class="stu-cell-label">Status</div>
                                    <div class="stu-cell-value" id="stu-status">—</div>
                                </div>
                                <div class="stu-cell">
                                    <div class="stu-cell-label">Email</div>
                                    <div class="stu-cell-value" id="stu-email" style="word-break:break-all;">—</div>
                                </div>

                                <div class="stu-section-divider">Academic Info</div>

                                <div class="stu-cell">
                                    <div class="stu-cell-label">College</div>
                                    <div class="stu-cell-value" id="stu-college">—</div>
                                </div>
                                <div class="stu-cell">
                                    <div class="stu-cell-label">Academic Year</div>
                                    <div class="stu-cell-value" id="stu-acadyear">—</div>
                                </div>
                                <div class="stu-cell">
                                    <div class="stu-cell-label">Course</div>
                                    <div class="stu-cell-value" id="stu-course">—</div>
                                </div>
                                <div class="stu-cell">
                                    <div class="stu-cell-label">Branch / Stream</div>
                                    <div class="stu-cell-value" id="stu-stream">—</div>
                                </div>
                                <div class="stu-cell">
                                    <div class="stu-cell-label">Section</div>
                                    <div class="stu-cell-value" id="stu-section">—</div>
                                </div>
                                <div class="stu-cell">
                                    <div class="stu-cell-label">Student Type</div>
                                    <div class="stu-cell-value" id="stu-stype">—</div>
                                </div>
                                <div class="stu-cell">
                                    <div class="stu-cell-label">Residence</div>
                                    <div class="stu-cell-value" id="stu-rtype">—</div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>{{-- end srf-body --}}
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const existingStopId  = "{{ old('stop_id',  $studentroutemapping->stop_id  ?? '') }}";
    const existingRouteId = "{{ old('route_id', $studentroutemapping->route_id ?? '') }}";

    function fillPanel(s) {
        document.getElementById('stu-avatar').textContent    = (s.first_name[0]||'').toUpperCase() + (s.last_name[0]||'').toUpperCase();
        document.getElementById('stu-name').textContent      = s.first_name + ' ' + s.last_name;
        document.getElementById('stu-admission').textContent = s.admission_number;

        function val(id, v) {
            const el = document.getElementById(id);
            el.textContent  = v || '—';
            el.className    = 'stu-cell-value' + (v ? '' : ' empty');
        }

        val('stu-gender',   s.gender);
        val('stu-dob',      s.dob);
        val('stu-blood',    s.blood_group);
        val('stu-category', s.category);
        val('stu-status',   s.status);
        val('stu-email',    s.email);
        val('stu-college',  s.college_name);
        val('stu-acadyear', s.academic_year);
        val('stu-course',   s.course);
        val('stu-stream',   s.stream);
        val('stu-section',  s.section);
        val('stu-stype',    s.student_type);
        val('stu-rtype',    s.residence_type);

        document.getElementById('stu-panel').classList.add('show');
    }

    function searchStudent() {
        const regNumber = document.getElementById('reg_number_input').value.trim();
        const regError  = document.getElementById('reg-error');
        const loading   = document.getElementById('student-loading');
        const searchBtn = document.getElementById('search-btn');

        if (!regNumber) {
            regError.textContent = 'Please enter a registration number';
            regError.style.display = 'flex';
            return;
        }

        regError.style.display = 'none';
        document.getElementById('stu-panel').classList.remove('show');
        document.getElementById('student_id').value = '';
        loading.classList.add('show');
        searchBtn.disabled = true;

        $.ajax({
            url: "{{ route('find.student.by.reg') }}",
            type: 'GET',
            data: { reg_number: regNumber },
            dataType: 'json',
            success: function(student) {
                loading.classList.remove('show');
                searchBtn.disabled = false;
                document.getElementById('student_id').value = student.id;
                fillPanel(student);
            },
            error: function() {
                loading.classList.remove('show');
                searchBtn.disabled = false;
                regError.textContent = 'Student not found with this registration number';
                regError.style.display = 'flex';
                document.getElementById('student_id').value = '';
                document.getElementById('stu-panel').classList.remove('show');
            }
        });
    }

    function onStopChange(stopId) {
        const loading       = document.getElementById('route-loading');
        const routeWrap     = document.getElementById('route-select-wrap');
        const routeInfoCard = document.getElementById('route-info-card');
        const noRouteWarn   = document.getElementById('no-route-warn');
        const routeDropdown = document.getElementById('route_dropdown');
        const routeHidden   = document.getElementById('selected_route_id');

        routeWrap.classList.remove('show');
        routeInfoCard.classList.remove('show');
        noRouteWarn.classList.remove('show');
        routeDropdown.innerHTML = '<option value="">-- Select Route --</option>';
        routeHidden.value = '';

        if (!stopId) return;

        loading.classList.add('show');

        $.ajax({
            url: "{{ route('get.buses.by.stop', ':stop_id') }}".replace(':stop_id', stopId),
            type: 'GET',
            dataType: 'json',
            success: function(buses) {
                loading.classList.remove('show');

                if (!buses || buses.length === 0) {
                    noRouteWarn.classList.add('show');
                    return;
                }

                if (buses.length === 1) {
                    routeHidden.value = buses[0].route_id;
                    document.getElementById('route-info-text').innerHTML =
                        buses[0].route_name + ' — ' + (buses[0].registration_number || 'No Vehicle');
                    routeInfoCard.classList.add('show');
                } else {
                    buses.forEach(function(bus) {
                        const opt = document.createElement('option');
                        opt.value = bus.route_id;
                        opt.textContent = bus.route_name + ' — ' + (bus.registration_number || 'No Vehicle');
                        if (existingRouteId && String(bus.route_id) === String(existingRouteId)) {
                            opt.selected = true;
                            routeHidden.value = bus.route_id;
                        }
                        routeDropdown.appendChild(opt);
                    });
                    routeWrap.classList.add('show');
                }
            },
            error: function() {
                loading.classList.remove('show');
                noRouteWarn.classList.add('show');
            }
        });
    }

    function onRouteDropdownChange(val) {
        document.getElementById('selected_route_id').value = val;
    }

    function validateBeforeSubmit() {
        if (!document.getElementById('student_id').value) {
            alert('Please search and select a student first.');
            return false;
        }
        if (!document.getElementById('stop_id').value) {
            alert('Please select a boarding stop.');
            return false;
        }
        if (!document.getElementById('selected_route_id').value) {
            alert('Route not selected. Please select a stop that has a route assigned.');
            return false;
        }
        return true;
    }

    document.getElementById('reg_number_input').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); searchStudent(); }
    });

    window.addEventListener('DOMContentLoaded', function() {
        if (existingStopId) onStopChange(existingStopId);
    });
</script>

@endsection