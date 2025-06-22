<div class="mx-auto max-w-10xl p-4 sm:p-6 lg:p-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <div class="flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                </svg>
                <h1 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">{{ __('Discount Codes') }}</h1>
            </div>
            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                {{ __('Manage all discount codes.') }}
            </p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
            @can('discount-codes.create')
            <x-ui.button href="{{ route('discount-codes.create') }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('New Discount Code') }}
            </x-ui.button>
            @endcan
        </div>
    </div>

    <!-- Filters -->
    <div class="my-4 flex items-center gap-6">
        <!-- Search -->
        <div class="relative w-1/3">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 103 10.5a7.5 7.5 0 0013.15 6.15z"/>
                </svg>
            </div>
            <input type="text"
                   wire:model.live.debounce.150ms="search"
                   placeholder="{{ __('Search discount codes or events...') }}"
                   class="pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 p-2 text-sm w-full"
            />
        </div>

        <!-- Status filter -->
        <x-ui.forms.select wire:model.live="statusFilter">
            <option value="all">{{ __('All Statuses') }}</option>
            <option value="active">{{ __('Active') }}</option>
            <option value="upcoming">{{ __('Upcoming') }}</option>
            <option value="expired">{{ __('Expired') }}</option>
            <option value="event_past">{{ __('Event Past') }}</option>
            <option value="limit_reached">{{ __('Limit Reached') }}</option>
            @if($includeDeleted)
                <option value="deleted">{{ __('Deleted') }}</option>
            @endif
        </x-ui.forms.select>

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

        <!-- Include deleted -->
        <label class="flex items-center ml-auto">
            <input type="checkbox" wire:model.live="includeDeleted" class="form-checkbox text-indigo-600">
            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('Include deleted') }}</span>
        </label>
    </div>

    @if (session()->has('message'))
        <x-ui.flash-message
            :message="session('message')"
            :type="session('message_type', 'success')"
        />
    @endif

    <!-- Discount Codes Table -->
    <div class="overflow-auto shadow-sm sm:rounded-lg bg-white dark:bg-gray-800">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
            <tr>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300 transition-colors duration-300 ease-in-out cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700" wire:click="sortBy('code')">
                    <div class="flex items-center">
                        {{ __('Code') }}
                        <span class="ml-1 text-xs" style="visibility: {{ $sortField == 'code' ? 'visible' : 'hidden' }};">
                            {{ $sortDirection == 'asc' ? '↑' : '↓' }}
                        </span>
                    </div>
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                    {{ __('Event') }}
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                    {{ __('Discount') }}
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                    {{ __('Usage') }}
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-300 ease-in-out" wire:click="sortBy('start_date')">
                    <div class="flex items-center">
                        {{ __('Start Date') }}
                        <span class="ml-1 text-xs" style="visibility: {{ $sortField == 'start_date' ? 'visible' : 'hidden' }};">
            {{ $sortDirection == 'asc' ? '↑' : '↓' }}
        </span>
                    </div>
                </th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-300 ease-in-out" wire:click="sortBy('end_date')">
                    <div class="flex items-center">
                        {{ __('End Date') }}
                        <span class="ml-1 text-xs" style="visibility: {{ $sortField == 'end_date' ? 'visible' : 'hidden' }};">
            {{ $sortDirection == 'asc' ? '↑' : '↓' }}
        </span>
                    </div>
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
            @forelse($discountCodes as $discountCode)
                <tr wire:key="discount-code-{{ $discountCode->id }}" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition-colors duration-300 ease-in-out">
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                            <code>{{ $discountCode->code }}</code>
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-white">
                            @if($discountCode->event)
                                {{ Str::limit($discountCode->event->name, 30) }}
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-white">
                            @if($discountCode->discount_percent)
                                {{ $discountCode->discount_percent }}%
                            @elseif($discountCode->discount_fixed_cents)
                                €{{ number_format($discountCode->discount_fixed_cents / 100, 2) }}
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <x-ui.usage-bar :progress="$discountCode->orders_count" :max="$discountCode->max_uses" />
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                        {{ $discountCode->start_date ? $discountCode->start_date->format('M j, Y') : '-' }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                        {{ $discountCode->end_date ? $discountCode->end_date->format('M j, Y') : '-' }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        @php
                            $badge = match(true) {
                                $discountCode->trashed() => ['color' => 'red', 'text' => 'Deleted'],
                                $discountCode->event && $discountCode->event->date->isPast() => ['color' => 'gray', 'text' => 'Event Past'],
                                $discountCode->max_uses && $discountCode->orders_count >= $discountCode->max_uses => ['color' => 'red', 'text' => 'Limit Reached'],
                                $discountCode->end_date && $discountCode->end_date->isPast() => ['color' => 'gray', 'text' => 'Expired'],
                                $discountCode->start_date && $discountCode->start_date->isFuture() => ['color' => 'blue', 'text' => 'Upcoming'],
                                default => ['color' => 'green', 'text' => 'Active']
                            };
                        @endphp
                        <x-ui.badge :color="$badge['color']" :text="$badge['text']" size="sm"/>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end gap-2">
                            @if($discountCode->trashed())
                                @can('discount-codes.delete')
                                <button wire:click="restoreDiscountCode({{ $discountCode->id }})"
                                        class="p-1 text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors duration-300 ease-in-out hover:cursor-pointer"
                                        title="{{ __('Restore') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                                    </svg>
                                </button>
                                <x-ui.delete-button
                                    type="forcedelete"
                                    method="forceDeleteDiscountCode"
                                    :args="[$discountCode->id]"
                                    confirmation="⚠️ Are you sure you want to permanently delete this discount code?"
                                    title="{{ __('Delete permanently') }}"
                                    disabledTitle="Cannot permanently delete discount codes that have been used"
                                    :disabled="$discountCode->orders_count + $discountCode->temporary_orders_count > 0"
                                />
                                @endcan
                            @else
                                @can('discount-codes.update')
                                    <x-ui.edit-button
                                        route="discount-codes.update"
                                        :routeParams="['discountCode' => $discountCode]"
                                        :disabled="$discountCode->orders_count + $discountCode->temporary_orders_count > 0"
                                        :title="__('Edit')"
                                        :disabledTitle="__('Cannot edit used discount code')"
                                    />
                                @endcan
                                @can('discount-codes.delete')
                                <x-ui.delete-button
                                    type="delete"
                                    method="deleteDiscountCode"
                                    :args="[$discountCode->id]"
                                    confirmation="Are you sure you want to delete this discount code?"
                                    title="{{ __('Delete') }}"
                                />
                                @endcan
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-sm text-gray-500 dark:text-gray-300 text-center">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            {{ __('No discount codes found') }}
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $discountCodes->links() }}
    </div>
</div>
