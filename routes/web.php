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

require __DIR__.'/auth.php';