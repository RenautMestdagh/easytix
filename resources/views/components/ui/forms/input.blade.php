@props([
    'type' => 'text',
    'id' => null,
    'name' => null,
    'error' => null,
    'disabled' => false,
    'autocomplete' => 'off', // Add autocomplete prop with default 'off'
])

@php
    $baseClasses = 'w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-300';
    $errorClasses = $error ? 'border-red-500' : 'border-gray-300';
    $disabledClasses = $disabled ? 'opacity-50 pointer-events-none' : '';
@endphp

<input
    {{ $attributes->merge([
        'type' => $type,
        'id' => $id ?? $name,
        'name' => $name,
        'class' => "$baseClasses $errorClasses $disabledClasses",
        'autocomplete' => $autocomplete, // Include autocomplete in merge
    ])->when($disabled, fn($attr) => $attr->merge(['disabled' => 'disabled'])) }}
>
