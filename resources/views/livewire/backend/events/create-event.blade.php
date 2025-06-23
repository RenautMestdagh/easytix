<div class="py-16">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

        <div class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-semibold text-gray-800 dark:text-gray-200">
                    {{ __('Create New Event') }}
                </h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ __('Set up a new event for your organization') }}
                </p>
            </div>
            <x-ui.back-to-button route="events.index" text="{{ __('Back to events') }}"/>
        </div>

        @if (session()->has('message'))
            <x-ui.flash-message/>
        @endif

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm rounded-xl transition-colors duration-300 ease-in-out hover:border-indigo-800">
            <div class="p-8">
                <form wire:submit="store">
                    <div class="space-y-8">
                        <!-- Event Information -->
                        <div>
                            <div class="inline-flex items-center p-1 px-3 mb-6 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 rounded-full shadow-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                {{ __('Event Details') }}
                            </div>

                            <x-ui.forms.group label="Event Name" for="name" error="name">
                                <x-ui.forms.input
                                    wire:model.lazy="name"
                                    name="name"
                                    placeholder="Enter event name"
                                    error="{{ $errors->has('name') }}"
                                    class="rounded-xl shadow-md focus:ring-2 focus:ring-indigo-500"
                                />
                            </x-ui.forms.group>

                            <x-ui.forms.group label="Description" for="description" error="description">
                                <x-ui.forms.textarea
                                    wire:model.lazy="description"
                                    name="description"
                                    placeholder="Enter event description"
                                    rows="4"
                                    error="{{ $errors->has('description') }}"
                                    class="rounded-xl shadow-md focus:ring-2 focus:ring-indigo-500"
                                />
                            </x-ui.forms.group>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <x-ui.forms.group label="Venue" for="venue_id" error="venue_id">
                                    <div class="flex gap-4">
                                        <div class="flex-1 flex flex-col justify-center">
                                            @if($venue_id && $venue = \App\Models\Venue::find($venue_id))
                                                <div class="flex justify-between">
                                                    <div class="flex flex-col items-start">
                                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                                            {{ Str::limit($venue->name, 25, '...') }}
                                                        </p>
                                                        @if($venue->max_capacity)
                                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                                ({{ __('Capacity') }}: {{ $venue->max_capacity }})
                                                            </p>
                                                        @endif
                                                    </div>
                                                    <x-ui.cross-button wire:click="$dispatch('venueSelected', { venueId: null, venueName: '' })" />
                                                </div>

                                            @else
                                                <p class="text-gray-600 dark:text-gray-400">
                                                    {{ __('No venue selected') }}
                                                </p>
                                            @endif
                                        </div>

                                        @livewire('modals.venue-picker-modal', [
                                            'selectedVenueId' => $venue_id,
                                            'showTriggerButton' => true
                                        ], key('venue-picker-'.$venue_id))
                                    </div>
                                </x-ui.forms.group>

                                <div>
                                    <x-ui.forms.group label="Max Capacity" for="max_capacity" error="max_capacity" class="h-full">
                                        <div class="flex items-center gap-4">
                                            @if(!$use_venue_capacity)
                                            <div class="flex-1">
                                                <x-ui.forms.input
                                                    type="number"
                                                    wire:model.lazy="max_capacity"
                                                    name="max_capacity"
                                                    placeholder="Unlimited"
                                                    min="1"
                                                    error="{{ $errors->has('max_capacity') }}"
                                                    class="rounded-xl shadow-md focus:ring-2 focus:ring-indigo-500"
                                                />
                                            </div>
                                            @endif
                                            @if($venue_id)
                                                <div class="flex items-center @if($use_venue_capacity) mt-3 @endif">
                                                    <x-ui.forms.checkbox
                                                        wire:model.live="use_venue_capacity"
                                                        id="use_venue_capacity"
                                                        name="use_venue_capacity"
                                                        class="rounded focus:ring-indigo-500"
                                                    />
                                                    <label for="use_venue_capacity" class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                                        {{ __('Use venue capacity') }}
                                                    </label>
                                                </div>
                                            @endif
                                        </div>
                                    </x-ui.forms.group>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <x-ui.forms.group label="Date & Time" for="date" error="date">
                                    <x-ui.forms.input
                                        type="datetime-local"
                                        wire:model.lazy="date"
                                        name="date"
                                        error="{{ $errors->has('date') }}"
                                        class="rounded-xl shadow-md focus:ring-2 focus:ring-indigo-500"
                                    />
                                </x-ui.forms.group>
                            </div>
                        </div>

                        <!-- Images Section -->
                        <div class="pt-4">
                            <div class="inline-flex items-center p-1 px-3 mb-6 bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 rounded-full shadow-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ __('Event Images') }}
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <!-- Event Image Upload -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('Event Image') }}
                                    </label>
                                    <livewire:improved-dropzone
                                        wire:model="event_imageInput"
                                        :rules="(new \App\Http\Requests\Event\EventRequest())->rules()['event_image']"
                                        :messages="(new \App\Http\Requests\Event\EventRequest())->messages()"
                                        :multiple="false"
                                        key="event_image"
                                        accentColor="#138eff"
                                    />
                                </div>

                                <!-- Header Image Upload -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('Header Image') }}
                                    </label>
                                    <livewire:improved-dropzone
                                        wire:model="header_imageInput"
                                        :rules="(new \App\Http\Requests\Event\EventRequest())->rules()['header_image']"
                                        :messages="(new \App\Http\Requests\Event\EventRequest())->messages()"
                                        :multiple="false"
                                        key="header_image"
                                        accentColor="#737cff"
                                    />
                                </div>

                                <!-- Background Image Upload -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('Background Image') }}
                                    </label>
                                    <livewire:improved-dropzone
                                        wire:model="background_imageInput"
                                        :rules="(new \App\Http\Requests\Event\EventRequest())->rules()['background_image']"
                                        :messages="(new \App\Http\Requests\Event\EventRequest())->messages()"
                                        :multiple="false"
                                        key="background_image"
                                        accentColor="#ce6cff"
                                    />
                                </div>
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
                                        {{ __('Control when this event will be visible to attendees') }}
                                    </p>

                                    <x-ui.forms.group label="Publish Option" for="publish_option" error="publish_at">
                                        <select wire:model.live="publish_option" id="publish_option" class="block w-full rounded-md p-2 border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
                                            <option value="publish_now">{{ __('Publish Immediately') }}</option>
                                            <option value="schedule">{{ __('Schedule for Later') }}</option>
                                            <option value="unlisted">{{ __('Save as Unlisted') }}</option>
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
                                                {{ __('This event will be published immediately when saved') }}
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
                                            {{ __('Event will automatically publish at the specified date/time') }}
                                        </p>
                                    </div>
                                @endif

                                <!-- Unlisted Notice -->
                                @if($publish_option === 'unlisted')
                                    <div class="bg-gray-50 dark:bg-gray-700/30 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-500">
                                                {{ __('Event will not be visible to attendees until published') }}
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-end pt-8 space-x-4">
                            <x-ui.button type="button" variant="secondary" wire:click="cancel">
                                {{ __('Cancel') }}
                            </x-ui.button>
                            <x-ui.button type="submit" variant="indigo">
                                {{ __('Create Event') }}
                            </x-ui.button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
