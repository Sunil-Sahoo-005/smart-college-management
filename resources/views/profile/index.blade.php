@extends('admin_layout')

@section('content')
<div class="nxl-content">

    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">My Profile</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">My Profile</li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <div class="profile-layout">

            {{-- ══════════════════════════════
                 LEFT SIDEBAR
            ══════════════════════════════ --}}
            <div class="profile-sidebar">
                <div class="pcard">

                    {{-- Cover Banner --}}
                    <div class="pcard-cover">
                        <div class="pcover-pattern"></div>
                        <div class="pcover-av-wrap">
                            {{ strtoupper(substr($user->first_name ?? 'U', 0, 1) . substr($user->last_name ?? '', 0, 1)) }}
                        </div>
                    </div>

                    {{-- Name & Role --}}
                    <div class="pcard-identity">
                        <h5 class="pcard-name">
                            {{ trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) }}
                        </h5>
                        <span class="pcard-role-badge">
                            @if($portalType === 'teacher') Teacher
                            @elseif($portalType === 'student') Student
                            @else Admin
                            @endif
                        </span>
                        <div class="pcard-tenant">
                            <i class="feather-home"></i>
                            {{ $tenantName }}
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="pcard-divider"></div>

                    {{-- Info List --}}
                    <div class="pcard-info-list">

                        @if(!empty($user->email))
                        <div class="pinfo-row">
                            <div class="pinfo-icon" style="background:#EAF3FB;">
                                <i class="feather-mail" style="color:#185FA5;"></i>
                            </div>
                            <div class="pinfo-content">
                                <div class="pinfo-label">Email</div>
                                <div class="pinfo-value">{{ $user->email }}</div>
                            </div>
                        </div>
                        @endif

                        @if(!empty($user->phone))
                        <div class="pinfo-row">
                            <div class="pinfo-icon" style="background:#EAF3DE;">
                                <i class="feather-phone" style="color:#3B6D11;"></i>
                            </div>
                            <div class="pinfo-content">
                                <div class="pinfo-label">Phone</div>
                                <div class="pinfo-value">{{ $user->phone }}</div>
                            </div>
                        </div>
                        @endif

                        @if(!empty($user->employee_id) || !empty($user->student_id))
                        <div class="pinfo-row">
                            <div class="pinfo-icon" style="background:#F3EEFE;">
                                <i class="feather-credit-card" style="color:#534AB7;"></i>
                            </div>
                            <div class="pinfo-content">
                                <div class="pinfo-label">{{ $portalType === 'teacher' ? 'Employee ID' : 'Student ID' }}</div>
                                <div class="pinfo-value">{{ $user->employee_id ?? $user->student_id }}</div>
                            </div>
                        </div>
                        @endif

                        @if(!empty($user->joining_date) || !empty($user->admission_date))
                        <div class="pinfo-row">
                            <div class="pinfo-icon" style="background:#FFF8E6;">
                                <i class="feather-calendar" style="color:#BA7517;"></i>
                            </div>
                            <div class="pinfo-content">
                                <div class="pinfo-label">{{ $portalType === 'teacher' ? 'Joined' : 'Admitted' }}</div>
                                <div class="pinfo-value">
                                    {{ \Carbon\Carbon::parse($user->joining_date ?? $user->admission_date)->format('d M Y') }}
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(!empty($user->address))
                        <div class="pinfo-row">
                            <div class="pinfo-icon" style="background:#FFF0F0;">
                                <i class="feather-map-pin" style="color:#A32D2D;"></i>
                            </div>
                            <div class="pinfo-content">
                                <div class="pinfo-label">Address</div>
                                <div class="pinfo-value">{{ $user->address }}</div>
                            </div>
                        </div>
                        @endif

                        @if(!empty($user->class_name) || !empty($user->section))
                        <div class="pinfo-row">
                            <div class="pinfo-icon" style="background:#EAF3FB;">
                                <i class="feather-book-open" style="color:#185FA5;"></i>
                            </div>
                            <div class="pinfo-content">
                                <div class="pinfo-label">Class / Section</div>
                                <div class="pinfo-value">
                                    {{ $user->class_name ?? '' }}{{ !empty($user->section) ? ' – ' . $user->section : '' }}
                                </div>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════
                 RIGHT CONTENT
            ══════════════════════════════ --}}
            <div class="profile-main">

                {{-- Change Password Card --}}
                <div class="pcard pw-card">
                    <div class="pw-card-header">
                        <div class="pw-header-icon">
                            <i class="feather-lock"></i>
                        </div>
                        <div>
                            <div class="pw-header-title">Change Password</div>
                            <div class="pw-header-sub">Update your account password securely</div>
                        </div>
                    </div>

                    <div class="pw-card-body">

                        {{-- Success Alert --}}
                        @if(session('pp_success'))
                        <div class="pw-alert pw-alert-success">
                            <i class="feather-check-circle"></i>
                            {{ session('pp_success') }}
                        </div>
                        @endif

                        {{-- Error Alert --}}
                        @if($errors->has('current_password'))
                        <div class="pw-alert pw-alert-error">
                            <i class="feather-alert-circle"></i>
                            {{ $errors->first('current_password') }}
                        </div>
                        @endif

                        <form action="{{ route('change.password') }}" method="POST">
                            @csrf

                            <div class="pw-fields">

                                {{-- Current Password --}}
                                <div class="pw-field-group">
                                    <label class="pw-label">Current Password</label>
                                    <div class="pw-input-wrap">
                                        <span class="pw-input-icon"><i class="feather-lock"></i></span>
                                        <input type="password" name="current_password" id="cpCurrent"
                                               class="pw-input {{ $errors->has('current_password') ? 'is-invalid' : '' }}"
                                               placeholder="Enter your current password" required>
                                        <button type="button" class="pw-eye" onclick="cpToggle('cpCurrent', this)">
                                            <i class="feather-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- New Password --}}
                                <div class="pw-field-group">
                                    <label class="pw-label">New Password</label>
                                    <div class="pw-input-wrap">
                                        <span class="pw-input-icon"><i class="feather-key"></i></span>
                                        <input type="password" name="new_password" id="cpNew"
                                               class="pw-input"
                                               placeholder="Enter new password"
                                               oninput="cpStrength(this.value)" required>
                                        <button type="button" class="pw-eye" onclick="cpToggle('cpNew', this)">
                                            <i class="feather-eye"></i>
                                        </button>
                                    </div>
                                    {{-- Strength Bar --}}
                                    <div class="pw-strength-wrap">
                                        <div class="pw-strength-bars">
                                            <div class="pw-sb" id="cpSb1"></div>
                                            <div class="pw-sb" id="cpSb2"></div>
                                            <div class="pw-sb" id="cpSb3"></div>
                                            <div class="pw-sb" id="cpSb4"></div>
                                        </div>
                                        <span class="pw-strength-txt" id="cpStrTxt">Min. 8 characters</span>
                                    </div>
                                </div>

                                {{-- Confirm Password --}}
                                <div class="pw-field-group">
                                    <label class="pw-label">Confirm New Password</label>
                                    <div class="pw-input-wrap">
                                        <span class="pw-input-icon"><i class="feather-check-circle"></i></span>
                                        <input type="password" name="new_password_confirmation" id="cpConfirm"
                                               class="pw-input"
                                               placeholder="Re-enter new password"
                                               oninput="cpMatchCheck()" required>
                                        <button type="button" class="pw-eye" onclick="cpToggle('cpConfirm', this)">
                                            <i class="feather-eye"></i>
                                        </button>
                                    </div>
                                    <div class="pw-match-hint" id="cpMatchHint"></div>
                                </div>

                            </div>

                            <button type="submit" id="cpSubmit" class="pw-submit-btn">
                                <i class="feather-check"></i>
                                Update Password
                            </button>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
