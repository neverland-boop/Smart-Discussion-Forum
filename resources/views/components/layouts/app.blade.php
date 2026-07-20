<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Smart Discussion Forum</title>
    
    <!-- Inline Theme Memory Script (Prevents White Flashing) -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    
    @fluxStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-zinc-100 dark:bg-zinc-950 font-sans antialiased min-h-screen flex items-center justify-center p-4">

    <!-- The Universal Application Box Frame Container -->
    <div class="w-full max-w-6xl mx-auto bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-2xl rounded-2xl overflow-hidden flex min-h-[600px]">
        
        <!-- SIDEBAR PLATFORM COMPONENT (Left Side) -->
        <div class="w-64 bg-[#064e3b] text-zinc-100 p-6 flex flex-col justify-between relative shrink-0">
            <div class="flex flex-col gap-6">
                
                <!-- 1. NEW INTERACTIVE USER AVATAR SECTION BLOCK (Alpine.js State Driven) -->
                <div x-data="{ open: false }" class="relative px-1 border-b border-white/10 pb-5 z-20">
                    <div @click="open = !open" @click.outside="open = false" class="flex items-center gap-3 cursor-pointer group p-1.5 rounded-xl hover:bg-white/5 transition-all">
                        <!-- Clickable Profile Avatar Sphere Graphic (Uses Initials dynamically or placeholder) -->
                        <div class="w-10 h-10 rounded-full bg-[#10b981] border-2 border-emerald-400 flex items-center justify-center font-black text-xs text-white shadow-md uppercase tracking-wider group-hover:scale-105 transition-transform">
                            {{ substr(auth()->user()->name ?? 'DF', 0, 2) }}
                        </div>
                        <!-- Profile Metadata Text -->
                        <div class="truncate flex-1">
                            <span class="block text-xs font-black tracking-tight text-white truncate">{{ auth()->user()->name ?? 'Dr. Duncan Francis' }}</span>
                            <span class="block text-[10px] text-emerald-400 font-bold tracking-wide truncate mt-0.5">Manage Profile ▾</span>
                        </div>
                    </div>

                    <!-- Dropdown Selection Floating Overlay List Layer -->
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute left-0 mt-2 w-56 bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-xl py-1.5 text-zinc-800 dark:text-zinc-200"
                         style="display: none;">
                        
                        <div class="px-3.5 py-1.5 border-b border-zinc-100 dark:border-zinc-800 text-[10px] uppercase font-black text-zinc-400">
                            Account Management
                        </div>
                        
                        <!-- Navigation Shortcuts linking directly into your coordinated layout frames -->
                        <a href="{{ route('settings') }}" wire:navigate class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-bold hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-colors">
                            ⚙️ Settings
                        </a>
                        
                        <a href="{{ route('settings') }}" wire:navigate class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-bold hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-colors border-b border-zinc-100 dark:border-zinc-800">
                            👤 User Profile Details
                        </a>
                        
                        <!-- Secure Session Logout Trigger Command -->
<!-- SECURE LOGOUT ACTION TRIGGER ELEMENT -->
<button 
    onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
    class="w-full text-left flex items-center gap-2.5 px-3.5 py-2 text-xs font-bold text-red-600 hover:bg-red-50 dark:hover:bg-red-950/20 transition-colors border-none bg-transparent cursor-pointer"
>
    🚪 Secure Sign Out
</button>

<!-- CORRECTED SUBMISSION FORM CONTAINER -->
<form id="logout-form" method="POST" action="/logout" style="display: none;">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
</form>


                    </div>
                </div>

                <!-- Coordinated Main Navigation Links Network Utilizing Livewire wire:navigate -->
                <nav class="flex flex-col gap-1.5 text-sm font-semibold text-zinc-300">
                    <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'bg-[#10b981] text-white shadow-md' : 'hover:bg-white/5' }}">
                        <span>🏠</span> Dashboard
                    
                    <a href="{{ route('quizzes.create') }}" wire:navigate class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('quizzes.*') ? 'bg-[#10b981] text-white shadow-md' : 'hover:bg-white/5' }}">
                        <span>📝</span> Quizzes
                    </a>
                    <a href="{{ route('students') }}" wire:navigate class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('students') ? 'bg-[#10b981] text-white shadow-md' : 'hover:bg-white/5' }}">
                        <span>👥</span> Students
                    </a>
                    <a href="{{ route('grades') }}" wire:navigate class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('grades') ? 'bg-[#10b981] text-white shadow-md' : 'hover:bg-white/5' }}">
                        <span>📊</span> Grades
                    </a>
                    <a href="{{ route('reports') }}" wire:navigate class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('reports') ? 'bg-[#10b981] text-white shadow-md' : 'hover:bg-white/5' }}">
                        <span>📄</span> Reports
                    </a>
                    <a href="{{ route('settings') }}" wire:navigate class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('settings') ? 'bg-[#10b981] text-white shadow-md' : 'hover:bg-white/5' }}">
                        <span>⚙️</span> Settings
                    </a>
                </nav>
            </div>

            <!-- Decorative Sidebar SVG Graphics Base Layout background flags -->
            <div class="opacity-5 absolute bottom-4 left-4 pointer-events-none">
                <svg xmlns="http://w3.org" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-16 h-16 text-emerald-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12a7.5 7.5 0 0 1 15 0m-15 0a7.5 7.5 0 1 1 15 0" />
                </svg>
            </div>
        </div>

        <!-- MAIN WORKING CANVAS AREA (Right Side) -->
        <div class="flex-1 bg-white dark:bg-zinc-900 p-8 overflow-y-auto">
            {{ $slot }}
        </div>

    </div>

    @fluxScripts
</body>
</html>
