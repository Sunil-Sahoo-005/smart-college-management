<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AttendancePeriod;
use App\Models\Classe;
use App\Models\ClassStreamMapping;
use App\Models\Course;
use App\Models\CourseStream;
use App\Models\MergedClassSchedule;
use App\Models\Room;
use App\Models\Section;
use App\Models\Staff;
use App\Models\Stream;
use App\Models\Subject;
use App\Models\TimetableSlot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class MergedClassScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = MergedClassSchedule::with(['staff', 'room', 'subject', 'period'])
            ->where('tenant_id', Auth::user()->tenant_id);

        if ($request->filled('date')) {
            $query->whereDate('schedule_date', $request->date);
        }

        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'today') {
                $query->whereDate('schedule_date', today());
            } elseif ($request->status === 'upcoming') {
                $query->whereDate('schedule_date', '>', today());
            }
        }

        $mergedSchedules = $query
            ->orderBy('schedule_date', 'desc')
            ->orderBy('period_id')
            ->paginate(25);

        $mergedSchedules->getCollection()->transform(function ($schedule) {
            $schedule->class_combination_labels = $this->buildCombinationLabels($schedule->class_combinations ?? []);
            return $schedule;
        });

        $tenantId = Auth::user()->tenant_id;

        $stats = [
            'today' => MergedClassSchedule::where('tenant_id', $tenantId)
                ->whereDate('schedule_date', today())
                ->count(),
            'upcoming' => MergedClassSchedule::where('tenant_id', $tenantId)
                ->whereDate('schedule_date', '>', today())
                ->count(),
            'total' => MergedClassSchedule::where('tenant_id', $tenantId)->count(),
        ];

        $staffList = Staff::where('tenant_id', $tenantId)
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name']);

        return view('Academics.merged_classes.index', compact('mergedSchedules', 'stats', 'staffList'));
    }

    public function create()
    {
        return view('Academics.merged_classes.form', $this->buildFormPayload());
    }

    public function edit($id)
    {
        $mergedSchedule = MergedClassSchedule::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        return view('Academics.merged_classes.form', $this->buildFormPayload($mergedSchedule));
    }

    public function store(Request $request)
    {
        $data = $this->validateMergedSchedule($request);

        MergedClassSchedule::create([
            'tenant_id' => Auth::user()->tenant_id,
            'academic_year_id' => $this->getCurrentAcademicYearId(),
            'schedule_date' => $data['schedule_date'],
            'period_id' => $data['period_id'],
            'class_combinations' => $data['class_combinations'],
            'subject_id' => $data['subject_id'],
            'staff_id' => $data['staff_id'],
            'room_id' => $data['room_id'],
        ]);

        return redirect()
            ->route('merged-classes.index')
            ->with('success', 'Merged class schedule created successfully.');
    }

    public function update(Request $request, $id)
    {
        $mergedSchedule = MergedClassSchedule::where('tenant_id', Auth::user()->tenant_id)
            ->findOrFail($id);

        $data = $this->validateMergedSchedule($request, $mergedSchedule->id);

        $mergedSchedule->update([
            'schedule_date' => $data['schedule_date'],
            'period_id' => $data['period_id'],
            'class_combinations' => $data['class_combinations'],
            'subject_id' => $data['subject_id'],
            'staff_id' => $data['staff_id'],
            'room_id' => $data['room_id'],
        ]);

        return redirect()
            ->route('merged-classes.index')
            ->with('success', 'Merged class schedule updated successfully.');
    }

    public function destroy(MergedClassSchedule $mergedSchedule)
    {
        abort_if($mergedSchedule->tenant_id !== Auth::user()->tenant_id, 403);

        $mergedSchedule->delete();

        return redirect()
            ->route('merged-classes.index')
            ->with('success', 'Merged class schedule deleted successfully.');
    }

    public function getClassesByStream($streamId)
    {
        $classes = Classe::where('tenant_id', Auth::user()->tenant_id)
            ->whereHas('streams', function ($query) use ($streamId) {
                $query->where('streams.id', $streamId);
            })
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($classes);
    }

    public function getSectionsByClass($classId)
    {
        $sections = Section::where('tenant_id', Auth::user()->tenant_id)
            ->where('class_id', $classId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($sections);
    }

    private function buildFormPayload(?MergedClassSchedule $mergedSchedule = null): array
    {
        $tenantId = Auth::user()->tenant_id;

        return [
            'courses' => Course::where('tenant_id', $tenantId)->orderBy('name')->get(['id', 'name']),
            'streams' => Stream::where('tenant_id', $tenantId)->orderBy('name')->get(['id', 'name']),
            'periods' => AttendancePeriod::where('tenant_id', $tenantId)->get(),
            'staff' => Staff::where('tenant_id', $tenantId)->orderBy('first_name')->get(['id', 'first_name', 'last_name']),
            'rooms' => Room::where('tenant_id', $tenantId)->orderBy('name')->get(['id', 'name']),
            'subjects' => Subject::where('tenant_id', $tenantId)->orderBy('name')->get(['id', 'name']),
            'mergedSchedule' => $mergedSchedule,
            'currentAcademicYearId' => $this->getCurrentAcademicYearId(),
        ];
    }

    private function validateMergedSchedule(Request $request, ?int $excludeId = null): array
    {
        $data = $request->validate([
            'schedule_date' => 'required|date',
            'period_id' => 'required|exists:attendance_periods,id',
            'class_combinations' => 'required|array|min:2',
            'class_combinations.*.course_id' => 'required|exists:courses,id',
            'class_combinations.*.stream_id' => 'required|exists:streams,id',
            'class_combinations.*.class_id' => 'required|exists:classes,id',
            'class_combinations.*.section_id' => 'required|exists:sections,id',
            'subject_id' => 'required|exists:subjects,id',
            'staff_id' => 'required|exists:staff,id',
            'room_id' => 'required|exists:rooms,id',
        ], [
            'class_combinations.min' => 'Please add at least two class combinations for a merged session.',
        ]);

        $data['class_combinations'] = $this->normalizeCombinations($data['class_combinations']);

        if (count($data['class_combinations']) < 2) {
            throw ValidationException::withMessages([
                'class_combinations' => 'Please add at least two valid class combinations.',
            ]);
        }

        $this->ensureUniqueCombinations($data['class_combinations']);
        $this->ensureRoomAndStaffAvailability(
            $data['schedule_date'],
            (int) $data['period_id'],
            (int) $data['staff_id'],
            (int) $data['room_id'],
            $excludeId
        );

        return $data;
    }

    private function normalizeCombinations(array $combinations): array
    {
        return collect($combinations)
            ->filter(function ($combo) {
                if (! is_array($combo)) {
                    return false;
                }

                return ! empty(Arr::get($combo, 'course_id'))
                    && ! empty(Arr::get($combo, 'stream_id'))
                    && ! empty(Arr::get($combo, 'class_id'))
                    && ! empty(Arr::get($combo, 'section_id'));
            })
            ->map(fn ($combo) => [
                'course_id' => (int) Arr::get($combo, 'course_id'),
                'stream_id' => (int) Arr::get($combo, 'stream_id'),
                'class_id' => (int) Arr::get($combo, 'class_id'),
                'section_id' => (int) Arr::get($combo, 'section_id'),
            ])
            ->values()
            ->all();
    }

    private function ensureUniqueCombinations(array $combinations): void
    {
        $keys = collect($combinations)
            ->map(fn ($combo) => $combo['course_id'] . '-' . $combo['stream_id'] . '-' . $combo['class_id'] . '-' . $combo['section_id']);

        if ($keys->count() !== $keys->unique()->count()) {
            throw ValidationException::withMessages([
                'class_combinations' => 'The same course, branch, class, and section combination cannot be added twice.',
            ]);
        }

        foreach ($combinations as $combo) {
            $mapping = $this->resolveClassStreamMapping($combo['course_id'], $combo['stream_id'], $combo['class_id']);

            if (! $mapping) {
                throw ValidationException::withMessages([
                    'class_combinations' => 'One or more selected semesters are not mapped to the chosen course and branch.',
                ]);
            }

            $sectionExists = Section::where('tenant_id', Auth::user()->tenant_id)
                ->where('id', $combo['section_id'])
                ->where('class_stream_id', $mapping->id)
                ->exists();

            if (! $sectionExists) {
                throw ValidationException::withMessages([
                    'class_combinations' => 'One or more selected sections do not belong to the chosen course, branch, and semester.',
                ]);
            }
        }
    }

    private function ensureRoomAndStaffAvailability(
        string $scheduleDate,
        int $periodId,
        int $staffId,
        int $roomId,
        ?int $excludeId = null
    ): void {
        $tenantId = Auth::user()->tenant_id;
        $currentAcademicYearId = $this->getCurrentAcademicYearId();
        $dayOfWeek = Carbon::parse($scheduleDate)->dayOfWeekIso;

        $mergedConflict = MergedClassSchedule::where('tenant_id', $tenantId)
            ->whereDate('schedule_date', $scheduleDate)
            ->where('period_id', $periodId)
            ->when($excludeId, fn ($query) => $query->where('id', '!=', $excludeId))
            ->where(function ($query) use ($staffId, $roomId) {
                $query->where('staff_id', $staffId)
                    ->orWhere('room_id', $roomId);
            })
            ->first();

        if ($mergedConflict) {
            $message = (int) $mergedConflict->staff_id === $staffId
                ? 'This staff member already has another merged class in the selected date and period.'
                : 'This room is already used by another merged class in the selected date and period.';

            throw ValidationException::withMessages([
                'schedule_date' => $message,
            ]);
        }

        if ($currentAcademicYearId && $dayOfWeek >= 1 && $dayOfWeek <= 6) {
            $regularConflict = TimetableSlot::where('tenant_id', $tenantId)
                ->where('period_id', $periodId)
                ->where('day_of_week', $dayOfWeek)
                ->where(function ($query) use ($staffId, $roomId) {
                    $query->where('staff_id', $staffId)
                        ->orWhere('room_id', $roomId);
                })
                ->whereHas('timetable', fn ($query) => $query->whereHas(
                    'academicYears',
                    fn ($yearQuery) => $yearQuery->where('academic_year_id', $currentAcademicYearId)
                ))
                ->first();

            if ($regularConflict) {
                $message = (int) $regularConflict->staff_id === $staffId
                    ? 'This staff member is already assigned in the regular timetable for the selected day and period.'
                    : 'This room is already assigned in the regular timetable for the selected day and period.';

                throw ValidationException::withMessages([
                    'schedule_date' => $message,
                ]);
            }
        }
    }

    private function buildCombinationLabels(array $combinations): array
    {
        $validCombinations = collect($combinations)
            ->filter(fn ($combo) => is_array($combo))
            ->map(fn ($combo) => [
                'course_id' => Arr::get($combo, 'course_id'),
                'stream_id' => Arr::get($combo, 'stream_id'),
                'class_id' => Arr::get($combo, 'class_id'),
                'section_id' => Arr::get($combo, 'section_id'),
            ])
            ->filter(fn ($combo) => $combo['stream_id'] && $combo['class_id'] && $combo['section_id'])
            ->values();

        $courseIds = $validCombinations->pluck('course_id')->filter()->unique()->all();
        $streamIds = $validCombinations->pluck('stream_id')->filter()->unique()->all();
        $classIds = $validCombinations->pluck('class_id')->filter()->unique()->all();
        $sectionIds = $validCombinations->pluck('section_id')->filter()->unique()->all();

        $courses = Course::whereIn('id', $courseIds)->pluck('name', 'id');
        $streams = Stream::whereIn('id', $streamIds)->pluck('name', 'id');
        $classes = Classe::whereIn('id', $classIds)->pluck('name', 'id');
        $sections = Section::whereIn('id', $sectionIds)->pluck('name', 'id');

        return $validCombinations->map(function ($combo) use ($courses, $streams, $classes, $sections) {
            return trim(
                ($courses[$combo['course_id']] ?? 'Course')
                . ' / '
                . ($streams[$combo['stream_id']] ?? 'Branch')
                . ' / '
                . ($classes[$combo['class_id']] ?? 'Class')
                . ' / '
                . ($sections[$combo['section_id']] ?? 'Section')
            );
        })->all();
    }

    private function resolveClassStreamMapping(int $courseId, int $streamId, int $classId): ?ClassStreamMapping
    {
        $courseStream = CourseStream::where('tenant_id', Auth::user()->tenant_id)
            ->where('course_id', $courseId)
            ->where('stream_id', $streamId)
            ->first();

        if (! $courseStream) {
            return null;
        }

        return ClassStreamMapping::where('tenant_id', Auth::user()->tenant_id)
            ->where('course_stream_id', $courseStream->id)
            ->where('class_id', $classId)
            ->first();
    }

    private function getCurrentAcademicYearId(): ?int
    {
        return AcademicYear::where('tenant_id', Auth::user()->tenant_id)
            ->where('is_current', true)
            ->first()?->id
            ?? AcademicYear::where('tenant_id', Auth::user()->tenant_id)
                // ->orderBy('start_date', 'desc')
                ->first()?->id;
    }
}
