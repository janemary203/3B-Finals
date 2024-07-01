<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;

// Routes for students
Route::get('/students', [StudentController::class, 'index']);        // GET /api/students
Route::post('/students', [StudentController::class, 'store']);       // POST /api/students
Route::get('/students/{student_id}', [StudentController::class, 'show']);    // GET /api/students/{student_id}
Route::patch('/students/{student_id}', [StudentController::class, 'update']); // PATCH /api/students/{student_id}

// Routes for subjects related to a specific student
Route::prefix('students/{student_id}')->group(function () {
    Route::get('/subjects', [SubjectController::class, 'index']);
    Route::post('/subjects', [SubjectController::class, 'store']);
    Route::get('/subjects/{subject_id}', [SubjectController::class, 'show']);
    Route::patch('/subjects/{subject_id}', [SubjectController::class, 'update']);
});
