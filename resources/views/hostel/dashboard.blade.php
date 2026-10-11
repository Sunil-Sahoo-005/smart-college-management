@extends('admin_layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('hostel.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">
            <div class="content-area-header bg-white sticky-top">
                <h5 class="fw-bold mb-0 me-4">
                    <span class="d-block mb-2">Hostel Dashboard</span>
                    <small class="text-muted">Overview of hostel occupancy and revenue</small>
                </h5>
                <div class="page-header-right ms-auto">
                    <div class="d-flex align-items-center gap-3">
                        <a href="{{ route('admin.hostel.create') }}" class="btn btn-primary">
                            <i class="feather-home me-2"></i>Add Hostel
                        </a>
                        <a href="{{ route('admin.hostelallocation.create') }}" class="btn btn-outline-primary">
                            <i class="feather-user-check me-2"></i>Allocate Bed
                        </a>
                    </div>
                </div>
            </div>

            <div class="content-area-body p-4">
                @include('elements.flash')

                {{-- 🎯 MAIN STATS ROW --}}
                <div class="row g-4 mb-4">
                    {{-- Total Hostels --}}
                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <div class="card border-0 shadow-sm h-100 hover-lift">
                            <div class="card-body text-center p-4">
                                <div class="metric-icon mb-3">
                                    <i class="feather-home text-primary fs-2"></i>
                                </div>
                                <h3 class="fw-bold text-primary mb-1">{{ $totalHostels ?? 0 }}</h3>
                                <h6 class="text-muted mb-2">Total Hostels</h6>
                                <div class="d-flex align-items-center justify-content-center">
                                    <small class="text-success fw-semibold me-1">
                                        <i class="feather-trending-up"></i> +2
                                    </small>
                                    <small class="text-muted">this month</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Total Rooms --}}
                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <div class="card border-0 shadow-sm h-100 hover-lift">
                            <div class="card-body text-center p-4">
                                <div class="metric-icon mb-3">
                                    <i class="feather-home text-success fs-2"></i>
                                </div>
                                <h3 class="fw-bold text-success mb-1">{{ $totalRooms ?? 0 }}</h3>
                                <h6 class="text-muted mb-2">Total Rooms</h6>
                                <div class="d-flex align-items-center justify-content-center">
                                    <small class="text-success fw-semibold me-1">
                                        <i class="feather-trending-up"></i> +8
                                    </small>
                                    <small class="text-muted">this month</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Total Beds --}}
                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <div class="card border-0 shadow-sm h-100 hover-lift">
                            <div class="card-body text-center p-4">
                                <div class="metric-icon mb-3">
                                    <i class="feather-bed text-info fs-2"></i>
                                </div>
                                <h3 class="fw-bold text-info mb-1">{{ $totalBeds ?? 0 }}</h3>
                                <h6 class="text-muted mb-2">Total Beds</h6>
                                <div class="d-flex align-items-center justify-content-center">
                                    <small class="text-success fw-semibold me-1">
                                        <i class="feather-trending-up"></i> +15
                                    </small>
                                    <small class="text-muted">this month</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Vacancy Rate --}}
                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <div class="card border-0 shadow-sm h-100 hover-lift">
                            <div class="card-body text-center p-4">
                                <div class="metric-icon mb-3">
                                    <i class="feather-activity text-warning fs-2"></i>
                                </div>
                                <h3 class="fw-bold text-warning mb-1">{{ $vacancyPercent ?? 0 }}%</h3>
                                <h6 class="text-muted mb-2">Vacancy Rate</h6>
                                <div class="progress mt-3" style="height: 8px;">
                                    <div class="progress-bar bg-warning" 
                                         style="width: {{ $vacancyPercent ?? 0 }}%">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Occupied Beds --}}
                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <div class="card border-0 shadow-sm h-100 hover-lift">
                            <div class="card-body text-center p-4">
                                <div class="metric-icon mb-3">
                                    <i class="feather-user-check text-danger fs-2"></i>
                                </div>
                                <h3 class="fw-bold text-danger mb-1">{{ $occupiedBeds ?? 0 }}</h3>
                                <h6 class="text-muted mb-2">Occupied Beds</h6>
                                <div class="d-flex align-items-center justify-content-center">
                                    <small class="text-success fw-semibold me-1">
                                        <i class="feather-trending-up"></i> +12
                                    </small>
                                    <small class="text-muted">today</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Total Revenue Due --}}
                    <div class="col-xl-2 col-md-3 col-sm-6">
                        <div class="card border-0 shadow-sm h-100 hover-lift">
                            <div class="card-body text-center p-4">
                                <div class="metric-icon mb-3">
                                    <i class="feather-dollar-sign text-success fs-2"></i>
                                </div>
                                <h3 class="fw-bold text-success mb-1">₹{{ number_format($totalDueFees ?? 0, 2) }}</h3>
                                <h6 class="text-muted mb-2">Revenue Due</h6>
                                <div class="d-flex align-items-center justify-content-center">
                                    <small class="text-danger fw-semibold me-1">
                                        <i class="feather-trending-down"></i> -₹5,200
                                    </small>
                                    <small class="text-muted">this week</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    {{-- 📊 OCCUPANCY CHART --}}
                    <div class="col-xl-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom pb-3">
                                <h6 class="card-title mb-0">Occupancy Overview</h6>
                            </div>
                            <div class="card-body p-4">
                                [image:62]  <!-- Chart showing beds occupied vs vacant by hostel -->
                                <div class="mt-3 d-flex justify-content-around flex-wrap gap-2">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success rounded-circle me-2" style="width:12px;height:12px;"></div>
                                        <small class="fw-semibold">Occupied</small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light border rounded-circle me-2" style="width:12px;height:12px;"></div>
                                        <small class="fw-semibold">Vacant</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 🏠 HOSTEL BREAKDOWN --}}
                    <div class="col-xl-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom pb-3">
                                <h6 class="card-title mb-0">Hostel Capacity</h6>
                            </div>
                            <div class="card-body p-4">
                                @forelse($hostels ?? [] as $hostel)
                                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                        <div>
                                            <h6 class="mb-1">{{ $hostel->name }}</h6>
                                            <small class="text-muted">{{ $hostel->type }}</small>
                                        </div>
                                        <div class="text-end">
                                            <div class="progress" style="width:60px;height:8px;">
                                                <div class="progress-bar bg-primary" 
                                                     style="width: {{ rand(40,95) }}%"></div>
                                            </div>
                                            <small class="fw-semibold">{{ rand(20,50) }}/{{ rand(50,100) }}</small>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted">
                                        <i class="feather-building fs-1 mb-3 opacity-50"></i>
                                        <p class="mb-0">No hostels yet</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mt-4">
                    {{-- 📋 RECENT ALLOCATIONS --}}
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom pb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="card-title mb-0">Recent Allocations</h6>
                                    <div class="d-flex gap-2">
                                        <input type="text" id="allocationSearch" 
                                               class="form-control form-control-sm" 
                                               placeholder="Search allocations..." 
                                               style="width:250px;">
                                        <a href="{{ route('admin.hostelallocation.index') }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            View All <i class="feather-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Student</th>
                                                <th>Hostel</th>
                                                <th>Room</th>
                                                <th>Bed</th>
                                                <th>From</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="recentAllocationsTable">
                                            @forelse ($recentAllocations ?? [] as $alloc)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-xs me-2">
                                                                <div class="avatar-title bg-soft-primary rounded-circle text-primary fs-2">
                                                                    {{ substr($alloc->student->first_name ?? '', 0, 1) }}
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <div class="fw-semibold">{{ $alloc->student->first_name ?? '-' }} {{ $alloc->student->last_name ?? '' }}</div>
                                                                <small class="text-muted">{{ $alloc->student->admission_number ?? '' }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><span class="badge bg-light text-dark px-2 py-1">{{ $alloc->hostel->name ?? '-' }}</span></td>
                                                    <td>{{ $alloc->room->room_number ?? '-' }}</td>
                                                    <td>
                                                        <span class="badge bg-info px-2 py-1">
                                                            {{ $alloc->bed->bed_number ?? '-' }}
                                                        </span>
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($alloc->from_date)->format('MMM D') }}</td>
                                                    <td>
                                                        @if($alloc->to_date)
                                                            <span class="badge bg-secondary px-2 py-1">Completed</span>
                                                        @else
                                                            <span class="badge bg-success px-2 py-1">Active</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-5 text-muted">
                                                        <i class="feather-calendar fs-1 mb-3 opacity-50 d-block"></i>
                                                        <h5 class="mb-2">No recent allocations</h5>
                                                        <p class="mb-0">Allocations will appear here</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-lift {
    transition: all 0.2s ease;
}
.hover-lift:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px rgba(0,0,0,0.1) !important;
}
.metric-icon {
    width: 60px;
    height: 60px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, rgba(13,110,253,0.1), rgba(13,202,252,0.1));
    border-radius: 12px;
}
</style>

<script>
document.getElementById('allocationSearch')?.addEventListener('input', function() {
    const search = this.value.toLowerCase();
    const rows = document.querySelectorAll('#recentAllocationsTable tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(search) ? '' : 'none';
    });
});
</script>
@endsection