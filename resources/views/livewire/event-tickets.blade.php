<div>
    @if($timeRemaining === 'EXPIRED')
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Order Expired</h3>
                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-gray-600 mb-6">Your order has expired. Please refresh the page to start a new order.</p>
                <button
                    wire:click="orderExpired"
                    class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200"
                >
                    Refresh Page
                </button>
            </div>
        </div>
    @else


        <div wire:poll.{{ $pollInterval }}ms="updateTimeRemaining">
            @if(collect($this->quantities)->flatten()->sum() !== 0)
            <!-- Floating Countdown Timer -->
            <div class="sm:fixed sm:top-6 sm:right-6 sm:mb-0 mb-4 z-50 bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-2 rounded-xl shadow-lg flex items-center gap-2">
                <svg class="w-5 h-5 text-yellow-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                </svg>
                <span class="text-sm">
                    Order expires in <span class="font-bold">{{ $timeRemaining }}</span>
                </span>
            </div>
            @endif
        </div>

        <!-- Event Header -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
            <div class="p-6">
                <h1 class="text-3xl font-bold text-gray-800 mb-4">{{ $event->name }}</h1>

                @if($event->description)
                    <div class="px-6 pb-6">
                        <p class="text-gray-600 leading-relaxed">{{ $event->description }}</p>
                    </div>
                @endif

                @include('partials.event-meta', ['event' => $event])
            </div>
        </div>

        <!-- Ticket Selection Livewire Component -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">

            <!-- Ticket Selection -->
            <div class="p-6" wire:poll.15s="calculateAllAvailableTickets">
                @forelse($this->ticketTypes as $ticketType)
                    <div class="border-b last:border-b-0">
                        <div class="flex flex-wrap items-center mb-2 mt-2 text-gray-800">
                            <h3 class="font-bold text-lg w-full sm:w-auto sm:flex-1">{{ $ticketType->name }}</h3>

                            <div class="flex items-center justify-between w-full sm:w-auto mt-2 sm:mt-0">
                                <p class="text-gray-600 sm:mr-6 md:mr-12 lg:mr-24">€{{ number_format($ticketType->price_cents / 100, 2) }}</p>
                                @if($remainingQuantities[$ticketType->id] != -99)
                                    <div class="flex items-center">
                                        <button
                                            wire:click="decrement({{ $ticketType->id }})"
                                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-l disabled:opacity-50 disabled:hover:bg-gray-200 aspect-square h-10 flex items-center justify-center"
                                            @disabled($quantities[$ticketType->id] <= 0)
                                            wire:loading.attr="disabled"
                                            wire:target="decrement({{ $ticketType->id }})"
                                        >
                                        <span class="inline-flex items-center justify-center w-4 h-4">
                                            <span wire:loading.remove wire:target="decrement({{ $ticketType->id }})">-</span>
                                            <span wire:loading wire:target="decrement({{ $ticketType->id }})">
                                                <svg width="12" height="12" stroke="#000" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><style>.spinner_V8m1{transform-origin:center;animation:spinner_zKoa 2s linear infinite}.spinner_V8m1 circle{stroke-linecap:round;animation:spinner_YpZS 1.5s ease-in-out infinite}@keyframes spinner_zKoa{100%{transform:rotate(360deg)}}@keyframes spinner_YpZS{0%{stroke-dasharray:0 150;stroke-dashoffset:0}47.5%{stroke-dasharray:42 150;stroke-dashoffset:-16}95%,100%{stroke-dasharray:42 150;stroke-dashoffset:-59}}</style><g class="spinner_V8m1"><circle cx="12" cy="12" r="9.5" fill="none" stroke-width="3"></circle></g></svg>
                                            </span>
                                        </span>
                                        </button>
                                        <span class="bg-gray-100 px-4 py-2 text-gray-800 tabular-nums">
                                        {{ $quantities[$ticketType->id] }}
                                    </span>
                                        <button
                                            wire:click="increment({{ $ticketType->id }})"
                                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-r disabled:opacity-50 disabled:hover:bg-gray-200 aspect-square h-10 flex items-center justify-center"
                                            @disabled($quantities[$ticketType->id] >= $ticketType->available_quantity)
                                            wire:loading.attr="disabled"
                                            wire:target="increment({{ $ticketType->id }})"
                                        >
                                        <span class="inline-flex items-center justify-center w-4 h-4">
                                            <span wire:loading.remove wire:target="increment({{ $ticketType->id }})">+</span>
                                            <span wire:loading wire:target="increment({{ $ticketType->id }})">
                                                <svg width="12" height="12" stroke="#000" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><style>.spinner_V8m1{transform-origin:center;animation:spinner_zKoa 2s linear infinite}.spinner_V8m1 circle{stroke-linecap:round;animation:spinner_YpZS 1.5s ease-in-out infinite}@keyframes spinner_zKoa{100%{transform:rotate(360deg)}}@keyframes spinner_YpZS{0%{stroke-dasharray:0 150;stroke-dashoffset:0}47.5%{stroke-dasharray:42 150;stroke-dashoffset:-16}95%,100%{stroke-dasharray:42 150;stroke-dashoffset:-59}}</style><g class="spinner_V8m1"><circle cx="12" cy="12" r="9.5" fill="none" stroke-width="3"></circle></g></svg>
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

        <!-- Checkout -->
        <button
            class="bg-blue-500 hover:bg-blue-600 active:bg-blue-700 transition-colors duration-200 rounded-lg shadow-lg overflow-hidden mb-8 w-full"
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


