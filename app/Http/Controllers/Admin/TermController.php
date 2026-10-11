<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Term;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TermController extends Controller
{
    protected $termListRoute = 'admin.terms.index';

    public function index()
    {
        $terms = Term::where('tenant_id', auth()->user()->tenant_id)
            ->with('academicYear')
            ->orderBy('name')
            ->paginate(10);

        return view('Academics.terms.index', compact('terms'));
    }

    public function create()
    {
        $term = null;
        $academicYears = AcademicYear::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        return view('Academics.terms.form', compact('term', 'academicYears'));
    }

    public function edit(Term $term)
    {
        $this->authorizeTenant($term);

        $academicYears = AcademicYear::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        return view('Academics.terms.form', compact('term', 'academicYears'));
    }

    public function store(Request $request)
    {
        $rules = [
            'academic_year_id' => 'required|exists:academic_years,id',
            'name'             => 'required|min:1|max:50|unique:terms,name,' . $request->get('id') . ',id,tenant_id,' . auth()->user()->tenant_id,
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after_or_equal:start_date',
        ];

        $messages = [
            'academic_year_id.required' => 'Please select academic year!',
            'academic_year_id.exists'   => 'Invalid academic year selected!',
            'name.required'             => 'Please enter term name!',
            'name.min'                  => 'Term name should be minimum of 1 character!',
            'name.max'                  => 'Term name should be maximum of 50 characters!',
            'name.unique'               => 'Term name already exists!',
            'start_date.required'       => 'Please enter start date!',
            'start_date.date'           => 'Start date must be a valid date!',
            'end_date.required'         => 'Please enter end date!',
            'end_date.date'             => 'End date must be a valid date!',
            'end_date.after_or_equal'   => 'End date must be after or equal to start date!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            return redirect()->back()->withErrors($errorMessage)->withInput();
        }

        if (!empty($request->get('id'))) {
            $term = Term::where('id', $request->get('id'))
                ->where('tenant_id', auth()->user()->tenant_id)
                ->first();

            if (empty($term)) {
                abort(404);
            }

            $redirect = [
                'url' => redirect()->back(),
                'msg' => 'Term updated successfully',
            ];
        } else {
            $term = new Term;
            $term->tenant_id = auth()->user()->tenant_id;

            $redirect = [
                'url' => redirect()->route($this->termListRoute),
                'msg' => 'Term created successfully',
            ];
        }

        $term->academic_year_id = $request->get('academic_year_id');
        $term->name             = trim($request->get('name'));
        $term->start_date       = $request->get('start_date');
        $term->end_date         = $request->get('end_date');

        try {
            $term->save();

            return $redirect['url']->with('success', $redirect['msg']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong, please try again later!');
        }
    }

    public function destroy(Term $term)
    {
        $this->authorizeTenant($term);

        try {
            $term->delete();
            return back()->with('success', 'Term deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete term, it may be in use.');
        }
    }

    protected function authorizeTenant(Term $term)
    {
        if ($term->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }
    }
}
