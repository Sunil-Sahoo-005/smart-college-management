<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    protected $listRoute = 'admin.rooms.index';

    public function index(Request $request)
{
    $rooms = Room::where('tenant_id', auth()->user()->tenant_id)
        ->orderBy('name')
        ->get();

    $types = ['classroom', 'lab', 'auditorium', 'library'];

    $roomsJson = $rooms->map(function ($room) {
        return [
            'id'       => $room->id,
            'name'     => $room->name,
            'type'     => $room->type,
            'capacity' => $room->capacity,
            'edit_url'    => route('admin.rooms.edit', $room),
            'delete_url'  => route('admin.rooms.destroy', $room),
        ];
    });

    return view('Academics.rooms.index', compact('rooms', 'roomsJson', 'types'));
}

    public function create()
    {
        $types = ['classroom', 'lab', 'auditorium', 'library'];

        return view('Academics.rooms.form', compact('types'));
    }

    public function edit(Room $room)
    {
        $this->authorizeTenant($room);

        $types = ['classroom', 'lab', 'auditorium', 'library'];

        return view('Academics.rooms.form', compact('room', 'types'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name'     => 'required|string|max:100',
            'type'     => 'required|in:classroom,lab,auditorium,library',
            'capacity' => 'nullable|integer|min:1',
        ];

        $messages = [
            'name.required'  => 'Please enter room name!',
            'name.max'       => 'Room name must not exceed 100 characters!',
            'type.required'  => 'Please select room type!',
            'type.in'        => 'Invalid room type!',
            'capacity.integer' => 'Capacity must be a number!',
            'capacity.min'   => 'Capacity must be at least 1!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator->errors()->first())
                ->withInput();
        }

        if (!empty($request->get('id'))) {
            $room = Room::where('id', $request->get('id'))
                ->where('tenant_id', auth()->user()->tenant_id)
                ->first();

            if (empty($room)) {
                abort(404);
            }

            $redirect = [
                'url' => redirect()->back(),
                'msg' => 'Room updated successfully',
            ];
        } else {
            $room = new Room;
            $room->tenant_id = auth()->user()->tenant_id;

            $redirect = [
                'url' => redirect()->route($this->listRoute),
                'msg' => 'Room created successfully',
            ];
        }

        $room->name     = $request->name;
        $room->type     = $request->type;
        $room->capacity = $request->capacity;

        try {
            $room->save();
            return $redirect['url']->with('success', $redirect['msg']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong, please try again later!');
        }
    }

    public function destroy(Room $room)
    {
        $this->authorizeTenant($room);

        try {
            $room->delete();
            return back()->with('success', 'Room deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete room, it may be in use.');
        }
    }

    protected function authorizeTenant(Room $room)
    {
        if ($room->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }
    }
}
