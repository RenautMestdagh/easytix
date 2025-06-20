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
            <x-ui.button href="{{ route('discount-codes.create') }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('New Discount Code') }}
            </x-ui.button>
        </div>
    </div>

    <!-- Filters -->
    <div class="my-4 flex items-center gap-4">
        <!-- Search -->
        <div class="relative w-1/3">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 103 10.5a7.5 7.5 0 0013.15 6.15z"/>
                </svg>
            </div>
            <input type="text"
                   wire:model.live.debounce.250ms="search"
                   placeholder="{{ __('Search discount codes or events...') }}"
                   class="pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 p-2 text-sm w-full"
            />
        </div>

        <!-- Event filter -->
        <x-ui.forms.select wire:model.live="selectedEvent">
            <option value="">{{ __('All Events') }}</option>
            @foreach($events as $event)
                <option value="{{ $event->id }}">{{ Str::limit($event->name, 30) }}</option>
            @endforeach
        </x-ui.forms.select>

        <!-- Status filter -->
        <x-ui.forms.select wire:model.live="statusFilter">
            <option value="all">{{ __('All Statuses') }}</option>
            <option value="active">{{ __('Active') }}</option>
            <option value="event_past">{{ __('Event Past') }}</option>
            <option value="limit_reached">{{ __('Limit Reached') }}</option>
            @if($includeDeleted)
                <option value="deleted">{{ __('Deleted') }}</option>
            @endif
        </x-ui.forms.select>

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
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" wire:click="sortBy('code')">
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
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" wire:click="sortBy('created_at')">
                    <div class="flex items-center">
                        {{ __('Created') }}
                        <span class="ml-1 text-xs" style="visibility: {{ $sortField == 'created_at' ? 'visible' : 'hidden' }};">
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
                <tr wire:key="discount-code-{{ $discountCode->id }}" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition duration-150">
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
                        <div class="flex items-center">
                            <div class="w-20 mr-2">
                                <div class="h-2 bg-gray-200 rounded-full dark:bg-gray-600">
                                    <div
                                        class="h-2 rounded-full
                                            @if(!$discountCode->max_uses && !$discountCode->orders_count)
                                            @elseif(!$discountCode->max_uses) bg-green-500
                                            @elseif(($discountCode->orders_count / $discountCode->max_uses * 100) >= 90) bg-red-500
                                            @elseif(($discountCode->orders_count / $discountCode->max_uses * 100) >= 50) bg-yellow-500
                                            @else bg-green-500 @endif
                                        "
                                        style="
                                            @if(!$discountCode->max_uses) width: 100%
                                            @else width: {{ min(100, ($discountCode->orders_count / $discountCode->max_uses * 100)) }}% @endif
                                        "
                                    ></div>
                                </div>
                            </div>
                            <span class="text-xs text-gray-600 dark:text-gray-300">
                                {{ $discountCode->orders_count }}/{{ $discountCode->max_uses ?? '∞' }}
                            </span>
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                        {{ $discountCode->created_at->format('M j, Y') }}
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        @if($discountCode->trashed())
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400">
                                {{ __('Deleted') }}
                            </span>
                        @elseif($discountCode->event && $discountCode->event->date->format('Y-m-d') < now()->format('Y-m-d'))
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400">
                                {{ __('Event Past') }}
                            </span>
                        @elseif($discountCode->max_uses && $discountCode->orders_count >= $discountCode->max_uses)
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400">
                                {{ __('Limit Reached') }}
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                {{ __('Active') }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end gap-2">
                            @if($discountCode->trashed())
                                <button wire:click="restoreDiscountCode({{ $discountCode->id }})"
                                        class="p-1 text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors hover:cursor-pointer"
                                        title="{{ __('Restore') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                                    </svg>
                                </button>
                                @can('discount-codes.delete')
                                <x-ui.delete-button
                                    type="forcedelete"
                                    method="forceDeleteDiscountCode"
                                    :args="[$discountCode->id]"
                                    confirmation="⚠️ Are you sure you want to permanently delete this discount code?"
                                    title="{{ __('Delete permanently') }}"
                                    disabledTitle="Cannot permanently delete discount codes that have been used"
                                    :disabled="$discountCode->getAllUsesCount() > 0"
                                />
                                @endcan
                            @else
                                <button
                                    @if($discountCode->getAllUsesCount() === 0)
                                        wire:navigate
                                        onclick="window.location.href='{{ route('discount-codes.update', $discountCode) }}'"
                                    @endif
                                    @disabled($discountCode->getAllUsesCount() > 0)
                                    class="p-1 rounded-full transition-colors
                                    @if($discountCode->getAllUsesCount() > 0)
                                        opacity-50 cursor-not-allowed
                                    @else
                                        text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:cursor-pointer
                                    @endif"
                                    title="{{ $discountCode->getAllUsesCount() > 0 ? __('Cannot edit used discount code') : __('Edit') }}"
                                    aria-label="{{ __('Edit discount code') }}"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                    </svg>
                                </button>
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
