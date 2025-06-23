<div class="py-16">
    <div class="px-10">
        <div class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-semibold text-gray-800 dark:text-gray-200">
                    {{ __('Organization Media') }}
                </h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ __('Upload logo and background images') }}
                </p>
            </div>
            @role('superadmin')
            <x-ui.back-to-button route="organizations.index" text="{{ __('Back to organizations') }}"/>
            @endrole
        </div>

        @if (session()->has('message'))
            <x-ui.flash-message/>
        @endif
        @error('favicon')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm rounded-xl transition-colors duration-300 ease-in-out hover:border-indigo-800">
            <form wire:submit.prevent="uploadMedia" class="p-8 space-y-8">
                @csrf
                <div class="flex flex-col gap-6">
                    <!-- Favicon Section -->
                    <div>
                        <div class="inline-flex items-center p-1 px-3 mb-6 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-full shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                            </svg>
                            {{ __('Favicon') }}
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Current Favicon -->
                            <div class="col-span-1 flex flex-col items-center justify-center">
                                <div class="flex flex-col items-center">
                                    @if($organization->favicon_url)
                                        <div class="relative group">
                                            <img src="{{ $organization->favicon_url }}?{{ time() }}"
                                                 alt="Favicon"
                                                 class="w-16 h-16 rounded-lg object-contain border border-gray-200 dark:border-gray-700">
                                            <button wire:click="removeMedia('favicon')"
                                                    class="absolute top-0 right-0 -mt-2 -mr-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300 ease-in-out">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                            {{ __('Current favicon') }}
                                        </p>
                                    @else
                                        <div class="w-16 h-16 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                                            </svg>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                            {{ __('No favicon uploaded') }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Favicon Upload -->
                            <div class="col-span-2">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            {{ __('Upload New Favicon') }}
                                        </label>
                                        <livewire:improved-dropzone
                                            wire:model="faviconInput"
                                            :rules="(new \App\Http\Requests\OrganizationMediaRequest())->rules()['favicon']"
                                            :messages="(new \App\Http\Requests\OrganizationMediaRequest())->messages()"
                                            :multiple="false"
                                            key="favicon"
                                            accentColor="#138eff"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="border-t border-gray-200 dark:border-gray-700 my-0">

                    <!-- Logo Section -->
                    <div>
                        <div class="inline-flex items-center p-1 px-3 mb-6 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 rounded-full shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ __('Organization Logo') }}
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Current Logo -->
                            <div class="col-span-1 flex flex-col items-center justify-center">
                                <div class="flex flex-col items-center">
                                    @if($organization->logo_url)
                                        <div class="relative group">
                                            <img src="{{ $organization->logo_url }}?{{ time() }}"
                                                 alt="Organization Logo"
                                                 class="w-32 rounded-lg object-contain border border-gray-200 dark:border-gray-700">
                                            <button wire:click="removeMedia('logo')"
                                                    class="absolute top-0 right-0 -mt-2 -mr-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300 ease-in-out">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                            {{ __('Current logo') }}
                                        </p>
                                    @else
                                        <div class="w-32 h-32 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                            {{ __('No logo uploaded') }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Logo Upload -->
                            <div class="col-span-2">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            {{ __('Upload New Logo') }}
                                        </label>
                                        <livewire:improved-dropzone
                                            wire:model="logoInput"
                                            :rules="(new \App\Http\Requests\OrganizationMediaRequest())->rules()['logo']"
                                            :messages="(new \App\Http\Requests\OrganizationMediaRequest())->messages()"
                                            :multiple="false"
                                            key="logo"
                                            accentColor="#737cff"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="border-t border-gray-200 dark:border-gray-700 my-0">

                    <!-- Background Section -->
                    <div>
                        <div class="inline-flex items-center p-1 px-3 mb-6 bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 rounded-full shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ __('Background Image') }}
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Current Background -->
                            <div class="col-span-1 flex flex-col items-center justify-center">
                                <div class="flex flex-col items-center">
                                    @if($organization->background_url)
                                        <div class="relative group w-full">
                                            <img src="{{ $organization->background_url }}?{{ time() }}"
                                                 alt="Background Image"
                                                 class="w-full h-32 rounded-lg object-cover border border-gray-200 dark:border-gray-700">
                                            <button wire:click="removeMedia('background')"
                                                    class="absolute top-0 right-0 -mt-2 -mr-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300 ease-in-out">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                            {{ __('Current background') }}
                                        </p>
                                    @else
                                        <div class="w-full h-32 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                            {{ __('No background uploaded') }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Background Upload -->
                            <div class="col-span-2">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            {{ __('Upload New Background') }}
                                        </label>
                                        <livewire:improved-dropzone
                                            wire:model="backgroundInput"
                                            :rules="(new \App\Http\Requests\OrganizationMediaRequest())->rules()['background']"
                                            :messages="(new \App\Http\Requests\OrganizationMediaRequest())->messages()"
                                            :multiple="false"
                                            key="background"
                                            accentColor="#ce6cff"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="flex justify-end pt-6">
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors duration-300 ease-in-out flex items-center"
                    >
                        <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ __('Save Changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
