<nav class="nxl-navigation">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ auth()->user()->role === 'super_admin' ? route('super_admin.dashboard') : route('admin.dashboard') }}"
                class="b-brand">
                <!-- ========   change your logo hear   ============ -->
                {{-- <img src="{{ asset('assets/images/logo-full.jpeg') }}" alt="Logo" class="logo logo-lg"> --}}
                <img src="{{ asset('assets\images\logo-1.webp') }}" alt="Logo" class="logo logo-lg">
                {{-- <img src="{{ asset('assets/images/logo-abbr.png') }}" alt="Logo" class="logo logo-sm"> --}}
                <img src="{{ asset('assets\images\logo-abb.jpg') }}" alt="Logo" class="logo logo-sm">

            </a>
        </div>
        <div class="navbar-content">
            <ul class="nxl-navbar">
                <li class="nxl-item nxl-caption">
                    <label>Navigation</label>
                </li>
                @if (auth()->user()->hasPermissionTo('View Dashboard'))
                    <li class="nxl-item nxl-hasmenu">
                        <a href="{{ auth()->user()->role === 'super_admin' ? route('super_admin.dashboard') : route('admin.dashboard') }}"
                            class="nxl-link">
                            <span class="nxl-micon"><i class="feather-airplay"></i></span>
                            <span class="nxl-mtext">DASHBOARD</span>
                        </a>

                    </li>
                @endif
                @if (auth()->user()->hasPermissionTo('view academic'))
                    <li class="nxl-item nxl-hasmenu">
                        <a href="{{ route('academic.dashboard') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-book-open"></i></span>
                            <span class="nxl-mtext">ACADEMICS</span>
                        </a>
                    </li>
                @endif
                @if (auth()->user()->hasPermissionTo('view account'))
                    <li class="nxl-item nxl-hasmenu">
                        <a href="{{ route('accountant.dashboard') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-dollar-sign"></i></span>
                            <span class="nxl-mtext">ACCOUNTS</span>
                        </a>
                    </li>
                @endif

                @if (auth()->user()->hasPermissionTo('view library'))
                    <li class="nxl-item nxl-hasmenu">
                        <a href="{{ route('library.dashboard') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-book"></i></span>
                            <span class="nxl-mtext">LIBRARY</span>
                        </a>
                    </li>
                @endif

                @if (auth()->user()->hasPermissionTo('View Transportation'))
                    <li class="nxl-item nxl-hasmenu">
                        <a href="{{ route('travels.dashboard') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-truck"></i></span>
                            <span class="nxl-mtext">TRANSPORTATION</span>
                        </a>
                    </li>
                @endif

                @if (auth()->user()->hasPermissionTo('view hostell'))
                    <li class="nxl-item nxl-hasmenu">
                        <a href="{{ route('hostel.dashboard') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-home"></i></span>
                            <span class="nxl-mtext">HOSTEL</span>
                        </a>
                    </li>
                @endif

                {{-- <li class="nxl-item nxl-hasmenu">
                    <a href="{{ route('communications.dashboard') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-message-circle"></i></span>
                        <span class="nxl-mtext">COMMUNICATION</span>
                    </a>

                </li> --}}
                @if (auth()->user()->hasPermissionTo('view teant'))
                    <li class="nxl-item nxl-hasmenu">
                        <a href="{{ route('admin.tenants.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-cast"></i></span>
                            <span class="nxl-mtext">Unit</span><span class="nxl-arrow">
                                {{-- <i class="feather-chevron-right"></i> --}}
                            </span>
                        </a>
                        {{-- <ul class="nxl-submenu">
                        <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.tenants.index') }}">Teants
                                Lists</a></li>
                        <li class="nxl-item"><a class="nxl-link" href="{{ route('admin.tenants.create') }}">Create
                                Teants</a></li>

                    </ul> --}}
                    </li>
                @endif
                @if (auth()->user()->hasPermissionTo('view user'))
                    <li class="nxl-item nxl-hasmenu">
                        <a href="{{ route('users.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-users"></i></span>
                            <span class="nxl-mtext">USERS</span><span class="nxl-arrow">
                            </span>
                        </a>
                    </li>
                @endif
                @if(auth()->user()->hasPermissionTo('view role'))
                 <li class="nxl-item nxl-hasmenu">
                    <a href="{{ route('admin.roles.index') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-shield"></i></span>
                        <span class="nxl-mtext">ROLES</span><span class="nxl-arrow">
                        </span>
                    </a>
                </li>
                @endif
                @if(auth()->user()->hasPermissionTo('view setting'))
                <li class="nxl-item nxl-hasmenu">
                    <a href="{{ route('settings.index') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-settings"></i></span>
                        <span class="nxl-mtext">SETTINGS</span><span class="nxl-arrow">
                        </span>
                    </a>
                </li>
                @endif
                @if (auth()->user()->hasPermissionTo('view permission'))
                    </span>
                    </a>
                    </li>
                    <li class="nxl-item nxl-hasmenu">
                        <a href="{{ route('admin.permissions.index') }}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-lock"></i></span>
                            <span class="nxl-mtext">PERMISSIONS</span><span class="nxl-arrow">
                            </span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>
