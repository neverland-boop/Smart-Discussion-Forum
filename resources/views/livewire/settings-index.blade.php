<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app')] class extends Component {}; ?>

<div class="flex flex-col gap-6">
    <!-- Section Header Header -->
    <div>
        <h1 class="text-2xl font-bold text-zinc-800 dark:text-zinc-100 tracking-tight">Console Settings</h1>
        <p class="text-xs font-semibold text-zinc-400 mt-1">Configure moderation policies, display interfaces, and gateway parameters</p>
    </div>

    <!-- Main Administrative Configuration Stack -->
    <div class="p-6 bg-zinc-50 dark:bg-zinc-950/40 border border-zinc-200 dark:border-zinc-800 rounded-2xl flex flex-col gap-4">
        
        <!-- THEME CONTROLLER ROW BLOCK (Alpine.js State Driven) -->
        <div x-data="{ 
            darkMode: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
            toggleTheme() {
                this.darkMode = !this.darkMode;
                if (this.darkMode) {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                }
            }
        }" 
        class="flex items-center justify-between p-4 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-150 dark:border-zinc-800 shadow-sm">
            <div class="flex flex-col gap-0.5">
                <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200">Interface Display Theme</span>
                <span class="text-[11px] text-zinc-400 font-medium">Switch the platform workspace canvas appearance context</span>
            </div>
            
            <!-- Switch Toggle Controls -->
            <button @click="toggleTheme()" type="button" class="flex items-center gap-2 px-3 py-1.5 bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-xs font-bold rounded-lg text-zinc-700 dark:text-zinc-200 cursor-pointer shadow-sm transition-all">
                <!-- Light Mode Icon (Visible when dark is active) -->
                <span x-show="darkMode" class="flex items-center gap-1.5">☀️ <span>Switch to Light Mode</span></span>
                <!-- Dark Mode Icon (Visible when light is active) -->
                <span x-show="!darkMode" class="flex items-center gap-1.5">🌙 <span>Switch to Dark Mode</span></span>
            </button>
        </div>

        <!-- Security Multi-Factor Item -->
        <div class="flex items-center justify-between p-4 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-150 dark:border-zinc-800 shadow-sm">
            <div class="flex flex-col gap-0.5">
                <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200">Enforce Multi-Factor Authentication (MFA) for Lecturers</span>
                <span class="text-[11px] text-zinc-400 font-medium">Require identity verification security steps upon login paths</span>
            </div>
            <span class="text-[10px] bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 font-black px-2 py-1 rounded border border-emerald-200 dark:border-emerald-900/50 uppercase tracking-wide">
                Enabled
            </span>
        </div>

        <!-- Automated Moderation Filter -->
        <div class="flex items-center justify-between p-4 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-150 dark:border-zinc-800 shadow-sm">
            <div class="flex flex-col gap-0.5">
                <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200">Automatic Moderation AI Filter</span>
                <span class="text-[11px] text-zinc-400 font-medium">Scan discussion text buffers against platform code bounds</span>
            </div>
            <span class="text-[10px] bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 font-black px-2 py-1 rounded border border-zinc-200 dark:border-zinc-700 uppercase tracking-wide">
                Disabled
            </span>
        </div>

    </div>
</div>
