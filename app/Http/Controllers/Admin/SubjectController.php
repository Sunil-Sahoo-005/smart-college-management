<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubjectController extends Controller
{
    protected $subjectListRoute = 'admin.subjects.index';

    public function index()
{
    $subjects = Subject::where('tenant_id', auth()->user()->tenant_id)
        ->orderBy('name')
        ->get();

    $subjectsJson = $subjects->map(function ($subject) {
        return [
            'id'         => $subject->id,
            'name'       => $subject->name,
            'code'       => $subject->code,
            'type'       => $subject->type,
            'edit_url'   => route('admin.subjects.edit', $subject),
            'delete_url' => route('admin.subjects.destroy', $subject),
        ];
    });

    return view('Academics.subjects.index', compact('subjects', 'subjectsJson'));
}
    public function create()
    {
        return view('Academics.subjects.form');
    }

    public function edit(Subject $subject)
    {
        $this->authorizeTenant($subject);

        return view('Academics.subjects.form', compact('subject'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|min:1|max:100|unique:subjects,name,' . $request->get('id') . ',id,tenant_id,' . auth()->user()->tenant_id,
            'code' => 'nullable|max:20|unique:subjects,code,' . $request->get('id') . ',id,tenant_id,' . auth()->user()->tenant_id,
            'type' => 'required|in:core,elective,practical',
        ];

        $messages = [
            'name.required' => 'Please enter subject name!',
            'name.min'      => 'Subject name should be minimum of 1 character!',
            'name.max'      => 'Subject name should be maximum of 100 characters!',
            'name.unique'   => 'Subject name already exists!',
            'code.max'      => 'Subject code should be maximum of 20 characters!',
            'code.unique'   => 'Subject code already exists!',
            'type.required' => 'Please select subject type!',
            'type.in'       => 'Invalid subject type selected!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            return redirect()->back()->withErrors($errorMessage)->withInput();
        }

        if (!empty($request->get('id'))) {
            $subject = Subject::where('id', $request->get('id'))
                ->where('tenant_id', auth()->user()->tenant_id)
                ->first();

            if (empty($subject)) {
                abort(404);
            }

            $redirect = [
                'url' => redirect()->back(),
                'msg' => 'Subject updated successfully',
            ];
        } else {
            $subject = new Subject;
            $subject->tenant_id = auth()->user()->tenant_id;

            $redirect = [
                'url' => redirect()->route($this->subjectListRoute),
                'msg' => 'Subject created successfully',
            ];
        }

        $subject->name = trim($request->get('name'));
        $subject->code = $request->get('code');
        $subject->type = $request->get('type');

        try {
            $subject->save();

            return $redirect['url']->with('success', $redirect['msg']);
        } catch (\Exception $e) {
            echo $e->getMessage(); // For debugging purposes, remove in production
            // exit;
            return redirect()->back()->withErrors( 'Something went wrong, please try again later!');
        }
    }

    public function destroy(Subject $subject)
    {
        $this->authorizeTenant($subject);

        try {
            $subject->delete();
            return back()->with('success', 'Subject deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete subject, it may be in use.');
        }
    }

    protected function authorizeTenant(Subject $subject)
    {
        if ($subject->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }
    }
}
