<div class="content-sidebar content-sidebar-md" data-scrollbar-target="#psScrollbarInit">
    <div class="content-sidebar-header bg-white sticky-top hstack justify-content-between">
        <h4 class="fw-bolder mb-0">Accountants</h4>

    </div>
    <div class="content-sidebar-body">
        <ul class="nav flex-column nxl-content-sidebar-item">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('accountant.dashboard') ? 'active' : '' }} "
                    href="{{ route('accountant.dashboard') }}">
                    <i class="feather-airplay"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            {{-- @if (auth()->user()->hasPermissionTo('View Student Ledger')) --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.student-ledger.index') ? 'active' : '' }} "
                    href="{{ route('admin.student-ledger.index') }}">
                    <i class="feather-user-check"></i>
                    <span>Student Ledger</span>
                </a>
            </li>
            {{-- @endif --}}
            @if (auth()->user()->hasPermissionTo('View Fee Categories'))
                <li class="nav-item">
                    <a class="nav-link  {{ request()->routeIs('admin.fee_categories.*') ? 'active' : '' }}"
                        href="{{ route('admin.fee_categories.index') }}">
                        <i class="feather-dollar-sign"></i>
                        <span>Fee Categories</span>
                    </a>
                </li>
            @endif

            @if (auth()->user()->hasPermissionTo('View Fee Structure'))
                <li class="nav-item">
                    <a class="nav-link  {{ request()->routeIs('admin.fee_structures.*') ? 'active' : '' }}"
                        href="{{ route('admin.fee_structures.index') }}">
                        <i class="feather-clipboard"></i>
                        <span>Fee Structure</span>
                    </a>
                </li>
            @endif

            @if (auth()->user()->hasPermissionTo('View Fee Installments'))
                <li class="nav-item">
                    <a class="nav-link  {{ request()->routeIs('admin.fee_installments.*') ? 'active' : '' }}"
                        href="{{ route('admin.fee_installments.index') }}">
                        <i class="feather-calendar"></i>
                        <span>Fee Installments</span>
                    </a>
                </li>
            @endif

            @if (auth()->user()->hasPermissionTo('View Student Fee Plans'))
                <li class="nav-item">
                    <a class="nav-link  {{ request()->routeIs('admin.student_fee_structures.*') ? 'active' : '' }}"
                        href="{{ route('admin.student_fee_structures.index') }}">
                        <i class="feather-file-text"></i>
                        <span>Student fee Plans</span>
                    </a>
                </li>
            @endif

            @if (auth()->user()->hasPermissionTo('View Generate Fee Schedule'))
                <!-- <li class="nav-item">
                    <a class="nav-link  {{ request()->routeIs('admin.student-fee-plans.generate') ? 'active' : '' }}"
                        href="{{ route('admin.student-fee-plans.generate') }}">
                        <i class="feather-users"></i>
                        <span>Generate Fee Schedule</span>
                    </a>
                </li> -->
            @endif

            @if (auth()->user()->hasPermissionTo('View Student Fee Plans'))
                {{-- <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.student-fee-plans.index') }}">
                    <i class="feather-users"></i>
                    <span>Student Fee Plans</span>
                </a>
            </li> --}}
                {{-- <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.student-fee-plans.index') }}">
                    <i class="feather-users"></i>
                    <span>Student Fee Plans</span>
                </a>
                 </li> --}}
            @endif
            @if (auth()->user()->hasPermissionTo('View Extra Fees'))
                <li class="nav-item">
                    <a class="nav-link  {{ request()->routeIs('admin.extra_fee.*') ? 'active' : '' }}"
                        href="{{ route('admin.extra_fee.create') }}">
                        <i class="feather-plus-circle"></i>
                        <span>Extra Fees</span>
                    </a>
                    {{-- <a href="{{ route('admin.extra_fee.create') }}" class="btn btn-outline-primary">
                    Extra Fees
                </a> --}}

                </li>
            @endif
            {{-- <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.student-fee-plans.index') }}">
                    <i class="feather-users"></i>
                    <span>Student Fee Plans</span>
                </a> --}}

            @if (auth()->user()->hasPermissionTo('View Fee Payment'))
                <li class="nav-item">
                    <a class="nav-link  {{ request()->routeIs('admin.fee-payments.*') ? 'active' : '' }}"
                        href="{{ route('admin.fee-payments.index') }}">
                        <i class="feather-dollar-sign"></i>
                        <span>Fee Payment</span>
                    </a>
                </li>
            @endif

            @if (auth()->user()->hasPermissionTo('View Expense Heads'))
                <li class="nav-item">
                    <a class="nav-link  {{ request()->routeIs('expense-heads.*') ? 'active' : '' }}"
                        href="{{ route('expense-heads.index') }}">
                        <i class="feather-minus-circle"></i>
                        <span>Expense Heads</span>
                    </a>
                </li>
            @endif
            @if (auth()->user()->hasPermissionTo('View Expense Records'))
                <li class="nav-item">
                    <a class="nav-link  {{ request()->routeIs('expense-records.*') ? 'active' : '' }}"
                        href="{{ route('expense-records.index') }}">
                        <i class="feather-minus-circle"></i>
                        <span>Expense Records</span>
                    </a>
                </li>
            @endif
            @if (auth()->user()->hasPermissionTo('View Income Heads'))
                <li class="nav-item">
                    <a class="nav-link  {{ request()->routeIs('income-heads.*') ? 'active' : '' }}"
                        href="{{ route('income-heads.index') }}">
                        <i class="feather-plus-circle"></i>
                        <span>Income Heads</span>
                    </a>
                </li>
            @endif

            @if (auth()->user()->hasPermissionTo('View Income Records'))
                <li class="nav-item">
                    <a class="nav-link  {{ request()->routeIs('income-records.*') ? 'active' : '' }}"
                        href="{{ route('income-records.index') }}">
                        <i class="feather-file-text"></i>
                        <span>Income Records</span>
                    </a>
                </li>
            @endif
            @if (auth()->user()->hasPermissionTo('View Ledgers'))
                <li class="nav-item">
                    <a class="nav-link  {{ request()->routeIs('ledgers.*') ? 'active' : '' }}"
                        href="{{ route('ledgers.index') }}">
                        <i class="feather-book"></i>
                        <span>Ledgers</span>
                    </a>
                </li>
            @endif

            {{-- @if (auth()->user()->hasPermissionTo('View Scholarships')) --}}
                <li class="nav-item">
                    <a class="nav-link  {{ request()->routeIs('scholarships.*') && !request()->routeIs('scholarships.list') && !request()->routeIs('scholarships.assign*') ? 'active' : '' }}"
                        href="{{ route('scholarships.index') }}">
                        <i class="feather-award"></i>
                        <span>Scholarships</span>
                    </a>
                </li>
            {{-- @endif --}}

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('scholarships.list') || request()->routeIs('scholarships.assign*') ? 'active' : '' }}"
                        href="{{ route('scholarships.list') }}">
                        <i class="feather-gift"></i>
                        <span>Scholarship Assignments</span>
                    </a>
                </li>

            @if (auth()->user()->hasPermissionTo('View Bank Accounts'))
                 <li class="nav-item">
                    <a class="nav-link  {{ request()->routeIs('admin.bank_accounts.*') ? 'active' : '' }}"
                        href="{{ route('admin.bank_accounts.index') }}">
                        <i class="feather-credit-card"></i>
                        <span>Bank Accounts</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>
</div>
