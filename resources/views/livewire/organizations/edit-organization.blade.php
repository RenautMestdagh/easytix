<div class="py-16">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        @if (session()->has('message'))
            <div class="mb-8 px-6 py-4 rounded-xl shadow-md {{ session('message_type') === 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }} transition-all duration-300">
                {{ session('message') }}
            </div>
        @endif

        <div class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-semibold text-gray-800 dark:text-gray-200 transition-all duration-300">
                    {{ __('Edit Organization') }}
                </h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ __('Update organization details') }}
                </p>
            </div>
            <a href="{{ route('organizations.index') }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 flex items-center transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ __('Back to organizations') }}
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm rounded-xl transition-all duration-300 hover:border-indigo-800">
            <div class="p-8">
                <form wire:submit.prevent="save">
                    @csrf
                    <div class="space-y-8">
                        <!-- Organization Information -->
                        <div>
                            <div class="inline-flex items-center p-1 px-3 mb-6 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 rounded-full shadow-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                {{ __('Organization Details') }}
                            </div>

                            <x-ui.forms.group label="Organization Name" for="organization.name" error="organization.name">
                                <x-ui.forms.input
                                    wire:model.lazy="organization.name"
                                    name="organization.name"
                                    placeholder="Enter the organization's name"
                                    error="{{ $errors->has('organization.name') }}"
                                    class="rounded-xl shadow-md focus:ring-2 focus:ring-indigo-500"
                                />
                            </x-ui.forms.group>

                            <x-ui.forms.group label="Subdomain" for="organization.subdomain" error="organization.subdomain">
                                <div class="flex">
                                    <x-ui.forms.input
                                        wire:model.lazy="organization.subdomain"
                                        name="organization.subdomain"
                                        placeholder="the-organization"
                                        error="{{ $errors->has('organization.subdomain') }}"
                                        class="rounded-l-xl shadow-md focus:ring-2 focus:ring-indigo-500"
                                    />
                                    <span class="inline-flex items-center px-3 bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 border border-l-0 border-gray-300 dark:border-gray-700 rounded-r-xl">
                                        .{{ config('app.domain', 'example.com') }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                    {{ __('Only lowercase letters, numbers, and hyphens are allowed.') }}
                                </p>
                            </x-ui.forms.group>
                        </div>

                        <div class="flex items-center justify-end pt-8 space-x-4">
                            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 hover:cursor-pointer transition-all duration-300">
                                {{ __('Update Organization') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
