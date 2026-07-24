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

        <!-- Theme initialization script -->
        <script>
            (function() {
                const theme = localStorage.getItem('smart-discussion-theme') || 'green';
                document.documentElement.dataset.theme = theme;
            })();
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
<body class="font-sans antialiased text-slate-50" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen bg-slate-950 overflow-hidden">
        
        <!-- Sidebar -->
        @if(!request()->routeIs('quiz.attempt'))
            <!-- Mobile backdrop -->
            <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
                 x-transition:enter="transition-opacity ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/60 z-40 lg:hidden"></div>

            <!-- Sidebar -->
            <aside
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                class="fixed inset-y-0 left-0 z-50 w-64 h-full bg-brand-primary border-r border-brand-primary flex flex-col justify-between shrink-0 shadow-2xl transform transition-transform duration-200 ease-out lg:static lg:translate-x-0 lg:shadow-none">
                <div>
                    <div class="h-16 flex items-center justify-between px-6 border-b border-slate-700">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group outline-none">
                        <!-- Chat Bubble Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-8 h-8 text-brand-soft group-hover:text-white transition-colors">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                        </svg>
                        <span class="text-white font-bold text-lg tracking-wider group-hover:text-brand-soft transition-colors">SMART DISCUSSION</span>
                        </a>
                        <!-- Mobile close button -->
                        <button @click="sidebarOpen = false" type="button" aria-label="Close menu" class="lg:hidden text-brand-soft hover:text-white p-1 -mr-2 rounded-md hover:bg-brand-primary-hover transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <nav class="p-4 space-y-2 mt-4">
                        
                        <!-- Dashboard -->
                        <a href="{{ route('dashboard') }}" @click="sidebarOpen = false" class="w-full flex items-center px-4 py-3 rounded-md transition-colors {{ request()->routeIs('dashboard') ? 'bg-brand-dark text-white font-semibold shadow-inner' : 'text-brand-soft hover:bg-brand-primary-hover hover:text-white' }}">Dashboard</a>
                        
                        <!-- Student Links -->
                        @if(auth()->user()->hasAnyRole(['student']))
                            <a href="{{ route('forums') }}" @click="sidebarOpen = false" class="w-full flex items-center px-4 py-3 rounded-md transition-colors {{ request()->routeIs('forums') ? 'bg-brand-dark text-white font-semibold shadow-inner' : 'text-brand-soft hover:bg-brand-primary-hover hover:text-white' }}">Discussions</a>
                            <a href="{{ route('quizzes') }}" @click="sidebarOpen = false" class="w-full flex items-center px-4 py-3 rounded-md transition-colors {{ request()->routeIs('quizzes') ? 'bg-brand-dark text-white font-semibold shadow-inner' : 'text-brand-soft hover:bg-brand-primary-hover hover:text-white' }}">Quizzes</a>
                        @endif
                        
                        <!-- Lecturer Links -->
                        @if(auth()->user()->hasAnyRole(['lecturer']))
                            <a href="{{ route('lecturer.students') }}" @click="sidebarOpen = false" class="w-full flex items-center px-4 py-3 rounded-md transition-colors {{ request()->routeIs('lecturer.students') ? 'bg-brand-dark text-white font-semibold shadow-inner' : 'text-brand-soft hover:bg-brand-primary-hover hover:text-white' }}">Students</a>
                            <a href="{{ route('lecturer.grades') }}" @click="sidebarOpen = false" class="w-full flex items-center px-4 py-3 rounded-md transition-colors {{ request()->routeIs('lecturer.grades') ? 'bg-brand-dark text-white font-semibold shadow-inner' : 'text-brand-soft hover:bg-brand-primary-hover hover:text-white' }}">Grades</a>
                            <a href="{{route('lecturer.quizzes')}}" @click="sidebarOpen = false" class="w-full flex items-center px-4 py-3 rounded-md transition-colors {{ request()->routeIs('lecturer.quiz-control') ? 'bg-brand-dark text-white font-semibold shadow-inner' : 'text-brand-soft hover:bg-brand-primary-hover hover:text-white' }}">Quiz Control</a>
                              @endif
                        
                        <!-- Admin Links -->
                        @if(auth()->user()->hasAnyRole(['admin']))
                            <a href="{{ route('admin.members') }}" @click="sidebarOpen = false" class="w-full flex items-center px-4 py-3 rounded-md transition-colors {{ request()->routeIs('admin.members') ? 'bg-brand-dark text-white font-semibold shadow-inner' : 'text-brand-soft hover:bg-brand-primary-hover hover:text-white' }}">Members</a>
                            <a href="{{ route('admin.settings') }}" @click="sidebarOpen = false" class="w-full flex items-center px-4 py-3 rounded-md transition-colors {{ request()->routeIs('admin.settings') ? 'bg-brand-dark text-white font-semibold shadow-inner' : 'text-brand-soft hover:bg-brand-primary-hover hover:text-white' }}">Settings</a>
                        @endif
                        
                        <!-- Shared Links -->
                        @if(auth()->user()->hasAnyRole(['lecturer', 'admin']))
                            <a href="{{route('reports')}}" @click="sidebarOpen = false" class="w-full flex items-center px-4 py-3 rounded-md transition-colors {{ request()->routeIs('reports.*') ? 'bg-brand-dark text-white font-semibold shadow-inner' : 'text-brand-soft hover:bg-brand-primary-hover hover:text-white' }}">Reports</a>
                        @endif  

                    </nav>
                </div>

                <!-- Profile Dropdown -->
                <div class="p-6 border-t border-slate-700">
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-3 w-full hover:opacity-80 transition">
                            <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center font-bold text-brand-primary shrink-0 shadow">{{ substr(Auth::user()->name, 0, 1) }}</div>
                            <div class="flex flex-col text-left min-w-0">
                                <span class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</span>
                                <span class="text-xs text-brand-soft truncate">Settings & Profile</span>
                            </div>
                        </button>
                        <div x-show="open" x-cloak @click.away="open = false" 
                             x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute bottom-16 left-0 w-full bg-slate-900 border border-slate-700 rounded-lg p-2 z-50 shadow-xl">
                            <a href="{{ route('settings.profile') }}" class="block px-4 py-2 text-sm text-brand-soft hover:bg-brand-primary hover:text-white rounded transition">Settings</a>
                            <div class="border-t border-brand-strong my-2"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-300 hover:bg-red-600 hover:text-white rounded transition">Logout</button>
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
                <header class="h-16 flex items-center gap-3 px-4 border-b border-slate-700 bg-slate-900 shrink-0 lg:hidden">
                    <button @click="sidebarOpen = true" type="button" aria-label="Open menu" class="p-2 -ml-2 rounded-md text-brand-soft hover:text-white hover:bg-brand-primary-hover transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6 text-brand-soft">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                        </svg>
                        <span class="text-white font-bold tracking-wider text-sm">SMART DISCUSSION</span>
                    </a>
                </header>
            @endif
            <main class="flex-1 overflow-y-auto bg-slate-950">
                {{ $slot ?? '' }}
                @yield('content')
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
        x-show="show" x-cloak
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/80 backdrop-blur-sm">

        <div class="bg-slate-800 border border-slate-700 rounded-2xl max-w-sm w-full p-6 text-center shadow-2xl">
            <h2 class="text-xl font-bold text-white mb-2" x-text="title"></h2>
            <p class="text-slate-400 mb-6" x-text="message"></p>
            
            <div class="flex gap-3">
                <button @click="show = false" class="flex-1 py-2 rounded-lg border border-slate-600 text-slate-300 hover:bg-slate-700 transition">Cancel</button>
                <button @click="callback(); show = false;" class="flex-1 py-2 rounded-lg bg-brand-primary text-white font-bold hover:bg-brand-primary-hover transition">Confirm</button>
            </div>
        </div>
    </div>
    @stack('scripts')
</body>
</html>