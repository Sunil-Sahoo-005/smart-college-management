@extends('admin_layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Academics.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">
            <div class="content-area-header bg-white sticky-top">
                <h5 class="fw-bold mb-0 me-4">Create Practical Timetable</h5>
                <div class="page-header-right ms-auto">
                    <a href="{{ route('practical_timetables.index') }}" class="btn btn-secondary">Back</a>
                </div>
            </div>

            <div class="content-area-body">
                @include('elements.flash')

                <form method="POST" action="{{ route('practical_timetables.store') }}">
                    @csrf

                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Practical Group</label>
                                    <select name="practical_group_id" class="form-select" required>
                                        <option value="">Select group</option>
                                        @foreach($practicalGroups as $group)
                                            <option value="{{ $group->id }}">{{ $group->name }} @if($group->groupMaster) - {{ $group->groupMaster->name }} @endif</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Timetable Name</label>
                                    <input type="text" name="name" class="form-control" placeholder="Optional name">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Slots</h6>
                                <button type="button" id="addSlotBtn" class="btn btn-sm btn-outline-primary">Add Slot</button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-sm align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Day</th>
                                            <th>Period</th>
                                            <th>Room</th>
                                            <th>Staff</th>
                                            <th>Subject</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="slotRows"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button class="btn btn-primary">Save Practical Timetable</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<script>
window.__periods = @json($periods);
window.__rooms = @json($rooms);
window.__staff = @json($staff);
window.__subjects = @json($subjects);
</script>
@endpush

@push('scripts')
<script>
const dayOptions = [
    [1, 'Monday'], [2, 'Tuesday'], [3, 'Wednesday'],
    [4, 'Thursday'], [5, 'Friday'], [6, 'Saturday']
];

function buildSelect(name, options, placeholder = 'Select') {
    return `<select name="${name}" class="form-select" required>
        <option value="">${placeholder}</option>
        ${options.map(opt => `<option value="${opt.id}">${opt.label}</option>`).join('')}
    </select>`;
}

function slotRowHtml(index) {
    return `
    <tr>
        <td>
            <select name="slots[${index}][day_of_week]" class="form-select" required>
                <option value="">Select day</option>
                ${dayOptions.map(([value, label]) => `<option value="${value}">${label}</option>`).join('')}
            </select>
        </td>
        <td>
            <select name="slots[${index}][attendance_period_id]" class="form-select" required>
                <option value="">Select period</option>
                ${window.__periods.map(p => `<option value="${p.id}">${p.name} (${p.start_time} - ${p.end_time})</option>`).join('')}
            </select>
        </td>
        <td>
            <select name="slots[${index}][room_id]" class="form-select" required>
                <option value="">Select room</option>
                ${window.__rooms.map(r => `<option value="${r.id}">${r.name}</option>`).join('')}
            </select>
        </td>
        <td>
            <select name="slots[${index}][staff_id]" class="form-select" required>
                <option value="">Select staff</option>
                ${window.__staff.map(s => `<option value="${s.id}">${s.first_name} ${s.last_name}</option>`).join('')}
            </select>
        </td>
        <td>
            <select name="slots[${index}][subject_id]" class="form-select" required>
                <option value="">Select practical subject</option>
                ${window.__subjects.map(s => `<option value="${s.id}">${s.name}</option>`).join('')}
            </select>
        </td>
        <td><button type="button" class="btn btn-sm btn-outline-danger remove-slot">Remove</button></td>
    </tr>`;
}

const tbody = document.getElementById('slotRows');
let slotIndex = 0;

function addSlot() {
    tbody.insertAdjacentHTML('beforeend', slotRowHtml(slotIndex++));
}

document.getElementById('addSlotBtn').addEventListener('click', addSlot);
tbody.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-slot')) {
        e.target.closest('tr').remove();
    }
});

addSlot();
</script>
@endpush
@endsection
