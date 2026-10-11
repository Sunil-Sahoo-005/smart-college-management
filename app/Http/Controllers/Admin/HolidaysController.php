<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Holidays;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HolidaysController extends Controller
{
    protected $holidayListRoute = 'admin.holidays.index';

    // ✅ INDEX
    public function index()
    {
        $holidays = Holidays::where('tenant_id', auth()->user()->tenant_id)
            ->latest()
            ->get();

        $holidaysJson = $holidays->map(function ($h) {
            return [
                'id'             => $h->id,
                'name'           => $h->name,
                'date'           => $h->date, // ISO date for calendar
                'date_formatted' => \Carbon\Carbon::parse($h->date)->format('d M Y'),
                'description'    => $h->description ?? '—',
                'status'         => $h->status,
                'edit_url'       => route('admin.holidays.edit', $h),
                'delete_url'     => route('admin.holidays.destroy', $h),
            ];
        });

        return view('Academics.holidays.index', compact('holidays', 'holidaysJson'));
    }
    // ✅ CREATE
    public function create()
    {
        $holiday = null;
        return view('Academics.holidays.form', compact('holiday'));
    }

    // ✅ EDIT
    public function edit($id)
    {
        $holiday = Holidays::where('id', $id)
            ->where('tenant_id', auth()->user()->tenant_id)
            ->first();

        if (empty($holiday)) {
            abort(404);
        }

        return view('Academics.holidays.form', compact('holiday'));
    }

    // ✅ STORE + UPDATE
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'date'        => 'required|date',
            'status'      => 'required|in:active,inactive,cancelled',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // ✅ UPDATE
        if (!empty($request->get('id'))) {

            $holiday = Holidays::where('id', $request->get('id'))
                ->where('tenant_id', auth()->user()->tenant_id)
                ->first();

            if (empty($holiday)) {
                abort(404);
            }

            $redirect = [
                'url' => redirect()->back(),
                'msg' => 'Holidays updated successfully',
            ];

        } else {
            // ✅ CREATE
            $holiday = new Holidays;
            $holiday->tenant_id = auth()->user()->tenant_id;

            $redirect = [
                'url' => redirect()->route($this->holidayListRoute),
                'msg' => 'Holidays created successfully',
            ];
        }

        // ✅ SAVE DATA
        $holiday->name = $request->get('name');
        $holiday->description = $request->get('description');
        $holiday->date = $request->get('date');
        $holiday->status = $request->get('status');

        try {
            $holiday->save();
            return $redirect['url']->with('success', $redirect['msg']);

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['Something went wrong, please try again later!']);
        }
    }

    // ✅ DELETE
    public function destroy($id)
    {
        $holiday = Holidays::where('id', $id)
            ->where('tenant_id', auth()->user()->tenant_id)
            ->first();

        if (empty($holiday)) {
            abort(404);
        }

        try {
            $holiday->delete();
            return redirect()->back()->with('success', 'Holidays deleted successfully');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong, please try again later!');
        }
    }
}