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
                    {{ __('New Organization') }}
                </h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ __('Create a new organization and admin account') }}
                </p>
            </div>
            <a href="{{ route('organizations.index') }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 flex items-center transition-all duration-300 ease-in-out">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ __('Back to organizations') }}
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm rounded-xl transition-all duration-300 ease-in-out hover:border-indigo-800">
            <div class="p-8">
                <form wire:submit.prevent="save">
                    <!-- Organization Details Section -->
                    <div class="space-y-8">
                        <!-- Organization Information -->
                        <div>
                            <div class="inline-flex items-center p-1 px-3 mb-6 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 rounded-full shadow-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                {{ __('Organization Details') }}
                            </div>

                            <x-ui.forms.group label="Organization Name" for="organization.name" error="organizationName">
                                <x-ui.forms.input
                                    wire:model.lazy="organizationName"
                                    name="organizationName"
                                    placeholder="Enter the organization's name"
                                    error="{{ $errors->has('organizationName') }}"
                                    class="rounded-xl shadow-md focus:ring-2 focus:ring-indigo-500"
                                />
                            </x-ui.forms.group>

                            <x-ui.forms.group label="Subdomain" for="organizationSubdomain" error="organizationSubdomain">
                                <div class="flex">
                                    <x-ui.forms.input
                                        wire:model.lazy="organizationSubdomain"
                                        name="organizationSubdomain"
                                        placeholder="the-organization"
                                        error="{{ $errors->has('organizationSubdomain') }}"
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

                        <!-- Admin Account Section -->
                        <div class="pt-4">
                            <div class="inline-flex items-center p-1 px-3 mb-6 bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 rounded-full shadow-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                {{ __('Admin Account') }}
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                                {{ __('This user will have full administrative access to the organization.') }}
                            </p>

                            <x-ui.forms.group label="Full Name" for="userName" error="userName">
                                <x-ui.forms.input
                                    wire:model.lazy="userName"
                                    name="userName"
                                    placeholder="Enter admin's full name"
                                    error="{{ $errors->has('userName') }}"
                                    class="rounded-xl shadow-md focus:ring-2 focus:ring-indigo-500"
                                />
                            </x-ui.forms.group>

                            <x-ui.forms.group label="Email Address" for="userEmail" error="userEmail">
                                <x-ui.forms.input
                                    type="email"
                                    wire:model.lazy="userEmail"
                                    name="userEmail"
                                    placeholder="admin@example.com"
                                    error="{{ $errors->has('userEmail') }}"
                                    class="rounded-xl shadow-md focus:ring-2 focus:ring-indigo-500"
                                />
                            </x-ui.forms.group>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <x-ui.forms.group label="Password" for="userPassword" error="userPassword">
                                    <x-ui.forms.input
                                        type="password"
                                        wire:model.lazy="userPassword"
                                        name="userPassword"
                                        placeholder="••••••••"
                                        error="{{ $errors->has('userPassword') }}"
                                        class="rounded-xl shadow-md focus:ring-2 focus:ring-indigo-500"
                                    />
                                </x-ui.forms.group>

                                <x-ui.forms.group label="Confirm Password" for="userPassword_confirmation">
                                    <x-ui.forms.input
                                        type="password"
                                        wire:model.lazy="userPassword_confirmation"
                                        name="userPassword_confirmation"
                                        placeholder="••••••••"
                                        class="rounded-xl shadow-md focus:ring-2 focus:ring-indigo-500"
                                    />
                                </x-ui.forms.group>
                            </div>
                        </div>

                        <div class="flex items-center justify-end pt-8 space-x-4">
                            <x-ui.button type="submit" variant="indigo">
                                {{ __('Create Organization') }}
                            </x-ui.button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
