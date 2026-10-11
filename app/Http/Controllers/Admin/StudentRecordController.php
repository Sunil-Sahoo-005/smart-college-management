<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentRecord;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudentRecordController extends Controller
{
    protected $studentRecordListRoute = 'admin.student.records.index';

    public function index(Student $student)
    {
        $records = StudentRecord::where('tenant_id', auth()->user()->tenant_id)
            ->where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('Academics.student.records.index', compact('student', 'records'));
    }

    public function create(Student $student)
    {
        $record = new StudentRecord;
        return view('Academics.student.records.form', compact('student', 'record'));
    }

    public function edit(Student $student, StudentRecord $record)
    {
        $this->authorizeTenant($record);
        return view('Academics.student.records.form', compact('student', 'record'));
    }

    public function store(Request $request, Student $student)
    {
        $request->validate([
            'message'   => 'nullable|string|max:1000',
            'file_path' => 'nullable|mimes:pdf|max:10240',
        ]);

        $filePath = null;

        if ($request->hasFile('file_path')) {
            $file      = $request->file('file_path');
            $filename  = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $filePath  = $file->storeAs('student_records', $filename, 'public');
        }

        if (!empty($request->get('id'))) {
            $record = StudentRecord::where('id', $request->get('id'))
                ->where('tenant_id', auth()->user()->tenant_id)
                ->first();

            if (empty($record)) {
                abort(404);
            }

            if ($request->hasFile('file_path') && $record->file_path) {
                $oldPath = storage_path('app/public/' . $record->file_path);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $redirect = [
                'url' => redirect()->back(),
                'msg' => 'Record updated successfully!',
            ];
        } else {
            $record             = new StudentRecord;
            $record->tenant_id  = $student->tenant_id;
            $record->student_id = $student->id;

            $redirect = [
                'url' => redirect()->back(),
                'msg' => 'Record added successfully!',
            ];
        }

        $record->message   = $request->get('message');
        if ($filePath) {
            $record->file_path = $filePath;
        }

        try {
            $record->save();
            return $redirect['url']->with('success', $redirect['msg']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong, please try again later!');
        }
    }

    public function destroy(StudentRecord $record)
    {
        $this->authorizeTenant($record);

        if ($record->file_path) {
            $fullPath = storage_path('app/public/' . $record->file_path);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }

        try {
            $record->delete();
            return back()->with('success', 'Record deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete record, please try again later!');
        }
    }

    protected function authorizeTenant(StudentRecord $record)
    {
        if ($record->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }
    }
}