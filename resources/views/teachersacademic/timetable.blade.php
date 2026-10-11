 <div class="tab-pane fade" id="timetableTab">
     <div class="card stretch stretch-full">
         <div class="card-header d-flex justify-content-between align-items-center">
             <h6 class="card-title mb-0">
                 <i class="feather-calendar me-2 text-primary"></i>Class Schedule
             </h6>
             <div class="d-flex gap-2 no-print">
                
                 <button id="printBtn" class="btn btn-outline-primary btn-sm">
                     <i class="feather-printer"></i> Print
                 </button>
             </div>
         </div>
         <div class="card-body p-0">
             @forelse($timetables as $timetable)
                 <div class="mb-4 border-bottom">
                     

                     <div class="table-responsive">
                         <table class="table table-borderless m-0">
                             <thead class="position-sticky top-0 z-2">
                                 <tr>
                                     <th class="period-name text-center py-2"
                                         style="min-width: 100px; background: #f8f9fa;">Period
                                     </th>
                                     @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                                         <th class="weekday-header text-center py-3">
                                             {{ $day }}</th>
                                     @endforeach
                                 </tr>
                             </thead>
                             <tbody>
                                 @php
                                     $slots = $staffSchedules->get($timetable->id, collect());
                                 @endphp
                                 @foreach ($slots->groupBy('period_id') as $periodId => $periodSlots)
                                     <tr>
                                         <td class="period-name fw-semibold bg-light text-center align-middle">
                                             {{ $periodSlots->first()->period->name ?? 'Period ' . $periodId }}
                                         </td>
                                         @foreach ([1, 2, 3, 4, 5, 6] as $dayOfWeek)
                                             @php
                                                 $slot = $periodSlots->where('day_of_week', $dayOfWeek)->first();
                                             @endphp
                                             <td class="align-middle">
                                                 @if ($slot)
                                                     <div class="timetable-slot has-class p-2">
                                                         <div class="subject-name fw-semibold">
                                                             {{ $slot->subject->name ?? 'N/A' }}
                                                         </div>
                                                         <div class="slot-details small mt-1">
                                                             <span class="badge bg-soft-primary">
                                                                 {{ $slot->timetable->class->name ?? 'Class' }}
                                                             </span>
                                                             @if ($slot->timetable->section)
                                                                 <span class="badge bg-soft-info ms-1">
                                                                     {{ $slot->timetable->section->name }}
                                                                 </span>
                                                             @endif
                                                             @if ($slot->room)
                                                                 <br>
                                                                 <span
                                                                     class="badge bg-soft-warning text-warning mt-1 d-block">
                                                                     <i
                                                                         class="feather-map-pin me-1"></i>{{ $slot->room->name }}
                                                                 </span>
                                                             @endif
                                                         </div>
                                                     </div>
                                                 @else
                                                     <div class="timetable-slot no-class p-3 text-center">
                                                         <i class="feather-clock me-1"></i>Free
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
             @empty
                 <div class="text-center p-5">
                     <i class="feather-calendar fs-1 text-muted mb-3 d-block"></i>
                     <h5 class="text-muted mb-2">No Class Schedule Assigned</h5>
                     <p class="text-muted">No classes assigned to this staff member yet.</p>
                 </div>
             @endforelse
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
         position: sticky;
         top: 0;
         z-index: 10;
     }

     .timetable-slot {
         min-height: 70px;
         border: 1px solid #e9ecef;
         border-radius: 8px;
         display: flex;
         flex-direction: column;
         justify-content: center;
         padding: 10px;
         transition: all 0.3s ease;
         margin: 2px 0;
         background: #fff;
     }

     .timetable-slot:hover {
         background-color: #f8f9ff !important;
         border-color: #0d6efd !important;
         transform: translateY(-2px);
         box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15);
     }

     .timetable-slot.has-class {
         background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important;
         color: white !important;
         border-color: transparent !important;
         box-shadow: 0 4px 15px rgba(79, 172, 254, 0.4);
     }

     .period-name {
         font-weight: 600;
         font-size: 0.85rem;
         background: #f8f9fa !important;
     }

     .subject-name {
         font-size: 0.95rem;
         font-weight: 600;
         margin-bottom: 4px;
         line-height: 1.2;
     }

     .slot-details {
         font-size: 0.75rem;
         opacity: 0.95;
         line-height: 1.3;
     }

     .no-class {
         color: #6c757d !important;
         font-style: italic;
         font-size: 0.85rem;
     }

     @media print {
         .no-print {
             display: none !important;
         }

         .timetable-slot {
             box-shadow: none !important;
             margin: 1px 0 !important;
             page-break-inside: avoid;
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
