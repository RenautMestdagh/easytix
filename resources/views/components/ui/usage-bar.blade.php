@props([
    'progress' => 0, // Current progress value (number of tickets sold)
    'max' => null, // Maximum capacity (null means unlimited)
    'class' => '', // Additional classes
    'size' => 'md', // 'sm', 'md', 'lg' - affects text size and bar height
])

@php
    // Base classes
    $baseClasses = 'flex items-center';

    // Size classes
    $sizeClasses = [
        'sm' => 'text-xs h-1.5',
        'md' => 'text-xs h-2',
        'lg' => 'text-base h-3',
    ];

    // Calculate percentage if max is set
    $percentage = $max ? $progress / $max * 100 : 100;
    $limitedPercentage = min(100, $percentage);

    // Determine color based on percentage
    $color = match(true) {
        !$max && !$progress => 'gray',
        !$max => 'green',
        ($percentage > 100) => 'dark-red',
        ($percentage >= 90) => 'red',
        ($percentage >= 50) => 'yellow',
        default => 'green',
    };

    // Color classes for the progress bar
    $colorClasses = [
        'dark-red' => 'bg-red-900',
        'red' => 'bg-red-500',
        'yellow' => 'bg-yellow-500',
        'green' => 'bg-green-500',
        'gray' => 'bg-gray-200 dark:bg-gray-600',
    ];

    $classes = $baseClasses . ' ' . ($sizeClasses[$size] ?? $sizeClasses['md']) . ' ' . $class;
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    <div class="w-20 mr-2 h-full">
        <div class="h-full rounded-full bg-gray-200 dark:bg-gray-600">
            <div
                class="h-full rounded-full {{ $colorClasses[$color] }}"
                style="width: {{ $limitedPercentage }}%"
            ></div>
        </div>
    </div>
    <span class="@if($percentage<=100) text-gray-600 dark:text-gray-300 @else text-red-600 dark:text-red-400 @endif">
        {{ number_format($progress, 0, ',', '.') }} <strong>/</strong> {{ $max !== null ? number_format($max, 0, ',', '.') : '∞' }}
    </span>
</div>
