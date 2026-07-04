<?php
use App\Services\RegistrationService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.guest')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $secret_code = ''; // The special field
    public bool $agreed_to_rules = false;

    public function register(RegistrationService $service) {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'agreed_to_rules' => ['accepted'],
            'secret_code' => ['required', 'string'],
        ]);

        // Security Check
        if ($validated['secret_code'] !== env('LECTURER_SECRET_CODE')) {
            $this->addError('secret_code', 'Invalid registration code.');
            return;
        }

        $user = $service->registerUser($validated, 'lecturer');
        event(new Registered($user));
        Auth::login($user);
        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="space-y-6">
    <form wire:submit="register">
        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input wire:model="password" id="password" class="block mt-1 w-full" type="password" name="password" required />
        </div>

        <!-- Secret Code -->
        <div class="mt-4">
            <x-input-label for="secret_code" :value="__('Lecturer Registration Code')" />
            <x-text-input wire:model="secret_code" id="secret_code" class="block mt-1 w-full" type="password" required />
            <x-input-error :messages="$errors->get('secret_code')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a href="{{ route('register.lecturer') }}" wire:navigate>
            <x-primary-button>
                {{ __('Register as Lecturer') }}
            </x-primary-button>
        </div>
    </form>
</div>