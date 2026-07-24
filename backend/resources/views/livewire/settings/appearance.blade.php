<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component {
    public function mount()
    {
        // Initialize theme from localStorage on page load
        $this->dispatch('init-theme');
    }
}; ?>

<div class="flex flex-col items-start" x-data="{
    theme: localStorage.getItem('smart-discussion-theme') || 'green'
}" x-init="
    document.documentElement.dataset.theme = theme;
    $watch('theme', value => {
        document.documentElement.dataset.theme = value;
        localStorage.setItem('smart-discussion-theme', value);
    });
">
    @include('partials.settings-heading')

    <x-settings.layout heading="Appearance" subheading="Update your account's appearance settings">
        <div class="flex flex-col gap-6">
            <div>
                <p class="mb-2 text-sm font-medium text-slate-200">Mode</p>
                <flux:radio.group x-data variant="segmented" x-model="$flux.appearance" class="bg-slate-700">
                    <flux:radio value="light" icon="sun">Light</flux:radio>
                    <flux:radio value="dark" icon="moon">Dark</flux:radio>
                    <flux:radio value="system" icon="computer-desktop">System</flux:radio>
                </flux:radio.group>
            </div>

            <div>
                <p class="mb-2 text-sm font-medium text-slate-200">Theme Color</p>
                <flux:radio.group variant="segmented" x-model="theme" class="bg-slate-700">
                    <flux:radio value="green">Green</flux:radio>
                    <flux:radio value="emerald">Emerald</flux:radio>
                    <flux:radio value="teal">Teal</flux:radio>
                </flux:radio.group>
            </div>
        </div>
    </x-settings.layout>
</div>
