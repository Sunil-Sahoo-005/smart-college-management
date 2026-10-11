 <div class="tab-pane fade" id="transportTab">
     <div class="card stretch stretch-full">
         <div class="card-header">
             <h6 class="card-title mb-0">
                 <i class="feather-truck me-2 text-primary"></i>Transport Details
             </h6>
         </div>
         <div class="card-body">
             <form id="transportForm" method="POST" action="{{ route('admin.student.transport.store', $student->id) }}">
                 @csrf
                 <input type="hidden" name="student_id" value="{{ $student->id }}">

                 @if ($existingMapping)
                     <input type="hidden" name="mapping_id" value="{{ $existingMapping->id }}">
                 @endif

                 <div class="row mb-4">
                     <div class="col-lg-6">
                         <label class="form-label fw-semibold">Route <span class="text-danger">*</span></label>
                         <select class="form-select @error('route_id') is-invalid @enderror" id="route_id"
                             name="route_id" required>
                             <option value="">Select Route</option>
                             @foreach ($routes as $route)
                                 <option value="{{ $route->id }}"
                                     {{ old('route_id', $existingMapping->route_id ?? '') == $route->id ? 'selected' : '' }}>
                                     {{ $route->name }}
                                     @if ($route->vehicle)
                                         ({{ $route->vehicle->registration_number }} -
                                         {{ $route->vehicle->capacity }} seats)
                                     @endif
                                 </option>
                             @endforeach
                         </select>
                         @error('route_id')
                             <div class="invalid-feedback">{{ $message }}</div>
                         @enderror
                     </div>
                     <div class="col-lg-6">
                         <label class="form-label fw-semibold">Bus Stop</label>
                         <div id="stops-container">
                             @if (old('stop_id', $existingMapping->stop_id ?? ''))
                                 @include('Academics.student._route_stops', [
                                     'routeId' => old('route_id', $existingMapping->route_id ?? ''),
                                 ])
                             @else
                                 <div class="text-muted py-3">Select route first</div>
                             @endif
                         </div>
                         @error('stop_id')
                             <div class="invalid-feedback">{{ $message }}</div>
                         @enderror
                     </div>
                 </div>

                 {{-- <div class="row mb-4">
                     <div class="col-lg-6">
                         <label class="form-label fw-semibold">Transport Fee</label>
                         <input type="number" step="0.01" name="transport_fee" class="form-control"
                             value="{{ old('transport_fee', $existingMapping->transport_fee ?? 0) }}" min="0">
                     </div>
                 </div> --}}

                 <div class="d-flex gap-2">
                     <button type="submit" class="btn btn-primary">
                         {{ $existingMapping ? 'Update' : 'Assign' }} Transport
                     </button>
                     @if ($existingMapping)
                         <button type="button" class="btn btn-outline-danger"
                             onclick="removeTransport({{ $student->id }})">
                             Remove Transport
                         </button>
                     @endif
                 </div>
             </form>

             {{-- 🔥 CURRENT ASSIGNMENT DISPLAY --}}
             @if ($existingMapping)
                 <div class="mt-4 p-4 border rounded bg-light">
                     <h6 class="mb-3">
                         <i class="feather-check-circle text-success me-2"></i>
                         Current Transport Assignment
                     </h6>
                     <div class="row text-center">
                         <div class="col-md-4">
                             <div class="p-3">
                                 <i class="feather-map fs-2 text-primary mb-2 d-block"></i>
                                 <h6>{{ $existingMapping->route->name }}</h6>
                                 <small class="text-muted">Route</small>
                             </div>
                         </div>
                         <div class="col-md-4">
                             <div class="p-3">
                                 <i class="feather-map-pin fs-2 text-info mb-2 d-block"></i>
                                 <h6>{{ $existingMapping->stop->stop_name ?? 'N/A' }}</h6>
                                 <small class="text-muted">{{ $existingMapping->stop->arrival_time ?? 'N/A' }}</small>
                             </div>
                         </div>
                         <div class="col-md-4">
                             <div class="p-3">
                                 <i class="feather-dollar-sign fs-2 text-success mb-2 d-block"></i>
                                 <h6>₹{{ number_format($existingMapping->transport_fee, 2) }}
                                 </h6>
                                 <small class="text-muted">Monthly Fee</small>
                             </div>
                         </div>
                     </div>
                 </div>
             @endif
         </div>
     </div>
 </div>
 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

 <script>
     $(document).ready(function() {
         $('#route_id').change(function() {
             const routeId = $(this).val();
             const container = $('#stops-container');

             if (routeId) {
                 // ✅ FIX: Pass route ID as parameter
                 $.get(`/admin/routes/${routeId}/stops`, function(data) {
                     container.html(data);
                 }).fail(function() {
                     container.html('<div class="alert alert-danger">Error loading stops</div>');
                 });
             } else {
                 container.html('<div class="text-muted py-3">Select route first</div>');
             }
         });
     });

     function removeTransport(studentId) {
         console.log('Removing transport for student ID:', studentId); // Debug log
         if (confirm('Remove transport assignment?')) {
             // ✅ FIX: Direct URL instead of route helper
             $.ajax({
                 url: `/admin/student/${studentId}/transport/destroy`,
                 method: 'post',
                 data: {
                     _token: '{{ csrf_token() }}'
                 },
                 success: function() {
                     location.reload();
                 }
             });
         }
     }
 </script>
