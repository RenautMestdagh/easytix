<div class="p-6 event-item"
     data-event-name="{{ strtolower($event->name) }}"
     data-event-description="{{ strtolower($event->description) }}"
     data-event-location="{{ strtolower($event->venue?->name) }}">
    <div class="flex items-start justify-between gap-5">
        <div class="flex items-start space-x-4">
            <!-- Event Image -->
            @if($event->event_image)
                <div class="w-30 h-30 rounded overflow-hidden flex-shrink-0">
                    <img src="{{ $event->event_image_url }}"
                         alt="{{ $event->name }}"
                         class="w-full h-full object-cover">
                </div>
            @else
                <div class="w-30 h-30 rounded bg-gradient-to-br from-gray-200 to-gray-300 flex-shrink-0">
                </div>
            @endif

            <!-- Event Details -->
            <div class="flex flex-col h-30">
                <div class="flex-1">
                    <h4 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-2">{{ $event->name }}</h4>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">{{ Str::limit($event->description, 100) }}</p>
                </div>

                @include('partials.tenant-event.event-meta')
            </div>
        </div>

        <!-- Buy Tickets Button -->
        <div class="flex-shrink-0 ml-4 self-center">
            <a href="{{ route('event.tickets', [$organization->subdomain, $event->uniqid]) }}"
               target="_blank"
               class="inline-flex items-center px-6 py-2 bg-black dark:bg-white text-white dark:text-black text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-200 transition-all duration-300 ease-in-out">
                Buy Tickets
            </a>
        </div>
    </div>
</div>
