<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Models\Vehicle;
use App\Models\Driver;

class VehicleController extends Controller
{
    protected $vehicleListRoute = 'admin.vehicle.index';

    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            $query = Vehicle::with('driver');
        } else {
            // Global + apne college ke vehicles ✅
            $query = Vehicle::where(function($q) use ($user) {
                $q->whereNull('tenant_id')
                  ->orWhere('tenant_id', $user->tenant_id);
            })->with('driver');
        }

        if ($request->filled('search')) {
            $query->where('registration_number', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('driver')) {
            if ($request->driver === 'assigned') {
                $query->whereNotNull('driver_id');
            } elseif ($request->driver === 'unassigned') {
                $query->whereNull('driver_id');
            }
        }

        $vehicle = $query->orderBy('registration_number')->paginate(10)->withQueryString();

        // Global + apne college ke types ✅
        $types = Vehicle::where(function($q) use ($user) {
                $q->whereNull('tenant_id')
                  ->orWhere('tenant_id', $user->tenant_id);
            })->distinct()->pluck('type')->filter()->sort()->values();

        return view('Travels.vehicle.index', compact('vehicle', 'types'));
    }

   public function create()
{
    $vehicle = null;
    $user = auth()->user();

    $assignedDriverIds = Vehicle::whereNotNull('driver_id')->pluck('driver_id');

    $drivers = Driver::where(function($q) use ($user) {
            $q->whereNull('tenant_id')
              ->orWhere('tenant_id', $user->tenant_id);
        })->whereNotIn('id', $assignedDriverIds)
          ->orderBy('first_name')
          ->get();

    // ✅ Pehle $usedLicences define karo
    $usedLicences = Vehicle::whereNotNull('registration_number')
        ->pluck('registration_number');

    // ✅ Tab use karo
    $driverLicenses = Driver::where(function($q) use ($user) {
            $q->whereNull('tenant_id')
              ->orWhere('tenant_id', $user->tenant_id);
        })->whereNotNull('license_number')
          ->whereNotIn('license_number', $usedLicences)
          ->pluck('license_number');

    return view('Travels.vehicle.form', compact('vehicle', 'drivers', 'driverLicenses'));
}

  public function edit(Vehicle $vehicle)
{
    $this->authorizeTenant($vehicle);

    $user = auth()->user();

    $assignedDriverIds = Vehicle::whereNotNull('driver_id')
        ->where('id', '!=', $vehicle->id)
        ->pluck('driver_id');

    // ✅ $driver -> $drivers (blade ke saath match karo)
    $drivers = Driver::where(function($q) use ($user) {
            $q->whereNull('tenant_id')
              ->orWhere('tenant_id', $user->tenant_id);
        })->whereNotIn('id', $assignedDriverIds)
          ->orderBy('first_name')
          ->get();

    $usedLicences = Vehicle::whereNotNull('registration_number')
        ->where('id', '!=', $vehicle->id)
        ->pluck('registration_number');

    $driverLicenses = Driver::where(function($q) use ($user) {
            $q->whereNull('tenant_id')
              ->orWhere('tenant_id', $user->tenant_id);
        })->whereNotNull('license_number')
          ->whereNotIn('license_number', $usedLicences)
          ->pluck('license_number');

    // ✅ $driver -> $drivers
    return view('Travels.vehicle.form', compact('vehicle', 'drivers', 'driverLicenses'));
}

    public function store(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'vehicle_number' => [
                'required', 'string', 'max:20',
                Rule::unique('vehicles', 'vehicle_number')
                    ->where('tenant_id', $user->tenant_id)
                    ->ignore($request->get('id')),
            ],
            'registration_number' => [
                'required', 'string', 'max:50',
                Rule::exists('drivers', 'license_number'),
                Rule::unique('vehicles', 'registration_number')
                    ->where('tenant_id', $user->tenant_id)
                    ->ignore($request->get('id')),
            ],
            'type'      => 'required|in:Bus,Minivan,van',
            'capacity'  => 'nullable|integer|min:1|max:100',
            'driver_id' => 'nullable|exists:drivers,id',
        ];

        $messages = [
            'vehicle_number.required'      => 'Please enter vehicle number!',
            'vehicle_number.unique'        => 'This vehicle number already exists!',
            'registration_number.required' => 'Please enter registration number!',
            'registration_number.unique'   => 'This registration number already exists!',
            'registration_number.exists'   => 'Licence number must match an existing driver!',
            'type.required'                => 'Please select vehicle type!',
            'capacity.integer'             => 'Capacity must be a number!',
            'driver_id.exists'             => 'Selected driver is invalid!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if (!empty($request->get('id'))) {
            $vehicle = Vehicle::where('id', $request->get('id'))
                ->where(function($q) use ($user) {
                    $q->whereNull('tenant_id')
                      ->orWhere('tenant_id', $user->tenant_id);
                })->first();

            if (empty($vehicle)) {
                abort(404);
            }

            $redirect = [
                'url' => redirect()->back(),
                'msg' => 'Vehicle updated successfully',
            ];
        } else {
            $vehicle = new Vehicle;

            // Super admin = global (NULL), College = apna tenant ✅
            $vehicle->tenant_id = $user->hasRole('super_admin')
                                   ? null
                                   : $user->tenant_id;

            $redirect = [
                'url' => redirect()->route($this->vehicleListRoute),
                'msg' => 'Vehicle created successfully',
            ];
        }

        $vehicle->vehicle_number      = strtoupper($request->vehicle_number);
        $vehicle->registration_number = $request->registration_number;
        $vehicle->type                = $request->type;
        $vehicle->capacity            = $request->capacity;
        $vehicle->driver_id           = $request->driver_id;

        try {
            $vehicle->save();
            return $redirect['url']->with('success', $redirect['msg']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong, please try again later!');
        }
    }

    public function destroy(Vehicle $vehicle)
    {
        $this->authorizeTenant($vehicle);

        try {
            $vehicle->delete();
            return back()->with('success', 'Vehicle deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete vehicle, it may be in use.');
        }
    }

    protected function authorizeTenant(Vehicle $vehicle)
    {
        // NULL = global vehicle — sabko access milega ✅
        if ($vehicle->tenant_id !== null &&
            $vehicle->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }
    }
}