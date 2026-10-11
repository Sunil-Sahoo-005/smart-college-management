@extends('teacher_layout')
@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    *, *::before, *::after { box-sizing: border-box; }
    body, .nxl-content {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background: radial-gradient(circle at top right, rgba(190,217,193,0.46), transparent 28%),
                    linear-gradient(180deg, #f5efe3 0%, #eef3ea 100%);
        color: #18352d;
    }
    .lc-shell { padding: 26px; display: flex; flex-direction: column; gap: 22px; max-width: 860px; margin: 0 auto; }

    /* Header */
    .lc-header {
        display: flex; align-items: center; gap: 14px;
    }
    .back-btn {
        display:inline-flex; align-items:center; gap:6px; padding:10px 16px; border-radius:12px;
        background:rgba(255,255,255,.88); border:1px solid rgba(24,53,45,.1);
        color:#18352d; font-family:inherit; font-size:13px; font-weight:600; text-decoration:none; transition:.2s;
    }
    .back-btn:hover { background:#12342b; color:#f7f3ea; }
    .lc-header-text h1 { font-size:24px; font-weight:800; letter-spacing:-.04em; margin:0 0 4px; color:#12342b; }
    .lc-header-text p  { font-size:13px; color:#687872; margin:0; }

    /* Form card */
    .form-card {
        border-radius:26px; padding:30px;
        background:rgba(255,255,255,.92); border:1px solid rgba(24,53,45,.08);
        box-shadow:0 20px 55px rgba(28,49,43,.09);
    }
    .form-section-title {
        font-size:11px; text-transform:uppercase; letter-spacing:.08em; color:#8a7c64;
        font-weight:700; margin-bottom:18px; padding-bottom:10px;
        border-bottom:1px solid rgba(24,53,45,.08);
    }
    .form-row   { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px; }
    .form-row.full { grid-template-columns:1fr; }
    .form-group { display:flex; flex-direction:column; gap:6px; }
    .form-label { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#687872; }
    .form-control {
        padding:12px 14px; border-radius:12px; border:1.5px solid rgba(24,53,45,.14);
        font-family:inherit; font-size:14px; color:#18352d; background:#fafaf9;
        transition:.2s; outline:none;
    }
    .form-control:focus { border-color:#12342b; background:#fff; box-shadow:0 0 0 3px rgba(18,52,43,.08); }
    .form-control.error { border-color:#ef4444; }

    textarea.form-control { min-height:110px; resize:vertical; }

    .form-hint { font-size:12px; color:#9caea7; margin-top:2px; }

    /* Days calculated badge */
    .days-badge {
        display:inline-flex; align-items:center; gap:8px; padding:10px 16px;
        border-radius:12px; background:#edf5ef; color:#12342b; font-size:14px; font-weight:700;
    }

    /* Submit */
    .submit-row { display:flex; gap:12px; justify-content:flex-end; flex-wrap:wrap; margin-top:8px; }
    .btn-submit {
        padding:14px 28px; border-radius:14px; border:none; font-family:inherit;
        font-size:14px; font-weight:700; cursor:pointer; transition:.2s;
        background:linear-gradient(135deg,#12342b,#1d5548); color:#f7f3ea;
    }
    .btn-submit:hover { opacity:.88; transform:translateY(-1px); }
    .btn-cancel {
        padding:14px 22px; border-radius:14px; border:1.5px solid rgba(24,53,45,.15);
        font-family:inherit; font-size:14px; font-weight:600; cursor:pointer; transition:.2s;
        background:transparent; color:#687872; text-decoration:none; display:inline-flex; align-items:center;
    }
    .btn-cancel:hover { background:#12342b; color:#f7f3ea; border-color:#12342b; }

    /* Errors */
    .error-box { background:#fee2e2; color:#991b1b; border-radius:14px; padding:14px 18px; margin-bottom:4px; }
    .error-box ul { margin:0; padding-left:18px; font-size:13px; }

    @media(max-width:768px){ .lc-shell{padding:16px;} .form-row{grid-template-columns:1fr;} }
</style>

<div class="nxl-content">
<div class="lc-shell">

    {{-- Header --}}
    <div class="lc-header">
        <a href="{{ route('teacher.my_leave.index') }}" class="back-btn">
            <i class="feather-arrow-left"></i> Back
        </a>
        <div class="lc-header-text">
            <h1>Apply for Leave</h1>
            <p>Fill in the details below to submit a leave request for admin approval.</p>
        </div>
    </div>

    {{-- Errors --}}
    @if($errors->any())
        <div class="error-box">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form --}}
    <div class="form-card">
        <form method="POST" action="{{ route('teacher.my_leave.store') }}">
            @csrf

            <div class="form-section-title">Leave Details</div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="leave_type_id">Leave Type <span style="color:#ef4444">*</span></label>
                    <select name="leave_type_id" id="leave_type_id"
                            class="form-control @error('leave_type_id') error @enderror" required>
                        <option value="">— Select leave type —</option>
                        @foreach($leaveTypes as $type)
                            <option value="{{ $type->id }}" @selected(old('leave_type_id') == $type->id)>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('leave_type_id')
                        <span style="color:#ef4444;font-size:12px;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="half_day">Half Day Option</label>
                    <select name="half_day" id="half_day"
                            class="form-control @error('half_day') error @enderror">
                        <option value="none"   @selected(old('half_day','none') === 'none')>Full Day(s)</option>
                        <option value="first"  @selected(old('half_day') === 'first')>First Half</option>
                        <option value="second" @selected(old('half_day') === 'second')>Second Half</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="from_date">From Date <span style="color:#ef4444">*</span></label>
                    <input type="date" name="from_date" id="from_date"
                           class="form-control @error('from_date') error @enderror"
                           value="{{ old('from_date') }}" required
                           min="{{ \Carbon\Carbon::today()->toDateString() }}">
                    @error('from_date')
                        <span style="color:#ef4444;font-size:12px;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="to_date">To Date <span style="color:#ef4444">*</span></label>
                    <input type="date" name="to_date" id="to_date"
                           class="form-control @error('to_date') error @enderror"
                           value="{{ old('to_date') }}" required
                           min="{{ \Carbon\Carbon::today()->toDateString() }}">
                    @error('to_date')
                        <span style="color:#ef4444;font-size:12px;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="total_days">Total Days <span style="color:#ef4444">*</span></label>
                    <input type="number" name="total_days" id="total_days"
                           class="form-control @error('total_days') error @enderror"
                           value="{{ old('total_days', 1) }}" min="0.5" step="0.5" required>
                    <span class="form-hint">Auto-calculated from dates. You can adjust for half days.</span>
                </div>

                <div class="form-group" style="justify-content:flex-end;padding-bottom:6px;">
                    <label class="form-label" style="visibility:hidden;">Preview</label>
                    <div class="days-badge" id="daysBadge">
                        <i class="feather-calendar"></i>
                        <span id="daysCount">0</span> day(s) selected
                    </div>
                </div>
            </div>

            <div class="form-row full">
                <div class="form-group">
                    <label class="form-label" for="reason">Reason</label>
                    <textarea name="reason" id="reason"
                              class="form-control @error('reason') error @enderror"
                              placeholder="Briefly describe the reason for your leave...">{{ old('reason') }}</textarea>
                    @error('reason')
                        <span style="color:#ef4444;font-size:12px;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="submit-row">
                <a href="{{ route('teacher.my_leave.index') }}" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-submit">
                    <i class="feather-send"></i> &nbsp; Submit Request
                </button>
            </div>
        </form>
    </div>

</div>
</div>

<script>
    const fromDate  = document.getElementById('from_date');
    const toDate    = document.getElementById('to_date');
    const totalDays = document.getElementById('total_days');
    const daysCount = document.getElementById('daysCount');
    const halfDay   = document.getElementById('half_day');

    function calcDays() {
        if (fromDate.value && toDate.value) {
            const from = new Date(fromDate.value);
            const to   = new Date(toDate.value);
            if (to >= from) {
                let diff = Math.round((to - from) / (1000*60*60*24)) + 1;
                if (halfDay.value !== 'none') diff = 0.5;
                totalDays.value = diff;
                daysCount.textContent = diff;
            }
        }
    }

    fromDate.addEventListener('change', () => {
        if (toDate.value && toDate.value < fromDate.value) toDate.value = fromDate.value;
        toDate.min = fromDate.value;
        calcDays();
    });
    toDate.addEventListener('change', calcDays);
    halfDay.addEventListener('change', calcDays);

    calcDays();
</script>

@endsection
