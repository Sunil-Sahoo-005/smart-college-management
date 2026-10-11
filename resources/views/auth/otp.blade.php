<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OTP Verification — {{ config('app.name') }}</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.min.css') }}">
    <style>
        .otp-input {
            letter-spacing: 10px;
            font-size: 28px;
            font-weight: 700;
            text-align: center;
        }
        #timer { font-weight: 600; color: #4f46e5; }
        #timer.expired { color: #dc2626; }
    </style>
</head>
<body>
<main class="auth-creative-wrapper">
    <div class="auth-creative-inner">
        <div class="creative-card-wrapper">
            <div class="card my-4 overflow-hidden" style="max-width:440px;margin:auto;z-index:1;">
                <div class="card-body p-sm-5">

                    {{-- Icon --}}
                    <div class="text-center mb-4">
                        <div style="width:64px;height:64px;background:#ede9fe;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;">
                            <svg width="28" height="28" fill="none" stroke="#4f46e5" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 3H8L2 7h20l-6-4z"/>
                            </svg>
                        </div>
                    </div>

                    <h2 class="fs-20 fw-bolder mb-2 text-center">Check Your Email</h2>
                    <p class="fs-12 text-muted text-center mb-4">
                        We sent a 6-digit OTP to<br>
                        <strong class="text-dark">{{ session('otp_email') }}</strong>
                    </p>

                    {{-- Alerts --}}
                    @if(session('success'))
                        <div class="alert alert-success py-2 text-center">{{ session('success') }}</div>
                    @endif
                    @if($errors->has('otp'))
                        <div class="alert alert-danger py-2 text-center">{{ $errors->first('otp') }}</div>
                    @endif

                    {{-- OTP Form --}}
                    <form action="{{ route('otp.verify') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <input type="text" name="otp"
                                class="form-control form-control-lg otp-input @error('otp') is-invalid @enderror"
                                maxlength="6" placeholder="• • • • • •"
                                inputmode="numeric" pattern="[0-9]{6}"
                                autofocus autocomplete="one-time-code" required>
                        </div>

                        {{-- Timer --}}
                        <p class="text-center fs-12 mb-3">
                            OTP expires in <span id="timer">05:00</span>
                        </p>

                        <button type="submit" class="btn btn-lg btn-primary w-100 mb-3">
                            Verify OTP
                        </button>
                    </form>

                    {{-- Resend --}}
                    <form action="{{ route('otp.resend') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary w-100" id="resendBtn" disabled>
                            Resend OTP <span id="resendTimer">(wait 60s)</span>
                        </button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="{{ route('login') }}" class="fs-12 text-muted">← Back to Login</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</main>

<script src="{{ asset('assets/vendors/js/vendors.min.js') }}"></script>
<script>
    // OTP expiry countdown — 5 minutes
    (function () {
        let total = 5 * 60;
        const el  = document.getElementById('timer');
        const t   = setInterval(() => {
            total--;
            const m = String(Math.floor(total / 60)).padStart(2, '0');
            const s = String(total % 60).padStart(2, '0');
            el.textContent = m + ':' + s;
            if (total <= 0) {
                clearInterval(t);
                el.textContent = 'Expired';
                el.classList.add('expired');
            }
        }, 1000);
    })();

    // Resend button — 60 second cooldown
    (function () {
        let sec = 60;
        const btn  = document.getElementById('resendBtn');
        const span = document.getElementById('resendTimer');
        const t    = setInterval(() => {
            sec--;
            span.textContent = '(wait ' + sec + 's)';
            if (sec <= 0) {
                clearInterval(t);
                btn.disabled     = false;
                span.textContent = '';
            }
        }, 1000);
    })();
</script>
</body>
</html>