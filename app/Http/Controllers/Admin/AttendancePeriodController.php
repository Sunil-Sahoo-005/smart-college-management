<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\AttendancePeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttendancePeriodController extends Controller
{
    protected $attendancePeriodListRoute = 'admin.attendance_periods.index';

    public function index()
{
    $attendancePeriods = AttendancePeriod::where('tenant_id', auth()->user()->tenant_id)
        ->orderBy('start_time')
        ->get();

    $attendancePeriodsJson = $attendancePeriods->map(function ($ap) {
        return [
            'id'         => $ap->id,
            'name'       => $ap->name,
            'start_time' => date('H:i', strtotime($ap->start_time)),
            'end_time'   => date('H:i', strtotime($ap->end_time)),
            'edit_url'   => route('admin.attendance_periods.edit', $ap),
            'delete_url' => route('admin.attendance_periods.destroy', $ap),
        ];
    });

    return view('Academics.attendance_periods.index', compact('attendancePeriods', 'attendancePeriodsJson'));
}

    public function create()
    {
        return view('Academics.attendance_periods.form');
    }

   public function edit(AttendancePeriod $attendancePeriod)
{
    $this->authorizeTenant($attendancePeriod);
    
    // Agar time string hai toh format karein
    $attendancePeriod->start_time = date('H:i', strtotime($attendancePeriod->start_time));
    $attendancePeriod->end_time = date('H:i', strtotime($attendancePeriod->end_time));

    return view('Academics.attendance_periods.form', compact('attendancePeriod'));
}

    public function store(Request $request)
    {
        // dd($request->all());
        $rules = [
            'name'        => 'required|min:1|max:100|unique:attendance_periods,name,' . $request->get('id') . ',id,tenant_id,' . auth()->user()->tenant_id,
            'start_time'  => 'required|date_format:H:i',
            'end_time'    => 'required|date_format:H:i|after:start_time',
        ];

        $messages = [
            'name.required'      => 'Please enter period name!',
            'name.min'           => 'Period name should be minimum of 1 character!',
            'name.max'           => 'Period name should be maximum of 100 characters!',
            'name.unique'        => 'Period name already exists!',
            'start_time.required' => 'Please enter start time!',
            'start_time.date_format' => 'Start time must be in HH:MM format!',
            'end_time.required'   => 'Please enter end time!',
            'end_time.date_format' => 'End time must be in HH:MM format!',
            'end_time.after'      => 'End time must be after start time!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            return redirect()->back()->withErrors($errorMessage)->withInput();
        }

        if (!empty($request->get('id'))) {
                    // dd($request->all());

            $attendancePeriod = AttendancePeriod::where('id', $request->get('id'))
                ->where('tenant_id', auth()->user()->tenant_id)
                ->first();

            if (empty($attendancePeriod)) {
                abort(404);
            }

            $redirect = [
                'url' => redirect()->back(),
                'msg' => 'Attendance period updated successfully',
            ];
        } else {
            $attendancePeriod = new AttendancePeriod;
            $attendancePeriod->tenant_id = auth()->user()->tenant_id;

            $redirect = [
                'url' => redirect()->route($this->attendancePeriodListRoute),
                'msg' => 'Attendance period created successfully',
            ];
        }

        $attendancePeriod->name       = trim($request->get('name'));
        $attendancePeriod->start_time = $request->get('start_time');
        $attendancePeriod->end_time   = $request->get('end_time');

        try {
            $attendancePeriod->save();

            return $redirect['url']->with('success', $redirect['msg']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong, please try again later!');
        }
    }

    public function destroy(AttendancePeriod $attendancePeriod)
    {
        $this->authorizeTenant($attendancePeriod);

        try {
            $attendancePeriod->delete();
            return back()->with('success', 'Attendance period deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete attendance period, it may be in use.');
        }
    }

    protected function authorizeTenant(AttendancePeriod $attendancePeriod)
    {
        if ($attendancePeriod->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }
    }
}
