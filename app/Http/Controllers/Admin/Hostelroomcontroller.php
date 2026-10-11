<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hostelroom;
use App\Models\Hostelbed;
use App\Models\Hostel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Exception;

class HostelroomController extends Controller
{
    protected $roomListRoute = 'admin.hostelroom.index';

    // 🔹 Room List
    public function index()
    {
        $rooms = Hostelroom::where('tenant_id', auth()->user()->tenant_id)
            ->with('hostel')
            ->orderBy('room_number')
            ->paginate(10);

        return view('hostel.hostelroom.index', compact('rooms'));
    }

    // 🔹 Create Form
    public function create()
    {
        $hostelroom = null;
        $hostels = Hostel::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get()
            ->map(function($hostel) {
                // Add type information to each hostel
                $typeLabel = '';
                if ($hostel->type === 'boys') {
                    $typeLabel = 'Boys Hostel';
                } elseif ($hostel->type === 'girls') {
                    $typeLabel = 'Girls Hostel';
                } else {
                    $typeLabel = ucfirst($hostel->type ?? 'Hostel');
                }
                
                $hostel->display_name = $hostel->name . ' (' . $typeLabel . ')';
                return $hostel;
            });

        return view('hostel.hostelroom.form', compact('hostelroom', 'hostels'));
    }

    public function store(Request $request)
{
    $rules = [
        'room_number' => [
            'required',
            'max:50',
            function ($attribute, $value, $fail) use ($request) {
                $query = Hostelroom::where('tenant_id', auth()->user()->tenant_id)
                    ->where('hostel_id', $request->hostel_id)
                    ->where('room_number', $value);
                
                if ($request->id) {
                    $query->where('id', '!=', $request->id);
                }
                
                if ($query->exists()) {
                    $fail('Room number already exists in this hostel!');
                }
            },
        ],
        'hostel_id'   => 'required|exists:hostels,id',
        'type'        => 'required|in:ac,non_ac',
        'max_beds'    => 'required|integer|min:1|max:20'
    ];

    $messages = [
        'room_number.required' => 'Please enter room number!',
        'room_number.max' => 'Room number should be maximum of 50 characters!',
        'hostel_id.required' => 'Please select a hostel!',
        'type.required' => 'Please select room type!',
        'type.in' => 'Room type must be either AC or Non-AC!',
        'max_beds.required' => 'Please enter maximum beds!',
        'max_beds.min' => 'Maximum beds must be at least 1!',
        'max_beds.max' => 'Maximum beds cannot exceed 20!',
    ];

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator->errors()->first())
            ->withInput();
    }

    try {
        // Create room
        $room = new Hostelroom();
        $room->tenant_id = auth()->user()->tenant_id;
        $room->hostel_id = $request->hostel_id;
        $room->room_number = $request->room_number;
        $room->type = $request->type;
        $room->max_beds = $request->max_beds;
        $room->save();
        
        // 🔥 ADD THIS - CREATE ALL BEDS AUTOMATICALLY
        for($i = 1; $i <= $request->max_beds; $i++) {
            Hostelbed::create([
                'tenant_id' => auth()->user()->tenant_id,
                'room_id' => $room->id,
                'bed_number' => $i,
                'amount' => 0,
                'status' => 'vacant'
            ]);
        }
        
        // If beds data is submitted in the form, update them
        if($request->has('beds')) {
            foreach($request->beds as $bedData) {
                Hostelbed::where('room_id', $room->id)
                    ->where('bed_number', $bedData['bed_number'])
                    ->update([
                        'amount' => $bedData['amount'],
                        'status' => $bedData['status']
                    ]);
            }
        }
        
        return redirect()->route($this->roomListRoute)
            ->with('success', 'Room created successfully with ' . $request->max_beds . ' beds');
            
    } catch (Exception $e) {
        return redirect()->back()
            ->with('error', 'Something went wrong: ' . $e->getMessage());
    }
}
    
    // 🔹 Edit Form
   public function edit(Hostelroom $hostelroom)
{
    // Use route model binding - $hostelroom is already the model instance
    // Just load the relationship
    $hostelroom->load('beds');
    
    $this->authorizeTenant($hostelroom);

    $hostels = Hostel::where('tenant_id', auth()->user()->tenant_id)
        ->orderBy('name')
        ->get()
        ->map(function($hostel) {
            $typeLabel = '';
            if ($hostel->type === 'boys') {
                $typeLabel = 'Boys Hostel';
            } elseif ($hostel->type === 'girls') {
                $typeLabel = 'Girls Hostel';
            } else {
                $typeLabel = ucfirst($hostel->type ?? 'Hostel');
            }
            
            $hostel->display_name = $hostel->name . ' (' . $typeLabel . ')';
            return $hostel;
        });
        
    return view('hostel.hostelroom.form', compact('hostelroom', 'hostels'));
}
   
