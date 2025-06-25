@props(['color' => null])

<div class="flex justify-center items-center rounded-md py-2 {{ $color ? '' : 'text-black dark:text-white' }}"
     @if($color) style="color: {{ $color }}" @endif>
    <div class="flex aspect-square size-8 items-center justify-center">
        <x-app-logo-icon class="p-2"/>
    </div>
    <div class="ms-1 me-2 flex h-8 w-28 items-center justify-center">
        <x-app-name
            start-color="currentColor"
            end-color="currentColor"/>
    </div>
</div>
