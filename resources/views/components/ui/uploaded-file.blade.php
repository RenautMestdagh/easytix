@props([
    'file' => [],
    'dbField' => null,
    'tmpFilename' => null,
    'class' => '',
])
@php
    $isImageMime = function ($mime) {
        return in_array($mime, ['png', 'gif', 'bmp', 'svg', 'jpeg', 'jpg']);
    }
@endphp
@if(is_array($file))
<div {{ $attributes->merge(['class' => "flex items-center gap-4 border border-gray-200 dark:border-gray-700 rounded-md p-1 w-fit {$class}"]) }}>
    @if($isImageMime($file['extension'] ?? ''))
        <div class="flex-shrink-0">
            <img
                class="h-12 w-12 rounded-md object-cover"
                @if(array_key_exists('temporaryUrl', $file))
                    src="{{ $file['temporaryUrl'] }}"
                @elseif(array_key_exists('url', $file))
                    src="{{ $file['url'] }}"
                @endif
                @if(array_key_exists('name', $file))
                    alt="{{ $file['name'] }}"
                @endif
            >
        </div>
    @else
        <div class="flex justify-center items-center w-14 h-14 bg-gray-100 dark:bg-gray-700 rounded">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-8 h-8 text-gray-500">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
            </svg>
        </div>
    @endif
    <div class="min-w-0">
        <p class="text-sm font-medium text-gray-900 dark:text-gray-200 truncate">
            {{ $file['name'] }}
        </p>
        <p class="text-xs text-gray-500 dark:text-gray-400">
            {{ \Illuminate\Support\Number::fileSize($file['size']) }}
        </p>
    </div>
    <x-ui.cross-button
        wire:click="markFileRemoved('{{ $dbField ?? ($file['dbField'] ?? null) }}')"
        @click="removeUpload('{{ $tmpFilename ?? ($file['tmpFilename'] ?? null) }}')"
    />
</div>
@endif
