<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hostel;
use App\Models\HostelAllocation;
use App\Models\Student;
use App\Models\Hostelroom;
use App\Models\Hostelbed;
use App\Models\FeeCategory;
use App\Models\FeeInstallment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class HostelAllocationController extends Controller
{
    protected $allocationListRoute = 'admin.hostelallocation.index';

    // List
    public function index()
    {
        $allocations = HostelAllocation::with(['student', 'room', 'bed'])
            ->where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('hostel.hostelallocation.index', compact('allocations'));
    }

    // Create Form
    public function create()
    {
        $allocation = null;
        $students   = Student::where('tenant_id', auth()->user()->tenant_id)->get();
        $rooms      = Hostelroom::where('tenant_id', auth()->user()->tenant_id)->get();
        $beds       = Hostelbed::where('tenant_id', auth()->user()->tenant_id)->get();
        $hostels    = Hostel::where('tenant_id', auth()->user()->tenant_id)->get();

        return view('hostel.hostelallocation.form', compact('allocation', 'students', 'rooms', 'beds', 'hostels'));
    }

    // Store / Update
    public function store(Request $request)
    {
        $rules = [
            'student_id' => 'required|exists:students,id',
            'room_id'    => 'required|exists:hostel_rooms,id',
            'bed_id'     => 'required|exists:hostel_beds,id',
            'from_date'  => 'required|date',
            'to_date'    => 'nullable|date|after_or_equal:from_date',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator->errors()->first())->withInput();
        }

        DB::transaction(function () use ($request) {
            if ($request->id) {
                // ── UPDATE ──────────────────────────────────────────
                $allocation = HostelAllocation::findOrFail($request->id);

                // Bed status update karo agar bed change hua
                if ($allocation->bed_id != $request->bed_id) {
                    Hostelbed::where('id', $allocation->bed_id)->update(['status' => 'vacant']);
                    Hostelbed::where('id', $request->bed_id)->update(['status' => 'occupied']);
                }

                $allocation->update([
                    'student_id' => $request->student_id,
                    'room_id'    => $request->room_id,
                    'bed_id'     => $request->bed_id,
                    'from_date'  => $request->from_date,
                    'to_date'    => $request->to_date,
                ]);

                // FeeInstallment update karo agar exist karta hai
                $room = Hostelroom::findOrFail($request->room_id);
                $bed  = Hostelbed::findOrFail($request->bed_id);

                $existingFee = FeeInstallment::where('tenant_id', auth()->user()->tenant_id)
                    ->where('student_id', $request->student_id)
                    ->whereHas('feeCategory', fn($q) => $q->where('name', 'Hostel Fee'))
                    ->latest()
                    ->first();

                if ($existingFee) {
                    $existingFee->update([
                        'student_id'  => $request->student_id,
                        'amount'      => $bed->amount,
                        'due_date'    => $request->to_date
                                            ? Carbon::parse($request->to_date)
                                            : Carbon::parse($request->from_date)->endOfMonth(),
                        'reason'      => 'Hostel Fee - Room ' . $room->room_number,
                        'description' => 'Hostel Fee - Room ' . $room->room_number,
                    ]);
                }

            } else {
                // ── CREATE ──────────────────────────────────────────
                $room = Hostelroom::findOrFail($request->room_id);
                $bed  = Hostelbed::findOrFail($request->bed_id);

                Hostelbed::where('id', $request->bed_id)->update(['status' => 'occupied']);

                $allocation = new HostelAllocation([
                    'tenant_id'  => auth()->user()->tenant_id,
                    'student_id' => $request->student_id,
                    'hostel_id'  => $room->hostel_id,
                    'room_id'    => $request->room_id,
                    'bed_id'     => $request->bed_id,
                    'from_date'  => $request->from_date,
                    'to_date'    => $request->to_date,
                ]);
                $allocation->save();

                // FeeCategory firstOrCreate
                $hostelFeeCategory = FeeCategory::firstOrCreate(
                    [
                        'tenant_id' => auth()->user()->tenant_id,
                        'name'      => 'Hostel Fee',
                    ],
                    [
                        'description' => 'Auto created Hostel Fee category',
                    ]
                );

                // FeeInstallment create karo
                FeeInstallment::create([
                    'tenant_id'       => auth()->user()->tenant_id,
                    'student_id'      => $request->student_id,
                    'fee_category_id' => $hostelFeeCategory->id,
                    'amount'          => $bed->amount,
                    'paid_amount'     => 0,
                    'due_date'        => $request->to_date
                                            ? Carbon::parse($request->to_date)
                                            : Carbon::parse($request->from_date)->endOfMonth(),
                    'status'          => 'unpaid',
                    'reason'          => 'Hostel Fee - Room ' . $room->room_number,
                    'description'     => 'Hostel Fee - Room ' . $room->room_number,
                ]);
            }
        });

        return redirect()->route($this->allocationListRoute)
            ->with('success', 'Hostel allocation saved successfully');
    }

    // Student ki existing allocation fetch karo (AJAX)
    public function getStudentAllocation($studentId)
    {
        $allocation = HostelAllocation::where('tenant_id', auth()->user()->tenant_id)
            ->where('student_id', $studentId)
            ->with(['room', 'bed', 'hostel'])
            ->latest()
            ->first();

        if (!$allocation) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found'      => true,
            'hostel_id'  => $allocation->hostel_id,
            'room_id'    => $allocation->room_id,
            'bed_id'     => $allocation->bed_id,
            'from_date'  => $allocation->from_date,
            'to_date'    => $allocation->to_date,
        ]);
    }

    public function getRoomsByHostel($hostelId)
    {
        $rooms = Hostelroom::where('tenant_id', auth()->user()->tenant_id)
            ->where('hostel_id', $hostelId)
            ->orderBy('room_number')
            ->get();

        return response()->json($rooms);
    }

    public function getBedsByRoom($roomId)
    {
        $beds = Hostelbed::where('tenant_id', auth()->user()->tenant_id)
            ->where('room_id', $roomId)
            ->orderBy('bed_number')
            ->get();

        return response()->json($beds);
    }

    public function edit($id)
    {
        $allocation = HostelAllocation::where('id', $id)
            ->where('tenant_id', auth()->user()->tenant_id)
            ->with(['room.hostel', 'bed'])
            ->firstOrFail();

        $students = Student::where('tenant_id', auth()->user()->tenant_id)->get();
        $hostels  = Hostel::where('tenant_id', auth()->user()->tenant_id)->get();

        return view('hostel.hostelallocation.form', compact('allocation', 'students', 'hostels'));
    }

    public function destroy($id)
{
    $allocation = HostelAllocation::where('id', $id)
        ->where('tenant_id', auth()->user()->tenant_id)
        ->firstOrFail();

    DB::transaction(function () use ($allocation) {
        Hostelbed::where('id', $allocation->bed_id)->update(['status' => 'vacant']);

        // ✅ FeeInstallment bhi delete karo
        FeeInstallment::where('tenant_id', auth()->user()->tenant_id)
            ->where('student_id', $allocation->student_id)
            ->whereHas('feeCategory', fn($q) => $q->where('name', 'Hostel Fee'))
            ->latest()
            ->first()
            ?->delete();

        $allocation->delete();
    });

    return back()->with('success', 'Allocation deleted successfully');
}

    protected function authorizeTenant(HostelAllocation $allocation)
    {
        if ($allocation->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }
    }
}