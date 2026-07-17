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

Route::middleware(['auth:sanctum'])->group(function () {
    
    // --- Administrator Only ---
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::post('/register-lecturer', [AuthController::class, 'registerLecturer']);
        Route::get('/groups', [GroupController::class, 'adminIndex']); 
    });

    // --- Quiz Management (Lecturer/Admin) ---
    Route::middleware(['role:lecturer|admin'])->group(function () {
        Route::apiResource('groups.quizzes', QuizController::class)->only(['store']);
        Route::get('/quizzes/{quiz}/report', [QuizController::class, 'report']);
    });
    
    // --- General Group Actions ---
    // Custom action goes BEFORE the resource
    Route::post('/groups/join', [GroupController::class, 'join']); 
    
    // Using apiResource for index and store
    Route::apiResource('groups', GroupController::class)->only(['index', 'store']);
    
    Route::post('/logout', [AuthController::class, 'logout']);
});

// --- FORUM & INTERACTION ROUTES (Protected by Blacklist) ---
Route::middleware(['auth:sanctum', 'check.blacklist'])->group(function () {
    
    // Nested Resource for Topics (Handles GET /groups/{group}/topics and POST /groups/{group}/topics)
    Route::apiResource('groups.topics', TopicController::class)->only(['index', 'store']);

    // Custom Moderation/Access Actions
    Route::post('/topics/request-access', [TopicController::class, 'requestAccess']);
    Route::post('/topics/approve', [TopicController::class, 'approve']);
    Route::post('/topics/warn', [TopicController::class, 'warn']);

    Route::get('/quizzes', [QuizController::class, 'index']); 
    
    Route::get('/quizzes/{quiz}', [QuizController::class, 'show']); 

    Route::post('/attempts/{attempt}/submit', [QuizAttemptController::class, 'submit']);

    Route::apiResource('topics.posts', PostController::class)->only(['index']);
    
    // Custom action for sending messages (triggers compliance)
    Route::post('/topics/message', [TopicController::class, 'sendMessage']); 
    
});