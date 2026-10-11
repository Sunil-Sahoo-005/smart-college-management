@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Timetable Slots</span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                            <a href="{{ route('admin.timetable_slots.create') }}" class="btn btn-primary">
                                <i class="feather-plus me-2"></i>
                                <span>Create Slot</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="content-area-body">
                    <div class="card stretch stretch-full">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover" id="timetableSlotList">
                                    <thead>
                                        <tr>
                                            <th>Timetable</th>
                                            <th>Day</th>
                                            <th>Period</th>
                                            <th>Subject</th>
                                            <th>Staff</th>
                                            <th>Room</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($slots as $slot)
                                            <tr class="single-item">
                                                <td>{{ $slot->timetable?->name ?? 'N/A' }}</td>
                                                <td>
                                                    @php
                                                        $days = [
                                                            0 => 'Sunday',
                                                            1 => 'Monday',
                                                            2 => 'Tuesday',
                                                            3 => 'Wednesday',
                                                            4 => 'Thursday',
                                                            5 => 'Friday',
                                                            6 => 'Saturday',
                                                        ];
                                                    @endphp
                                                    {{ $days[$slot->day_of_week] ?? 'N/A' }}
                                                </td>
                                                <td>{{ $slot->period?->name ?? 'N/A' }}</td>
                                                <td>{{ $slot->subject?->name ?? 'N/A' }}</td>
                                                <td>{{ $slot->staff?->first_name ?? '' }} {{ $slot->staff?->last_name ?? '' }}</td>
                                                <td>{{ $slot->room?->name ?? 'N/A' }}</td>
                                                <td>
                                                    <div class="hstack gap-2 justify-content-end">
                                                        <a href="{{ route('admin.timetable_slots.edit', $slot) }}"
                                                           class="avatar-text avatar-md">
                                                            <i class="feather-edit-3"></i>
                                                        </a>
                                                        <div class="dropdown">
                                                            <a href="javascript:void(0)" class="avatar-text avatar-md"
                                                               data-bs-toggle="dropdown" data-bs-offset="0,21">
                                                                <i class="feather-more-horizontal"></i>
                                                            </a>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <a class="dropdown-item"
                                                                       href="{{ route('admin.timetable_slots.edit', $slot) }}">
                                                                        <i class="feather-edit-3 me-3"></i>
                                                                        <span>Edit</span>
                                                                    </a>
                                                                </li>
                                                                <li class="dropdown-divider"></li>
                                                                <li>
                                                                    <form
                                                                        action="{{ route('admin.timetable_slots.destroy', $slot) }}"
                                                                        method="POST" style="display:inline;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="dropdown-item"
                                                                                onclick="return confirm('Are you sure?')">
                                                                            Delete
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">No timetable slots found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    {{ $slots->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
