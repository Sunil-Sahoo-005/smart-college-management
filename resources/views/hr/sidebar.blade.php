<div class="content-sidebar content-sidebar-md" data-scrollbar-target="#psScrollbarInit">
    <div class="content-sidebar-header bg-white sticky-top hstack justify-content-between">
        <h4 class="fw-bolder mb-0">HR</h4>

    </div>
    <div class="content-sidebar-body">
        @php
            $tenantKey = request()->route('tenantKey');
        @endphp
        <ul class="nav flex-column nxl-content-sidebar-item">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('hr.tenant.dashboard') ? 'active' : '' }} "
                    href="{{ route('hr.tenant.dashboard', $tenantKey) }}">
                    <i class="feather-airplay"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            @if ($tenantKey)
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('hr.staff.*') ? 'active' : '' }}"
                        href="{{ route('hr.staff.index', $tenantKey) }}">
                        <i class="feather-users"></i>
                        <span>Staff</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('hr.leave-requests.*') ? 'active' : '' }}"
                        href="{{ route('hr.leave-requests.index', $tenantKey) }}">
                        <i class="feather-calendar"></i>
                        <span>Leave Requests</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('hr.attendance-config.*') ? 'active' : '' }}"
                        href="{{ route('hr.attendance-config.edit', $tenantKey) }}">
                        <i class="feather-clock"></i>
                        <span>Attendance Config</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('hr.attendance.*') ? 'active' : '' }}"
                        href="{{ route('hr.attendance.index', $tenantKey) }}">
                        <i class="feather-calendar"></i>
                        <span>Attendance</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('hr.salary-structures.*') ? 'active' : '' }}"
                        href="{{ route('hr.salary-structures.index', $tenantKey) }}">
                        <i class="feather-credit-card"></i>
                        <span>Salary Structures</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('hr.staff-salary-assignments.*') ? 'active' : '' }}"
                        href="{{ route('hr.staff-salary-assignments.index', $tenantKey) }}">
                        <i class="feather-file-text"></i>
                        <span>Salary Assignments</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('hr.payrolls.*') ? 'active' : '' }}"
                        href="{{ route('hr.payrolls.index', $tenantKey) }}">
                        <i class="feather-printer"></i>
                        <span>Payroll</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('hr.leave-types.*') ? 'active' : '' }}"
                        href="{{ route('hr.leave-types.index', $tenantKey) }}">
                        <i class="feather-clipboard"></i>
                        <span>Leave Types</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('hr.holidays.*') ? 'active' : '' }}"
                        href="{{ route('hr.holidays.index', $tenantKey) }}">
                        <i class="feather-calendar"></i>
                        <span>Holidays</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>
</div>
