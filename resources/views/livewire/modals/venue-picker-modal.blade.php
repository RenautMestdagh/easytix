<div>
    <!-- Trigger Button (only shown if showTriggerButton is true) -->
    @if($showTriggerButton)
        <button
            type="button"
            wire:click="openVenuePicker(@js($selectedVenueId))"
            class="inline-flex items-center px-2 py-2 bg-indigo-600 border border-transparent rounded-2xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors duration-300 ease-in-out hover:cursor-pointer"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
            </svg>
        </button>
    @endif

    <!-- Modal -->
    <div
        x-data="{ show: @entangle('showModal') }"
        x-show="show"
        x-on:keydown.escape.window="show = false"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <!-- Overlay -->
        <div
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-500/75 transition-opacity"
        ></div>

        <!-- Modal Container -->
        <div class="flex min-h-screen items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <!-- Modal Panel -->
            <div
                x-show="show"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all duration-300 ease-in-out sm:my-8 w-full sm:max-w-2xl"
                @click.away="show = false"
            >
                <!-- Modal Content -->
                <div class="bg-white dark:bg-gray-800 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="mb-4">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">
                            {{ __('Select Venue') }}
                        </h3>
                    </div>

                    <div class="space-y-4">
                        <!-- Search -->
                        <div class="flex items-center gap-4">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 103 10.5a7.5 7.5 0 0013.15 6.15z"/>
                                    </svg>
                                </div>
                                <input
                                    type="text"
                                    wire:model.live.debounce.150ms="search"
                                    placeholder="{{ __('Search venues by name...') }}"
                                    class="pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 p-2 text-sm w-full"
                                    autofocus
                                />
                            </div>
                        </div>

                        <!-- Venues List -->
                        <div class="border border-gray-200 dark:border-gray-700 rounded-t-lg last:rounded-b-lg overflow-hidden mb-0">
                            <div class="max-h-96 overflow-y-auto">
                                @forelse($this->venues as $venue)
                                    <div
                                        wire:key="venue-{{ $venue->id }}"
                                        wire:click="selectVenue('{{ $venue->id }}', '{{ $venue->name }}')"
                                        class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors duration-300 ease-in-out border-b border-gray-200 dark:border-gray-700 last:border-b-0"
                                    >
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <p class="font-medium text-gray-900 dark:text-white">
                                                    {{ $venue->name }}
                                                    @if($venue->trashed())
                                                        <span class="ml-2 inline-flex items-center rounded-md bg-red-50 dark:bg-red-900/20 px-2 py-1 text-xs font-medium text-red-700 dark:text-red-400 ring-1 ring-inset ring-red-600/10">
                                                            {{ __('Deleted') }}
                                                        </span>
                                                    @endif
                                                </p>
                                                @if($venue->max_capacity)
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ __('Capacity') }}: {{ $venue->max_capacity }}
                                                    </p>
                                                @endif
                                            </div>
                                            @if($venue->coordinates)
                                                <a
                                                    href="{{ $venue->getGoogleMapsUrl() }}"
                                                    target="_blank"
                                                    class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 transition-colors duration-300 ease-in-out"
                                                    onclick="event.stopPropagation()"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                                        {{ $search ? __('No venues match your search') : __('Start typing to search venues') }}
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Results count message -->
                        @if($this->venues->isNotEmpty() && $this->totalMatchingVenues > $maxResults)
                            <div class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/30 rounded-b-lg border-t-0 border border-gray-200 dark:border-gray-700">
                                {{ __('Showing :count of :total venues. Refine your search to find more.', [
                                    'count' => $this->venues->count(),
                                    'total' => $this->totalMatchingVenues
                                ]) }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700/30 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-3">
                    <button
                        type="button"
                        wire:click="$set('showModal', false)"
                        class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors duration-300 ease-in-out"
                    >
                        {{ __('Cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
