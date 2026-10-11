@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <div>
                        <h5 class="fw-bold mb-0">Practical Master Timetable</h5>
                        <small class="text-muted">Add practical slots here and view all group slots day-wise by
                            subject.</small>
                    </div>
                    <div class="page-header-right ms-auto">
                        <a href="{{ route('practical_timetables.index') }}" class="btn btn-secondary me-2">Back</a>
                    </div>
                </div>

                <div class="content-area-body">
                    @include('elements.flash')

                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                <div>
                                    <h6 class="mb-1">Add Practical Timetable</h6>
                                    <small class="text-muted">Choose the group and fill slots right from this page.</small>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('practical_timetables.store') }}">
                                @csrf
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-4">
                                        <label class="form-label">Practical Group</label>
                                        <select name="practical_group_id" class="form-select" required>
                                            <option value="">Select practical group</option>
                                            @foreach ($practicalGroups as $group)
                                                @php
                                                    $groupLabel = trim(
                                                        ($group->name ?? 'Group') .
                                                            ' • ' .
                                                            ($group->groupMaster->name ?? 'No master') .
                                                            ' • ' .
                                                            ($group->course->name ?? '-') .
                                                            ' • ' .
                                                            ($group->stream->name ?? '-'),
                                                    );
                                                @endphp
                                                <option value="{{ $group->id }}">
                                                    {{ $groupLabel }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Timetable Name</label>
                                        <input type="text" name="name" class="form-control"
                                            placeholder="Optional label">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Practical Subject</label>
                                        <select name="subject_id" class="form-select" id="subjectIdForAdd" required>
                                            <option value="">Select practical subject</option>
                                            @foreach ($subjects as $subject)
                                                <option value="{{ $subject->id }}"
                                                    {{ (string) $selectedSubjectId === (string) $subject->id ? 'selected' : '' }}>
                                                    {{ $subject->name }} @if ($subject->code)
                                                        ({{ $subject->code }})
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0">Slots</h6>
                                        <button type="button" id="addSlotBtn" class="btn btn-sm btn-outline-primary">Add
                                            Slot</button>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-sm align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width:12%">Day</th>
                                                    <th style="width:18%">Period</th>
                                                    <th style="width:20%">Room</th>
                                                    <th style="width:20%">Staff</th>
                                                    <th>Subject</th>
                                                    <th style="width:8%"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="slotRows"></tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <button class="btn btn-primary">Save Practical Timetable</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div id="masterResults">
                    @if ($selectedSubjectId)
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                    <h6 class="mb-0">Day-wise slots for selected subject</h6>
                                    <span class="badge bg-primary">{{ $slots->count() }} slot(s)</span>
                                </div>

                                @php
                                    $dayLabels = [
                                        1 => 'Monday',
                                        2 => 'Tuesday',
                                        3 => 'Wednesday',
                                        4 => 'Thursday',
                                        5 => 'Friday',
                                        6 => 'Saturday',
                                    ];
                                @endphp

                                @forelse($dayLabels as $dayNo => $dayLabel)
                                    @php $daySlots = $groupedByDay->get($dayNo, collect()); @endphp
                                    <div class="mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="mb-0">{{ $dayLabel }}</h6>
                                            <small class="text-muted">{{ $daySlots->count() }} slot(s)</small>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-sm align-middle mb-0">
                                                <thead class="table-light">
                                                <tr>
                                                        <th style="width:14%">Period</th>
                                                        <th style="width:18%">Group</th>
                                                        <th style="width:18%">Room</th>
                                                        <th style="width:18%">Staff</th>
                                                        <th>Subject</th>
                                                        <th style="width:90px"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($daySlots as $slot)
                                                        <tr>
                                                            <td>{{ $slot->period->name ?? '-' }}</td>
                                                            <td>
                                                                <div class="fw-semibold">
                                                                    {{ $slot->timetable->practicalGroup->name ?? '-' }}
                                                                </div>
                                                                @if ($slot->timetable?->practicalGroup?->groupMaster)
                                                                    <div class="text-muted small">
                                                                        {{ $slot->timetable->practicalGroup->groupMaster->name }}
                                                                        @if ($slot->timetable?->practicalGroup?->groupMaster?->strength)
                                                                            • Strength:
                                                                            {{ $slot->timetable->practicalGroup->groupMaster->strength }}
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                                @if ($slot->timetable?->practicalGroup?->course || $slot->timetable?->practicalGroup?->stream)
                                                                    <div class="text-muted small">
                                                                        {{ $slot->timetable->practicalGroup->course->name ?? '-' }}
                                                                        •
                                                                        {{ $slot->timetable->practicalGroup->stream->name ?? '-' }}
                                                                    </div>
                                                                @endif
                                                            </td>
                                                            <td>{{ $slot->room->name ?? '-' }}</td>
                                                            <td>{{ trim(($slot->staff->first_name ?? '') . ' ' . ($slot->staff->last_name ?? '')) ?: '-' }}
                                                            </td>
                                                            <td>{{ $slot->subject->name ?? '-' }}</td>
                                                            <td class="text-end">
                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-primary me-1 edit-slot-btn"
                                                                    data-slot-id="{{ $slot->id }}"
                                                                    data-day="{{ $slot->day_of_week }}"
                                                                    data-period="{{ $slot->attendance_period_id }}"
                                                                    data-room="{{ $slot->room_id }}"
                                                                    data-staff="{{ $slot->staff_id }}"
                                                                    data-subject="{{ $slot->subject_id }}">
                                                                    Edit
                                                                </button>
                                                                <form method="POST"
                                                                    action="{{ route('practical_timetable_slots.destroy', $slot) }}"
                                                                    class="d-inline"
                                                                    onsubmit="return confirm('Delete this practical slot?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="btn btn-sm btn-outline-danger">
                                                                        Del
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="6" class="text-center text-muted py-3">No slots
                                                                on this day.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @empty
                                    <div class="alert alert-info mb-0">No timetable data found for the selected subject.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @else
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <div class="mb-3">
                                    <i class="feather-calendar" style="font-size:34px; color:#94a3b8;"></i>
                                </div>
                                <h6 class="mb-2">Select a practical subject to view timetable slots</h6>
                                <p class="text-muted mb-0">
                                    Choose a subject above to see all group-wise slots arranged day by day.
                                </p>
                            </div>
                        </div>
                    @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


    <style>
        .slot-remove-btn {
            border: 0;
            background: transparent;
            color: #dc2626;
            font-size: 16px;
        }

        .slot-remove-btn:hover {
            color: #b91c1c;
        }
    </style>



    <script>
        const periods = @json($periods);
        const rooms = @json($rooms);
        const staffMembers = @json($staff);
        const practicalSubjects = @json($subjects);
        const days = [
            [1, 'Monday'],
            [2, 'Tuesday'],
            [3, 'Wednesday'],
            [4, 'Thursday'],
            [5, 'Friday'],
            [6, 'Saturday'],
        ];

        function optionHtml(items, labelFn, placeholder) {
            return `<option value="">${placeholder}</option>` + items.map(item =>
                `<option value="${item.id}">${labelFn(item)}</option>`).join('');
        }

        function slotRow(index) {
            return `
        <tr>
            <td>
                <select name="slots[${index}][day_of_week]" class="form-select form-select-sm" required>
                    <option value="">Day</option>
                    ${days.map(([value, label]) => `<option value="${value}">${label}</option>`).join('')}
                </select>
            </td>
            <td>
                <select name="slots[${index}][attendance_period_id]" class="form-select form-select-sm" required>
                    <option value="">Period</option>
                    ${periods.map(period => `<option value="${period.id}">${period.name} (${period.start_time} - ${period.end_time})</option>`).join('')}
                </select>
            </td>
            <td>
                <select name="slots[${index}][room_id]" class="form-select form-select-sm" required>
                    <option value="">Room</option>
                    ${rooms.map(room => `<option value="${room.id}">${room.name}</option>`).join('')}
                </select>
            </td>
            <td>
                <select name="slots[${index}][staff_id]" class="form-select form-select-sm" required>
                    <option value="">Staff</option>
                    ${staffMembers.map(staff => `<option value="${staff.id}">${staff.first_name} ${staff.last_name}</option>`).join('')}
                </select>
            </td>
            <td>
                <select name="slots[${index}][subject_id]" class="form-select form-select-sm" required>
                    <option value="">Subject</option>
                    ${practicalSubjects.map(subject => `<option value="${subject.id}">${subject.name}</option>`).join('')}
                </select>
            </td>
            <td class="text-end">
                <button type="button" class="slot-remove-btn" title="Remove slot"><i class="feather-trash-2"></i></button>
            </td>
        </tr>
    `;
        }

        const slotRows = document.getElementById('slotRows');
        const masterResults = document.getElementById('masterResults');
        let slotIndex = 0;

        function addSlot() {
            slotRows.insertAdjacentHTML('beforeend', slotRow(slotIndex++));
        }

        function renderMasterResults(payload) {
            if (!payload || !payload.selectedSubjectId) {
                masterResults.innerHTML = `
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <div class="mb-3">
                                <i class="feather-calendar" style="font-size:34px; color:#94a3b8;"></i>
                            </div>
                            <h6 class="mb-2">Select a practical subject to view timetable slots</h6>
                            <p class="text-muted mb-0">Choose a subject above to see all group-wise slots arranged day by day.</p>
                        </div>
                    </div>`;
                return;
            }

            const dayLabels = {
                1: 'Monday',
                2: 'Tuesday',
                3: 'Wednesday',
                4: 'Thursday',
                5: 'Friday',
                6: 'Saturday',
            };

            const grouped = payload.slots.reduce((carry, slot) => {
                (carry[slot.day_of_week] ||= []).push(slot);
                return carry;
            }, {});

            const sections = Object.entries(dayLabels).map(([dayNo, dayLabel]) => {
                const daySlots = grouped[dayNo] || [];
            const rows = daySlots.length
                                ? daySlots.map(slot => `
                        <tr>
                            <td>${slot.period}</td>
                            <td>
                                <div class="fw-semibold">${slot.group_name}</div>
                                ${slot.group_master ? `<div class="text-muted small">${slot.group_master}${slot.strength ? ` • Strength: ${slot.strength}` : ''}</div>` : ''}
                                ${(slot.course || slot.stream) ? `<div class="text-muted small">${slot.course ?? '-'} • ${slot.stream ?? '-'}</div>` : ''}
                            </td>
                            <td>${slot.room}</td>
                            <td>${slot.staff}</td>
                            <td>${slot.subject}</td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-primary me-1 edit-slot-btn"
                                    data-slot-id="${slot.id}"
                                    data-day="${slot.day_of_week}"
                                    data-period="${slot.attendance_period_id}"
                                    data-room="${slot.room_id}"
                                    data-staff="${slot.staff_id}"
                                    data-subject="${slot.subject_id}">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger delete-slot-btn"
                                    data-slot-id="${slot.id}">
                                    Del
                                </button>
                            </td>
                        </tr>`).join('')
                    : `<tr><td colspan="6" class="text-center text-muted py-3">No slots on this day.</td></tr>`;

                return `
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">${dayLabel}</h6>
                            <small class="text-muted">${daySlots.length} slot(s)</small>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:14%">Period</th>
                                        <th style="width:18%">Group</th>
                                        <th style="width:18%">Room</th>
                                        <th style="width:18%">Staff</th>
                                        <th>Subject</th>
                                        <th style="width:90px"></th>
                                    </tr>
                                </thead>
                                <tbody>${rows}</tbody>
                            </table>
                        </div>
                    </div>`;
            }).join('');

            masterResults.innerHTML = `
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <h6 class="mb-0">Day-wise slots for selected subject</h6>
                            <span class="badge bg-primary">${payload.slots.length} slot(s)</span>
                        </div>
                        ${sections || '<div class="alert alert-info mb-0">No timetable data found for the selected subject.</div>'}
                    </div>
                </div>`;
        }

        document.getElementById('subjectIdForAdd').addEventListener('change', function() {
            if (!this.value) {
                renderMasterResults(null);
                return;
            }

            const url = new URL(window.location.href);
            url.searchParams.set('filter_subject_id', this.value);

            fetch(url.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            })
                .then(response => response.json())
                .then(renderMasterResults)
                .catch(() => {
                    masterResults.innerHTML = '<div class="alert alert-danger">Failed to load timetable. Please try again.</div>';
                });
        });

        const slotEditModalHtml = `
            <div class="modal fade" id="slotEditModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Practical Slot</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form id="slotEditForm" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Day</label>
                                        <select name="day_of_week" class="form-select" id="editDay"></select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Period</label>
                                        <select name="attendance_period_id" class="form-select" id="editPeriod"></select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Room</label>
                                        <select name="room_id" class="form-select" id="editRoom"></select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Staff</label>
                                        <select name="staff_id" class="form-select" id="editStaff"></select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Subject</label>
                                        <select name="subject_id" class="form-select" id="editSubject"></select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>`;
        document.body.insertAdjacentHTML('beforeend', slotEditModalHtml);
        const slotEditModal = new bootstrap.Modal(document.getElementById('slotEditModal'));
        const editForm = document.getElementById('slotEditForm');
        const editDay = document.getElementById('editDay');
        const editPeriod = document.getElementById('editPeriod');
        const editRoom = document.getElementById('editRoom');
        const editStaff = document.getElementById('editStaff');
        const editSubject = document.getElementById('editSubject');

        editDay.innerHTML = days.map(([value, label]) => `<option value="${value}">${label}</option>`).join('');
        editPeriod.innerHTML = `<option value="">Select Period</option>` + periods.map(period => `<option value="${period.id}">${period.name}</option>`).join('');
        editRoom.innerHTML = `<option value="">Select Room</option>` + rooms.map(room => `<option value="${room.id}">${room.name}</option>`).join('');
        editStaff.innerHTML = `<option value="">Select Staff</option>` + staffMembers.map(staff => `<option value="${staff.id}">${staff.first_name} ${staff.last_name}</option>`).join('');
        editSubject.innerHTML = `<option value="">Select Subject</option>` + practicalSubjects.map(subject => `<option value="${subject.id}">${subject.name}</option>`).join('');

        function bindEditButtons(root = document) {
            root.querySelectorAll('.edit-slot-btn').forEach(btn => {
                btn.onclick = () => {
                    const slotId = btn.dataset.slotId;
                    editForm.action = `{{ url('/practical-timetable-slots') }}/${slotId}`;
                    editDay.value = btn.dataset.day || '';
                    editPeriod.value = btn.dataset.period || '';
                    editRoom.value = btn.dataset.room || '';
                    editStaff.value = btn.dataset.staff || '';
                    editSubject.value = btn.dataset.subject || '';
                    slotEditModal.show();
                };
            });
        }

        bindEditButtons();
        const observer = new MutationObserver(() => bindEditButtons());
        observer.observe(masterResults, { childList: true, subtree: true });

        function getCsrfToken() {
            return document.querySelector('input[name="_token"]')?.value || '';
        }

        document.addEventListener('click', function(event) {
            const deleteBtn = event.target.closest('.delete-slot-btn');
            if (!deleteBtn) return;

            if (!confirm('Delete this practical slot?')) return;

            fetch(`{{ url('/practical-timetable-slots') }}/${deleteBtn.dataset.slotId}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ _method: 'DELETE' }),
            })
                .then(response => {
                    if (!response.ok) throw new Error('Failed');
                    return response.text();
                })
                .then(() => window.location.reload())
                .catch(() => alert('Could not delete slot. Please try again.'));
        });

        document.getElementById('addSlotBtn').addEventListener('click', addSlot);
        slotRows.addEventListener('click', function(event) {
            if (event.target.closest('.slot-remove-btn')) {
                event.target.closest('tr').remove();
            }
        });

        addSlot();
    </script>

@endsection
