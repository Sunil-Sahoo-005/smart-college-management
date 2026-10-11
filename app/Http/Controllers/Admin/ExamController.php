<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\ExamType;
use App\Models\ExamCenter;
use App\Models\GradingSystem;
use App\Models\GradeLevel;
use App\Models\Classe;
use App\Models\Subject;
use App\Models\AcademicYear;
use App\Models\Stream;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    protected $examListRoute = 'admin.exams.index';

    // ══════════════════════════════════════
    // INDEX
    // ══════════════════════════════════════
    public function index(Request $request)
    {
        $exams = Exam::where('tenant_id', Auth::user()->tenant_id)
            ->with(['examType', 'class', 'subject', 'academicYear', 'stream', 'course'])
            ->latest()
            ->get();

        $examTypes = ExamType::where('tenant_id', Auth::user()->tenant_id)->orderBy('name')->get();
        $classes   = Classe::where('tenant_id', Auth::user()->tenant_id)->orderBy('name')->get();
        $subjects  = Subject::where('tenant_id', Auth::user()->tenant_id)->orderBy('name')->get();
        $streams   = Stream::where('tenant_id', Auth::user()->tenant_id)->orderBy('name')->get();
        $courses   = Course::where('tenant_id', Auth::user()->tenant_id)->orderBy('name')->get();

        $examsJson = $exams->map(function ($e) {
            return [
                'id'             => $e->id,
                'exam_code'      => $e->exam_code,
                'name'           => $e->name,
                'stream_name'    => $e->stream?->name ?? '—',
                'stream_id'      => $e->stream_id,
                'course_name'    => $e->course?->name ?? '—',
                'course_id'      => $e->course_id,
                'exam_type_name' => $e->examType?->name ?? 'N/A',
                'exam_type_id'   => $e->exam_type_id,
                'class_name'     => $e->class?->name ?? 'N/A',
                'class_id'       => $e->class_id,
                'subject_name'   => $e->subject?->name ?? 'N/A',
                'subject_id'     => $e->subject_id,
                'exam_date'      => $e->exam_date,
                'start_time'     => $e->start_time,
                'end_time'       => $e->end_time,
                'total_marks'    => $e->total_marks,
                'pass_marks'     => $e->pass_marks,
                'show_url'       => route('admin.exams.show',    $e),
                'edit_url'       => route('admin.exams.edit',    $e),
                'delete_url'     => route('admin.exams.destroy', $e),
                'marks_url'      => route('admin.exams.marks',   $e->id),
            ];
        });

        $courses = Course::where('tenant_id', Auth::user()->tenant_id)
            ->with(['streams' => function ($q) {
                $q->where('streams.tenant_id', Auth::user()->tenant_id)
                  ->orderBy('streams.name');
            }])
            ->orderBy('name')
            ->get();

        $coursesJson = $courses->map(fn($c) => [
            'id'      => $c->id,
            'name'    => $c->name,
            'code'    => $c->code,
            'streams' => $c->streams->map(fn($s) => ['id' => $s->id, 'name' => $s->name])->values(),
        ]);

        return view('Academics.exam.index', compact(
            'exams', 'examsJson', 'examTypes', 'classes', 'subjects', 'streams', 'coursesJson', 'courses'
        ));
    }

    // ══════════════════════════════════════
    // CREATE
    // ══════════════════════════════════════
    public function create()
    {
        $exam           = new Exam;
        $examTypes      = ExamType::where('tenant_id', Auth::user()->tenant_id)->get();
        $classes        = Classe::where('tenant_id', Auth::user()->tenant_id)->get();
        $subjects       = Subject::where('tenant_id', Auth::user()->tenant_id)->get();
        $academicYears  = AcademicYear::where('tenant_id', Auth::user()->tenant_id)->get();
        $streams        = Stream::where('tenant_id', Auth::user()->tenant_id)->orderBy('name')->get();
        $courses        = Course::where('tenant_id', Auth::user()->tenant_id)
                            ->with(['streams' => fn($q) => $q->orderBy('name')])
                            ->orderBy('name')
                            ->get();
        $gradingSystems = GradingSystem::where('tenant_id', Auth::user()->tenant_id)
                            ->orderBy('name')
                            ->get();
        $gradeLevels    = GradeLevel::with('gradingSystem')
                            ->orderBy('grading_system_id')
                            ->orderBy('min_score', 'desc')
                            ->get();

        // ✅ Active exam centers for online exam assignment
        $examCenters = ExamCenter::where('tenant_id', Auth::user()->tenant_id)
                            ->active()
                            ->orderBy('name')
                            ->get();

        $questionBanks = \App\Models\QuestionBank::where('tenant_id', Auth::user()->tenant_id)
            ->with(['studentClass', 'subject'])
            ->where('status', 1)
            ->get();

        $questionBanksForJs = $questionBanks->map(fn($qb) => [
            'id'           => $qb->id,
            'name'         => $qb->name,
            'course_id'    => $qb->course_id,
            'stream_id'    => $qb->stream_id,
            'class_id'     => $qb->class_id,
            'subject_id'   => $qb->subject_id,
            'class_name'   => $qb->studentClass?->name ?? '—',
            'subject_name' => $qb->subject?->name ?? '—',
            'description'  => $qb->description,
            'status'       => $qb->status,
        ]);

        $existingQuestionBankId = null;
        $existingQuestionIds    = [];

        return view('Academics.exam.form', compact(
            'exam', 'examTypes', 'classes', 'subjects', 'academicYears',
            'streams', 'courses', 'gradingSystems', 'gradeLevels',
            'questionBanksForJs', 'existingQuestionBankId', 'existingQuestionIds',
            'examCenters'
        ));
    }

    // ══════════════════════════════════════
    // EDIT
    // ══════════════════════════════════════
    public function edit($id)
    {
        $exam = Exam::where('tenant_id', Auth::user()->tenant_id)
            ->with(['examType', 'class', 'subject', 'academicYear', 'stream'])
            ->find($id);

        if (empty($exam)) abort(404);

        $examTypes      = ExamType::where('tenant_id', Auth::user()->tenant_id)->get();
        $classes        = Classe::where('tenant_id', Auth::user()->tenant_id)->get();
        $subjects       = Subject::where('tenant_id', Auth::user()->tenant_id)->get();
        $academicYears  = AcademicYear::where('tenant_id', Auth::user()->tenant_id)->get();
        $streams        = Stream::where('tenant_id', Auth::user()->tenant_id)->orderBy('name')->get();
        $courses        = Course::where('tenant_id', Auth::user()->tenant_id)
                            ->with(['streams' => fn($q) => $q->orderBy('name')])
                            ->orderBy('name')
                            ->get();
        $gradingSystems = GradingSystem::where('tenant_id', Auth::user()->tenant_id)
                            ->orderBy('name')
                            ->get();
        $gradeLevels    = GradeLevel::with('gradingSystem')
                            ->orderBy('grading_system_id')
                            ->orderBy('min_score', 'desc')
                            ->get();

        // ✅ Active exam centers for online exam assignment
        $examCenters = ExamCenter::where('tenant_id', Auth::user()->tenant_id)
                            ->active()
                            ->orderBy('name')
                            ->get();

        $questionBanks = \App\Models\QuestionBank::where('tenant_id', Auth::user()->tenant_id)
            ->with(['studentClass', 'subject'])
            ->where('status', 1)
            ->get();

        $questionBanksForJs = $questionBanks->map(fn($qb) => [
            'id'           => $qb->id,
            'name'         => $qb->name,
            'course_id'    => $qb->course_id,
            'stream_id'    => $qb->stream_id,
            'class_id'     => $qb->class_id,
            'subject_id'   => $qb->subject_id,
            'class_name'   => $qb->studentClass?->name ?? '—',
            'subject_name' => $qb->subject?->name ?? '—',
            'description'  => $qb->description,
            'status'       => $qb->status,
        ]);

        $existingQuestionBankId = $exam->question_bank_id ?? null;

        $existingQuestionIds = [];
        if ($existingQuestionBankId) {
            $existingQuestionIds = DB::table('exam_questions')
                ->where('exam_id', $exam->id)
                ->pluck('question_id')
                ->toArray();
        }

        return view('Academics.exam.form', compact(
            'exam', 'examTypes', 'classes', 'subjects', 'academicYears',
            'streams', 'courses', 'gradingSystems', 'gradeLevels',
            'questionBanksForJs', 'existingQuestionBankId', 'existingQuestionIds',
            'examCenters'
        ));
    }

    // ══════════════════════════════════════
    // STORE / UPDATE
    // ══════════════════════════════════════
    public function store(Request $request)
    {
        $rules = [
            'exam_type_id'      => 'required|exists:exam_types,id',
            'exam_code'         => 'nullable|string|max:50|unique:exams,exam_code,' . $request->get('id'),
            'name'              => 'required|string|max:191',
            'class_id'          => 'required|exists:classes,id',
            'section_id'        => 'nullable|exists:sections,id',
            'subject_id'        => 'required|exists:subjects,id',
            'academic_year_id'  => 'required|exists:academic_years,id',
            'exam_date'         => 'required|date',
            'start_time'        => 'required|date_format:H:i',
            'end_time'          => 'required|date_format:H:i|after:start_time',
            'total_marks'       => 'required|integer|min:1',
            'pass_marks'        => 'required|integer|min:0',
            'stream_id'         => 'nullable|exists:streams,id',
            'course_id'         => 'nullable|exists:courses,id',
            'grading_system_id' => 'nullable|exists:grading_systems,id',
            'exam_mode'         => 'required|in:online,offline',
            'question_bank_id'  => 'required_if:exam_mode,online|nullable|exists:question_banks,id',
            // ✅ center_id is required only for online exams
            'center_id'         => 'required_if:exam_mode,online|nullable|exists:exam_centers,id',
        ];

        $messages = [
            'exam_type_id.required'         => 'Please select exam type!',
            'name.required'                 => 'Please enter exam name!',
            'class_id.required'             => 'Please select class!',
            'subject_id.required'           => 'Please select subject!',
            'academic_year_id.required'     => 'Please select academic year!',
            'exam_date.required'            => 'Please select exam date!',
            'start_time.required'           => 'Please enter start time!',
            'end_time.required'             => 'Please enter end time!',
            'end_time.after'                => 'End time must be after start time!',
            'total_marks.required'          => 'Please enter total marks!',
            'pass_marks.required'           => 'Please enter pass marks!',
            'exam_mode.required'            => 'Please select exam mode!',
            'course_id.exists'              => 'Selected course is invalid!',
            'question_bank_id.required_if'  => 'Please select question bank for online exam!',
            'center_id.required_if'         => 'Please select an exam center for online exam!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if (!empty($request->get('id'))) {
            $exam = Exam::where('tenant_id', Auth::user()->tenant_id)->find($request->get('id'));
            if (empty($exam)) abort(404);
            $redirect = ['url' => redirect()->back(), 'msg' => 'Exam updated successfully'];
        } else {
            $exam     = new Exam;
            $redirect = ['url' => redirect()->route($this->examListRoute), 'msg' => 'Exam created successfully'];
        }

        // ✅ AUTO GRADING SYSTEM ASSIGN
        $gradingSystemId = $request->grading_system_id;

        if (empty($gradingSystemId)) {
            $examTypeName = ExamType::find($request->exam_type_id)?->name ?? '';
            $nameLower    = strtolower($examTypeName);

            if (str_contains($nameLower, 'cgpa')) {
                $keyword = 'cgpa';
            } elseif (str_contains($nameLower, 'sgpa')) {
                $keyword = 'sgpa';
            } else {
                $keyword = 'percentage';
            }

            $gradingSystemId = GradingSystem::where('tenant_id', Auth::user()->tenant_id)
                ->whereRaw('LOWER(name) LIKE ?', ["%{$keyword}%"])
                ->value('id');
        }

        $examCode = $request->exam_code;
        if (empty($examCode)) {
            $prefix = 'EXAM-';
            $year   = date('Y');
            $count  = Exam::where('tenant_id', Auth::user()->tenant_id)->count() + 1;
            $examCode = $prefix . $year . str_pad($count, 4, '0', STR_PAD_LEFT);
            
            // Ensure uniqueness
            while (Exam::where('exam_code', $examCode)->exists()) {
                $count++;
                $examCode = $prefix . $year . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        }

        $exam->tenant_id         = Auth::user()->tenant_id;
        $exam->exam_code         = $examCode;
        $exam->exam_type_id      = $request->exam_type_id;
        $exam->name              = trim($request->name);
        $exam->class_id          = $request->class_id;
        $exam->section_id        = $request->section_id;
        $exam->subject_id        = $request->subject_id;
        $exam->academic_year_id  = $request->academic_year_id;
        $exam->exam_date         = $request->exam_date;
        $exam->start_time        = $request->start_time;
        $exam->end_time          = $request->end_time;
        $exam->total_marks       = $request->total_marks;
        $exam->pass_marks        = $request->pass_marks;
        $exam->stream_id         = $request->stream_id;
        $exam->course_id         = $request->course_id ?: null;
        $exam->grading_system_id = $gradingSystemId;
        $exam->exam_mode         = $request->exam_mode;
        $exam->question_bank_id  = $request->question_bank_id ?: null;

        // ✅ Save center_id only for online exams, clear for offline
        $exam->center_id = ($request->exam_mode === 'online')
            ? ($request->center_id ?: null)
            : null;

        try {
            $exam->save();

            // ✅ Save selected questions for online exams
            if ($request->exam_mode === 'online' && $request->filled('selected_question_ids')) {
                DB::table('exam_questions')->where('exam_id', $exam->id)->delete();

                $inserts = collect($request->selected_question_ids)->map(fn($qid) => [
                    'exam_id'     => $exam->id,
                    'question_id' => $qid,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ])->toArray();

                if (!empty($inserts)) {
                    DB::table('exam_questions')->insert($inserts);
                }
            }

            return $redirect['url']->with('success', $redirect['msg']);
        } catch (\Exception $e) {
            \Log::error('Exam save error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Something went wrong: ' . $e->getMessage())
                ->withInput();
        }
    }

    // ══════════════════════════════════════
    // SHOW
    // ══════════════════════════════════════
    public function show($id)
    {
        $exam = Exam::where('tenant_id', Auth::user()->tenant_id)
            ->with(['examType', 'class', 'subject', 'academicYear', 'stream'])
            ->find($id);

        if (empty($exam)) abort(404);

        return view('Academics.exam.show', compact('exam'));
    }

    // ══════════════════════════════════════
    // DESTROY
    // ══════════════════════════════════════
    public function destroy($id)
    {
        $exam = Exam::where('tenant_id', Auth::user()->tenant_id)->find($id);
        if (empty($exam)) abort(404);
        $exam->delete();
        return redirect()->route('admin.exams.index')->with('success', 'Exam deleted successfully.');
    }

    // ══════════════════════════════════════
    // MARKS PAGE
    // ══════════════════════════════════════
    public function marks($id)
    {
        $exam = Exam::where('tenant_id', Auth::user()->tenant_id)
            ->with(['examType', 'class', 'section', 'subject', 'stream'])
            ->find($id);

        if (empty($exam)) abort(404);

        $studentQuery = Student::where('students.tenant_id', Auth::user()->tenant_id)
            ->join('student_class_enrollments as sce', 'students.id', '=', 'sce.student_id')
            ->where('sce.tenant_id', Auth::user()->tenant_id)
            ->where('sce.class_id', $exam->class_id);

        if ($exam->section_id) {
            $studentQuery->where('sce.section_id', $exam->section_id);
        }

        if ($exam->stream_id) {
            $studentQuery->where('sce.stream_id', $exam->stream_id);
        }

        $students = (clone $studentQuery)
            ->where('sce.status', 'active')
            ->select('students.*')
            ->distinct()
            ->orderBy('students.roll_number')
            ->get();

        if ($students->isEmpty()) {
            $students = $studentQuery
                ->select('students.*')
                ->distinct()
                ->orderBy('students.roll_number')
                ->get();
        }

        $markedStudentIds = ExamMark::where('exam_id', $exam->id)
            ->where('tenant_id', Auth::user()->tenant_id)
            ->pluck('student_id')
            ->toArray();

        if ($students->isEmpty() && !empty($markedStudentIds)) {
            $students = Student::where('tenant_id', Auth::user()->tenant_id)
                ->whereIn('id', $markedStudentIds)
                ->orderBy('roll_number')
                ->get();
        }

        if (!empty($markedStudentIds)) {
            $existingIds = $students->pluck('id')->toArray();
            $missingIds  = array_diff($markedStudentIds, $existingIds);

            if (!empty($missingIds)) {
                $extraStudents = Student::where('tenant_id', Auth::user()->tenant_id)
                    ->whereIn('id', $missingIds)
                    ->orderBy('roll_number')
                    ->get();
                $students = $students->merge($extraStudents)->sortBy('roll_number')->values();
            }
        }

        $existingMarks = ExamMark::where('exam_id', $exam->id)
            ->where('tenant_id', Auth::user()->tenant_id)
            ->get()
            ->keyBy('student_id');

        return view('Academics.exam.marks', compact('exam', 'students', 'existingMarks'));
    }

    // ══════════════════════════════════════
    // SAVE MARKS (AJAX)
    // ══════════════════════════════════════
    public function saveMarks(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exam_id'        => 'required|exists:exams,id',
            'student_id'     => 'required|exists:students,id',
            'marks_obtained' => 'nullable|numeric|min:0',
            'grade_letter'   => 'nullable|string|max:5',
            'remarks'        => 'nullable|string|max:191',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $exam = Exam::where('tenant_id', Auth::user()->tenant_id)
                ->where('id', $request->exam_id)
                ->with(['gradeLevels'])
                ->firstOrFail();

            $gradeLevel = null;

            if ($request->filled('marks_obtained') && $exam->gradeLevels->isNotEmpty()) {
                $gradeLevel = $exam->gradeLevels
                    ->where('min_score', '<=', (float) $request->marks_obtained)
                    ->where('max_score', '>=', (float) $request->marks_obtained)
                    ->first();
            }

            ExamMark::updateOrCreate(
                [
                    'tenant_id'  => Auth::user()->tenant_id,
                    'exam_id'    => $request->exam_id,
                    'student_id' => $request->student_id,
                ],
                [
                    'marks_obtained'     => $request->marks_obtained,
                    'grade_letter'       => $gradeLevel?->grade_letter ?? $request->grade_letter ?? null,
                    'remarks'            => $gradeLevel?->remarks      ?? $request->remarks      ?? null,
                    'entered_by_user_id' => Auth::id(),
                ]
            );

            return response()->json([
                'success'      => true,
                'message'      => 'Saved successfully',
                'grade_letter' => $gradeLevel?->grade_letter ?? $request->grade_letter ?? '—',
                'remarks'      => $gradeLevel?->remarks      ?? $request->remarks      ?? '',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ══════════════════════════════════════
    // PRIVATE HELPER — Auto Grading System
    // ══════════════════════════════════════
    private function autoDetectGradingSystem(int $examTypeId, int $tenantId): ?int
    {
        $examTypeName = ExamType::find($examTypeId)?->name ?? '';
        $nameLower    = strtolower($examTypeName);

        if (str_contains($nameLower, 'cgpa')) {
            $keyword = 'cgpa';
        } elseif (str_contains($nameLower, 'sgpa')) {
            $keyword = 'sgpa';
        } else {
            $keyword = 'percentage';
        }

        return GradingSystem::where('tenant_id', $tenantId)
            ->whereRaw('LOWER(name) LIKE ?', ["%{$keyword}%"])
            ->value('id');
    }
}