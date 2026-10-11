<div class="content-sidebar content-sidebar-md" data-scrollbar-target="#psScrollbarInit">
    <div class="content-sidebar-header bg-white sticky-top hstack justify-content-between">
        <h4 class="fw-bolder mb-0">Library</h4>

    </div>
    <div class="content-sidebar-body">
        <ul class="nav flex-column nxl-content-sidebar-item">
            <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('Library.dashboard') ? 'active' : '' }} "
                        href="{{ route('Library.dashboard') }}">
                        <i class="feather-airplay"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
            @if (auth()->user()->hasPermissionTo('View Book Issues'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.book_issues.create') ? 'active' : '' }} "
                        href="{{ route('admin.book_issues.create') }}">
                        <i class="feather-map-pin"></i>
                        <span>Book Issues</span>
                    </a>
                </li>
            @endif
            @if (auth()->user()->hasPermissionTo('View Return Books'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.book_issues.return') ? 'active' : '' }} "
                    href="{{ route('admin.book_issues.return') }}">
                    <i class="feather-corner-up-left"></i>
                    <span>Return Books</span>
                </a>
            </li>
            @endif
            @if (auth()->user()->hasPermissionTo('View Book'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.books.*') ? 'active' : '' }} "
                        href="{{ route('admin.books.index') }}">
                        <i class="feather-book"></i>
                        <span>Books</span>
                    </a>
                </li>
            @endif
            @if (auth()->user()->hasPermissionTo('View Library Card'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.library_cards.*') ? 'active' : '' }} "
                        href="{{ route('admin.library_cards.index') }}">
                        <i class="feather-credit-card"></i>
                        <span>Library Cards</span>
                    </a>
                </li>
            @endif
            @if (auth()->user()->hasPermissionTo('View Book Fines'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.book_fines.*') ? 'active' : '' }} "
                        href="{{ route('admin.book_fines.index') }}">
                        <i class="feather-dollar-sign"></i>
                        <span>Book Fines</span>
                    </a>
                </li>
            @endif
                @if (auth()->user()->hasPermissionTo('View Book History'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.book_issues.index') ? 'active' : '' }} "
                    href="{{ route('admin.book_issues.index') }}">
                    <i class="feather-book-open"></i>
                    <span>Book History</span>
                </a>
            </li>
            @endif
            @if (auth()->user()->hasPermissionTo('View Library Stock'))
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.library.stock*') ? 'active' : '' }} "
                    href="{{ route('admin.library.stock.index') }}">
                    <i class="feather-box"></i>
                    <span>Book Stock</span>
                </a>
            </li>
            @endif
            @if (auth()->user()->hasPermissionTo('View Master Data'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.library_master.*') ? 'active' : '' }} "
                        href="{{ route('admin.library_master.index') }}">
                        <i class="feather-settings"></i>
                        <span>Master Data</span>
                    </a>
                </li>
            @endif


            @if (auth()->user()->hasPermissionTo('View Academic Group'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.academic_groups.*') ? 'active' : '' }} "
                        href="{{ route('admin.academic_groups.index') }}">
                        <i class="feather-settings"></i>
                        <span>Academic Group</span>
                    </a>
                </li>
            @endif



        </ul>
    </div>
</div>
