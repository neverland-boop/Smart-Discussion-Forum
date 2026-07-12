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
        
        <!-- Main Layout Wrapper -->
           <div class="flex h-screen bg-slate-800 overflow-hidden">
            
            {{-- LOCKDOWN: Only show the sidebar if NOT on the quiz attempt page --}}
            @if(!request()->routeIs('quiz.attempt'))
            <aside class="w-64 h-screen bg-slate-900 border-r border-slate-800 flex flex-col justify-between flex-shrink-0 z-50 shadow-2xl">
                
                <!-- Top Section: App Title & Navigation -->
                <div>
                    <!-- Header Area -->
                    <div class="h-16 flex items-center px-6 border-b border-slate-800">
                        <span class="text-white font-bold text-lg tracking-wider">
                            SMART DISCUSSION
                        </span>
                    </div>

                    <!-- Navigation Links -->
                    <nav class="p-4 space-y-2 mt-4">
                        
                        <!-- Dashboard -->
                        <a href="{{ route('dashboard') }}" 
                           class="w-full flex items-center px-4 py-3 rounded-md transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'bg-green-600 text-white font-medium shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-green-400' }}">
                            <svg class="w-5 h-5 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            Dashboard
                        </a>

                        <!-- Discussions (Using your 'forums' route) -->
                        <a href="{{ route('forums') ?? '#' }}" 
                           class="w-full flex items-center px-4 py-3 rounded-md transition-colors duration-200 {{ request()->routeIs('forums') ? 'bg-green-600 text-white font-medium shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-green-400' }}">
                            <svg class="w-5 h-5 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                            Discussions
                        </a>

                        <!-- Quizzes (Pending Route) -->
<!-- Locate this specific block and update it -->
                        <a href="{{ route('quizzes') }}" 
                        class="flex items-center px-4 py-3 w-full rounded-xl transition-colors duration-200 
                        {{ request()->routeIs('quizzes') ? 'bg-green-600 text-white font-medium shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-green-400' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Quizzes
                        </a>

                        <!-- Students (Pending Route) -->
                        <a href="#" 
                           class="w-full flex items-center px-4 py-3 rounded-md transition-colors duration-200 text-slate-400 hover:bg-slate-800 hover:text-green-400">
                            <svg class="w-5 h-5 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Students
                        </a>

                        <!-- Grades (Pending Route) -->
                        <a href="#" 
                           class="w-full flex items-center px-4 py-3 rounded-md transition-colors duration-200 text-slate-400 hover:bg-slate-800 hover:text-green-400">
                            <svg class="w-5 h-5 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            Grades
                        </a>

                        <!-- Reports (Pending Route) -->
                        <a href="#" 
                           class="w-full flex items-center px-4 py-3 rounded-md transition-colors duration-200 text-slate-400 hover:bg-slate-800 hover:text-green-400">
                            <svg class="w-5 h-5 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Reports
                        </a>

                        <!-- Settings (Pending Route) -->
                        <a href="#" 
                           class="w-full flex items-center px-4 py-3 rounded-md transition-colors duration-200 text-slate-400 hover:bg-slate-800 hover:text-green-400">
                            <svg class="w-5 h-5 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Settings
                        </a>
                    </nav>
                </div>

                <!-- Bottom Section: Profile & Logout -->
                <div class="p-6 border-t border-slate-800 flex items-center justify-between">
                    
                    <!-- Profile Link Area -->
                    <a href="{{ route('profile') }}" class="flex items-center gap-3 cursor-pointer group">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-green-500 to-emerald-500 flex items-center justify-center text-white font-bold shadow-lg transition-transform group-hover:scale-105">
                            {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                        </div>
                        <div class="flex flex-col text-left">
                            <span class="text-sm font-semibold text-white group-hover:text-green-400 transition-colors">{{ Auth::user()->name ?? 'Profile' }}</span>
                            <span class="text-xs text-slate-400">Student</span>
                        </div>
                    </a>

                    <!-- Existing Logout Form -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" 
                            class="w-10 h-10 rounded-full bg-slate-800 hover:bg-slate-700 flex items-center justify-center transition-colors group"
                            title="Logout">
                            <svg class="text-red-500 group-hover:text-red-400 w-5 h-5 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </button>
                    </form>
                </div>
            </aside>
@endif
            <!-- DYNAMIC CONTENT AREA -->
            <main class="flex-1 overflow-y-auto bg-slate-800">
                {{ $slot }}
            </main>
    
        </div>
    </body>
</html>