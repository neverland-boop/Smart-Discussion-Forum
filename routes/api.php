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
        Route::get('/groups', [GroupController::class, 'adminIndex']); // Suggest renaming the admin method to avoid conflicts
        Route::post('/groups', [GroupController::class, 'store']);
    });

    // --- Quiz Management ---
    Route::middleware(['role:lecturer|admin'])->group(function () {
        Route::post('/groups/{group}/quizzes', [QuizController::class, 'store']);
        Route::get('/quizzes/{quiz}/report', [QuizController::class, 'report']);
    });
    
    // --- General Authenticated Routes (Students, Lecturers, Admins) ---
    // These allow suspended users to see available groups, but not participate yet
    Route::get('/groups', [GroupController::class, 'index']); // Uses the new Service-based index
    Route::post('/groups/join', [GroupController::class, 'join']);
    
    Route::post('/logout', [AuthController::class, 'logout']);
});

// --- FORUM & INTERACTION ROUTES (Protected by Blacklist) ---
Route::middleware(['auth:sanctum', 'check.blacklist'])->group(function () {
    
    // Topic Viewing & Creation
    Route::get('/groups/{group}/topics', [TopicController::class, 'index']);
    Route::post('/groups/{group}/topics', [TopicController::class, 'store']);

    // --- NEW: Topic Participation & Moderation ---
    Route::post('/topics/request-access', [TopicController::class, 'requestAccess']);
    Route::post('/topics/approve', [TopicController::class, 'approve']);
    Route::post('/topics/warn', [TopicController::class, 'warn']);

    // --- Forum/Thread Communication ---
    Route::get('/topics/{topic}/posts', [PostController::class, 'index']);
    
    // Replaced your old PostController@store with the new TopicController@sendMessage 
    // so that it triggers the "Compliance/Un-warning" logic when a desktop user posts
    Route::post('/topics/message', [TopicController::class, 'sendMessage']); 
    
    // Quizzes
    Route::post('/attempts/{attempt}/submit', [QuizAttemptController::class, 'submit']);
});