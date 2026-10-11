<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="keyword" content="">
    <meta name="author" content="theme_ocean">

    <title>Sudhananda ERP | Login</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/favicon.ico') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendors.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/daterangepicker.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/theme.min.css') }}" />

    <style>
        :root {
            --brand-primary: #1546b0;
            --brand-primary-deep: #0f3180;
            --brand-secondary: #f2a9c4;
            --brand-secondary-soft: rgba(242, 169, 196, 0.2);
            --border-soft: rgba(255, 255, 255, 0.32);
            --text-main: #ffffff;
            --text-muted: rgba(255, 255, 255, 0.78);
            --text-premium: #fff7e8;
            --text-premium-soft: #f8e7c2;
            --text-premium-accent: #ffd7a1;
            --text-premium-danger: #ffe3e8;
            --shadow-soft: none;
            --danger-soft: rgba(220, 53, 69, 0.14);
            --danger-border: rgba(255, 99, 132, 0.45);
        }

        body {
            min-height: 100vh;
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: url('{{ asset('assets/images/sudhananada.jpg') }}') center center / cover no-repeat fixed;
            color: var(--text-main);
        }

        .s-login-page,
        .s-login-page-inner,
        .s-login-card-wrap {
            min-height: 100vh;
        }

        .s-login-page {
            position: relative;
            overflow: hidden;
        }

        .s-login-page-inner {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
        }

        .s-login-card-wrap {
            width: 100%;
            max-width: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-shell {
            width: 100%;
            max-width: 460px;
            margin: 0 auto;
            border: 1px solid var(--border-soft);
            border-radius: 30px;
            overflow: hidden;
            background: transparent !important;
            backdrop-filter: blur(22px);
            -webkit-backdrop-filter: blur(22px);
            box-shadow: var(--shadow-soft);
            animation: cardEntrance 0.8s ease-out;
        }

        .login-shell,
        .card.login-shell,
        div.login-shell {
            background: transparent !important;
            border-color: rgba(255, 255, 255, 0.22) !important;
            box-shadow: var(--shadow-soft) !important;
        }

        .login-left-panel {
            position: relative;
            padding: 44px 34px 36px;
            background: transparent;
        }

        .brand-logo-wrap {
            width: 100%;
            max-width: 100%;
            margin: 0 auto 18px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: floatLogo 5s ease-in-out infinite;
        }

        .brand-logo-wrap img {
            width: 90%;
            max-width: 320px;
            height: auto;
            object-fit: contain;
        }

        .brand-kicker {
            font-size: 0.72rem;
            letter-spacing: 0.18rem;
            text-transform: uppercase;
            color: var(--text-premium-accent);
            margin-bottom: 10px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.65);
        }

        .login-subcopy {
            color: var(--text-main);
            font-size: 0.93rem;
            line-height: 1.65;
            text-shadow: 0 2px 12px rgba(0, 0, 0, 0.72);
        }

        .login-title {
            font-size: 1.9rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--text-premium);
            text-shadow: 0 3px 14px rgba(0, 0, 0, 0.72);
        }

        .login-subcopy {
            margin-bottom: 24px;
        }

        .form-label {
            font-size: 0.88rem;
            font-weight: 700;
            color: var(--text-premium);
            margin-bottom: 8px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.65);
        }

        .form-control {
            min-height: 56px;
            border-radius: 18px;
            border: 1px solid rgba(255, 255, 255, 0.42);
            background: rgba(255, 255, 255, 0.12) !important;
            color: var(--text-premium);
            padding: 14px 18px;
            transition: border-color 0.25s ease, box-shadow 0.25s ease, transform 0.25s ease, background 0.25s ease;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.45);
        }

        .form-control::placeholder {
            color: rgba(248, 231, 194, 0.88);
        }

        .form-control:focus {
            color: var(--text-premium);
            border-color: rgba(242, 169, 196, 0.92);
            background: rgba(255, 255, 255, 0.16) !important;
            box-shadow: 0 0 0 0.24rem rgba(242, 169, 196, 0.16);
            transform: translateY(-1px);
        }

        .form-control.is-invalid {
            border-color: rgba(255, 130, 130, 0.88);
            background: rgba(220, 53, 69, 0.1);
            box-shadow: none;
        }

        .password-field-wrap {
            position: relative;
        }

        .input-icon-wrap {
            position: relative;
        }

        .input-icon-wrap .form-control {
            padding-right: 54px;
        }

        .input-field-icon {
            position: absolute;
            top: 50%;
            right: 16px;
            transform: translateY(-50%);
            width: 34px;
            height: 34px;
            color: var(--text-premium);
            pointer-events: none;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.12);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.25);
        }

        .input-field-icon svg {
            width: 18px;
            height: 18px;
            stroke: currentColor;
        }

        .password-field-wrap .form-control {
            padding-right: 54px;
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 14px;
            transform: translateY(-50%);
            width: 34px;
            height: 34px;
            border: 0;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.12);
            color: var(--text-premium);
            cursor: pointer;
            transition: background 0.2s ease, transform 0.2s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.25);
        }

        .password-toggle:hover,
        .password-toggle:focus {
            background: rgba(242, 169, 196, 0.18);
            transform: translateY(-50%) scale(1.04);
        }

        .password-toggle svg {
            width: 18px;
            height: 18px;
            stroke: currentColor;
        }

        .password-toggle .eye-off {
            display: none;
        }

        .password-toggle.is-visible .eye-open {
            display: none;
        }

        .password-toggle.is-visible .eye-off {
            display: block;
        }

        .invalid-feedback {
            display: block;
            margin-top: 8px;
            color: var(--text-premium-danger);
            font-size: 0.84rem;
            font-weight: 600;
            text-shadow: 0 2px 8px rgba(99, 7, 21, 0.55);
        }

        .btn-login {
            min-height: 56px;
            border: 0;
            border-radius: 18px;
            background: linear-gradient(142deg, #2052bd91, #255ddfeb);
            color: #ffffff;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: 0.02rem;
            box-shadow: 0 18px 36px rgba(15, 49, 128, 0.34);
            transition: transform 0.25s ease, box-shadow 0.25s ease, filter 0.25s ease;
        }

        .btn-login:hover,
        .btn-login:focus {
            transform: translateY(-2px);
            filter: brightness(1.05);
            box-shadow: 0 22px 42px rgba(15, 49, 128, 0.42);
        }

        .alert-soft,
        .status-alert {
            border-radius: 16px;
            border: 1px solid var(--danger-border);
            background: var(--danger-soft);
            color: var(--text-premium);
            padding: 14px 16px;
            margin-bottom: 18px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.35);
        }

        .status-alert {
            border-color: rgba(79, 209, 150, 0.35);
            background: rgba(79, 209, 150, 0.12);
        }

        .brand-divider {
            width: 74px;
            height: 4px;
            border-radius: 999px;
            margin: 0 auto 20px;
            background: linear-gradient(90deg, var(--brand-secondary), rgba(255, 255, 255, 0.85));
        }

        .alert-soft ul {
            padding-left: 18px;
            margin: 0;
        }

        @keyframes cardEntrance {
            from {
                opacity: 0;
                transform: translateY(24px) scale(0.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes floatLogo {
            0%, 100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-6px);
            }
        }

        @media (max-width: 991.98px) {
            .login-shell {
                max-width: 460px;
            }
        }

        @media (max-width: 575.98px) {
            .login-left-panel {
                padding: 34px 22px 28px;
            }

            .brand-logo-wrap {
                margin-bottom: 16px;
            }
        }
    </style>
</head>

<body>
    <main class="s-login-page">
        <div class="s-login-page-inner">
            <div class="s-login-card-wrap">
                <div class="my-4 overflow-hidden login-shell">
                    <div class="login-left-panel">
                        <div class="brand-logo-wrap">
                            <img src="{{ asset('assets/images/logo/Sudhanada_logo.png') }}" alt="Sudhananda Logo">
                        </div>

                        <div class="text-center mb-4">
                            <h1 class="login-title">Sign In</h1>
                            <div class="brand-divider"></div>
                            <p class="login-subcopy mb-0">Access your account with the essential details only.</p>
                        </div>

                        @if (session('message'))
                            <div class="status-alert">
                                {{ session('message') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert-soft">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('login.post') }}" class="w-100 mt-4" method="POST" novalidate>
                            @csrf

                            <div class="mb-4">
                                <label for="email">Email Address</label>
                                <div class="input-icon-wrap">
                                    <span class="input-field-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M4 6h16a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2z"></path>
                                            <path d="m22 8-10 6L2 8"></path>
                                        </svg>
                                    </span>
                                    <input
                                        type="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        id="email"
                                        name="email"
                                        placeholder="Enter your email"
                                        value="{{ old('email') }}"
                                        required
                                        autocomplete="off">
                                </div>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="password">Password</label>
                                <div class="password-field-wrap">
                                    <input
                                        type="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        id="password"
                                        name="password"
                                        placeholder="Enter your password"
                                        required
                                        autocomplete="off">
                                    <button type="button" class="password-toggle" id="passwordToggle" aria-label="Show password" aria-controls="password" aria-pressed="false">
                                        <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                        <svg class="eye-off" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M17.94 17.94A10.94 10.94 0 0 1 12 19C5 19 1 12 1 12a21.76 21.76 0 0 1 5.06-6.94"></path>
                                            <path d="M9.9 4.24A10.93 10.93 0 0 1 12 4c7 0 11 8 11 8a21.8 21.8 0 0 1-3.17 4.66"></path>
                                            <path d="M14.12 14.12A3 3 0 1 1 9.88 9.88"></path>
                                            <path d="M1 1l22 22"></path>
                                        </svg>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mt-2">
                                <button type="submit" class="btn btn-login w-100">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="assets/vendors/js/vendors.min.js"></script>
    <script src="assets/js/common-init.min.js"></script>
    <script src="assets/js/theme-customizer-init.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.getElementById('password');
            const passwordToggle = document.getElementById('passwordToggle');

            if (!passwordInput || !passwordToggle) {
                return;
            }

            passwordToggle.addEventListener('click', function () {
                const isPassword = passwordInput.type === 'password';

                passwordInput.type = isPassword ? 'text' : 'password';
                passwordToggle.classList.toggle('is-visible', isPassword);
                passwordToggle.setAttribute('aria-pressed', String(isPassword));
                passwordToggle.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
            });
        });
    </script>
</body>

</html>
