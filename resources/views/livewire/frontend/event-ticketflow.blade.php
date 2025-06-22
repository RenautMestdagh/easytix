<div>
    @if($timeRemaining === 'EXPIRED')
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white  rounded-lg p-6 max-w-md w-full mx-4">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Order Expired</h3>
                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-gray-600 mb-6">Your order has expired. Please refresh the page to start a new order.</p>
                <a
                    href="{{ route('event.tickets', [$this->event->organization->subdomain, $this->event->uniqid]) }}"
                    class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition-all duration-300 ease-in-out"
                >
                    Refresh Page
                </a>
            </div>
        </div>
    @else

        <div
            @if($this->tempOrder_checkout_stage === 0 || $this->tempOrder_checkout_stage === 1)
                 wire:poll.{{ $pollInterval }}ms="updateTimeRemaining"
            @endif
        >
            @if(($this->tempOrder?->checkout_stage > 0 || collect($this->quantities)->sum('amount') != 0) && $this->tempOrder?->checkout_stage < 4)
                <!-- Floating Countdown Timer -->
                <div class="sm:fixed sm:top-6 sm:right-6 sm:mb-0 mb-4 z-50 bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-2 rounded-xl shadow-lg flex items-center gap-2">
                    <svg class="w-5 h-5 text-yellow-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm">
                        Order expires in <span class="font-bold" id="timeRemaining">{{ $timeRemaining }}</span>
                    </span>
                </div>
           @endif
        </div>

        @include("partials.tenant-event.{$toShowPartial}")

    @endif

</div>


