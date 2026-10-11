@php
    $headerUser = auth()->user();
    $portalType = session('portal_type');

    if (!$headerUser && session('user_id')) {
        if ($portalType === 'teacher') {
            $headerUser = \Illuminate\Support\Facades\DB::table('staff')->where('id', session('user_id'))->first();
        } else {
            $headerUser = \Illuminate\Support\Facades\DB::table('students')->where('id', session('user_id'))->first();
        }
    }

    $headerTenantName = 'Default';
    if ($headerUser && isset($headerUser->tenant_id)) {
        $headerTenantName = \Illuminate\Support\Facades\DB::table('tenants')
            ->where('id', $headerUser->tenant_id)
            ->value('name') ?? 'Default';
    }

    $headerLogoutUrl = auth()->check()
        ? route('logout')
        : ($portalType === 'teacher'
            ? route('teacher.teachersacademic.logout')
            : route('student.studentsacademic.logout'));

    $firstName = $headerUser->first_name ?? ($portalType === 'teacher' ? 'Teacher' : 'Student');
    $lastName  = $headerUser->last_name  ?? '';
    $fullName  = trim($firstName . ' ' . $lastName);
    $email     = $headerUser->email ?? (session('username') ?? ($portalType === 'teacher' ? 'teacher' : 'student'));
    $initials  = strtoupper(substr($firstName, 0, 1) . substr($lastName ?: $firstName, $lastName ? 0 : 1, 1));
@endphp

<header class="nxl-header">
    <div class="header-wrapper">

        <div class="header-left d-flex align-items-center gap-4">
            <a href="javascript:void(0);" class="nxl-head-mobile-toggler" id="mobile-collapse">
                <div class="hamburger hamburger--arrowturn">
                    <div class="hamburger-box"><div class="hamburger-inner"></div></div>
                </div>
            </a>
            <div class="nxl-navigation-toggle">
                <a href="javascript:void(0);" id="menu-mini-button"><i class="feather-align-left"></i></a>
                <a href="javascript:void(0);" id="menu-expend-button" style="display:none;"><i class="feather-arrow-right"></i></a>
            </div>
            <div class="nxl-lavel-mega-menu-toggle d-flex d-lg-none">
                {{-- <a href="javascript:void(0);" id="nxl-lavel-mega-menu-open"><i class="feather-align-left"></i></a> --}}
            </div>
            {{-- <h4>{{ $headerTenantName }}</h4> --}}
        </div>

        <div class="header-right ms-auto">
            <div class="d-flex align-items-center">

                <div class="nxl-h-item d-none d-sm-flex">
                    {{-- <div class="full-screen-switcher">
                        <a href="javascript:void(0);" class="nxl-head-link me-0"
                           onclick="$('body').fullScreenHelper('toggle');">
                            <i class="feather-maximize maximize"></i>
                            <i class="feather-minimize minimize"></i>
                        </a>
                    </div> --}}
                </div>

                {{-- <div class="nxl-h-item dark-light-theme">
                    <a href="javascript:void(0);" class="nxl-head-link me-0 dark-button"><i class="feather-moon"></i></a>
                    <a href="javascript:void(0);" class="nxl-head-link me-0 light-button" style="display:none;"><i class="feather-sun"></i></a>
                </div> --}}

                {{-- ── Avatar + Dropdown ── --}}
                {{-- <div class="dropdown nxl-h-item">

                    <a href="javascript:void(0);" data-bs-toggle="dropdown" role="button"
                       data-bs-auto-close="outside">
                        <img src="{{ asset('assets/images/avatar/13.png') }}" alt=""
                             class="img-fluid user-avtar me-0" />
                    </a>

                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-user-dropdown"
                         id="hpMainDropdown">

                        <div class="dropdown-header">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/images/avatar/13.png') }}" alt=""
                                     class="img-fluid user-avtar" />
                                <div>
                                    <h6 class="text-dark mb-0">{{ $fullName }}</h6>
                                    <span class="fs-12 fw-medium text-muted">{{ $email }}</span>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('student.studentsacademic.profile') }}" class="dropdown-item">
                            <i class="feather-user"></i>
                            <span>Profile</span>
                        </a>

                        <div class="dropdown-divider"></div>

                        <a href="{{ $headerLogoutUrl }}" class="dropdown-item">
                            <i class="feather-log-out"></i>
                            <span>Logout</span>
                        </a>

                    </div>
                </div> --}}
                {{-- /dropdown --}}
            </div>
        </div>

    </div>
</header>