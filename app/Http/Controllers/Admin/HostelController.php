<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hostel;
use App\Models\Designation;
use App\Models\Department;
use App\Models\Hostelroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class HostelController extends Controller
{
    protected $hostelListRoute = 'admin.hostel.index';

   // 🔹 Hostel List
public function index()
{
    $hostelsByType = Hostel::with(['rooms' => function($q) {
            $q->withCount([
                'beds',
                'beds as vacant_beds_count'   => fn($q) => $q->where('status', 'vacant'),
                'beds as occupied_beds_count' => fn($q) => $q->where('status', 'occupied'),
            ]);
        }])
        ->where('tenant_id', auth()->user()->tenant_id)
        ->orderBy('name')
        ->get()
        ->groupBy('type');

    return view('hostel.hostel.index', compact('hostelsByType'));
}
    // 🔹 Create Form
    public function create()
    {   
        $hostel = null;
        $designations = Designation::where('tenant_id', auth()->user()->tenant_id)->orderBy('name')->get();
        $departments = Department::where('tenant_id', auth()->user()->tenant_id)
                ->orderBy('name')
                ->get();
        return view('hostel.hostel.form', compact('hostel','designations','departments'));
    }

    // 🔹 Edit Form
    public function edit(Hostel $hostel)
    {
        $this->authorizeTenant($hostel);
        $designations = Designation::where('tenant_id', auth()->user()->tenant_id)
                    ->orderBy('name')
                    ->get();
        $departments = Department::where('tenant_id', auth()->user()->tenant_id)
                ->orderBy('name')
                ->get();
        return view('hostel.hostel.form', compact('hostel','designations','departments'));
    }

    // 🔹 Store (Create + Update) with new fields
    public function store(Request $request)
    {
        $rules = [
            'name' => [
                'required',
                'max:100',
                function ($attribute, $value, $fail) use ($request) {
                    $query = Hostel::where('tenant_id', auth()->user()->tenant_id)
                        ->where('name', $value)
                        ->where('type', $request->type);
                    
                    if ($request->id) {
                        $query->where('id', '!=', $request->id);
                    }
                    
                    if ($query->exists()) {
                        $fail('A hostel with this name and type already exists!');
                    }
                },
            ],
            'type' => 'nullable|max:50',
            'address' => 'nullable|max:255',
            'contact_phone' => [
                'nullable',
                'string',
                'regex:/^[0-9]{10}$/',
                'size:10'
            ],
            // New fields
            'caretaker' => 'nullable|string|max:255',
            'caretaker_phone' => 'nullable|string|max:20',
            'code_of_conduct' => 'nullable|string',
        ];

        $messages = [
            'name.required' => 'Please enter hostel name!',
            'name.max' => 'Hostel name should be maximum of 100 characters!',
            'contact_phone.regex' => 'Contact phone must be exactly 10 digits!',
            'contact_phone.size' => 'Contact phone must be exactly 10 digits!',
            'contact_phone.string' => 'Contact phone must contain only numbers!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors()->first())->withInput();
        }
        
        if ($request->id) {
            $hostel = Hostel::where('id', $request->id)
                ->where('tenant_id', auth()->user()->tenant_id)
                ->firstOrFail();

            $redirect = [
                'url' => redirect()->back(),
                'msg' => 'Hostel updated successfully',
            ];
        } 
        else {
            $hostel = new Hostel;
            $hostel->tenant_id = auth()->user()->tenant_id;
            $redirect = [
                'url' => redirect()->route($this->hostelListRoute),
                'msg' => 'Hostel created successfully',
            ];
        }

        // Assign basic fields
        $hostel->name = $request->name;
        $hostel->type = $request->type;
        $hostel->address = $request->address;
        $hostel->contact_phone = $request->contact_phone;

        // Assign new fields
        $hostel->caretaker = $request->caretaker;
        $hostel->caretaker_phone = $request->caretaker_phone;
        $hostel->code_of_conduct = $request->code_of_conduct;

        try {
            $hostel->save();
            return $redirect['url']->with('success', $redirect['msg']);
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;

            return redirect()->back()
                ->withErrors('Something went wrong, please try again later!');
        }
    }

    // 🔹 Show hostel details (already includes rooms and beds)
    public function show($id)
    {
        $hostel = Hostel::findOrFail($id);

        $rooms = Hostelroom::with([
            'beds.currentStudent.student.user'
        ])
        ->where('hostel_id', $id)
        ->orderByRaw('CAST(room_number AS UNSIGNED)')
        ->orderBy('room_number')
        ->get();

        $rooms->each(function ($room) {
            $totalBeds    = $room->beds->count();
            $occupiedBeds = $room->beds->where('status', 'occupied')->count();

            if ($occupiedBeds == 0) {
                $room->status_text  = 'Available';
                $room->status_class = 'available';
            } elseif ($occupiedBeds == $totalBeds) {
                $room->status_text  = 'Full';
                $room->status_class = 'occupied';
            } else {
                $room->status_text  = 'Partially Occupied';
                $room->status_class = 'partial';
            }

            $room->occupied_beds_count  = $occupiedBeds;
            $room->total_beds_count     = $totalBeds;
            $room->available_beds_count = $totalBeds - $occupiedBeds;
        });

        return view('hostel.hostel.show', compact('hostel', 'rooms'));
    }

    // 🔹 Delete hostel
    public function destroy(Hostel $hostel)
    {
        $this->authorizeTenant($hostel);

        try {
            $hostel->delete();
            return back()->with('success', 'Hostel deleted successfully.');
        } catch (Exception $e) {
            return back()->with('error', 'Cannot delete hostel, it may be in use.');
        }
    }

    // 🔐 Tenant Security
    protected function authorizeTenant(Hostel $hostel)
    {
        if ($hostel->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }
    }
}