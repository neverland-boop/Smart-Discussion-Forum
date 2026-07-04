<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Smart Discussion Forum') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 dark:text-gray-100 antialiased bg-gray-50 dark:bg-[#121212] transition-colors duration-300">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            
            <!-- CUSTOM ICON / LOGO POSITION -->
            <div class="mb-8 flex flex-col items-center">
                <a href="/" wire:navigate class="flex flex-col items-center group">
                    
                    <!-- The Icon Wrapper -->
                    <div class="w-20 h-20 bg-[#5CC98B] rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform duration-200">
                        <!-- Placeholder SVG (Replace this <svg> tag with your own <img> tag later) -->
                        <svg class="w-10 h-10 text-[#1e1e1e]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>

                    <!-- App Title -->
                    <span class="mt-4 text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">Smart Discussion</span>
                </a>
            </div>

            <!-- FORM CARD -->
            <div class="w-full sm:max-w-md px-8 py-8 bg-white dark:bg-[#1e1e1e] shadow-xl border border-gray-100 dark:border-gray-800 sm:rounded-xl transition-colors duration-300">
                {{ $slot }}
            </div>

        </div>
    </body>
</html>