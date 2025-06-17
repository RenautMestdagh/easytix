<div class="bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg p-8 mb-8">
    <h1 class="mb-6 text-2xl">Order summary</h1>
    <div>
        @foreach($quantities as $orderTicket)
            @if($orderTicket->amount == 0)
                @continue
            @endif
            <div class="border-b dark:border-gray-700 last:border-b-gray-800 dark:last:border-b-gray-200">
                <div class="flex flex-wrap items-center mb-2 mt-2 text-gray-800 dark:text-gray-200">
                    <h3 class="font-bold text-lg w-full sm:w-auto sm:flex-1">
                        <span class="font-bold tabular-nums">{{ $orderTicket->amount }}</span>
                        <span class="mx-2">x</span>
                        {{ $orderTicket->name }}
                    </h3>

                    <div class="flex items-center justify-between w-full sm:w-auto mt-2 sm:mt-0">
                        <p class="text-gray-500 dark:text-gray-400 sm:mr-6 md:mr-12 lg:mr-24">
                            €{{ number_format($orderTicket->price_cents / 100, 2) }}
                        </p>

                        <div class="flex items-center">
                            <span class="px-4 py-2 text-gray-800 dark:text-gray-200 tabular-nums">
                                €{{ number_format($orderTicket->price_cents * $orderTicket->amount / 100, 2)  }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-8">
        <div class="flex flex-wrap items-center mb-2 mt-2 text-gray-800 dark:text-gray-200">
            <h3 class="font-bold text-lg w-full sm:w-auto sm:flex-1">
                Total Amount
            </h3>

            <div class="flex items-center justify-between w-full sm:w-auto mt-2 sm:mt-0">
                <div class="flex items-center">
                    <span class="px-4 py-2 text-gray-800 dark:text-gray-200 tabular-nums font-bold">
                        €{{ number_format($orderTotal / 100, 2) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
