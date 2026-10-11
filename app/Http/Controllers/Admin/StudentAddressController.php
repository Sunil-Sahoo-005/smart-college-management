<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentAddressController extends Controller
{
    // STORE
    public function store(Request $request, Student $student)
    {
        $rules = [
            'type' => 'required|in:home,permanent',
            'address_line1' => 'required|min:1|max:200',
            'address_line2' => 'nullable|max:200',
            'city' => 'nullable|max:100',
            'state' => 'nullable|max:100',
            'pincode' => 'nullable|max:10',
            'country' => 'nullable|max:100',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $address = new StudentAddress;
        $address->student_id = $student->id;
        $address->type = $request->get('type');
        $address->address_line1 = trim($request->get('address_line1'));
        $address->address_line2 = $request->get('address_line2');
        $address->city = $request->get('city');
        $address->state = $request->get('state');
        $address->pincode = $request->get('pincode');
        $address->country = $request->get('country');

        try {
            $address->save();
            return redirect()->back()->with('success', 'Address added successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    // UPDATE
    public function update(Request $request, Student $student, StudentAddress $address)
    {
        // Security: address is student ka hi hona chahiye
        if ($address->student_id !== $student->id) {
            abort(403, 'Unauthorized access');
        }

        $rules = [
            'type' => 'required|in:home,permanent',
            'address_line1' => 'required|min:1|max:200',
            'address_line2' => 'nullable|max:200',
            'city' => 'nullable|max:100',
            'state' => 'nullable|max:100',
            'pincode' => 'nullable|max:10',
            'country' => 'nullable|max:100',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $address->type = $request->get('type');
        $address->address_line1 = trim($request->get('address_line1'));
        $address->address_line2 = $request->get('address_line2');
        $address->city = $request->get('city');
        $address->state = $request->get('state');
        $address->pincode = $request->get('pincode');
        $address->country = $request->get('country');

        try {
            $address->save();
            return redirect()->back()->with('success', 'Address updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    // DESTROY
    public function destroy(Student $student, StudentAddress $address)
    {
        // Security: address is student ka hi hona chahiye
        if ($address->student_id !== $student->id) {
            abort(403, 'Unauthorized access');
        }

        try {
            $address->delete();
            return redirect()->back()->with('success', 'Address deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
}