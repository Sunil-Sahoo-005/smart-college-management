<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicGroups;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AcademicGroupsController extends Controller
{
    protected $academicGroupsListRoute = 'admin.academic_groups.index';

    // ✅ INDEX
    public function index()
{
    $academic_groups = AcademicGroups::where('tenant_id', auth()->user()->tenant_id)
        ->with('parent')
        ->latest()
        ->get();

    $academicGroupsJson = $academic_groups->map(function ($group) {
        return [
            'id'          => $group->id,
            'name'        => $group->name,
            'parent_name' => $group->parent?->name ?? '—',
            'status'      => (string) $group->status,
            'status_label'=> $group->status == 1 ? 'Active' : 'Inactive',
            'status_badge'=> $group->status == 1 ? 'success' : 'danger',
            'edit_url'    => route('admin.academic_groups.edit', $group),
            'delete_url'  => route('admin.academic_groups.destroy', $group),
        ];
    });

    return view('Library.academic_groups.index', compact('academic_groups', 'academicGroupsJson'));
}

    // ✅ CREATE
    public function create()
    {
        
        $academic_groups = null;

        $parent_groups = AcademicGroups::where('tenant_id', auth()->user()->tenant_id)
            // ->whereNull('parent_id')
            ->get();
            // dd($parent_groups);

        return view('Library.academic_groups.form', compact('academic_groups', 'parent_groups'));
    }

    // ✅ EDIT
    public function edit($id)
    {
        $academic_groups = AcademicGroups::where('id', $id)
            ->where('tenant_id', auth()->user()->tenant_id)
            ->first();

        if (empty($academic_groups)) {
            abort(404);
        }

        $parent_groups = AcademicGroups::where('tenant_id', auth()->user()->tenant_id)
            ->where('id', '!=', $id) // खुद को parent न बना सके
            ->get();

        return view('Library.academic_groups.form', compact('academic_groups', 'parent_groups'));
    }

    // ✅ STORE + UPDATE (Same Method)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'       => 'required|string|max:255',
            'parent_id'  => 'nullable|exists:academic_groups,id',
            'status'     => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // ✅ UPDATE
        if (!empty($request->get('id'))) {

            $academic_groups = AcademicGroups::where('id', $request->get('id'))
                ->where('tenant_id', auth()->user()->tenant_id)
                ->first();

            if (empty($academic_groups)) {
                abort(404);
            }

            $redirect = [
                'url' => redirect()->back(),
                'msg' => 'Academic group updated successfully',
            ];

        } else {
            // ✅ CREATE
            $academic_groups = new AcademicGroups;
            $academic_groups->tenant_id = auth()->user()->tenant_id;

            $redirect = [
                'url' => redirect()->route($this->academicGroupsListRoute),
                'msg' => 'Academic group created successfully',
            ];
        }

        // ✅ SAVE DATA
        $academic_groups->name = $request->get('name');
        $academic_groups->parent_id = $request->get('parent_id');
        $academic_groups->status = $request->get('status');

        try {
            $academic_groups->save();
            return $redirect['url']->with('success', $redirect['msg']);

        } catch (\Exception $e) {
            echo $e->getMessage();
            exit; // Debug log
            return redirect()->back()->withErrors(['Something went wrong, please try again later!']);
        }
    }

    // ✅ DELETE
    public function destroy($id)
    {
        $academic_groups = AcademicGroups::where('id', $id)
            ->where('tenant_id', auth()->user()->tenant_id)
            ->first();

        if (empty($academic_groups)) {
            abort(404);
        }

        try {
            $academic_groups->delete();
            return redirect()->back()->with('success', 'Academic group deleted successfully');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong, please try again later!');
        }
    }
}