<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use App\Models\ClassStreamMapping;
use App\Models\CourseStream;
use App\Models\Stream;
use App\Models\Section;
use App\Models\Subject;
use App\Models\ClassSubjectMapping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StreamController extends Controller
{
    protected $streamListRoute = 'admin.streams.index';

    // =========================================================================
    // CRUD — Stream itself
    // =========================================================================

    public function index()
    {
        $streams = Stream::where('tenant_id', auth()->user()->tenant_id)
            ->withCount('courseStreams')
            ->orderBy('name')
            ->get();

        $streamsJson = $streams->map(function ($stream) {
            return [
                'id'         => $stream->id,
                'name'       => $stream->name,
                'code'       => $stream->code,
                'edit_url'   => route('admin.streams.edit',    $stream),
                'delete_url' => route('admin.streams.destroy', $stream),
            ];
        });

        return view('Academics.streams.index', compact('streams', 'streamsJson'));
    }

    public function create()
    {
        return view('Academics.streams.form');
    }

    public function edit(Stream $stream)
    {
        $this->authorizeTenant($stream);
        return view('Academics.streams.form', compact('stream'));
    }

    public function store(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $rules = [
            'name' => 'required|min:1|max:50|unique:streams,name,' . $request->id . ',id,tenant_id,' . $tenantId,
            'code' => 'required|string|max:20|unique:streams,code,' . $request->id . ',id,tenant_id,' . $tenantId,
        ];

        $messages = [
            'name.required' => 'Please enter stream name!',
            'name.min'      => 'Stream name should be minimum of 1 character!',
            'name.max'      => 'Stream name should be maximum of 50 characters!',
            'name.unique'   => 'Stream name already exists!',
            'code.required' => 'Please enter branch code!',
            'code.max'      => 'Branch code cannot exceed 20 characters!',
            'code.unique'   => 'Branch code already exists for this tenant!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator->errors()->first())
                ->withInput();
        }

        if (!empty($request->id)) {
            $stream = Stream::where('id', $request->id)
                ->where('tenant_id', $tenantId)
                ->firstOrFail();

            $redirect = [
                'url' => redirect()->back(),
                'msg' => 'Stream updated successfully',
            ];
        } else {
            $stream = new Stream();
            $stream->tenant_id = $tenantId;

            $redirect = [
                'url' => redirect()->route($this->streamListRoute),
                'msg' => 'Stream created successfully',
            ];
        }

        $stream->name = trim($request->name);
        $stream->code = trim($request->code);

        try {
            $stream->save();
            return $redirect['url']->with('success', $redirect['msg']);
        } catch (\Exception $e) {
            echo $e->getMessage(); // For debugging purposes, remove in production
            return redirect()->back()->with('error', 'Something went wrong, please try again later!');
        }
    }

    public function destroy(Stream $stream)
    {
        $this->authorizeTenant($stream);

        try {
            $stream->delete();
            return back()->with('success', 'Stream deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete stream, it may be in use.');
        }
    }

    // =========================================================================
    // Manage — scoped to a CourseStream pivot record
    // =========================================================================

    public function manage(CourseStream $courseStream)
    {
        $this->authorizeCourseStream($courseStream);

        $assignedMappings = ClassStreamMapping::with([
                'class',
                'sections',
                'classSubjectMappings.subject',
            ])
            ->where('course_stream_id', $courseStream->id)
            ->where('tenant_id', auth()->user()->tenant_id)
            ->get()
            ->sortBy(fn($m) => $m->class->name ?? '');

        $assignedClassIds = $assignedMappings->pluck('class_id');

        $availableClasses = Classe::where('tenant_id', auth()->user()->tenant_id)
            ->whereNotIn('id', $assignedClassIds)
            ->orderBy('name')
            ->get();

        $allSubjects = Subject::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        $courseStream->load(['stream', 'course']);

        return view('Academics.streams.manage', compact(
            'courseStream',
            'assignedMappings',
            'availableClasses',
            'allSubjects'
        ));
    }

    // =========================================================================
    // Class ↔ CourseStream mapping
    // =========================================================================

    public function addClass(Request $request, CourseStream $courseStream)
    {
        $this->authorizeCourseStream($courseStream);

        $request->validate([
            'class_id' => 'required|exists:classes,id',
        ]);

        $exists = ClassStreamMapping::where('course_stream_id', $courseStream->id)
            ->where('class_id', $request->class_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'This class is already assigned to this branch.');
        }

        ClassStreamMapping::create([
            'tenant_id'        => auth()->user()->tenant_id,
            'course_stream_id' => $courseStream->id,
            'class_id'         => $request->class_id,
        ]);

        return back()->with('success', 'Class added to branch successfully.');
    }

    public function removeClass(CourseStream $courseStream, Classe $class)
    {
        $this->authorizeCourseStream($courseStream);

        ClassStreamMapping::where('course_stream_id', $courseStream->id)
            ->where('class_id', $class->id)
            ->delete();

        return back()->with('success', 'Class removed from branch successfully.');
    }

    // =========================================================================
    // Sections — under ClassStreamMapping
    // =========================================================================

    public function storeSection(Request $request, CourseStream $courseStream, ClassStreamMapping $mapping)
    {
        $this->authorizeCourseStream($courseStream);
        $this->assertMappingBelongs($mapping, $courseStream);

        $request->validate([
            'sections'                => 'required|array|min:1',
            'sections.*.name'         => 'nullable|string|max:100',
            'sections.*.max_strength' => 'nullable|integer|min:1',
        ]);

        $created = 0;
        $skipped = 0;

        foreach ($request->sections as $sectionData) {
            $name = trim($sectionData['name'] ?? '');

            if (empty($name)) continue;

            $exists = Section::where('tenant_id',       auth()->user()->tenant_id)
                              ->where('class_stream_id', $mapping->id)
                              ->whereRaw('LOWER(name) = ?', [strtolower($name)])
                              ->exists();

            if ($exists) { $skipped++; continue; }

            Section::create([
                'tenant_id'       => auth()->user()->tenant_id,
                'class_id'        => $mapping->class_id,
                'class_stream_id' => $mapping->id,
                'name'            => $name,
                'max_strength'    => $sectionData['max_strength'] ?? null,
            ]);

            $created++;
        }

        if ($created > 0 && $skipped > 0)   $msg = "{$created} section(s) created. {$skipped} skipped (already exist).";
        elseif ($created > 0)                $msg = "{$created} section(s) created successfully.";
        elseif ($skipped > 0)                $msg = "No new sections added — all already exist.";
        else                                 $msg = "No section names were entered.";

        return back()->with('success', $msg);
    }

    public function destroySection(CourseStream $courseStream, ClassStreamMapping $mapping, Section $section)
    {
        $this->authorizeCourseStream($courseStream);
        $this->assertMappingBelongs($mapping, $courseStream);

        if ($section->class_stream_id !== $mapping->id) abort(404);

        $section->delete();
        return back()->with('success', 'Section deleted successfully.');
    }

    // =========================================================================
    // Subjects — under ClassStreamMapping
    // =========================================================================

    public function storeSubject(Request $request, CourseStream $courseStream, ClassStreamMapping $mapping)
    {
        $this->authorizeCourseStream($courseStream);
        $this->assertMappingBelongs($mapping, $courseStream);

        $request->validate([
            'subjects'                  => 'required|array|min:1',
            'subjects.*.subject_id'     => 'nullable|exists:subjects,id',
            'subjects.*.is_compulsory'  => 'nullable|boolean',
        ]);

        $created = 0;
        $skipped = 0;

        foreach ($request->subjects as $subjectData) {
            $subjectId    = $subjectData['subject_id']    ?? null;
            $isCompulsory = $subjectData['is_compulsory'] ?? 1;

            if (empty($subjectId)) continue;

            $exists = ClassSubjectMapping::where('class_stream_id', $mapping->id)
                                         ->where('subject_id', $subjectId)
                                         ->exists();

            if ($exists) { $skipped++; continue; }

            ClassSubjectMapping::create([
                'tenant_id'       => auth()->user()->tenant_id,
                'class_stream_id' => $mapping->id,
                'subject_id'      => $subjectId,
                'is_compulsory'   => $isCompulsory,
            ]);

            $created++;
        }

        if ($created > 0 && $skipped > 0)   $msg = "{$created} subject(s) assigned. {$skipped} skipped (already exist).";
        elseif ($created > 0)                $msg = "{$created} subject(s) assigned successfully.";
        elseif ($skipped > 0)                $msg = "No new subjects added — all already assigned.";
        else                                 $msg = "No subjects were selected.";

        return back()->with('success', $msg);
    }

    public function destroySubject(CourseStream $courseStream, ClassStreamMapping $mapping, ClassSubjectMapping $subjectMapping)
    {
        $this->authorizeCourseStream($courseStream);
        $this->assertMappingBelongs($mapping, $courseStream);

        if ($subjectMapping->class_stream_id !== $mapping->id) abort(404);

        $subjectMapping->delete();
        return back()->with('success', 'Subject removed successfully.');
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    protected function authorizeTenant(Stream $stream): void
    {
        if ($stream->tenant_id !== auth()->user()->tenant_id) abort(403);
    }

    protected function authorizeCourseStream(CourseStream $courseStream): void
    {
        if ($courseStream->tenant_id !== auth()->user()->tenant_id) abort(403);
    }

    protected function assertMappingBelongs(ClassStreamMapping $mapping, CourseStream $courseStream): void
    {
        if ($mapping->course_stream_id !== $courseStream->id) abort(404);
    }
}