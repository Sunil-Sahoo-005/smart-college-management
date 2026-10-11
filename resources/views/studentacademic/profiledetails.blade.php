@extends('studentadmin_layout')
@section('content')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    @php
        $studentName = trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''));
        $studentName = $studentName !== '' ? $studentName : 'Student';
    @endphp

    <style>
        * {
            box-sizing: border-box;
        }

        body,
        .nxl-content {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background:
                radial-gradient(circle at top right, rgba(190, 217, 193, 0.46), transparent 28%),
                linear-gradient(180deg, #f5efe3 0%, #eef3ea 100%);
            color: #18352d;
        }

        .profile-shell {
            padding: 26px;
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        .profile-hero {
            position: relative;
            overflow: hidden;
            border-radius: 28px;
            padding: 40px 32px;
            background:
                radial-gradient(circle at top left, rgba(255, 255, 255, 0.14), transparent 24%),
                linear-gradient(135deg, #12342b 0%, #1d5548 58%, #c68b3c 160%);
            color: #f7f3ea;
            box-shadow: 0 20px 55px rgba(19, 52, 43, 0.18);
        }

        .profile-hero::after {
            content: '';
            position: absolute;
            inset: auto -70px -90px auto;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.07);
        }

        .profile-hero-content {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            font-weight: 800;
            border: 3px solid rgba(255, 255, 255, 0.2);
            flex-shrink: 0;
        }

        .profile-hero-info h1 {
            font-size: 28px;
            font-weight: 800;
            margin: 0 0 4px;
            letter-spacing: -0.02em;
        }

        .profile-hero-info .subtitle {
            font-size: 14px;
            opacity: 0.8;
            margin: 0;
        }

        .profile-badges {
            margin-top: 16px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .profile-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.1);
            font-size: 12px;
            font-weight: 600;
        }

        .profile-body {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 20px;
        }

        .profile-card {
            background: rgba(255, 255, 255, 0.88);
            border: 1px solid rgba(24, 53, 45, 0.08);
            box-shadow: 0 16px 45px rgba(28, 49, 43, 0.08);
            backdrop-filter: blur(14px);
            border-radius: 26px;
            padding: 24px;
        }

        .profile-card-title {
            font-size: 16px;
            font-weight: 700;
            color: #16342d;
            margin: 0 0 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .profile-card-title i {
            color: #1d5548;
        }

        .profile-field {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 12px 0;
            border-bottom: 1px solid rgba(24, 53, 45, 0.06);
        }

        .profile-field:last-child {
            border-bottom: none;
        }

        .profile-field-label {
            font-size: 13px;
            color: #667871;
            font-weight: 500;
        }

        .profile-field-value {
            font-size: 14px;
            color: #18352d;
            font-weight: 600;
            text-align: right;
            max-width: 60%;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .status-badge.active {
            background: rgba(72, 199, 142, 0.15);
            color: #2d8a5a;
        }

        .status-badge.inactive {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }

        @media (max-width: 768px) {
            .profile-shell {
                padding: 16px;
            }

            .profile-hero {
                padding: 28px 20px;
            }

            .profile-hero-content {
                flex-direction: column;
                text-align: center;
            }

            .profile-badges {
                justify-content: center;
            }

            .profile-body {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="nxl-content">
        <div class="profile-shell">
            {{-- Hero Section --}}
            <section class="profile-hero">
                <div class="profile-hero-content">
                    <div class="profile-avatar">
                        {{ strtoupper(substr($student->first_name ?? 'S', 0, 1) . substr($student->last_name ?? '', 0, 1)) }}
                    </div>
                    <div class="profile-hero-info">
                        <h1>{{ $studentName }}</h1>
                        <p class="subtitle">Student Profile</p>
                        <div class="profile-badges">
                            <span class="profile-badge">
                                <i class="feather-hash"></i>
                                {{ $student->admission_number ?? 'N/A' }}
                            </span>
                            <span class="profile-badge">
                                <i class="feather-book"></i>
                                {{ $student->stream_name ?? 'Not assigned' }}
                            </span>
                            <span class="profile-badge">
                                <i class="feather-users"></i>
                                {{ $student->class_name ?? 'Not assigned' }}
                                @if(isset($student->section_name))
                                    - {{ $student->section_name }}
                                @endif
                            </span>
                            <span class="profile-badge">
                                <i class="feather-calendar"></i>
                                {{ $student->academic_name ?? 'Current' }}
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            @if(session('pp_success'))
                <div style="padding: 14px 18px; border-radius: 16px; background: rgba(72, 199, 142, 0.15); color: #2d8a5a; font-weight: 600; font-size: 14px;">
                    <i class="feather-check-circle" style="margin-right: 8px;"></i>
                    {{ session('pp_success') }}
                </div>
            @endif

            @if(session('pp_error'))
                <div style="padding: 14px 18px; border-radius: 16px; background: rgba(239, 68, 68, 0.1); color: #dc2626; font-weight: 600; font-size: 14px;">
                    <i class="feather-alert-circle" style="margin-right: 8px;"></i>
                    {{ session('pp_error') }}
                </div>
            @endif

            {{-- Profile Details --}}
            <div class="profile-body">
                {{-- Personal Information --}}
                <div class="profile-card">
                    <h3 class="profile-card-title">
                        <i class="feather-user"></i>
                        Personal Information
                    </h3>
                    <div class="profile-field">
                        <span class="profile-field-label">First Name</span>
                        <span class="profile-field-value">{{ $student->first_name ?? 'N/A' }}</span>
                    </div>
                    <div class="profile-field">
                        <span class="profile-field-label">Last Name</span>
                        <span class="profile-field-value">{{ $student->last_name ?? 'N/A' }}</span>
                    </div>
                    <div class="profile-field">
                        <span class="profile-field-label">Email</span>
                        <span class="profile-field-value">{{ $student->email ?? 'N/A' }}</span>
                    </div>
                    <div class="profile-field">
                        <span class="profile-field-label">Phone</span>
                        <span class="profile-field-value">{{ $student->phone ?? 'N/A' }}</span>
                    </div>
                    <div class="profile-field">
                        <span class="profile-field-label">Date of Birth</span>
                        <span class="profile-field-value">
                            {{ $student->dob ? \Carbon\Carbon::parse($student->dob)->format('d M Y') : 'N/A' }}
                        </span>
                    </div>
                    <div class="profile-field">
                        <span class="profile-field-label">Gender</span>
                        <span class="profile-field-value">{{ ucfirst($student->gender ?? 'N/A') }}</span>
                    </div>
                </div>

                {{-- Academic Information --}}
                <div class="profile-card">
                    <h3 class="profile-card-title">
                        <i class="feather-graduation-cap"></i>
                        Academic Information
                    </h3>
                    <div class="profile-field">
                        <span class="profile-field-label">Admission Number</span>
                        <span class="profile-field-value">{{ $student->admission_number ?? 'N/A' }}</span>
                    </div>
                    <div class="profile-field">
                        <span class="profile-field-label">Stream</span>
                        <span class="profile-field-value">{{ $student->stream_name ?? 'N/A' }}</span>
                    </div>
                    <div class="profile-field">
                        <span class="profile-field-label">Class</span>
                        <span class="profile-field-value">{{ $student->class_name ?? 'N/A' }}</span>
                    </div>
                    <div class="profile-field">
                        <span class="profile-field-label">Section</span>
                        <span class="profile-field-value">{{ $student->section_name ?? 'N/A' }}</span>
                    </div>
                    <div class="profile-field">
                        <span class="profile-field-label">Academic Year</span>
                        <span class="profile-field-value">{{ $student->academic_name ?? 'N/A' }}</span>
                    </div>
                    <div class="profile-field">
                        <span class="profile-field-label">Status</span>
                        <span class="profile-field-value">
                            <span class="status-badge {{ $student->enrollment_status === 'active' ? 'active' : 'inactive' }}">
                                {{ ucfirst($student->enrollment_status ?? 'N/A') }}
                            </span>
                        </span>
                    </div>
                </div>

                {{-- Additional Information --}}
                <div class="profile-card">
                    <h3 class="profile-card-title">
                        <i class="feather-info"></i>
                        Additional Information
                    </h3>
                    <div class="profile-field">
                        <span class="profile-field-label">Username</span>
                        <span class="profile-field-value">{{ $student->username ?? 'N/A' }}</span>
                    </div>
                    <div class="profile-field">
                        <span class="profile-field-label">Blood Group</span>
                        <span class="profile-field-value">{{ $student->blood_group ?? 'N/A' }}</span>
                    </div>
                    <div class="profile-field">
                        <span class="profile-field-label">Nationality</span>
                        <span class="profile-field-value">{{ $student->nationality ?? 'N/A' }}</span>
                    </div>
                    <div class="profile-field">
                        <span class="profile-field-label">Address</span>
                        <span class="profile-field-value">{{ $student->address ?? 'N/A' }}</span>
                    </div>
                    <div class="profile-field">
                        <span class="profile-field-label">City</span>
                        <span class="profile-field-value">{{ $student->city ?? 'N/A' }}</span>
                    </div>
                    <div class="profile-field">
                        <span class="profile-field-label">State</span>
                        <span class="profile-field-value">{{ $student->state ?? 'N/A' }}</span>
                    </div>
                </div>

                {{-- Change Password --}}
                <div class="profile-card">
                    <h3 class="profile-card-title">
                        <i class="feather-lock"></i>
                        Change Password
                    </h3>
                    <form action="{{ route('change.password') }}" method="POST">
                        @csrf
                        <div class="profile-field" style="flex-direction: column; align-items: stretch; gap: 4px;">
                            <span class="profile-field-label">Current Password</span>
                            <input type="password" name="current_password" class="form-control" 
                                   style="width: 100%; padding: 10px 14px; border: 1px solid rgba(24, 53, 45, 0.15); border-radius: 12px; font-size: 14px; font-family: inherit; background: #fff;" 
                                   placeholder="Enter current password" required>
                        </div>
                        <div class="profile-field" style="flex-direction: column; align-items: stretch; gap: 4px;">
                            <span class="profile-field-label">New Password</span>
                            <input type="password" name="new_password" class="form-control" 
                                   style="width: 100%; padding: 10px 14px; border: 1px solid rgba(24, 53, 45, 0.15); border-radius: 12px; font-size: 14px; font-family: inherit; background: #fff;" 
                                   placeholder="Enter new password (min 8 characters)" required>
                        </div>
                        <div class="profile-field" style="flex-direction: column; align-items: stretch; gap: 4px;">
                            <span class="profile-field-label">Confirm New Password</span>
                            <input type="password" name="new_password_confirmation" class="form-control" 
                                   style="width: 100%; padding: 10px 14px; border: 1px solid rgba(24, 53, 45, 0.15); border-radius: 12px; font-size: 14px; font-family: inherit; background: #fff;" 
                                   placeholder="Confirm new password" required>
                        </div>
                        <button type="submit" 
                                style="width: 100%; padding: 12px 20px; border: none; border-radius: 14px; background: linear-gradient(135deg, #12342b 0%, #2f6958 100%); color: #fff; font-size: 14px; font-weight: 700; cursor: pointer; margin-top: 8px; transition: transform 0.16s ease, box-shadow 0.16s ease;"
                                onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 8px 20px rgba(18, 52, 43, 0.2)'"
                                onmouseout="this.style.transform='';this.style.boxShadow=''">
                            <i class="feather-key" style="margin-right: 6px;"></i>
                            Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection