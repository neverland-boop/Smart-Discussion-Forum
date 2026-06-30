<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\TopicController;
use App\Http\Controllers\Api\GroupController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/onboard', [AuthController::class, 'onboard']);
    Route::get('/profile', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('groups', GroupController::class);
    Route::apiResource('topics', TopicController::class);
    Route::apiResource('posts', PostController::class);

    
    // Get the current user
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
