<div class="mx-auto max-w-10xl p-4 sm:p-6 lg:p-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <div class="flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                </svg>
                <h1 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">{{ __('Ticket Types for :event', ['event' => $event->name]) }}</h1>
            </div>
            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                {{ __('Manage all ticket types for this event.') }}
            </p>
        </div>
        <a href="{{ route('events.index') }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 flex items-center transition-all duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            {{ __('Back to Events') }}
        </a>
    </div>

    <div class="mt-4 flex flex-row-reverse">
        {{--            <x-ui.button href="{{ route('tickettypes.create', $event) }}">--}}
        <x-ui.button href="">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('New Ticket Type') }}
        </x-ui.button>
    </div>

    <!-- Event Summary -->
    <div class="mt-6 bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 mb-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('Event Summary') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('Total Capacity') }}</p>
                <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ $event->max_capacity ?: __('Unlimited') }}
                </p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('Tickets Sold') }}</p>
                <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ $event->tickets->count() }}
                </p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('Tickets Assigned') }}</p>
                <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">
{{--                    {{ $event->tickets()->whereNotNull('assigned_to')->count() }}--}}
                    --
                </p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('Available Tickets') }}</p>
                <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ $event->max_capacity ? $event->max_capacity - $event->tickets->count() : __('Unlimited') }}
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
                <tr wire:key="ticket-type-{{ $ticketType->id }}" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition duration-150">
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $ticketType->name }}</div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                        {{ number_format($ticketType->price_cents / 100, 2) }} €
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-20 mr-2">
                                <div class="h-2 bg-gray-200 rounded-full dark:bg-gray-600">
                                    @if($ticketType->available_quantity)
                                        <div class="h-2 rounded-full
                                        @if(($ticketType->tickets_count / $ticketType->available_quantity * 100) >= 90) bg-red-500
                                        @elseif(($ticketType->tickets_count / $ticketType->available_quantity * 100) >= 50) bg-yellow-500
                                        @else bg-green-500 @endif"
                                             style="width: {{ min(100, ($ticketType->tickets_count / $ticketType->available_quantity * 100)) }}%">
                                        </div>
                                    @else
                                        <div class="h-2 rounded-full bg-green-500" style="width: 100%"></div>
                                    @endif
                                </div>
                            </div>
                            <span class="text-xs text-gray-600 dark:text-gray-300">
                                @if($ticketType->available_quantity)
                                    {{ $ticketType->tickets_count }}/{{ $ticketType->available_quantity }}
                                @else
                                    {{ $ticketType->tickets_count }}/∞
                                @endif
                            </span>
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        @if($ticketType->trashed())
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400">
                                {{ __('Deleted') }}
                            </span>
                        @elseif($ticketType->is_published)
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                {{ __('Published') }}
                            </span>
                        @elseif($ticketType->publish_at)
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                {{ __('Publishes ') }}{{ $ticketType->publish_at->diffForHumans() }}
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400">
                                {{ __('Draft') }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end gap-2">
                            @if($ticketType->trashed())
                                <button wire:click="restoreTicketType({{ $ticketType->id }})"
                                        class="p-1 text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors"
                                        title="{{ __('Restore') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                                    </svg>
                                </button>
                                <button onclick="confirm('Are you sure you want to permanently delete this ticket type?') && @this.call('forceDeleteTicketType', {{ $ticketType->id }})"
                                        class="p-1 text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors"
                                        title="{{ __('Delete permanently') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                    </svg>
                                </button>
                            @else
{{--                                <a href="{{ route('tickettypes.edit', $ticketType) }}" wire:navigate--}}
                                <a href="" wire:navigate
                                   class="p-1 text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors"
                                   title="{{ __('Edit') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                    </svg>
                                </a>
                                <button onclick="confirm('Are you sure you want to delete this ticket type?') && @this.call('deleteTicketType', {{ $ticketType->id }})"
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
