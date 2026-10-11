<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassController extends Controller
{
    protected $classListRoute = 'admin.classes.index';

   public function index()
{
    $classes = Classe::where('tenant_id', auth()->user()->tenant_id)
        ->orderBy('order_index')
        ->orderBy('name')
        ->get();

    $classesJson = $classes->map(function ($class) {
        return [
            'id'          => $class->id,
            'name'        => $class->name,
            'order_index' => $class->order_index,
            'edit_url'    => route('admin.classes.edit', $class),
            'delete_url'  => route('admin.classes.destroy', $class),
        ];
    });

    return view('Academics.classes.index', compact('classes', 'classesJson'));
}

    public function create()
    {
        return view('Academics.classes.form');
    }

    public function edit(Classe $class)
    {
        $this->authorizeTenant($class);

        return view('Academics.classes.form', compact('class'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name'        => 'required|min:1|max:50|unique:classes,name,' . $request->get('id') . ',id,tenant_id,' . auth()->user()->tenant_id,
            'order_index' => 'nullable|integer|min:0',
        ];

        $messages = [
            'name.required'     => 'Please enter class name!',
            'name.min'          => 'Class name should be minimum of 1 character!',
            'name.max'          => 'Class name should be maximum of 50 characters!',
            'name.unique'       => 'Class name already exists!',
            'order_index.integer' => 'Order index must be a number!',
            'order_index.min'     => 'Order index must be at least 0!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            return redirect()->back()->withErrors($errorMessage)->withInput();
        }

        if (!empty($request->get('id'))) {
            $class = Classe::where('id', $request->get('id'))
                ->where('tenant_id', auth()->user()->tenant_id)
                ->first();

            if (empty($class)) {
                abort(404);
            }

            $redirect = [
                'url' => redirect()->back(),
                'msg' => 'Class updated successfully',
            ];
        } else {
            $class = new Classe;
            $class->tenant_id = auth()->user()->tenant_id;

            $redirect = [
                'url' => redirect()->route($this->classListRoute),
                'msg' => 'Class created successfully',
            ];
        }

        $class->name        = trim($request->get('name'));
        $class->order_index = $request->get('order_index', 0);

        try {
            $class->save();

            return $redirect['url']->with('success', $redirect['msg']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong, please try again later!');
        }
    }

    public function destroy(Classe $class)
    {
        $this->authorizeTenant($class);

        try {
            $class->delete();
            return back()->with('success', 'Class deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete class, it may be in use.');
        }
    }

    protected function authorizeTenant(Classe $class)
    {
        if ($class->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }
    }
}
