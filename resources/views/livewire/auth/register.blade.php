<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

// Note: We completely remove the default layout wrapper here to take total control over the CSS canvas
new #[Layout('components.layouts.auth.simple')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $agree_to_rules = false; 

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'agree_to_rules' => ['accepted'], 
        ], [
            'agree_to_rules.accepted' => 'You must agree to the platform rules to create an account.',
        ]);

        unset($validated['agree_to_rules']); 

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>
<!-- Main Application Window Wrapper -->
<div class="w-full max-w-4xl mx-auto my-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 shadow-xl rounded-xl overflow-hidden font-sans">
    
    <!-- 1. Flat Window App Header Bar -->
    <div class="w-full bg-zinc-800 text-zinc-300 px-4 py-2.5 text-xs font-semibold border-b border-zinc-700 select-none">
        <span>Smart Discussion Forum</span>
    </div>

    <!-- 2. Brand Banner Header Section -->
    <div class="w-full bg-zinc-50 dark:bg-zinc-950 p-6 border-b border-zinc-200 dark:border-zinc-800">
        <h1 class="text-2xl font-black tracking-wide text-zinc-950 dark:text-white block !text-zinc-950 dark:!text-white">
            Smart Discussion Forum
        </h1>
        <p class="text-xs text-zinc-500 dark:text-zinc-400 font-semibold block mt-1">
            Share Learn Grow Together
        </p>
    </div>

    <!-- 3. Dynamic Side-by-Side Content Grid -->
    <div class="grid grid-cols-1 md:grid-cols-12 w-full bg-zinc-50 dark:bg-zinc-950">
        
        <!-- LEFT PANEL: Registration Inputs (7 out of 12 columns) -->
        <div class="col-span-1 md:col-span-7 p-6 md:p-8 flex flex-col justify-between bg-white dark:bg-zinc-900 border-b md:border-b-0 md:border-r border-zinc-200 dark:border-zinc-800">
            <div>
                <h2 class="text-base font-bold text-zinc-800 dark:text-zinc-100 mb-0.5">Create an account</h2>
                <p class="text-[11px] text-zinc-400 mb-6">Join the Smart Discussion Forum</p>

                <!-- Form Validation Alerts -->
                @if ($errors->any())
                    <div class="mb-4 text-xs text-red-600 space-y-1 bg-red-50 dark:bg-red-950/20 p-3 rounded border border-red-200 dark:border-red-900">
                        @foreach ($errors->all() as $error)
                            <p>• {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form wire:submit="register" id="registrationForm" class="flex flex-col gap-4">
                    <!-- Full Name -->
                    <div class="flex flex-col gap-1">
                        <label for="name" class="text-xs font-semibold text-zinc-600 dark:text-zinc-400">Full Name</label>
                        <flux:input wire:model="name" id="name" type="text" name="name" required autofocus autocomplete="name" class="!py-1" />
                    </div>

                    <!-- Email Address -->
                    <div class="flex flex-col gap-1">
                        <label for="email" class="text-xs font-semibold text-zinc-600 dark:text-zinc-400">Email Address</label>
                        <flux:input wire:model="email" id="email" type="email" name="email" required autocomplete="email" class="!py-1" />
                    </div>

                    <!-- Password (FIXED: Relies on native flux positioning without explicit sizing overrides) -->
                    <div class="flex flex-col gap-1">
                        <label for="password" class="text-xs font-semibold text-zinc-600 dark:text-zinc-400">Password</label>
                        <flux:input wire:model="password" id="password" type="password" name="password" required autocomplete="new-password" viewable />
                    </div>

                    <!-- Confirm Password -->
                    <div class="flex flex-col gap-1">
                        <label for="password_confirmation" class="text-xs font-semibold text-zinc-600 dark:text-zinc-400">Confirm Password</label>
                        <flux:input wire:model="password_confirmation" id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="!py-1" />
                    </div>
                </form>
            </div>

            <!-- Login Redirect Link -->
            <div class="mt-8 text-xs text-zinc-500 dark:text-zinc-400">
                Already have an account? 
                <x-text-link href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 hover:underline ml-0.5 font-bold !text-blue-600">
                    Login here
                </x-text-link>
            </div>
        </div>

        <!-- RIGHT PANEL: Full Green Column Background (5 out of 12 columns) -->
        <div class="col-span-1 md:col-span-5 p-6 md:p-8 bg-[#52c48a] flex flex-col justify-between gap-6 border-t md:border-t-0 border-zinc-200 dark:border-zinc-800">
            
            <!-- Platform Rules Bright Inner Card -->
            <div class="bg-white/10 dark:bg-black/10 border border-white/20 rounded-xl p-6 text-zinc-900 dark:text-white grow flex flex-col justify-center min-h-[220px]">
                <h3 class="text-center font-black text-base text-zinc-950 dark:text-white mb-4 tracking-wide uppercase">
                    Platform Rules
                </h3>
                <ul class="space-y-4 text-xs font-bold text-zinc-900 dark:text-zinc-100 leading-relaxed text-center">
                    <li>Be respectful to all members</li>
                    <li>No spam or irrelevant content</li>
                    <li>Use the platform for academic discussions</li>
                    <li>Violation may lead to warnings or bans</li>
                </ul>
            </div>

            <!-- Aggregated Submission Block -->
            <div class="flex flex-col gap-4">
                <!-- Checkbox Integration Container -->
                <div class="flex items-center gap-2 px-1">
                    <input 
                        wire:model="agree_to_rules" 
                        id="agree_to_rules" 
                        type="checkbox" 
                        name="agree_to_rules"
                        class="w-4 h-4 text-zinc-900 border-white/40 rounded focus:ring-zinc-900 dark:bg-zinc-900 dark:border-zinc-700"
                        required
                    />
                    <label for="agree_to_rules" class="text-xs font-bold text-zinc-900 dark:text-zinc-100 select-none cursor-pointer">
                        I agree to the platform rules and terms
                    </label>
                </div>

                <!-- Registration Button (FIXED: Standard HTML button eliminates the broken circle anomaly) -->
                <div>
                    <button form="registrationForm" type="submit" class="w-full bg-zinc-900 hover:bg-zinc-800 text-white py-3 text-xs font-bold tracking-wide rounded shadow transition-all block border-none text-center cursor-pointer">
                        Register
                    </button>
                </div>
            </div>

        </div>

    </div>
</div>

