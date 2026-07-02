<?php
use App\Http\Controllers\Api\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/register/lecturer', [AuthController::class, 'registerLecturer']);
Route::post('/login', [AuthController::class, 'login']);