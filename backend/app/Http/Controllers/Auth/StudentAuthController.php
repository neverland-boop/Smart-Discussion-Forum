<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StudentAuthController extends Controller
{
    // Render registration UI form layout
    public function showRegisterForm()
    {
        return view('auth.student-register');
    }

    // Render logging UI authentication input panel
    public function showLoginForm()
    {
        return view('auth.student-login');
    }

    //1. Handle Student Registration
  public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create user and explicitly enforce the 'student' role
        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password']),
            'role' => 'student', 
        ]);

        // Log the student in immediately after registration
        Auth::login($user);

        return redirect()->route('student.dashboard')->with('success', 'Registration successful!');
    }

    // 2. Handle Student Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt to log in with provided credentials
        if (Auth::attempt($credentials, $request->remember)) {
            // CRITICAL: Verify the logged-in user actually has the student role
            if (Auth::user()->role === 'student') {
                $request->session()->regenerate();
                return redirect()->route('student.dashboard');
            }

            // If they are a lecturer or admin trying to use student login, boot them
            Auth::logout();
            return back()->withErrors(['email' => 'Access denied. This portal is for students only.']);
        }

        return back()->withErrors(['email' => 'The provided credentials do not match our records.']);
    }

    // 3. Handle Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('student.login')->with('success', 'Logged out successfully.');
    }  


}
