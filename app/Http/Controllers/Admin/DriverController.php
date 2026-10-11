<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver;

class DriverController extends Controller
{
    protected $driverListRoute = 'admin.driver.index';

    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            $driver = Driver::orderBy('first_name')->paginate(10);
        } else {
            $driver = Driver::where(function($query) use ($user) {
                $query->whereNull('tenant_id')
                      ->orWhere('tenant_id', $user->tenant_id);
            })->orderBy('first_name')->paginate(10);
        }

        $driverJson = $driver->map(function ($v) {
            return [
                'id'             => $v->id,
                'first_name'     => $v->first_name,
                'last_name'      => $v->last_name,
                'full_name'      => $v->first_name . ' ' . $v->last_name,
                'phone'          => $v->phone,
                'license_number' => $v->license_number ?? 'N/A',
                'edit_url'       => route('admin.driver.edit', $v->id),
                'delete_url'     => route('admin.driver.destroy', $v->id),
            ];
        });

        return view('Travels.driver.index', compact('driver', 'driverJson'));
    }

    public function create()
    {
        $driver = null;
        $tenant_id = Driver::whereNull('tenant_id')
                    ->orWhere('tenant_id', auth()->user()->tenant_id)
                    ->orderBy('first_name')->get();

        return view('Travels.driver.form', compact('driver', 'tenant_id'));
    }

    public function edit(Driver $driver)
    {
        $this->authorizeTenant($driver);

        $tenant_id = Driver::whereNull('tenant_id')
                    ->orWhere('tenant_id', auth()->user()->tenant_id)
                    ->orderBy('first_name')->get();

        return view('Travels.driver.form', compact('driver', 'tenant_id'));
    }

    public function store(Request $request)
    {
        $id = $request->get('id');

        $request->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'phone'          => 'required|digits_between:10,15|unique:drivers,phone' . (!empty($id) ? ',' . $id : ''),
            'license_number' => 'required|string|max:50|unique:drivers,license_number' . (!empty($id) ? ',' . $id : ''),
        ], [
            'first_name.required'     => 'First name is required.',
            'first_name.max'          => 'First name must not exceed 100 characters.',
            'last_name.required'      => 'Last name is required.',
            'last_name.max'           => 'Last name must not exceed 100 characters.',
            'phone.required'          => 'Phone number is required.',
            'phone.digits_between'    => 'Phone number must be between 10 and 15 digits.',
            'phone.unique'            => 'This phone number is already registered.',
            'license_number.required' => 'License number is required.',
            'license_number.max'      => 'License number must not exceed 50 characters.',
            'license_number.unique'   => 'This license number is already registered.',
        ]);

        if (!empty($id)) {
            $driver = Driver::where('id', $id)
                ->where(function($query) {
                    $query->whereNull('tenant_id')
                          ->orWhere('tenant_id', auth()->user()->tenant_id);
                })->first();

            if (empty($driver)) {
                abort(404);
            }

            $redirect = [
                'url' => redirect()->back(),
                'msg' => 'Driver updated successfully.',
            ];
        } else {
            $driver = new Driver;

            $driver->tenant_id = auth()->user()->hasRole('super_admin')
                                  ? null
                                  : auth()->user()->tenant_id;

            $redirect = [
                'url' => redirect()->route($this->driverListRoute),
                'msg' => 'Driver created successfully.',
            ];
        }

        $driver->first_name     = $request->get('first_name');
        $driver->last_name      = $request->get('last_name');
        $driver->phone          = $request->get('phone');
        $driver->license_number = $request->get('license_number');

        try {
            $driver->save();
            return $redirect['url']->with('success', $redirect['msg']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong, please try again later.');
        }
    }

    public function destroy(Driver $driver)
    {
        $this->authorizeTenant($driver);

        try {
            $driver->delete();
            return back()->with('success', 'Driver deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete driver, it may be in use.');
        }
    }

    protected function authorizeTenant(Driver $driver)
    {
        if ($driver->tenant_id !== null &&
            $driver->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }
    }
}