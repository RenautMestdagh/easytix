<div class="flex items-center space-x-4 text-sm text-gray-600 dark:text-gray-400">
    <div class="flex items-center text-nowrap">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        {{ $event->date->format('d M Y') }}
    </div>
    <div class="flex items-center">
        <svg class="w-4 h-4 mr-1 text-nowrap" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        {{ $event->date->format('H:i') }}
    </div>
    @if($event->venue)
        <div class="flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            @if(!empty($event->venue->coordinates))
                <a href="{{ $event->venue->getGoogleMapsUrl() }}"
                   target="_blank"
                   class="hover:underline transition-all duration-300 ease-in-out"
                   title="{{ __('View on Google Maps') }}"
                >
                    {{ Str::limit($event->venue->name, 50, '...') }}
                </a>
            @else
                {{ Str::limit($event->venue->name, 50, '...') }}
            @endif
        </div>
    @endif
</div>
