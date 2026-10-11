<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Portal | Login</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets\images\logo-abb.jpg') }}" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/theme.min.css') }}">

    <style>
        :root {
            --primary-emerald: #12342b;
            --accent-gold: #c68b3c;
            --glass-bg: rgba(255, 255, 255, 0.85);
            --glass-border: rgba(255, 255, 255, 0.4);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: 
                radial-gradient(circle at top right, rgba(190, 217, 193, 0.4), transparent 30%),
                radial-gradient(circle at bottom left, rgba(198, 139, 60, 0.15), transparent 30%),
                linear-gradient(135deg, #f5efe3 0%, #eef3ea 100%);
            overflow: hidden;
        }

        .login-container {
            width: 100%;
            max-width: 1100px;
            height: 640px;
            display: flex;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border-radius: 40px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 40px 100px rgba(18, 52, 43, 0.12);
            overflow: hidden;
            position: relative;
        }

        /* Left Side - Form */
        .login-form-side {
            flex: 1;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            z-index: 2;
        }

        .brand-logo {
            width: 60px;
            margin-bottom: 40px;
        }

        .login-header h1 {
            font-size: 32px;
            font-weight: 800;
            color: var(--primary-emerald);
            margin-bottom: 12px;
            letter-spacing: -0.03em;
        }

        .login-header p {
            color: #6b7f77;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 35px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            font-size: 13px;
            font-weight: 700;
            color: #18352d;
            margin-bottom: 8px;
            display: block;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .form-control {
            height: 56px;
            background: rgba(24, 53, 45, 0.03);
            border: 1px solid rgba(24, 53, 45, 0.08);
            border-radius: 16px;
            padding: 0 20px;
            font-size: 15px;
            font-weight: 500;
            color: #18352d;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            background: #fff;
            border-color: var(--primary-emerald);
            box-shadow: 0 0 0 4px rgba(18, 52, 43, 0.05);
            outline: none;
        }

        .btn-login {
            height: 56px;
            background: var(--primary-emerald);
            color: #fff;
            border: none;
            border-radius: 16px;
            font-size: 16px;
            font-weight: 700;
            width: 100%;
            margin-top: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(18, 52, 43, 0.2);
        }

        .btn-login:hover {
            background: #1d5548;
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(18, 52, 43, 0.25);
        }

        .auth-footer {
            margin-top: 40px;
            text-align: center;
            font-size: 14px;
            color: #6b7f77;
        }

        /* Right Side - Visual */
        .login-visual-side {
            flex: 1.1;
            background: linear-gradient(135deg, #12342b 0%, #1d5548 60%, #c68b3c 140%);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            overflow: hidden;
        }

        .login-visual-side::before {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            top: -100px;
            right: -100px;
        }

        .visual-content {
            position: relative;
            z-index: 2;
            color: #fff;
            text-align: center;
        }

        .visual-title {
            font-size: 36px;
            font-weight: 800;
            margin-bottom: 20px;
            letter-spacing: -0.04em;
            line-height: 1.1;
            color: #fff
        }

        .visual-copy {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.6;
            max-width: 340px;
            margin: 0 auto;
        }

        .floating-badges {
            position: absolute;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .badge-item {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 12px 20px;
            border-radius: 20px;
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        @media (max-width: 992px) {
            .login-container {
                max-width: 500px;
                height: auto;
                flex-direction: column;
            }
            .login-visual-side {
                display: none;
            }
            .login-form-side {
                padding: 40px;
            }
        }

        @media (max-width: 576px) {
            body { padding: 20px; }
            .login-form-side { padding: 30px; }
            .login-header h1 { font-size: 26px; }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <!-- Form Side -->
        <div class="login-form-side">
            {{-- <div class="brand-logo">
                <img src="{{ asset('assets/images/logo-abbr.png') }}" alt="Logo" class="img-fluid">
            </div> --}}

            @include('elements.flash')

            <div class="login-header">
                <h1>Welcome Back</h1>
                <p>Login to your student portal to access your grades, timetable, and academic records.</p>
            </div>

            <form action="{{ route('student.studentsacademic.logins') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Admission Number</label>
                    <input type="text" class="form-control" name="username" placeholder="Enter your admission number" value="{{ old('username') }}" required autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" placeholder="••••••••" required autocomplete="new-password">
                </div>

                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                        <label class="form-check-label" for="rememberMe" style="font-size: 14px; color: #6b7f77;">
                            Remember Me
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn-login">Sign In</button>
            </form>

            <div class="auth-footer">
                <p>&copy; {{ date('Y') }} Student Portal. All rights reserved.</p>
            </div>
        </div>

        <!-- Visual Side -->
        <div class="login-visual-side">
            <div class="floating-badges">
                <div class="badge-item" style="top: 20%; left: 10%; animation-delay: 0s;">
                    <i class="feather-check-circle"></i> Attendance Tracking
                </div>
                <div class="badge-item" style="top: 15%; right: 15%; animation-delay: 1s;">
                    <i class="feather-book"></i> Library Portal
                </div>
                <div class="badge-item" style="bottom: 25%; left: 15%; animation-delay: 2s;">
                    <i class="feather-credit-card"></i> Fee Management
                </div>
                <div class="badge-item" style="bottom: 20%; right: 10%; animation-delay: 1.5s;">
                    <i class="feather-calendar"></i> Smart Timetable
                </div>
            </div>

            <div class="visual-content">
                <h2 class="visual-title">Academic Excellence <br> Starts Here.</h2>
                <p class="visual-copy">Manage your student life with our modern, intuitive, and high-performance portal.</p>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/vendors/js/vendors.min.js') }}"></script>
    <script src="{{ asset('assets/js/common-init.min.js') }}"></script>
</body>

</html>
