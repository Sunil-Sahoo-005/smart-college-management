
@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Edit Timetable</span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <a href="{{ route('admin.timetables.show', $timetable) }}" class="btn btn-secondary">
                            View Timetable
                        </a>
                        <a href="{{ route('admin.timetables.index') }}" class="btn btn-outline-secondary">
                            Back to List
                        </a>
                    </div>
                </div>
@include('elements.flash')
                <div class="content-area-body">
                    <form method="POST" action="{{ route('admin.timetables.update', $timetable) }}">
                        @csrf
                        @method('PUT')

                        <!-- Basic Info -->
                        <div class="card stretch stretch-full mb-3">
                            <div class="card-body">
                                <h6 class="card-title">Basic Information</h6>
                                <div class="row">
                                    <!-- STEP 0: COURSE -->
                                    <div class="col-md-2">
                                        <label class="form-label">Course <span id="stream-loading" style="display:none;">🔄</span></label>
                                        <select name="course_id" id="course_id" class="form-select" required onchange="handleCourseChange()">
                                            <option value="">Select Course</option>
                                            @foreach ($courses as $course)
                                                <option value="{{ $course->id }}" 
                                                    data-is-lateral="{{ $course->is_lateral_entry ? '1' : '0' }}"
                                                    {{ old('course_id', $timetable->course_id) == $course->id ? 'selected' : '' }}>
                                                    {{ $course->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Entry Type (Conditional) -->
                                    <div class="col-md-2" id="entry_type_container" 
                                        style="display: {{ $timetable->course->is_lateral_entry ? 'block' : 'none' }};">
                                        <label class="form-label">Entry Type</label>
                                        <select name="entry_type" id="entry_type" class="form-select" onchange="filterSemesters()">
                                            <option value="regular" {{ old('entry_type', $timetable->entry_type) == 'regular' ? 'selected' : '' }}>Regular</option>
                                            <option value="lateral" {{ old('entry_type', $timetable->entry_type) == 'lateral' ? 'selected' : '' }}>Lateral</option>
                                            <option value="both" {{ old('entry_type', $timetable->entry_type) == 'both' ? 'selected' : '' }}>Regular & Lateral</option>
                                        </select>
                                    </div>

                                    <!-- STEP 1: STREAM -->
                                    <div class="col-md-2">
                                        <label class="form-label">Branch <span id="class-loading"
                                                style="display:none;">🔄</span></label>
                                        <select name="stream_id" id="stream_id" class="form-select" required disabled
                                            onchange="loadClasses()">
                                            <option value="">Select Branch</option>
                                        </select>
                                    </div>

                                    <!-- STEP 2: CLASS -->
                                    <div class="col-md-2">
                                        <label class="form-label">Semester <span id="subject-loading"
                                                style="display:none;">🔄</span></label>
                                        <select name="class_id" id="class_id" class="form-select" required disabled
                                            onchange="loadSubjects(); loadSections()">
                                            <option value="">Select Semester</option>
                                        </select>
                                    </div>

                                    <!-- STEP 3: SECTION -->
                                    <div class="col-md-2">
                                        <label class="form-label">Section</label>
                                        <select name="section_id" id="section_id" class="form-select" required disabled>
                                            <option value="">Select Section</option>
                                        </select>
                                    </div>

                                    <!-- Academic Year -->
                                    <div class="col-md-2">
                                        <label class="form-label">Academic Year</label>
                                        <select name="academic_year_ids[]" class="form-select select-multi" multiple required>
                                            @foreach ($academicYears as $year)
                                                @php
                                                    $selected = is_array(old('academic_year_ids')) 
                                                        ? in_array($year->id, old('academic_year_ids')) 
                                                        : $timetable->academicYears->contains($year->id);
                                                @endphp
                                                <option value="{{ $year->id }}" {{ $selected ? 'selected' : '' }}>
                                                    {{ $year->name }} {{ $year->is_current ? '(Current)' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>


                                </div>
                            </div>
                        </div>

                        <!-- Available Subjects Preview -->
                        <div class="card stretch stretch-full mb-3">
                            <div class="card-body">
                                <h6 class="card-title">Available Subjects</h6>
                                <div id="subjects-preview">
                                    <p class="text-muted">Select Stream → Class to see available subjects</p>
                                </div>
                            </div>
                        </div>

                        <!-- Timetable Grid -->
                        <div class="card stretch stretch-full mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="card-title mb-0">Weekly Schedule (Subjects: <span id="grid-subject-count">0</span>)</h6>
                                    <button type="button" class="btn btn-sm btn-primary" id="btn-add-period" onclick="addPeriodRow()" disabled>
                                        <i class="feather-plus me-1"></i> Add Period
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered timetable-grid" id="timetable-grid">
                                        <thead>
                                            <tr>
                                                <th style="min-width: 160px; background: #f8f9fa;">Period</th>
                                                @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                                                    <th style="min-width: 170px; background: #f8f9fa;">{{ $day }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody id="timetable-body">
                                            <!-- Grid content loaded via JS -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" id="save-btn">
                            Update Timetable
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .timetable-grid th {
            background: #f8f9fa !important;
            font-weight: 700 !important;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
            color: #6b7280;
            border-bottom: 2px solid #e5e7eb !important;
            text-align: center;
        }

        .slot-input-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            padding: 8px;
            background: #fcfcfc;
            border-radius: 8px;
            transition: all 0.2s;
        }
        
        .slot-input-group:hover {
            background: #f8fafc;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .slot-input-group select {
            font-size: 0.75rem;
            padding: 0.35rem 0.5rem;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }

        .slot-input-group select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
        }
        
        #timetable-grid td {
            vertical-align: top;
            padding: 8px;
            background: #fff;
        }

        .card-title {
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            font-weight: 700;
            color: #475569;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .card-title::before {
            content: '';
            display: inline-block;
            width: 4px;
            height: 16px;
            background: #2563eb;
            border-radius: 2px;
        }
    </style>
    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            min-height: 38px;
        }
        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: transparent;
            border: 1px solid #2563eb;
            color: #2563eb;
            border-radius: 4px;
            padding: 2px 6px;
            margin-top: 6px;
            font-size: 13px;
            font-weight: 500;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #2563eb;
            margin-right: 5px;
            border-right: none;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
            background-color: #eff6ff;
            color: #1d4ed8;
        }
    </style>
    @endpush

    @push('scripts')
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        const routes = {
        streams: "{{ route('admin.timetables.streams', ':id') }}",
        classes: "{{ route('admin.timetables.classes', ['courseId' => ':course', 'streamId' => ':stream']) }}",
        subjects: "{{ route('admin.timetables.subjects', ['courseId' => ':course', 'streamId' => ':stream', 'classId' => ':class']) }}",
        sections: "{{ route('admin.timetables.sections', ['courseId' => ':course', 'streamId' => ':stream', 'classId' => ':class']) }}"
    };
        let availableSubjects = [];
        let allAvailableClasses = [];
        let existingSlots = @json($existingSlots);
        let oldSlots = @json(old('slots', []));

        // Form repopulation data
        let oldValues = {
            stream_id: "{{ old('stream_id', $timetable->stream_id) }}",
            class_id: "{{ old('class_id', $timetable->class_id) }}",
            section_id: "{{ old('section_id', $timetable->section_id) }}",
            entry_type: "{{ old('entry_type', $timetable->entry_type) }}",
            slots: @json(old('slots', []))
        };

        $(document).ready(function() {
            // Initialize Select2
            $('.select-multi').select2({
                placeholder: "Select Academic Years",
                allowClear: true
            });

            if ($('#course_id').val()) {
                handleCourseChange(); // This will handle visibility and load streams
                if (oldValues.entry_type) {
                    $('#entry_type').val(oldValues.entry_type);
                }
            }
        });

        function buildRoute(template, replacements) {
            let url = template;

            Object.entries(replacements).forEach(([key, value]) => {
                url = url
                    .replace(`:${key}`, value)
                    .replace(`%3A${key}`, value);
            });

            return url;
        }

        function handleCourseChange() {
            const courseSelect = document.getElementById('course_id');
            const selectedOption = courseSelect.options[courseSelect.selectedIndex];
            const isLateral = selectedOption ? selectedOption.getAttribute('data-is-lateral') === '1' : false;
            const container = document.getElementById('entry_type_container');
            
            if (isLateral) {
                container.style.display = 'block';
            } else {
                container.style.display = 'none';
                document.getElementById('entry_type').value = 'regular';
            }
            
            loadStreams();
        }

        function filterSemesters() {
            const entryType = document.getElementById('entry_type').value;
            const classSelect = document.getElementById('class_id');
            const currentVal = classSelect.value;
            
            classSelect.innerHTML = '<option value="">Select Semester</option>';
            
            allAvailableClasses.forEach(cls => {
                const name = cls.name.toLowerCase();
                const isFirstOrSecond = name.includes('1st') || name.includes('2nd') || 
                                       name.includes('semester 1') || name.includes('semester 2') ||
                                       name.includes('sem 1') || name.includes('sem 2');
                
                if ((entryType === 'lateral' || entryType === 'both') && isFirstOrSecond) {
                    return; // Skip 1st and 2nd semesters for lateral entry
                }
                
                classSelect.innerHTML += `<option value="${cls.id}">${cls.name}</option>`;
            });
            
            if (Array.from(classSelect.options).some(opt => opt.value === currentVal)) {
                classSelect.value = currentVal;
            }
        }

        function loadStreams() {
            const courseId = document.getElementById('course_id').value;
            const streamSelect = document.getElementById('stream_id');
            const classSelect = document.getElementById('class_id');
            const sectionSelect = document.getElementById('section_id');

            if (!courseId) {
                streamSelect.innerHTML = '<option value="">Select Branch</option>';
                streamSelect.disabled = true;
                classSelect.innerHTML = '<option value="">Select Semester</option>';
                classSelect.disabled = true;
                sectionSelect.innerHTML = '<option value="">Select Section</option>';
                sectionSelect.disabled = true;
                updateSubjectsPreview([]);
                return;
            }

            document.getElementById('stream-loading').style.display = 'inline';

            fetch(routes.streams.replace(':id', courseId))
                .then(res => res.json())
                .then(data => {
                    streamSelect.innerHTML = '<option value="">Select Branch</option>';
                    data.forEach(stream => {
                        streamSelect.innerHTML += `<option value="${stream.id}">${stream.name}</option>`;
                    });
                    streamSelect.disabled = false;

                    if (oldValues.stream_id) {
                        streamSelect.value = oldValues.stream_id;
                        oldValues.stream_id = null; 
                        loadClasses();
                    }
                })
                .finally(() => {
                    document.getElementById('stream-loading').style.display = 'none';
                });
        }

        function loadClasses() {
            const courseId = document.getElementById('course_id').value;
            const streamId = document.getElementById('stream_id').value;
            const classSelect = document.getElementById('class_id');
            const sectionSelect = document.getElementById('section_id');

            if (!courseId || !streamId) {
                classSelect.innerHTML = '<option value="">Select Semester</option>';
                classSelect.disabled = true;
                sectionSelect.innerHTML = '<option value="">Select Section</option>';
                sectionSelect.disabled = true;
                updateSubjectsPreview([]);
                return;
            }

            document.getElementById('class-loading').style.display = 'inline';

            fetch(buildRoute(routes.classes, {
                course: courseId,
                stream: streamId
            }))
                .then(res => res.json())
                .then(data => {
                    allAvailableClasses = data;
                    filterSemesters();
                    classSelect.disabled = false;

                    if (oldValues.class_id) {
                        classSelect.value = oldValues.class_id;
                        oldValues.class_id = null;
                        loadSubjects();
                    }
                })
                .finally(() => {
                    document.getElementById('class-loading').style.display = 'none';
                });
        }

        function loadSubjects() {
            const courseId = document.getElementById('course_id').value;
            const classId = document.getElementById('class_id').value;
            const streamId = document.getElementById('stream_id').value;
            if (!courseId || !classId || !streamId) {
                updateSubjectsPreview([]);
                return;
            }

            document.getElementById('subject-loading').style.display = 'inline';

            fetch(buildRoute(routes.subjects, {
                course: courseId,
                stream: streamId,
                class: classId
            }))
                .then(res => res.json())
                .then(data => {
                    availableSubjects = data;
                    updateSubjectsPreview(data);
                    loadSections();
                    loadTimetableGrid(data);
                })
                .finally(() => {
                    document.getElementById('subject-loading').style.display = 'none';
                });
        }

        function loadSections() {
            const courseId = document.getElementById('course_id').value;
            const classId = document.getElementById('class_id').value;
            const streamId = document.getElementById('stream_id').value;
            const sectionSelect = document.getElementById('section_id');

            if (!courseId || !classId || !streamId) {
                sectionSelect.innerHTML = '<option value="">Select Section</option>';
                sectionSelect.disabled = true;
                return;
            }

            fetch(buildRoute(routes.sections, {
                course: courseId,
                stream: streamId,
                class: classId
            }))
                .then(res => res.json())
                .then(data => {
                    sectionSelect.innerHTML = '<option value="">Select Section</option>';
                    data.forEach(section => {
                        sectionSelect.innerHTML += `<option value="${section.id}">${section.name}</option>`;
                    });
                    sectionSelect.disabled = false;

                    if (oldValues.section_id) {
                        sectionSelect.value = oldValues.section_id;
                        oldValues.section_id = null;
                    }
                });
        }

        function updateSubjectsPreview(subjects) {
            if (subjects.length === 0) {
                document.getElementById('subjects-preview').innerHTML =
                    '<p class="text-muted">Select Stream → Class to see available subjects</p>';
                return;
            }

            document.getElementById('subjects-preview').innerHTML = `
                <div class="alert alert-info">
                    <strong>${subjects.length} subjects</strong> available:
                    ${subjects.map(s => `<span class="badge bg-primary">${s.name}${s.is_compulsory ? ' (C)' : ''}</span>`).join(' ')}
                </div>
            `;
        }

        let rowCount = 0;
        const periods = @json($periods);
        const staff = @json($staff);
        const rooms = @json($rooms);

        function loadTimetableGrid(subjects) {
            availableSubjects = subjects;
            document.getElementById('grid-subject-count').textContent = subjects.length;
            document.getElementById('timetable-body').innerHTML = '';
            document.getElementById('btn-add-period').disabled = subjects.length === 0;

            let slotsToLoad = oldValues.slots;
            let usingOld = false;

            if (Object.keys(slotsToLoad).length > 0) {
                usingOld = true;
            } else {
                slotsToLoad = existingSlots;
            }
            
            const hasSlots = slotsToLoad && (Array.isArray(slotsToLoad) ? slotsToLoad.length > 0 : Object.keys(slotsToLoad).length > 0);

            if (subjects.length > 0 && hasSlots) {
                let groupedSlots;
                if (Array.isArray(slotsToLoad)) {
                    groupedSlots = slotsToLoad.reduce((acc, slot) => {
                        if (!acc[slot.period_id]) acc[slot.period_id] = {};
                        acc[slot.period_id][slot.day_of_week] = slot;
                        return acc;
                    }, {});
                } else {
                    groupedSlots = slotsToLoad; // old('slots') is already grouped [period_id][day_of_week]
                }

                for (const [periodId, daySlots] of Object.entries(groupedSlots)) {
                    addPeriodRow(periodId, daySlots);
                }
                
                if (usingOld) {
                    oldValues.slots = {};
                } else {
                    existingSlots = [];
                }
            }
        }

        function addPeriodRow(presetPeriodId = null, presetDaySlots = {}) {
            if (availableSubjects.length === 0) return;
            
            const tbody = document.getElementById('timetable-body');
            const rowId = 'row-' + rowCount++;
            
            let tr = document.createElement('tr');
            tr.id = rowId;
            
            // Period selector cell
            let periodTd = document.createElement('td');
            let periodSelect = `<div class="d-flex align-items-center gap-2">
                <select class="form-select form-select-sm period-selector" onchange="updateRowNames('${rowId}', this.value)" style="min-width: 120px;">
                    <option value="">- Period -</option>
                    ${periods.map(p => `<option value="${p.id}" ${p.id == presetPeriodId ? 'selected' : ''}>${p.name}</option>`).join('')}
                </select>
                <button type="button" class="btn btn-sm btn-light-danger" onclick="document.getElementById('${rowId}').remove()" style="padding: 0.25rem 0.5rem;">
                    <i class="feather-trash-2"></i>
                </button>
            </div>`;
            periodTd.innerHTML = periodSelect;
            tr.appendChild(periodTd);
            
            // 6 days cells
            [1, 2, 3, 4, 5, 6].forEach(dayOfWeek => {
                let slot = presetDaySlots[dayOfWeek] || {};
                let td = document.createElement('td');
                td.innerHTML = `
                    <div class="slot-input-group">
                        <select data-day="${dayOfWeek}" data-type="subject_id" class="form-select form-select-sm" ${presetPeriodId ? `name="slots[${presetPeriodId}][${dayOfWeek}][subject_id]"` : ''}>
                            <option value="">- Subject -</option>
                            ${availableSubjects.map(subject => 
                                `<option value="${subject.id}" ${slot.subject_id == subject.id ? 'selected' : ''}>${subject.name}${subject.is_compulsory ? ' (C)' : ''}</option>`
                            ).join('')}
                        </select>
                        <select data-day="${dayOfWeek}" data-type="staff_id" class="form-select form-select-sm" ${presetPeriodId ? `name="slots[${presetPeriodId}][${dayOfWeek}][staff_id]"` : ''}>
                            <option value="">- Teacher -</option>
                            ${staff.map(s => `<option value="${s.id}" ${slot.staff_id == s.id ? 'selected' : ''}>${s.first_name} ${s.last_name}</option>`).join('')}
                        </select>
                        <select data-day="${dayOfWeek}" data-type="room_id" class="form-select form-select-sm" ${presetPeriodId ? `name="slots[${presetPeriodId}][${dayOfWeek}][room_id]"` : ''}>
                            <option value="">- Room -</option>
                            ${rooms.map(r => `<option value="${r.id}" ${slot.room_id == r.id ? 'selected' : ''}>${r.name}</option>`).join('')}
                        </select>
                    </div>
                `;
                tr.appendChild(td);
            });
            
            tbody.appendChild(tr);
        }

        function updateRowNames(rowId, periodId) {
            const row = document.getElementById(rowId);
            const selects = row.querySelectorAll('.slot-input-group select');
            
            selects.forEach(select => {
                if (!periodId) {
                    select.removeAttribute('name'); 
                } else {
                    const day = select.getAttribute('data-day');
                    const type = select.getAttribute('data-type');
                    select.name = `slots[${periodId}][${day}][${type}]`;
                }
            });
        }
    </script>
@endpush
@endsection