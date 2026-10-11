<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendancePeriod;
use App\Models\PracticalGroups;
use App\Models\PracticalTimetable;
use App\Models\PracticalTimetableSlot;
use App\Models\Room;
use App\Models\Staff;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PracticalTimetableController extends Controller
{
    public function index()
    {
        $timetables = PracticalTimetable::where('tenant_id', Auth::user()->tenant_id)
            ->with(['practicalGroup', 'slots.period', 'slots.room', 'slots.staff', 'slots.subject'])
            ->latest()
            ->paginate(10);

        return view('Academics.practical_timetables.index', compact('timetables'));
    }

    public function master(Request $request)
    {
        $practicalGroups = PracticalGroups::where('tenant_id', Auth::user()->tenant_id)
            ->with(['groupMaster', 'course', 'stream'])
            ->orderBy('name')
            ->get();

        $subjects = Subject::where('tenant_id', Auth::user()->tenant_id)
            ->where('type', 'practical')
            ->orderBy('name')
            ->get();

        $rooms = Room::where('tenant_id', Auth::user()->tenant_id)->orderBy('name')->get(['id', 'name']);
        $staff = Staff::where('tenant_id', Auth::user()->tenant_id)->orderBy('first_name')->get(['id', 'first_name', 'last_name']);

        $selectedSubjectId = $request->integer('filter_subject_id');

        $slots = collect();
        if ($selectedSubjectId) {
            $slots = PracticalTimetableSlot::where('tenant_id', Auth::user()->tenant_id)
                ->where('subject_id', $selectedSubjectId)
                ->with(['timetable.practicalGroup.groupMaster', 'period', 'room', 'staff', 'subject'])
                ->get()
                ->sortBy([
                    ['day_of_week', 'asc'],
                    ['attendance_period_id', 'asc'],
                ])
                ->values();
        }

        $groupedByDay = $slots->groupBy('day_of_week');
        $periods = AttendancePeriod::where('tenant_id', Auth::user()->tenant_id)
            ->orderBy('start_time')
            ->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'selectedSubjectId' => $selectedSubjectId,
                'slots' => $slots->map(function ($slot) {
                    return [
                        'id' => $slot->id,
                        'day_of_week' => $slot->day_of_week,
                        'attendance_period_id' => $slot->attendance_period_id,
                        'room_id' => $slot->room_id,
                        'staff_id' => $slot->staff_id,
                        'subject_id' => $slot->subject_id,
                        'period' => $slot->period->name ?? '-',
                        'room' => $slot->room->name ?? '-',
                        'staff' => trim(($slot->staff->first_name ?? '') . ' ' . ($slot->staff->last_name ?? '')) ?: '-',
                        'subject' => $slot->subject->name ?? '-',
                        'group_name' => $slot->timetable->practicalGroup->name ?? '-',
                        'group_master' => $slot->timetable->practicalGroup->groupMaster->name ?? null,
                        'strength' => $slot->timetable->practicalGroup->groupMaster->strength ?? null,
                        'course' => $slot->timetable->practicalGroup->course->name ?? null,
                        'stream' => $slot->timetable->practicalGroup->stream->name ?? null,
                    ];
                })->values(),
            ]);
        }

        return view('Academics.practical_timetables.master', compact(
            'practicalGroups',
            'subjects',
            'selectedSubjectId',
            'slots',
            'groupedByDay',
            'periods',
            'rooms',
            'staff'
        ));
    }

    public function editSlot(PracticalTimetableSlot $slot)
    {
        $this->authorizeSlotTenant($slot);

        return response()->json([
            'id' => $slot->id,
            'day_of_week' => $slot->day_of_week,
            'attendance_period_id' => $slot->attendance_period_id,
            'room_id' => $slot->room_id,
            'staff_id' => $slot->staff_id,
            'subject_id' => $slot->subject_id,
            'practical_timetable_id' => $slot->practical_timetable_id,
            'practical_group_id' => $slot->timetable?->practical_group_id,
        ]);
    }

    public function updateSlot(Request $request, PracticalTimetableSlot $slot)
    {
        $this->authorizeSlotTenant($slot);

        $request->validate([
            'day_of_week' => 'required|integer|between:1,6',
            'attendance_period_id' => 'required|exists:attendance_periods,id',
            'room_id' => 'required|exists:rooms,id',
            'staff_id' => 'required|exists:staff,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $subject = Subject::where('tenant_id', Auth::user()->tenant_id)
            ->where('id', $request->subject_id)
            ->where('type', 'practical')
            ->first();

        if (! $subject) {
            return back()->withErrors(['subject_id' => 'Please select a practical subject only.']);
        }

        $slot->update([
            'day_of_week' => $request->day_of_week,
            'attendance_period_id' => $request->attendance_period_id,
            'room_id' => $request->room_id,
            'staff_id' => $request->staff_id,
            'subject_id' => $request->subject_id,
        ]);

        $subjectId = $slot->subject_id;
        return redirect()
            ->route('practical_timetables.master', ['filter_subject_id' => $subjectId])
            ->with('success', 'Practical slot updated successfully.');
    }

    public function destroySlot(PracticalTimetableSlot $slot)
    {
        $this->authorizeSlotTenant($slot);
        $subjectId = $slot->subject_id;
        $slot->delete();

        return redirect()
            ->route('practical_timetables.master', ['filter_subject_id' => $subjectId])
            ->with('success', 'Practical slot deleted successfully.');
    }

    public function create()
    {
        $practicalGroups = PracticalGroups::where('tenant_id', Auth::user()->tenant_id)
            ->with('groupMaster')
            ->orderBy('name')
            ->get();

        $periods = AttendancePeriod::where('tenant_id', Auth::user()->tenant_id)->orderBy('start_time')->get();
        $rooms = Room::where('tenant_id', Auth::user()->tenant_id)->orderBy('name')->get();
        $staff = Staff::where('tenant_id', Auth::user()->tenant_id)->orderBy('first_name')->get();
        $subjects = Subject::where('tenant_id', Auth::user()->tenant_id)
            ->where('type', 'practical')
            ->orderBy('name')
            ->get();

        return view('Academics.practical_timetables.form', compact('practicalGroups', 'periods', 'rooms', 'staff', 'subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'practical_group_id' => 'required|exists:practical_groups,id',
            'name' => 'nullable|string|max:255',
            'slots' => 'required|array|min:1',
            'slots.*.day_of_week' => 'required|integer|between:1,6',
            'slots.*.attendance_period_id' => 'required|exists:attendance_periods,id',
            'slots.*.room_id' => 'required|exists:rooms,id',
            'slots.*.staff_id' => 'required|exists:staff,id',
            'slots.*.subject_id' => 'required|exists:subjects,id',
        ]);

        $subjectIds = collect($request->slots)->pluck('subject_id')->unique()->values();
        $invalidSubjects = Subject::where('tenant_id', Auth::user()->tenant_id)
            ->whereIn('id', $subjectIds)
            ->where('type', '!=', 'practical')
            ->pluck('id');

        if ($invalidSubjects->isNotEmpty()) {
            return back()->withErrors([
                'slots' => 'Only practical subjects can be used in practical timetable slots.'
            ])->withInput();
        }

        $timetable = PracticalTimetable::create([
            'tenant_id' => Auth::user()->tenant_id,
            'practical_group_id' => $request->practical_group_id,
            'name' => $request->name,
        ]);

        foreach ($request->slots as $slot) {
            PracticalTimetableSlot::create([
                'tenant_id' => Auth::user()->tenant_id,
                'practical_timetable_id' => $timetable->id,
                'day_of_week' => $slot['day_of_week'],
                'attendance_period_id' => $slot['attendance_period_id'],
                'room_id' => $slot['room_id'],
                'staff_id' => $slot['staff_id'],
                'subject_id' => $slot['subject_id'],
            ]);
        }

        return redirect()
            ->route('practical_timetables.master', ['subject_id' => $request->input('subject_id')])
            ->with('success', 'Practical timetable created successfully.');
    }

    protected function authorizeSlotTenant(PracticalTimetableSlot $slot): void
    {
        if ($slot->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }
    }
}
