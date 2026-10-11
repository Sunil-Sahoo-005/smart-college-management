<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PracticalGroups;
use App\Models\GroupMaster;
use App\Models\Course;
use App\Models\Stream;
use App\Models\AcademicYear;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PracticalGroupController extends Controller
{
    /* ═══════════════════════════════════════════
     *  INDEX
     * ═══════════════════════════════════════════ */
    public function index()
    {
        $practicalGroups = PracticalGroups::where('tenant_id', Auth::user()->tenant_id)
            ->with(['course', 'stream', 'academicYears', 'groupMaster', 'students'])
            ->latest()
            ->paginate(12);

        return view('Academics.practical_groups.index', compact('practicalGroups'));
    }

    /* ═══════════════════════════════════════════
     *  CREATE
     * ═══════════════════════════════════════════ */
    public function create()
    {
        $courses       = Course::where('tenant_id', Auth::user()->tenant_id)->get();
        $streams       = Stream::where('tenant_id', Auth::user()->tenant_id)->get();
        $academicYears = AcademicYear::where('tenant_id', Auth::user()->tenant_id)->get();
        $groupMasters  = GroupMaster::where('status', 'active')->get();

        return view('Academics.practical_groups.form',
            compact('courses', 'streams', 'academicYears', 'groupMasters'));
    }

    /* ═══════════════════════════════════════════
     *  STORE
     * ═══════════════════════════════════════════ */
    public function store(Request $request)
    {
        $request->validate([
            'course_id'           => 'required|exists:courses,id',
            'stream_id'           => 'required|exists:streams,id',
            'group_master_id'     => 'required|exists:group_masters,id',
            'academic_year_ids'   => 'required|array|min:1',
            'academic_year_ids.*' => 'exists:academic_years,id',
            'student_ids'         => 'nullable|array',
            'student_ids.*'       => 'exists:students,id',
        ]);

        // Agar same course+stream+group_master ka record already exist karta hai
        // toh naya mat banao — usse update karo (redirect to edit)
        $existingGroup = PracticalGroups::where('tenant_id', Auth::user()->tenant_id)
            ->where('course_id', $request->course_id)
            ->where('stream_id', $request->stream_id)
            ->where('group_master_id', $request->group_master_id)
            ->first();

        if ($existingGroup) {
            // Sirf students sync karo
            $existingGroup->academicYears()->sync($request->academic_year_ids);
            $existingGroup->students()->sync($request->student_ids ?? []);
            return redirect()->route('practical_groups.index')
                ->with('success', 'Practical Group updated successfully.');
        }

        // Check: koi student already kisi aur group mein toh nahi hai?
        if (!empty($request->student_ids)) {
            $alreadyAssigned = \DB::table('practical_group_students')
                ->whereIn('student_id', $request->student_ids)
                ->pluck('student_id')
                ->toArray();

            if (!empty($alreadyAssigned)) {
                $names = Student::whereIn('id', $alreadyAssigned)
                    ->get()
                    ->map(fn($s) => $s->first_name . ' ' . $s->last_name)
                    ->implode(', ');
                return back()->withErrors([
                    'student_ids' => "Ye student(s) already kisi aur practical group mein assigned hain: {$names}"
                ])->withInput();
            }
        }

        // Derive a name from the group master so the name column is never empty
        $groupMaster = GroupMaster::findOrFail($request->group_master_id);
        $name = $groupMaster->name;

        // Make name unique within tenant if another record already uses it
        $base  = $name;
        $count = 1;
        while (
            PracticalGroups::where('tenant_id', Auth::user()->tenant_id)
                ->where('name', $name)
                ->exists()
        ) {
            $name = $base . ' ' . ++$count;
        }

        $practicalGroup = PracticalGroups::create([
            'tenant_id'       => Auth::user()->tenant_id,
            'name'            => $name,
            'course_id'       => $request->course_id,
            'stream_id'       => $request->stream_id,
            'group_master_id' => $request->group_master_id,
            'status'          => 'active',
        ]);

        $practicalGroup->academicYears()->sync($request->academic_year_ids);
        $practicalGroup->students()->sync($request->student_ids ?? []);

        return redirect()->route('practical_groups.index')
            ->with('success', 'Practical Group created successfully.');
    }

    /* ═══════════════════════════════════════════
     *  EDIT
     * ═══════════════════════════════════════════ */
    public function edit(PracticalGroups $practicalGroup)
    {
        $this->authorizeTenant($practicalGroup);

        $courses       = Course::where('tenant_id', Auth::user()->tenant_id)->get();
        $streams       = Stream::where('tenant_id', Auth::user()->tenant_id)->get();
        $academicYears = AcademicYear::where('tenant_id', Auth::user()->tenant_id)->get();
        $groupMasters  = GroupMaster::where('status', 'active')->get();

        return view('Academics.practical_groups.form',
            compact('practicalGroup', 'courses', 'streams', 'academicYears', 'groupMasters'));
    }

    /* ═══════════════════════════════════════════
     *  UPDATE
     * ═══════════════════════════════════════════ */
    public function update(Request $request, PracticalGroups $practicalGroup)
    {
        $this->authorizeTenant($practicalGroup);

        $request->validate([
            'course_id'           => 'required|exists:courses,id',
            'stream_id'           => 'required|exists:streams,id',
            'group_master_id'     => 'required|exists:group_masters,id',
            'academic_year_ids'   => 'required|array|min:1',
            'academic_year_ids.*' => 'exists:academic_years,id',
            'student_ids'         => 'nullable|array',
            'student_ids.*'       => 'exists:students,id',
        ]);

        // Check: koi student already kisi AUR group mein toh nahi hai? (current group exclude)
        if (!empty($request->student_ids)) {
            $alreadyAssigned = \DB::table('practical_group_students')
                ->whereIn('student_id', $request->student_ids)
                ->where('practical_group_id', '!=', $practicalGroup->id)
                ->pluck('student_id')
                ->toArray();

            if (!empty($alreadyAssigned)) {
                $names = Student::whereIn('id', $alreadyAssigned)
                    ->get()
                    ->map(fn($s) => $s->first_name . ' ' . $s->last_name)
                    ->implode(', ');
                return back()->withErrors([
                    'student_ids' => "Ye student(s) already kisi aur practical group mein assigned hain: {$names}"
                ])->withInput();
            }
        }

        // If group master changed, update the name accordingly
        if ($practicalGroup->group_master_id != $request->group_master_id) {
            $groupMaster = GroupMaster::findOrFail($request->group_master_id);
            $base  = $groupMaster->name;
            $name  = $base;
            $count = 1;
            while (
                PracticalGroups::where('tenant_id', Auth::user()->tenant_id)
                    ->where('name', $name)
                    ->where('id', '!=', $practicalGroup->id)
                    ->exists()
            ) {
                $name = $base . ' ' . ++$count;
            }
            $practicalGroup->name = $name;
        }

        $practicalGroup->update([
            'name'            => $practicalGroup->name,
            'course_id'       => $request->course_id,
            'stream_id'       => $request->stream_id,
            'group_master_id' => $request->group_master_id,
            'status'          => $request->status,
        ]);

        $practicalGroup->academicYears()->sync($request->academic_year_ids);
        $practicalGroup->students()->sync($request->student_ids ?? []);

        return redirect()->route('practical_groups.index')
            ->with('success', 'Practical Group updated successfully.');
    }

    /* ═══════════════════════════════════════════
     *  DESTROY
     * ═══════════════════════════════════════════ */
    public function destroy(PracticalGroups $practicalGroup)
    {
        $this->authorizeTenant($practicalGroup);
        $practicalGroup->students()->detach();
        $practicalGroup->academicYears()->detach();
        $practicalGroup->delete();

        return redirect()->route('practical_groups.index')
            ->with('success', 'Practical Group deleted successfully.');
    }

    /* ═══════════════════════════════════════════
     *  AJAX – streams by course
     * ═══════════════════════════════════════════ */
    public function getStreams($courseId)
    {
        $streams = Stream::whereHas('courses', function ($q) use ($courseId) {
            $q->where('course_id', $courseId);
        })->where('tenant_id', Auth::user()->tenant_id)->get(['id', 'name']);

        return response()->json($streams);
    }

    /* ═══════════════════════════════════════════
     *  AJAX – students filtered by course + stream + academic years
     * ═══════════════════════════════════════════ */
    public function getStudents(Request $request)
    {
        $request->validate([
            'course_id'         => 'required|exists:courses,id',
            'stream_id'         => 'required|exists:streams,id',
            'academic_year_ids' => 'required|array|min:1',
        ]);

        /*
         * Students ke direct columns mein course/stream/year nahi hain.
         * Ye sab StudentClassEnrollment (enrollments) table mein hain.
         * isliye whereHas se filter karo.
         */
        $excludeGroupId  = $request->input('exclude_group_id');  // edit mode: current pg id
        $groupMasterId   = $request->input('group_master_id');   // same-group pre-load ke liye

        // Is group master ke already assigned students ka practical_group_id dhundo
        $sameGroupPgId = null;
        if ($groupMasterId) {
            $sameGroupPgId = \DB::table('practical_groups')
                ->where('tenant_id', Auth::user()->tenant_id)
                ->where('group_master_id', $groupMasterId)
                ->where('course_id', $request->course_id)
                ->where('stream_id', $request->stream_id)
                ->when($excludeGroupId, fn($q) => $q->where('id', '!=', $excludeGroupId))
                ->value('id');
        }

        // Students jo already DOOSRE groups mein hain (same group aur current edit group exclude)
        $assignedStudentIds = \DB::table('practical_group_students')
            ->when($excludeGroupId, fn($q) => $q->where('practical_group_id', '!=', $excludeGroupId))
            ->when($sameGroupPgId,  fn($q) => $q->where('practical_group_id', '!=', $sameGroupPgId))
            ->pluck('student_id')
            ->toArray();

        // Same group mein already saved students (right panel mein pre-load honge)
        $preAssignedIds = $sameGroupPgId
            ? \DB::table('practical_group_students')
                ->where('practical_group_id', $sameGroupPgId)
                ->pluck('student_id')
                ->toArray()
            : [];

        $students = Student::where('tenant_id', Auth::user()->tenant_id)
            ->whereHas('enrollments', function ($q) use ($request) {
                $q->where('course_id', $request->course_id)
                  ->where('stream_id', $request->stream_id)
                  ->whereIn('academic_year_id', $request->academic_year_ids)
                  ->whereIn('status', ['active', 'promoted']);
            })
            ->whereNotIn('id', $assignedStudentIds)
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'roll_number'])
            ->map(fn($s) => array_merge($s->toArray(), [
                'pre_assigned' => in_array($s->id, $preAssignedIds),
                'pre_assigned_pg_id' => $sameGroupPgId,
            ]));

        return response()->json($students);
    }

    /* ─── helpers ──────────────────────────────────────────── */

    protected function authorizeTenant($model)
    {
        if ($model->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }
    }
}