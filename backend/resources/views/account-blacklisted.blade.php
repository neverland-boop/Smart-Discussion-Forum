<x-guest-layout>
    <!-- We remove the generic HTML/Body tags and let the guest-layout handle the background and centering -->
    <div class="max-w-md w-full bg-gray-800 p-8 rounded-2xl shadow-xl border border-gray-700 text-center sm:mx-auto">
        
        <!-- Icon -->
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-900/30 mb-6">
            <svg class="h-10 w-10 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>

        <h1 class="text-2xl font-bold text-white mb-2">Account Suspended</h1>
        
        <p class="text-gray-400 mb-6">
            Your access to the discussion platform has been temporarily suspended due to multiple policy violations or prolonged inactivity.
        </p>

        <div class="bg-gray-900 rounded-lg p-4 mb-8 border border-gray-700 text-left">
            <h3 class="text-sm font-semibold text-gray-300 mb-1">What happens next?</h3>
            <p class="text-sm text-gray-500">
                Suspensions typically last for 7 days. Once your suspension period expires, your account access will be automatically restored, and your warnings will be reset.
            </p>
        </div>

        <a href="{{ route('login') }}" class="inline-block w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-lg transition-colors">
            Return to Login
        </a>
        
    </div>
</x-guest-layout>