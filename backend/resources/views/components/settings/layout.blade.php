<div class="flex items-start max-md:flex-col bg-slate-800 rounded-xl border border-slate-700 shadow-lg p-6">
    <div class="mr-10 w-full pb-4 md:w-[220px]">
        <flux:navlist class="bg-slate-800">
            <flux:navlist.item href="{{ route('settings.profile') }}" wire:navigate class="text-slate-200 hover:text-white hover:bg-slate-700 [&.active]:bg-brand-primary [&.active]:text-white">Profile</flux:navlist.item>
            <flux:navlist.item href="{{ route('settings.password') }}" wire:navigate class="text-slate-200 hover:text-white hover:bg-slate-700 [&.active]:bg-brand-primary [&.active]:text-white">Password</flux:navlist.item>
            <flux:navlist.item href="{{ route('settings.appearance') }}" wire:navigate class="text-slate-200 hover:text-white hover:bg-slate-700 [&.active]:bg-brand-primary [&.active]:text-white">Appearance</flux:navlist.item>
        </flux:navlist>
    </div>

    <flux:separator class="md:hidden" />

    <div class="flex-1 self-stretch max-md:pt-6">
        <h2 class="text-xl font-bold text-white mb-2">{{ $heading ?? '' }}</h2>
        <p class="text-slate-300 text-sm mb-5">{{ $subheading ?? '' }}</p>

        <div class="mt-5 w-full max-w-lg">
            {{ $slot }}
        </div>
    </div>
</div>
