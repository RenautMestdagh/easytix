<div class="p-6 event-item"
     data-event-name="{{ strtolower($event->name) }}"
     data-event-description="{{ strtolower($event->description) }}"
     data-event-location="{{ strtolower($event->venue?->name) }}">
    <div class="flex flex-col md:flex-row md:items-start justify-between gap-5">
        <div class="flex flex-col md:flex-row md:items-start space-y-4 md:space-y-0 md:space-x-4 w-full">
            <!-- Event Image -->
            @if($event->event_image)
                <div class="w-full h-40 md:w-30 md:h-30 rounded overflow-hidden flex-shrink-0">
                    <img src="{{ $event->event_image_url }}"
                         alt="{{ $event->name }}"
                         class="w-full h-full object-cover">
                </div>
            @else
                <div class="w-full h-40 md:w-30 md:h-30 rounded bg-gradient-to-br from-gray-200 to-gray-300 flex-shrink-0">
                </div>
            @endif

            <!-- Event Details -->
            <div class="flex flex-col h-auto md:h-30 w-full">
                <div class="flex-1">
                    <h4 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-2">{{ $event->name }}</h4>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">{{ Str::limit($event->description, 100) }}</p>
                </div>

                @include('partials.tenant-event.event-meta')
            </div>
        </div>

        <!-- Buy Tickets Button - moves to bottom on mobile -->
        <div class="flex-shrink-0 md:ml-4 md:self-center w-full md:w-auto mt-4 md:mt-0">
            @if($event->publishedTicketTypes->count() > 0)
                <a
                    href="{{ $event->ticket_url }}"
                    target="_blank"
                    class="inline-flex justify-center items-center px-6 py-3 md:py-2 w-full md:w-auto bg-black dark:bg-white text-white dark:text-black text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-200 transition-colors duration-300 ease-in-out"
                >
                    Buy Tickets
                </a>
            @else
                <span class="inline-flex justify-center items-center px-6 py-3 md:py-2 w-full md:w-auto bg-black dark:bg-white text-white dark:text-black text-sm font-medium rounded-lg opacity-50 hover:cursor-default">
                No Tickets
            </span>
            @endif
        </div>
    </div>
</div>
