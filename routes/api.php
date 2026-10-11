<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ExamCenter\AuthController;
use App\Http\Controllers\Api\ExamCenter\ExamController as StudentExamController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('exam-center')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/match-device', [AuthController::class, 'matchDevice']);
        Route::post('/student-login', [AuthController::class, 'studentLogin']);
    });
});

Route::prefix('exam')->middleware('auth:sanctum')->group(function () {
    Route::get('/{exam_id}/details', [StudentExamController::class, 'details']);
    Route::post('/{exam_id}/start', [StudentExamController::class, 'startExam']);
    Route::get('/{exam_id}/questions', [StudentExamController::class, 'getQuestions']);
});
