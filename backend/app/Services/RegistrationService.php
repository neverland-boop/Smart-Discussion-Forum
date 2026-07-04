<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class RegistrationService
{
    /**
     * Handle the registration logic for both Web and API.
     */
    public function registerUser(array $data, string $role = 'student')
    {
        // Enforce Requirement #5: Rules Agreement
        if (!isset($data['agreed_to_rules']) || $data['agreed_to_rules'] == false) {
            throw ValidationException::withMessages([
                'agreed_to_rules' => 'You must agree to the platform rules to register.'
            ]);
        }

        // 1. Create the user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'agreed_to_rules' => true,
        ]);

        // 2. Assign the role (Requires Spatie/Laravel-Permission)
        $user->assignRole($role);

        return $user;
    }
}