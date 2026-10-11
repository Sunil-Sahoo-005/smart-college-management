<div class="content-sidebar content-sidebar-md" data-scrollbar-target="#psScrollbarInit">
    <div class="content-sidebar-header bg-white sticky-top hstack justify-content-between">
        <h4 class="fw-bolder mb-0">Transportation</h4>

    </div>
    <div class="content-sidebar-body">
        <ul class="nav flex-column nxl-content-sidebar-item">
            @if(auth()->user()->hasPermissionTo('View Driver'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.driver.*') ? 'active' : '' }} " href="{{ route('admin.driver.index') }}">
                    <i class="feather-airplay"></i>
                    <span>Driver</span>
                </a>
            </li>
            @endif
            @if(auth()->user()->hasPermissionTo('View Vehicles'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.vehicle.*') ? 'active' : '' }} " href="{{ route('admin.vehicle.index') }}">
                    <i class="feather-truck"></i>
                    <span>Vehicles</span>
                </a>
            </li>
            @endif
            @if(auth()->user()->hasPermissionTo('View Routes'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.routes.*') ? 'active' : '' }} " href="{{ route('admin.routes.index') }}">
                    <i class="feather-map-pin"></i>
                    <span>Routes</span>
                </a>
            </li>
            @endif
            <!-- @if(auth()->user()->hasPermissionTo('View Stoppage'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.routestops.*') ? 'active' : '' }} " href="{{ route('admin.routestops.index') }}">
                    <i class="feather-stop-circle"></i>
                    <span>Stoppage</span>
                </a>
            </li>
            @endif -->
            @if(auth()->user()->hasPermissionTo('View Student Route Mapping'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.studentroutemapping.*') ? 'active' : '' }} " href="{{ route('admin.studentroutemapping.index') }}">
                    <i class="feather-user"></i>
                    <span>Student Route Mapping</span>
                </a>
            </li>
            @endif
        </ul>
    </div>
</div>
