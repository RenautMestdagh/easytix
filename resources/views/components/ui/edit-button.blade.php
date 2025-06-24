@props([
    'href' => null, // Optional href for link-style buttons
    'route' => null, // Optional route name (alternative to href)
    'routeParams' => [], // Parameters for route generation
    'disabled' => false,
    'title' => 'Edit',
    'disabledTitle' => 'Cannot edit',
    'size' => 'md', // 'sm', 'md', 'lg'
    'iconOnly' => true, // Whether to show only the icon
])

@php
    // Base classes
    $baseClasses = 'inline-flex items-center justify-center rounded-full transition-colors duration-300 ease-in-out focus:outline-none';

    // Size classes
    $sizeClasses = [
        'sm' => 'p-1',
        'md' => 'p-1.5',
        'lg' => 'p-2',
    ];

    // Color classes
    $colorClasses = 'text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 hover:bg-gray-100 dark:hover:bg-gray-700';

    // Disabled classes
    $disabledClasses = 'opacity-50';

    // Icon size classes
    $iconSizeClasses = [
        'sm' => 'size-4',
        'md' => 'size-5',
        'lg' => 'size-6',
    ];

    // Combine classes
    $classes = $baseClasses . ' ' . ($sizeClasses[$size] ?? $sizeClasses['md']);
    if (!$disabled) {
        $classes .= ' hover:cursor-pointer ' . $colorClasses;
    } else {
        $classes .= ' ' . $disabledClasses;
    }

    // Generate href if route is provided
    if ($route && !$href) {
        $href = route($route, $routeParams);
    }

    // Determine the title
    $buttonTitle = $disabled ? $disabledTitle : $title;
@endphp

@if($href && !$disabled)
    <a
        href="{{ $href }}"
        wire:navigate
        {{ $attributes->merge(['class' => $classes]) }}
        title="{{ $buttonTitle }}"
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="{{ $iconSizeClasses[$size] ?? $iconSizeClasses['md'] }}">
            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
        </svg>
        @if(!$iconOnly)
            <span class="ml-1">{{ $title }}</span>
        @endif
    </a>
@else
    <button
        type="button"
        wire:navigate
        @disabled($disabled)
        {{ $attributes->merge(['class' => $classes]) }}
        title="{{ $buttonTitle }}"
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="{{ $iconSizeClasses[$size] ?? $iconSizeClasses['md'] }}">
            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
        </svg>
        @if(!$iconOnly)
            <span class="ml-1">{{ $title }}</span>
        @endif
    </button>
@endif
