@extends('teacher_layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            {{-- HEADER --}}
            <div class="content-area-header bg-white sticky-top shadow-sm">
                <div class="d-flex align-items-center justify-content-between p-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="feather-calendar me-2 text-primary"></i>
                        Class Schedule
                    </h5>

                    <button id="printBtn" class="btn btn-outline-primary btn-sm no-print" style="margin-left: 1023px;">
                        <i class="feather-printer me-1"></i> Print
                    </button>
                </div>
            </div>

            {{-- BODY --}}
            <div class="content-area-body">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">

                        @forelse($timetables as $timetable)
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle text-center m-0">
                                    <thead>
                                        <tr>
                                            <th class="period-name">Period</th>
                                            @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                                                <th class="weekday-header">{{ $day }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $slots = $staffSchedules->get($timetable->id, collect());
                                        @endphp

                                        @foreach ($slots->groupBy('period_id') as $periodId => $periodSlots)
                                            <tr>
                                                <td class="period-name bg-light fw-semibold">
                                                    {{ $periodSlots->first()->period->name ?? 'Period '.$periodId }}
                                                </td>

                                                @foreach ([1,2,3,4,5,6] as $dayOfWeek)
                                                    @php
                                                        $slot = $periodSlots->where('day_of_week', $dayOfWeek)->first();
                                                    @endphp
                                                    <td>
@if ($slot)
    @php
        $isToday = $dayOfWeek == now()->dayOfWeekIso;
    @endphp
    @if($isToday)
        <a href="{{ route('teacher.student_attendance.mark_direct', ['slot' => $slot->id, 'date' => date('Y-m-d')]) }}" class="text-decoration-none">
    @endif
    <div class="timetable-slot has-class {{ $isToday ? 'border-primary' : 'opacity-75' }}" {!! !$isToday ? 'title="Attendance can only be marked on the day of the class" style="cursor: not-allowed;"' : '' !!}>
        <div class="subject-name">
            {{ $slot->subject->name ?? 'N/A' }}
        </div>

        <div class="badge-custom">
            {{ $slot->timetable->term->name ?? 'Semester' }}
        </div>

        <div class="badge-custom">
            {{ $slot->timetable->section->name ?? 'A' }}
        </div>

        @if ($slot->room)
            <div class="room-box">
                📍 {{ $slot->room->name }}
            </div>
        @endif
    </div>
    @if($isToday)
        </a>
    @endif
@else
    <div class="timetable-slot no-class">
        <i class="feather-clock"></i>
        Free
    </div>
@endif
</td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @empty
                            <div class="text-center p-5">
                                <i class="feather-calendar fs-1 text-muted mb-3 d-block"></i>
                                <h5 class="text-muted">No Class Schedule Assigned</h5>
                                <p class="text-muted">No classes assigned to this staff member yet.</p>
                            </div>
                        @endforelse

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- BACK BUTTON --}}
<a href="{{ route('admin.staff.index') }}"
   class="btn btn-secondary position-fixed bottom-0 end-0 m-4 shadow-lg no-print"
   style="z-index: 1050;">
    <i class="feather-list me-1"></i> Back to List
</a>

{{-- STYLES --}}
<style>
/* HEADER (Days Row) */
.weekday-header {
    background: linear-gradient(135deg, #5f6af5, #8b5cf6);
    color: #fff;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: none;
}

/* PERIOD COLUMN */
.period-name {
    font-weight: 600;
    background: #f4f6f9;
    color: #2d3748;
    width: 160px;
    font-size: 14px;
}

/* SLOT CARD BASE */
.timetable-slot {
    border-radius: 12px;
    padding: 14px;
    min-height: 90px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    text-align: center;
    transition: 0.3s ease;
}

/* FREE SLOT */
.timetable-slot.no-class {
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    color: #64748b;
}

.timetable-slot.no-class i {
    font-size: 16px;
    margin-bottom: 5px;
}

/* ACTIVE CLASS SLOT */
.timetable-slot.has-class {
    background: linear-gradient(135deg, #36d1dc, #5b86e5);
    color: white;
    box-shadow: 0 8px 20px rgba(91, 134, 229, 0.3);
}

/* SUBJECT */
.subject-name {
    font-weight: 700;
    font-size: 15px;
}

/* BADGES */
.badge-custom {
    background: rgba(255,255,255,0.2);
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 11px;
    margin-top: 4px;
}

/* ROOM */
.room-box {
    background: #facc15;
    color: #92400e;
    font-size: 12px;
    padding: 4px 10px;
    border-radius: 8px;
    margin-top: 6px;
    font-weight: 600;
}

/* TABLE STYLE */
.table {
    border-collapse: separate;
    border-spacing: 0 10px;
}

.table td {
    border: none !important;
}

/* HOVER EFFECT */
.timetable-slot:hover {
    transform: translateY(-3px);
}

/* PRINT BUTTON */
#printBtn {
    border-radius: 8px;
}
</style>

{{-- PRINT SCRIPT --}}
<script>
document.getElementById('printBtn')?.addEventListener('click', function() {
    window.print();
});
</script>

@endsection