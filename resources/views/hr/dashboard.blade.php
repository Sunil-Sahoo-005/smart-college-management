@extends('hr.layout')
@php use Illuminate\Support\Str; @endphp

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        <div class="content-area" data-scrollbar-target="#psScrollbarInit">
            <div class="content-area-body px-4 py-4">
                <div class="row g-4">
                    @forelse($tenants as $tenant)
                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <a href="{{ route('hr.tenant.dashboard', Str::slug($tenant->name) . '-' . $tenant->id) }}" class="text-decoration-none">
                                <div class="card border-0 shadow-sm h-100 tenant-card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start justify-content-between mb-3">
                                            <div class="avatar-text rounded-3 bg-soft-primary text-primary fw-bold d-flex align-items-center justify-content-center" style="width:54px;height:54px;">
                                                {{ strtoupper(substr($tenant->name, 0, 1)) }}
                                            </div>
                                            <span class="badge bg-soft-success text-success">{{ ucfirst($tenant->status) }}</span>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-2">{{ $tenant->name }}</h5>
                                        <div class="text-muted fs-12 mb-3">
                                            {{ $tenant->email ?? 'No email provided' }}
                                        </div>
                                        <div class="d-flex flex-wrap gap-2">
                                            <span class="badge bg-light text-dark">{{ $tenant->staff_count }} staff</span>
                                            <span class="badge bg-light text-dark">{{ $tenant->students_count }} students</span>
                                            <span class="badge bg-light text-dark">{{ $tenant->academic_years_count }} years</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body py-5 text-center text-muted">
                                    No active tenants found.
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .tenant-card {
        transition: transform .18s ease, box-shadow .18s ease;
    }
    .tenant-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 40px rgba(16, 42, 67, .12) !important;
    }
</style>
@endsection
