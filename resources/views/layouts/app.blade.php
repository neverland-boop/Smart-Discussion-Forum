<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Smart Discussion') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-50">
        
        <!-- Discord Layout Wrapper -->
        <div class="flex h-screen bg-slate-800 overflow-hidden">
            
            <!-- GLOBAL VERTICAL SIDEBAR -->
            <nav class="w-[72px] bg-slate-900 flex flex-col items-center py-3 gap-3 flex-shrink-0 z-50 shadow-2xl">
                
                <!-- Brand / Home Icon -->
                <a href="{{ route('dashboard') }}" class="relative group flex items-center justify-center w-12 h-12 bg-slate-800 text-white rounded-[24px] hover:rounded-[16px] hover:bg-purple-600 transition-all duration-300">
                    <span class="font-black text-xl">S</span>
                    <div class="absolute left-16 bg-black text-white text-sm font-bold px-3 py-1 rounded shadow-lg opacity-0 scale-0 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left pointer-events-none whitespace-nowrap z-50">
                        Dashboard
                    </div>
                </a>

                <div class="w-8 h-[2px] bg-slate-700 rounded-full my-1"></div>

                <!-- Quizzes -->
                <a href="#" class="relative group flex items-center justify-center w-12 h-12 bg-slate-800 text-emerald-500 hover:text-white rounded-[24px] hover:rounded-[16px] hover:bg-emerald-500 transition-all duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    <div class="absolute left-16 bg-black text-white text-sm font-bold px-3 py-1 rounded shadow-lg opacity-0 scale-0 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left pointer-events-none whitespace-nowrap z-50">
                        Quizzes
                    </div>
                </a>

                <!-- Forums -->
                <!-- Forums Icon in app.blade.php -->
                <a href="{{ route('forums') }}" class="relative group flex items-center justify-center w-12 h-12 bg-slate-800 text-blue-500 hover:text-white rounded-[24px] hover:rounded-[16px] hover:bg-blue-500 transition-all duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                    <div class="absolute left-16 bg-black text-white text-sm font-bold px-3 py-1 rounded shadow-lg opacity-0 scale-0 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left pointer-events-none whitespace-nowrap z-50">
                        Discussions
                    </div>
                </a>
                

                <!-- Spacer -->
                <div class="flex-grow"></div>

                <!-- Profile -->
                <a href="{{ route('profile') }}" class="relative group flex items-center justify-center w-12 h-12 bg-gradient-to-tr from-purple-500 to-blue-500 text-white font-bold rounded-[24px] hover:rounded-[16px] transition-all duration-300 shadow-lg">
                    {{ auth()->user()->initials() ?? 'U' }}
                    <div class="absolute left-16 bg-black text-white text-sm font-bold px-3 py-1 rounded shadow-lg opacity-0 scale-0 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left pointer-events-none whitespace-nowrap z-50">
                        Profile
                    </div>
                </a>

                <!-- Logout -->
 <!-- Use the default route provided by Laravel's auth.php -->
                <form method="POST" action="{{ route('logout') }}" class="w-full flex justify-center mb-2">
                    @csrf
                    <button type="submit" class="relative group flex items-center justify-center w-12 h-12 bg-slate-800 text-red-500 hover:text-white rounded-[24px] hover:rounded-[16px] hover:bg-red-600 transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        <div class="absolute left-16 bg-black text-white text-sm font-bold px-3 py-1 rounded shadow-lg opacity-0 scale-0 group-hover:opacity-100 group-hover:scale-100 transition-all origin-left pointer-events-none whitespace-nowrap z-50">
                            Logout
                        </div>
                    </button>
                </form>
            </nav>

            <!-- DYNAMIC CONTENT AREA -->
            <main class="flex-1 overflow-y-auto bg-slate-800 p-8">
                {{ $slot }}
            </main>

        </div>
    </body>
</html>