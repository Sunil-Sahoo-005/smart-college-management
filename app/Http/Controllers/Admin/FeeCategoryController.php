<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\FeeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class FeeCategoryController extends Controller
{
    protected $feeCategoryListRoute = 'admin.fee_categories.index';

    public function index(Request $request)
    {
        $feeCategories = FeeCategory::where('tenant_id', Auth::user()->tenant_id)
            ->orderBy('name')
            ->paginate(20);

        return view('Accountants.fee_categories.index', compact('feeCategories'));
    }

    public function create()
    {
        $feeCategory = new FeeCategory;

        return view('Accountants.fee_categories.form', compact('feeCategory'));
    }

    public function edit($id)
    {
        $feeCategory = FeeCategory::where('tenant_id', Auth::user()->tenant_id)->find($id);

        if (empty($feeCategory)) {
            abort(404);
        }

        return view('Accountants.fee_categories.form', compact('feeCategory'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name'        => 'required|min:1|max:50|unique:fee_categories,name,NULL,id,tenant_id,' . Auth::user()->tenant_id,
            'description' => 'nullable|string|max:2000',
        ];

        $messages = [
            'name.required' => 'Please enter fee category name!',
            'name.min'      => 'Fee category name should be minimum of 1 character!',
            'name.max'      => 'Fee category name should be maximum of 50 characters!',
            'name.unique'   => 'Fee category name already exists!',
            'description.max' => 'Description should be maximum of 2000 characters!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            return redirect()->back()->withErrors($errorMessage)->withInput();
        }

        if (!empty($request->get('id'))) {
            $feeCategory = FeeCategory::where('tenant_id', Auth::user()->tenant_id)->find($request->get('id'));
            if (empty($feeCategory)) {
                abort(404);
            }
            $redirect = [
                'url' => redirect()->back(),
                'msg' => 'Fee category updated successfully',
            ];
        } else {
            $feeCategory = new FeeCategory;
            $redirect = [
                'url' => redirect()->route($this->feeCategoryListRoute),
                'msg' => 'Fee category created successfully',
            ];
        }

        $feeCategory->tenant_id   = Auth::user()->tenant_id;
        $feeCategory->name        = trim($request->get('name'));
        $feeCategory->description = $request->get('description');

        try {
            $feeCategory->save();

            return $redirect['url']->with('success', $redirect['msg']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong, please try again later!');
        }
    }

    public function show($id)
    {
        $feeCategory = FeeCategory::where('tenant_id', Auth::user()->tenant_id)->find($id);

        if (empty($feeCategory)) {
            abort(404);
        }

        return view('admin.fee_categories.show', compact('feeCategory'));
    }

    public function destroy($id)
    {
        $feeCategory = FeeCategory::where('tenant_id', Auth::user()->tenant_id)->find($id);

        if (empty($feeCategory)) {
            abort(404);
        }

        $feeCategory->delete();

        return redirect()->route('admin.fee_categories.index')->with('success', 'Fee category deleted successfully.');
    }
}
