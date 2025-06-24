<div class="py-16">
    <div class="px-10">

        <div class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-semibold text-gray-800 dark:text-gray-200">
                    {{ __('Edit Organization') }}
                </h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ __('Update organization details') }}
                </p>
            </div>
            @role('superadmin')
            <x-ui.back-to-button route="organizations.index" text="{{ __('Back to organizations') }}"/>
            @endrole
        </div>

        @if (session()->has('message'))
            <x-ui.flash-message/>
        @endif

        <!-- Organization and Users Section -->
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm rounded-xl transition-colors duration-300 ease-in-out hover:border-indigo-800">
            <div class="p-8 space-y-8">

                <!-- Form: Organization Details -->
                <form wire:submit.prevent="save">
                    <div class="flex justify-between">
                        <div class="flex items-center gap-6">
                            <div class="inline-flex items-center p-1 px-3 mb-6 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 rounded-full shadow-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                {{ __('Organization Details') }}
                            </div>
                        </div>

                        <div class="flex gap-2">

                            @if ($saveButtonVisible)
                                <button type="submit" class="px-6 my-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors duration-300 ease-in-out">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-floppy" viewBox="0 0 16 16">
                                        <path d="M11 2H9v3h2z"/>
                                        <path d="M1.5 0h11.586a1.5 1.5 0 0 1 1.06.44l1.415 1.414A1.5 1.5 0 0 1 16 2.914V14.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 14.5v-13A1.5 1.5 0 0 1 1.5 0M1 1.5v13a.5.5 0 0 0 .5.5H2v-4.5A1.5 1.5 0 0 1 3.5 9h9a1.5 1.5 0 0 1 1.5 1.5V15h.5a.5.5 0 0 0 .5-.5V2.914a.5.5 0 0 0-.146-.353l-1.415-1.415A.5.5 0 0 0 13.086 1H13v4.5A1.5 1.5 0 0 1 11.5 7h-7A1.5 1.5 0 0 1 3 5.5V1H1.5a.5.5 0 0 0-.5.5m3 4a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 .5-.5V1H4zM3 15h10v-4.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5z"/>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>


                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-ui.forms.group label="Organization Name" for="organizationName" error="organizationName">
                            <x-ui.forms.input
                                wire:model.live="organizationName"
                                name="organizationName"
                                placeholder="Enter the organization's name"
                                error="{{ $errors->has('organizationName') }}"
                                class="rounded-xl shadow-md focus:ring-2 focus:ring-indigo-500"
                            />
                        </x-ui.forms.group>

                        <x-ui.forms.group label="Subdomain" for="organizationSubdomain" error="organizationSubdomain">
                            <div class="flex">
                                <x-ui.forms.input
                                    wire:model.live="organizationSubdomain"
                                    name="organizationSubdomain"
                                    placeholder="the-organization"
                                    error="{{ $errors->has('organizationSubdomain') }}"
                                    class="rounded-l-xl shadow-md focus:ring-2 focus:ring-indigo-500"
                                />
                                <span class="inline-flex items-center px-3 bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 border border-l-0 border-gray-300 dark:border-gray-700 rounded-r-xl">
                                    .{{ config('app.domain', 'example.com') }}
                                </span>
                            </div>
                            @if($organizationSubdomain != $organization->subdomain)
                                <p class="text-red-600 dark:text-red-400 font-bold mt-2">
                                    Changing The subdomain will break any existing links to the organization.<br>Proceed with caution!
                                </p>
                            @endif
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                {{ __('Only lowercase letters, numbers, and hyphens are allowed.') }}
                            </p>
                        </x-ui.forms.group>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

