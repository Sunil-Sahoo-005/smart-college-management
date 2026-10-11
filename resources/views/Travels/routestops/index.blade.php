@extends('admin_layout')
@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Travels.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                <div class="card-header">
                            <h6 class="card-title mb-0">Route Stops List</h6>
                        </div>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                            @if(auth()->user()->hasPermissionTo('Create Stoppage'))
                            <a href="{{ route('admin.routestops.create') }}" class="btn btn-primary">
                                <i class="feather-plus me-2"></i>
                                <span>Create Stoppage</span>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="content-area-body">
                @include('elements.flash')
                <div class="card stretch stretch-full">
                 
                 <div class="card-body">
                   

                    @if ($routestops->isEmpty())
                        <div class="text-center py-4">
                            <p class="text-muted">No Routestops found.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <!-- ... same header ... -->

                            <table class="table table-hover" id="routestopsList">
                                <thead>
                                    <tr>
                                        <th>Route Name</th>
                                        <th>Stop name</th>
                                        <th>Arrival time</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse ($routestops as $v)
                                <tr>
                                    <td>{{ $v->route->name ?? 'N/A' }}</td>

                                    <td>{{ $v->stop_name }}</td>

                                    <td>{{ $v->arrival_time }}</td>

                                 
                                    <td class="text-end">
                                        <div class="hstack gap-2 justify-content-end">
                                            <a href="{{ route('admin.routestops.edit', $v->id) }}" class="avatar-text avatar-md">
                                                <i class="feather-edit-3"></i>
                                            </a>

                                            <form action="{{ route('admin.routestops.destroy', $v->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="avatar-text avatar-md border-0"
                                                    onclick="return confirm('Are you sure?')">
                                                    <i class="feather-trash-2"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No routes found.</td>
                                </tr>
                                @endforelse
                                </tbody>

                            </table>


                            {{ $routestops->links() }}
                        </div>
                    @endif
                </div>
            </div>
                </div>
            </div>
        </div>
    </div>
@endsection
