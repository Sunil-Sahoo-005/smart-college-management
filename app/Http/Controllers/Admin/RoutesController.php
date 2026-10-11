<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Routes;
use App\Models\Vehicle;

class RoutesController extends Controller
{
    protected $routesListRoute = 'admin.routes.index';

    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            $query = Routes::with('vehicle');
        } else {
            // Global + apne college ke routes ✅
            $query = Routes::with('vehicle')
                ->where(function($q) use ($user) {
                    $q->whereNull('tenant_id')
                      ->orWhere('tenant_id', $user->tenant_id);
                });
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->filled('start_point')) {
            $query->where('start_point', 'like', '%' . $request->start_point . '%');
        }

        if ($request->filled('end_point')) {
            $query->where('end_point', 'like', '%' . $request->end_point . '%');
        }

        $routes = $query->orderBy('name')->paginate(10)->withQueryString();

        // Global + apne college ke vehicles ✅
        $vehicles = Vehicle::where(function($q) use ($user) {
                $q->whereNull('tenant_id')
                  ->orWhere('tenant_id', $user->tenant_id);
            })->orderBy('registration_number')->get();

        return view('Travels.routes.index', compact('routes', 'vehicles'));
    }

    public function create()
    {
        $routes = null;
        $user = auth()->user();

        // Global + apne college ke vehicles ✅
        $vehicle = Vehicle::where(function($q) use ($user) {
                $q->whereNull('tenant_id')
                  ->orWhere('tenant_id', $user->tenant_id);
            })->orderBy('registration_number')->get();

        return view('Travels.routes.create', compact('routes', 'vehicle'));
    }

    public function edit($id)
    {
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            $routes = Routes::with('vehicle', 'stops')
                ->where('id', $id)
                ->firstOrFail();
        } else {
            // Global + apne college ka route edit kar sake ✅
            $routes = Routes::with('vehicle', 'stops')
                ->where('id', $id)
                ->where(function($q) use ($user) {
                    $q->whereNull('tenant_id')
                      ->orWhere('tenant_id', $user->tenant_id);
                })->firstOrFail();
        }

        $this->authorizeTenant($routes);

        // Global + apne college ke vehicles ✅
        $vehicle = Vehicle::where(function($q) use ($user) {
                $q->whereNull('tenant_id')
                  ->orWhere('tenant_id', $user->tenant_id);
            })->orderBy('registration_number')->get();

        return view('Travels.routes.create', compact('routes', 'vehicle'));
    }

    public function show($id)
    {
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            $route = Routes::with(['vehicle', 'stops' => function($q) {
                $q->orderBy('order');
            }])->where('id', $id)->firstOrFail();
        } else {
            // Global + apne college ka route ✅
            $route = Routes::with(['vehicle', 'stops' => function($q) {
                $q->orderBy('order');
            }])->where('id', $id)
              ->where(function($q) use ($user) {
                  $q->whereNull('tenant_id')
                    ->orWhere('tenant_id', $user->tenant_id);
              })->firstOrFail();
        }

        return view('Travels.routes.show', compact('route'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (!empty($request->get('id'))) {
            $routes = Routes::where('id', $request->get('id'))
                ->where(function($q) use ($user) {
                    $q->whereNull('tenant_id')
                      ->orWhere('tenant_id', $user->tenant_id);
                })->first();

            if (empty($routes)) {
                abort(404);
            }

            $redirect = [
                'url' => redirect()->route($this->routesListRoute),
                'msg' => 'Routes updated successfully',
            ];
        } else {
            $routes = new Routes;

            // Super admin = global (NULL), College = apna tenant ✅
            $routes->tenant_id = $user->hasRole('super_admin')
                                  ? null
                                  : $user->tenant_id;

            $redirect = [
                'url' => redirect()->route($this->routesListRoute),
                'msg' => 'Routes created successfully',
            ];
        }

        $routes->name           = $request->get('name');
        $routes->description    = $request->get('description');
        $routes->vehicle_id     = $request->get('vehicle_id');
        $routes->route_distance = $request->get('route_distance');
        $routes->route_duration = $request->get('route_duration');

        $stops = $request->get('stops', []);
        if (!empty($stops)) {
            $first = $stops[0];
            $last  = $stops[array_key_last($stops)];

            $routes->start_point = $first['name'] ?? null;
            $routes->start_lat   = $first['lat']  ?? null;
            $routes->start_lng   = $first['lng']  ?? null;
            $routes->end_point   = $last['name']  ?? null;
            $routes->end_lat     = $last['lat']   ?? null;
            $routes->end_lng     = $last['lng']   ?? null;
        }

        try {
            $routes->save();

            if (!empty($stops)) {
                $routes->stops()->delete();
                foreach ($stops as $index => $stop) {
                    if (empty($stop['name'])) continue;
                    $routes->stops()->create([
                        'stop_name'    => $stop['name'],
                        'lat'          => $stop['lat']  ?? null,
                        'lng'          => $stop['lng']  ?? null,
                        'order'        => $index,
                        'arrival_time' => $stop['time'] ?? null,
                    ]);
                }
            }

            return $redirect['url']->with('success', $redirect['msg']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Routes $routes)
    {
        $this->authorizeTenant($routes);

        try {
            $routes->delete();
            return back()->with('success', 'Routes deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete routes, it may be in use.');
        }
    }

    protected function authorizeTenant(Routes $routes)
    {
        // NULL = global route — sabko access milega ✅
        if ($routes->tenant_id !== null &&
            $routes->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }
    }
}