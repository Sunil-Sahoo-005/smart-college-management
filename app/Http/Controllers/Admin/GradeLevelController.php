<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GradeLevel;
use App\Models\GradingSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GradeLevelController extends Controller
{
    protected $gradeLevelListRoute = 'admin.grade_levels.index';

    public function index(Request $request)
    {
        // Sirf is tenant ke grading systems
        $gradingSystems = GradingSystem::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        // Sirf is tenant ke grade levels
        $query = GradeLevel::with('gradingSystem')
            ->whereHas('gradingSystem', function ($q) {
                $q->where('tenant_id', auth()->user()->tenant_id);
            })
            ->orderBy('grading_system_id')
            ->orderBy('min_score', 'desc');

        // Grading system filter
        if ($request->filled('grading_system_id')) {
            $query->where('grading_system_id', $request->grading_system_id);
        }

        $gradeLevels             = $query->get();
        $selectedGradingSystemId = $request->grading_system_id;
        $selectedGradingSystem   = $gradingSystems->find($selectedGradingSystemId);

        $gradeLevelsJson = $gradeLevels->map(function ($g) {
            return [
                'id'                  => $g->id,
                'grading_system_name' => $g->gradingSystem?->name ?? 'N/A',
                'grading_system_id'   => $g->grading_system_id,
                'min_score'           => number_format($g->min_score, 2),
                'max_score'           => number_format($g->max_score, 2),
                'grade_letter'        => $g->grade_letter,
                'remarks'             => $g->remarks ?? 'N/A',
                'edit_url'            => route('admin.grade_levels.edit', $g),
                'delete_url'          => route('admin.grade_levels.destroy', $g),
            ];
        });

        return view('Academics.grade_levels.index', compact(
            'gradeLevels', 'gradeLevelsJson', 'gradingSystems',
            'selectedGradingSystemId', 'selectedGradingSystem'
        ));
    }

    public function create(Request $request)
    {
        $gradingSystems = GradingSystem::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')->get();
        $selectedGradingSystemId = $request->grading_system_id;

        return view('Academics.grade_levels.form', compact('gradingSystems', 'selectedGradingSystemId'));
    }

    public function edit(GradeLevel $gradeLevel)
    {
        $gradingSystems = GradingSystem::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')->get();
        $selectedGradingSystemId = $gradeLevel->grading_system_id;

        return view('Academics.grade_levels.form', compact('gradeLevel', 'gradingSystems', 'selectedGradingSystemId'));
    }

    public function store(Request $request)
    {
        $rules = [
            'grading_system_id' => 'required|exists:grading_systems,id',
            'min_score'         => 'required|numeric|min:0|max:100',
            'max_score'         => 'required|numeric|min:0|max:100|gte:min_score',
            'grade_letter'      => 'required|min:1|max:5',
            'remarks'           => 'nullable|string|max:255',
        ];

        $messages = [
            'grading_system_id.required' => 'Please select grading system!',
            'grading_system_id.exists'   => 'Invalid grading system selected!',
            'min_score.required'         => 'Please enter minimum score!',
            'min_score.numeric'          => 'Minimum score must be a number!',
            'min_score.min'              => 'Minimum score must be at least 0!',
            'min_score.max'              => 'Minimum score must be at most 100!',
            'max_score.required'         => 'Please enter maximum score!',
            'max_score.numeric'          => 'Maximum score must be a number!',
            'max_score.min'              => 'Maximum score must be at least 0!',
            'max_score.max'              => 'Maximum score must be at most 100!',
            'max_score.gte'              => 'Maximum score must be greater than or equal to minimum score!',
            'grade_letter.required'      => 'Please enter grade letter!',
            'grade_letter.min'           => 'Grade letter should be minimum of 1 character!',
            'grade_letter.max'           => 'Grade letter should be maximum of 5 characters!',
            'remarks.max'                => 'Remarks should be maximum of 255 characters!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors()->first())->withInput();
        }

        if (!empty($request->get('id'))) {
            $gradeLevel = GradeLevel::where('id', $request->get('id'))->first();
            if (empty($gradeLevel)) abort(404);

            $redirect = [
                'url' => redirect()->route($this->gradeLevelListRoute, [
                    'grading_system_id' => $request->get('grading_system_id')
                ]),
                'msg' => 'Grade level updated successfully',
            ];
        } else {
            $gradeLevel = new GradeLevel;

            $redirect = [
                'url' => redirect()->route($this->gradeLevelListRoute, [
                    'grading_system_id' => $request->get('grading_system_id')
                ]),
                'msg' => 'Grade level created successfully',
            ];
        }

        $gradeLevel->grading_system_id = $request->get('grading_system_id');
        $gradeLevel->min_score         = $request->get('min_score');
        $gradeLevel->max_score         = $request->get('max_score');
        $gradeLevel->grade_letter      = trim($request->get('grade_letter'));
        $gradeLevel->remarks           = $request->get('remarks');

        try {
            $gradeLevel->save();
            return $redirect['url']->with('success', $redirect['msg']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong, please try again later!');
        }
    }

    public function destroy(GradeLevel $gradeLevel)
    {
        try {
            $gradeLevel->delete();
            return back()->with('success', 'Grade level deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete grade level, it may be in use.');
        }
    }
}