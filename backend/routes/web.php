<?php

use App\Livewire\Pages\Auth\RegisterLecturer;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome');

Volt::route('register/lecturer', 'pages.auth.register-lecturer')
    ->name('register.lecturer');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');



require __DIR__.'/auth.php';
