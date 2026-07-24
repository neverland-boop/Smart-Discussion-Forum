@props(['user', 'size' => 'md'])

@php
$sizeClasses = match($size) {
    'sm' => 'w-8 h-8 text-sm',
    'md' => 'w-10 h-10 text-base',
    'lg' => 'w-16 h-16 text-lg',
    'xl' => 'w-24 h-24 text-2xl',
    default => 'w-10 h-10 text-base',
};
@endphp

@if($user->avatar)
    <img 
        src="{{ \Storage::disk('public')->url($user->avatar) }}"
        alt="{{ $user->name }}"
        {{ $attributes->merge(['class' => "rounded-full object-cover border border-slate-600 $sizeClasses"]) }}
    />
@else
    <div 
        {{ $attributes->merge(['class' => "rounded-full bg-brand-primary flex items-center justify-center font-bold text-white $sizeClasses"]) }}
    >
        {{ substr($user->name, 0, 1) }}
    </div>
@endif
