<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hostel;
use App\Models\Hostelbed;
use App\Models\Hostelroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HostelbedController extends Controller
{
    protected $bedListRoute = 'admin.hostelbed.index';

    // 🔹 Bed List
    public function index()
    {
        $beds = Hostelbed::with('room')
            ->where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('bed_number')
            ->paginate(10);

        return view('hostel.hostelbed.index', compact('beds'));
    }

    // 🔹 Create Form
    public function create()
    {
        // $hostelbed = null;
        // $rooms = HostelRoom::where('tenant_id', auth()->user()->tenant_id)
        //     ->orderBy('room_number')
        //     ->get();
        $hostelbed = null;
        $hostels = Hostel::where('tenant_id', auth()->user()->tenant_id)->get();
        $rooms = collect(); // Empty initially

        return view('hostel.hostelbed.form', compact('hostelbed', 'rooms', 'hostels'));
    }

    // 🔹 Edit Form
    public function edit(Hostelbed $hostelbed)
    {
        // dd(78);
        $this->authorizeTenant($hostelbed);
        // $rooms = HostelRoom::where('tenant_id', auth()->user()->tenant_id)
        //     ->orderBy('room_number')
        //     ->get();
        $hostels = Hostel::where('tenant_id', auth()->user()->tenant_id)->get();
        $rooms = Hostelroom::where('tenant_id', auth()->user()->tenant_id)
            ->where('hostel_id', $hostelbed->room->hostel_id)
            ->orderBy('room_number')
            ->get();

        return view('hostel.hostelbed.form', compact('hostelbed', 'rooms', 'hostels'));
    }
    public function getRoomsByHostel($hostelId)
    {
        $rooms = Hostelroom::where('tenant_id', auth()->user()->tenant_id)
            ->where('hostel_id', $hostelId)
            ->orderBy('room_number')
            ->get();

        return response()->json($rooms);
    }

    // 🔹 Store (Create + Update)
    public function store(Request $request)
    {
        //    dd($request->all());

        $rules = [
            'room_id'    => 'required|exists:hostel_rooms,id',
            'bed_number' => 'required|max:50',
            'status'     => 'required|in:vacant,occupied',
            'amount'     => 'required|numeric|min:0',
        ];

        $messages = [
            'room_id.required'    => 'Please select room!',
            'bed_number.required' => 'Please enter bed number!',
            'bed_number.max'      => 'Bed number max 50 characters!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        // if ($validator->fails()) {
        //     return redirect()->back()
        //         ->withErrors($validator->errors()->first())
        //         ->withInput();
        // }

        if ($request->id) {
            // dd(45);
            $hostelbed = Hostelbed::where('id', $request->id)
                ->where('tenant_id', auth()->user()->tenant_id)
                ->firstOrFail();

            $redirect = [
                'url' => redirect()->back(),
                'msg' => 'Bed updated successfully',
            ];
        } else {
            $hostelbed = new Hostelbed();
            $hostelbed->tenant_id = auth()->user()->tenant_id;
            $redirect = [
                'url' => redirect()->route($this->bedListRoute),
                'msg' => 'Bed created successfully',
            ];
        }

        $hostelbed->room_id    = $request->room_id;
        $hostelbed->bed_number = $request->bed_number;
        $hostelbed->status     = $request->status;
        $hostelbed->amount     = $request->amount;

        try {
            $hostelbed->save();
            return $redirect['url']
                ->with('success', $redirect['msg']);
        } catch (\Exception $e) {
            echo $e->getMessage();exit;
            return redirect()->back()
                ->withErrors('Something went wrong, please try again!');
            // ->withInput();
        }
    }

    // 🔹 Delete
    public function destroy(Hostelbed $hostelbed)
    {
        $this->authorizeTenant($hostelbed);

        try {
            $hostelbed->delete();
            return back()->with('success', 'Bed deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete bed, it may be in use.');
        }
    }

    // 🔐 Tenant Security
    protected function authorizeTenant(Hostelbed $hostelbed)
    {
        if ($hostelbed->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }
    }
}
