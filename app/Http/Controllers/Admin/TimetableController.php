<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Timetable;
use App\Models\Classe;
use App\Models\Section;
use App\Models\AcademicYear;
use App\Models\AttendancePeriod;
use App\Models\Room;
use App\Models\Staff;
use App\Models\Stream;
use App\Models\Subject;
use App\Models\Course;
use App\Models\CourseStream;
use App\Models\ClassStreamMapping;
use App\Models\TimetableSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TimetableController extends Controller
{
    // protected $listRoute = 'admin.timetables.index';

    public function destroy(Timetable $timetable)
    {
        $this->authorizeTenant($timetable);

        try {
            $timetable->delete();
            return back()->with('success', 'Timetable deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete timetable, it may be in use.');
        }
    }

    protected function authorizeTenant(Timetable $timetable)
    {
        if ($timetable->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }
    }


    public function index(Request $request)
    {
        $currentAcademicYear = AcademicYear::where('tenant_id', Auth::user()->tenant_id)
            ->where('is_current', true)
            ->first();

        $courses = Course::where('tenant_id', Auth::user()->tenant_id)
            ->orderBy('name')
            ->get();

        // Optional: filter by course
        $courseId = $request->get('course_id');

        $query = Timetable::where('tenant_id', Auth::user()->tenant_id)
            ->with(['class', 'section', 'academicYear', 'academicYears', 'course'])
            ->when($courseId, fn($q) => $q->where('course_id', $courseId))
            ->latest();

        $timetables = $query->paginate(10);

        return view('Academics.timetables.index', compact(
            'courses',
            'timetables',
            'currentAcademicYear',
            'courseId'
        ));
    }

    public function create()
    {
        $academicYears = AcademicYear::where('tenant_id', Auth::user()->tenant_id)->get();
        $currentAcademicYear = AcademicYear::where('tenant_id', Auth::user()->tenant_id)
            ->where('is_current', true)
            ->first();

        $courses = Course::where('tenant_id', Auth::user()->tenant_id)->get();
        $periods = AttendancePeriod::where('tenant_id', Auth::user()->tenant_id)->get();
        $staff = Staff::where('tenant_id', Auth::user()->tenant_id)->get(['id', 'first_name', 'last_name']);
        $rooms = Room::where('tenant_id', Auth::user()->tenant_id)->get(['id', 'name']);

        return view('Academics.timetables.create', compact(
            'courses',
            'academicYears',
            'currentAcademicYear',
            'periods',
            'staff',
            'rooms'
        ));
    }


    public function store(Request $request)
    {
        $rules = [
            'name' => 'nullable|string|max:255|regex:/^[a-zA-Z0-9\s\-\_\.]+$/|unique:timetables,name,NULL,id,tenant_id,' . Auth::user()->tenant_id,
            'course_id' => 'required|exists:courses,id',
            'stream_id' => 'required|exists:streams,id',
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'required|exists:sections,id',
            'academic_year_ids' => 'required|array|min:1',
            'academic_year_ids.*' => 'exists:academic_years,id',
            'entry_type' => 'required|in:regular,lateral,both',

            'slots' => 'required|array|min:1',
            'slots.*' => 'array',

            'slots.*.*.subject_id' => 'required|exists:subjects,id',
            'slots.*.*.staff_id' => 'required|exists:staff,id',
            'slots.*.*.room_id' => 'required|exists:rooms,id',
        ];

        $messages = [
            'name.regex' => 'Name can only contain letters, numbers, spaces, hyphens (-), underscores (_), and periods (.).',
            'name.unique' => 'Timetable name already exists for this tenant.',
            'name.required' => 'Timetable name is required.',
            'course_id.required' => 'Please select a valid course.',
            'course_id.exists' => 'Selected course does not exist.',
            'stream_id.required' => 'Please select a valid stream.',
            'stream_id.exists' => 'Selected stream does not exist.',

            'class_id.required' => 'Please select a class.',
            'class_id.exists' => 'Selected class does not exist.',

            'section_id.required' => 'Please select a section.',
            'section_id.exists' => 'Selected section does not exist.',

            'academic_year_ids.required' => 'At least one academic year is required.',
            'academic_year_ids.array' => 'Invalid academic years format.',
            'academic_year_ids.*.exists' => 'One or more selected academic years do not exist.',
            'entry_type.required' => 'Entry type is required.',
            'entry_type.in' => 'Invalid entry type selected.',

            'slots.required' => 'Please assign at least one timetable slot.',
            'slots.min' => 'Timetable must have at least one slot.',
            'slots.*.*.subject_id.required' => 'Subject is mandatory for every slot.',
            'slots.*.*.subject_id.exists' => 'Please select a valid subject.',

            'slots.*.*.staff_id.required' => 'Teacher is mandatory for every slot.',
            'slots.*.*.staff_id.exists' => 'Please select a valid teacher.',

            'slots.*.*.room_id.required' => 'Room is mandatory for every slot.',
            'slots.*.*.room_id.exists' => 'Please select a valid room.',
        ];

        $request->validate($rules, $messages);
        $mapping = $this->resolveClassStreamMapping($request->course_id, $request->stream_id, $request->class_id);

        if (! $mapping) {
            return back()
                ->withErrors(['class_id' => 'Selected semester is not mapped to the chosen course and branch.'])
                ->withInput();
        }

        $sectionExists = Section::where('id', $request->section_id)
            ->where('tenant_id', Auth::user()->tenant_id)
            ->where('class_stream_id', $mapping->id)
            ->exists();

        if (! $sectionExists) {
            return back()
                ->withErrors(['section_id' => 'Selected section does not belong to the chosen course, branch, and semester.'])
                ->withInput();
        }

        if ($request->has('slots')) {
            $conflictMessage = $this->validateSlotConflicts($request->slots, $request->academic_year_ids);
            if ($conflictMessage) {
                return back()->withErrors(['slots' => $conflictMessage])->withInput();
            }
        }
        $timetable = Timetable::create([
            'tenant_id' => Auth::user()->tenant_id,
            'course_id' => $request->course_id,
            'stream_id' => $request->stream_id,
            'class_id' => $request->class_id,
            'section_id' => $request->section_id,
            'entry_type' => $request->entry_type,
            'name' => $request->name,
        ]);

        $timetable->academicYears()->sync($request->academic_year_ids);
        if ($request->has('slots')) {
            foreach ($request->slots as $periodId => $days) {
                foreach ($days as $dayOfWeek => $slotData) {
                    if (empty($slotData['subject_id'])) continue;

                    TimetableSlot::create([
                        'tenant_id'     => $timetable->tenant_id,
                        'timetable_id' => $timetable->id,
                        'day_of_week' => $dayOfWeek,
                        'period_id' => $periodId,
                        'subject_id' => $slotData['subject_id'],
                        'staff_id' => $slotData['staff_id'] ?? null,
                        'room_id' => $slotData['room_id'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('admin.timetables.index')
            ->with('success', 'Timetable created successfully.');
    }
    public function show(Timetable $timetable)
    {
        $timetable->load([
            'course',
            'class',
            'section',
            'stream',
            'academicYear',
            'academicYears',
            'slots.subject',
            'slots.staff',
            'slots.room',
            'slots.period',
        ]);

        $periods = AttendancePeriod::where('tenant_id', Auth::user()->tenant_id)->get();

        return view('Academics.timetables.show', compact('timetable', 'periods'));
    }

    public function edit(Timetable $timetable)
    {
        $timetable->load(['slots', 'academicYears']);

        $academicYears = AcademicYear::where('tenant_id', Auth::user()->tenant_id)->get();
        $currentAcademicYear = AcademicYear::where('tenant_id', Auth::user()->tenant_id)
            ->where('is_current', true)
            ->first();

        $courses = Course::where('tenant_id', Auth::user()->tenant_id)->get();
        $streams = Stream::where('tenant_id', Auth::user()->tenant_id)->get();
        $periods = AttendancePeriod::where('tenant_id', Auth::user()->tenant_id)->get();
        $staff = Staff::where('tenant_id', Auth::user()->tenant_id)->get(['id', 'first_name', 'last_name']);
        $rooms = Room::where('tenant_id', Auth::user()->tenant_id)->get(['id', 'name']);

        // Existing slots for prefilling grid (prioritize old() values if validation failed)
        $existingSlots = old('slots') ? [] : $timetable->slots->map(function ($slot) {
            return [
                'period_id' => $slot->period_id,
                'day_of_week' => $slot->day_of_week,
                'subject_id' => $slot->subject_id,
                'staff_id' => $slot->staff_id,
                'room_id' => $slot->room_id,
            ];
        })->toArray();

        // If old slots exist, we pass them as well (Blade will handle prioritize them via script)
        $oldSlots = old('slots', []);

        return view('Academics.timetables.edit', compact(
            'timetable',
            'academicYears',
            'currentAcademicYear',
            'courses',
            'streams',
            'periods',
            'staff',
            'rooms',
            'existingSlots',
            'oldSlots'
        ));
    }

    public function update(Request $request, Timetable $timetable)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'stream_id' => 'required|exists:streams,id',
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'required|exists:sections,id',
            'academic_year_ids' => 'required|array|min:1',
            'academic_year_ids.*' => 'exists:academic_years,id',
            'entry_type' => 'required|in:regular,lateral,both',
            'slots' => 'array',
            'slots.*.*.subject_id' => 'nullable|exists:subjects,id',
            'slots.*.*.staff_id' => 'nullable|exists:staff,id',
            'slots.*.*.room_id' => 'nullable|exists:rooms,id',
        ]);

        $mapping = $this->resolveClassStreamMapping($request->course_id, $request->stream_id, $request->class_id);

        if (! $mapping) {
            return back()
                ->withErrors(['class_id' => 'Selected semester is not mapped to the chosen course and branch.'])
                ->withInput();
        }

        $sectionExists = Section::where('id', $request->section_id)
            ->where('tenant_id', Auth::user()->tenant_id)
            ->where('class_stream_id', $mapping->id)
            ->exists();

        if (! $sectionExists) {
            return back()
                ->withErrors(['section_id' => 'Selected section does not belong to the chosen course, branch, and semester.'])
                ->withInput();
        }

        if ($request->has('slots')) {
            $conflictMessage = $this->validateSlotConflicts($request->slots, $request->academic_year_ids, $timetable->id);
            if ($conflictMessage) {
                return back()->withErrors(['slots' => $conflictMessage])->withInput();
            }
        }

        $timetable->update([
            'tenant_id' => Auth::user()->tenant_id,
            'course_id' => $request->course_id,
            'stream_id' => $request->stream_id,
            'class_id' => $request->class_id,
            'section_id' => $request->section_id,
            'entry_type' => $request->entry_type,
            'name' => $request->name,
        ]);

        $timetable->academicYears()->sync($request->academic_year_ids);

        // Delete existing slots
        TimetableSlot::where('timetable_id', $timetable->id)->delete();

        if ($request->has('slots')) {
            foreach ($request->slots as $periodId => $days) {
                foreach ($days as $dayOfWeek => $slotData) {
                    if (empty($slotData['subject_id'])) continue;

                    TimetableSlot::create([
                        'tenant_id' => $timetable->tenant_id,
                        'timetable_id' => $timetable->id,
                        'day_of_week' => $dayOfWeek,
                        'period_id' => $periodId,
                        'subject_id' => $slotData['subject_id'],
                        'staff_id' => $slotData['staff_id'] ?? null,
                        'room_id' => $slotData['room_id'] ?? null,
                    ]);
                }
            }
        }

        return redirect()
            ->route('admin.timetables.index')
            ->with('success', 'Timetable updated successfully.');
    }


    public function getStreams($courseId)
    {
        $query = DB::table('course_stream')
            ->where('course_stream.course_id', $courseId)
            ->join('streams', 'course_stream.stream_id', '=', 'streams.id')
            ->select('streams.*');

        $user = Auth::user();
        if ($user && isset($user->tenant_id)) {
            $query->where('course_stream.tenant_id', $user->tenant_id);
        }

        return response()->json($query->get()->toArray());
    }

    public function getClasses($courseId, $streamId)
    {
        $courseStreamQuery = CourseStream::where('course_id', $courseId)
            ->where('stream_id', $streamId);

        $user = Auth::user();
        if ($user && isset($user->tenant_id)) {
            $courseStreamQuery->where('tenant_id', $user->tenant_id);
        }

        $courseStream = $courseStreamQuery->first();

        if (! $courseStream) {
            return response()->json([]);
        }

        $query = DB::table('class_stream_mappings')
            ->where('course_stream_id', $courseStream->id)
            ->join('classes', 'class_stream_mappings.class_id', '=', 'classes.id')
            ->select('classes.*');

        if ($user && isset($user->tenant_id)) {
            $query->where('class_stream_mappings.tenant_id', $user->tenant_id);
        }

        $classes = $query->get()->toArray();
        return response()->json($classes);
    }

    public function getSubjects($courseId, $streamId, $classId)
    {
        $mapping = $this->resolveClassStreamMapping($courseId, $streamId, $classId);

        if (! $mapping) {
            return response()->json([]);
        }

        $query = DB::table('class_subject_mappings')
            ->where('class_stream_id', $mapping->id)
            ->join('subjects', 'class_subject_mappings.subject_id', '=', 'subjects.id')
            ->select('subjects.*', 'class_subject_mappings.is_compulsory');

        $user = Auth::user();
        if ($user && isset($user->tenant_id)) {
            $query->where('class_subject_mappings.tenant_id', $user->tenant_id);
        }

        return response()->json($query->get()->toArray());
    }

    public function getSections($courseId, $streamId, $classId)
    {
        $mapping = $this->resolveClassStreamMapping($courseId, $streamId, $classId);

        if (! $mapping) {
            return response()->json([]);
        }

        $query = Section::where('class_stream_id', $mapping->id);

        $user = Auth::user();
        if ($user && isset($user->tenant_id)) {
            $query->where('tenant_id', $user->tenant_id);
        }

        return response()->json($query->get()->toArray());
    }

    protected function resolveClassStreamMapping($courseId, $streamId, $classId): ?ClassStreamMapping
    {
        $user = Auth::user();

        $courseStreamQuery = CourseStream::where('course_id', $courseId)
            ->where('stream_id', $streamId);

        if ($user && isset($user->tenant_id)) {
            $courseStreamQuery->where('tenant_id', $user->tenant_id);
        }

        $courseStream = $courseStreamQuery->first();

        if (! $courseStream) {
            return null;
        }

        $mappingQuery = ClassStreamMapping::where('course_stream_id', $courseStream->id)
            ->where('class_id', $classId);

        if ($user && isset($user->tenant_id)) {
            $mappingQuery->where('tenant_id', $user->tenant_id);
        }

        return $mappingQuery->first();
    }

    protected function validateSlotConflicts(array $slots, array $academicYearIds, ?int $excludeTimetableId = null): ?string
    {
        $dayNames = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday'];

        foreach ($slots as $periodId => $days) {
            foreach ($days as $dayOfWeek => $slotData) {
                $dayName = $dayNames[(int) $dayOfWeek] ?? 'Unknown Day';

                if (! empty($slotData['staff_id'])) {
                    $staffConflict = $this->findSlotConflict(
                        (int) $periodId,
                        (int) $dayOfWeek,
                        'staff_id',
                        (int) $slotData['staff_id'],
                        $academicYearIds,
                        $excludeTimetableId
                    );

                    if ($staffConflict) {
                        return 'Teacher is already scheduled on ' . $dayName . ' for this period.';
                    }
                }

                if (! empty($slotData['room_id'])) {
                    $roomConflict = $this->findSlotConflict(
                        (int) $periodId,
                        (int) $dayOfWeek,
                        'room_id',
                        (int) $slotData['room_id'],
                        $academicYearIds,
                        $excludeTimetableId
                    );

                    if ($roomConflict) {
                        return 'Room is already booked on ' . $dayName . ' for this period.';
                    }
                }
            }
        }

        return null;
    }

    protected function findSlotConflict(
        int $periodId,
        int $dayOfWeek,
        string $column,
        int $resourceId,
        array $academicYearIds,
        ?int $excludeTimetableId = null
    ): ?TimetableSlot {
        return TimetableSlot::where('tenant_id', Auth::user()->tenant_id)
            ->where('period_id', $periodId)
            ->where('day_of_week', $dayOfWeek)
            ->where($column, $resourceId)
            ->when($excludeTimetableId, fn($query) => $query->where('timetable_id', '!=', $excludeTimetableId))
            ->whereHas('timetable', fn($query) => $query->whereHas(
                'academicYears',
                fn($academicYearQuery) => $academicYearQuery->whereIn('academic_year_id', $academicYearIds)
            ))
            ->first();
    }
}
