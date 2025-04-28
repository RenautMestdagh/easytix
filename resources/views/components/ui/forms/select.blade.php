@props([
    'id' => null,
    'name' => null,
    'error' => null,
])

<select
    {{ $attributes->merge([
        'id' => $id ?? $name,
        'name' => $name,
        'class' => 'w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-300 ' . ($error ? 'border-red-500' : 'border-gray-300')
    ]) }}
>
    {{ $slot }}
</select>
