<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseStream;
use App\Models\Stream;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    protected $courseListRoute = 'admin.courses.index';

    // =========================================================================
    // List all courses (with their streams via course_stream pivot)
    // =========================================================================

    public function index()
    {
        $courses = Course::where('tenant_id', auth()->user()->tenant_id)
            ->with([
                // Load stream through the pivot, and bring along the pivot id
                // so the view can build manage URLs (which need courseStream->id)
                'courseStreams.stream',
            ])
            ->withCount('courseStreams')
            ->orderBy('name')
            ->get();

        return view('Academics.courses.index', compact('courses'));
    }

    // =========================================================================
    // Create / Edit forms
    // =========================================================================

    public function create()
    {
        $streams = Stream::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        return view('Academics.courses.form', compact('streams'));
    }

    public function edit(Course $course)
    {
        $this->authorizeTenant($course);

        $streams = Stream::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('name')
            ->get();

        // IDs of streams currently linked to this course (via course_stream)
        $selectedStreams = $course->streams()->pluck('streams.id')->toArray();

        return view('Academics.courses.form', compact('course', 'streams', 'selectedStreams'));
    }

    // =========================================================================
    // Store (create + update)
    // =========================================================================

    public function store(Request $request)
    { 
        $tenantId = auth()->user()->tenant_id;

        $rules = [
            'name'           => 'required|min:1|max:100|unique:courses,name,'
                . $request->get('id') . ',id,tenant_id,' . $tenantId,
            'code'           => 'required|min:1|max:50|unique:courses,code,'
                . $request->get('id') . ',id,tenant_id,' . $tenantId,
            'duration_years' => 'required|integer|min:1|max:10',
            'course_type'    => 'required|in:semester,yearly',
            'strength'       => 'required|integer|min:0|max:999999',
            'is_lateral_entry' => 'nullable|boolean',
            'lateral_seat_percentage' => 'required_if:is_lateral_entry,1|nullable|numeric|min:0|max:100',
            'streams'        => 'nullable|array',
            'streams.*'      => [
                'integer',
                Rule::exists('streams', 'id')->where(fn($q) => $q->where('tenant_id', $tenantId)),
            ],
        ];

        $messages = [
            'name.required'           => 'Please enter course name!',
            'name.unique'             => 'Course name already exists!',
            'code.required'           => 'Please enter course code!',
            'code.unique'             => 'Course code already exists!',
            'duration_years.required' => 'Please enter duration!',
            'course_type.required'    => 'Please select course type!',
            'course_type.in'          => 'Selected course type is invalid!',
            'strength.required'       => 'Please enter course strength!',
            'strength.integer'        => 'Course strength must be a valid number!',
            'strength.min'            => 'Course strength cannot be negative!',
            'lateral_seat_percentage.required_if' => 'Please enter lateral seat percentage!',
            'lateral_seat_percentage.numeric'  => 'Lateral seat percentage must be a valid number!',
            'lateral_seat_percentage.min'      => 'Lateral seat percentage cannot be negative!',
            'lateral_seat_percentage.max'      => 'Lateral seat percentage cannot be more than 100!',
            'streams.array'           => 'Invalid stream selection!',
            'streams.*.exists'        => 'Selected stream is invalid!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::transaction(function () use ($request, $tenantId) {

                // ── Resolve course record ──────────────────────────────────
                if (!empty($request->id)) {
                    $course = Course::where('id', $request->id)
                        ->where('tenant_id', $tenantId)
                        ->firstOrFail();
                } else {
                    $course            = new Course();
                    $course->tenant_id = $tenantId;
                }

                $course->name           = trim($request->name);
                $course->code           = trim($request->code);
                $course->duration_years = $request->duration_years;
                $course->course_type    = $request->course_type;
                $course->strength       = $request->strength;
                $course->is_lateral_entry = $request->boolean('is_lateral_entry');
                $course->lateral_seat_percentage = $course->is_lateral_entry
                    ? $request->lateral_seat_percentage
                    : 0;
                $course->save();

                // ── Sync course_stream pivot ───────────────────────────────
                // Build the sync array: [ stream_id => ['tenant_id' => ...] ]
                $syncData = [];
                foreach ($request->streams ?? [] as $streamId) {
                    $syncData[$streamId] = ['tenant_id' => $tenantId];
                }

                // sync() removes detached pivots and adds new ones.
                // cascade delete in the schema will clean up class_stream_mappings.
                $course->streams()->sync($syncData);
            });

            if (!empty($request->id)) {
                return redirect()->back()->with('success', 'Course updated successfully');
            }

            return redirect()->route($this->courseListRoute)->with('success', 'Course created successfully');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong, please try again later!');
        }
    }

    // =========================================================================
    // Delete
    // =========================================================================

    public function destroy(Course $course)
    {
        $this->authorizeTenant($course);

        try {
            $course->delete();
            return back()->with('success', 'Course deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete course, it may be in use.');
        }
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    protected function authorizeTenant(Course $course): void
    {
        if ($course->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }
    }
}
