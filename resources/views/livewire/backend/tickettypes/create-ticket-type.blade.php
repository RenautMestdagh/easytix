<div class="py-16">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-semibold text-gray-800 dark:text-gray-200 transition-all duration-300">
                    {{ __('Create New Ticket Type') }}
                </h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ __('Add a new ticket type for: ') }} <span class="font-medium">{{ $event->name }}</span>
                </p>
            </div>
            <a href="{{ route('ticket-types.index', $event) }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 flex items-center transition-all duration-300 ease-in-out">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ __('Back to ticket types') }}
            </a>
        </div>

        @if (session()->has('message'))
            <x-ui.flash-message
                :message="session('message')"
                :type="session('message_type', 'success')"
            />
        @endif

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm rounded-xl transition-all duration-300 ease-in-out hover:border-indigo-800">
            <div class="p-8">
                <form wire:submit.prevent="store">
                    <div class="space-y-8">
                        <!-- Ticket Type Information -->
                        <div>
                            <div class="inline-flex items-center p-1 px-3 mb-6 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 rounded-full shadow-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                                </svg>
                                {{ __('Ticket Details') }}
                            </div>

                            <x-ui.forms.group label="Ticket Name" for="name" error="name">
                                <x-ui.forms.input
                                    wire:model.lazy="name"
                                    name="name"
                                    placeholder="Enter ticket type name"
                                    error="{{ $errors->has('name') }}"
                                    class="rounded-xl shadow-md focus:ring-2 focus:ring-indigo-500"
                                />
                            </x-ui.forms.group>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <x-ui.forms.group label="Price (€)" for="price_euros" error="price_euros">
                                    <x-ui.forms.input
                                        type="number"
                                        wire:model.lazy="price_euros"
                                        name="price_euros"
                                        placeholder="Price in € (e.g. 19.99)"
                                        step="any"
                                        error="{{ $errors->has('price_euros') }}"
                                        class="rounded-xl shadow-md focus:ring-2 focus:ring-indigo-500"
                                    />
                                </x-ui.forms.group>

                                @php
                                    $alreadyAvailableTickets = $this->event->ticketTypes->sum('available_quantity');
                                    $this->event->max_capacity - $alreadyAvailableTickets;
                                    $placeholder = $event->max_capacity ? 'Remaining: ' . ($event->max_capacity - $alreadyAvailableTickets) : "Leave empty for unlimited";
                                @endphp
                                <x-ui.forms.group label="Available Quantity" for="available_quantity" error="available_quantity">
                                    <x-ui.forms.input
                                        type="number"
                                        wire:model.lazy="available_quantity"
                                        name="available_quantity"
                                        placeholder="{{$placeholder}}"
                                        min="1"
                                        error="{{ $errors->has('available_quantity') }}"
                                        class="rounded-xl shadow-md focus:ring-2 focus:ring-indigo-500"
                                    />
                                </x-ui.forms.group>
                            </div>
                        </div>

                        <!-- Publishing Options -->
                        <div class="pt-4">
                            <div class="inline-flex items-center p-1 px-3 mb-6 bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 rounded-full shadow-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ __('Publishing Options') }}
                            </div>

                            <div class="space-y-4">
                                <!-- Publish Status Dropdown -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('Publish Status') }}
                                    </label>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                        {{ __('Control when this ticket type will be visible to attendees') }}
                                    </p>

                                    <x-ui.forms.group label="Publish Option" for="publish_option" :error="$publish_option !== 'schedule' ? 'publish_at' : null">
                                        <select wire:model.live="publish_option" id="publish_option" class="block w-full rounded-md p-2 border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
                                            <option value="publish_now">{{ __('Publish Immediately') }}</option>
                                            @if(!$event->is_published)
                                                <option value="with_event">{{ __('Publish with Event') }}</option>
                                            @endif
                                            <option value="schedule">{{ __('Schedule for Later') }}</option>
                                            <option value="draft">{{ __('Save as Draft') }}</option>
                                        </select>
                                    </x-ui.forms.group>
                                </div>

                                <!-- Publish Immediately Confirmation -->
                                @if($publish_option === 'publish_now')
                                    <div class="bg-green-50 dark:bg-green-900/10 p-4 rounded-lg border border-green-100 dark:border-green-900/20">
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span class="text-sm font-medium text-green-700 dark:text-green-400">
                                                {{ __('This ticket type will be published immediately when saved') }}
                                            </span>
                                        </div>
                                    </div>
                                @endif

                                <!-- Schedule for Later Datetime Picker -->
                                @if($publish_option === 'schedule')
                                    <div class="space-y-2">
                                        <x-ui.forms.group label="Publish At" for="publish_at" error="publish_at">
                                            <x-ui.forms.input
                                                type="datetime-local"
                                                wire:model.lazy="publish_at"
                                                name="publish_at"
                                                error="{{ $errors->has('publish_at') }}"
                                                class="rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500"
                                            />
                                        </x-ui.forms.group>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ __('Ticket type will automatically publish at the specified date/time') }}
                                        </p>
                                    </div>
                                @endif

                                <!-- Publish with Event Notice -->
                                @if($publish_option === 'with_event')
                                    <div class="bg-blue-50 dark:bg-blue-900/10 p-4 rounded-lg border border-blue-100 dark:border-blue-900/20">
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            </svg>
                                            <span class="text-sm font-medium text-blue-700 dark:text-blue-400">
                                                {{ __('Ticket type will publish when the event is published') }}
                                            </span>
                                        </div>
                                    </div>
                                @endif

                                <!-- Draft Notice -->
                                @if($publish_option === 'draft')
                                    <div class="bg-gray-50 dark:bg-gray-700/30 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-500">
                                                {{ __('Ticket type will not be visible to attendees until published') }}
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-end pt-8 space-x-4">
                            <x-ui.button type="button" variant="secondary" href="{{ route('ticket-types.index', $event) }}">
                                {{ __('Cancel') }}
                            </x-ui.button>
                            <x-ui.button type="submit" variant="indigo">
                                {{ __('Create Ticket Type') }}
                            </x-ui.button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
