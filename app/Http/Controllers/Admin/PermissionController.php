<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class PermissionController extends Controller
{
    protected $permissionListRoute = 'admin.permissions.index';

    public function index(Request $request)
    {
        $permissions = Permission::orderBy('module')->paginate(20);

        return view('permissions.index', compact('permissions'));
    }

    public function create()
    {
        $permission = new Permission;

        return view('permissions.permission-form', compact('permission'));
    }

    public function edit($id)
    {
        $permission = Permission::find($id);

        if (empty($permission)) {
            abort(404);
        }

        return view('permissions.permission-form', compact('permission'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|min:1|max:100|unique:permissions,name,' . $request->get('id'),
            'guard_name' => 'required|max:20',
            'module' => 'nullable|max:50',
        ];

        $messages = [
            'name.required' => 'Please enter permission name!',
            'name.min' => 'Permission name should be minimum of 1 character!',
            'name.max' => 'Permission name should be maximum of 100 characters!',
            'name.unique' => 'Permission name already exists!',
            'guard_name.required' => 'Please choose guard!',
            'guard_name.max' => 'Guard name should be maximum of 20 characters!',
            'module.max' => 'Module should be maximum of 50 characters!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            return redirect()->back()->withErrors($errorMessage)->withInput();
        }

        if (!empty($request->get('id'))) {
            $permission = Permission::find($request->get('id'));
            if (empty($permission)) {
                abort(404);
            }
            $redirect = [
                'url' => redirect()->back(),
                'msg' => 'Permission updated successfully',
            ];
        } else {
            $permission = new Permission;
            $redirect = [
                'url' => redirect()->route($this->permissionListRoute),
                'msg' => 'Permission created successfully',
            ];
        }

        $permission->name = trim($request->get('name'));
        $permission->guard_name = $request->get('guard_name', 'web');
        $permission->module = $request->get('module');

        try {
            $permission->save();
            return $redirect['url']->with('success', $redirect['msg']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong, please try again later!');
        }
    }

    public function show($id)
    {
        $permission = Permission::find($id);

        if (empty($permission)) {
            abort(404);
        }

        return view('permissions.show', compact('permission'));
    }

    public function destroy($id)
    {
        $permission = Permission::find($id);

        if (empty($permission)) {
            abort(404);
        }

        $permission->roles()->detach();
        $permission->delete();

        return redirect()->route('admin.permissions.index')->with('success', 'Permission deleted successfully.');
    }
}
