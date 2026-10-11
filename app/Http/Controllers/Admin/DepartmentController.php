<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
     protected $departmentListRoute = 'admin.departments.index';

    public function index()
{
    $departments = Department::where('tenant_id', auth()->user()->tenant_id)
        ->orderBy('name')
        ->get(); // paginate hata diya

    $departmentsJson = $departments->map(function ($d) {
        return [
            'id'          => $d->id,
            'name'        => $d->name,
            'description' => $d->description ? \Str::limit($d->description, 80) : 'N/A',
            'edit_url'    => route('admin.departments.edit', $d),
            'delete_url'  => route('admin.departments.destroy', $d),
        ];
    });

    return view('Academics.departments.index', compact('departments', 'departmentsJson'));
}

    public function create()
    {
        return view('Academics.departments.form');
    }

    public function edit(Department $department)
    {
        $this->authorizeTenant($department);

        return view('Academics.departments.form', compact('department'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name'        => 'required|min:1|max:100|unique:departments,name,' . $request->get('id') . ',id,tenant_id,' . auth()->user()->tenant_id,
            'description' => 'nullable|max:500',
        ];

        $messages = [
            'name.required'   => 'Please enter department name!',
            'name.min'        => 'Department name should be minimum of 1 character!',
            'name.max'        => 'Department name should be maximum of 100 characters!',
            'name.unique'     => 'Department name already exists!',
            'description.max' => 'Description should be maximum of 500 characters!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            return redirect()->back()->withErrors($errorMessage)->withInput();
        }

        if (!empty($request->get('id'))) {
            $department = Department::where('id', $request->get('id'))
                ->where('tenant_id', auth()->user()->tenant_id)
                ->first();

            if (empty($department)) {
                abort(404);
            }

            $redirect = [
                'url' => redirect()->back(),
                'msg' => 'Department updated successfully',
            ];
        } else {
            $department = new Department;
            $department->tenant_id = auth()->user()->tenant_id;

            $redirect = [
                'url' => redirect()->route($this->departmentListRoute),
                'msg' => 'Department created successfully',
            ];
        }

        $department->name        = trim($request->get('name'));
        $department->description = $request->get('description');

        try {
            $department->save();

            return $redirect['url']->with('success', $redirect['msg']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong, please try again later!');
        }
    }

    public function destroy(Department $department)
    {
        $this->authorizeTenant($department);

        try {
            $department->delete();
            return back()->with('success', 'Department deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete department, it may be in use.');
        }
    }

    protected function authorizeTenant(Department $department)
    {
        if ($department->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }
    }
}
