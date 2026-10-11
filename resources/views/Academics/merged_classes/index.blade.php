@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')

            <div class="content-area">
                <div class="content-area-header bg-white sticky-top">
                    <div>
                        <h5 class="fw-bold mb-1">Merged Class Schedules</h5>
                        <p class="text-muted mb-0">Exception sessions where one staff member handles multiple classes together in one room.</p>
                    </div>
                    <div class="page-header-right ms-auto">
                        <a href="{{ route('merged-classes.create') }}" class="btn btn-primary">
                            <i class="feather-plus-circle me-2"></i>Add Merged Class
                        </a>
                    </div>
                </div>

                <div class="content-area-body">
                    @include('elements.flash')

                    <div class="row g-3 mb-4">
                        <div class="col-xl-3 col-md-6">
                            <div class="card border-0 shadow-sm stat-card">
                                <div class="card-body">
                                    <span>Today</span>
                                    <h3>{{ $stats['today'] }}</h3>
                                    <p class="mb-0">Merged sessions happening today.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card border-0 shadow-sm stat-card">
                                <div class="card-body">
                                    <span>Upcoming</span>
                                    <h3>{{ $stats['upcoming'] }}</h3>
                                    <p class="mb-0">Future exception sessions.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card border-0 shadow-sm stat-card">
                                <div class="card-body">
                                    <span>Total</span>
                                    <h3>{{ $stats['total'] }}</h3>
                                    <p class="mb-0">All merged schedules on record.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card border-0 shadow-sm stat-card accent-card">
                                <div class="card-body">
                                    <span>Listed</span>
                                    <h3>{{ $mergedSchedules->count() }}</h3>
                                    <p class="mb-0">Schedules in the current result set.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <form method="GET" class="row g-3 align-items-end">
                                <div class="col-lg-3 col-md-4">
                                    <label class="form-label fw-medium mb-1">Date</label>
                                    <input type="date" name="date" value="{{ request('date') }}" class="form-control">
                                </div>
                                <div class="col-lg-4 col-md-5">
                                    <label class="form-label fw-medium mb-1">Staff</label>
                                    <select name="staff_id" class="form-select">
                                        <option value="">All Staff</option>
                                        @foreach ($staffList as $staff)
                                            <option value="{{ $staff->id }}" {{ request('staff_id') == $staff->id ? 'selected' : '' }}>
                                                {{ $staff->first_name }} {{ $staff->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-3">
                                    <label class="form-label fw-medium mb-1">Filter</label>
                                    <select name="status" class="form-select">
                                        <option value="">All Dates</option>
                                        <option value="today" {{ request('status') == 'today' ? 'selected' : '' }}>Today</option>
                                        <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-12 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-fill">Filter</button>
                                    <a href="{{ route('merged-classes.index') }}" class="btn btn-outline-secondary">Clear</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0 pt-4 px-4">
                            <h5 class="mb-1">Scheduled Merged Sessions</h5>
                            <p class="text-muted mb-0">Each row represents one shared class session with a single staff member and room.</p>
                        </div>

                        <div class="card-body pt-2 px-0">
                            <div class="table-responsive">
                                <table class="table table-modern align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Period</th>
                                            <th>Merged Classes</th>
                                            <th>Subject</th>
                                            <th>Staff</th>
                                            <th>Room</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($mergedSchedules as $schedule)
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold">{{ $schedule->schedule_date->format('d M, Y') }}</div>
                                                    <small class="text-muted">{{ $schedule->schedule_date->format('l') }}</small>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold">{{ $schedule->period->name ?? 'N/A' }}</div>
                                                    <small class="text-muted">{{ $schedule->period?->start_time }}{{ $schedule->period?->end_time ? ' - ' . $schedule->period?->end_time : '' }}</small>
                                                </td>
                                                <td>
                                                    <div class="combo-stack">
                                                        @foreach ($schedule->class_combination_labels ?? [] as $label)
                                                            <span class="combo-chip">{{ $label }}</span>
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-soft-primary text-primary border-0">{{ $schedule->subject->name ?? 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold">{{ trim(($schedule->staff->first_name ?? '') . ' ' . ($schedule->staff->last_name ?? '')) }}</div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-soft-info text-info border-0">{{ $schedule->room->name ?? 'N/A' }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('merged-classes.edit', $schedule) }}" class="btn btn-outline-primary" title="Edit">
                                                            <i class="feather-edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-outline-danger" onclick="deleteSchedule({{ $schedule->id }})" title="Delete">
                                                            <i class="feather-trash-2"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-5 border-0">
                                                    <div class="text-muted">
                                                        <i class="feather-layers fs-1 mb-3 d-block"></i>
                                                        <h5 class="mb-2">No merged classes scheduled</h5>
                                                        <p class="mb-4">Create a merged class when one session needs to serve multiple classes together.</p>
                                                        <a href="{{ route('merged-classes.create') }}" class="btn btn-primary">
                                                            <i class="feather-plus me-2"></i>Create Schedule
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        @if ($mergedSchedules->hasPages())
                            <div class="card-footer bg-transparent border-top">
                                <div class="d-sm-flex justify-content-between align-items-center">
                                    <div class="text-muted small">
                                        Showing {{ $mergedSchedules->firstItem() }} to {{ $mergedSchedules->lastItem() }} of {{ $mergedSchedules->total() }} results
                                    </div>
                                    {{ $mergedSchedules->appends(request()->query())->links('pagination::simple-bootstrap-5') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .stat-card .card-body {
            padding: 1.25rem;
        }

        .stat-card span {
            display: block;
            color: #64748b;
            font-size: 0.82rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .stat-card h3 {
            margin: 0.35rem 0;
            font-weight: 800;
            color: #0f172a;
        }

        .accent-card {
            background: linear-gradient(135deg, #f8fbff 0%, #eef6ff 100%);
        }

        .table-modern thead th {
            font-size: 0.76rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #64748b;
            background: #f8fafc;
            border-bottom: 1px solid rgba(15, 23, 42, 0.08);
        }

        .table-modern > :not(caption) > * > * {
            padding: 1rem 1.25rem;
        }

        .combo-stack {
            display: flex;
            flex-wrap: wrap;
            gap: 0.45rem;
            max-width: 420px;
        }

        .combo-chip {
            display: inline-flex;
            align-items: center;
            padding: 0.38rem 0.7rem;
            border-radius: 999px;
            border: 1px solid #cfe2ff;
            background: #f8fbff;
            color: #1d4ed8;
            font-size: 0.78rem;
            font-weight: 600;
        }
    </style>

    <script>
        function deleteSchedule(id) {
            if (!confirm('Delete this merged class schedule?')) {
                return;
            }

            fetch(`/merged-classes/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                }
            }).then(() => {
                location.reload();
            });
        }
    </script>
@endsection
