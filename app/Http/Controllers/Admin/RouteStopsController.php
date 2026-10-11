<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Routes;
use Illuminate\Http\Request;
use App\Models\RouteStop;


class RouteStopsController extends Controller
{
    protected $routestopsListRoute = 'admin.routestops.index';
    public function index()
   {
    // dd(auth()->user()->route_id);

    $routestops = Routestop::orderBy('route_id')
    ->paginate(10);

return view('Travels.routestops.index', compact('routestops'));
    
   }
   public function create()
    {
        $routestops=-null;
        // $route_id = Routestop::where('route_id', auth()->user()->route_id)->orderBy('route_id')->get();
        $route_id = Routes::where('tenant_id', auth()->user()->tenant_id)->get();

        return view('Travels.routestops.form', compact('routestops', 'route_id'));
    }
    
    public function edit(Routestop $routestops)
    {
        // $this->authorizeTenant($routestops);
        
        $route_id = Routes::where('tenant_id', auth()->user()->tenant_id)->get();

        // $route_id = Routestop::where('route_id', auth()->user()->route_id)->orderBy('route_id')->get();
        
        return view('Travels.routestops.form', compact('routestops', 'route_id'));
    }

     public function store(Request $request)
    {
        // dd($request->all());
        if (!empty($request->get('id'))) {
            // dd(233)
            $routestops = Routestop::where('id', $request->get('id'))
            
                // ->where('route_id', auth()->user()->route_id)
                ->first();
               

            if (empty($routestops)) {
                abort(404);
            }
            // dd($routestops);
            $redirect = [
                'url' => redirect()->back(),
                'msg' => 'Routestops updated successfully',
            ];
        } else {
            $routestops = new Routestop;
            // $routestops->route_id = auth()->user()->route_id;

            $redirect = [
                'url' => redirect()->route($this->routestopsListRoute),
                'msg' => 'Routestops created successfully',
            ];
        }
         $routestops->route_id           = $request->get('route_id');
        $routestops->stop_name          = $request->get('stop_name');
        $routestops->arrival_time          = $request->get('arrival_time');
       
        
        try {
            //  dd($request->all());
            $routestops->save();

            return $redirect['url']->with('success', $redirect['msg']);
        } catch (\Exception $e) {
            // echo $e->getMessage();
            // exit;
            return redirect()->back()->with('error', 'Something went wrong, please try again later!');
        }
    }

    public function destroy(Routestop $routestops)
    {
        // $this->authorizeTenant($routestops);

        try {
            $routestops->delete();
            return back()->with('success', 'routes deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete routes, it may be in use.');
        }
    }

    protected function authorizeTenant(Routestop $routestops)
    {
        if ($routestops->route_id !== auth()->user()->route_id) {
            abort(403);
        }
    }
}
