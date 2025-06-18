<div>
    <!-- Event Header -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden mb-8">
        <div class="p-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200 mb-4">{{ $event->name }}</h1>

            @if($event->description)
                <div class="px-6 pb-6">
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">{{ $event->description }}</p>
                </div>
            @endif

            @include('partials.tenant-event.event-meta')
        </div>
    </div>

    @if (session()->has('message'))
        <x-ui.flash-message
            :message="session('message')"
            :type="session('message_type', 'success')"
        />
    @endif

    <!-- Ticket Selection Livewire Component -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden mb-8">

        <!-- Ticket Selection -->
        <div class="p-6" wire:poll.15s="calculateAllAvailableTickets">
            @forelse($event->ticketTypes as $ticketType)
                <div class="border-b dark:border-gray-700 last:border-b-0">
                    <div class="flex flex-wrap items-center mb-2 mt-2 text-gray-800 dark:text-gray-200">
                        <h3 class="font-bold text-lg w-full sm:w-auto sm:flex-1">{{ $ticketType->name }}</h3>

                        <div class="flex items-center justify-between w-full sm:w-auto mt-2 sm:mt-0">
                            <p class="text-gray-600 dark:text-gray-400 sm:mr-6 md:mr-12 lg:mr-24">€{{ number_format($ticketType->price_cents / 100, 2) }}</p>
                            @if(!$remainingQuantities[$ticketType->id]->soldout)
                                <div class="flex items-center">
                                    <button
                                        wire:click="decrement({{ $ticketType->id }})"
                                        class="bg-gray-200 text-gray-800 hover:bg-gray-300 disabled:hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 dark:disabled:hover:bg-gray-700 font-bold py-2 px-4 rounded-l disabled:opacity-50  aspect-square h-10 flex items-center justify-center"
                                        @disabled($quantities[$ticketType->id]->amount <= 0)
                                        wire:loading.attr="disabled"
                                        wire:target="decrement({{ $ticketType->id }})"
                                    >
                                        <span class="inline-flex items-center justify-center w-4 h-4">
                                            <span wire:loading.remove wire:target="decrement({{ $ticketType->id }})">-</span>
                                            <span wire:loading wire:target="decrement({{ $ticketType->id }})">
                                                <svg width="12" height="12" class="stroke-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><style>.spinner_V8m1{transform-origin:center;animation:spinner_zKoa 2s linear infinite}.spinner_V8m1 circle{stroke-linecap:round;animation:spinner_YpZS 1.5s ease-in-out infinite}@keyframes spinner_zKoa{100%{transform:rotate(360deg)}}@keyframes spinner_YpZS{0%{stroke-dasharray:0 150;stroke-dashoffset:0}47.5%{stroke-dasharray:42 150;stroke-dashoffset:-16}95%,100%{stroke-dasharray:42 150;stroke-dashoffset:-59}}</style><g class="spinner_V8m1"><circle cx="12" cy="12" r="9.5" fill="none" stroke-width="3"></circle></g></svg>
                                            </span>
                                        </span>
                                    </button>
                                    <span class="bg-gray-100 dark:bg-gray-600 px-4 py-2 text-gray-800 dark:text-gray-200 tabular-nums">
                                        {{ $quantities[$ticketType->id]->amount }}
                                    </span>
                                    @php
                                        $allRemainingTicketsInBasket = $this->event->tickets->count() + collect($this->quantities)->sum('amount') >= $this->event->max_capacity
                                    @endphp
                                    <button
                                        wire:click="increment({{ $ticketType->id }})"
                                        class="bg-gray-200 text-gray-800 hover:bg-gray-300 disabled:hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 dark:disabled:hover:bg-gray-700 font-bold py-2 px-4 rounded-r disabled:opacity-50  aspect-square h-10 flex items-center justify-center"
                                        @disabled($quantities[$ticketType->id]->amount >= $remainingQuantities[$ticketType->id]->plusDisabledFrom || $allRemainingTicketsInBasket)
                                        wire:loading.attr="disabled"
                                        wire:target="increment({{ $ticketType->id }})"
                                    >
                                        <span class="inline-flex items-center justify-center w-4 h-4">
                                            <span wire:loading.remove wire:target="increment({{ $ticketType->id }})">+</span>
                                            <span wire:loading wire:target="increment({{ $ticketType->id }})">
                                                <svg width="12" height="12" class="stroke-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><style>.spinner_V8m1{transform-origin:center;animation:spinner_zKoa 2s linear infinite}.spinner_V8m1 circle{stroke-linecap:round;animation:spinner_YpZS 1.5s ease-in-out infinite}@keyframes spinner_zKoa{100%{transform:rotate(360deg)}}@keyframes spinner_YpZS{0%{stroke-dasharray:0 150;stroke-dashoffset:0}47.5%{stroke-dasharray:42 150;stroke-dashoffset:-16}95%,100%{stroke-dasharray:42 150;stroke-dashoffset:-59}}</style><g class="spinner_V8m1"><circle cx="12" cy="12" r="9.5" fill="none" stroke-width="3"></circle></g></svg>
                                            </span>
                                        </span>
                                    </button>
                                </div>
                            @else
                                <div class="flex items-center">
                                    <span class="bg-gray-100 px-4 py-2 text-gray-800 tabular-nums rounded-sm opacity-60">
                                        SOLD OUT
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-sm text-gray-500 text-center">
                    <div class="flex flex-col items-center justify-center gap-2 text-gray-500">
                        <!-- No Ticket Available Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="1.5" stroke="currentColor" class="w-12 h-12 relative">
                            <!-- Original Ticket Path -->
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                            <!-- Red X (cross) -->
                            <line x1="3" y1="3" x2="21" y2="21" stroke="red" stroke-width="1.5" stroke-linecap="round"/>
                            <line x1="21" y1="3" x2="3" y2="21" stroke="red" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>

                        {{ __('No tickets available') }}
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    @if($event->ticketTypes->isNotEmpty())
        <!-- Checkout -->
        <button
            class="bg-blue-500 hover:bg-blue-600 active:bg-blue-700 transition-colors duration-200 rounded-lg shadow-lg overflow-hidden mb-8 w-full"
            wire:click="proceedToCheckout"
        >
            <div class="p-4 flex gap-3 justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                </svg>
                <p>Go to Checkout</p>
            </div>
        </button>
    @endif
</div>
