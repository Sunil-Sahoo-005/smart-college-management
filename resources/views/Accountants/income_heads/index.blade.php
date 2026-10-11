@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Accountants.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Income Heads List</span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                            @if(auth()->user()->hasPermissionTo('Create Income Heads'))
                                <a href="{{ route('income-heads.create') }}" class="btn btn-primary">
                                    <i class="feather-plus me-2"></i>
                                    <span>Create Income Head</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="content-area-body">
                    @include('elements.flash')
                    <div class="card stretch stretch-full">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover" id="incomeHeadList">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Created</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($incomeHeads as $head)
                                            <tr class="single-item">
                                                <td class="project-name-td">
                                                    <div class="hstack gap-4">
                                                        <div>
                                                            @if(auth()->user()->hasPermissionTo('Edit Income Heads'))
                                                                <a href="{{ route('income-heads.edit', $head) }}"
                                                                   class="text-truncate-1-line">
                                                                    {{ $head->name }}
                                                                </a>
                                                            @else
                                                                <span class="text-truncate-1-line">{{ $head->name }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $head->type == 'fee' ? 'bg-primary' : 'bg-success' }}">
                                                        {{ ucfirst($head->type) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    {{ $head->created_at->format('d M Y') }}
                                                </td>
                                                <td>
                                                    <div class="hstack gap-2 justify-content-center">
                                                        @if(auth()->user()->hasPermissionTo('Edit Income Heads'))
                                                            <a href="{{ route('income-heads.edit', $head) }}"
                                                               class="avatar-text avatar-md text-warning" title="Edit Income Head">
                                                                <i class="feather-edit-3"></i>
                                                            </a>
                                                        @endif

                                                        @if(auth()->user()->hasPermissionTo('Delete Income Heads'))
                                                            <form action="{{ route('income-heads.destroy', $head) }}" method="POST" class="d-inline"
                                                                  onsubmit="return confirm('Are you sure you want to delete this income head?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="avatar-text avatar-md text-danger border-0 bg-transparent"
                                                                        title="Delete Income Head">
                                                                    <i class="feather-trash-2"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-muted">
                                                    <i class="feather-dollar-sign fs-1 mb-3 d-block opacity-50"></i>
                                                    No income heads found.
                                                    @if(auth()->user()->hasPermissionTo('Create Income Heads'))
                                                        <div class="mt-3">
                                                            <a href="{{ route('income-heads.create') }}" class="btn btn-sm btn-primary">
                                                                Create your first income head
                                                            </a>
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{ $incomeHeads->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection