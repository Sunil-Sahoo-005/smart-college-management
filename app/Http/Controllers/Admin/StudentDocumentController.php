<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class StudentDocumentController extends Controller
{
    // STORE
    public function store(Request $request, Student $student)
    {
        $rules = [
            'doc_type' => 'required|string|max:50',
            'document' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $file = $request->file('document');
        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
        $path = $file->storeAs('students/' . $student->id . '/documents', $filename, 'public');

        $doc = new StudentDocument;
        $doc->student_id = $student->id;
        $doc->doc_type = $request->get('doc_type');
        $doc->file_path = $path;
        $doc->uploaded_by_user_id = auth()->id();

        try {
            $doc->save();
            return redirect()->back()->with('success', 'Document uploaded successfully.');
        } catch (\Exception $e) {
            // Delete the file if save fails
            Storage::disk('public')->delete($path);
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    // DESTROY (DELETE)
    public function destroy(Student $student, StudentDocument $document)
    {
        // Security: document is student ka hi hona chahiye
        if ($document->student_id !== $student->id) {
            abort(403, 'Unauthorized access');
        }

        try {
            // Delete the physical file
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }
            
            // Delete the database record
            $document->delete();
            
            return redirect()->back()->with('success', 'Document deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
}