@extends('hr.layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('hr.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top px-4 py-3 border-bottom shadow-sm mb-2">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div>
                            <h5 class="fw-bold mb-0">Salary Structures</h5>
                            <small class="text-muted">Manage salary templates for staff in this tenant.</small>
                        </div>
                        @if (auth()->user()->hasPermissionTo('Create SalaryStructures'))
                        <a href="{{ route('hr.salary-structures.create', $tenantKey) }}" class="btn btn-primary btn-sm">
                            <i class="feather-plus me-1"></i> Add Structure
                        </a>
                        @endif
                    </div>
                </div>

                <div class="content-area-body px-4 py-4">
                    @include('elements.flash')

                    <div class="card shadow-sm border-0">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th>Components</th>
                                            <th>Status</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($salaryStructures as $structure)
                                            <tr>
                                                <td class="fw-semibold">{{ $structure->name }}</td>
                                                <td class="text-muted">
                                                    {{ \Illuminate\Support\Str::limit($structure->description, 80) ?: '—' }}
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark border">
                                                        {{ is_array($structure->components) ? count($structure->components) : 0 }}
                                                        items
                                                    </span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $structure->is_active ? 'success' : 'secondary' }}">
                                                        {{ $structure->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="hstack gap-2 justify-content-center">

                                                        @if (auth()->user()->hasPermissionTo('Edit SalaryStructures'))
                                                        <a href="{{ route('hr.salary-structures.edit', [$tenantKey, $structure]) }}"
                                                        class="avatar-text avatar-md text-warning"
                                                        title="Edit">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </a>
                                                        @endif

                                                        @if (auth()->user()->hasPermissionTo('Delete SalaryStructures'))
                                                        <form action="{{ route('hr.salary-structures.destroy', [$tenantKey, $structure]) }}"
                                                            method="POST"
                                                            class="d-inline"
                                                            onsubmit="return confirm('Delete this salary structure?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="avatar-text avatar-md text-danger border-0 bg-transparent"
                                                                    title="Delete">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                        @endif

                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5 text-muted">No salary structures
                                                    found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-3">
                                {{ $salaryStructures->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
