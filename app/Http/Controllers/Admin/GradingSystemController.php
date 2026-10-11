<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GradingSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GradingSystemController extends Controller
{
    protected $gradingSystemListRoute = 'admin.grading_systems.index';

    public function index()
    {
        $gradingSystems = GradingSystem::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        $gradingSystemsJson = $gradingSystems->map(function ($g) {
            return [
                'id'               => $g->id,
                'name'             => $g->name,
                'description'      => $g->description ? \Str::limit($g->description, 80) : 'N/A',
                'edit_url'         => route('admin.grading_systems.edit', $g),
                'delete_url'       => route('admin.grading_systems.destroy', $g),
                'grade_levels_url' => route('admin.grade_levels.index', ['grading_system_id' => $g->id]),
            ];
        });

        return view('Academics.grading_systems.index', compact('gradingSystems', 'gradingSystemsJson'));
    }

    public function create()
    {
        return view('Academics.grading_systems.form');
    }

    public function edit(GradingSystem $gradingSystem)
    {
        $this->authorizeTenant($gradingSystem);

        return view('Academics.grading_systems.form', compact('gradingSystem'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name'        => 'required|min:1|max:100|unique:grading_systems,name,' . $request->get('id') . ',id,tenant_id,' . auth()->user()->tenant_id,
            'description' => 'nullable|string|max:1000',
        ];

        $messages = [
            'name.required'   => 'Please enter grading system name!',
            'name.min'        => 'Grading system name should be minimum of 1 character!',
            'name.max'        => 'Grading system name should be maximum of 100 characters!',
            'name.unique'     => 'Grading system name already exists!',
            'description.max' => 'Description should be maximum of 1000 characters!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors()->first())->withInput();
        }

        if (!empty($request->get('id'))) {
            $gradingSystem = GradingSystem::where('id', $request->get('id'))
                ->where('tenant_id', auth()->user()->tenant_id)
                ->first();

            if (empty($gradingSystem)) abort(404);

            $redirect = [
                'url' => redirect()->back(),
                'msg' => 'Grading system updated successfully',
            ];
        } else {
            $gradingSystem = new GradingSystem;
            $gradingSystem->tenant_id = auth()->user()->tenant_id;

            $redirect = [
                'url' => redirect()->route($this->gradingSystemListRoute),
                'msg' => 'Grading system created successfully',
            ];
        }

        $gradingSystem->name        = trim($request->get('name'));
        $gradingSystem->description = $request->get('description');

        try {
            $gradingSystem->save();
            return $redirect['url']->with('success', $redirect['msg']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong, please try again later!');
        }
    }

    public function destroy(GradingSystem $gradingSystem)
    {
        $this->authorizeTenant($gradingSystem);

        try {
            $gradingSystem->delete();
            return back()->with('success', 'Grading system deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete grading system, it may be in use.');
        }
    }

    protected function authorizeTenant(GradingSystem $gradingSystem)
    {
        if ($gradingSystem->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }
    }
}