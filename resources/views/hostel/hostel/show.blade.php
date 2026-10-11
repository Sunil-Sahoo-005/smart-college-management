@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">

            {{-- SIDEBAR --}}
            @include('hostel.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">

                {{-- TOP HEADER --}}
                <div class="content-area-header bg-white sticky-top border-bottom d-flex align-items-center px-4">
                    <h6 class="card-title mb-0">
                        <i class="feather-home me-2 text-primary"></i>
                        {{ $hostel->name }}
                        <span class="badge ms-2 {{ $hostel->type == 'boys' ? 'bg-primary' : ($hostel->type == 'girls' ? 'bg-danger' : 'bg-secondary') }}">
                            {{ ucfirst($hostel->type) }} Hostel
                        </span>
                    </h6>

                    <div class="page-header-right ms-auto d-flex align-items-center gap-2 flex-wrap">
                        <span class="d-flex align-items-center gap-1" style="font-size:12px;">
                            <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#e8365d;"></span>
                            <span class="text-muted">Occupied</span>
                        </span>
                        <span class="d-flex align-items-center gap-1 me-2" style="font-size:12px;">
                            <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#2ecc71;"></span>
                            <span class="text-muted">Vacant</span>
                        </span>
                        <span class="d-flex align-items-center gap-1 me-3" style="font-size:12px;">
                            <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#f39c12;"></span>
                            <span class="text-muted">Maintenance</span>
                        </span>

                        @php
                            $totalOccupied  = $rooms->sum('occupied_beds_count');
                            $totalAvailable = $rooms->sum('available_beds_count');
                            $totalBeds      = $rooms->sum('total_beds_count');
                        @endphp

                        <span class="badge bg-danger bg-opacity-10 text-white border border-danger"
                            style="font-size:12px; padding:6px 12px;">
                            Occupied: <strong>{{ $totalOccupied }}</strong>
                        </span>
                        <span class="badge bg-success bg-opacity-10 text-white border border-success me-2"
                            style="font-size:12px; padding:6px 12px;">
                            Vacant: <strong>{{ $totalAvailable }}</strong>
                        </span>

                        <a href="{{ route('admin.hostel.index') }}?type={{ $hostel->type }}&hostel_id={{ $hostel->id }}"
                           class="btn btn-outline-secondary btn-sm">
                            <i class="feather-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>

                {{-- BODY --}}
                <div class="content-area-body p-0">

                    @if ($rooms->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="feather-inbox" style="font-size:48px; opacity:.3;"></i>
                            <p class="mt-3">No rooms found for this hostel.</p>
                            @if (auth()->user()->hasPermissionTo('Create Hostel Room'))
                                <a href="{{ route('admin.hostelroom.create') }}" class="btn btn-primary btn-sm mt-2">
                                    <i class="feather-plus me-1"></i> Add Room
                                </a>
                            @endif
                        </div>
                    @else
                        @foreach ($rooms as $room)
                            <div class="room-block">

                                {{-- Room Bar --}}
                                <div class="room-bar d-flex align-items-center px-3 py-2"
                                    style="background:#e9eaee; color:#a0aacf; font-size:12px; font-weight:700;
                                    letter-spacing:2px; text-transform:uppercase;
                                    border-top:1px solid #2e3251;">
                                    <i class="feather-grid me-2" style="color:#6a7fca; font-size:13px;"></i>
                                    Room No:
                                    <span style="color:#fff; margin-left:6px;">{{ $room->room_number }}</span>
                                    <span class="ms-3 badge {{ $room->type == 'ac' ? 'bg-success' : 'bg-warning text-dark' }}"
                                        style="font-size:10px; letter-spacing:0; font-weight:600;">
                                        {{ $room->type == 'ac' ? 'AC' : 'Non-AC' }}
                                    </span>
                                    <span class="ms-2"
                                        style="font-size:11px; font-weight:400; letter-spacing:0; color:#8892b0;">
                                        ({{ $room->total_beds_count }} beds &bull;
                                        <span style="color:#2ecc71;">{{ $room->available_beds_count }} vacant</span> &bull;
                                        <span style="color:#e8365d;">{{ $room->occupied_beds_count }} occupied</span>)
                                    </span>
                                    <span class="ms-auto badge
                                        {{ $room->status_class == 'available'
                                            ? 'bg-success'
                                            : ($room->status_class == 'occupied'
                                                ? 'bg-danger'
                                                : 'bg-warning text-dark') }}"
                                        style="font-size:10px; letter-spacing:0; text-transform:none; font-weight:600;">
                                        {{ $room->status_text }}
                                    </span>
                                </div>

                                {{-- Beds Container --}}
                                <div class="beds-container px-4 py-3"
                                    style="background:{{ $loop->even ? '#f4f6fb' : '#ffffff' }};
                                    border-bottom:1px solid #e4e8f0;">

                                    @if ($room->beds->isEmpty())
                                        <div class="text-muted" style="font-size:13px;">
                                            <i class="feather-info me-1"></i> No beds configured for this room.
                                        </div>
                                    @else
                                        <div class="d-flex flex-wrap gap-3">
                                            @foreach ($room->beds as $bed)
                                                @php
                                                    $isOccupied = $bed->status === 'occupied';
                                                    $isMaint    = $bed->status === 'maintenance';
                                                    $isVacant   = $bed->status === 'vacant';

                                                    $accentColor = $isOccupied ? '#e8365d' : ($isMaint ? '#f39c12' : '#2ecc71');
                                                    $bgColor     = $isOccupied ? '#fff5f7' : ($isMaint ? '#fffbf0' : '#f0fff5');

                                                    $alloc       = $bed->currentStudent ?? null;
                                                    $student     = $alloc && $alloc->student ? $alloc->student : null;

                                                    $studentName   = $student ? trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')) : null;
                                                    $admissionNo   = $student ? $student->admission_number ?? '-' : '-';
                                                    $admissionDate = $alloc ? \Carbon\Carbon::parse($alloc->created_at)->format('d/m/Y h:i A') : '-';
                                                    $guardianName  = $student ? $student->emergency_contact_name ?? '-' : '-';
                                                    $guardianPhone = $student ? $student->emergency_contact_phone ?? '-' : '-';

                                                    $phone = '-';
                                                    if ($student && $student->relationLoaded('user') && $student->user) {
                                                        $phone = $student->user->phone ?? '-';
                                                    } elseif ($student && !$student->relationLoaded('user')) {
                                                        try { $phone = optional($student->user)->phone ?? '-'; } catch (\Exception $e) { $phone = '-'; }
                                                    }
                                                @endphp

                                                <div class="bed-wrapper" style="position:relative;">
                                                    {{-- BED CARD --}}
                                                    <div class="bed-card {{ $isOccupied ? 'bed-occupied' : ($isMaint ? 'bed-maintenance' : 'bed-vacant') }}"
                                                        style="width:110px; background:{{ $bgColor }};
                                                        border:2px solid {{ $accentColor }};
                                                        border-left:4px solid {{ $accentColor }};
                                                        border-radius:10px; padding:10px 10px 8px;
                                                        position:relative;
                                                        cursor:{{ $isVacant ? 'pointer' : ($isOccupied ? 'pointer' : 'default') }};
                                                        transition:transform .15s, box-shadow .15s;"
                                                        @if ($isVacant)
                                                            onclick="openAllocationModal(
                                                                {{ $bed->id }},
                                                                '{{ addslashes($hostel->name) }}',
                                                                {{ $hostel->id }},
                                                                '{{ addslashes($room->room_number) }}',
                                                                {{ $room->id }},
                                                                {{ $bed->bed_number }},
                                                                {{ $bed->amount ?? 0 }}
                                                            )"
                                                            title="Click to allocate this bed"
                                                        @endif>

                                                        {{-- Glowing Status Dot --}}
                                                        <span style="position:absolute; top:7px; right:7px;
                                                            width:8px; height:8px; border-radius:50%;
                                                            background:{{ $accentColor }};
                                                            box-shadow:0 0 5px {{ $accentColor }};"></span>

                                                        {{-- Bed SVG --}}
                                                        <div style="margin-bottom:6px;">
                                                            <svg width="64" height="34" viewBox="0 0 64 34" xmlns="http://www.w3.org/2000/svg">
                                                                <rect x="4" y="14" width="56" height="16" rx="4" fill="{{ $accentColor }}" opacity="0.85" />
                                                                <ellipse cx="50" cy="16" rx="9" ry="7" fill="{{ $accentColor }}" opacity="0.55" />
                                                                @if ($isOccupied)
                                                                    <circle cx="18" cy="11" r="6" fill="{{ $accentColor }}" opacity="0.9" />
                                                                    <rect x="12" y="16" width="12" height="9" rx="3" fill="{{ $accentColor }}" opacity="0.75" />
                                                                @endif
                                                                <rect x="8" y="28" width="5" height="5" rx="1" fill="{{ $accentColor }}" opacity="0.45" />
                                                                <rect x="51" y="28" width="5" height="5" rx="1" fill="{{ $accentColor }}" opacity="0.45" />
                                                            </svg>
                                                        </div>

                                                        {{-- Main Label --}}
                                                        <div style="font-size:11px; font-weight:700; color:{{ $accentColor }};
                                                            white-space:nowrap; overflow:hidden;
                                                            text-overflow:ellipsis; max-width:90px;"
                                                            title="{{ $studentName ?? ($isMaint ? 'Maintenance' : 'Bed ' . $bed->bed_number) }}">
                                                            @if ($isOccupied)
                                                                {{ $studentName ?: 'Occupied' }}
                                                            @elseif($isMaint)
                                                                Maintenance
                                                            @else
                                                                Bed {{ $bed->bed_number }}
                                                            @endif
                                                        </div>

                                                        <div style="font-size:10px; color:#999; margin-top:2px;">
                                                            Bed No. {{ $bed->bed_number }}
                                                        </div>

                                                        @if ($isVacant)
                                                            <div style="font-size:9px; color:#2ecc71; margin-top:3px;">
                                                                <i class="feather-plus-circle"></i> Allocate
                                                            </div>
                                                        @endif
                                                    </div>
                                                    {{-- END BED CARD --}}

                                                    {{-- HOVER TOOLTIP --}}
                                                    @if ($isOccupied)
                                                        <div class="bed-tooltip">
                                                            <div style="font-weight:700; font-size:12px; color:#222;
                                                                border-bottom:1px solid #eee; padding-bottom:6px; margin-bottom:6px;">
                                                                <i class="feather-user" style="color:#e8365d; margin-right:4px;"></i>
                                                                {{ $studentName ?: 'Student' }}
                                                            </div>
                                                            <table style="font-size:12px; color:#333; width:100%; border-collapse:collapse;">
                                                                <tr>
                                                                    <td class="tt-label">Admission No.</td>
                                                                    <td class="tt-value">: {{ $admissionNo }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="tt-label">Allotment Date</td>
                                                                    <td class="tt-value">: {{ $admissionDate }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="tt-label">Phone</td>
                                                                    <td class="tt-value">: {{ $phone }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="tt-label">Guardian Name</td>
                                                                    <td class="tt-value">: {{ $guardianName }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="tt-label">Guardian Phone</td>
                                                                    <td class="tt-value">: {{ $guardianPhone }}</td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    @endif
                                                </div>
                                                {{-- END BED WRAPPER --}}
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ALLOCATION MODAL --}}
    <div class="modal fade" id="allocationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius:12px; overflow:hidden;">
                <div class="modal-header border-0 px-4 pt-4 pb-2"
                    style="background:linear-gradient(135deg,#3a3f5c,#4a5080);">
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-white">
                            <i class="feather-home me-2"></i> Hostel Bed Allocation
                        </h5>
                        <small style="color:rgba(255,255,255,.6);">Fill the details to allocate this vacant bed</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="px-4 py-2" style="background:#f0f4ff; border-bottom:1px solid #dde3f5;">
                    <div class="d-flex align-items-center gap-3" style="font-size:13px;">
                        <span><i class="feather-home text-primary me-1"></i><strong id="info_hostel_name"></strong></span>
                        <span style="color:#bbb;">|</span>
                        <span>Room: <strong id="info_room_number"></strong></span>
                        <span style="color:#bbb;">|</span>
                        <span>Bed: <strong id="info_bed_number"></strong></span>
                        <span style="color:#bbb;">|</span>
                        <span class="text-success fw-semibold" id="info_bed_amount"></span>
                    </div>
                </div>

                <form id="allocationForm" action="{{ route('admin.hostelallocation.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="hostel_id" id="modal_hostel_id">
                    <input type="hidden" name="room_id"   id="modal_room_id">
                    <input type="hidden" name="bed_id"    id="modal_bed_id">

                    <div class="modal-body px-4 py-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-medium" style="font-size:13px;">Hostel</label>
                                <input type="text" id="display_hostel" class="form-control form-control-sm bg-light fw-semibold" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-medium" style="font-size:13px;">Room No.</label>
                                <input type="text" id="display_room" class="form-control form-control-sm bg-light fw-semibold" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-medium" style="font-size:13px;">Bed No.</label>
                                <input type="text" id="display_bed" class="form-control form-control-sm bg-light fw-semibold" readonly>
                            </div>

                            {{-- STUDENT SELECT --}}
                            <div class="col-12">
                                <label class="form-label fw-medium" style="font-size:13px;">
                                    Student <span class="text-danger">*</span>
                                </label>
                                <select name="student_id" id="modal_student_id"
                                    class="form-select form-select-sm" style="font-size:13px;" required>
                                    <option value="">-- Select Student --</option>
                                    @foreach (\App\Models\Student::with(['activeEnrollment.class.streams'])
                                        ->where('tenant_id', auth()->user()->tenant_id)
                                        ->orderBy('first_name')->orderBy('last_name')->get() as $st)
                                        @php
                                            $enrollment = $st->activeEnrollment;
                                            $class      = $enrollment?->class;
                                            $branchName = null;
                                            if ($class && $class->streams->isNotEmpty()) {
                                                $branchName = $class->streams->first()->name ?? null;
                                            }
                                        @endphp
                                        <option value="{{ $st->id }}">
                                            {{ trim($st->first_name . ' ' . $st->last_name) }}
                                            @if ($st->admission_number) [{{ $st->admission_number }}] @endif
                                            @if ($branchName) - {{ $branchName }} @endif
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted" style="font-size:11px; display:block; margin-top:4px;">
                                    <i class="feather-info"></i> Format: Name [Admission Number] - Branch
                                </small>

                                {{-- Already allocated warning (modal ke andar) --}}
                                <div id="modal_already_banner"
                                     style="display:none;"
                                     class="alert alert-warning d-flex align-items-start gap-2 mt-2 mb-0 py-2">
                                    
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-medium" style="font-size:13px;">
                                    From Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="from_date" id="modal_from_date"
                                    class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium" style="font-size:13px;">To Date</label>
                                <input type="date" name="to_date" id="modal_to_date" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium" style="font-size:13px;">
                                    Hostel Fee (Monthly) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" name="fee" id="modal_fee" class="form-control"
                                        step="0.01" min="0" placeholder="0.00" required>
                                </div>
                                <div id="fee_hint" class="text-muted mt-1" style="font-size:11px;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 px-4 pb-4 pt-1 gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="modal_submit_btn" class="btn btn-primary btn-sm px-4">
                            <i class="feather-save me-1"></i> Save Allocation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .content-area, .content-area-body, .beds-container { overflow: visible !important; }
        .room-block { animation: fadeUp .35s ease both; position: relative; overflow: visible !important; }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .bed-wrapper { position: relative; overflow: visible !important; z-index: 1; }
        .bed-wrapper:hover { z-index: 9999 !important; }
        .bed-wrapper:hover .bed-card { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,0.13); }
        .bed-vacant:hover { box-shadow: 0 6px 20px rgba(46,204,113,0.25) !important; }
        .bed-tooltip {
            display: none; position: absolute;
            top: 37%; left: calc(100% + 12px);
            transform: translateY(-50%);
            background: #fff; border: 1px solid #dde3ef;
            border-radius: 8px; padding: 10px 14px;
            min-width: 250px; box-shadow: 0 8px 28px rgba(0,0,0,0.25);
            z-index: 99999 !important; pointer-events: none; white-space: nowrap;
        }
        .bed-tooltip::before {
            content: ''; position: absolute; left: -7px; top: 37%;
            transform: translateY(-50%);
            border-top: 7px solid transparent; border-bottom: 7px solid transparent;
            border-right: 7px solid #dde3ef;
        }
        .bed-tooltip::after {
            content: ''; position: absolute; left: -6px; top: 37%;
            transform: translateY(-50%);
            border-top: 6px solid transparent; border-bottom: 6px solid transparent;
            border-right: 6px solid #fff;
        }
        .bed-wrapper:hover .bed-tooltip { display: block; animation: tooltipInSide .2s ease; }
        @keyframes tooltipInSide {
            from { opacity: 0; transform: translateY(-50%) translateX(-10px); }
            to   { opacity: 1; transform: translateY(-50%) translateX(0); }
        }
        .tt-label { padding: 2px 8px 2px 0; color: #888; white-space: nowrap; font-weight: 500; }
        .tt-value { padding: 2px 0; font-weight: 600; color: #222; }
        .content-area-header { min-height: 56px; }
        select.form-select-sm option { padding: 8px; font-size: 13px; }
        select.form-select-sm { cursor: pointer; }
    </style>

    <script>
        // ── Modal open karo ──
        function openAllocationModal(bedId, hostelName, hostelId, roomNumber, roomId, bedNumber, bedAmount) {
            document.getElementById('modal_hostel_id').value  = hostelId;
            document.getElementById('modal_room_id').value    = roomId;
            document.getElementById('modal_bed_id').value     = bedId;
            document.getElementById('display_hostel').value   = hostelName;
            document.getElementById('display_room').value     = roomNumber;
            document.getElementById('display_bed').value      = bedNumber;
            document.getElementById('info_hostel_name').textContent = hostelName;
            document.getElementById('info_room_number').textContent = roomNumber;
            document.getElementById('info_bed_number').textContent  = bedNumber;

            if (bedAmount > 0) {
                const monthly = (bedAmount / 12).toFixed(2);
                document.getElementById('modal_fee').value = monthly;
                document.getElementById('info_bed_amount').textContent =
                    '₹' + parseFloat(bedAmount).toLocaleString() + '/yr  →  ₹' + parseFloat(monthly).toLocaleString() + '/mo';
                document.getElementById('fee_hint').textContent =
                    'Annual fee ₹' + parseFloat(bedAmount).toLocaleString() +
                    ' ÷ 12 = ₹' + parseFloat(monthly).toLocaleString() + '/month (editable)';
            } else {
                document.getElementById('modal_fee').value = '';
                document.getElementById('info_bed_amount').textContent = 'Fee not set';
                document.getElementById('fee_hint').textContent = '';
            }

            // Reset student + banner + button
            document.getElementById('modal_student_id').value    = '';
            document.getElementById('modal_from_date').value     = '{{ date('Y-m-d') }}';
            document.getElementById('modal_to_date').value       = '';
            document.getElementById('modal_already_banner').style.display = 'none';
            document.getElementById('modal_submit_btn').disabled = false;

            new bootstrap.Modal(document.getElementById('allocationModal')).show();
        }

        // ── Student change → AJAX duplicate check ──
        document.getElementById('modal_student_id').addEventListener('change', function () {
            const studentId = this.value;
            const banner    = document.getElementById('modal_already_banner');
            const submitBtn = document.getElementById('modal_submit_btn');

            // Reset
            banner.style.display    = 'none';
            submitBtn.disabled      = false;

            if (!studentId) return;

            // Loading state
            submitBtn.disabled    = true;
            submitBtn.innerHTML   = '<span class="spinner-border spinner-border-sm me-1"></span> Checking...';

            fetch(`{{ url('admin/hostelallocation/student') }}/${studentId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.found) {
                        // Already allocated — block karo
                        banner.style.display  = 'flex';
                        submitBtn.disabled    = true;
                        submitBtn.innerHTML   = '<i class="feather-save me-1"></i> Save Allocation';
                    } else {
                        // OK — allow karo
                        banner.style.display  = 'none';
                        submitBtn.disabled    = false;
                        submitBtn.innerHTML   = '<i class="feather-save me-1"></i> Save Allocation';
                    }
                })
                .catch(() => {
                    // Network error — conservative: block nahi karo, allow karo
                    banner.style.display  = 'none';
                    submitBtn.disabled    = false;
                    submitBtn.innerHTML   = '<i class="feather-save me-1"></i> Save Allocation';
                });
        });
    </script>

@endsection