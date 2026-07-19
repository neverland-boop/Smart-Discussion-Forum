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
<body class="font-sans antialiased text-slate-50" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen bg-slate-800 overflow-hidden">
        <!-- Sidebar -->
        @if(!request()->routeIs('quiz.attempt'))
            <!-- Mobile backdrop -->
            <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
                 x-transition:enter="transition-opacity ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/60 z-40 lg:hidden"></div>

            <aside
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                class="fixed inset-y-0 left-0 z-50 w-64 h-full bg-slate-900 border-r border-slate-800 flex flex-col justify-between shrink-0 shadow-2xl transform transition-transform duration-200 ease-out lg:static lg:translate-x-0 lg:shadow-none">
                <div>
                    <div class="h-16 flex items-center justify-between px-6 border-b border-slate-800">
                        <span class="text-white font-bold text-lg tracking-wider">SMART DISCUSSION</span>
                        <!-- Mobile close button -->
                        <button @click="sidebarOpen = false" type="button" aria-label="Close menu" class="lg:hidden text-slate-400 hover:text-white p-1 -mr-2 rounded-md hover:bg-slate-800 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <nav class="p-4 space-y-2 mt-4">
                        <a href="{{ route('dashboard') }}" @click="sidebarOpen = false" class="w-full flex items-center px-4 py-3 rounded-md transition-colors {{ request()->routeIs('dashboard') ? 'bg-green-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-green-400' }}">Dashboard</a>
                        @if(auth()->user()->hasAnyRole(['student']))
                        <a href="{{ route('forums') }}" @click="sidebarOpen = false" class="w-full flex items-center px-4 py-3 rounded-md transition-colors {{ request()->routeIs('forums') ? 'bg-green-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-green-400' }}">Discussions</a>
                        <a href="{{ route('quizzes') }}" @click="sidebarOpen = false" class="w-full flex items-center px-4 py-3 rounded-md transition-colors {{ request()->routeIs('quizzes') ? 'bg-green-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-green-400' }}">Quizzes</a>
                        @endif
                        @if(auth()->user()->hasAnyRole(['lecturer']))
                            <a href="{{ route('lecturer.students') }}" @click="sidebarOpen = false" class="w-full flex items-center px-4 py-3 rounded-md transition-colors text-slate-400 hover:bg-slate-800 hover:text-green-400">Students</a>
                            <a href="{{ route('lecturer.grades') }}" @click="sidebarOpen = false" class="w-full flex items-center px-4 py-3 rounded-md transition-colors text-slate-400 hover:bg-slate-800 hover:text-green-400">Grades</a>
                            <a href="#" @click="sidebarOpen = false" class="w-full flex items-center px-4 py-3 rounded-md transition-colors text-slate-400 hover:bg-slate-800 hover:text-green-400">Quiz Control</a>
                             @endif
                        @if(auth()->user()->hasAnyRole(['admin']))
                            <a href="{{ route('admin.members') }}" @click="sidebarOpen = false" class="w-full flex items-center px-4 py-3 rounded-md transition-colors text-slate-400 hover:bg-slate-800 hover:text-green-400">Members</a>
                        @endif
                        @if(auth()->user()->hasAnyRole(['lecturer', 'admin']))
                            <a href="#" @click="sidebarOpen = false" class="w-full flex items-center px-4 py-3 rounded-md transition-colors text-slate-400 hover:bg-slate-800 hover:text-green-400">Reports</a>
                        @endif  

                    </nav>
                </div>

                <!-- Profile Dropdown -->
                <div class="p-6 border-t border-slate-800">
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-3 w-full">
                            <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center font-bold text-white shrink-0">{{ substr(Auth::user()->name, 0, 1) }}</div>
                            <div class="flex flex-col text-left min-w-0">
                                <span class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</span>
                                <span class="text-xs text-slate-400 truncate">Settings & Profile</span>
                            </div>
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute bottom-16 left-0 w-full bg-slate-800 border border-slate-700 rounded-lg p-2 z-50">
                            <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-slate-300 hover:bg-slate-700">Profile</a>
                            <a href="#" class="block px-4 py-2 text-sm text-slate-300 hover:bg-slate-700">Settings</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-slate-700">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </aside>
        @endif

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Mobile top bar with hamburger -->
            @if(!request()->routeIs('quiz.attempt'))
                <header class="h-16 flex items-center gap-3 px-4 border-b border-slate-800 bg-slate-900 shrink-0 lg:hidden">
                    <button @click="sidebarOpen = true" type="button" aria-label="Open menu" class="p-2 -ml-2 rounded-md text-slate-300 hover:text-white hover:bg-slate-800 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <span class="text-white font-bold tracking-wider text-sm">SMART DISCUSSION</span>
                </header>
            @endif
            <main class="flex-1 overflow-y-auto bg-slate-800">
                {{ $slot }}
            </main>
        </div>
    </div>
    <!-- Global Confirmation Modal -->
    <div x-data="{ 
            show: false, 
            title: '', 
            message: '', 
            callback: null 
        }" 
        @open-confirm.window="
            title = $event.detail.title;
            message = $event.detail.message;
            callback = $event.detail.callback;
            show = true;
        "
        x-show="show"
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/80 backdrop-blur-sm"
        style="display: none;">

        <div class="bg-slate-800 border border-slate-700 rounded-2xl max-w-sm w-full p-6 text-center shadow-2xl">
            <h2 class="text-xl font-bold text-white mb-2" x-text="title"></h2>
            <p class="text-slate-400 mb-6" x-text="message"></p>
            
            <div class="flex gap-3">
                <button @click="show = false" class="flex-1 py-2 rounded-lg border border-slate-600 text-slate-300 hover:bg-slate-700">Cancel</button>
                <button @click="callback(); show = false;" class="flex-1 py-2 rounded-lg bg-green-600 text-white font-bold hover:bg-green-500">Confirm</button>
            </div>
        </div>
    </div>
    @stack('scripts')
</body>
</html>