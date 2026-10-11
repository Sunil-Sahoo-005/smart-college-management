<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentAcademicQualification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentAcademicQualificationController extends Controller
{
    public function store(Request $request, Student $student)
    {
        $validated = $request->validate([
            'qualification_level' => 'required|in:10th,12th,diploma,graduation,post_graduation,phd,other',
            'board_university'    => 'nullable|string|max:255',
            'school_college'      => 'nullable|string|max:255',
            'passing_year'        => 'nullable|digits:4',
            'percentage'          => 'nullable|numeric|min:0|max:100',
            'grade'               => 'nullable|string|max:50',
            'stream'              => 'nullable|string|max:100',
            'roll_number'         => 'nullable|string|max:100',
            'certificate'         => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $validated['student_id']  = $student->id;
        $validated['uploaded_by'] = auth()->id();

        if ($request->hasFile('certificate')) {
            $validated['certificate_path'] = $request->file('certificate')
                ->store("students/{$student->id}/qualifications", 'public');
        }

        StudentAcademicQualification::create($validated);

        return back()->with('success', 'Qualification added successfully.');
    }

    public function edit(Student $student, StudentAcademicQualification $qualification)
    {
        abort_if($qualification->student_id !== $student->id, 403);
        return redirect()->route('admin.students.show', $student);
    }

    public function update(Request $request, Student $student, StudentAcademicQualification $qualification)
    {
        abort_if($qualification->student_id !== $student->id, 403);

        $validated = $request->validate([
            'qualification_level' => 'required|in:10th,12th,diploma,graduation,post_graduation,phd,other',
            'board_university'    => 'nullable|string|max:255',
            'school_college'      => 'nullable|string|max:255',
            'passing_year'        => 'nullable|digits:4',
            'percentage'          => 'nullable|numeric|min:0|max:100',
            'grade'               => 'nullable|string|max:50',
            'stream'              => 'nullable|string|max:100',
            'roll_number'         => 'nullable|string|max:100',
            'certificate'         => 'nullable|file|mimes:pdf|max:10240',
        ]);

        if ($request->hasFile('certificate')) {
            // Purana file delete karo
            if ($qualification->certificate_path &&
                Storage::disk('public')->exists($qualification->certificate_path)) {
                Storage::disk('public')->delete($qualification->certificate_path);
            }
            $validated['certificate_path'] = $request->file('certificate')
                ->store("students/{$student->id}/qualifications", 'public');
        }

        $qualification->update($validated);

        return back()->with('success', 'Qualification updated successfully.');
    }

    public function destroy(Student $student, StudentAcademicQualification $qualification)
    {
        abort_if($qualification->student_id !== $student->id, 403);

        if ($qualification->certificate_path &&
            Storage::disk('public')->exists($qualification->certificate_path)) {
            Storage::disk('public')->delete($qualification->certificate_path);
        }

        $qualification->delete();

        return back()->with('success', 'Qualification deleted successfully.');
    }
}