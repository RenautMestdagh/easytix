<x-layouts.organization :organization="$organization">

    <!-- Event Agenda Section -->
    <div class="max-w-4xl mx-auto relative z-10">
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Event Agenda</h2>
            <p class="text-gray-600 mb-6">Please choose one of the following events to buy your tickets</p>

            <!-- Search Bar -->
            <div class="relative mb-6">
                <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                    <input
                        type="text"
                        placeholder="Search for event"
                        class="flex-1 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 text-black"
                        id="eventSearch"
                    >
                    <button class="px-3 py-2 bg-gray-50 border-l border-gray-300 hover:bg-gray-100">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Events List -->
        @include('partials.events-list', ['events' => $events])
    </div>

    @push('scripts')
        <!-- Search Functionality -->
        <script>
            document.getElementById('eventSearch').addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const eventItems = document.querySelectorAll('.event-item');
                const dayCards = document.querySelectorAll('.event-day-card');

                // First hide all events
                eventItems.forEach(item => {
                    const eventName = item.getAttribute('data-event-name');
                    const eventDescription = item.getAttribute('data-event-description');
                    const eventLocation = item.getAttribute('data-event-location');

                    if (eventName.includes(searchTerm) || eventLocation.includes(searchTerm) || eventDescription.includes(searchTerm)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });

                // Then hide empty day cards
                dayCards.forEach(card => {
                    const visibleEvents = card.querySelectorAll('.event-item[style="display: block;"]').length;
                    const allEvents = card.querySelectorAll('.event-item').length;

                    if (visibleEvents === 0 && allEvents > 0) {
                        card.style.display = 'none';
                    } else {
                        card.style.display = 'block';
                    }
                });
            });
        </script>
    @endpush
</x-layouts.organization>
