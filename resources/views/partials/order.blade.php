<x-layouts.organization
    :backgroundOverride="$event->background_image_url ?? null"
    :logoOverride="$event->header_image_url ?? null"
>
    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-zinc-700 rounded-2xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gray-800 dark:bg-zinc-800 px-6 py-8 text-center relative">
                <div class="absolute inset-0 opacity-10"></div>
                <div class="relative z-10">
                    <div class="text-5xl mb-4">🎉</div>
                    <h1 class="text-3xl font-bold text-white">Order Confirmation</h1>
                    <p class="mt-2 text-gray-300">Your order has been confirmed</p>
                </div>
            </div>

            <!-- Content -->
            <div class="px-6 py-8">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-4">Event Details</h2>

                    @if($event->event_image)
                        <div class="mb-6 overflow-hidden flex justify-center">
                            <div class="w-fit">
                                <img src="{{ $event->event_image_url }}" alt="{{ $event->name }}" class="w-full h-64 object-contain rounded-xl shadow-lg">
                            </div>
                        </div>
                    @endif

                    <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-2">{{ $event->name }}</h3>

                    <div class="grid gap-4 mt-4">
                        <div class="flex items-center p-4 bg-gray-50 dark:bg-zinc-600 rounded-lg">
                            <svg class="w-6 h-6 text-gray-600 dark:text-gray-400 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-gray-700 dark:text-gray-200">{{ $event->date->format('F j, Y, g:i A') }}</span>
                        </div>

                        <div class="flex items-center p-4 bg-gray-50 dark:bg-zinc-600 rounded-lg">
                            <svg class="w-6 h-6 text-gray-600 dark:text-gray-400 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="text-gray-700 dark:text-gray-200">{{ $event->location }}</span>
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-4">Order Summary</h2>

                    <div class="bg-white dark:bg-zinc-600 rounded-xl shadow-sm overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-500">
                            <thead class="bg-gray-50 dark:bg-zinc-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ticket Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Price</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-zinc-600 divide-y divide-gray-200 dark:divide-zinc-500">
                            @foreach($quantities as $id => $ticketType)
                                @if($ticketType->amount == 0) @continue @endif
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $ticketType->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $ticketType->amount }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">€{{ number_format($ticketType->price_cents / 100, 2) }}</td>
                                </tr>
                            @endforeach

                            @if(count($appliedDiscounts) > 0)
                                <tr class="bg-gray-50 dark:bg-zinc-700 font-semibold">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100" colspan="2">Subtotal</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        €{{ number_format($subtotal / 100, 2) }}
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="2" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        Discounts
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-500 dark:text-red-400">
                                        -€{{ number_format( $discountAmount / 100, 2) }}
                                    </td>
                                </tr>
                            @endif

                            <tr class="bg-gray-50 dark:bg-zinc-700 font-bold">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100" colspan="2">Total</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    €{{ number_format($orderTotal / 100, 2) }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-4">Order Information</h2>

                    <div class="bg-gray-50 dark:bg-zinc-600 p-4 rounded-lg flex justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Order id</h3>
                        <p class="text-gray-700 dark:text-gray-200">{{ $order->uniqid }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 dark:bg-zinc-600 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-2">Customer Details</h3>
                            <p class="text-gray-700 dark:text-gray-200">{{ $order->customer->first_name }} {{ $order->customer->last_name }}</p>
                            <p class="text-gray-700 dark:text-gray-200">{{ $order->customer->email }}</p>
                        </div>

                        <div class="bg-gray-50 dark:bg-zinc-600 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-2">Payment Information</h3>
                            <p class="text-gray-700 dark:text-gray-200">Payment ID: ****{{ substr($order->payment_id, -4) }}</p>
                            <p class="text-gray-700 dark:text-gray-200">Status: <span class="text-green-600 dark:text-green-400 font-semibold">Completed</span></p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-8">
                    <a href="{{ route('tickets.download', [$event->organization->subdomain, $order->uniqid]) }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-gray-800 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                        <svg class="-ml-1 mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Download Tickets
                    </a>

                    <a href="{{ url()->previous() }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md shadow-sm text-gray-700 dark:text-gray-200 bg-white dark:bg-zinc-600 hover:bg-gray-50 dark:hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                        Back to Event
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.organization>
