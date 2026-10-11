<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentParent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentParentController extends Controller
{
    // STORE
    public function store(Request $request, Student $student)
    {
        $rules = [
            'parent_type' => 'required|in:father,mother,guardian',
            'first_name'  => 'required|min:1|max:50',
            'last_name'   => 'required|min:1|max:50',
            'phone'       => 'required|min:10|max:15',
            'email'       => 'nullable|email|max:100',
            'occupation'  => 'nullable|max:100',
            'address'     => 'nullable|max:500',
            'relationship'=> 'nullable|max:100',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // If emergency contact, remove others
        if ($request->has('is_emergency_contact')) {
            StudentParent::where('student_id', $student->id)
                ->update(['is_emergency_contact' => 0]);
        }

        $parent = new StudentParent;
        $parent->student_id           = $student->id;
        $parent->parent_type          = $request->get('parent_type');
        $parent->first_name           = trim($request->get('first_name'));
        $parent->last_name            = trim($request->get('last_name'));
        $parent->phone                = $request->get('phone');
        $parent->email                = $request->get('email');
        $parent->occupation           = $request->get('occupation');
        $parent->address              = $request->get('address');
        $parent->relationship         = $request->get('relationship');
        $parent->is_emergency_contact = $request->has('is_emergency_contact') ? 1 : 0;

        try {
            $parent->save();
            return redirect()->back()->with('success', 'Parent / Guardian added successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    // UPDATE
    public function update(Request $request, Student $student, StudentParent $parent)
    {
        // ✅ REMOVE THIS dd() - COMMENT OUT OR DELETE
        // dd($request->all());
        
        // Security: parent is student ka hi hona chahiye
        if ($parent->student_id !== $student->id) {
            abort(403, 'Unauthorized access');
        }

        $rules = [
            'parent_type' => 'required|in:father,mother,guardian',
            'first_name'  => 'required|min:1|max:50',
            'last_name'   => 'required|min:1|max:50',
            'phone'       => 'required|min:10|max:15',
            'email'       => 'nullable|email|max:100',
            'occupation'  => 'nullable|max:100',
            'address'     => 'nullable|max:500',
            'relationship'=> 'nullable|max:100',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // If emergency contact, remove others
        if ($request->has('is_emergency_contact')) {
            StudentParent::where('student_id', $student->id)
                ->where('id', '!=', $parent->id)
                ->update(['is_emergency_contact' => 0]);
        }

        $parent->parent_type          = $request->get('parent_type');
        $parent->first_name           = trim($request->get('first_name'));
        $parent->last_name            = trim($request->get('last_name'));
        $parent->phone                = $request->get('phone');
        $parent->email                = $request->get('email');
        $parent->occupation           = $request->get('occupation');
        $parent->address              = $request->get('address');
        $parent->relationship         = $request->get('relationship');
        $parent->is_emergency_contact = $request->has('is_emergency_contact') ? 1 : 0;

        try {
            $parent->save();
            return redirect()->back()->with('success', 'Parent / Guardian updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    // DESTROY
    public function destroy(Student $student, StudentParent $parent)
    {
        // Security: parent is student ka hi hona chahiye
        if ($parent->student_id !== $student->id) {
            abort(403, 'Unauthorized access');
        }

        try {
            $parent->delete();
            return redirect()->back()->with('success', 'Parent / Guardian deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
}