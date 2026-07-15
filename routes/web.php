<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Redirect guest users visiting the root URL straight to login
Route::get('/', function () {
    return redirect()->route('login');
});

// SECURE USER WORKSPACE: Only accessible after logging in successfully
Route::middleware(['auth', 'verified'])->group(function () {
    
    // 1. Central Hub Dashboard Layout
    Volt::route('/dashboard', 'lecturer-dashboard')->name('dashboard');
    
    // 2. Forums & Messaging Channels
    Volt::route('/discussions', 'chat-room')->name('discussions');
    
    // 3. Quizzes Portal Panel (The screen we built)
    Volt::route('/quizzes/create', 'create-quiz')->name('quizzes.create');
    
    
    Volt::route('/students', 'students')->name('students');
    Volt::route('/grades', 'grades-index')->name('grades');
    Volt::route('/reports', 'reports-index')->name('reports');
    Volt::route('/settings', 'settings-index')->name('settings');
});

require __DIR__.'/auth.php';
