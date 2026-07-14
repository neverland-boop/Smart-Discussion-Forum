<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\TopicController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\QuizController;
use App\Http\Controllers\Api\QuizAttemptController;
use Illuminate\Support\Facades\Route;


// Public Authentication Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Protected Routes
Route::middleware(['auth:sanctum'])->group(function () {
    // --- Administrator Only Routes (Using Spatie Role Middleware) ---
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::post('/register-lecturer', [AuthController::class, 'registerLecturer']);
        Route::get('/groups', [GroupController::class, 'index']); // Admin can view all groups
        Route::post('/groups', [GroupController::class, 'store']);
    });

    // --- General Authenticated Routes (Students, Lecturers, Admins) ---
    
    // Group & Topic Management


    // Quiz Management
    // Only Lecturers/Admins should ideally access store, but this is base access
    Route::middleware(['role:lecturer|admin'])->group(function () {
        Route::post('/groups/{group}/quizzes', [QuizController::class, 'store']);
        Route::get('/quizzes/{quiz}/report', [QuizController::class, 'report']);
    });
    
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum', 'check.blacklist'])->group(function () {
    Route::get('/groups/{group}/topics', [TopicController::class, 'index']);
    Route::post('/groups/{group}/topics', [TopicController::class, 'store']);

    // Forum/Thread Communication
    Route::get('/topics/{topic}/posts', [PostController::class, 'index']);
    Route::post('/topics/{topic}/posts', [PostController::class, 'store']);
    Route::post('/attempts/{attempt}/submit', [QuizAttemptController::class, 'submit']);
    });