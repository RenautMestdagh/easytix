@props([
    'variant' => 'danger', // 'danger', 'primary', 'secondary', etc.
    'disabled' => false,
    'title' => 'Close',
    'disabledTitle' => 'Cannot close',
    'size' => 'md', // 'sm', 'md', 'lg'
    'wireClick' => null, // Livewire click handler
    'onClick' => null, // JavaScript click handler
])

@php
    // Base classes
    $baseClasses = 'inline-flex items-center justify-center rounded-full transition-all duration-300 ease-in-out focus:outline-none focus:ring-1';

    // Size classes
    $sizeClasses = [
        'sm' => 'h-6 w-6',
        'md' => 'h-8 w-8',
        'lg' => 'h-10 w-10',
    ];

    // Variant classes
    $variantClasses = [
        'danger' => 'text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-gray-700 focus:ring-red-300',
        'primary' => 'text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-gray-700 focus:ring-blue-300',
        'secondary' => 'text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:ring-gray-300',
    ];

    // Disabled classes
    $disabledClasses = 'opacity-50 cursor-not-allowed';

    // Combine classes
    $classes = $baseClasses . ' ' . ($sizeClasses[$size] ?? $sizeClasses['md']) . ' ' . ($variantClasses[$variant] ?? $variantClasses['danger']);

    if ($disabled) {
        $classes .= ' ' . $disabledClasses;
    }
@endphp

<button
    type="button"
    @if($wireClick) wire:click.prevent="{{ $wireClick }}" @endif
    @if($onClick) onclick="{{ $onClick }}" @endif
    @if($disabled) disabled @endif
    title="{{ $disabled ? $disabledTitle : $title }}"
    {{ $attributes->merge(['class' => $classes]) }}
>
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
    </svg>
</button>
