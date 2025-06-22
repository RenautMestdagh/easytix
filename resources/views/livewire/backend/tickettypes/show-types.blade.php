<div class="mx-auto max-w-10xl p-4 sm:p-6 lg:p-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <div class="flex items-center gap-3">
                <div class="flex flex-col gap-2">
                    <div class="flex gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                        </svg>
                        <h1 class="text-xl font-semibold leading-6 text-gray-900 dark:text-white me-2">
                            {{ $event->name }}
                        </h1>
                        @can('events.update')
                            <x-ui.edit-button wire:click="editEvent({{ $event->id }})" title="{{ __('Edit') }}"/>
                        @endcan
                        <a href="{{ route('event.tickets', [$event->organization->subdomain, $event->uniqid]) }}" target="_blank"
                           class="p-1 pt-0 text-blue-600 hover:text-green-900 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors duration-300 ease-in-out"
                           title="{{ __('Show event') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                            </svg>
                        </a>
                    </div>
                    @include('partials.tenant-event.event-meta')
                </div>
            </div>
        </div>
        <x-ui.back-to-button route="events.index" text="{{ __('Back to events') }}"/>
    </div>

    <div class="mt-4 flex justify-between">
        <div class="flex gap-3 h-min">
            <p class="text-sm text-gray-700 dark:text-gray-300">Status:</p>
            @php
                $badge = match(true) {
                    $event->trashed() => ['color' => 'red', 'text' => __('Deleted')],
                    $event->date->isPast() => ['color' => 'gray', 'text' => __('Passed')],
                    $event->is_published => ['color' => 'green', 'text' => __('Published')],
                    !empty($event->publish_at) => ['color' => 'blue', 'text' => __('Publishes ') . $event->publish_at->diffForHumans()],
                    default => ['color' => 'yellow', 'text' => __('Unlisted')]
                };
            @endphp
            <x-ui.badge :color="$badge['color']" :text="$badge['text']" size="sm"/>
        </div>
    </div>

    <!-- Ticket Summary -->
    <div class="mt-6 bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 mb-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('Ticket Summary') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('Total Capacity') }}</p>
                <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ $event->max_capacity ?: '∞' }}
                </p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('Tickets Sold') }}</p>
                <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ $event->tickets->count() }}
                </p>
            </div>
            @php
                $publishedTickets = $event->ticketTypes->filter(fn($type) => $type->is_published);
                $hasUnlimited = $publishedTickets->contains(fn($type) => is_null($type->available_quantity));
                $totalRemaining = $event->ticketTypes->sum(function($ticketType) {
                    return max(0, $ticketType->available_quantity - $ticketType->tickets_count);
                });
                if($hasUnlimited)
                    $totalRemaining = $event->max_capacity - $event->tickets->count();
                $totalRemaining = max(0, $totalRemaining);
            @endphp
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('Tickets Remaining') }}</p>
                <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ !$event->max_capacity&&$hasUnlimited ? '∞' : $totalRemaining }}
                </p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('Total Published Tickets') }}</p>
                <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ $hasUnlimited ? '∞' : $publishedTickets->sum('available_quantity') }}
                </p>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
        <x-ui.flash-message
            :message="session('message')"
            :type="session('message_type', 'success')"
        />
    @endif

    <div class="mt-12 mb-6 flex justify-between">
        <div>
            <div class="flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                </svg>
                <h1 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">{{ __('Ticket Types') }}</h1>
            </div>

            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                {{ __('Manage all ticket types for this event.') }}
            </p>
        </div>

        @can('ticket-types.create')
        <x-ui.button href="{{ route('ticket-types.create', $event) }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('New Ticket Type') }}
        </x-ui.button>
        @endcan
    </div>

    <!-- Ticket Types Table -->
    <div class="overflow-auto shadow-sm sm:rounded-lg bg-white dark:bg-gray-800">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
            <tr>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                    {{ __('Name') }}
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                    {{ __('Price') }}
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                    {{ __('Available/Sold') }}
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                    {{ __('Status') }}
                </th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                    {{ __('Actions') }}
                </th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($event->ticketTypes as $ticketType)
                <tr wire:key="ticket-type-{{ $ticketType->id }}" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition-colors duration-300 ease-in-out">
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $ticketType->name }}</div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                        {{ number_format($ticketType->price_cents / 100, 2) }} €
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <x-ui.usage-bar :progress="$ticketType->tickets_count" :max="$ticketType->available_quantity" />
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        @php
                            $badge = match(true) {
                                $ticketType->is_published => ['color' => 'green', 'text' => __('Published')],
                                $ticketType->publish_with_event => ['color' => 'blue', 'text' => __('Publishes with event')],
                                !empty($ticketType->publish_at) => ['color' => 'purple', 'text' => __('Publishes ') . $ticketType->publish_at->diffForHumans()],
                                default => ['color' => 'yellow', 'text' => __('Draft')]
                            };
                        @endphp
                        <x-ui.badge :color="$badge['color']" :text="$badge['text']" size="sm"/>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end gap-2">
                            @can('ticket-types.update')
                            <x-ui.edit-button
                                route="ticket-types.update"
                                :routeParams="['event' => $event, 'ticketType' => $ticketType]"
                                title="{{ __('Edit') }}"
                            />
                            @endcan
                            @can('ticket-types.delete')
                            <x-ui.delete-button
                                type="delete"
                                method="deleteTicketType"
                                :args="[$ticketType->id]"
                                confirmation="Are you sure you want to delete this ticket type?"
                                title="{{ __('Delete') }}"
                                disabledTitle="Cannot delete - tickets exist"
                                :disabled="$ticketType->tickets_count + $ticketType->reserved_tickets_count != 0"
                            />
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-sm text-gray-500 dark:text-gray-300 text-center">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            {{ __('No ticket types found') }}
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
