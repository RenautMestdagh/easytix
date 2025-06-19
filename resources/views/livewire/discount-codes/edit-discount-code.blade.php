<div class="py-16">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-semibold text-gray-800 dark:text-gray-200 transition-all duration-300">
                    {{ __('Edit Discount Code') }}
                </h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ __('Update discount code details') }}
                </p>
            </div>
            <a href="{{ route('discount-codes.index') }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 flex items-center transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ __('Back to discount codes') }}
            </a>
        </div>

        @if (session()->has('message'))
            <x-ui.flash-message
                :message="session('message')"
                :type="session('message_type', 'success')"
            />
        @endif

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm rounded-xl transition-all duration-300 hover:border-indigo-800">
            <div class="p-8">
                <form wire:submit.prevent="update">
                    @csrf
                    <div class="space-y-8">
                        <!-- Discount Code Information -->
                        <div>
                            <div class="inline-flex items-center p-1 px-3 mb-6 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 rounded-full shadow-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185zM9.75 9h.008v.008H9.75V9zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm4.125 4.5h.008v.008h-.008V13.5zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                                {{ __('Discount Code Details') }}
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <x-ui.forms.group label="Discount Code" for="code" error="code">
                                    <div class="flex gap-2">
                                        <x-ui.forms.input
                                            wire:model.lazy="code"
                                            name="code"
                                            placeholder="Enter discount code"
                                            error="{{ $errors->has('code') }}"
                                            class="rounded-xl shadow-md focus:ring-2 focus:ring-indigo-500 flex-1"
                                        />
                                        <x-ui.button
                                            type="button"
                                            variant="gray"
                                            wire:click="generateCode"
                                            class="whitespace-nowrap"
                                        >
                                            {{ __('Generate') }}
                                        </x-ui.button>
                                    </div>
                                </x-ui.forms.group>

                                <x-ui.forms.group label="Event (Optional)" for="event_id" error="event_id">
                                    <select
                                        wire:model="event_id"
                                        id="event_id"
                                        class="block w-full rounded-md p-2 border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600"
                                    >
                                        <option value=""></option>
                                        @foreach($this->events as $event)
                                            <option value="{{ $event->id }}" @selected($event->id == $event_id)>
                                                {{ $event->name }} ({{ $event->date->format('M j, Y') }})
                                            </option>
                                        @endforeach
                                    </select>
                                </x-ui.forms.group>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-6">
                                <x-ui.forms.group label="Max Uses (Optional)" for="max_uses" error="max_uses">
                                    <x-ui.forms.input
                                        type="number"
                                        wire:model.lazy="max_uses"
                                        name="max_uses"
                                        placeholder="Leave empty for unlimited uses"
                                        min="1"
                                        error="{{ $errors->has('max_uses') }}"
                                        class="rounded-xl shadow-md focus:ring-2 focus:ring-indigo-500"
                                    />
                                </x-ui.forms.group>
                            </div>
                        </div>

                        <!-- Discount Type Selection -->
                        <div class="pt-4">
                            <div class="inline-flex items-center p-1 px-3 mb-6 bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 rounded-full shadow-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                </svg>
                                {{ __('Discount Type') }}
                            </div>

                            <div class="space-y-4">
                                <div class="flex gap-4">
                                    <label class="inline-flex items-center">
                                        <input
                                            type="radio"
                                            wire:model.live="discount_type"
                                            value="percent"
                                            class="form-radio h-4 w-4 text-indigo-600 transition duration-150 ease-in-out"
                                        >
                                        <span class="ml-2 text-gray-700 dark:text-gray-300">{{ __('Percentage Discount') }}</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input
                                            type="radio"
                                            wire:model.live="discount_type"
                                            value="fixed"
                                            class="form-radio h-4 w-4 text-indigo-600 transition duration-150 ease-in-out"
                                        >
                                        <span class="ml-2 text-gray-700 dark:text-gray-300">{{ __('Fixed Amount Discount') }}</span>
                                    </label>
                                </div>

                                @if($discount_type === 'percent')
                                    <x-ui.forms.group label="Discount Percentage" for="discount_percent" error="discount_percent">
                                        <div class="relative">
                                            <x-ui.forms.input
                                                type="number"
                                                wire:model.lazy="discount_percent"
                                                name="discount_percent"
                                                placeholder="Enter percentage (1-100)"
                                                min="1"
                                                max="100"
                                                error="{{ $errors->has('discount_percent') }}"
                                                class="rounded-xl shadow-md focus:ring-2 focus:ring-indigo-500 pl-10"
                                            />
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500">%</span>
                                            </div>
                                        </div>
                                    </x-ui.forms.group>
                                @else
                                    <x-ui.forms.group label="Fixed Discount Amount (€)" for="discount_fixed_euros" error="discount_fixed_euros">
                                        <x-ui.forms.input
                                            type="text"
                                            wire:model.lazy="discount_fixed_euros"
                                            name="discount_fixed_euros"
                                            placeholder="Enter amount (e.g. 10.50)"
                                            error="{{ $errors->has('discount_fixed_euros') }}"
                                            class="rounded-xl shadow-md focus:ring-2 focus:ring-indigo-500"
                                        />
                                    </x-ui.forms.group>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-end pt-8 space-x-4">
                            <x-ui.button type="button" variant="gray" href="{{ route('discount-codes.index') }}">
                                {{ __('Cancel') }}
                            </x-ui.button>
                            <x-ui.button type="submit" variant="indigo">
                                {{ __('Update Discount Code') }}
                            </x-ui.button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
