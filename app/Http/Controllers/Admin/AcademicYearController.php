<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AcademicYearController extends Controller
{
    protected $academicYearListRoute = 'admin.academic_years.index';

    public function index()
    {
        $academicYears = AcademicYear::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();


        $academicYearsJson = $academicYears->map(function ($ay) {
            return [
                'id'         => $ay->id,
                'name'       => $ay->name,
                'start_year' => $ay->start_year,
                'end_year' => $ay->end_year,

                'edit_url'   => route('admin.academic_years.edit', $ay),
                'delete_url' => route('admin.academic_years.destroy', $ay),
            ];
        });

        return view('Academics.academic_years.index', compact('academicYears', 'academicYearsJson'));
    }

    public function create()
    {
        $academicYear = null;
        return view('Academics.academic_years.form', compact('academicYear'));
    }

    public function edit(AcademicYear $academicYear)
    {
        $this->authorizeTenant($academicYear);

        return view('Academics.academic_years.form', compact('academicYear'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|min:1|max:20|unique:academic_years,name,'
                . $request->get('id')
                . ',id,tenant_id,'
                . auth()->user()->tenant_id,

            'start_year' => 'required|digits:4',
            'end_year' => 'required|digits:4|gt:start_year',
        ];

        $messages = [
            'name.required' => 'Please enter academic year name!',
            'name.min' => 'Academic year name should be minimum of 1 character!',
            'name.max' => 'Academic year name should be maximum of 20 characters!',
            'name.unique' => 'Academic year name already exists!',

            'start_year.required' => 'Please select start year!',
            'start_year.digits' => 'Start year must be 4 digits!',

            'end_year.required' => 'Please select end year!',
            'end_year.digits' => 'End year must be 4 digits!',
            'end_year.gt' => 'End year must be greater than start year!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            return redirect()->back()->withErrors($errorMessage)->withInput();
        }

        if (!empty($request->get('id'))) {
            $academicYear = AcademicYear::where('id', $request->get('id'))
                ->where('tenant_id', auth()->user()->tenant_id)
                ->first();

            if (empty($academicYear)) {
                abort(404);
            }

            $redirect = [
                'url' => redirect()->back(),
                'msg' => 'Academic year updated successfully',
            ];
        } else {
            $academicYear = new AcademicYear;
            $academicYear->tenant_id = auth()->user()->tenant_id;

            $redirect = [
                // 'url' => redirect($this->academicYearListRoute),
                'url' => redirect()->route($this->academicYearListRoute),
                'msg' => 'Academic year created successfully',
            ];
        }

        $academicYear->name       = trim($request->get('name'));
        // $academicYear->start_date = $request->get('start_date');
        // $academicYear->end_date   = $request->get('end_date');
        // $academicYear->is_current = $request->has('is_current');
        $academicYear->start_year = $request->start_year;
        $academicYear->end_year = $request->end_year;
        try {
            // If is_current is true, unset all others
            // if ($academicYear->is_current) {
            //     AcademicYear::where('tenant_id', $academicYear->tenant_id)
            //         ->where('id', '!=', $academicYear->id)
            //         ->update(['is_current' => false]);
            // }

            $academicYear->save();

            return $redirect['url']->with('success', $redirect['msg']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong, please try again later!');
        }
    }

    public function destroy(AcademicYear $academicYear)
    {
        $this->authorizeTenant($academicYear);

        try {
            $academicYear->delete();
            return back()->with('success', 'Academic year deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete academic year, it may be in use.');
        }
    }

    protected function authorizeTenant(AcademicYear $academicYear)
    {
        if ($academicYear->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }
    }
}
