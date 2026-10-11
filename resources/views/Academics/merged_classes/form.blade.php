@extends('admin_layout')

@section('content')
    @php
        $editing = !empty($mergedSchedule?->id);
        $formCombinations = old('class_combinations', $mergedSchedule?->class_combinations ?? [['course_id' => '', 'stream_id' => '', 'class_id' => '', 'section_id' => ''], ['course_id' => '', 'stream_id' => '', 'class_id' => '', 'section_id' => '']]);
        $formCombinations = collect($formCombinations)->map(function ($combo) {
            return [
                'course_id' => is_array($combo) ? ($combo['course_id'] ?? '') : '',
                'stream_id' => is_array($combo) ? ($combo['stream_id'] ?? '') : '',
                'class_id' => is_array($combo) ? ($combo['class_id'] ?? '') : '',
                'section_id' => is_array($combo) ? ($combo['section_id'] ?? '') : '',
            ];
        })->values()->all();
        if (count($formCombinations) < 2) {
            $formCombinations[] = ['course_id' => '', 'stream_id' => '', 'class_id' => '', 'section_id' => ''];
        }
    @endphp

    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <div>
                        <h5 class="fw-bold mb-1">{{ $editing ? 'Edit Merged Class' : 'Create Merged Class' }}</h5>
                        <p class="text-muted mb-0">Use this only for exception cases where multiple classes are handled together in one room by one staff member.</p>
                    </div>
                    <div class="page-header-right ms-auto">
                        <a href="{{ route('merged-classes.index') }}" class="btn btn-secondary">
                            Back to List
                        </a>
                    </div>
                </div>

                <div class="content-area-body">
                    @include('elements.flash')

                    <form id="mergedClassForm"
                        action="{{ $editing ? route('merged-classes.update', $mergedSchedule->id) : route('merged-classes.store') }}"
                        method="POST">
                        @csrf
                        @if ($editing)
                            @method('PUT')
                        @endif

                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body p-4 p-lg-5">
                                <div class="exception-banner mb-4">
                                    <div>
                                        <span class="exception-kicker">Exception Workflow</span>
                                        <h4 class="mb-2">One combined session for multiple classes</h4>
                                        <p class="mb-0 text-muted">This merged class sits outside the normal timetable. We still check staff and room availability against both merged schedules and the regular timetable.</p>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        {{ $editing ? 'Update Schedule' : 'Create Schedule' }}
                                    </button>
                                </div>

                                <div class="row g-4 mb-4">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Schedule Date <span class="text-danger">*</span></label>
                                        <input type="date" name="schedule_date"
                                            value="{{ old('schedule_date', optional($mergedSchedule?->schedule_date)->format('Y-m-d')) }}"
                                            class="form-control @error('schedule_date') is-invalid @enderror" required>
                                        @error('schedule_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Period <span class="text-danger">*</span></label>
                                        <select name="period_id" class="form-select @error('period_id') is-invalid @enderror" required>
                                            <option value="">Select Period</option>
                                            @foreach ($periods as $period)
                                                <option value="{{ $period->id }}" {{ old('period_id', $mergedSchedule?->period_id) == $period->id ? 'selected' : '' }}>
                                                    {{ $period->name }} ({{ $period->start_time }} - {{ $period->end_time }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('period_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Current Academic Year</label>
                                        <input type="text" class="form-control" value="{{ $currentAcademicYearId ?: 'Not found' }}" disabled>
                                        <small class="text-muted">The conflict check uses the current academic year for regular timetable matching.</small>
                                    </div>
                                </div>

                                @error('class_combinations')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror

                                <div class="combination-shell mb-4">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                                        <div>
                                            <h6 class="fw-bold mb-1">Class Combinations</h6>
                                            <p class="text-muted mb-0">Add every course, branch, class, and section that joins this same merged session.</p>
                                        </div>
                                        <button type="button" class="btn btn-success btn-sm" onclick="addClassCombination()">
                                            <i class="feather-plus me-1"></i>Add Combination
                                        </button>
                                    </div>

                                    <div id="class-combinations-container" class="d-grid gap-3">
                                        @foreach ($formCombinations as $index => $combo)
                                            <div class="combination-group card border-0 shadow-sm" data-index="{{ $index }}">
                                                <div class="card-body p-3 p-lg-4">
                                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                                        <div class="combination-title">Combination {{ $index + 1 }}</div>
                                                        <button type="button" class="btn btn-outline-danger btn-sm {{ $index < 2 ? 'd-none' : '' }}" onclick="removeCombination(this)">
                                                            <i class="feather-trash-2 me-1"></i>Remove
                                                        </button>
                                                    </div>

                                                    <div class="row g-3">
                                                        <div class="col-lg-3">
                                                            <label class="form-label">Course</label>
                                                            <select name="class_combinations[{{ $index }}][course_id]"
                                                                class="form-select course-select @error("class_combinations.$index.course_id") is-invalid @enderror"
                                                                data-selected="{{ old("class_combinations.$index.course_id", $combo['course_id'] ?? '') }}"
                                                                onchange="loadStreams(this)" required>
                                                                <option value="">Select Course</option>
                                                                @foreach ($courses as $course)
                                                                    <option value="{{ $course->id }}" {{ old("class_combinations.$index.course_id", $combo['course_id'] ?? '') == $course->id ? 'selected' : '' }}>
                                                                        {{ $course->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error("class_combinations.$index.course_id")
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="col-lg-3">
                                                            <label class="form-label">Branch</label>
                                                            <select name="class_combinations[{{ $index }}][stream_id]"
                                                                class="form-select stream-select @error("class_combinations.$index.stream_id") is-invalid @enderror"
                                                                data-selected="{{ old("class_combinations.$index.stream_id", $combo['stream_id'] ?? '') }}"
                                                                onchange="loadClasses(this)" required>
                                                                <option value="">Select Branch</option>
                                                            </select>
                                                            @error("class_combinations.$index.stream_id")
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="col-lg-3">
                                                            <label class="form-label">Class</label>
                                                            <select name="class_combinations[{{ $index }}][class_id]"
                                                                class="form-select class-select @error("class_combinations.$index.class_id") is-invalid @enderror"
                                                                data-selected="{{ old("class_combinations.$index.class_id", $combo['class_id'] ?? '') }}"
                                                                onchange="loadSections(this)" required disabled>
                                                                <option value="">Select Class</option>
                                                            </select>
                                                            @error("class_combinations.$index.class_id")
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="col-lg-3">
                                                            <label class="form-label">Section</label>
                                                            <select name="class_combinations[{{ $index }}][section_id]"
                                                                class="form-select section-select @error("class_combinations.$index.section_id") is-invalid @enderror"
                                                                data-selected="{{ old("class_combinations.$index.section_id", $combo['section_id'] ?? '') }}"
                                                                required disabled>
                                                                <option value="">Select Section</option>
                                                            </select>
                                                            @error("class_combinations.$index.section_id")
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="row g-4">
                                    <div class="col-lg-4">
                                        <label class="form-label fw-semibold">Subject <span class="text-danger">*</span></label>
                                        <select name="subject_id" class="form-select @error('subject_id') is-invalid @enderror" required>
                                            <option value="">Select Subject</option>
                                            @foreach ($subjects as $subject)
                                                <option value="{{ $subject->id }}" {{ old('subject_id', $mergedSchedule?->subject_id) == $subject->id ? 'selected' : '' }}>
                                                    {{ $subject->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('subject_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-lg-4">
                                        <label class="form-label fw-semibold">Staff <span class="text-danger">*</span></label>
                                        <select name="staff_id" class="form-select @error('staff_id') is-invalid @enderror" required>
                                            <option value="">Select Staff</option>
                                            @foreach ($staff as $member)
                                                <option value="{{ $member->id }}" {{ old('staff_id', $mergedSchedule?->staff_id) == $member->id ? 'selected' : '' }}>
                                                    {{ $member->first_name }} {{ $member->last_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('staff_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-lg-4">
                                        <label class="form-label fw-semibold">Room <span class="text-danger">*</span></label>
                                        <select name="room_id" class="form-select @error('room_id') is-invalid @enderror" required>
                                            <option value="">Select Room</option>
                                            @foreach ($rooms as $room)
                                                <option value="{{ $room->id }}" {{ old('room_id', $mergedSchedule?->room_id) == $room->id ? 'selected' : '' }}>
                                                    {{ $room->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('room_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <template id="combination-template">
        <div class="combination-group card border-0 shadow-sm" data-index="__INDEX__">
            <div class="card-body p-3 p-lg-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="combination-title">Combination __NUMBER__</div>
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeCombination(this)">
                        <i class="feather-trash-2 me-1"></i>Remove
                    </button>
                </div>

                <div class="row g-3">
                    <div class="col-lg-3">
                        <label class="form-label">Course</label>
                        <select name="class_combinations[__INDEX__][course_id]" class="form-select course-select" data-selected="" onchange="loadStreams(this)" required>
                            <option value="">Select Course</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label">Branch</label>
                        <select name="class_combinations[__INDEX__][stream_id]" class="form-select stream-select" data-selected="" onchange="loadClasses(this)" required disabled>
                            <option value="">Select Branch</option>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label">Class</label>
                        <select name="class_combinations[__INDEX__][class_id]" class="form-select class-select" data-selected="" onchange="loadSections(this)" required disabled>
                            <option value="">Select Class</option>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label">Section</label>
                        <select name="class_combinations[__INDEX__][section_id]" class="form-select section-select" data-selected="" required disabled>
                            <option value="">Select Section</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <style>
        .exception-banner {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            padding: 1.5rem;
            border-radius: 1.25rem;
            border: 1px solid rgba(13, 110, 253, 0.12);
            background: linear-gradient(135deg, #f8fbff 0%, #eef6ff 55%, #fdfefe 100%);
        }

        .exception-kicker {
            display: inline-block;
            margin-bottom: 0.5rem;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #0d6efd;
        }

        .combination-shell {
            padding: 1.25rem;
            border-radius: 1.25rem;
            background: #f8fafc;
            border: 1px solid rgba(15, 23, 42, 0.08);
        }

        .combination-group {
            overflow: hidden;
        }

        .combination-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #1e293b;
        }

        @media (max-width: 767.98px) {
            .exception-banner {
                flex-direction: column;
            }
        }
    </style>

    <script>
        let combinationIndex = {{ count($formCombinations) }};
        const routes = {
            streams: "{{ route('admin.timetables.streams', ':course') }}",
            classes: "{{ route('admin.timetables.classes', ['courseId' => ':course', 'streamId' => ':stream']) }}",
            sections: "{{ route('admin.timetables.sections', ['courseId' => ':course', 'streamId' => ':stream', 'classId' => ':class']) }}",
        };

        function addClassCombination() {
            const template = document.getElementById('combination-template').innerHTML
                .replaceAll('__INDEX__', combinationIndex)
                .replaceAll('__NUMBER__', combinationIndex + 1);

            document.getElementById('class-combinations-container').insertAdjacentHTML('beforeend', template);
            combinationIndex++;
        }

        function removeCombination(button) {
            const groups = document.querySelectorAll('.combination-group');
            if (groups.length <= 2) {
                return;
            }

            button.closest('.combination-group').remove();
            refreshCombinationTitles();
        }

        function refreshCombinationTitles() {
            document.querySelectorAll('.combination-group').forEach((group, index) => {
                const title = group.querySelector('.combination-title');
                if (title) {
                    title.textContent = `Combination ${index + 1}`;
                }
            });
        }

        function buildRoute(template, replacements) {
            let url = template;
            Object.entries(replacements).forEach(([key, value]) => {
                url = url.replace(`:${key}`, value).replace(`%3A${key}`, value);
            });
            return url;
        }

        function loadStreams(courseSelect) {
            const group = courseSelect.closest('.combination-group');
            const streamSelect = group.querySelector('.stream-select');
            const classSelect = group.querySelector('.class-select');
            const sectionSelect = group.querySelector('.section-select');
            const courseId = courseSelect.value;
            const selectedStreamId = streamSelect.dataset.selected || '';

            streamSelect.innerHTML = '<option value="">Select Branch</option>';
            classSelect.innerHTML = '<option value="">Select Class</option>';
            sectionSelect.innerHTML = '<option value="">Select Section</option>';
            streamSelect.disabled = true;
            classSelect.disabled = true;
            sectionSelect.disabled = true;

            if (!courseId) {
                return;
            }

            streamSelect.innerHTML = '<option value="">Loading branches...</option>';

            fetch(buildRoute(routes.streams, { course: courseId }))
                .then(response => response.json())
                .then(streams => {
                    streamSelect.innerHTML = '<option value="">Select Branch</option>';
                    streams.forEach(item => {
                        streamSelect.innerHTML += `<option value="${item.id}">${item.name}</option>`;
                    });
                    streamSelect.disabled = false;

                    if (selectedStreamId) {
                        streamSelect.value = selectedStreamId;
                        streamSelect.dataset.selected = '';
                        loadClasses(streamSelect);
                    }
                })
                .catch(() => {
                    streamSelect.innerHTML = '<option value="">Unable to load branches</option>';
                });
        }

        function loadClasses(streamSelect) {
            const group = streamSelect.closest('.combination-group');
            const courseSelect = group.querySelector('.course-select');
            const classSelect = group.querySelector('.class-select');
            const sectionSelect = group.querySelector('.section-select');
            const courseId = courseSelect.value;
            const streamId = streamSelect.value;
            const selectedClassId = classSelect.dataset.selected || '';

            classSelect.innerHTML = '<option value="">Select Class</option>';
            sectionSelect.innerHTML = '<option value="">Select Section</option>';
            sectionSelect.disabled = true;

            if (!courseId || !streamId) {
                classSelect.disabled = true;
                return;
            }

            classSelect.disabled = true;
            classSelect.innerHTML = '<option value="">Loading classes...</option>';

            fetch(buildRoute(routes.classes, { course: courseId, stream: streamId }))
                .then(response => response.json())
                .then(classes => {
                    classSelect.innerHTML = '<option value="">Select Class</option>';
                    classes.forEach(item => {
                        classSelect.innerHTML += `<option value="${item.id}">${item.name}</option>`;
                    });
                    classSelect.disabled = false;

                    if (selectedClassId) {
                        classSelect.value = selectedClassId;
                        classSelect.dataset.selected = '';
                        loadSections(classSelect);
                    }
                })
                .catch(() => {
                    classSelect.innerHTML = '<option value="">Unable to load classes</option>';
                });
        }

        function loadSections(classSelect) {
            const group = classSelect.closest('.combination-group');
            const courseSelect = group.querySelector('.course-select');
            const streamSelect = group.querySelector('.stream-select');
            const sectionSelect = group.querySelector('.section-select');
            const courseId = courseSelect.value;
            const streamId = streamSelect.value;
            const classId = classSelect.value;
            const selectedSectionId = sectionSelect.dataset.selected || '';

            sectionSelect.innerHTML = '<option value="">Select Section</option>';

            if (!courseId || !streamId || !classId) {
                sectionSelect.disabled = true;
                return;
            }

            sectionSelect.disabled = true;
            sectionSelect.innerHTML = '<option value="">Loading sections...</option>';

            fetch(buildRoute(routes.sections, { course: courseId, stream: streamId, class: classId }))
                .then(response => response.json())
                .then(sections => {
                    sectionSelect.innerHTML = '<option value="">Select Section</option>';
                    sections.forEach(item => {
                        sectionSelect.innerHTML += `<option value="${item.id}">${item.name}</option>`;
                    });
                    sectionSelect.disabled = false;

                    if (selectedSectionId) {
                        sectionSelect.value = selectedSectionId;
                        sectionSelect.dataset.selected = '';
                    }
                })
                .catch(() => {
                    sectionSelect.innerHTML = '<option value="">Unable to load sections</option>';
                });
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.combination-group').forEach(group => {
                const courseSelect = group.querySelector('.course-select');
                if (courseSelect && courseSelect.value) {
                    loadStreams(courseSelect);
                }
            });

            refreshCombinationTitles();
        });
    </script>
@endsection
