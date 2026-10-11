<div class="tab-pane fade" id="timetableTab">
    <div class="card stretch stretch-full">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="card-title mb-0">
                <i class="feather-calendar me-2 text-primary"></i>Class Schedule
            </h6>
            <div class="d-flex gap-2 no-print">
                <a href="{{ route('admin.staff.classes', $staff->id) }}" class="btn btn-primary btn-sm">
                    <i class="feather-list me-1"></i>Take Attendance
                </a>
                <button id="printBtn" class="btn btn-outline-primary btn-sm">
                    <i class="feather-printer"></i> Print
                </button>
            </div>
        </div>

        <div class="card-body p-4">

            {{-- 🟢 SECTION 1: REGULAR WEEKLY SCHEDULE (UNIFIED) --}}
            <div class="mb-5">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="feather-calendar me-2 text-primary"></i>Weekly Routine
                    </h6>
                    <span class="badge bg-soft-primary text-primary border-0 px-3 py-2">Regular Schedule</span>
                </div>

                <div class="table-responsive rounded shadow-sm border overflow-hidden">
                    <table class="table table-bordered timetable-grid mb-0">
                        <thead>
                            <tr>
                                <th class="text-center align-middle" width="120" style="background: #f9fafb;">PERIOD</th>
                                @php
                                    $currentDayOfWeek = now()->dayOfWeek;
                                @endphp
                                @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $index => $day)
                                    @php $isToday = ($index + 1) == $currentDayOfWeek; @endphp
                                    <th class="text-center py-3 {{ $isToday ? 'today-highlight-header' : '' }}" 
                                        style="background: {{ $isToday ? '#eef2ff' : '#f9fafb' }}; font-weight: 700; font-size: 11px; letter-spacing: 0.5px; color: {{ $isToday ? '#4f46e5' : '#4b5563' }};">
                                        {{ strtoupper($day) }}
                                        @if($isToday)
                                            <div class="fs-9 text-primary mt-1 fw-normal">TODAY</div>
                                        @endif
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($regularPeriods as $period)
                                <tr>
                                    <td class="text-center align-middle bg-light">
                                        <div class="fw-bold text-dark fs-13">{{ $period->name }}</div>
                                        <div class="fs-10 text-muted">{{ $period->start_time }} - {{ $period->end_time }}</div>
                                    </td>
                                    @foreach ([1, 2, 3, 4, 5, 6] as $dayOfWeek)
                                        @php
                                            $slots = $unifiedGrid->get($period->id)?->get($dayOfWeek) ?? collect();
                                            $isToday = $dayOfWeek == $currentDayOfWeek;
                                        @endphp
                                        <td class="slot-cell p-2 align-top {{ $isToday ? 'today-highlight-cell' : '' }}" 
                                            style="min-width: 150px; min-height: 100px; background: {{ $isToday ? '#f5f7ff' : 'transparent' }};">
                                            @forelse($slots as $slot)
                                                @if($isToday)
                                                    <a href="{{ route('admin.student_attendance.mark_direct', ['slot' => $slot->id, 'date' => date('Y-m-d')]) }}" class="text-decoration-none" title="Click to mark student attendance">
                                                        <div class="slot-card rounded p-2 mb-2 border-start border-4 border-primary shadow-xs bg-white">
                                                            <div class="fw-bold text-dark fs-12 mb-1 text-truncate" title="{{ $slot->subject?->name }}">
                                                                {{ $slot->subject?->name ?? 'N/A' }}
                                                            </div>
                                                            <div class="d-flex flex-wrap gap-1 mb-1">
                                                                <span class="badge bg-soft-info text-info border-0 px-1 py-0 fs-10" style="font-size: 9px;">
                                                                    {{ $slot->timetable->stream->name ?? 'N/A' }}
                                                                </span>
                                                                <span class="badge bg-soft-secondary text-dark border-0 px-1 py-0 fs-10" style="font-size: 9px;">
                                                                    {{ $slot->timetable->class->name ?? 'N/A' }} {{ $slot->timetable->section->name ?? '' }}
                                                                </span>
                                                            </div>
                                                            @if ($slot->room)
                                                                <div class="d-flex align-items-center gap-1 fs-10 text-muted">
                                                                    <i class="feather-map-pin" style="font-size: 10px;"></i> {{ $slot->room->name }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </a>
                                                @else
                                                    <div class="slot-card rounded p-2 mb-2 border-start border-4 border-secondary shadow-none bg-light opacity-50" style="cursor: not-allowed;" title="Attendance can only be marked on the day of the class">
                                                        <div class="fw-bold text-muted fs-12 mb-1 text-truncate">{{ $slot->subject?->name ?? 'N/A' }}</div>
                                                        <div class="d-flex flex-wrap gap-1 mb-1">
                                                            <span class="badge bg-soft-secondary text-muted border-0 px-1 py-0 fs-10" style="font-size: 9px;">
                                                                {{ $slot->timetable->class->name ?? 'N/A' }} {{ $slot->timetable->section->name ?? '' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endif
                                            @empty
                                                <div class="empty-slot text-center py-4">
                                                    <span class="text-muted fs-11" style="opacity: 0.3;">-</span>
                                                </div>
                                            @endforelse
                                        </td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="feather-calendar fs-1 mb-2 d-block"></i>
                                            <p class="mb-0">No regular classes assigned yet.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 🟠 SECTION 2: MERGED / SPECIAL SCHEDULE --}}
            @if ($mergedSchedules->isNotEmpty())
                <div class="mt-5 border-top pt-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="feather-git-merge me-2 text-warning"></i>Special / Merged Classes
                        </h6>
                        <span class="badge bg-soft-warning text-dark border-0 px-3 py-2">
                            Week: {{ $startOfWeek->format('d M') }} - {{ $endOfWeek->format('d M') }}
                        </span>
                    </div>

                    <div class="table-responsive rounded shadow-sm border overflow-hidden">
                        <table class="table table-bordered timetable-grid mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center align-middle" width="120" style="background: #fdfaf5;">PERIOD</th>
                                    @foreach ([1, 2, 3, 4, 5, 6] as $index => $dayOfWeek)
                                        @php $isToday = $dayOfWeek == $currentDayOfWeek; @endphp
                                        <th class="text-center py-3" style="background: {{ $isToday ? '#fffbeb' : '#fdfaf5' }}; font-weight: 700; font-size: 11px; color: {{ $isToday ? '#92400e' : '#854d0e' }};">
                                            <div>{{ Carbon\Carbon::now()->startOfWeek()->addDays($dayOfWeek-1)->format('l') }}</div>
                                            <div class="fw-normal text-muted" style="font-size: 9px;">{{ $startOfWeek->copy()->addDays($dayOfWeek-1)->format('d M') }}</div>
                                            @if($isToday)
                                                <div class="fs-9 text-warning mt-1 fw-normal">TODAY</div>
                                            @endif
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mergedPeriods as $period)
                                    <tr>
                                        <td class="text-center align-middle bg-light">
                                            <div class="fw-bold text-dark fs-13">{{ $period->name }}</div>
                                        </td>
                                        @foreach ([1, 2, 3, 4, 5, 6] as $dayOfWeek)
                                            @php
                                                $slot = $mergedByPeriodAndDay->get($period->id)?->get($dayOfWeek);
                                                $isToday = $dayOfWeek == $currentDayOfWeek;
                                            @endphp
                                            <td class="slot-cell p-2 align-top" style="min-width: 150px; background: {{ $isToday ? '#fffdf5' : 'transparent' }};">
                                                @if ($slot)
                                                    @if($isToday)
                                                        <a href="{{ route('admin.student_attendance.mark_merged_direct', ['schedule' => $slot->id, 'date' => date('Y-m-d')]) }}" class="text-decoration-none" title="Click to mark student attendance">
                                                            <div class="slot-card rounded p-2 border-start border-4 border-warning shadow-xs bg-white">
                                                                <div class="fw-bold text-dark fs-12 mb-1">{{ $slot->subject->name ?? 'N/A' }}</div>
                                                                <div class="slot-combinations mb-1">
                                                                    @foreach (collect($slot->class_combinations) as $combo)
                                                                        <span class="badge bg-light text-dark border-0 px-1 py-0 fs-9 d-block mb-1 text-truncate" style="font-size: 8px;">
                                                                            <i class="feather-users me-1"></i>
                                                                            {{ \App\Models\Stream::find($combo['stream_id'])?->name ?? 'Branch' }} - 
                                                                            {{ \App\Models\Classe::find($combo['class_id'])?->name ?? 'Semester' }}
                                                                        </span>
                                                                    @endforeach
                                                                </div>
                                                                @if ($slot->room)
                                                                    <div class="d-flex align-items-center gap-1 fs-10 text-muted">
                                                                        <i class="feather-map-pin"></i> {{ $slot->room->name }}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </a>
                                                    @else
                                                        <div class="slot-card rounded p-2 border-start border-4 border-secondary shadow-none bg-light opacity-50" style="cursor: not-allowed;">
                                                            <div class="fw-bold text-muted fs-12 mb-1">{{ $slot->subject->name ?? 'N/A' }}</div>
                                                            <div class="text-muted fs-10">Attendance disabled</div>
                                                        </div>
                                                    @endif
                                                @else
                                                    <div class="empty-slot text-center py-3">
                                                        <span class="text-muted fs-11" style="opacity: 0.2;">-</span>
                                                    </div>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>

        <style>
            .timetable-grid { border-collapse: separate; border-spacing: 0; }
            .slot-cell { background-color: #fcfcfc; }
            .slot-card { transition: all 0.2s ease; cursor: default; }
            .slot-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
            .shadow-xs { box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }
            
            .bg-soft-primary { background-color: #eff6ff; color: #2563eb; }
            .bg-soft-info { background-color: #f0f9ff; color: #0ea5e9; }
            .bg-soft-warning { background-color: #fffbeb; color: #854d0e; }
            .bg-soft-secondary { background-color: #f3f4f6; color: #374151; }
            
            .fs-10 { font-size: 10px; }
            .fs-11 { font-size: 11px; }
            .fs-12 { font-size: 12px; }
            .fs-13 { font-size: 13px; }
            .fs-9 { font-size: 9px; }
            
            @media print {
                .no-print { display: none !important; }
                .card { border: none !important; box-shadow: none !important; }
                .timetable-grid { width: 100% !important; border: 1px solid #000 !important; }
                .slot-card { border: 1px solid #ccc !important; page-break-inside: avoid; }
            }
        </style>
    </div>
</div>
<script>
    document.getElementById('printBtn').addEventListener('click', function() {
        window.print();
    });
</script>
