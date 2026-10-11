@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Timetables</span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        @if (auth()->user()->hasPermissionTo('create time table'))
                            <a href="{{ route('admin.timetables.create') }}" class="btn btn-primary">
                                <i class="feather-plus me-2"></i>Add Timetable
                            </a>
                        @endif
                    </div>
                    <a href="{{ route('merged-classes.index') }}" class="btn btn-primary">
                                <i class="feather-plus me-2"></i>merged Timetable
                            </a>
                </div>

                <div class="content-area-body">

                    {{-- Stats Row --}}
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-3">
                            <div class="tt-stat-card">
                                <div class="tt-stat-icon" style="background:#eff6ff; color:#2563eb;">
                                    <i class="feather-grid"></i>
                                </div>
                                <div>
                                    <div class="tt-stat-val">{{ $timetables->total() }}</div>
                                    <div class="tt-stat-lbl">Total timetables</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="tt-stat-card">
                                <div class="tt-stat-icon" style="background:#f0fdf4; color:#16a34a;">
                                    <i class="feather-book"></i>
                                </div>
                                <div>
                                    <div class="tt-stat-val">{{ $courses->count() }}</div>
                                    <div class="tt-stat-lbl">Courses</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="tt-stat-card">
                                <div class="tt-stat-icon" style="background:#fffbeb; color:#d97706;">
                                    <i class="feather-calendar"></i>
                                </div>
                                <div>
                                    <div class="tt-stat-val">{{ $currentAcademicYear?->name ?? '—' }}</div>
                                    <div class="tt-stat-lbl">Academic year</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="tt-stat-card">
                                <div class="tt-stat-icon" style="background:#fdf4ff; color:#9333ea;">
                                    <i class="feather-filter"></i>
                                </div>
                                <div>
                                    <div class="tt-stat-val">
                                        {{ $courseId ? $courses->find($courseId)?->name : 'All' }}
                                    </div>
                                    <div class="tt-stat-lbl">Active filter</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Course Filter Tabs --}}
                    <div class="tt-stream-tabs mb-4">
                        <a href="{{ route('admin.timetables.index') }}"
                            class="tt-stream-tab {{ !$courseId ? 'active' : '' }}">
                            <i class="feather-layers me-1" style="font-size:12px;"></i>
                            All Courses
                        </a>
                        @foreach ($courses as $course)
                            <a href="{{ route('admin.timetables.index', ['course_id' => $course->id]) }}"
                                class="tt-stream-tab {{ $courseId == $course->id ? 'active' : '' }}">
                                {{ $course->name }}
                            </a>
                        @endforeach
                    </div>

                    {{-- Timetable Cards Grid --}}
                    @if ($timetables->isEmpty())
                        <div class="tt-empty-state">
                            <div class="tt-empty-icon">
                                <i class="feather-calendar" style="font-size: 32px; opacity: 0.3;"></i>
                            </div>
                            <h6 class="fw-semibold text-muted mb-1">No timetables found</h6>
                            <p class="text-muted small mb-3">
                                {{ $courseId ? 'No timetables exist for this course yet.' : 'No timetables created yet.' }}
                            </p>
                            @if (auth()->user()->hasPermissionTo('create time table'))
                                <a href="{{ route('admin.timetables.create') }}" class="btn btn-primary btn-sm">
                                    <i class="feather-plus me-1"></i>Create First Timetable
                                </a>
                            @endif
                        </div>
                    @else
                        <div class="tt-cards-grid">
                            @foreach ($timetables as $index => $timetable)
                                @php
                                    $colors = ['#2563eb', '#16a34a', '#d97706', '#9333ea', '#dc2626', '#0891b2'];
                                    $accentColor = $colors[$index % count($colors)];
                                @endphp
                                <div class="tt-card" style="animation-delay: {{ $index * 40 }}ms">
                                    <div class="tt-card-accent" style="background: {{ $accentColor }};"></div>

                                    <div class="tt-card-top">
                                        <div class="tt-card-class">{{ $timetable->class?->name ?? 'N/A' }}</div>
                                        <span class="tt-section-pill">
                                            <i class="feather-users" style="font-size:10px;"></i>
                                            {{ $timetable->section?->name ?? 'N/A' }}
                                        </span>
                                    </div>

                                    <div class="tt-card-meta">
                                        @if ($timetable->stream)
                                            <div class="tt-meta-row">
                                                <i class="feather-git-branch"></i>
                                                {{ $timetable->stream->name }}
                                            </div>
                                        @endif
                                        <div class="tt-meta-row">
                                            <i class="feather-user"></i>
                                            <span class="text-capitalize">{{ $timetable->entry_type }} Entry</span>
                                        </div>
                                        @if ($timetable->academicYears->isNotEmpty())
                                            <div class="tt-meta-row">
                                                <i class="feather-calendar"></i>
                                                {{ $timetable->academicYears->pluck('name')->join(', ') }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="tt-card-actions">
                                        @if (auth()->user()->hasPermissionTo('edit time table'))
                                            <a href="{{ route('admin.timetables.edit', $timetable) }}"
                                                class="tt-action edit">
                                                <i class="feather-edit-2"></i> Edit
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.timetables.show', $timetable) }}" class="tt-action view">
                                            <i class="feather-eye"></i> View
                                        </a>
                                    </div>
                                </div>
                            @endforeach

                            {{-- Create new card --}}
                            @if (auth()->user()->hasPermissionTo('create time table'))
                                <a href="{{ route('admin.timetables.create') }}" class="tt-card tt-card-new">
                                    <i class="feather-plus-circle"></i>
                                    <span>New timetable</span>
                                </a>
                            @endif
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-4">
                            {{ $timetables->appends(['course_id' => $courseId])->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        /* ── Stats ── */
        .tt-stat-card {
            display: flex;
            align-items: center;
            gap: 14px;
            background: #fff;
            border: 0.5px solid #e5e7eb;
            border-radius: 12px;
            padding: 14px 16px;
        }

        .tt-stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .tt-stat-val {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
            line-height: 1.1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 110px;
        }

        .tt-stat-lbl {
            font-size: 12px;
            color: #6b7280;
            margin-top: 2px;
        }

        /* ── Stream Tabs ── */
        .tt-stream-tabs {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f3f4f6;
        }

        .tt-stream-tab {
            display: inline-flex;
            align-items: center;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 400;
            color: #6b7280;
            border: 1px solid #e5e7eb;
            background: #fff;
            text-decoration: none;
            transition: all 0.15s;
        }

        .tt-stream-tab:hover {
            background: #f9fafb;
            color: #111827;
            text-decoration: none;
        }

        .tt-stream-tab.active {
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
            font-weight: 500;
        }

        /* ── Cards Grid ── */
        .tt-cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 14px;
        }

        /* ── Timetable Card ── */
        .tt-card {
            background: #fff;
            border: 0.5px solid #e5e7eb;
            border-radius: 12px;
            padding: 1.1rem 1.2rem;
            position: relative;
            overflow: hidden;
            transition: border-color 0.15s, transform 0.15s;
            animation: ttFadeUp 0.3s ease both;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .tt-card:hover {
            border-color: #d1d5db;
            transform: translateY(-2px);
        }

        @keyframes ttFadeUp {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .tt-card-accent {
            position: absolute;
            top: 0;
            left: 0;
            width: 3px;
            height: 100%;
            border-radius: 12px 0 0 12px;
        }

        .tt-card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 8px;
        }

        .tt-card-class {
            font-size: 15px;
            font-weight: 600;
            color: #111827;
        }

        .tt-section-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 500;
            color: #1d4ed8;
            background: #eff6ff;
            padding: 3px 10px;
            border-radius: 20px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .tt-card-meta {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .tt-meta-row {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 12px;
            color: #6b7280;
        }

        .tt-meta-row i {
            font-size: 12px;
            opacity: 0.6;
            width: 12px;
            flex-shrink: 0;
        }

        .tt-card-actions {
            display: flex;
            gap: 8px;
            margin-top: 4px;
        }

        .tt-action {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 6px 8px;
            border-radius: 7px;
            font-size: 12px;
            font-weight: 500;
            text-decoration: none;
            border: 1px solid #e5e7eb;
            color: #374151;
            background: #fff;
            transition: all 0.12s;
        }

        .tt-action i {
            font-size: 12px;
        }

        .tt-action:hover {
            text-decoration: none;
        }

        .tt-action.edit {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #1d4ed8;
        }

        .tt-action.edit:hover {
            background: #dbeafe;
        }

        .tt-action.view:hover {
            background: #f9fafb;
            color: #111827;
        }

        /* ── New card ── */
        .tt-card-new {
            border: 1.5px dashed #d1d5db !important;
            background: #f9fafb !important;
            display: flex !important;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 150px;
            color: #9ca3af;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s !important;
        }

        .tt-card-new i {
            font-size: 24px;
        }

        .tt-card-new span {
            font-size: 13px;
        }

        .tt-card-new:hover {
            border-color: #2563eb !important;
            color: #2563eb !important;
            background: #eff6ff !important;
            transform: translateY(-2px) !important;
            text-decoration: none;
        }

        /* ── Empty state ── */
        .tt-empty-state {
            text-align: center;
            padding: 4rem 1rem;
        }

        .tt-empty-icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }
    </style>
@endsection
