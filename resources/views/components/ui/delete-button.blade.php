@props([
    'type' => 'delete', // 'delete' or 'forcedelete'
    'confirmation' => 'Are you sure you want to delete this item?',
    'method' => 'deleteItem', // Livewire method to call
    'args' => [], // Array of arguments to pass to the method
    'disabled' => false,
    'title' => 'Delete',
    'disabledTitle' => 'Cannot delete',
])

@php
    // Base classes
    $baseClasses = 'p-1 rounded transition-colors inline-flex items-center justify-center transition-colors duration-300 ease-in-out';

    // Variant-specific classes (with hover effects)
    $variantClasses = [
        'delete' => 'text-red-600 dark:text-red-400',
        'forcedelete' => 'bg-red-600 dark:bg-red-500 text-white',
    ];

    // Hover classes (only added when not disabled)
    $hoverClasses = [
        'delete' => 'hover:text-red-900 dark:hover:text-red-300 hover:bg-gray-100 dark:hover:bg-gray-700',
        'forcedelete' => 'hover:bg-red-700 dark:hover:bg-red-600',
    ];

    // Disabled classes
    $disabledClasses = 'opacity-50';

    // Combine classes
    $classes = $baseClasses . ' ' . ($variantClasses[$type] ?? $variantClasses['delete']);
    if (!$disabled) {
        $classes .= ' hover:cursor-pointer ' . ($hoverClasses[$type] ?? $hoverClasses['delete']);
    } else {
        $classes .= ' ' . $disabledClasses;
    }

    // Prepare arguments string for @this.call
    $argsString = implode(', ', array_map(function($arg) {
        return is_string($arg) ? "'" . addslashes($arg) . "'" : $arg;
    }, $args));
@endphp

<button
    type="button"
    @if($disabled)
        title="{{ $disabledTitle }}"
    @else
        onclick="confirm('{{ $confirmation }}') && @this.call('{{ $method }}', {{ $argsString }})"
    title="{{ $title }}"
    @endif
    @disabled($disabled)
    {{ $attributes->merge(['class' => $classes]) }}
>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
    </svg>
</button>
