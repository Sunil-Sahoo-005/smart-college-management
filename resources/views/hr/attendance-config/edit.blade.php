@extends('hr.layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('hr.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">
            <div class="content-area-header bg-white sticky-top px-4 py-3 border-bottom shadow-sm mb-2">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div>
                        <h5 class="fw-bold mb-0">Attendance Configuration</h5>
                        <small class="text-muted">Set office timing and working rules for each day</small>
                    </div>
                    <span class="badge bg-{{ $config->is_active ? 'success' : 'secondary' }}">
                        {{ $config->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>

            <div class="content-area-body px-4 py-4">
                @include('elements.flash')

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('hr.attendance-config.update', $tenantKey) }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <div class="row g-4 align-items-end">
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Config Name</label>
                                            <input type="text" name="name" class="form-control" value="{{ old('name', $config->name) }}" placeholder="Default">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">Late After (minutes)</label>
                                            <input type="number" min="0" name="late_after_minutes" class="form-control" value="{{ old('late_after_minutes', $config->late_after_minutes) }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">Early Exit (minutes)</label>
                                            <input type="number" min="0" name="early_exit_minutes" class="form-control" value="{{ old('early_exit_minutes', $config->early_exit_minutes) }}">
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $config->is_active) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-semibold" for="is_active">Active</label>
                                            </div>
                                            <button type="submit" class="btn btn-primary w-100">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                                        <div>
                                            <h6 class="fw-bold mb-1">Weekly Day Rules</h6>
                                            <small class="text-muted">Toggle working days and set schedule for each day</small>
                                        </div>
                                        <div class="text-muted small">
                                            Tip: uncheck a day to mark it as non-working
                                        </div>
                                    </div>

                                    @php
                                        $dayMap = $config->days->keyBy('day_of_week');
                                    @endphp

                                    <div class="row g-3">
                                        @foreach($days as $day)
                                            @php
                                                $row = $dayMap->get($day['index']);
                                                $working = old("days.{$day['key']}.is_working_day", $row?->is_working_day ?? true);
                                            @endphp
                                            <div class="col-12">
                                                <div class="border rounded-4 p-3 bg-white">
                                                    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-3">
                                                        <div class="d-flex align-items-center gap-3">
                                                            <div class="day-pill">{{ substr($day['label'], 0, 3) }}</div>
                                                            <div>
                                                                <div class="fw-semibold">{{ $day['label'] }}</div>
                                                                <small class="text-muted">Day {{ $day['index'] }}</small>
                                                            </div>
                                                        </div>
                                                        <div class="form-check form-switch">
                                                            <input type="hidden" name="days[{{ $day['key'] }}][is_working_day]" value="0">
                                                            <input class="form-check-input day-toggle" data-target="day-{{ $day['key'] }}" type="checkbox" name="days[{{ $day['key'] }}][is_working_day]" value="1" {{ $working ? 'checked' : '' }}>
                                                            <label class="form-check-label fw-semibold">Working Day</label>
                                                        </div>
                                                    </div>

                                                    <div id="day-{{ $day['key'] }}" class="row g-3 {{ $working ? '' : 'opacity-50' }}">
                                                        <div class="col-md-3">
                                                            <label class="form-label small text-muted">Office Start</label>
                                                            <input type="time" name="days[{{ $day['key'] }}][office_start_time]" class="form-control" value="{{ old("days.{$day['key']}.office_start_time", $row?->office_start_time) }}">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label small text-muted">Office End</label>
                                                            <input type="time" name="days[{{ $day['key'] }}][office_end_time]" class="form-control" value="{{ old("days.{$day['key']}.office_end_time", $row?->office_end_time) }}">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label small text-muted">Full Day Minutes</label>
                                                            <input type="number" min="1" name="days[{{ $day['key'] }}][full_day_minutes]" class="form-control" value="{{ old("days.{$day['key']}.full_day_minutes", $row?->full_day_minutes) }}" placeholder="480">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label small text-muted">Half Day Minutes</label>
                                                            <input type="number" min="1" name="days[{{ $day['key'] }}][half_day_minutes]" class="form-control" value="{{ old("days.{$day['key']}.half_day_minutes", $row?->half_day_minutes) }}" placeholder="240">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.day-pill {
    width: 44px;
    height: 44px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #e6f1fb, #cfe3fb);
    color: #185fa5;
    font-weight: 700;
    letter-spacing: .02em;
}
</style>

<script>
document.querySelectorAll('.day-toggle').forEach(function(toggle) {
    const sync = function() {
        const target = document.getElementById(toggle.dataset.target);
        if (!target) return;
        target.classList.toggle('opacity-50', !toggle.checked);
        target.querySelectorAll('input').forEach(function(input) {
            input.disabled = !toggle.checked && input.type !== 'hidden';
        });
    };
    toggle.addEventListener('change', sync);
    sync();
});
</script>
@endsection
