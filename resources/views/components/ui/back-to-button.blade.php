@props([
    'route' => null,
    'routeParams' => [],
    'text' => __('Back'),
    'icon' => true,
    'referrerCheck' => false,
])

<a href="{{ route($route, $routeParams) }}" wire:navigate class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 flex items-center transition-colors duration-300 ease-in-out">
    @if($icon)
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
    @endif
    {{ $text }}
</a>
