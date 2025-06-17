<div class="space-y-4" id="eventsList">
    @forelse($events->groupBy(function($event) { return $event->date->format('Y-m-d'); }) as $date => $dayEvents)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden event-day-card">
            <!-- Date Header -->
            <div class="bg-gray-100 dark:bg-gray-800 px-6 py-3 border-b dark:border-gray-700">
                <h3 class="font-semibold text-gray-800 dark:text-gray-200">
                    {{ $dayEvents->first()->date->format('d M Y') }}
                </h3>
            </div>

            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($dayEvents as $event)
                    @include('partials.tenant-home.event-item', ['event' => $event])
                @endforeach
            </div>
        </div>
    @empty
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8 text-center">
            <div class="text-gray-400 mb-4">
                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-800 mb-2">No Events Available</h3>
            <p class="text-gray-600">There are currently no published events available for this organization.</p>
        </div>
    @endforelse
</div>
