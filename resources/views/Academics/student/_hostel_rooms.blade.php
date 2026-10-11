@if($hostelRooms->isNotEmpty())
    <select class="form-select @error('room_id') is-invalid @enderror" id="room_id" name="room_id">
        <option value="">Select Room</option>
        @foreach($hostelRooms as $room)
            <option value="{{ $room->id }}"
                {{ old('room_id') == $room->id ? 'selected' : '' }}>
                {{ $room->room_number }} ({{ $room->type }}, {{ $room->max_beds }} beds)
            </option>
        @endforeach
    </select>
@else
    <div class="text-muted py-3">No rooms available</div>
@endif
