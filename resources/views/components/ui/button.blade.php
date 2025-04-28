@props([
    'type' => 'button',
    'variant' => 'primary', // 'primary', 'secondary', 'danger'
    'disabled' => false
])

@php
    $base = 'inline-flex items-center justify-center px-4 py-2 rounded-md font-semibold text-sm transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2';

    $variants = [
        'primary' => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500',
        'secondary' => 'bg-gray-200 text-gray-700 hover:bg-gray-300 focus:ring-gray-400',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
    ];

    $classes = $base . ' ' . ($variants[$variant] ?? $variants['primary']);

    if ($disabled) {
        $classes .= ' opacity-50 cursor-not-allowed';
    }
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => $classes]) }}
    @if ($disabled) disabled @endif
>
    {{ $slot }}
</button>

