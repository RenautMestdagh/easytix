<div class="py-16">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        @if (session()->has('message'))
            <x-ui.flash-message
                :message="session('message')"
                :type="session('message_type', 'success')"
            />
        @endif

        <div class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-semibold text-gray-800 dark:text-gray-200 transition-all duration-300">
                    {{ __('New Venue') }}
                </h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ __('Create a new venue location') }}
                </p>
            </div>
            <a href="{{ route('venues.index') }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 flex items-center transition-all duration-300 ease-in-out">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ __('Back to venues') }}
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm rounded-xl transition-all duration-300 ease-in-out hover:border-indigo-800">
            <div class="p-8">
                <form wire:submit.prevent="save">
                    <div class="space-y-8">
                        <!-- Venue Information -->
                        <div>
                            <div class="inline-flex items-center p-1 px-3 mb-6 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 rounded-full shadow-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                </svg>
                                {{ __('Venue Details') }}
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <x-ui.forms.group label="Venue Name" for="name" error="name">
                                    <x-ui.forms.input
                                        wire:model.lazy="name"
                                        name="name"
                                        placeholder="Enter venue name"
                                        error="{{ $errors->has('name') }}"
                                        class="rounded-xl shadow-md focus:ring-2 focus:ring-indigo-500"
                                    />
                                </x-ui.forms.group>

                                <x-ui.forms.group label="Venue Capacity" for="max_capacity" error="max_capacity">
                                    <x-ui.forms.input
                                        wire:model.lazy="max_capacity"
                                        name="max_capacity"
                                        placeholder="Enter venue capacity"
                                        error="{{ $errors->has('max_capacity') }}"
                                        class="rounded-xl shadow-md focus:ring-2 focus:ring-indigo-500"
                                    />
                                </x-ui.forms.group>
                            </div>


                            <x-ui.forms.group label="Location Coordinates" for="latitude">
                                <div class="flex flex-col space-y-2">
                                    <div class="flex gap-4">
                                        <!-- Latitude Input -->
                                        <div class="flex-1">
                                            <div class="flex rounded-xl border overflow-hidden shadow-md @error('latitude') border-red-500 @enderror">
                                                <div class="flex items-center justify-center px-3 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                                    N
                                                </div>
                                                <x-ui.forms.input
                                                    wire:model.lazy="latitude"
                                                    name="latitude"
                                                    id="latitude"
                                                    placeholder="51.5074"
                                                    class="border-0 rounded-none focus:ring-0 w-full"
                                                />
                                            </div>
                                            @error('latitude')
                                            <x-ui.forms.error error="latitude" />
                                            @enderror
                                        </div>

                                        <!-- Longitude Input -->
                                        <div class="flex-1">
                                            <div class="flex rounded-xl border overflow-hidden shadow-md @error('longitude') border-red-500 @enderror">
                                                <div class="flex items-center justify-center px-3 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 border-r border-gray-300 dark:border-gray-600">
                                                    E
                                                </div>
                                                <x-ui.forms.input
                                                    wire:model.lazy="longitude"
                                                    name="longitude"
                                                    id="longitude"
                                                    placeholder="2.1278"
                                                    class="border-0 rounded-none focus:ring-0 w-full"
                                                />
                                            </div>
                                            @error('longitude')
                                                <x-ui.forms.error error="longitude" />
                                            @enderror
                                        </div>
                                    </div>

                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Enter decimal degrees (e.g. 51.5074, 0.1278)
                                    </p>
                                </div>
                            </x-ui.forms.group>
                        </div>

                        <div class="flex items-center justify-end pt-8 space-x-4">
                            <x-ui.button type="submit" variant="indigo">
                                {{ __('Create Venue') }}
                            </x-ui.button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
