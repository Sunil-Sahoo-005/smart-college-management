<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GroupMaster;
use Illuminate\Http\Request;

class GroupMasterController extends Controller
{
    public function index()
    {
        $groupMasters = GroupMaster::all();

        $groupMastersJson = $groupMasters->map(fn($gm) => [
            'id'             => $gm->id,
            'name'           => $gm->name,
            'strength'       => $gm->strength,
            'status'         => $gm->status,
            'date_formatted' => $gm->created_at ? $gm->created_at->format('d-m-Y') : '-',
            'edit_url'       => route('group_master.edit', $gm),
            'delete_url'     => route('group_master.destroy', $gm),
        ]);

        return view('Academics.group_master.index', compact('groupMastersJson'));
    }

    public function create()
    {
        return view('Academics.group_master.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255|unique:group_masters,name',
            'strength' => 'required|integer|min:0',
            'status'   => 'required|in:active,inactive',
        ]);

        GroupMaster::create($validated);

        return redirect()->route('group_master.index')
            ->with('success', 'Group Master created successfully.');
    }

    public function edit(GroupMaster $groupMaster)
    {
        return view('Academics.group_master.form', compact('groupMaster'));
    }

    public function update(Request $request, GroupMaster $groupMaster)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255|unique:group_masters,name,' . $groupMaster->id,
            'strength' => 'required|integer|min:0',
            'status'   => 'required|in:active,inactive',
        ]);

        $groupMaster->update($validated);

        return redirect()->route('group_master.index')
            ->with('success', 'Group Master updated successfully.');
    }

    public function destroy(GroupMaster $groupMaster)
    {
        $groupMaster->delete();

        return redirect()->route('group_master.index')
            ->with('success', 'Group Master deleted successfully.');
    }
}