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
                    <a href="{{ route('student.studentsacademic.dashboard') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-airplay"></i></span>
                        <span class="nxl-mtext">DASHBOARD</span>
                    </a>

                </li>


                <li class="nxl-item nxl-hasmenu">
                    <a href="{{ route('student.studentsacademic.timetable') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-calendar"></i></span>
                        <span class="nxl-mtext">Time Table</span>
                    </a>

                </li>
                <li class="nxl-item nxl-hasmenu">
                    <a href="{{ route('student.studentsacademic.careerdetails') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-briefcase"></i></span>
                        <span class="nxl-mtext">Career Details</span>
                    </a>

                </li>

                <li class="nxl-item nxl-hasmenu">
                    <a href="{{ route('student.studentsacademic.examresults') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-layers"></i></span>
                        <span class="nxl-mtext">Exam Details</span>
                    </a>

                </li>

                {{-- <li class="nxl-item nxl-hasmenu">
                    <a href="" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-edit-3"></i></span>
                        <span class="nxl-mtext">Internal Theory Results</span>
                    </a>

                </li> --}}


                <li class="nxl-item nxl-hasmenu">
                    <a href="{{ route('student.studentsacademic.attendancedetails') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-check-square"></i></span>
                        <span class="nxl-mtext">Attendance Details</span>
                    </a>

                </li>



                <li class="nxl-item nxl-hasmenu">
                    <a href="{{ route('student.studentsacademic.hostel') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-truck"></i></span>
                        <span class="nxl-mtext">Transport Details</span>
                    </a>

                </li>


                <li class="nxl-item nxl-hasmenu">
                    <a href="{{ route('student.studentsacademic.accountdetails') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-dollar-sign"></i></span>
                        <span class="nxl-mtext">Account Details</span>
                    </a>

                </li>

                <li class="nxl-item nxl-hasmenu">
                    <a href="{{ route('student.studentsacademic.librarydetails') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-book"></i></span>
                        <span class="nxl-mtext">Library Details</span>
                    </a>

                </li>


                <li class="nxl-item nxl-hasmenu">
                    <a href="{{ route('student.studentsacademic.profile') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-user"></i></span>
                        <span class="nxl-mtext">My Profile</span>
                    </a>
                </li>

                <li class="nxl-item nxl-hasmenu">
                    <a href="{{ route('student.studentsacademic.logout') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-log-out"></i></span>
                        <span class="nxl-mtext">Logout</span>
                    </a>

                </li>

            </ul>
        </div>
    </div>
</nav>
