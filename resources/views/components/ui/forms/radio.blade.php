@props([
    'id' => null,
    'name' => null,
    'value',
])

<label class="inline-flex items-center space-x-2">
    <input
        type="radio"
        value="{{ $value }}"
        {{ $attributes->merge([
            'id' => $id ?? $name,
            'name' => $name,
            'class' => 'text-blue-600 border-gray-300 focus:ring-blue-500'
        ]) }}
    >
    <span>{{ $slot }}</span>
</label>
