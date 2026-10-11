<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\StaffDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StaffDocumentController extends Controller
{
    public function index(Staff $staff)
    {
        $this->authorizeTenant($staff);

        $documents = StaffDocument::where('staff_id', $staff->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('Academics.staff.documents.index', compact('staff', 'documents'));
    }

    public function create(Staff $staff)
    {
        $this->authorizeTenant($staff);

        return view('Academics.staff.documents.create', compact('staff'));
    }

    public function store(Request $request, Staff $staff)
    {
        $this->authorizeTenant($staff);

        $request->validate([
            'documents'          => 'required|array|min:1',
            'documents.*.type'   => 'required|string|max:100',
            'documents.*.file'   => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            'documents.*.note'   => 'nullable|string|max:255',
        ], [
            'documents.required'        => 'Please add at least one document!',
            'documents.*.type.required' => 'Document type is required!',
            'documents.*.file.required' => 'Please upload a file!',
            'documents.*.file.mimes'    => 'File must be PDF, JPG, PNG, DOC or DOCX!',
            'documents.*.file.max'      => 'File size must not exceed 5MB!',
        ]);

        foreach ($request->file('documents') as $index => $doc) {
            if (isset($doc['file'])) {
                $file     = $doc['file'];
                $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $path     = $file->storeAs('staff/documents', $filename, 'public');

                StaffDocument::create([
                    'staff_id'            => $staff->id,
                    'doc_type'            => $request->documents[$index]['type'],
                    'file_path'           => $path,
                    'note'                => $request->documents[$index]['note'] ?? null,
                    'uploaded_by_user_id' => auth()->id(),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Documents uploaded successfully!');
    }

    public function edit(Staff $staff, StaffDocument $document)
    {
        $this->authorizeTenant($staff);

        return view('Academics.staff.documents.edit', compact('staff', 'document'));
    }

    public function update(Request $request, Staff $staff, StaffDocument $document)
    {
        $this->authorizeTenant($staff);

        $request->validate([
            'doc_type' => 'required|string|max:100',
            'note'     => 'nullable|string|max:255',
            'file'     => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ], [
            'doc_type.required' => 'Document type is required!',
            'file.mimes'        => 'File must be PDF, JPG, PNG, DOC or DOCX!',
            'file.max'          => 'File size must not exceed 5MB!',
        ]);

        if ($request->hasFile('file')) {
            // Delete old file
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $file     = $request->file('file');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $path     = $file->storeAs('staff/documents', $filename, 'public');

            $document->file_path = $path;
        }

        $document->doc_type = $request->doc_type;
        $document->note     = $request->note;
        $document->save();

        return redirect()->back()->with('success', 'Document updated successfully!');
    }
    // AJAX - Store Schedule Document
public function storeScheduleDocument(Request $request, Staff $staff)
{
    $this->authorizeTenant($staff);

    $request->validate([
        'doc_type' => 'required|string|max:100',
        'file'     => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        'note'     => 'nullable|string|max:255',
    ]);

    $file     = $request->file('file');
    $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
    $path     = $file->storeAs('staff/schedule-documents', $filename, 'public');

    $doc = StaffDocument::create([
        'staff_id'            => $staff->id,
        'doc_type'            => 'SCHEDULE: ' . $request->doc_type,  // ← prefix se identify
        'file_path'           => $path,
        'note'                => $request->note ?? null,
        'uploaded_by_user_id' => auth()->id(),
    ]);

    return response()->json([
        'success'  => true,
        'document' => [
            'id'         => $doc->id,
            'doc_type'   => str_replace('SCHEDULE: ', '', $doc->doc_type), // prefix hata ke dikhao
            'note'       => $doc->note,
            'file_url'   => Storage::url($doc->file_path),
            'created_at' => $doc->created_at->format('d M Y'),
        ]
    ]);
}

// AJAX - Delete Schedule Document  
public function destroyScheduleDocument($id)
{
    $doc = StaffDocument::findOrFail($id);

    if ($doc->staff->tenant_id !== auth()->user()->tenant_id) {
        abort(403);
    }

    if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
        Storage::disk('public')->delete($doc->file_path);
    }

    $doc->delete();

    return response()->json(['success' => true]);
}

    public function destroy(Staff $staff, StaffDocument $document)
    {
        $this->authorizeTenant($staff);

        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return response()->json(['success' => true]);
    }

    protected function authorizeTenant(Staff $staff)
    {
        if ($staff->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }
    }
}