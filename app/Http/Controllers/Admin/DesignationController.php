<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DesignationController extends Controller
{
  protected $designationListRoute = 'admin.designations.index';

   public function index()
{
    $designations = Designation::where('tenant_id', auth()->user()->tenant_id)
        ->orderBy('name')
        ->get(); // paginate hata diya

    $designationsJson = $designations->map(function ($d) {
        return [
            'id'          => $d->id,
            'name'        => $d->name,
            'description' => $d->description ? \Str::limit($d->description, 80) : 'N/A',
            'edit_url'    => route('admin.designations.edit', $d),
            'delete_url'  => route('admin.designations.destroy', $d),
        ];
    });

    return view('Academics.designations.index', compact('designations', 'designationsJson'));
}
    public function create()
    {
        return view('Academics.designations.form');
    }

    public function edit(Designation $designation)
    {
        $this->authorizeTenant($designation);

        return view('Academics.designations.form', compact('designation'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name'        => 'required|min:1|max:100|unique:designations,name,' . $request->get('id') . ',id,tenant_id,' . auth()->user()->tenant_id,
            'description' => 'nullable|max:500',
        ];

        $messages = [
            'name.required'   => 'Please enter designation name!',
            'name.min'        => 'Designation name should be minimum of 1 character!',
            'name.max'        => 'Designation name should be maximum of 100 characters!',
            'name.unique'     => 'Designation name already exists!',
            'description.max' => 'Description should be maximum of 500 characters!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            return redirect()->back()->withErrors($errorMessage)->withInput();
        }

        if (!empty($request->get('id'))) {
            $designation = Designation::where('id', $request->get('id'))
                ->where('tenant_id', auth()->user()->tenant_id)
                ->first();

            if (empty($designation)) {
                abort(404);
            }

            $redirect = [
                'url' => redirect()->back(),
                'msg' => 'Designation updated successfully',
            ];
        } else {
            $designation = new Designation;
            $designation->tenant_id = auth()->user()->tenant_id;

            $redirect = [
                'url' => redirect()->route($this->designationListRoute),
                'msg' => 'Designation created successfully',
            ];
        }

        $designation->name        = trim($request->get('name'));
        $designation->description = $request->get('description');

        try {
            $designation->save();

            return $redirect['url']->with('success', $redirect['msg']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong, please try again later!');
        }
    }

    public function destroy(Designation $designation)
    {
        $this->authorizeTenant($designation);

        try {
            $designation->delete();
            return back()->with('success', 'Designation deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete designation, it may be in use.');
        }
    }

    protected function authorizeTenant(Designation $designation)
    {
        if ($designation->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }
    }  
}
