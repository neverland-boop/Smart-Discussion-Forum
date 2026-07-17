<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Smart Discussion Forum</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50 dark:bg-[#121212] text-gray-900 dark:text-gray-100 flex flex-col min-h-screen transition-colors duration-300">

    <!-- Main Hero Section -->
    <main class="flex-grow flex flex-col items-center justify-center px-4 text-center pt-20 pb-16">
        <h1 class="text-4xl md:text-6xl font-extrabold text-gray-900 dark:text-white mb-6 tracking-tight">
            Welcome to the <br class="hidden sm:block" />
            <span class="text-[#5CC98B]">Smart Discussion Forum</span>
        </h1>
        
        <p class="text-lg md:text-xl text-gray-600 dark:text-gray-400 mb-10 max-w-2xl mx-auto">
            Share, Learn, and Grow Together. Join our academic community to engage in meaningful discussions, access valuable resources, and connect with peers and lecturers.
        </p>

        <!-- Centered Authentication Buttons -->
        @if (Route::has('login'))
            <div class="flex flex-col sm:flex-row gap-4 justify-center w-full max-w-md mx-auto">
                @auth
                    <!-- If already logged in -->
                    <a href="{{ url('/dashboard') }}" class="w-full px-8 py-3 rounded-md font-semibold text-white bg-[#1e1e1e] dark:bg-gray-800 hover:bg-black dark:hover:bg-gray-700 transition ease-in-out duration-150 shadow-lg border border-transparent dark:border-gray-600">
                        Go to Dashboard
                    </a>
                @else
                    <!-- Log In Button -->
                    <a href="{{ route('login') }}" class="w-full px-8 py-3 rounded-md font-semibold text-white bg-[#1e1e1e] dark:bg-gray-800 hover:bg-black dark:hover:bg-gray-700 transition ease-in-out duration-150 shadow-lg border border-transparent dark:border-gray-600">
                        Log in
                    </a>

                    @if (Route::has('register'))
                        <!-- Register Button -->
                        <a href="{{ route('register') }}" class="w-full px-8 py-3 rounded-md font-semibold text-[#1e1e1e] bg-[#5CC98B] hover:bg-[#4ab879] transition ease-in-out duration-150 shadow-lg">
                            Register
                        </a>
                    @endif
                @endauth
            </div>
        @endif
    </main>

    <!-- Professional Features Section (Placeholders) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20 w-full">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <!-- Feature Card 1 -->
            <div class="bg-white dark:bg-[#1e1e1e] p-6 rounded-lg shadow-sm border border-gray-100 dark:border-gray-800 border-t-4 border-t-[#5CC98B] transition-colors duration-300">
                <h3 class="text-xl font-bold mb-2 text-gray-800 dark:text-gray-100">Academic Discussions</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Engage in organized topics strictly moderated to ensure quality learning and continuous academic growth.</p>
            </div>

            <!-- Feature Card 2 -->
            <div class="bg-white dark:bg-[#1e1e1e] p-6 rounded-lg shadow-sm border border-gray-100 dark:border-gray-800 border-t-4 border-t-gray-800 dark:border-t-gray-500 transition-colors duration-300">
                <h3 class="text-xl font-bold mb-2 text-gray-800 dark:text-gray-100">Connect with Lecturers</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Get direct insights, feedback, and guidance from authorized university staff members and professors.</p>
            </div>

            <!-- Feature Card 3 -->
            <div class="bg-white dark:bg-[#1e1e1e] p-6 rounded-lg shadow-sm border border-gray-100 dark:border-gray-800 border-t-4 border-t-[#5CC98B] transition-colors duration-300">
                <h3 class="text-xl font-bold mb-2 text-gray-800 dark:text-gray-100">Secure & Moderated</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">A safe platform equipped with clear community rules, ensuring a respectful environment for all members.</p>
            </div>

        </div>
    </section>

    <!-- Simple Footer -->
    <footer class="bg-gray-100 dark:bg-[#0a0a0a] py-6 mt-auto border-t border-gray-200 dark:border-gray-800 text-center text-sm text-gray-500 dark:text-gray-500 transition-colors duration-300">
        &copy; {{ date('Y') }} Smart Discussion Forum. All rights reserved.
    </footer>

</body>
</html>