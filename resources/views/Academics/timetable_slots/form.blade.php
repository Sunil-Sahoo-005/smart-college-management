@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">
                            {{ !empty($slot->id) ? 'Edit Timetable Slot' : 'Create Timetable Slot' }}
                        </span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                            <a href="{{ route('admin.timetable_slots.index') }}" class="btn btn-secondary">
                                <i class="feather-arrow-left me-2"></i>
                                <span>Back to List</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="content-area-body">
                    @include('elements.flash')
                    <div class="card stretch stretch-full">
                        <div class="card-body general-info">
                            <div class="mb-5 d-flex align-items-center justify-content-between">
                                <h5 class="fw-bold mb-0 me-4">
                                    <span class="d-block mb-2">Timetable Slot Info :</span>
                                    <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                        Define subject, staff, and room for each timetable‑day‑period
                                    </span>
                                </h5>
                                <button type="submit" form="timetableSlotForm" class="btn btn-primary">Save</button>
                            </div>

                            <form id="timetableSlotForm" action="{{ route('admin.timetable_slots.store') }}" method="POST">
                                @csrf
                                @if (!empty($slot->id))
                                    <input type="hidden" name="id" value="{{ $slot->id }}">
                                @endif

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="timetable_id" class="fw-semibold">Timetable:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-book"></i></div>
                                            <select class="form-select @error('timetable_id') is-invalid @enderror"
                                                    id="timetable_id" name="timetable_id" required>
                                                <option value="">Select Timetable</option>
                                                @foreach($timetables as $timetable)
                                                    <option value="{{ $timetable->id }}"
                                                            {{ old('timetable_id', $slot->timetable_id ?? '') == $timetable->id ? 'selected' : '' }}>
                                                        {{ $timetable->name }} ({{ $timetable->class?->name }} - {{ $timetable->section?->name }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('timetable_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="day_of_week" class="fw-semibold">Day of Week:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-calendar"></i></div>
                                            <select class="form-select @error('day_of_week') is-invalid @enderror"
                                                    id="day_of_week" name="day_of_week" required>
                                                <option value="">Select Day</option>
                                                @foreach($daysOfWeek as $dayNum => $dayName)
                                                    <option value="{{ $dayNum }}"
                                                            {{ old('day_of_week', $slot->day_of_week ?? '') == $dayNum ? 'selected' : '' }}>
                                                        {{ $dayName }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('day_of_week')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="period_id" class="fw-semibold">Period:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-clock"></i></div>
                                            <select class="form-select @error('period_id') is-invalid @enderror"
                                                    id="period_id" name="period_id" required>
                                                <option value="">Select Period</option>
                                                @foreach($periods as $period)
                                                    <option value="{{ $period->id }}"
                                                            {{ old('period_id', $slot->period_id ?? '') == $period->id ? 'selected' : '' }}>
                                                        {{ $period->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('period_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="subject_id" class="fw-semibold">Subject:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-book"></i></div>
                                            <select class="form-select @error('subject_id') is-invalid @enderror"
                                                    id="subject_id" name="subject_id" required>
                                                <option value="">Select Subject</option>
                                                @foreach($subjects as $subject)
                                                    <option value="{{ $subject->id }}"
                                                            {{ old('subject_id', $slot->subject_id ?? '') == $subject->id ? 'selected' : '' }}>
                                                        {{ $subject->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('subject_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="staff_id" class="fw-semibold">Staff:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-user"></i></div>
                                            <select class="form-select @error('staff_id') is-invalid @enderror"
                                                    id="staff_id" name="staff_id" required>
                                                <option value="">Select Staff</option>
                                                @foreach($staff as $s)
                                                    <option value="{{ $s->id }}"
                                                            {{ old('staff_id', $slot->staff_id ?? '') == $s->id ? 'selected' : '' }}>
                                                        {{ $s->first_name }} {{ $s->last_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('staff_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="room_id" class="fw-semibold">Room:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-home"></i></div>
                                            <select class="form-select @error('room_id') is-invalid @enderror"
                                                    id="room_id" name="room_id" required>
                                                <option value="">Select Room</option>
                                                @foreach($rooms as $room)
                                                    <option value="{{ $room->id }}"
                                                            {{ old('room_id', $slot->room_id ?? '') == $room->id ? 'selected' : '' }}>
                                                        {{ $room->name }} ({{ ucfirst($room->type) }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('room_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
