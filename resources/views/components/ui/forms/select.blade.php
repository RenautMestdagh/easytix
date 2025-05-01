@props([
    'id' => null,
    'name' => null,
    'error' => null,
    'class' => null,
])

<select
    {{ $attributes->merge([
        'id' => $id ?? $name,
        'name' => $name,
        'class' => 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white border rounded-lg px-3 py-2 focus:outline-none focus:ring focus:ring-blue-300 ' . ($error ? 'border-red-500 ' : 'border-gray-300 ') . $class
    ]) }}
>
    {{ $slot }}
</select>
