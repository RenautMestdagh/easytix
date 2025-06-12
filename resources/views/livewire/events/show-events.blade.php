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
            <x-ui.button href="{{ route('events.create') }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('New Event') }}
            </x-ui.button>
        </div>
    </div>

    <!-- Filters -->
    <div class="my-4 flex items-center gap-4">
        <!-- Search -->
        <div class="relative w-1/3">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 103 10.5a7.5 7.5 0 0013.15 6.15z"/>
                </svg>
            </div>
            <input type="text"
                   wire:model.live.debounce.250ms="search"
                   placeholder="{{ __('Search events...') }}"
                   class="pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 p-2 text-sm w-full"
            />
        </div>

        <!-- Organization filter (for superadmin) -->
        @role('superadmin')
        <x-ui.forms.select wire:model.live="selectedOrganization">
            @foreach($organizations as $org)
                <option value="{{ $org->id }}">{{ Str::limit($org->name, 30) }}</option>
            @endforeach
        </x-ui.forms.select>
        @endrole

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
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" wire:click="sortBy('name')">
                    <div class="flex items-center">
                        {{ __('Name') }}
                        <span class="ml-1 text-xs" style="visibility: {{ $sortField == 'name' ? 'visible' : 'hidden' }};">
                            {{ $sortDirection == 'asc' ? '↑' : '↓' }}
                        </span>
                    </div>
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" wire:click="sortBy('date')">
                    <div class="flex items-center">
                        {{ __('Date') }}
                        <span class="ml-1 text-xs" style="visibility: {{ $sortField == 'date' ? 'visible' : 'hidden' }};">
                            {{ $sortDirection == 'asc' ? '↑' : '↓' }}
                        </span>
                    </div>
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                    {{ __('Location') }}
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
                <tr wire:key="event-{{ $event->id }}" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition duration-150">
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
                        {{ Str::limit($event->location, 30) }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        @if($event->max_capacity)
                            <div class="flex items-center">
                                <div class="w-20 mr-2">
                                    <div class="h-2 bg-gray-200 rounded-full dark:bg-gray-600">
                                        <div class="h-2 rounded-full
                                        @if(($event->tickets->count() / $event->max_capacity * 100) >= 90) bg-red-500
                                        @elseif(($event->tickets->count() / $event->max_capacity * 100) >= 50) bg-yellow-500
                                        @else bg-green-500 @endif"
                                             style="width: {{ min(100, ($event->tickets->count() / $event->max_capacity * 100)) }}%">
                                        </div>
                                    </div>
                                </div>
                                <span class="text-xs text-gray-600 dark:text-gray-300">
                                    {{ $event->tickets->count() }}/{{ $event->max_capacity }}
                                </span>
                            </div>
                        @else
                            <span class="text-xs text-gray-500 dark:text-gray-400">Unlimited</span>
                        @endif
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        @if($event->trashed())
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400">
                                {{ __('Deleted') }}
                            </span>
                        @elseif($event->date->isPast())
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400">
                                {{ __('Passed') }}
                            </span>
                        @elseif($event->is_published)
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                {{ __('Published') }}
                            </span>
                        @elseif($event->publish_at)
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400" title="Will publish on {{ $event->publish_at->format('M j, Y g:i A') }}">
                                {{ __('Publishes ') }}{{ $event->publish_at->diffForHumans() }}
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400">
                                {{ __('Draft') }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end gap-2">
                            @if($event->trashed())
                                <button wire:click="restoreEvent({{ $event->id }})"
                                        class="p-1 text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors"
                                        title="{{ __('Restore') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                                    </svg>
                                </button>
                                <button onclick="confirm('Are you sure you want to permanently delete this event?') && @this.call('forceDeleteEvent', {{ $event->id }})"
                                        class="p-1 text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors"
                                        title="{{ __('Delete permanently') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                    </svg>
                                </button>
                            @else
                                <a href="{{ route('tickettypes.show', $event) }}" wire:navigate
                                   class="p-1 text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors"
                                   title="{{ __('Tickets') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                                    </svg>
                                </a>
                                <a href="{{ route('events.edit', $event) }}" wire:navigate
                                   class="p-1 text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors"
                                   title="{{ __('Edit') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                    </svg>
                                </a>
                                <button onclick="confirm('Are you sure you want to delete this event?') && @this.call('deleteEvent', {{ $event->id }})"
                                        class="p-1 text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors"
                                        title="{{ __('Delete') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                    </svg>
                                </button>
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
