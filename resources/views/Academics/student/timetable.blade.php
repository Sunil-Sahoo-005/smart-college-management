{{-- 🔥 TIMETABLE TAB --}}
<div class="tab-pane fade" id="timetableTab">
    <div class="card stretch stretch-full">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="card-title mb-0">
                <i class="feather-calendar me-2 text-primary"></i>
                My Timetable
            </h6>
            <div class="d-flex gap-2 no-print">
                {{-- <select id="termFilter" class="form-select form-select-sm">
                    <option value="">All Terms</option>
                    @foreach($terms as $term)
                        <option value="{{ $term->id }}">{{ $term->name }}</option>
                    @endforeach
                </select> --}}
                <button id="printBtn" class="btn btn-outline-primary btn-sm">
                    <i class="feather-printer"></i> Print
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            @forelse($timetables as $timetable)
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-top">
                    <h6 class="mb-0 fw-semibold">
                        {{ $timetable->name }} - {{ $timetable->term->name ?? 'Term' }}
                    </h6>
                    <small class="text-muted">{{ $timetable->updated_at->format('M d, Y') }}</small>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-borderless m-0">
                        <thead>
                            <tr>
                                <th class="period-name text-center py-2" style="min-width: 100px; background: #f8f9fa;">Period</th>
                                @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                                    <th class="weekday-header text-center py-3">{{ $day }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($timetable->slots->groupBy('period_id') as $periodId => $slots)
                                <tr>
                                    <td class="period-name fw-semibold bg-light text-center align-middle">
                                        {{ $slots->first()->period->name ?? 'Period ' . $periodId }}
                                    </td>
                                    @foreach([1,2,3,4,5,6] as $dayOfWeek)
                                        @php $slot = $slots->where('day_of_week', $dayOfWeek)->first(); @endphp
                                        <td class="align-middle">
                                            @if($slot)
                                                <div class="timetable-slot has-class p-2">
                                                    <div class="subject-name">{{ $slot->subject->name }}</div>
                                                    <div class="slot-details small">
                                                        <i class="feather-user me-1"></i>{{ $slot->staff->first_name ?? 'TBD' }}
                                                        @if($slot->room) | {{ $slot->room->name }} @endif
                                                    </div>
                                                </div>
                                            @else
                                                <div class="timetable-slot no-class p-3 text-center">No Class</div>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @empty
                <div class="text-center p-5">
                    <i class="feather-calendar fs-1 text-muted mb-3"></i>
                    <h5 class="text-muted">No Timetable Assigned</h5>
                </div>
            @endforelse

            @if (!empty($practicalTimetables) && $practicalTimetables->isNotEmpty())
                <div class="border-top mt-4 pt-4">
                    <div class="d-flex justify-content-between align-items-center px-3 mb-3">
                        <h6 class="mb-0">
                            <i class="feather-layers me-2 text-primary"></i>
                            Practical Timetable
                        </h6>
                    </div>

                    @forelse($practicalTimetables as $practicalTimetable)
                        @php
                            $slotsByPeriod = $practicalTimetable->slots->groupBy('attendance_period_id');
                        @endphp
                        <div class="mb-4 px-3">
                            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-top">
                                <h6 class="mb-0 fw-semibold">
                                    {{ $practicalTimetable->practicalGroup?->name ?? 'Practical Group' }}
                                    <span class="text-muted fw-normal">
                                        • {{ $practicalTimetable->practicalGroup?->groupMaster?->name ?? 'Group Master' }}
                                    </span>
                                </h6>
                                <small class="text-muted">{{ $practicalTimetable->slots->count() }} slot(s)</small>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-borderless m-0">
                                    <thead>
                                        <tr>
                                            <th class="period-name text-center py-2" style="min-width: 100px; background: #f8f9fa;">Period</th>
                                            @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                                                <th class="weekday-header text-center py-3">{{ $day }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($slotsByPeriod as $periodId => $slots)
                                            <tr>
                                                <td class="period-name fw-semibold bg-light text-center align-middle">
                                                    {{ $slots->first()->period->name ?? 'Period ' . $periodId }}
                                                </td>
                                                @foreach([1,2,3,4,5,6] as $dayOfWeek)
                                                    @php $slot = $slots->where('day_of_week', $dayOfWeek)->first(); @endphp
                                                    <td class="align-middle">
                                                        @if($slot)
                                                            <div class="timetable-slot has-class p-2">
                                                                <div class="subject-name">{{ $slot->subject->name ?? 'N/A' }}</div>
                                                                <div class="slot-details small">
                                                                    <i class="feather-users me-1"></i>{{ $practicalTimetable->practicalGroup?->name ?? 'Group' }}
                                                                    <br>
                                                                    <i class="feather-user me-1"></i>{{ trim(($slot->staff->first_name ?? '') . ' ' . ($slot->staff->last_name ?? '')) ?: 'TBD' }}
                                                                    @if($slot->room) | {{ $slot->room->name }} @endif
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div class="timetable-slot no-class p-3 text-center">No Class</div>
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @empty
                        <div class="text-center p-5">
                            <i class="feather-layers fs-1 text-muted mb-3"></i>
                            <h5 class="text-muted">No Practical Timetable Assigned</h5>
                        </div>
                    @endforelse
                </div>
            @endif
        </div>
    </div>
</div>
<style>
.weekday-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    color: white !important;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}
.timetable-slot {
    min-height: 70px;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 8px;
    transition: all 0.2s ease;
    margin: 2px;
}
.timetable-slot:hover {
    background-color: #f8f9ff !important;
    border-color: #0d6efd !important;
    transform: translateY(-1px);
}
.timetable-slot.has-class {
    /* background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important; */
    /* color: white !important; */
    border-color: transparent !important;
    box-shadow: 0 2px 8px rgba(240, 147, 251, 0.3);
}
.period-name {
    font-weight: 600;
    font-size: 0.8rem;
    margin-bottom: 4px;
}
.subject-name {
    font-size: 0.9rem;
    font-weight: 500;
    margin-bottom: 2px;
}
.slot-details {
    font-size: 0.75rem;
    opacity: 0.9;
    line-height: 1.2;
}
.no-class {
    color: #6c757d;
    font-style: italic;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
}
@media print {
    .no-print { display: none !important; }
    .timetable-slot { 
        box-shadow: none !important; 
        page-break-inside: avoid;
        margin: 1px !important;
    }
    .weekday-header { 
        -webkit-print-color-adjust: exact !important;
        color-adjust: exact !important;
    }
}
</style>

 <script>
     document.getElementById('printBtn')?.addEventListener('click', function() {
         window.print();
     });
 </script>
