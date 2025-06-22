@php use App\Models\Venue; @endphp
<div class="mx-auto max-w-10xl p-4 sm:p-6 lg:p-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <div class="flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
                </svg>
                <h1 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">{{ __('Events') }}</h1>
            </div>
            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                {{ __('Manage all events.') }}
            </p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
            @can('events.create')
                <x-ui.button href="{{ route('events.create') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('New Event') }}
                </x-ui.button>
            @endcan
        </div>
    </div>

    <!-- Filters -->
    <div class="my-4 flex items-center gap-6">
        <!-- Search -->
        <div class="relative w-1/3">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 103 10.5a7.5 7.5 0 0013.15 6.15z"/>
                </svg>
            </div>
            <input type="text"
                   wire:model.live.debounce.150ms="search"
                   placeholder="{{ __('Search events...') }}"
                   class="pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 p-2 text-sm w-full"
            />
        </div>

        <!-- Date range filters -->
        <div class="flex items-center gap-2">
            <x-ui.forms.input
                type="date"
                wire:model.live="startDate"
                class="text-sm"
            />
            <span class="text-gray-500 dark:text-gray-400">to</span>
            <x-ui.forms.input
                type="date"
                wire:model.live="endDate"
                class="text-sm"
            />
        </div>

        <!-- Status filter -->
        <x-ui.forms.select wire:model.live="statusFilter">
            <option value="all">All Statuses</option>
            <option value="published">Published</option>
            <option value="scheduled">Scheduled</option>
            <option value="draft">Draft</option>
        </x-ui.forms.select>

        <!-- Venue filter -->
        <div class="flex gap-4">
            <div class="flex-1 flex flex-col justify-center">
                @if($venueFilter && $venue = Venue::find($venueFilter))
                    <div class="flex justify-between">
                        <div class="flex flex-col items-start">
                            <p>
                                {{ Str::limit($venue->name, 15, '...') }}
                            </p>
                        </div>
                        <x-ui.cross-button wire:click="$set('venueFilter', null)" />
                    </div>
                @else
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ __('All Venues') }}
                    </p>
                @endif
            </div>

            @livewire('modals.venue-picker-modal', [
                'selectedVenueId' => $venueFilter,
                'showTriggerButton' => true,
                'key' => 'venue-filter-picker-'.$venueFilter
            ])
        </div>

        <!-- Include deleted -->
        <label class="flex items-center ml-auto">
            <input type="checkbox" wire:model.live="includeDeleted" class="form-checkbox text-indigo-600">
            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('Include deleted') }}</span>
        </label>
    </div>

    @if (session()->has('message'))
        <x-ui.flash-message
            :message="session('message')"
            :type="session('message_type', 'success')"
        />
    @endif

    <!-- Events Table -->
    <div class="overflow-auto shadow-sm sm:rounded-lg bg-white dark:bg-gray-800">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
            <tr>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                    {{ __('Image') }}
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-300 ease-in-out" wire:click="sortBy('name')">
                    <div class="flex items-center">
                        {{ __('Name') }}
                        <span class="ml-1 text-xs" style="visibility: {{ $sortField == 'name' ? 'visible' : 'hidden' }};">
                            {{ $sortDirection == 'asc' ? '↑' : '↓' }}
                        </span>
                    </div>
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-300 ease-in-out" wire:click="sortBy('date')">
                    <div class="flex items-center">
                        {{ __('Date') }}
                        <span class="ml-1 text-xs" style="visibility: {{ $sortField == 'date' ? 'visible' : 'hidden' }};">
                            {{ $sortDirection == 'asc' ? '↑' : '↓' }}
                        </span>
                    </div>
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                    {{ __('Venue') }}
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                    {{ __('Capacity') }}
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
            @forelse($events as $event)
                <tr wire:key="event-{{ $event->id }}" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition-colors duration-300 ease-in-out">
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="flex-shrink-0 h-50 w-50">
                            @if($event->event_image)
                                <img class="h-50 w-50 rounded-md object-cover" src="{{ $event->event_image_url }}" alt="{{ $event->name }}">
                            @else
                                <div class="h-50 w-50 rounded-md bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400 dark:text-gray-500">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ Str::limit($event->name, 30) }}</div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                        {{ $event->date->format('M j, Y g:i A') }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                        <a href="{{ $event->venue?->getGoogleMapsUrl() }}"
                           target="_blank"
                           class="hover:underline"
                           title="{{ __('View on Google Maps') }}"
                        >
                            {{ Str::limit($event->venue?->name, 30, '...') }}
                        </a>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <x-ui.usage-bar :progress="$event->tickets_count" :max="$event->capacity" />
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        @php
                            $badge = match(true) {
                                $event->trashed() => ['color' => 'red', 'text' => 'Deleted'],
                                $event->date->isPast() => ['color' => 'gray', 'text' => 'Passed'],
                                $event->is_published => ['color' => 'green', 'text' => 'Published'],
                                !empty($event->publish_at) => ['color' => 'blue', 'text' => 'Publishes '.$event->publish_at->diffForHumans()],
                                default => ['color' => 'yellow', 'text' => 'Unlisted']
                            };
                        @endphp
                        <x-ui.badge :color="$badge['color']" :text="$badge['text']" size="sm"/>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end gap-2">
                            @if($event->trashed())
                                @can('events.delete')
                                    <button wire:click="restoreEvent({{ $event->id }})"
                                            class="p-1 text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors duration-300 ease-in-out hover:cursor-pointer"
                                            title="{{ __('Restore') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                                        </svg>
                                    </button>
                                    <x-ui.delete-button
                                        type="forcedelete"
                                        method="forceDeleteEvent"
                                        :args="[$event->id]"
                                        confirmation="⚠️ Are you sure you want to permanently delete this event?"
                                        title="{{ __('Delete permanently') }}"
                                        disabledTitle="Cannot permanently delete events which have sold tickets"
                                        :disabled="$event->tickets->count() !== 0"
                                    />
                                @endcan
                            @else
                                <a href="{{ route('event.tickets', [$event->organization->subdomain, $event->uniqid]) }}" target="_blank"
                                   class="p-1 text-blue-600 hover:text-green-900 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors duration-300 ease-in-out"
                                   title="{{ __('Show event') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                                    </svg>

                                </a>
                                <a href="{{ route('ticket-types.index', $event) }}" wire:navigate
                                   class="p-1 text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors duration-300 ease-in-out"
                                   title="{{ __('Tickets') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                    </svg>
                                </a>
                                @can('events.update')
                                    <x-ui.edit-button wire:click="editEvent({{ $event->id }})" title="{{ __('Edit') }}"/>
                                @endcan
                                @can('events.delete')
                                    <x-ui.delete-button
                                        type="delete"
                                        method="deleteEvent"
                                        :args="[$event->id]"
                                        confirmation="Are you sure you want to delete this event?"
                                        title="{{ __('Delete') }}"
                                    />
                                @endcan
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-sm text-gray-500 dark:text-gray-300 text-center">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            {{ __('No events found') }}
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $events->links() }}
    </div>
</div>