// 🔹 Update
public function update(Request $request, Hostelroom $hostelroom)
{
    $this->authorizeTenant($hostelroom);
    
    $rules = [
        'room_number' => [
            'required',
            'max:50',
            function ($attribute, $value, $fail) use ($request, $hostelroom) {
                $query = Hostelroom::where('tenant_id', auth()->user()->tenant_id)
                    ->where('hostel_id', $request->hostel_id)
                    ->where('room_number', $value)
                    ->where('id', '!=', $hostelroom->id);
                
                if ($query->exists()) {
                    $fail('Room number already exists in this hostel!');
                }
            },
        ],
        'hostel_id'   => 'required|exists:hostels,id',
        'type'        => 'required|in:ac,non_ac',
        'max_beds'    => 'required|integer|min:1|max:20'
    ];

    $messages = [
        'room_number.required' => 'Please enter room number!',
        'hostel_id.required' => 'Please select a hostel!',
        'type.required' => 'Please select room type!',
        'type.in' => 'Room type must be either AC or Non-AC!',
        'max_beds.required' => 'Please enter maximum beds!',
        'max_beds.min' => 'Maximum beds must be at least 1!',
        'max_beds.max' => 'Maximum beds cannot exceed 20!',
    ];

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator->errors()->first())
            ->withInput();
    }

    try {
        // Update room details
        $hostelroom->hostel_id   = $request->hostel_id;
        $hostelroom->room_number = $request->room_number;
        $hostelroom->type        = $request->type;
        $hostelroom->max_beds    = $request->max_beds;
        $hostelroom->save();
        
        // 🔥 ADD THIS CODE - HANDLE BEDS
        if($request->has('beds')) {
            foreach($request->beds as $bedData) {
                // Check if bed exists
                $bed = Hostelbed::where('room_id', $hostelroom->id)
                    ->where('bed_number', $bedData['bed_number'])
                    ->first();
                
                if($bed) {
                    // Update existing bed
                    $bed->update([
                        'amount' => $bedData['amount'],
                        'status' => $bedData['status']
                    ]);
                } else {
                    // Create new bed
                    Hostelbed::create([
                        'tenant_id' => auth()->user()->tenant_id,
                        'room_id' => $hostelroom->id,
                        'bed_number' => $bedData['bed_number'],
                        'amount' => $bedData['amount'] ?? 0,
                        'status' => $bedData['status'] ?? 'vacant'
                    ]);
                }
            }
        }
        
        // Optional: Delete beds that are not in the request (if max_beds decreased)
        $existingBedNumbers = Hostelbed::where('room_id', $hostelroom->id)
            ->pluck('bed_number')
            ->toArray();
        
        $submittedBedNumbers = array_column($request->beds ?? [], 'bed_number');
        
        $bedsToDelete = array_diff($existingBedNumbers, $submittedBedNumbers);
        if(!empty($bedsToDelete)) {
            Hostelbed::where('room_id', $hostelroom->id)
                ->whereIn('bed_number', $bedsToDelete)
                ->delete();
        }
        
        return redirect()->route('admin.hostelroom.index')
            ->with('success', 'Room updated successfully');
            
    } catch (Exception $e) {
        return redirect()->back()
            ->with('error', 'Something went wrong: ' . $e->getMessage());
    }
}

// 🔹 Delete Room
public function destroy(Hostelroom $hostelroom)
{
    $this->authorizeTenant($hostelroom);
    
    try {
        // First delete all beds associated with this room
        Hostelbed::where('room_id', $hostelroom->id)->delete();
        
        // Then delete the room
        $hostelroom->delete();
        
        return redirect()->route('admin.hostelroom.index')
            ->with('success', 'Room and its beds deleted successfully');
            
    } catch (Exception $e) {
        return redirect()->back()
            ->with('error', 'Cannot delete room: ' . $e->getMessage());
    }
}
    // 🔐 Tenant Protection
    protected function authorizeTenant(Hostelroom $hostelroom)
    {
        if ($hostelroom->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }
    }
}