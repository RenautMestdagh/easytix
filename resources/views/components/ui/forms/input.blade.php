@props([
    'type' => 'text',
    'id' => null,
    'name' => null,
    'error' => null,
])

<input
    {{ $attributes->merge([
        'type' => $type,
        'id' => $id ?? $name,
        'name' => $name,
        'class' => 'w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-300 ' . ($error ? 'border-red-500' : 'border-gray-300')
    ]) }}
>
