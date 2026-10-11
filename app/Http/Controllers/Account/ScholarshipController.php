<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScholarshipController extends Controller
{
    protected $userListRoute = 'scholarships.index';

    public function index()
    {
        $scholarships = Scholarship::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->paginate(10);

        return view('Accountants.scholarships.index', compact('scholarships'));
    }

    public function create()
    {
        return view('Accountants.scholarships.form');
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255|unique:scholarships,name,' . $request->id . ',id,tenant_id,' . auth()->user()->tenant_id,
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'source' => 'required|in:college,government,trust,nsp',
            'caste_category' => 'required|string|max:255',
        ];

        // If type is percentage, value should not exceed 100
        if ($request->type === 'percentage') {
            $rules['value'] .= '|max:100';
        }

        $messages = [
            'name.required' => 'Please enter scholarship name!',
            'name.unique' => 'Scholarship name already exists for this tenant!',
            'type.required' => 'Please select scholarship type!',
            'value.required' => 'Please enter scholarship value!',
            'value.numeric' => 'Value must be a valid number!',
            'value.max' => 'Percentage value cannot exceed 100%!',
            'source.required' => 'Please select scholarship source!',
            'caste_category.required' => 'Please select caste category!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Scholarship::updateOrCreate(
            [
                'id' => $request->id,
                'tenant_id' => auth()->user()->tenant_id
            ],
            [
                'name' => trim($request->name),
                'type' => $request->type,
                'value' => $request->value,
                'source' => $request->source,
                'caste_category' => $request->caste_category,
                'is_active' => $request->has('is_active') ? (bool) $request->is_active : false,
            ]
        );

        $msg = $request->id ? 'Scholarship updated successfully' : 'Scholarship created successfully';

        return redirect()->route($this->userListRoute)
            ->with('success', $msg);
    }

    public function edit($id)
    {
        $scholarship = Scholarship::where('tenant_id', auth()->user()->tenant_id)
            ->findOrFail($id);

        return view('Accountants.scholarships.form', compact('scholarship'));
    }

    public function destroy($id)
    {
        $scholarship = Scholarship::where('tenant_id', auth()->user()->tenant_id)
            ->findOrFail($id);

        $scholarship->delete();

        return redirect()->route($this->userListRoute)
            ->with('success', 'Scholarship deleted successfully');
    }
}
