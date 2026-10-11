<nav class="nxl-navigation">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="index.html" class="b-brand">
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
                <li class="nxl-item nxl-hasmenu">
                    <a href="{{ route('teacher.teachersacademic.dashboard') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-airplay"></i></span>
                        <span class="nxl-mtext">DASHBOARD</span>
                    </a>
                </li>
                <li class="nxl-item nxl-hasmenu">
                    <a href="/teachersacademic/show" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-cast"></i></span>
                        <span class="nxl-mtext"> Time Table</span>
                    </a>
                </li>
                <li class="nxl-item nxl-hasmenu">
                    <a href="/teachersacademic/attendance" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-cast"></i></span>
                        <span class="nxl-mtext"> Attendance</span>
                    </a>
                </li>
                <li class="nxl-item nxl-hasmenu">
                    <a href="/teachersacademic/marks" class="nxl-link">
                        <span class="nxl-micon"><i class=""></i></span>
                        <span class="nxl-mtext"> Leave </span>
                    </a>
                </li>

                <li class="nxl-item nxl-hasmenu">
                    <a href="/teachersacademic/leave" class="nxl-link">
                        <span class="nxl-micon"><i class=""></i></span>
                        <span class="nxl-mtext"> Salary </span>
                    </a>
                </li>
                <li class="nxl-item nxl-hasmenu">
                    <a href="{{ route('teacher.teachersacademic.logout') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-cast"></i></span>
                        <span class="nxl-mtext">Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
