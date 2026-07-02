<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

use function Livewire\Volt\layout;
use function Livewire\Volt\rules;
use function Livewire\Volt\state;

layout('layouts.guest');

state([
    'name' => '',
    'email' => '',
    'password' => '',
    'password_confirmation' => '',
    'agreed_to_rules' => false
]);

rules([
    'name' => ['required', 'string', 'max:255'],
    'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
    'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
    'agreed_to_rules' => ['accepted']
]);

$register = function (RegistrationService $registrationService) {
    $validated = $this->validate();

    $user = $registrationService->registerUser($validated, 'student');

    event(new Registered($user));

    Auth::login($user);

    $this->redirect(route('dashboard', absolute: false), navigate: true);
};

?>

<div>
    <form wire:submit="register">
        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input wire:model="password" id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- New Rules Agreement Requirement -->
 <!-- Rules Agreement -->
        <!-- Rules Section -->
        <div class="mt-4">
            <div class="flex items-center">
                <input wire:model="agreed_to_rules" id="agreed_to_rules" type="checkbox" 
                    class="rounded border-gray-600 bg-gray-700 text-indigo-500 shadow-sm focus:ring-indigo-500">
                <label for="agreed_to_rules" class="ms-2 text-sm text-gray-300">
                    {{ __('I have read and agree to the') }}
                    <!-- Trigger for the modal -->
                    <button type="button" @click="$dispatch('open-modal', 'platform-rules')" 
                            class="text-indigo-400 hover:text-indigo-300 underline font-semibold">
                        {{ __('Platform Rules') }}
                    </button>
                </label>
            </div>
            <x-input-error :messages="$errors->get('agreed_to_rules')" class="mt-2" />
        </div>

        <!-- Modal Component -->
        <x-modal name="platform-rules" focusable>
            <div class="p-6 bg-gray-800 text-gray-200">
                <h2 class="text-lg font-medium text-white mb-4">Platform Rules</h2>
                <ul class="list-decimal list-inside space-y-2 text-sm text-gray-300">
                    <li>Respect all other users in the forum.</li>
                    <li>No hate speech, harassment, or spam.</li>
                    <li>Keep discussions relevant to the academic topics.</li>
                    <li>Moderators have the final say on all content.</li>
                </ul>
                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Close') }}
                    </x-secondary-button>
                </div>
            </div>
        </x-modal>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}" wire:navigate>
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</div>
