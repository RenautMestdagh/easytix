@props(['for'])

<label
    for="{{ $for }}"
    class="block mb-1 font-medium text-gray-700 dark:text-gray-300"
>
    {{ $slot }}
</label>
