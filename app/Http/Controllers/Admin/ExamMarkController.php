<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamMark;
use App\Models\Exam;
use App\Models\Stream;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\GradingSystem;
use App\Models\StudentClassEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class ExamMarkController extends Controller
{
    // ══════════════════════════════════════
    // INDEX
    // ══════════════════════════════════════
    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $academicYears = AcademicYear::where('tenant_id', $tenantId)
            ->orderBy('start_year', 'desc')
            ->get();

        $streams = Stream::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();

        $exams = Exam::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();

        $courses = Course::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();

        $allMarks = ExamMark::where('tenant_id', $tenantId)
            ->with(['exam.subject', 'exam.class', 'exam.section', 'student', 'enteredBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        $grades = $allMarks
            ->pluck('grade_letter')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $examMarksJson = $allMarks->map(function ($mark) {
            return [
                'id'               => $mark->id,
                'exam_id'          => $mark->exam_id,
                'exam_name'        => $mark->exam->name ?? '—',
                'subject_name'     => $mark->exam->subject->name ?? '—',
                'student_name'     => trim(($mark->student->first_name ?? '') . ' ' . ($mark->student->last_name ?? '')),
                'marks_obtained'   => $mark->marks_obtained,
                'grade_letter'     => $mark->grade_letter ?? 'N/A',
                'entered_by'       => $mark->enteredBy->name ?? '—',
                'stream_id'        => $mark->exam->stream_id ?? null,
                'academic_year_id' => $mark->exam->academic_year_id ?? null,
                'edit_url'         => route('admin.exam_marks.edit', $mark->id),
                'delete_url'       => route('admin.exam_marks.destroy', $mark->id),
            ];
        });

        return view('Academics.exam_marks.index', compact(
            'examMarksJson',
            'academicYears',
            'streams',
            'exams',
            'grades',
            'courses'
        ));
    }

    // ══════════════════════════════════════
    // CREATE
    // ══════════════════════════════════════
    public function create()
    {
        $tenantId = auth()->user()->tenant_id;

        $courses = Course::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();

        $academicYears = AcademicYear::where('tenant_id', $tenantId)
            ->orderBy('start_year', 'desc')
            ->get();

        $streams = Stream::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();

        $exams = Exam::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();

        return view('Academics.exam_marks.form', compact(
            'courses',
            'academicYears',
            'streams',
            'exams'
        ));
    }

    // ══════════════════════════════════════
    // EDIT
    // ══════════════════════════════════════
    public function edit(ExamMark $examMark)
    {
        $this->authorizeTenant($examMark);

        $tenantId = auth()->user()->tenant_id;

        $courses = Course::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();

        $academicYears = AcademicYear::where('tenant_id', $tenantId)
            ->orderBy('start_year', 'desc')
            ->get();

        $streams = Stream::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();

        $exams = Exam::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();

        return view('Academics.exam_marks.form', compact(
            'examMark',
            'courses',
            'academicYears',
            'streams',
            'exams'
        ));
    }

    // ══════════════════════════════════════
    // STORE
    // ══════════════════════════════════════
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exam_id'                => 'required|exists:exams,id',
            'marks'                  => 'required|array',
            'marks.*.student_id'     => 'required|exists:students,id',
            'marks.*.marks_obtained' => 'required|numeric|min:0',
            'marks.*.grade_letter'   => 'nullable|string|max:5',
            'marks.*.remarks'        => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid data.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $tenantId = auth()->user()->tenant_id;

        $exam = Exam::where('tenant_id', $tenantId)
            ->where('id', $request->exam_id)
            ->with(['gradeLevels'])
            ->firstOrFail();

        foreach ($request->marks as $mark) {
            $gradeLevel = null;
            if (!empty($mark['marks_obtained']) && $exam->gradeLevels->isNotEmpty()) {
                $gradeLevel = $exam->gradeLevels
                    ->where('min_score', '<=', (float) $mark['marks_obtained'])
                    ->where('max_score', '>=', (float) $mark['marks_obtained'])
                    ->first();
            }

            ExamMark::updateOrCreate(
                [
                    'tenant_id'  => $tenantId,
                    'exam_id'    => $exam->id,
                    'student_id' => $mark['student_id'],
                ],
                [
                    'marks_obtained'     => $mark['marks_obtained'],
                    'grade_letter'       => $gradeLevel?->grade_letter ?? $mark['grade_letter'] ?? null,
                    'remarks'            => $gradeLevel?->remarks      ?? $mark['remarks']      ?? null,
                    'mark_date'          => $mark['mark_date'] ?? null,
                    'entered_by_user_id' => auth()->id(),
                ]
            );
        }

        return response()->json(['message' => 'Marks saved successfully!']);
    }

    // ══════════════════════════════════════
    // UPDATE
    // ══════════════════════════════════════
    public function update(Request $request, ExamMark $examMark)
    {
        $this->authorizeTenant($examMark);

        $validator = Validator::make($request->all(), [
            'exam_id'                => 'required|exists:exams,id',
            'marks'                  => 'required|array',
            'marks.*.student_id'     => 'required|exists:students,id',
            'marks.*.marks_obtained' => 'required|numeric|min:0',
            'marks.*.grade_letter'   => 'nullable|string|max:5',
            'marks.*.remarks'        => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid data.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $tenantId = auth()->user()->tenant_id;

        $exam = Exam::where('tenant_id', $tenantId)
            ->where('id', $request->exam_id)
            ->with(['gradeLevels'])
            ->firstOrFail();

        foreach ($request->marks as $mark) {
            $gradeLevel = null;
            if (!empty($mark['marks_obtained']) && $exam->gradeLevels->isNotEmpty()) {
                $gradeLevel = $exam->gradeLevels
                    ->where('min_score', '<=', (float) $mark['marks_obtained'])
                    ->where('max_score', '>=', (float) $mark['marks_obtained'])
                    ->first();
            }

            ExamMark::updateOrCreate(
                [
                    'tenant_id'  => $tenantId,
                    'exam_id'    => $exam->id,
                    'student_id' => $mark['student_id'],
                ],
                [
                    'marks_obtained'     => $mark['marks_obtained'],
                    'grade_letter'       => $gradeLevel?->grade_letter ?? $mark['grade_letter'] ?? null,
                    'remarks'            => $gradeLevel?->remarks      ?? $mark['remarks']      ?? null,
                    'entered_by_user_id' => auth()->id(),
                ]
            );
        }

        return response()->json(['message' => 'Marks updated successfully!']);
    }

    // ══════════════════════════════════════
    // DESTROY
    // ══════════════════════════════════════
    public function destroy(ExamMark $examMark)
    {
        $this->authorizeTenant($examMark);

        try {
            $examMark->delete();
            return back()->with('success', 'Exam mark deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete exam mark.');
        }
    }

    // ══════════════════════════════════════
    // AJAX — Exams by filters
    // ══════════════════════════════════════
    public function getExamsByFilters(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'course_id'        => 'required|exists:courses,id',
            'stream_id'        => 'required|exists:streams,id',
            'class_id'         => 'required|exists:classes,id',
            'section_id'       => 'required|exists:sections,id',
        ]);

        $tenantId = auth()->user()->tenant_id;

        $exams = Exam::where('tenant_id', $tenantId)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('stream_id', $request->stream_id)
            ->where('class_id', $request->class_id)
            ->where(function ($q) use ($request) {
                $q->where('section_id', $request->section_id)
                  ->orWhereNull('section_id');
            })
            ->with(['subject', 'examType'])
            ->orderBy('exam_date')
            ->get()
            ->map(fn($e) => [
                'id'          => $e->id,
                'name'        => $e->name,
                'subject'     => $e->subject->name ?? '—',
                'exam_type'   => $e->examType->name ?? '—',
                'exam_date'   => $e->exam_date,
                'total_marks' => $e->total_marks,
                'pass_marks'  => $e->pass_marks,
            ]);

        return response()->json(['exams' => $exams]);
    }

    // ══════════════════════════════════════
    // AJAX — Students by exam
    // ══════════════════════════════════════
    public function getStudentsByExam(Request $request)
    {
        $request->validate(['exam_id' => 'required|exists:exams,id']);

        $tenantId = auth()->user()->tenant_id;

        $exam = Exam::where('tenant_id', $tenantId)
            ->where('id', $request->exam_id)
            ->with(['subject', 'class', 'section', 'stream', 'gradeLevels'])
            ->firstOrFail();

        // Resolve course_id from stream via pivot table
        $courseId = null;
        if ($exam->stream_id) {
            $courseId = \DB::table('course_stream')
                ->where('stream_id', $exam->stream_id)
                ->value('course_id');
        }

        // ── Primary query: active enrollments, strict filters ──────────────
        $studentQuery = StudentClassEnrollment::where('student_class_enrollments.tenant_id', $tenantId)
            ->where('student_class_enrollments.class_id', $exam->class_id)
            ->where('student_class_enrollments.status', 'active')
            ->whereHas('student', fn($q) => $q->where('status', 'active'));

        if ($exam->stream_id) {
            $studentQuery->where('student_class_enrollments.stream_id', $exam->stream_id);
        }

        if ($courseId) {
            $studentQuery->where('student_class_enrollments.course_id', $courseId);
        }

        if ($exam->section_id) {
            $studentQuery->where('student_class_enrollments.section_id', $exam->section_id);
        }

        $students = $studentQuery
            ->with('student')
            ->get()
            ->sortBy(fn($e) => str_pad((string)($e->student->roll_number ?? ''), 20, '0', STR_PAD_LEFT))
            ->values()
            ->map(fn($e) => [
                'enrollment_id'    => $e->id,
                'student_id'       => $e->student->id,
                'admission_number' => $e->student->admission_number ?? '—',
                'roll_number'      => $e->student->roll_number      ?? '—',
                'first_name'       => $e->student->first_name,
                'last_name'        => $e->student->last_name,
            ])
            ->unique('student_id')   // ✅ FIX: duplicate enrollment rows hata do
            ->values();

        // ── Fallback: drop course filter, keep stream + section ──
        if ($students->isEmpty()) {
            $fallbackQuery = StudentClassEnrollment::where('student_class_enrollments.tenant_id', $tenantId)
                ->where('student_class_enrollments.class_id', $exam->class_id)
                ->where('student_class_enrollments.status', 'active')
                ->whereHas('student', fn($q) => $q->where('status', 'active'));

            if ($exam->stream_id) {
                $fallbackQuery->where('student_class_enrollments.stream_id', $exam->stream_id);
            }

            if ($exam->section_id) {
                $fallbackQuery->where('student_class_enrollments.section_id', $exam->section_id);
            }

            $students = $fallbackQuery
                ->with('student')
                ->get()
                ->sortBy(fn($e) => str_pad((string)($e->student->roll_number ?? ''), 20, '0', STR_PAD_LEFT))
                ->values()
                ->map(fn($e) => [
                    'enrollment_id'    => $e->id,
                    'student_id'       => $e->student->id,
                    'admission_number' => $e->student->admission_number ?? '—',
                    'roll_number'      => $e->student->roll_number      ?? '—',
                    'first_name'       => $e->student->first_name,
                    'last_name'        => $e->student->last_name,
                ])
                ->unique('student_id')   // ✅ FIX: fallback mein bhi deduplicate
                ->values();
        }

        $existing = ExamMark::where('tenant_id', $tenantId)
            ->where('exam_id', $exam->id)
            ->get(['student_id', 'marks_obtained', 'grade_letter', 'remarks'])
            ->mapWithKeys(fn($m) => [
                $m->student_id => [
                    'marks_obtained' => $m->marks_obtained,
                    'grade_letter'   => $m->grade_letter,
                    'remarks'        => $m->remarks,
                ]
            ]);

        $grades = $exam->gradeLevels->map(fn($g) => [
            'letter'    => $g->grade_letter,
            'min_marks' => $g->min_score,
            'max_marks' => $g->max_score,
        ]);

        return response()->json([
            'exam' => [
                'id'          => $exam->id,
                'total_marks' => $exam->total_marks,
                'pass_marks'  => $exam->pass_marks,
                'subject'     => $exam->subject->name  ?? '—',
                'class'       => $exam->class->name    ?? '—',
                'section'     => $exam->section->name  ?? '—',
                'stream'      => $exam->stream->name   ?? '—',
            ],
            'students' => $students,
            'existing' => $existing,
            'grades'   => $grades,
        ]);
    }

    // ══════════════════════════════════════
    // IMPORT
    // ══════════════════════════════════════
    public function import()
    {
        return view('Academics.exam_marks.import');
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        return redirect()->route('admin.exam_marks.index')
            ->with('success', 'Marks imported successfully.');
    }

    // ══════════════════════════════════════
    // EXPORT PDF
    // ══════════════════════════════════════
    public function exportPdf(Request $request)
    {
        $marks = $this->buildQuery($request)->get();
        $pdf   = Pdf::loadView('Academics.exam_marks.pdf_export', compact('marks', 'request'));
        return $pdf->download('exam-marks.pdf');
    }

    // ══════════════════════════════════════
    // PRIVATE HELPERS
    // ══════════════════════════════════════
    private function buildQuery(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $query = ExamMark::where('tenant_id', $tenantId)
            ->with(['exam.subject', 'student', 'enteredBy']);

        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        if ($request->filled('grade_letter')) {
            $query->where('grade_letter', $request->grade_letter);
        }

        return $query->orderBy('created_at', 'desc');
    }

    protected function authorizeTenant(ExamMark $examMark)
    {
        if ($examMark->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }
    }
}