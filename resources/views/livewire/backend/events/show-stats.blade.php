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
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z" />
                </svg>
                <h1 class="text-xl font-semibold leading-6 text-gray-900 dark:text-white">
                    {{ $event->name }} - {{ __('Statistics') }}
                </h1>
            </div>
            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                {{ __('Detailed statistics about event attendees') }}
            </p>
        </div>
        <x-ui.back-to-button route="ticket-types.index" :route-params="$event" text="{{ __('Back to event') }}"/>
    </div>

    <!-- Filters -->
    <div class="mt-6 bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Filters') }}</h2>
            <button wire:click="resetFilters" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 hover:cursor-pointer">
                {{ __('Reset Filters') }}
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">

            <!-- Gender Filter -->
            <x-ui.forms.group label="Gender" for="genderFilter" error="genderFilter">
                <x-ui.forms.select
                    wire:model.lazy="genderFilter"
                    name="genderFilter"
                    error="{{ $errors->has('genderFilter') }}"
                    class="rounded-xl shadow-md focus:ring-2 focus:ring-indigo-500 px-5 w-full"
                >
                    <option value="">{{ __('All Genders') }}</option>
                    <option value="male">{{ __('Male') }}</option>
                    <option value="female">{{ __('Female') }}</option>
                    <option value="other">{{ __('Other') }}</option>
                    <option value="prefer not to say">{{ __('Prefer not to say') }}</option>
                </x-ui.forms.select>
            </x-ui.forms.group>

            <!-- Age Range Filter -->
            <x-ui.forms.group label="Age Range" for="ageRangeFilter" error="ageRangeFilter">
                <x-ui.forms.select
                    wire:model.lazy="ageRangeFilter"
                    name="ageRangeFilter"
                    error="{{ $errors->has('ageRangeFilter') }}"
                    class="rounded-xl shadow-md focus:ring-2 focus:ring-indigo-500 px-5 w-full"
                >
                    <option value="">{{ __('All Ages') }}</option>
                    <option value="0-9">0-9</option>
                    <option value="10-19">10-19</option>
                    <option value="20-29">20-29</option>
                    <option value="30-39">30-39</option>
                    <option value="40-49">40-49</option>
                    <option value="50-59">50-59</option>
                    <option value="60-69">60-69</option>
                    <option value="70-79">70-79</option>
                </x-ui.forms.select>
            </x-ui.forms.group>

            <!-- Country Filter -->
            <x-ui.forms.group label="Country" for="countryFilter" error="countryFilter">
                <x-ui.forms.select
                    wire:model.lazy="countryFilter"
                    name="countryFilter"
                    error="{{ $errors->has('countryFilter') }}"
                    class="rounded-xl shadow-md focus:ring-2 focus:ring-indigo-500 px-5 w-full"
                >
                    <option value="">{{ __('All Countries') }}</option>
                    @foreach($countries as $country)
                        <option value="{{ $country }}">{{ $country }}</option>
                    @endforeach
                </x-ui.forms.select>
            </x-ui.forms.group>

            <!-- City Filter -->
            <x-ui.forms.group label="City" for="cityFilter" error="cityFilter">
                <x-ui.forms.select
                    wire:model.lazy="cityFilter"
                    name="cityFilter"
                    error="{{ $errors->has('cityFilter') }}"
                    class="rounded-xl shadow-md focus:ring-2 focus:ring-indigo-500 px-5 w-full"
                >
                    <option value="">{{ __('All Cities') }}</option>
                    @foreach($cities as $city)
                        <option value="{{ $city }}">{{ $city }}</option>
                    @endforeach
                </x-ui.forms.select>
            </x-ui.forms.group>

            <!-- Ticket Type Filter -->
            <x-ui.forms.group label="Ticket Type" for="ticketTypeFilter" error="ticketTypeFilter">
                <x-ui.forms.select
                    wire:model.lazy="ticketTypeFilter"
                    name="ticketTypeFilter"
                    error="{{ $errors->has('ticketTypeFilter') }}"
                    class="rounded-xl shadow-md focus:ring-2 focus:ring-indigo-500 px-5 w-full"
                >
                    <option value="">{{ __('All Ticket Types') }}</option>
                    @foreach($ticketTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </x-ui.forms.select>
            </x-ui.forms.group>
        </div>
    </div>

    @if(!$customers->isEmpty())
    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Gender Distribution -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 h-100">
            <livewire:livewire-pie-chart
                key="{{ $genderChart->reactiveKey() }}"
                :pie-chart-model="$genderChart"
            />
        </div>

        <!-- Age Distribution -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 h-100">
            <livewire:livewire-column-chart
                key="{{ $ageChart->reactiveKey() }}"
                :column-chart-model="$ageChart"
            />
        </div>

        <!-- Country Distribution -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 h-100">
            <livewire:livewire-pie-chart
                key="{{ $countryChart->reactiveKey() }}"
                :pie-chart-model="$countryChart"
            />
        </div>

        <!-- Ticket Type Distribution -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 h-100">
            <livewire:livewire-pie-chart
                key="{{ $ticketTypeChart->reactiveKey() }}"
                :pie-chart-model="$ticketTypeChart"
            />
        </div>
    </div>
    @endif

    <!-- Customers Table -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Attendees') }}</h2>
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
                        {{ __('Name') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                        {{ __('Email') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                        {{ __('Gender') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                        {{ __('Age') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                        {{ __('Location') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                        {{ __('Ticket Type') }}
                    </th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($customers as $customer)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $customer->first_name }} {{ $customer->last_name }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $customer->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($customer->gender)
                                <x-ui.badge
                                    :color="$customer->gender === 'male' ? 'blue' : ($customer->gender === 'female' ? 'pink' : 'purple')"
                                    :text="ucfirst($customer->gender)"
                                    size="sm"
                                />
                            @else
                                <span class="text-gray-400 dark:text-gray-500">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            @if($customer->date_of_birth)
                                {{ \Carbon\Carbon::parse($customer->date_of_birth)->age }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            @if($customer->city || $customer->country)
                                {{ $customer->city }}{{ $customer->city && $customer->country ? ', ' : '' }}{{ $customer->country }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            @php
                                $ticketTypes = [];
                                foreach($customer->orders as $order) {
                                    foreach($order->tickets as $ticket) {
                                        $typeName = $ticket->ticketType->name;
                                        if (!isset($ticketTypes[$typeName])) {
                                            $ticketTypes[$typeName] = 0;
                                        }
                                        $ticketTypes[$typeName]++;
                                    }
                                }
                            @endphp

                            @foreach($ticketTypes as $name => $count)
                                <span class="inline-block bg-gray-100 dark:bg-gray-700 rounded-full px-3 py-1 text-xs font-semibold text-gray-700 dark:text-gray-300 mr-1 mb-1">
                                    {{ $name }} ({{ $count }})
                                </span>
                            @endforeach
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                <span class="text-gray-500 dark:text-gray-400">
                                    {{ __('No attendees found') }}
                                </span>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if(!$customers->isEmpty())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $customers->links() }}
        </div>
        @endif
    </div>
</div>
