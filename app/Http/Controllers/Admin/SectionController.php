<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Classe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SectionController extends Controller
{
    protected $sectionListRoute = 'admin.sections.index';

    public function index()
    {
        $sections = Section::where('tenant_id', auth()->user()->tenant_id)
            ->with('class')
            ->orderBy('name')
            ->get();

        $sectionsJson = $sections->map(function ($section) {
            return [
                'id'           => $section->id,
                'name'         => $section->name,
                'class_name'   => $section->class?->name ?? 'N/A',
                'max_strength' => $section->max_strength,
                'edit_url'     => route('admin.sections.edit', $section),
                'delete_url'   => route('admin.sections.destroy', $section),
            ];
        });

        return view('Academics.sections.index', compact('sections', 'sectionsJson'));
    }

    public function create()
    {
        $section = null;
        $classes = Classe::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('order_index')
            ->orderBy('name')
            ->get();

        return view('Academics.sections.form', compact('section', 'classes'));
    }

    public function edit(Section $section)
    {
        $this->authorizeTenant($section);

        $classes = Classe::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('order_index')
            ->orderBy('name')
            ->get();

        return view('Academics.sections.form', compact('section', 'classes'));
    }

    public function store(Request $request)
    {
        $rules = [
            'class_id'     => 'required|exists:classes,id',
            'name'         => [
                'required',
                'min:1',
                'max:10',
                Rule::unique('sections', 'name')
                    ->where(function ($query) use ($request) {
                        return $query
                            ->where('tenant_id', auth()->user()->tenant_id)
                            ->where('class_id', $request->get('class_id'));
                    })
                    ->ignore($request->get('id')),
            ],
            'max_strength' => 'nullable|integer|min:1',
        ];

        $messages = [
            'class_id.required'    => 'Please select class!',
            'class_id.exists'      => 'Invalid class selected!',
            'name.required'        => 'Please enter section name!',
            'name.min'             => 'Section name should be minimum of 1 character!',
            'name.max'             => 'Section name should be maximum of 10 characters!',
            'name.unique'          => 'Section name already exists for this class!',
            'max_strength.integer' => 'Max strength must be a number!',
            'max_strength.min'     => 'Max strength must be at least 1!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors()->first())->withInput();
        }

        if (!empty($request->get('id'))) {
            $section = Section::where('id', $request->get('id'))
                ->where('tenant_id', auth()->user()->tenant_id)
                ->first();

            if (empty($section)) {
                abort(404);
            }

            $redirect = [
                'url' => redirect()->back(),
                'msg' => 'Section updated successfully',
            ];
        } else {
            $section            = new Section;
            $section->tenant_id = auth()->user()->tenant_id;

            $redirect = [
                'url' => redirect()->route($this->sectionListRoute),
                'msg' => 'Section created successfully',
            ];
        }

        $section->class_id     = $request->get('class_id');
        $section->name         = trim($request->get('name'));
        $section->max_strength = $request->get('max_strength');

        try {
            $section->save();
            return $redirect['url']->with('success', $redirect['msg']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong, please try again later!');
        }
    }

    public function destroy(Section $section)
    {
        $this->authorizeTenant($section);

        try {
            $section->delete();
            return back()->with('success', 'Section deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete section, it may be in use.');
        }
    }

    protected function authorizeTenant(Section $section)
    {
        if ($section->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }
    }
}