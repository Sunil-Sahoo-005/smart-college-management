<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\TimetableSlot;
use App\Models\Timetable;
use App\Models\AttendancePeriod;
use App\Models\Subject;
use App\Models\Staff;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TimetableSlotController extends Controller
{
    protected $listRoute = 'admin.timetable_slots.index';

    public function index()
    {
        $slots = TimetableSlot::with(['timetable', 'period', 'subject', 'staff', 'room'])
            ->orderBy('timetable_id')
            ->orderBy('day_of_week')
            ->orderBy('period_id')
            ->paginate(15);

        return view('Academics.timetable_slots.index', compact('slots'));
    }

    public function create()
    {
        $timetables = Timetable::where('tenant_id', auth()->user()->tenant_id)
            ->with(['class', 'section', 'academicYear'])
            ->orderBy('name')
            ->get();

        $periods = AttendancePeriod::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('start_time')
            ->get();

        $subjects = Subject::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        $staff = Staff::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $rooms = Room::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        $daysOfWeek = [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];

        return view('Academics.timetable_slots.form', compact(
            'timetables',
            'periods',
            'subjects',
            'staff',
            'rooms',
            'daysOfWeek'
        ));
    }

    public function edit(TimetableSlot $slot)
    {
        $timetables = Timetable::where('tenant_id', auth()->user()->tenant_id)
            ->with(['class', 'section', 'academicYear'])
            ->orderBy('name')
            ->get();

        $periods = AttendancePeriod::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('start_time')
            ->get();

        $subjects = Subject::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        $staff = Staff::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        $rooms = Room::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        $daysOfWeek = [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];

        return view('Academics.timetable_slots.form', compact(
            'slot',
            'timetables',
            'periods',
            'subjects',
            'staff',
            'rooms',
            'daysOfWeek'
        ));
    }

    public function store(Request $request)
    {
        $rules = [
            'timetable_id' => 'required|exists:timetables,id',
            'day_of_week'  => 'required|integer|between:0,6',
            'period_id'    => 'required|exists:attendance_periods,id',
            'subject_id'   => 'required|exists:subjects,id',
            'staff_id'     => 'required|exists:staff,id',
            'room_id'      => 'required|exists:rooms,id',
        ];

        $messages = [
            'timetable_id.required' => 'Please select timetable!',
            'timetable_id.exists'   => 'Invalid timetable selected!',
            'day_of_week.required'  => 'Please select day of week!',
            'day_of_week.between'   => 'Day of week must be between 0 and 6!',
            'period_id.required'    => 'Please select period!',
            'period_id.exists'      => 'Invalid period selected!',
            'subject_id.required'   => 'Please select subject!',
            'subject_id.exists'     => 'Invalid subject selected!',
            'staff_id.required'     => 'Please select staff!',
            'staff_id.exists'       => 'Invalid staff selected!',
            'room_id.required'      => 'Please select room!',
            'room_id.exists'        => 'Invalid room selected!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator->errors()->first())
                ->withInput();
        }

        if (!empty($request->get('id'))) {
            $slot = TimetableSlot::where('id', $request->get('id'))
                ->whereHas('timetable', function ($q) {
                    $q->where('tenant_id', auth()->user()->tenant_id);
                })
                ->first();

            if (empty($slot)) {
                abort(404);
            }

            $redirect = [
                'url' => redirect()->back(),
                'msg' => 'Timetable slot updated successfully',
            ];
        } else {
            // Check if slot already exists
            $existing = TimetableSlot::where('timetable_id', $request->timetable_id)
                ->where('day_of_week', $request->day_of_week)
                ->where('period_id', $request->period_id)
                ->first();

            if ($existing) {
                return redirect()->back()->with('error', 'Timetable slot already exists for this timetable, day and period!');
            }

            $slot = new TimetableSlot;

            $redirect = [
                'url' => redirect($this->listRoute),
                'msg' => 'Timetable slot created successfully',
            ];
        }

        $slot->timetable_id = $request->timetable_id;
        $slot->day_of_week  = $request->day_of_week;
        $slot->period_id    = $request->period_id;
        $slot->subject_id   = $request->subject_id;
        $slot->staff_id     = $request->staff_id;
        $slot->room_id      = $request->room_id;

        try {
            $slot->save();
            return $redirect['url']->with('success', $redirect['msg']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong, please try again later!');
        }
    }

    public function destroy(TimetableSlot $slot)
    {
        if (!auth()->user()->tenant_id == $slot->timetable->tenant_id) {
            abort(403);
        }

        try {
            $slot->delete();
            return back()->with('success', 'Timetable slot deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete timetable slot, it may be in use.');
        }
    }
}
