<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\StudentAuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Guest Routes (Accessible if not logged in)
Route::middleware('guest')->group(function () {
    Route::post('/register/student', [StudentAuthController::class, 'register'])->name('student.register.submit');
    Route::post('/login/student', [StudentAuthController::class, 'login'])->name('student.login.submit');
});

// Authenticated Routes (Accessible only if logged in)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [StudentAuthController::class, 'logout'])->name('logout');
    
    // Student Dashboard Route example
    Route::get('/student/dashboard', function () {
        return view('student.dashboard');
    })->name('student.dashboard');
});

// Root landing redirecting to login
Route::get('/', function () {
    return redirect()->route('student.login');
});

// Guest-only Routes (Cannot view if already authenticated)
Route::middleware('guest')->group(function () {
    // Render the form views
    Route::get('/register', [StudentAuthController::class, 'showRegisterForm'])->name('student.register');
    Route::get('/login', [StudentAuthController::class, 'showLoginForm'])->name('student.login');

    // Accept form data execution requests
    Route::post('/register', [StudentAuthController::class, 'register'])->name('student.register.submit');
    Route::post('/login', [StudentAuthController::class, 'login'])->name('student.login.submit');
});

// Protected Authenticated Profile Routes 
Route::middleware('auth')->group(function () {
    Route::post('/logout', [StudentAuthController::class, 'logout'])->name('logout');
    
    // Protected Dashboard target
    Route::get('/student/dashboard', function () {
        return view('student.dashboard');
    })->name('student.dashboard');
});

require __DIR__.'/auth.php';
