<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Volt;

Route::view('/', 'welcome');

Volt::route('register/lecturer', 'pages.auth.register-lecturer')
    ->middleware(['auth', 'role:admin']) 
    ->name('register.lecturer');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/account-blacklisted', function () {
    return view('account-blacklisted'); 
})->name('account.blacklisted');

Route::post('/logout', function () {
    Auth::guard('web')->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

Volt::route('forums', 'student.chat-interface')
    // Add 'role:student' here to block admins and lecturers
    ->middleware(['auth', 'verified', 'role:student']) 
    ->name('forums');
    
Volt::route('quizzes', 'student.quiz-dashboard')
    ->middleware(['auth', 'verified', 'role:student'])
    ->name('quizzes');

Volt::route('quizzes/{id}/attempt', 'student.quiz-attempt')
    ->middleware(['auth', 'verified', 'role:student'])
    ->name('quiz.attempt');

Route::middleware(['auth', 'role:lecturer'])->group(function () {
    Volt::route('/lecturer/students', 'lecturer.students')->name('lecturer.students');
    Volt::route('/lecturer/grades', 'lecturer.grades')->name('lecturer.grades');
});

Volt::route('register/lecturer', 'pages.auth.register-lecturer')
    ->middleware(['auth', 'role:admin']) 
    ->name('register.lecturer');
require __DIR__.'/auth.php';