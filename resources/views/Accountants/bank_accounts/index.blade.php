@extends('admin_layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Accountants.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">
            
            <!-- Header -->
            <div class="content-area-header bg-white sticky-top">
                <h5 class="fw-bold mb-0 me-4">
                    <span class="d-block mb-2">Bank Accounts</span>
                </h5>

                <div class="page-header-right ms-auto">
                    <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                        
                        @if(auth()->user()->hasPermissionTo('Create Bank Accounts'))
                        <a href="{{ route('admin.bank_accounts.create') }}" class="btn btn-primary">
                            <i class="feather-plus me-2"></i>
                            <span>Add Bank Account</span>
                        </a>
                        @endif

                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="content-area-body">
                @include('elements.flash')

                <div class="card stretch stretch-full">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            
                            <table class="table table-hover" id="bankAccountList">
                                <thead>
                                    <tr>
                                        <th>Bank Name</th>
                                        <th>Account Number</th>
                                        <th>IFSC Code</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($accounts as $acc)
                                        <tr class="single-item">

                                            <!-- Bank Name -->
                                            <td>
                                                @if(auth()->user()->hasPermissionTo('Edit Bank Accounts'))
                                                    <a href="{{ route('admin.bank_accounts.edit', $acc) }}"
                                                       class="text-truncate-1-line">
                                                        {{ $acc->bank_name }}
                                                    </a>
                                                @else
                                                    {{ $acc->bank_name }}
                                                @endif
                                            </td>

                                            <!-- Account Number -->
                                            <td>
                                                {{ $acc->account_number ?? 'N/A' }}
                                            </td>

                                            <!-- IFSC -->
                                            <td>
                                                {{ $acc->ifsc_code ?? 'N/A' }}
                                            </td>

                                            <!-- Status -->
                                            <td>
                                                <span class="badge 
                                                    @if($acc->status == 'active') bg-success 
                                                    @else bg-danger 
                                                    @endif">
                                                    {{ ucfirst($acc->status) }}
                                                </span>
                                            </td>

                                            <!-- Actions -->
                                            <td>
                                                <div class="hstack gap-2 justify-content-center">

                                                    <!-- Edit -->
                                                    @if(auth()->user()->hasPermissionTo('Edit Bank Accounts'))
                                                        <a href="{{ route('admin.bank_accounts.edit', $acc) }}"
                                                           class="avatar-text avatar-md text-warning"
                                                           title="Edit Account">
                                                            <i class="feather-edit-3"></i>
                                                        </a>
                                                    @endif

                                                    <!-- Delete -->
                                                    @if(auth()->user()->hasPermissionTo('Delete Bank Accounts'))
                                                        <form action="{{ route('admin.bank_accounts.destroy', $acc) }}"
                                                              method="POST"
                                                              class="d-inline"
                                                              onsubmit="return confirm('Are you sure you want to delete this bank account?')">
                                                            @csrf
                                                            @method('DELETE')

                                                            <button type="submit"
                                                                    class="avatar-text avatar-md text-danger"
                                                                    title="Delete Account">
                                                                <i class="feather-trash-2"></i>
                                                            </button>
                                                        </form>
                                                    @endif

                                                </div>
                                            </td>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">
                                                No bank accounts found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>

                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                {{ $accounts->links() }}

            </div>
        </div>
    </div>
</div>
@endsection