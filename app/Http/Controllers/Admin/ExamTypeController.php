<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\ExamType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class ExamTypeController extends Controller
{
    protected $examTypeListRoute = 'admin.exam_types.index';

   public function index(Request $request)
{
    $examTypes = ExamType::where('tenant_id', Auth::user()->tenant_id)
        ->orderBy('name')
        ->get(); // paginate hata diya

    $examTypesJson = $examTypes->map(function ($et) {
        return [
            'id'          => $et->id,
            'name'        => $et->name,
            'description' => $et->description ? \Str::limit($et->description, 80) : 'N/A',
            'edit_url'    => route('admin.exam_types.edit', $et),
            'delete_url'  => route('admin.exam_types.destroy', $et),
        ];
    });

    return view('Academics.exam_types.index', compact('examTypes', 'examTypesJson'));
}

    public function create()
    {
        $examType = new ExamType;

        return view('Academics.exam_types.form', compact('examType'));
    }

    public function edit($id)
    {
        $examType = ExamType::where('tenant_id', Auth::user()->tenant_id)->find($id);

        if (empty($examType)) {
            abort(404);
        }

        return view('Academics.exam_types.form', compact('examType'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $rules = [
            'name'        => 'required|min:1|max:50|unique:exam_types,name,' . $request->get('id') . ',id,tenant_id,' . auth()->user()->tenant_id,

            // 'name'        => 'required|min:1|max:50|unique:exam_types,name,NULL,id,tenant_id,' . Auth::user()->tenant_id,
            'description' => 'nullable|string|max:2000',
        ];

        $messages = [
            'name.required' => 'Please enter exam type name!',
            'name.min'      => 'Exam type name should be minimum of 1 character!',
            'name.max'      => 'Exam type name should be maximum of 50 characters!',
            'name.unique'   => 'Exam type name already exists!',
            'description.max' => 'Description should be maximum of 2000 characters!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            return redirect()->back()->withErrors($errorMessage)->withInput();
        }

        if (!empty($request->get('id'))) {
            $examType = ExamType::where('tenant_id', Auth::user()->tenant_id)->find($request->get('id'));
            if (empty($examType)) {
                abort(404);
            }
            $redirect = [
                'url' => redirect()->back(),
                'msg' => 'Exam type updated successfully',
            ];
        } else {
            $examType = new ExamType;
            $redirect = [
                'url' => redirect()->route($this->examTypeListRoute),

                'msg' => 'Exam type created successfully',
            ];
        }

        $examType->tenant_id   = Auth::user()->tenant_id;
        $examType->name        = trim($request->get('name'));
        $examType->description = $request->get('description');

        try {
            $examType->save();

            return $redirect['url']->with('success', $redirect['msg']);
        } catch (\Exception $e) {
            echo $e->getMessage();
            return redirect()->back()->with('error', 'Something went wrong, please try again later!');
        }
    }

    public function show($id)
    {
        $examType = ExamType::where('tenant_id', Auth::user()->tenant_id)->find($id);

        if (empty($examType)) {
            abort(404);
        }

        return view('admin.exam_types.show', compact('examType'));
    }

    public function destroy($id)
    {
        $examType = ExamType::where('tenant_id', Auth::user()->tenant_id)->find($id);

        if (empty($examType)) {
            abort(404);
        }

        $examType->delete();

        return redirect()->route('admin.exam_types.index')->with('success', 'Exam type deleted successfully.');
    }
}
