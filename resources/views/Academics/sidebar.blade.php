<div class="content-sidebar content-sidebar-md" data-scrollbar-target="#psScrollbarInit">
    <div class="content-sidebar-header bg-white sticky-top hstack justify-content-between">
        <h4 class="fw-bolder mb-0">Academics</h4>
    </div>
    <div class="content-sidebar-body">
        <ul class="nav flex-column nxl-content-sidebar-item">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('academic.dashboard') ? 'active' : '' }} "
                   href="{{ route('academic.dashboard') }}">
                    <i class="feather-airplay"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            @if (auth()->user()->hasPermissionTo('View Student'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}"
                       href="{{ route('admin.students.index') }}">
                        <i class="feather-user"></i>
                        <span>Student</span>
                    </a>
                </li>
            @endif

            {{-- @if (auth()->user()->hasPermissionTo('View Student Class Enrollments'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.student_class_enrollments.*') ? 'active' : '' }}"
                       href="{{ route('admin.student_class_enrollments.index') }}">
                        <i class="feather-grid"></i>
                        <span>Student Class Enrollments</span>
                    </a>
                </li>
            @endif --}}

            @if (auth()->user()->hasPermissionTo('View Branch Transfer'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.promotions.index') ? 'active' : '' }}"
                       href="{{ route('admin.promotions.index') }}">
                        <i class="feather-grid"></i>
                        <span>Batch Transfer</span>
                    </a>
                </li>
            @endif

            @if (auth()->user()->hasPermissionTo('view academics') ||
                    auth()->user()->hasPermissionTo('view time table') ||
                    auth()->user()->hasPermissionTo('view Room') ||
                    auth()->user()->hasPermissionTo('view Academic Year') ||
                    auth()->user()->hasPermissionTo('View Term') ||
                    auth()->user()->hasPermissionTo('View Semester') ||
                    auth()->user()->hasPermissionTo('View Branch') ||
                    auth()->user()->hasPermissionTo('View Semester Subject Mappings') ||
                    auth()->user()->hasPermissionTo('View Semester Branch Mappings') ||
                    auth()->user()->hasPermissionTo('View Section') ||
                    auth()->user()->hasPermissionTo('View Subject'))
                <li class="nav-item nxl-hasmenu" style="padding-top:12px;">
                    <a href="javascript:void(0);" class="nxl-link" style="padding-left:16px;">
                        <i class="feather-book-open me-2"></i>
                        <span class="nxl-mtext"> Academics</span>
                        <i class="feather-chevron-down ms-auto"></i>
                    </a>
                    <ul class="nxl-submenu" style="display:none;">
                        @if (auth()->user()->hasPermissionTo('view time table'))
                            <li class="nav-item pt-2">
                                <a class="nav-link {{ request()->routeIs('admin.timetables.index.*') ? 'active' : '' }}"
                                   href="{{ route('admin.timetables.index') }}">
                                    <span>Time Tables</span>
                                </a>
                            </li>
                        @endif
                        @if (auth()->user()->hasPermissionTo('View Courses'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.courses.index') }}">
                                    <span>Courses</span>
                                </a>
                            </li>
                        @endif
                        @if (auth()->user()->hasPermissionTo('view Room'))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.rooms.index.*') ? 'active' : '' }}"
                                   href="{{ route('admin.rooms.index') }}">
                                    <span>Rooms</span>
                                </a>
                            </li>
                        @endif
                        @if (auth()->user()->hasPermissionTo('view Academic Year'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.academic_years.index') }}">
                                    <span>Academic Year</span>
                                </a>
                            </li>
                        @endif
                        {{-- @if (auth()->user()->hasPermissionTo('View Term'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.terms.index') }}">
                                    <span>Stream</span>
                                </a>
                            </li>
                        @endif --}}
                        @if (auth()->user()->hasPermissionTo('View Semester'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.classes.index') }}">
                                    <span>Semesters/Years</span>
                                </a>
                            </li>
                        @endif
                        @if (auth()->user()->hasPermissionTo('View Branch'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.streams.index') }}">
                                    <span>Branch</span>
                                </a>
                            </li>
                        @endif
                        {{-- @if (auth()->user()->hasPermissionTo('View Semester Subject Mappings'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.class_subject_mappings.index') }}">
                                    <span>Semester Subject Mappings</span>
                                </a>
                            </li>
                        @endif
                        @if (auth()->user()->hasPermissionTo('View Semester Branch Mappings'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.class_stream_mappings.index') }}">
                                    <span>Semester Branch Mappings</span>
                                </a>
                            </li>
                        @endif --}}
                        {{-- @if (auth()->user()->hasPermissionTo('View Section'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.sections.index') }}">
                                    <span>Section</span>
                                </a>
                            </li>
                        @endif --}}
                        @if (auth()->user()->hasPermissionTo('View Subject'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.subjects.index') }}">
                                    <span>Subject</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if (auth()->user()->hasPermissionTo('View Attendance') ||
                    auth()->user()->hasPermissionTo('View Student Attendance') ||
                    auth()->user()->hasPermissionTo('View Attendance Reports') ||
                    auth()->user()->hasPermissionTo('View Attendance Periods'))
                <li class="nav-item nxl-hasmenu" style="padding-top:23px;">
                    <a href="javascript:void(0);" class="nxl-link" style="padding-left:17px;">
                        <i class="feather-clock me-2"></i>
                        <span class="nxl-mtext">Attendance</span>
                        <i class="feather-chevron-down ms-auto"></i>
                    </a>
                    <ul class="nxl-submenu" style="display:none;">
                        @if (auth()->user()->hasPermissionTo('View Student Attendance'))
                            <li class="nav-item" style="padding-top:21px;">
                                <a class="nxl-link {{ request()->routeIs('admin.student_attendance.index') ? 'active' : '' }}"
                                   href="{{ route('admin.student_attendance.index') }}">
                                    <span>Student Attendance</span>
                                </a>
                            </li>
                        @endif
                        @if (auth()->user()->hasPermissionTo('View Attendance'))
                        <li class="nav-item" style="padding-top:21px;">
                            <a class="nxl-link {{ request()->routeIs('admin.student_attendance.date') ? 'active' : '' }}"
                               href="{{ route('admin.student_attendance.date') }}">
                                <span>Attendance Reports</span>
                            </a>
                        </li>
                        @endif
                        @if (auth()->user()->hasPermissionTo('View Attendance Periods'))
                            <li class="nav-item" style="padding-top:18px;">
                                <a class="nxl-link" href="{{ route('admin.attendance_periods.index') }}">
                                    <span>Attendance Periods</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if (auth()->user()->hasPermissionTo('view Staff') ||
                    auth()->user()->hasPermissionTo('view Staff') ||
                    auth()->user()->hasPermissionTo('View Semester Teachers') ||
                    auth()->user()->hasPermissionTo('View Department') ||
                    auth()->user()->hasPermissionTo('View Designation'))
                <li class="nav-item nxl-hasmenu" style="padding-top:23px;">
                    <a href="javascript:void(0);" class="nxl-link" style="padding-left:17px;">
                        <i class="feather-users me-2"></i>
                        <span class="nxl-mtext">Personal Management</span>
                        <i class="feather-chevron-down ms-auto"></i>
                    </a>
                    <ul class="nxl-submenu" style="display:none;">
                        @if (auth()->user()->hasPermissionTo('view Staff'))
                            <li class="nav-item" style="padding-top:18px;">
                                <a class="nxl-link {{ request()->routeIs('admin.staff.index') ? 'active' : '' }}"
                                   href="{{ route('admin.staff.index') }}">
                                    <span>Staff</span>
                                </a>
                            </li>
                        @endif
                        @if (auth()->user()->hasPermissionTo('View Semester Teachers'))
                            <li class="nav-item" style="padding-top:14px;">
                                <a class="nxl-link" href="{{ route('admin.class_teachers.index') }}">
                                    <span>Semester Teachers</span>
                                </a>
                            </li>
                        @endif
                        @if (auth()->user()->hasPermissionTo('View Department'))
                            <li class="nav-item" style="padding-top:18px;">
                                <a class="nxl-link" href="{{ route('admin.departments.index') }}">
                                    <span>Department</span>
                                </a>
                            </li>
                        @endif
                        @if (auth()->user()->hasPermissionTo('View Designation'))
                            <li class="nav-item" style="padding-top:18px;">
                                <a class="nxl-link" href="{{ route('admin.designations.index') }}">
                                    <span>Designation</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if (auth()->user()->hasPermissionTo('View Exam') ||
                    auth()->user()->hasPermissionTo('View Exam list') ||
                    auth()->user()->hasPermissionTo('View Exam Types') ||
                    auth()->user()->hasPermissionTo('View Exam Marks') ||
                    auth()->user()->hasPermissionTo('View Grading System') ||
                    auth()->user()->hasPermissionTo('View Grade Levels'))
                <li class="nav-item nxl-hasmenu" style="padding-top:22px;">
                    <a href="javascript:void(0);" class="nxl-link" style="padding-left:17px;">
                        <i class="feather-book-open me-2"></i>
                        <span class="nxl-mtext">Exam</span>
                        <i class="feather-chevron-down ms-auto"></i>
                    </a>
                    <ul class="nxl-submenu" style="display:none;">
                        @if (auth()->user()->hasPermissionTo('View Exam list'))
                        @if (auth()->user()->hasPermissionTo('View Exam list'))
                            <li class="nav-item" style="padding-top:21px;">
                                <a class="nxl-link {{ request()->routeIs('admin.exams.index') ? 'active' : '' }}"
                                   href="{{ route('admin.exams.index') }}">
                                    <span>Exams</span>
                                </a>
                            </li>
                        @endif
                        @if (auth()->user()->hasPermissionTo('View Exam Types'))
                            <li class="nav-item" style="padding-top:14px;">
                                <a class="nxl-link" href="{{ route('admin.exam_types.index') }}">
                                    <span>Exam Types</span>
                                </a>
                            </li>
                        @endif
                        @if (auth()->user()->hasPermissionTo('View Exam Marks'))
                            <li class="nav-item" style="padding-top:14px;">
                                <a class="nxl-link" href="{{ route('admin.exam_marks.index') }}">
                                    <span>Exam Marks</span>
                                </a>
                            </li>
                        @endif
                        @if (auth()->user()->hasPermissionTo('View Question Papers'))
                        <li class="nav-item" style="padding-top:14px;">
                            <a class="nxl-link" href="{{ route('admin.question_banks.index') }}">
                                <span>Question Papers</span>
                            </a>
                        </li>
                        @endif
                        @if (auth()->user()->hasPermissionTo('View Exam Center'))
                        <li class="nav-item" style="padding-top:14px;">
                            <a class="nxl-link" href="{{ route('admin.exam_center.index') }}">
                                <span>Exam Centers</span>
                            </a>
                        </li>
                        @endif
                        <!-- <li class="nav-item" style="padding-top:14px;">
                            <a class="nxl-link" href="{{ route('admin.center_devices.index') }}">
                                <span>Exam Center Devices</span>
                            </a>
                        </li> -->
                        @if (auth()->user()->hasPermissionTo('View Grading System'))
                            <li class="nav-item" style="padding-top:14px;">
                                <a class="nxl-link" href="{{ route('admin.grading_systems.index') }}">
                                    <span>Grading System</span>
                                </a>
                            </li>
                        @endif
                        <!-- @if (auth()->user()->hasPermissionTo('View Grade Levels'))
                            <li class="nav-item" style="padding-top:14px;">
                                <a class="nxl-link" href="{{ route('admin.grade_levels.index') }}">
                                    <span>Grade Levels</span>
                                </a>
                            </li>
                        @endif -->
                    </ul>
                </li>
            @endif

          @if (auth()->user()->hasPermissionTo('View Practicals') ||
     auth()->user()->hasPermissionTo('View Practical Groups') ||
     auth()->user()->hasPermissionTo('View Group Master'))
    <li class="nav-item nxl-hasmenu" style="padding-top:23px;">
        <a href="javascript:void(0);" class="nxl-link" style="padding-left:17px;">
            <i class="feather-tool me-2"></i>
            <span class="nxl-mtext">Practicals</span>
            <i class="feather-chevron-down ms-auto"></i>
        </a>
        <ul class="nxl-submenu" style="display:none;">
            @if (auth()->user()->hasPermissionTo('View Practical Groups'))
                <li class="nav-item" style="padding-top:18px;">
                    <a class="nxl-link {{ request()->routeIs('practical_groups.*') ? 'active' : '' }}"
                       href="{{ route('practical_groups.index') }}">
                        <span>Practical Groups</span>
                    </a>
                </li>
            @endif
            @if (auth()->user()->hasPermissionTo('View Group Master'))
                <li class="nav-item" style="padding-top:14px;">
                    <a class="nxl-link {{ request()->routeIs('group_master.*') ? 'active' : '' }}"
                       href="{{ route('group_master.index') }}">
                        <span>Group Master</span>
                    </a>
                </li>
            @endif
        </ul>
    </li>
@endif
            {{-- Holidays --}}
            @if (auth()->user()->hasPermissionTo('View Holidays'))
            <li class="nav-item" style="padding-top:15px;">
                <a class="nav-link {{ request()->routeIs('admin.holidays.index') ? 'active' : '' }}"
                   href="{{ route('admin.holidays.index') }}">
                    <i class="feather-calendar"></i>
                    <span>Holidays</span>
                </a>
            </li>
            @endif

            {{-- Notice Board --}}
            @if (auth()->user()->hasPermissionTo('View Notice Board'))
                   
            <li class="nav-item" style="padding-top:15px;">
                <a class="nxl-link {{ request()->routeIs('admin.notice_boards.*') ? 'active' : '' }}"
                   href="{{ route('admin.notice_boards.index') }}"
                   style="padding-left:17px;">
                    <i class="feather-clipboard me-2"></i>
                    <span class="nxl-mtext">Notice Board</span>
                </a>
            </li>
            @endif
        </ul>
    </div>
</div>