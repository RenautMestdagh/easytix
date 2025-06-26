@props([
    'type' => 'button', // 'button', 'submit', 'reset'
    'variant' => 'primary', // 'primary', 'secondary', 'danger', 'purple', 'indigo', 'success'
    'disabled' => false,
    'href' => null // Optional href for link-style buttons
])

@php
    // Base classes
    $baseClasses = 'inline-flex items-center justify-center px-6 py-3 rounded-xl transition-colors duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2';

    // Variant-specific classes
    $variantClasses = [
        'primary' => 'text-white bg-blue-600 dark:bg-blue-500 focus:ring-blue-500',
        'secondary' => 'bg-gray-200 text-gray-700 dark:text-gray-700 focus:ring-gray-400',
        'danger' => 'text-white bg-red-600 dark:bg-red-500 focus:ring-red-500',
        'purple' => 'text-white bg-purple-600 dark:bg-purple-500 focus:ring-purple-500',
        'indigo' => 'text-white bg-indigo-600 dark:bg-indigo-500 focus:ring-indigo-500',
        'success' => 'text-white bg-green-600 dark:bg-green-500 focus:ring-green-500',
    ];

    // Hover classes (only added when not disabled)
    $hoverClasses = [
        'primary' => 'hover:bg-blue-700 dark:hover:bg-blue-600',
        'secondary' => 'hover:bg-gray-300 dark:hover:bg-gray-300',
        'danger' => 'hover:bg-red-700 dark:hover:bg-red-600',
        'purple' => 'hover:bg-purple-700 dark:hover:bg-purple-600',
        'indigo' => 'hover:bg-indigo-700 dark:hover:bg-indigo-600',
        'success' => 'hover:bg-green-700 dark:hover:bg-green-600',
    ];

    // Disabled classes
    $disabledClasses = 'opacity-50';

    // Combine classes
    $classes = $baseClasses . ' ' . ($variantClasses[$variant] ?? $variantClasses['primary']);
    if (!$disabled) {
        $classes .= ' hover:cursor-pointer ' . ($hoverClasses[$variant] ?? $hoverClasses['primary']);
    } else {
        $classes .= ' ' . $disabledClasses;
    }
@endphp

@if($href)
    <a
        href="{{ $href }}"
        {{ $attributes->merge(['class' => $classes]) }}
    >
        {{ $slot }}
    </a>
@else
    <button
        type="{{ $type }}"
        {{ $attributes->merge(['class' => $classes]) }}
        @disabled($disabled)
    >
        {{ $slot }}
    </button>
@endif
