{{-- Simple List UI - Clean & User Friendly --}}
@if ($routeStops->isNotEmpty())
    <div class="stop-list-container">
        @foreach ($routeStops as $stop)
            <div class="stop-item d-flex align-items-center p-3 mb-2 border rounded-3 {{ old('stop_id') == $stop->id ? 'active' : '' }}"
                data-stop-id="{{ $stop->id }}">

                <div class="flex-shrink-0 me-3">
                    <div class="stop-number bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 36px; height: 36px; font-size: 0.85rem; font-weight: 600;">
                        {{ $loop->iteration }}
                    </div>
                </div>

                <div class="flex-grow-1">
                    <div class="fw-semibold">{{ $stop->stop_name }}</div>
                    @if ($stop->arrival_time)
                        <small class="text-muted">
                            <i
                                class="feather-clock me-1"></i>{{ \Carbon\Carbon::parse($stop->arrival_time)->format('h:i A') }}
                        </small>
                    @endif
                </div>

                <div class="form-check ms-auto">
                    <input class="form-check-input" type="radio" name="stop_id" id="stop-{{ $stop->id }}"
                        value="{{ $stop->id }}" {{ old('stop_id') == $stop->id ? 'checked' : '' }}>
                    <label class="form-check-label cursor-pointer" for="stop-{{ $stop->id }}"></label>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="p-4 text-center text-muted">
        <i class="feather-alert-circle fs-3 mb-2"></i>
        <div>No stops available for this route</div>
    </div>
@endif

<style>
    .stop-item {
        transition: all 0.2s ease;
        cursor: pointer;
        border: 1px solid #e9ecef !important;
    }

    .stop-item:hover {
        background-color: #f8f9fa;
        border-color: #0d6efd !important;
        transform: translateX(4px);
    }

    .stop-item.active {
        background: linear-gradient(90deg, #667eea10, #764ba210) !important;
        border-color: #667eea !important;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
    }

    .stop-item.active .stop-number {
        background: #667eea !important;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.4);
    }

    .cursor-pointer {
        cursor: pointer;
    }
</style>
