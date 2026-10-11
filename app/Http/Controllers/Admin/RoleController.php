<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;

class RoleController extends Controller
{
    protected $roleListRoute = 'admin.roles.index';

    public function index(Request $request)
    {
        $roles = Role::with('permissions')->latest()->paginate(20);

        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $role = new Role;
        $permissions = Permission::orderBy('module')->get();

        return view('roles.role-form', compact('role', 'permissions'));
    }

    public function edit($id)
    {
        $role = Role::with('permissions')->find($id);

        if (empty($role)) {
            abort(404);
        }

        $permissions = Permission::orderBy('module')->get();

        return view('roles.role-form', compact('role', 'permissions'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|min:1|max:50|unique:roles,name,' . $request->get('id'),
            'guard_name' => 'required|max:20',
            'module' => 'nullable|max:50',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ];

        $messages = [
            'name.required' => 'Please enter role name!',
            'name.min' => 'Role name should be minimum of 1 character!',
            'name.max' => 'Role name should be maximum of 50 characters!',
            'name.unique' => 'Role name already exists!',
            'guard_name.required' => 'Please choose guard!',
            'guard_name.max' => 'Guard name should be maximum of 20 characters!',
            'module.max' => 'Module should be maximum of 50 characters!',
            'permissions.*.exists' => 'Invalid permission selected!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            return redirect()->back()->withErrors($errorMessage)->withInput();
        }

        if (!empty($request->get('id'))) {
            $role = Role::find($request->get('id'));
            if (empty($role)) {
                abort(404);
            }
            $redirect = [
                'url' => redirect()->back(),
                'msg' => 'Role updated successfully',
            ];
        } else {
            $role = new Role;
            $redirect = [
                'url' => redirect($this->roleListRoute),
                'msg' => 'Role created successfully',
            ];
        }

        $role->name = trim($request->get('name'));
        $role->guard_name = $request->get('guard_name', 'web');
        $role->module = $request->get('module');

        try {
            $role->save();

            $permissions = $request->get('permissions', []);
            $role->permissions()->sync($permissions);

            return $redirect['url']->with('success', $redirect['msg']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong, please try again later!');
        }
    }

    public function show($id)
    {
        $role = Role::with('permissions')->find($id);

        if (empty($role)) {
            abort(404);
        }

        return view('roles.show', compact('role'));
    }

    public function destroy($id)
    {
        $role = Role::find($id);

        if (empty($role)) {
            abort(404);
        }

        $role->permissions()->detach();
        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }
}
