<div>
    <div class="mx-auto max-w-7xl p-4 sm:p-6 lg:p-8">
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                    </svg>
                    <h1 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">{{ __('Venues') }}</h1>
                </div>
                <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                    {{ __('Manage all venues.') }}
                </p>
            </div>
            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                <x-ui.button
                    href="{{ route('venues.create') }}"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('New Venue') }}
                </x-ui.button>
            </div>
        </div>

        <!-- Search Field and Include Deleted -->
        <div class="my-4 flex items-center gap-4">
            <div class="relative w-1/3">
                <!-- Search icon -->
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 103 10.5a7.5 7.5 0 0013.15 6.15z"/>
                    </svg>
                </div>

                <!-- Input field -->
                <input type="text"
                       wire:model.live.debounce.150ms="search"
                       placeholder="{{ __('Search by name...') }}"
                       class="pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 p-2 text-sm w-full"
                />
            </div>

            <!-- Include Deleted Checkbox -->
            <label class="flex items-center ml-auto">
                <input type="checkbox" wire:model.live="includeDeleted" class="form-checkbox text-indigo-600">
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('Include deleted') }}</span>
            </label>
        </div>

        @if (session()->has('message'))
            <x-ui.flash-message/>
        @endif

        <!-- Venues Table -->
        <div>
            <div class="pt-4 flow-root">
                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle">
                        <div class="overflow-hidden shadow-sm sm:rounded-lg bg-white dark:bg-gray-800 mx-8">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-white sm:pl-6 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-300 ease-in-out" wire:click="sortBy('name')">
                                        {{ __('Name') }}
                                        <span class="text-xs ml-1" style="visibility: {{ $sortField == 'name' ? 'visible' : 'hidden' }};">
                                            {{ $sortDirection == 'asc' ? '↑' : '↓' }}
                                        </span>
                                    </th>
                                    <th scope="col" class="py-3.5 pl-3 pr-4 text-left text-sm font-semibold text-gray-900 dark:text-white sm:pl-6 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-300 ease-in-out" wire:click="sortBy('max_capacity')">
                                        {{ __('Capacity') }}
                                        <span class="text-xs ml-1" style="visibility: {{ $sortField == 'max_capacity' ? 'visible' : 'hidden' }};">
                                            {{ $sortDirection == 'asc' ? '↑' : '↓' }}
                                        </span>
                                    </th>
                                    php
                                    @if(auth()->user()->can('venues.update') || auth()->user()->can('venues.delete'))
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                            <span class="sr-only">{{ __('Actions') }}</span>
                                        </th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($venues as $venue)
                                    <tr wire:key="venue-{{ $venue->id }}" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition-colors duration-300 ease-in-out">
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-white sm:pl-6">
                                            <div class="flex items-center gap-2">
                                                {{ $venue->name }}
                                                @if($venue->trashed())
                                                    <x-ui.badge color="red" text="Deleted" size="sm"/>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap py-4 pl-3 pr-4 text-sm text-gray-500 dark:text-gray-300">
                                            @if($venue->max_capacity)
                                                {{ number_format($venue->max_capacity, 0, ',', '.') }}
                                            @else
                                                ∞
                                            @endif
                                        </td>
                                        @if(auth()->user()->can('venues.update') || auth()->user()->can('venues.delete'))
                                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                                <div class="flex justify-end gap-2">
                                                    @if(!empty($venue->coordinates))
                                                        <a href="{{ $venue->getGoogleMapsUrl() }}"
                                                           target="_blank"
                                                           class="p-1 text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors duration-300 ease-in-out"
                                                           title="{{ __('View on Google Maps') }}"
                                                        >
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                                            </svg>
                                                        </a>
                                                    @endif
                                                    @if ($venue->trashed())
                                                        @can('venues.delete')
                                                            <!-- Restore Button -->
                                                            <button type="button"
                                                                    wire:click="restoreVenue({{ $venue->id }})"
                                                                    class="p-1 text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors duration-300 ease-in-out hover:cursor-pointer"
                                                                    title="{{ __('Restore') }}"
                                                            >
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                                                                </svg>
                                                            </button>

                                                            <x-ui.delete-button
                                                                type="forcedelete"
                                                                method="forceDeleteVenue"
                                                                :args="[$venue->id]"
                                                                confirmation="⚠️ Are you sure you want to permanently delete this venue?"
                                                                title="{{ __('Delete permanently') }}"
                                                            />
                                                        @endcan
                                                    @else
                                                        @can('venues.update')
                                                            <!-- Edit Button -->
                                                            <x-ui.edit-button
                                                                route="venues.update"
                                                                :routeParams="['venue' => $venue]"
                                                                title="{{ __('Edit') }}"
                                                            />
                                                        @endcan

                                                        @can('venues.delete')
                                                            <!-- Soft Delete Button -->
                                                            <x-ui.delete-button
                                                                type="delete"
                                                                method="deleteVenue"
                                                                :args="[$venue->id]"
                                                                confirmation="Are you sure you want to delete this venue?"
                                                                title="{{ __('Delete') }}"
                                                            />
                                                        @endcan
                                                    @endif
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-3 py-8 text-sm text-gray-500 dark:text-gray-300 text-center">
                                            <div class="flex flex-col items-center justify-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-gray-400">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                                {{ __('No venues found') }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                {{ $venues->links() }}
            </div>
        </div>
    </div>
</div>
