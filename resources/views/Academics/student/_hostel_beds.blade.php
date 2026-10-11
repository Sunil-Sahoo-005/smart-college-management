@if($hostelBeds->isNotEmpty())
    <select class="form-select @error('bed_id') is-invalid @enderror" id="bed_id" name="bed_id" required>
        <option value="">Select Bed</option>
        @foreach($hostelBeds as $bed)
            @if($bed->status == 'vacant')
                <option value="{{ $bed->id }}" data-amount="{{ $bed->amount }}"
                    {{ old('bed_id') == $bed->id ? 'selected' : '' }}>
                    {{ $bed->bed_number }} (₹{{ $bed->amount }}/year)
                </option>
            @endif
        @endforeach
    </select>
@else
    <div class="text-muted py-3">No vacant beds</div>
@endif
