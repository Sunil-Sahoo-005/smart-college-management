<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSubjectMapping;
use App\Models\Classe;
use App\Models\Subject;
use App\Models\Stream;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassSubjectMappingController extends Controller
{
  protected $mappingListRoute = 'admin.class_subject_mappings.index';

   public function index()
{
    $mappings = ClassSubjectMapping::where('tenant_id', auth()->user()->tenant_id)
        ->with(['class', 'subject'])
        ->orderBy('class_id')
        ->get();

    $mappingsJson = $mappings->map(function ($mapping) {
        return [
            'id'           => $mapping->id,
            'class_name'   => $mapping->class?->name ?? 'N/A',
            'subject_name' => $mapping->subject?->name ?? 'N/A',
            'is_compulsory'=> $mapping->is_compulsory,
            'edit_url'     => route('admin.class_subject_mappings.edit', $mapping),
            'delete_url'   => route('admin.class_subject_mappings.destroy', $mapping),
        ];
    });

    return view('Academics.class_subject_mappings.index', compact('mappings', 'mappingsJson'));
}

    public function create()
    {
        $classes = Classe::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('order_index')
            ->orderBy('name')
            ->get();

        $subjects = Subject::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();
            $branches = Stream::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        return view('Academics.class_subject_mappings.form', compact('classes', 'subjects', 'branches'));
    }

    public function edit(ClassSubjectMapping $mapping)
    {
        $this->authorizeTenant($mapping);

        $classes = Classe::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('order_index')
            ->orderBy('name')
            ->get();

        $subjects = Subject::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

            $branches = Stream::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        return view('Academics.class_subject_mappings.form', compact('mapping', 'classes', 'subjects', 'branches'));
    }

    public function store(Request $request)
    {
        $rules = [
            'class_id'       => 'required|exists:classes,id',
            'subject_id'     => 'required|exists:subjects,id|unique:class_subject_mappings,subject_id,NULL,id,class_id,' . $request->get('class_id'),
            'is_compulsory'  => 'required|boolean',
        ];

        $messages = [
            'class_id.required'      => 'Please select class!',
            'class_id.exists'        => 'Invalid class selected!',
            'subject_id.required'    => 'Please select subject!',
            'subject_id.exists'      => 'Invalid subject selected!',
            'subject_id.unique'      => 'This subject is already mapped to this class!',
            'is_compulsory.required' => 'Please specify if the subject is compulsory!',
            'is_compulsory.boolean'  => 'Invalid value for compulsory flag!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            return redirect()->back()->withErrors($errorMessage)->withInput();
        }

        if (!empty($request->get('id'))) {
            $mapping = ClassSubjectMapping::where('id', $request->get('id'))
                ->where('tenant_id', auth()->user()->tenant_id)
                ->first();

            if (empty($mapping)) {
                abort(404);
            }

            $redirect = [
                'url' => redirect()->back(),
                'msg' => 'Class–Subject mapping updated successfully',
            ];
        } else {
            $mapping = new ClassSubjectMapping;
            $mapping->tenant_id = auth()->user()->tenant_id;
    
            $redirect = [
                'url' => redirect()->route($this->mappingListRoute),
                'msg' => 'Class–Subject mapping created successfully',
            ];
        }

        $mapping->class_id      = $request->get('class_id');
        $mapping->subject_id    = $request->get('subject_id');
        $mapping->is_compulsory = $request->get('is_compulsory');

        try {
            $mapping->save();

            return $redirect['url']->with('success', $redirect['msg']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong, please try again later!');
        }
    }

    public function destroy(ClassSubjectMapping $mapping)
    {
        $this->authorizeTenant($mapping);

        try {
            $mapping->delete();
            return back()->with('success', 'Class–Subject mapping deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete mapping, it may be in use.');
        }
    }

    protected function authorizeTenant(ClassSubjectMapping $mapping)
    {
        if ($mapping->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }
    }   
}
