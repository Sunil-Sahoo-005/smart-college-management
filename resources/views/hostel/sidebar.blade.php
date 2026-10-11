<div class="content-sidebar content-sidebar-md" data-scrollbar-target="#psScrollbarInit">
    <div class="content-sidebar-header bg-white sticky-top hstack justify-content-between">
        <h4 class="fw-bolder mb-0">Hostel</h4>
    </div>

    <div class="content-sidebar-body">
        <ul class="nav flex-column nxl-content-sidebar-item">

             <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('hostel.dashboard') ? 'active' : '' }} "
                        href="{{ route('hostel.dashboard') }}">
                        <i class="feather-airplay"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
            {{-- HOSTEL --}}
             @if(auth()->user()->hasPermissionTo('view hostel'))
            <li class="nav-item">
                <a class="nav-link {{ Route::is('admin.hostel.*') ? 'active' : '' }}"
                   href="{{ route('admin.hostel.index') }}">
                    <i class="feather-home"></i>
                    <span>Hostel</span>
                </a>
            </li>
            @endif

            {{-- HOSTEL ROOM --}}
             @if(auth()->user()->hasPermissionTo('View Hostel Room'))
            <li class="nav-item">
                <a class="nav-link {{ Route::is('admin.hostelroom.*') ? 'active' : '' }}"
                   href="{{ route('admin.hostelroom.index') }}">
                    <i class="feather-layout"></i>
                    <span>Hostel Room</span>
                </a>
            </li>
            @endif

            {{-- HOSTEL BED --}}
             <!-- @if(auth()->user()->hasPermissionTo('View Hostel Bed'))
            <li class="nav-item">
                <a class="nav-link {{ Route::is('admin.hostelbed.*') ? 'active' : '' }}"
                   href="{{ route('admin.hostelbed.index') }}">
                    <i class="feather-layers"></i>
                    <span>Hostel Bed</span>
                </a>
            </li>
            @endif -->
             {{-- HOSTEL ALLOCATION --}}
              @if(auth()->user()->hasPermissionTo('View Hostel Allocation'))
            <li class="nav-item">
                <a class="nav-link {{ Route::is('admin.hostelallocation.*') ? 'active' : '' }}"
                   href="{{ route('admin.hostelallocation.index') }}">
                    <i class="feather-users"></i>
                    <span>Hostel Allocation</span>
                </a>
            </li>
            @endif
             {{-- HOSTEL FEES --}}
              @if(auth()->user()->hasPermissionTo('View Hostel Fees'))
            <li class="nav-item">
                <a class="nav-link {{ Route::is('admin.hostelfees.*') ? 'active' : '' }}"
                   href="{{ route('admin.hostelfees.index') }}">
                    <i class="feather-dollar-sign"></i>
                    <span>Hostel Fees</span>
                </a>
            </li> 
            @endif
        </ul>
    </div>
</div>
