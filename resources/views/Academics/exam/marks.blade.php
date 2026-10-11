{{-- resources/views/Academics/exam/marks.blade.php --}}
@extends('admin_layout')
@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Academics.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            {{-- HEADER --}}
            <div class="content-area-header bg-white px-4 py-3 border-bottom shadow-sm"
                 style="position:sticky; top:0; z-index:200;">
                <div class="d-flex align-items-center gap-3 flex-wrap w-100">
                    <a href="{{ route('admin.exams.index') }}"
                       class="btn btn-sm btn-outline-secondary flex-shrink-0">
                        <i class="feather-arrow-left me-1"></i> Back
                    </a>
                    <div class="flex-shrink-0">
                        <h5 class="fw-bold mb-0 text-dark">{{ $exam->name }}</h5>
                        <small class="text-muted">Student Marks — Read Only</small>
                    </div>
                </div>
            </div>

            {{-- EXAM INFO BADGES --}}
            <div class="px-4 pt-4 pb-2 d-flex flex-wrap gap-2">
                <span class="badge rounded-pill"
                      style="background:#ede9fe;color:#5b21b6;font-size:.78rem;font-weight:600;padding:.45em .9em;">
                    <i class="feather-layers me-1" style="font-size:11px;"></i>
                    {{ $exam->examType?->name ?? 'N/A' }}
                </span>
                <span class="badge rounded-pill"
                      style="background:#e0f2fe;color:#075985;font-size:.78rem;font-weight:600;padding:.45em .9em;">
                    <i class="feather-users me-1" style="font-size:11px;"></i>
                    {{ $exam->class?->name ?? 'N/A' }} — {{ $exam->section?->name ?? 'N/A' }}
                </span>
                @if($exam->stream)
                <span class="badge rounded-pill"
                      style="background:#ecfdf5;color:#065f46;font-size:.78rem;font-weight:600;padding:.45em .9em;">
                    <i class="feather-git-branch me-1" style="font-size:11px;"></i>
                    {{ $exam->stream->name }}
                </span>
                @endif
                <span class="badge rounded-pill"
                      style="background:#fef3c7;color:#92400e;font-size:.78rem;font-weight:600;padding:.45em .9em;">
                    <i class="feather-book me-1" style="font-size:11px;"></i>
                    {{ $exam->subject?->name ?? 'N/A' }}
                </span>
                <span class="badge rounded-pill"
                      style="background:#f0fdf4;color:#166534;font-size:.78rem;font-weight:600;padding:.45em .9em;">
                    <i class="feather-calendar me-1" style="font-size:11px;"></i>
                    {{ \Carbon\Carbon::parse($exam->exam_date)->format('d M Y') }}
                </span>
                <span class="badge rounded-pill"
                      style="background:#fff1f2;color:#9f1239;font-size:.78rem;font-weight:600;padding:.45em .9em;">
                    <i class="feather-clock me-1" style="font-size:11px;"></i>
                    {{ $exam->start_time }} → {{ $exam->end_time }}
                </span>
                <span class="badge rounded-pill"
                      style="background:#f5f3ff;color:#4c1d95;font-size:.78rem;font-weight:600;padding:.45em .9em;">
                    <i class="feather-award me-1" style="font-size:11px;"></i>
                    Total: {{ $exam->total_marks }} &nbsp;|&nbsp; Pass: {{ $exam->pass_marks }}
                </span>
            </div>

            {{-- TABLE --}}
            <div class="content-area-body p-4 pt-2">
                @include('elements.flash')

                <div class="card stretch stretch-full">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="fw-semibold" style="width:50px;">#</th>
                                        <th class="fw-semibold">Roll No.</th>
                                        <th class="fw-semibold">Student Name</th>
                                        <th class="fw-semibold">Class</th>
                                        <th class="fw-semibold">Section</th>
                                        @if($exam->stream)
                                        <th class="fw-semibold">Branch</th>
                                        @endif
                                        <th class="fw-semibold">Subject</th>
                                        <th class="fw-semibold">Exam Date</th>
                                        <th class="fw-semibold">Time</th>
                                        <th class="fw-semibold text-center">Total Marks</th>
                                        <th class="fw-semibold text-center">Pass Marks</th>
                                        <th class="fw-semibold text-center">Marks Obtained</th>
                                        <th class="fw-semibold text-center">Status</th>
                                        <th class="fw-semibold">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($students as $index => $student)
                                        @php
                                            $mark           = $existingMarks[$student->id] ?? null;
                                            $marksObtained  = $mark?->marks_obtained;
                                            $hasMark        = !is_null($marksObtained);
                                            $passed         = $hasMark && $marksObtained >= $exam->pass_marks;
                                        @endphp
                                        <tr>
                                            <td class="text-muted" style="font-size:.8rem;">{{ $index + 1 }}</td>
                                            <td>
                                                <span class="fw-semibold" style="font-size:.85rem;">
                                                    {{ $student->roll_number ?? '—' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="fw-semibold" style="font-size:.875rem;">
                                                    {{ $student->first_name }} {{ $student->last_name }}
                                                </div>
                                                @if($student->admission_number)
                                                    <small class="text-muted">{{ $student->admission_number }}</small>
                                                @endif
                                            </td>
                                            <td style="font-size:.875rem;">{{ $exam->class?->name ?? '—' }}</td>
                                            <td style="font-size:.875rem;">{{ $exam->section?->name ?? '—' }}</td>
                                            @if($exam->stream)
                                            <td style="font-size:.875rem;">{{ $exam->stream->name }}</td>
                                            @endif
                                            <td style="font-size:.875rem;">{{ $exam->subject?->name ?? '—' }}</td>
                                            <td style="font-size:.875rem;">
                                                {{ \Carbon\Carbon::parse($exam->exam_date)->format('d M Y') }}
                                            </td>
                                            <td style="font-size:.8rem;white-space:nowrap;" class="text-muted">
                                                {{ $exam->start_time }} → {{ $exam->end_time }}
                                            </td>
                                            <td class="text-center fw-semibold" style="font-size:.875rem;">
                                                {{ $exam->total_marks }}
                                            </td>
                                            <td class="text-center" style="font-size:.875rem;">
                                                {{ $exam->pass_marks }}
                                            </td>
                                            <td class="text-center">
                                                @if($hasMark)
                                                    <span class="fw-bold" style="font-size:.95rem;
                                                        color: {{ $passed ? '#15803d' : '#b91c1c' }};">
                                                        {{ $marksObtained }}
                                                    </span>
                                                @else
                                                    <span class="text-muted" style="font-size:.8rem;">—</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($hasMark)
                                                    @if($passed)
                                                        <span class="badge"
                                                              style="background:#dcfce7;color:#166534;font-size:.72rem;
                                                                     font-weight:600;padding:.35em .75em;border-radius:20px;">
                                                            Pass
                                                        </span>
                                                    @else
                                                        <span class="badge"
                                                              style="background:#fee2e2;color:#991b1b;font-size:.72rem;
                                                                     font-weight:600;padding:.35em .75em;border-radius:20px;">
                                                            Fail
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="badge"
                                                          style="background:#f3f4f6;color:#6b7280;font-size:.72rem;
                                                                 font-weight:600;padding:.35em .75em;border-radius:20px;">
                                                        Not Entered
                                                    </span>
                                                @endif
                                            </td>
                                            <td style="font-size:.8rem;color:#6b7280;max-width:160px;">
                                                {{ $mark?->remarks ?? '—' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="14" class="text-center py-5">
                                                <i class="feather-users text-muted d-block mb-3"
                                                   style="font-size:3rem;"></i>
                                                <h6 class="text-muted">No students found</h6>
                                                <p class="text-muted small mb-0">
                                                    No students enrolled in this class/section
                                                </p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>

                                {{-- SUMMARY FOOTER --}}
                                @if($students->count() > 0)
                                @php
                                    $totalStudents  = $students->count();
                                    $appeared       = $existingMarks->count();
                                    $passedCount    = $existingMarks->filter(
                                        fn($m) => !is_null($m->marks_obtained)
                                              && $m->marks_obtained >= $exam->pass_marks
                                    )->count();
                                    $failedCount    = $appeared - $passedCount;
                                    $notEntered     = $totalStudents - $appeared;
                                @endphp
                                <tfoot>
                                    <tr style="background:#f8fafc;border-top:2px solid #e2e8f0;">
                                        <td colspan="{{ $exam->stream ? 12 : 11 }}"
                                            class="py-3 px-4">
                                            <div class="d-flex flex-wrap gap-3 align-items-center">
                                                <span style="font-size:.8rem;font-weight:600;color:#374151;">
                                                    Total Students: <strong>{{ $totalStudents }}</strong>
                                                </span>
                                                <span style="font-size:.8rem;font-weight:600;color:#374151;">
                                                    Appeared: <strong>{{ $appeared }}</strong>
                                                </span>
                                                <span style="font-size:.8rem;font-weight:600;color:#15803d;">
                                                    Passed: <strong>{{ $passedCount }}</strong>
                                                </span>
                                                <span style="font-size:.8rem;font-weight:600;color:#b91c1c;">
                                                    Failed: <strong>{{ $failedCount }}</strong>
                                                </span>
                                                <span style="font-size:.8rem;font-weight:600;color:#6b7280;">
                                                    Not Entered: <strong>{{ $notEntered }}</strong>
                                                </span>
                                            </div>
                                        </td>
                                        <td></td><td></td>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    .table th {
        white-space: nowrap;
        font-size: 0.8rem;
        padding: 0.7rem 1rem;
    }
    .table td {
        padding: 0.7rem 1rem;
        vertical-align: middle;
    }
    .content-area { overflow-y: auto !important; }
</style>
@endsection