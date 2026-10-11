<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassStreamMapping;
use App\Models\Classe;
use App\Models\Stream;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassStreamMappingController extends Controller
{
    protected $mappingListRoute = 'admin.class_stream_mappings.index';

    public function index()
{
    $mappings = ClassStreamMapping::where('tenant_id', auth()->user()->tenant_id)
        ->with(['class', 'stream'])
        ->orderBy('class_id')
        ->get();

    $mappingsJson = $mappings->map(function ($mapping) {
        return [
            'id'          => $mapping->id,
            'stream_name' => $mapping->stream?->name ?? 'N/A',
            'class_name'  => $mapping->class?->name  ?? 'N/A',
            'edit_url'    => route('admin.class_stream_mappings.edit', $mapping),
            'delete_url'  => route('admin.class_stream_mappings.destroy', $mapping),
        ];
    });

    return view('Academics.class_stream_mappings.index', compact('mappings', 'mappingsJson'));
}

    public function create()
    {
        $classes = Classe::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('order_index')
            ->orderBy('name')
            ->get();

        $streams = Stream::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        return view('Academics.class_stream_mappings.form', compact('classes', 'streams'));
    }

    public function edit(ClassStreamMapping $mapping)
    {
        $this->authorizeTenant($mapping);

        $classes = Classe::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('order_index')
            ->orderBy('name')
            ->get();

        $streams = Stream::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        return view('Academics.class_stream_mappings.form', compact('mapping', 'classes', 'streams'));
    }

    public function store(Request $request)
    {
        $rules = [
            'class_id' => 'required|exists:classes,id',
            'stream_id' => 'required|exists:streams,id|unique:class_stream_mappings,stream_id,NULL,id,class_id,' . $request->get('class_id'),
        ];

        $messages = [
            'class_id.required' => 'Please select class!',
            'class_id.exists'   => 'Invalid class selected!',
            'stream_id.required' => 'Please select stream!',
            'stream_id.exists'  => 'Invalid stream selected!',
            'stream_id.unique'  => 'This stream is already mapped to this class!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            return redirect()->back()->withErrors($errorMessage)->withInput();
        }

        if (!empty($request->get('id'))) {
            $mapping = ClassStreamMapping::where('id', $request->get('id'))
                ->where('tenant_id', auth()->user()->tenant_id)
                ->first();

            if (empty($mapping)) {
                abort(404);
            }

            $redirect = [
                'url' => redirect()->back(),
                'msg' => 'Class–Stream mapping updated successfully',
            ];
        } else {
            $mapping = new ClassStreamMapping;
            $mapping->tenant_id = auth()->user()->tenant_id;

            $redirect = [
                'url' => redirect()->route($this->mappingListRoute),
                'msg' => 'Class–Stream mapping created successfully',
            ];
        }

        $mapping->class_id = $request->get('class_id');
        $mapping->stream_id = $request->get('stream_id');

        try {
            $mapping->save();

            return $redirect['url']->with('success', $redirect['msg']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong, please try again later!');
        }
    }

    public function destroy(ClassStreamMapping $mapping)
    {
        $this->authorizeTenant($mapping);

        try {
            $mapping->delete();
            return back()->with('success', 'Class–Stream mapping deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete mapping, it may be in use.');
        }
    }

    protected function authorizeTenant(ClassStreamMapping $mapping)
    {
        if ($mapping->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }
    }
}
