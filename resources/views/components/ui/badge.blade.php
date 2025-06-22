@props([
    'color' => 'gray', // 'red', 'yellow', 'green', 'blue', 'purple', 'indigo', 'gray'
    'text' => null, // Custom text content (optional)
    'class' => '', // Additional classes
    'icon' => null, // Optional icon component
    'size' => 'md', // 'sm', 'md', 'lg'
])

@php
    // Base classes for all badges
    $baseClasses = 'inline-flex items-center font-medium rounded-full';

    // Size classes
    $sizeClasses = [
        'sm' => 'px-2 py-0.5 text-xs',
        'md' => 'px-2.5 py-1 text-sm',
        'lg' => 'px-3 py-1.5 text-base',
    ];

    // Color classes (light background with dark text)
    $colorClasses = [
        'red' => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
        'yellow' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
        'green' => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
        'blue' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
        'purple' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400',
        'indigo' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/20 dark:text-indigo-400',
        'gray' => 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400',
    ];

    // Combine all classes
    $classes = $baseClasses . ' ' .
               ($sizeClasses[$size] ?? $sizeClasses['md']) . ' ' .
               ($colorClasses[$color] ?? $colorClasses['gray']) . ' ' .
               $class;

    $content = $text ?? $slot;
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        <span class="mr-1.5">
            {{ $icon }}
        </span>
    @endif
    {{ $content }}
</span>
