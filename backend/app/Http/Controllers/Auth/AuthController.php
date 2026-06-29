<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'email' => 'required|string|unique:users|email|max:255',
            'password' => 'required|string|min:8'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'username' => $request->username,
            'email' => $request ->email,
            'password' => Hash::make($request->password),
            'account_status' => 'PENDING'
        ]);
        
        return response() -> json([
            'message' => 'User registered succesfully, Please accept the rules',
            'user' => $user  
        ], 201);
    }

    public function login(Request $request){
        $credentials = $request->only('email', 'password');

        if (!auth()->attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $user = auth()->user();

        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json([
        'token' => $token,
        'status' => $user->account_status,
        'message' => $user->account_status === 'PENDING' ? 'Account pending onboarding' : 'Login successful'], 200);
    }

    public function onboard(Request $request){
        $user = auth()->user();

        if ($user->account_status === 'ACTIVE') {
        return response()->json(['message' => 'Account is already onboarded'], 200);
    }
        if($request-> agreed === true){
            $user->update(['account_status' => 'ACTIVE']);
            return response()->json(['message' => 'Onbording complete'], 200);
        };
        return response()->json(['error'=>'Please agree to the rules'], 422);
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response() -> json(['message' => 'Logged out succesfully'], 200);
    }

    public function me(Request $request){
        return response()->json($request->user(), 200);
    }
}
