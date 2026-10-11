@extends('admin_layout')
@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Accountants.sidebar')
            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Fee Category List</span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                            @if(auth()->user()->hasPermissionTo('Create Fee Categories'))
                                <a href="{{ route('admin.fee_categories.create') }}" class="btn btn-primary">
                                    <i class="feather-plus me-2"></i>
                                    <span>Create Fee Category</span>
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
                                <table class="table table-hover" id="feeCategoryList">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($feeCategories as $category)
                                            <tr class="single-item">
                                                <td class="project-name-td">
                                                    <div class="hstack gap-4">
                                                        <div>
                                                            @if(auth()->user()->hasPermissionTo('Edit Fee Categories'))
                                                                <a href="{{ route('admin.fee_categories.edit', $category) }}"
                                                                   class="text-truncate-1-line">
                                                                    {{ $category->name }}
                                                                </a>
                                                            @else
                                                                <span class="text-truncate-1-line">{{ $category->name }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    {{ $category->description ? Str::limit($category->description, 80, '...') : 'N/A' }}
                                                </td>
                                                <td>
                                                    <div class="hstack gap-2 justify-content-center">
                                                        @if(auth()->user()->hasPermissionTo('Edit Fee Categories'))
                                                            <a href="{{ route('admin.fee_categories.edit', $category) }}"
                                                               class="avatar-text avatar-md text-warning" title="Edit Fee Category">
                                                                <i class="feather-edit-3"></i>
                                                            </a>
                                                        @endif

                                                        @if(auth()->user()->hasPermissionTo('Delete Fee Categories'))
                                                            <form action="{{ route('admin.fee_categories.destroy', $category) }}" method="POST" class="d-inline"
                                                                  onsubmit="return confirm('Are you sure you want to delete this fee category?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="avatar-text avatar-md text-danger"
                                                                        title="Delete Fee Category">
                                                                    <i class="feather-trash-2"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center">
                                                    No fee categories found.
                                                    @if(auth()->user()->hasPermissionTo('Create Fee Categories'))
                                                        <div class="mt-2">
                                                            <a href="{{ route('admin.fee_categories.create') }}" class="btn btn-sm btn-primary">
                                                                Create your first category
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
                    {{ $feeCategories->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection