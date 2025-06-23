<div class="py-16">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        @if (session()->has('message'))
            <x-ui.flash-message/>
        @endif

        <div class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-semibold text-gray-800 dark:text-gray-200">
                    {{ __('New User') }}
                </h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ __('Create a new user account') }}
                </p>
            </div>
            <x-ui.back-to-button route="users.index" text="{{ __('Back to users') }}"/>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm rounded-xl transition-colors duration-300 ease-in-out hover:border-indigo-800">
            <div class="p-8">
                <form wire:submit.prevent="save">
                    <div class="space-y-8">
                        <!-- User Information -->
                        <div>
                            <div class="inline-flex items-center p-1 px-3 mb-6 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 rounded-full shadow-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                {{ __('User Details') }}
                            </div>

                            <x-ui.forms.group label="Full Name" for="userName" error="userName">
                                <x-ui.forms.input
                                    wire:model.lazy="userName"
                                    name="userName"
                                    placeholder="Enter user's full name"
                                    error="{{ $errors->has('userName') }}"
                                    class="rounded-xl shadow-md focus:ring-2 focus:ring-indigo-500"
                                />
                            </x-ui.forms.group>

                            <x-ui.forms.group label="Email Address" for="userEmail" error="userEmail">
                                <x-ui.forms.input
                                    type="email"
                                    wire:model.lazy="userEmail"
                                    name="userEmail"
                                    placeholder="user@example.com"
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

                        <!-- Role and Organization Section -->
                        <div class="pt-4">
                            <div class="inline-flex items-center p-1 px-3 mb-6 bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 rounded-full shadow-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ __('Permissions') }}
                            </div>

                            <div class="flex flex-col">
                                <x-ui.forms.group label="Role" for="role" error="role">
                                    <x-ui.forms.select
                                        wire:model.lazy="role"
                                        name="role"
                                        error="{{ $errors->has('role') }}"
                                        class="rounded-xl shadow-md focus:ring-2 focus:ring-indigo-500 px-5"
                                    >
                                        <option value="">{{ __('Select a role') }}</option>
                                        @foreach($roles as $key => $value)
                                            <option value="{{ $key }}">{{ ucfirst($value) }}</option>
                                        @endforeach
                                    </x-ui.forms.select>
                                </x-ui.forms.group>

                                @role('superadmin')
                                @if($role && $role !== 'superadmin')
                                    <x-ui.forms.group label="Organization" for="organization_id" error="organization_id">
                                        <x-ui.forms.select
                                            wire:model.lazy="organization_id"
                                            name="organization_id"
                                            error="{{ $errors->has('organization_id') }}"
                                            class="rounded-xl shadow-md focus:ring-2 focus:ring-indigo-500"
                                        >
                                            @foreach  ($organizations as $id => $name)
                                                <option value="{{$id}}">{{ Str::limit($name, 30) }}</option>
                                            @endforeach
                                        </x-ui.forms.select>
                                    </x-ui.forms.group>
                                @endif
                                @endrole
                            </div>

                        </div>

                        <div class="flex items-center justify-end pt-8 space-x-4">
                            <x-ui.button type="submit" variant="indigo">
                                {{ __('Create User') }}
                            </x-ui.button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
