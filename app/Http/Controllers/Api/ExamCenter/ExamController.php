<?php

namespace App\Http\Controllers\Api\ExamCenter;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ExamController extends Controller
{
    /**
     * Get Exam Details and Start Time for Countdown.
     */
    public function details(Request $request, $exam_id)
    {
        $student = Auth::user();

        if (!$student) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated student.',
            ], 401);
        }

        $exam = Exam::with([
            'examType',
            'subject',
            'class'
        ])->findOrFail($exam_id);

        $attempt = ExamAttempt::where([
            'exam_id'    => $exam->id,
            'student_id' => $student->id,
        ])->first();

        if (!$attempt) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No attempt found for this exam',
            ], 404);
        }

        // Combine date and time
        $startDateTime = Carbon::parse($exam->exam_date->format('Y-m-d') . ' ' . $exam->start_time);
        $endDateTime   = Carbon::parse($exam->exam_date->format('Y-m-d') . ' ' . $exam->end_time);

        return response()->json([
            'status'  => 'success',
            'data'    => [
                'exam' => [
                    'id'          => $exam->id,
                    'name'        => $exam->name,
                    'type'        => $exam->examType?->name,
                    'subject'     => $exam->subject?->name,
                    'class'       => $exam->class?->name,
                    'start_time'  => $startDateTime->toIso8601String(),
                    'end_time'    => $endDateTime->toIso8601String(),
                    'duration'    => $startDateTime->diffInMinutes($endDateTime),
                    'total_marks' => $exam->total_marks,
                ],
                'attempt' => [
                    'id'         => $attempt->id,
                    'status'     => $attempt->status,
                    'started_at' => $attempt->started_at ? $attempt->started_at->toIso8601String() : null,
                ],
                'server_time' => Carbon::now()->toIso8601String(),
            ],
        ]);
    }

    /**
     * Start the Exam.
     */

    public function startExam(Request $request, $exam_id)
    {
        $student = Auth::user();

        if (!$student) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated student.',
            ], 401);
        }

        // Fetch attempt and exam
        $attempt = ExamAttempt::where('exam_id', $exam_id)
            ->where('student_id', $student->id)
            ->firstOrFail();

        // If attempt already marked as expired, prevent further actions
        if ($attempt->status === 'expired') {
            return response()->json([
                'status' => 'error',
                'message' => 'Exam has expired.',
            ], 403);
        }

        $exam = Exam::findOrFail($exam_id);
        $startDateTime = Carbon::parse($exam->exam_date->format('Y-m-d') . ' ' . $exam->start_time);
        $endDateTime = Carbon::parse($exam->exam_date->format('Y-m-d') . ' ' . $exam->end_time);

        // Early start check (allow 5 minutes before official start)
        if (Carbon::now()->lt($startDateTime->subMinutes(0))) {
            return response()->json([
                'status' => 'error',
                'message' => 'It is too early to start the exam',
            ], 400);
        }

        // Expiration check
        if (Carbon::now()->gt($endDateTime)) {
            // If exam time has passed, ensure attempt status reflects expiration
            if ($attempt->status !== 'submitted') {
                $attempt->update(['status' => 'expired']);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'Exam time has expired.',
            ], 403);
        }

        // If already submitted
        if ($attempt->status === 'submitted') {
            return response()->json([
                'status' => 'error',
                'message' => 'Exam already submitted.',
            ], 403);
        }

        // If already in progress, allow resume
        if ($attempt->status === 'in_progress') {
            return response()->json([
                'status' => 'success',
                'message' => 'Resume exam.',
                'data' => [
                    'attempt_id' => $attempt->id,
                    'started_at' => optional($attempt->started_at)->toIso8601String(),
                    'resume' => true,
                ],
            ]);
        }

        // Only start if not yet started
        if ($attempt->status !== 'not_started') {
            return response()->json([
                'status' => 'error',
                'message' => 'Exam already started or submitted',
                'attempt_status' => $attempt->status,
            ], 400);
        }

        // Start the exam
        $attempt->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Exam started successfully',
            'data' => [
                'started_at' => $attempt->started_at->toIso8601String(),
                'end_time' => $endDateTime->toIso8601String(),
                'remaining_seconds' => now()->diffInSeconds($endDateTime, false),
            ],
        ]);
    }

    /**
     * Get Exam Questions.
     */
    /**
     * Get Exam Questions.
     */
    public function getQuestions(Request $request, $exam_id)
    {
        // Ensure the student is authenticated
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated user.',
            ], 401);
        }
        // Retrieve the student model linked to this user
        $student = \App\Models\Student::where('id', $user->id)->first();
        if (!$student) {
            return response()->json([
                'status' => 'error',
                'message' => 'Student record not found.',
            ], 404);
        }

        // Find the exam attempt for this student
        $attempt = ExamAttempt::where('exam_id', $exam_id)
            ->where('student_id', $student->id)
            ->first();
        if (!$attempt) {
            return response()->json([
                'status' => 'error',
                'message' => 'Exam attempt not found.',
            ], 404);
        }

        // Ensure the exam is currently in progress
        if ($attempt->status !== 'in_progress') {
            return response()->json([
                'status' => 'error',
                'message' => 'Questions can only be accessed while exam is in progress',
            ], 403);
        }

        // Load questions with their options (excluding is_correct)
        $questions = Question::whereHas('exams', function ($query) use ($exam_id) {
                $query->where('exams.id', $exam_id);
            })
            ->with(['options:id,question_id,option_text'])
            ->get()
            ->map(function ($q) {
                return [
                    'id' => $q->id,
                    'type' => $q->type,
                    'question_text' => $q->question,
                    'mark' => $q->mark,
                    'negative_marks' => $q->negative_marks,
                    'options' => $q->options,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => [
                'questions' => $questions,
            ],
        ]);
    }
       
}
