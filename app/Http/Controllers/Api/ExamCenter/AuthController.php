<?php

namespace App\Http\Controllers\Api\ExamCenter;

use App\Http\Controllers\Controller;
use App\Models\ExamCenter;
use App\Models\Exam;
use App\Models\CenterDevice;
use App\Models\Student;
use App\Models\User;
use App\Models\ExamAttempt;
use App\Models\ExamCenterAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Login for Exam Center.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $center = ExamCenter::where('username', $request->username)->first();

        if (!$center || !Hash::check($request->password, $center->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid credentials',
            ], 401);
        }

        if ($center->status !== 'active') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Account is inactive',
            ], 403);
        }

        $token = $center->createToken('exam-center-token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Login successful',
            'data'    => [
                'token'  => $token,
                'center' => [
                    'id'   => $center->id,
                    'code' => $center->code,
                    'name' => $center->name,
                ],
            ],
        ]);
    }

    /**
     * Match Exam Code and Device Code.
     */
    public function matchDevice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exam_code'   => 'required|string',
            'device_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $center = $request->user(); // Currently authenticated ExamCenter
        // dd($center);
        // Find Exam
        $exam = Exam::where('exam_code', $request->exam_code)->first();
        if (!$exam) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid Exam Code',
            ], 404);
        }

        // Verify Exam is assigned to this Center
        // $isAssigned = ExamCenterAssignment::where('exam_id', $exam->id)
        //     ->where('center_id', $center->id)
        //     ->exists();

        // if (!$isAssigned) {
        //     return response()->json([
        //         'status'  => 'error',
        //         'message' => 'This exam is not assigned to this center',
        //     ], 403);
        // }
        // Verify Exam belongs to this Center
        if ($exam->center_id != $center->id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'This exam is not assigned to this center',
            ], 403);
        }
        // Find Device
        $device = CenterDevice::where('device_code', $request->device_code)
            ->where('center_id', $center->id)
            ->first();

        if (!$device) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid Device Code for this center',
            ], 404);
        }

        if ($device->status !== 'active') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Device is inactive',
            ], 403);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Exam and Device matched successfully',
            'data'    => [
                'exam' => [
                    'id'   => $exam->id,
                    'name' => $exam->name,
                    'code' => $exam->exam_code,
                ],
                'device' => [
                    'id'   => $device->id,
                    'name' => $device->device_name,
                    'code' => $device->device_code,
                ],
            ],
        ]);
    }

    /**
     * Student Login for Exam.
     */
    public function studentLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'admission_number' => 'required|string',
            'password'         => 'required|string',
            'exam_id'          => 'required|exists:exams,id',
            'device_id'        => 'required|exists:center_devices,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $center = $request->user();

        // Find Student
        $student = Student::where('admission_number', $request->admission_number)
            ->where('tenant_id', $center->tenant_id)
            ->first();

        if (!$student) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Student not found',
            ], 404);
        }

        // Find User
        // $user = User::find($student->user_id);
        // if (!$user || !Hash::check($request->password, $user->password)) {
        //     return response()->json([
        //         'status'  => 'error',
        //         'message' => 'Invalid student credentials',
        //     ], 401);
        // }
        // Verify Student Password
        if (!Hash::check($request->password, $student->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid student credentials',
            ], 401);
        }
        if ($student->status !== 'active') {
            return response()->json([
                'status' => 'error',
                'message' => 'Student account is inactive.',
            ], 403);
        }
        // Check if student is allowed for this exam (class/section match)
        $exam = Exam::find($request->exam_id);
        $activeEnrollment = $student->activeEnrollment;

        if (!$activeEnrollment || $activeEnrollment->class_id !== $exam->class_id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Student is not eligible for this exam (Class mismatch)',
            ], 403);
        }

        // Create or get Exam Attempt
        $attempt = ExamAttempt::firstOrCreate(
            [
                'exam_id'    => $exam->id,
                'student_id' => $student->id,
            ],
            [
                'tenant_id'  => $center->tenant_id,
                'center_id'  => $center->id,
                'device_id'  => $request->device_id,
                'status'     => 'not_started',
                'ip_address' => $request->ip(),
                'device_info' => $request->header('User-Agent'),
            ]
        );

        if ($attempt->status === 'submitted') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Exam already submitted',
            ], 403);
        }

        // Issue token for the student (User)
        // $token = $user->createToken('student-exam-token')->plainTextToken;
        $token = $student->createToken('student-exam-token')->plainTextToken;
        return response()->json([
            'status'  => 'success',
            'message' => 'Student logged in successfully',
            'data'    => [
                'token'   => $token,
                'attempt' => [
                    'id'     => $attempt->id,
                    'status' => $attempt->status,
                ],
                'student' => [
                    'id'   => $student->id,
                    'name' => $student->first_name . ' ' . $student->last_name,
                    'admission_number' => $student->admission_number,
                ],
            ],
        ]);
    }

    /**
     * Logout for Exam Center.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Logged out successfully',
        ]);
    }
}
