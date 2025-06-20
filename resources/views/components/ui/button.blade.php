@props([
    'type' => 'button', // 'button', 'submit', 'reset'
    'variant' => 'primary', // 'primary', 'secondary', 'danger', 'purple', 'indigo'
    'disabled' => false,
    'href' => null // Optional href for link-style buttons
])

@php
    $base = 'inline-flex items-center justify-center px-6 py-3 rounded-xl transition-all duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 hover:cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed';

    $variants = [
        'primary' => 'text-white bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
        'secondary' => 'bg-gray-200 text-gray-700 dark:text-gray-700 hover:bg-gray-300 focus:ring-gray-400',
        'danger' => 'text-white bg-red-600 hover:bg-red-700 focus:ring-red-500',
        'purple' => 'text-white bg-purple-600 hover:bg-purple-700 focus:ring-purple-500',
        'indigo' => 'text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500',
    ];

    $classes = $base . ' ' . ($variants[$variant] ?? $variants['primary']);

    if ($disabled) {
        $classes .= ' opacity-50 cursor-not-allowed';
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
        @if ($disabled) disabled @endif
    >
        {{ $slot }}
    </button>
@endif