/* ══════════════════════════════════════════
   LAYOUT
══════════════════════════════════════════ */
.profile-layout {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 24px;
    align-items: start;
}
@media (max-width: 991px) {
    .profile-layout { grid-template-columns: 1fr; }
}

/* ══════════════════════════════════════════
   BASE CARD
══════════════════════════════════════════ */
.pcard {
    background: #fff;
    border: 0.5px solid #e4eaf3;
    border-radius: 16px;
    overflow: hidden;
}

/* ══════════════════════════════════════════
   PROFILE SIDEBAR CARD
══════════════════════════════════════════ */
.pcard-cover {
    position: relative;
    height: 90px;
    background: linear-gradient(135deg, #1054A0 0%, #2B7FD4 60%, #5BA8EE 100%);
    overflow: visible; /* allow avatar to overflow below */
}
.pcover-pattern {
    position: absolute; inset: 0;
    background-image:
        radial-gradient(circle at 20% 50%, rgba(255,255,255,.08) 0%, transparent 55%),
        radial-gradient(circle at 80% 20%, rgba(255,255,255,.06) 0%, transparent 45%);
}

/* Avatar wrapper — double ring effect */
.pcover-av-wrap {
    position: absolute;
    bottom: -32px;
    left: 50%;
    transform: translateX(-50%);
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: #EAF3FB;
    color: #0C447C;
    font-size: 20px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    letter-spacing: 0.03em;
    /* white gap ring + blue border ring */
    box-shadow: 0 0 0 3px #ffffff, 0 0 0 5px #185FA5;
    z-index: 2;
}

.pcard-identity {
    padding: 48px 20px 20px; /* increased top padding to make room for avatar */
    text-align: center;
}
.pcard-name {
    font-size: 15px; font-weight: 700; color: #1a2332;
    margin-bottom: 8px;
}
.pcard-role-badge {
    display: inline-block;
    font-size: 11px; font-weight: 600;
    padding: 4px 14px; border-radius: 20px;
    background: #E6F1FB; color: #185FA5;
    letter-spacing: .03em;
}
.pcard-tenant {
    margin-top: 8px;
    font-size: 12px; color: #8a96a6;
    display: flex; align-items: center; justify-content: center; gap: 5px;
}
.pcard-tenant i { font-size: 12px; }

.pcard-divider {
    height: 0.5px; background: #f0f4f8;
    margin: 0 20px;
}

.pcard-info-list {
    padding: 16px 20px 20px;
    display: flex; flex-direction: column; gap: 4px;
}

.pinfo-row {
    display: flex; align-items: flex-start; gap: 12px;
    padding: 10px 12px;
    border-radius: 10px;
    transition: background .15s;
}
.pinfo-row:hover { background: #f8fbff; }

.pinfo-icon {
    width: 34px; height: 34px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; flex-shrink: 0;
}
.pinfo-label {
    font-size: 10px; font-weight: 600;
    text-transform: uppercase; letter-spacing: .06em;
    color: #adb5bd; margin-bottom: 2px;
}
.pinfo-value {
    font-size: 13px; font-weight: 500; color: #1a2332;
    word-break: break-word;
}

/* ══════════════════════════════════════════
   PASSWORD CARD
══════════════════════════════════════════ */
.pw-card-header {
    display: flex; align-items: center; gap: 14px;
    padding: 22px 28px 20px;
    border-bottom: 0.5px solid #f0f4f8;
}
.pw-header-icon {
    width: 42px; height: 42px; border-radius: 10px;
    background: linear-gradient(135deg, #1054A0, #2B7FD4);
    color: #fff; font-size: 18px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.pw-header-title { font-size: 15px; font-weight: 700; color: #1a2332; }
.pw-header-sub   { font-size: 12px; color: #8a96a6; margin-top: 1px; }

.pw-card-body { padding: 28px; }

/* Alerts */
.pw-alert {
    display: flex; align-items: center; gap: 10px;
    padding: 12px 16px; border-radius: 10px;
    font-size: 13px; margin-bottom: 22px;
}
.pw-alert-success { background: #EAF3DE; color: #3B6D11; border: 0.5px solid #c0dd97; }
.pw-alert-error   { background: #FCEBEB; color: #A32D2D; border: 0.5px solid #f7c1c1; }

/* Fields */
.pw-fields {
    display: flex; flex-direction: column; gap: 22px;
    max-width: 480px;
    margin-bottom: 28px;
}
.pw-field-group { display: flex; flex-direction: column; gap: 7px; }
.pw-label {
    font-size: 11px; font-weight: 600;
    text-transform: uppercase; letter-spacing: .06em;
    color: #6c757d;
}

/* Input */
.pw-input-wrap {
    display: flex; align-items: center;
    border: 0.5px solid #dee2e6; border-radius: 10px;
    background: #fff; overflow: hidden;
    transition: border-color .15s, box-shadow .15s;
}
.pw-input-wrap:focus-within {
    border-color: #185FA5;
    box-shadow: 0 0 0 3px rgba(24,95,165,.08);
}
.pw-input-icon {
    width: 42px; display: flex; align-items: center; justify-content: center;
    font-size: 14px; color: #185FA5; flex-shrink: 0;
    background: #f8fbff; border-right: 0.5px solid #e4eaf3;
    height: 42px;
}
.pw-input {
    flex: 1; border: none; outline: none;
    padding: 0 12px; font-size: 13px; color: #1a2332;
    height: 42px; background: transparent;
}
.pw-input::placeholder { color: #c0c8d3; }
.pw-eye {
    width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;
    background: transparent; border: none; cursor: pointer;
    color: #adb5bd; transition: color .15s; flex-shrink: 0;
}
.pw-eye:hover { color: #185FA5; }

/* Strength */
.pw-strength-wrap {
    display: flex; align-items: center; gap: 10px; margin-top: 6px;
}
.pw-strength-bars { display: flex; gap: 4px; flex: 1; }
.pw-sb { flex: 1; height: 4px; border-radius: 2px; background: #e9ecef; transition: background .2s; }
.pw-sb.weak   { background: #E24B4A; }
.pw-sb.medium { background: #BA7517; }
.pw-sb.strong { background: #3B6D11; }
.pw-strength-txt { font-size: 11px; color: #adb5bd; white-space: nowrap; }

/* Match hint */
.pw-match-hint { font-size: 12px; min-height: 18px; }

/* Submit */
.pw-submit-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 11px 28px;
    background: linear-gradient(135deg, #1054A0, #2B7FD4);
    color: #fff; border: none; border-radius: 10px;
    font-size: 13px; font-weight: 600; cursor: pointer;
    transition: opacity .15s, transform .12s;
    box-shadow: 0 4px 14px rgba(24,95,165,.25);
}
.pw-submit-btn:hover   { opacity: .92; transform: translateY(-1px); }
.pw-submit-btn:active  { transform: translateY(0); }
.pw-submit-btn:disabled { opacity: .5; cursor: not-allowed; transform: none; }
</style>

<script>
function cpToggle(id, btn) {
    var inp = document.getElementById(id);
    var show = inp.type === 'password';
    inp.type = show ? 'text' : 'password';
    btn.querySelector('i').className = show ? 'feather-eye-off' : 'feather-eye';
}

function cpStrength(val) {
    var score = [
        val.length >= 8,
        /[A-Z]/.test(val),
        /[0-9]/.test(val),
        /[^A-Za-z0-9]/.test(val)
    ].filter(Boolean).length;

    var labels  = ['', 'Weak', 'Fair', 'Strong', 'Very strong'];
    var classes = ['', 'weak', 'medium', 'strong', 'strong'];
    for (var i = 1; i <= 4; i++) {
        document.getElementById('cpSb' + i).className =
            'pw-sb ' + (i <= score ? classes[score] : '');
    }
    document.getElementById('cpStrTxt').textContent =
        score > 0 ? labels[score] : 'Min. 8 characters';
    cpMatchCheck();
}

function cpMatchCheck() {
    var np   = document.getElementById('cpNew').value;
    var cp   = document.getElementById('cpConfirm').value;
    var hint = document.getElementById('cpMatchHint');
    var btn  = document.getElementById('cpSubmit');
    if (!cp) { hint.textContent = ''; btn.disabled = false; return; }
    if (np === cp) {
        hint.innerHTML = '<span style="color:#3B6D11;">✓ Passwords match</span>';
        btn.disabled = false;
    } else {
        hint.innerHTML = '<span style="color:#A32D2D;">✗ Passwords do not match</span>';
        btn.disabled = true;
    }
}
</script>

@endsection