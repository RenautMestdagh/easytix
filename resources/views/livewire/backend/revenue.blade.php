<div class="mx-auto max-w-10xl p-4 sm:p-6 lg:p-8">
    @livewireStyles
    @livewireChartsScripts

    <style>
        .apexcharts-title-text {
            fill: rgb(49, 62, 94) !important;
        }
        .dark .apexcharts-title-text {
            fill: rgb(179, 188, 211) !important;
        }
        .apexcharts-text, .apexcharts-legend-text {
            fill: rgb(116, 129, 158) !important;
            color: rgb(116, 129, 158) !important;
        }
        .dark .apexcharts-text, .dark .apexcharts-legend-text {
            fill: #a8a8a8 !important;
            color: #a8a8a8 !important;
        }
        .apexcharts-tooltip.apexcharts-theme-light {
            background: #ffffff !important;
        }
        .dark .apexcharts-tooltip.apexcharts-theme-light, .dark .apexcharts-tooltip.apexcharts-theme-light .apexcharts-tooltip-title {
            background: #656565 !important;
        }
        .dark .apexcharts-tooltip.apexcharts-theme-light {
            border: 1px solid #4e4e4e;
        }
        .dark .apexcharts-tooltip.apexcharts-theme-light .apexcharts-tooltip-title {
            border-bottom: 1px solid #4e4e4e;
        }
    </style>

    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <div class="flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h1 class="text-xl font-semibold leading-6 text-gray-900 dark:text-white">
                    {{ __('Revenue Report') }}
                </h1>
            </div>
            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                {{ __('Financial overview for all events') }}
            </p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-3">
        <!-- Total Revenue -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Total Revenue') }}</p>
                    <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">
                        €{{ number_format($totalRevenue, 2) }}
                    </p>
                </div>
                <div class="bg-indigo-100 dark:bg-indigo-900/50 p-3 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-indigo-600 dark:text-indigo-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Tickets Sold -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Tickets Sold') }}</p>
                    <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">
                        {{ number_format($totalTicketsSold, 0) }}
                    </p>
                </div>
                <div class="bg-green-100 dark:bg-green-900/50 p-3 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-green-600 dark:text-green-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Average Order Value -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Avg. Order Value') }}</p>
                    <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">
                        €{{ number_format($averageOrderValue, 2) }}
                    </p>
                </div>
                <div class="bg-purple-100 dark:bg-purple-900/50 p-3 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-purple-600 dark:text-purple-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="mt-6 bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Filters') }}</h2>
            <button wire:click="resetFilters" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 hover:cursor-pointer">
                {{ __('Reset Filters') }}
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Event filter -->
            <div class="flex gap-4">
                <div class="flex-1 flex flex-col justify-center">
                    @if($selectedEvent && $event = \App\Models\Event::find($selectedEvent))
                        <div class="flex justify-between gap-2">
                            <div class="flex flex-col items-start">
                                <p>
                                    {{ Str::limit($event->name, 25, '...') }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    ({{ $event->date->format('M j, Y') }})
                                </p>
                            </div>
                            <x-ui.cross-button wire:click="$set('selectedEvent', '')" />
                        </div>
                    @else
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ __('All Events') }}
                        </p>
                    @endif
                </div>

                @livewire('modals.event-picker-modal', [
                    'selectedEventId' => $selectedEvent,
                    'showTriggerButton' => true
                ], key('event-picker-filter-'.$selectedEvent))
            </div>

            <!-- Date Range Filter -->
{{--            <x-ui.forms.group label="Date Range" for="dateRangeFilter" error="dateRangeFilter">--}}
{{--                <x-ui.forms.flatpickr--}}
{{--                    wire:model.lazy="dateRangeFilter"--}}
{{--                    name="dateRangeFilter"--}}
{{--                    error="{{ $errors->has('dateRangeFilter') }}"--}}
{{--                    class="rounded-xl shadow-md focus:ring-2 focus:ring-indigo-500 px-5 w-full"--}}
{{--                    placeholder="Select date range"--}}
{{--                    :config="['mode' => 'range']"--}}
{{--                />--}}
{{--            </x-ui.forms.group>--}}
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Daily Revenue -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 h-100">
            <livewire:livewire-line-chart
                key="{{ $dailyRevenueChart->reactiveKey() }}"
                :line-chart-model="$dailyRevenueChart"
            />
        </div>

        <!-- Revenue by Event -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 h-100">
            <livewire:livewire-column-chart
                key="{{ $eventRevenueChart->reactiveKey() }}"
                :column-chart-model="$eventRevenueChart"
            />
        </div>

        <!-- Revenue by Ticket Type -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 h-100">
            <livewire:livewire-pie-chart
                key="{{ $ticketTypeRevenueChart->reactiveKey() }}"
                :pie-chart-model="$ticketTypeRevenueChart"
            />
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Transactions') }}</h2>
            <div class="flex items-center">
                <span class="text-sm text-gray-500 dark:text-gray-400 mr-2">{{ __('Per page:') }}</span>
                <x-ui.forms.select
                    wire:model.live="perPage"
                    name="perPage"
                    error="{{ $errors->has('perPage') }}"
                    class="rounded-xl shadow-md focus:ring-2 focus:ring-indigo-500"
                >
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </x-ui.forms.select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                        {{ __('Date') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                        {{ __('Customer') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                        {{ __('Event') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                        {{ __('Tickets') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                        {{ __('Amount') }}
                    </th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($revenueData as $order)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ \Carbon\Carbon::parse($order->created_at)->format('M j, Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $order->first_name }} {{ $order->last_name }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $order->email }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $order->event_name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $order->ticket_count }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            €{{ number_format($order->total_cents / 100, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                <span class="text-gray-500 dark:text-gray-400">
                                    {{ __('No transactions found') }}
                                </span>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if(!$revenueData->isEmpty())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $revenueData->links() }}
            </div>
        @endif
    </div>
</div>
