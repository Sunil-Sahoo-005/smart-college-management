<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LibraryMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LibraryMasterController extends Controller
{
    protected $route = 'admin.library_master.index';

 public function index()
{
    $library_master = LibraryMaster::where('tenant_id', auth()->user()->tenant_id)
        ->with(['parent.parent'])
        ->orderBy('id', 'DESC')
        ->get();

  $libraryMasterJson = $library_master->map(function ($m) {
    $typeLabel  = match((int)$m->type) { 1 => 'Room', 2 => 'Row', 3 => 'Shelf', default => '' };
    $typeBadge  = match((int)$m->type) { 1 => 'info', 2 => 'secondary', 3 => 'warning', default => 'light' };
    $statusLabel = $m->status == 1 ? 'Active' : 'Inactive';
    $statusBadge = $m->status == 1 ? 'success' : 'danger';

    $parentName = '—';
    $parentSub  = '';

    if ($m->parent) {
        if ($m->type == 2) {
            // Row → parent = Room
            $parentName = $m->parent->name; // Room name
            $parentSub  = 'Room';
        } elseif ($m->type == 3) {
            // Shelf → parent = Row, grandparent = Room
            $parentName = $m->parent->name;                  // Row name
            $parentSub  = $m->parent->parent->name ?? '';    // Room name
        }
    }

    return [
        'id'          => $m->id,
        'name'        => $m->name,
        'type'        => (string) $m->type,
        'type_label'  => $typeLabel,
        'type_badge'  => $typeBadge,
        'parent_name' => $parentName,  // Row ke liye: Room name | Shelf ke liye: Row name
        'parent_sub'  => $parentSub,   // Row ke liye: "Room" | Shelf ke liye: Room name
        'status'      => (string) $m->status,
        'status_label'=> $statusLabel,
        'status_badge'=> $statusBadge,
        'created_at'  => \Carbon\Carbon::parse($m->created_at)->format('d-m-Y'),
        'edit_url'    => route('admin.library_master.edit', $m->id),
        'delete_url'  => route('admin.library_master.destroy', $m->id),
    ];
});
    $rooms = LibraryMaster::where('tenant_id', auth()->user()->tenant_id)
        ->where('type', 1)
        ->where('status', 1)
        ->orderBy('name')
        ->get();

    return view('Library.library_master.index', compact('library_master', 'libraryMasterJson', 'rooms'));
}

    public function create()
    {
        $library_master = null;
        $parent_options = LibraryMaster::getParentOptions(auth()->user()->tenant_id, $library_master->id ?? null, $library_master->type ?? null);
        return view('Library.library_master.form', compact('library_master', 'parent_options'));
    }

    public function edit($id)
    {
        $library_master = LibraryMaster::find($id);

        if (!$library_master) {
            abort(404);
        }

        $parent_options = LibraryMaster::getParentOptions(auth()->user()->tenant_id, $library_master->id ?? null, $library_master->type ?? null);
        return view('Library.library_master.form', compact('library_master', 'parent_options'));
    }

    public function store(Request $request)
    {
         $tenantId = auth()->user()->tenant_id;
        $validator = Validator::make($request->all(), [
            'name'   => 'required|string|max:255',
            'type'   => 'required|in:1,2,3',
            'parent_id' => [
            'nullable',
            Rule::exists('library_master', 'id')->where(function ($query) use ($tenantId) {
                return $query->where('tenant_id', $tenantId);
            }),
        ],
            'status' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if ($request->id) {

            $library_master = LibraryMaster::where('id', $request->id)
                ->where('tenant_id', auth()->user()->tenant_id) // 🔒 सुरक्षा
                ->first();
            if (!$library_master) {
                abort(404);
            }

            $msg = 'Library master updated successfully';
            $redirect = redirect()->back();
        }
        else {
            $library_master = new LibraryMaster();
            $library_master->tenant_id = auth()->user()->tenant_id;
            $msg = 'Library master created successfully';
            $redirect = redirect()->route($this->route);
        }

        $library_master->name   = $request->name;
        $library_master->type   = $request->type;
        $library_master->parent_id = $request->parent_id ?: null;
        $library_master->status = $request->status;

        try {
            $library_master->save();
            return $redirect->with('success', $msg);
        } catch (\Exception $e) {
            // echo $e->getMessage();
            // exit;
            return redirect()->back()->withErrors('Something went wrong!');
        }
    }
    public function getParents(Request $request)
    {
        $type = $request->get('type');
        $currentId = $request->get('current_id');

        $parents = LibraryMaster::getParentOptions(
            auth()->user()->tenant_id,
            $currentId,
            $type
        );

        return response()->json($parents);
    }
    public function getRowsByRoom(Request $request)
{
    $rows = LibraryMaster::where('tenant_id', auth()->user()->tenant_id)
        ->where('status', 1)
        ->where('type', 2)
        ->where('parent_id', $request->room_id)
        ->orderBy('name')
        ->get(['id', 'name']);

    return response()->json($rows);
}

public function getShelvesByRow(Request $request)
{
    $shelves = LibraryMaster::where('tenant_id', auth()->user()->tenant_id)
        ->where('status', 1)
        ->where('type', 3)
        ->where('parent_id', $request->row_id)
        ->orderBy('name')
        ->get(['id', 'name']);

    return response()->json($shelves);
}
    public function destroy($id)
    {
        $library_master = LibraryMaster::find($id);

        if (!$library_master) {
            abort(404);
        }

        try {
            $library_master->delete();
            return redirect()->back()->with('success', 'Library master deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong!');
        }
    }
}
