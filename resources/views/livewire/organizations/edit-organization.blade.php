<div class="py-16">
    <div class="px-10">

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

        @if (session()->has('message'))
            <x-ui.flash-message
                :message="session('message')"
                :type="session('message_type', 'success')"
            />
        @endif

        <!-- Organization and Users Section -->
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm rounded-xl transition-all duration-300 hover:border-indigo-800">
            <div class="p-8 space-y-8">

                <!-- Form: Organization Details -->
                <form wire:submit.prevent="save">
                    @csrf
                    <div class="flex justify-between">
                        <div class="inline-flex items-center p-1 px-3 mb-6 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 rounded-full shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            {{ __('Organization Details') }}
                        </div>
                        @if ($saveButtonVisible)
                            <button type="submit" class="px-6 my-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-floppy" viewBox="0 0 16 16">
                                    <path d="M11 2H9v3h2z"/>
                                    <path d="M1.5 0h11.586a1.5 1.5 0 0 1 1.06.44l1.415 1.414A1.5 1.5 0 0 1 16 2.914V14.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 14.5v-13A1.5 1.5 0 0 1 1.5 0M1 1.5v13a.5.5 0 0 0 .5.5H2v-4.5A1.5 1.5 0 0 1 3.5 9h9a1.5 1.5 0 0 1 1.5 1.5V15h.5a.5.5 0 0 0 .5-.5V2.914a.5.5 0 0 0-.146-.353l-1.415-1.415A.5.5 0 0 0 13.086 1H13v4.5A1.5 1.5 0 0 1 11.5 7h-7A1.5 1.5 0 0 1 3 5.5V1H1.5a.5.5 0 0 0-.5.5m3 4a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 .5-.5V1H4zM3 15h10v-4.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5z"/>
                                </svg>
                            </button>
                        @endif
                    </div>


                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-ui.forms.group label="Organization Name" for="organization.name" error="organization.name">
                            <x-ui.forms.input
                                wire:model.live="organization.name"
                                name="organization.name"
                                placeholder="Enter the organization's name"
                                error="{{ $errors->has('organization.name') }}"
                                class="rounded-xl shadow-md focus:ring-2 focus:ring-indigo-500"
                            />
                        </x-ui.forms.group>

                        <x-ui.forms.group label="Subdomain" for="organization.subdomain" error="organization.subdomain">
                            <div class="flex">
                                <x-ui.forms.input
                                    wire:model.live="organization.subdomain"
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
                </form>

                <!-- Section: Users -->
                <div class="mt-10">
                    <div class="flex justify-between flex-wrap">
                        <div class="inline-flex items-center p-1 px-3 mb-6 bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 rounded-full shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            {{ __('Users in this Organization') }}
                        </div>
                        <x-ui.button
                            variant="purple"
                            class="my-2"
                            href=""
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            {{ __('Add User') }}
                        </x-ui.button>
                    </div>

                    <!-- Search Field and Include Deleted for Users -->
                    <div class="my-4 flex items-center gap-4">
                        <div class="relative w-1/3">
                            <!-- Search icon -->
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 103 10.5a7.5 7.5 0 0013.15 6.15z"/>
                                </svg>
                            </div>

                            <!-- Input field -->
                            <input type="text"
                                   wire:model.live.debounce.250ms="userSearch"
                                   placeholder="{{ __('Search users...') }}"
                                   class="pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 p-2 text-sm w-full"
                            />
                        </div>

                        <x-ui.forms.select wire:model.live="userRole">
                            <option value="all">All Roles</option>
                            <option value="admin">Admins</option>
                            <option value="organizer">Organizers</option>
                        </x-ui.forms.select>

                        <!-- Include Deleted Checkbox -->
                        <label class="ml-auto flex items-center">
                            <input type="checkbox" wire:model.live="includeDeletedUsers" class="form-checkbox text-indigo-600">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('Include deleted') }}</span>
                        </label>
                    </div>

                    <div class="pt-4 flow-root">
                        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="inline-block min-w-full py-2 align-middle">
                                <div class="overflow-hidden shadow-sm sm:rounded-lg bg-white dark:bg-gray-800 mx-8">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                                        <tr>
                                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-white sm:pl-6 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700" wire:click="sortUsersBy('name')">
                                                {{ __('Name') }}
                                                <span class="text-xs ml-1" style="visibility: {{ $userSortField == 'name' ? 'visible' : 'hidden' }};">
                                                    {{ $userSortDirection == 'asc' ? '↑' : '↓' }}
                                                </span>
                                            </th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700" wire:click="sortUsersBy('email')">
                                                {{ __('Email') }}
                                                <span class="text-xs ml-1" style="visibility: {{ $userSortField == 'email' ? 'visible' : 'hidden' }};">
                                                    {{ $userSortDirection == 'asc' ? '↑' : '↓' }}
                                                </span>
                                            </th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                                {{ __('Role') }}
                                                <span class="text-xs ml-1" style="visibility: {{ $userSortField == 'role' ? 'visible' : 'hidden' }};">
                                                    {{ $userSortDirection == 'asc' ? '↑' : '↓' }}
                                                </span>
                                            </th>
                                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                                <span class="sr-only">{{ __('Actions') }}</span>
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @forelse($users as $user)
                                            <tr wire:key="user-{{ $user->id }}" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition duration-150">
                                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-white sm:pl-6">
                                                    <div class="flex items-center gap-2">
                                                        {{ $user->name }}
                                                        @if($user->trashed())
                                                            <span class="inline-flex items-center rounded-md bg-red-50 dark:bg-red-900/20 px-2 py-1 text-xs font-medium text-red-700 dark:text-red-400 ring-1 ring-inset ring-red-600/10">
                                                                {{ __('Deleted') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">
                                                    {{ $user->email }}
                                                </td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">
                                                    {{ $user->getRoleNames()->first() }}
                                                </td>
                                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                                    <div class="flex justify-end gap-2">
                                                        @if ($user->trashed())
                                                            <!-- Restore Button -->
                                                            <button type="button"
                                                                    wire:click="restoreUser({{ $user->id }})"
                                                                    class="p-1 text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors"
                                                                    title="{{ __('Restore') }}"
                                                            >
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                                                                </svg>
                                                            </button>

                                                            <!-- Force Delete Button -->
                                                            <button type="button"
                                                                    onclick="confirmUserForceDelete({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                                                    class="p-1 bg-red-600 hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-white rounded transition-colors"
                                                                    title="{{ __('Delete permanently') }}"
                                                            >
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                                                </svg>
                                                            </button>
                                                        @else
                                                            <!-- Edit Button -->
                                                            <a href=""
                                                               class="p-1 text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors"
                                                               title="{{ __('Edit') }}"
                                                            >
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                                                                </svg>
                                                            </a>

                                                            <!-- Soft Delete Button -->
                                                            @php
                                                                $isLastAdmin = $user->getRoleNames()->first() === 'admin' && $adminCount <= 1;
                                                            @endphp
                                                            <button type="button"
                                                                    @if($isLastAdmin) disabled @endif
                                                                    onclick="@unless($isLastAdmin) confirmUserSoftDelete({{ $user->id }}, '{{ addslashes($user->name) }}') @endunless"
                                                                    class="p-1 rounded-full transition-colors
                                                                    @if($isLastAdmin)
                                                                        text-gray-400 dark:text-gray-500 cursor-not-allowed
                                                                    @else
                                                                        text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 hover:bg-gray-100 dark:hover:bg-gray-700
                                                                    @endif"
                                                                    title="{{ $isLastAdmin ? __('Cannot delete the last admin') : __('Delete') }}">
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                                     stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                          d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                                                </svg>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-3 py-8 text-sm text-gray-500 dark:text-gray-300 text-center">
                                                    <div class="flex flex-col items-center justify-center gap-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-gray-400">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                                        </svg>
                                                        {{ __('No users found') }}
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>

                <script>
                    function confirmUserSoftDelete(id, name) {
                        if (confirm(`Are you sure you want to delete the user: ${name}?`)) {
                            @this.
                            call('removeUser', id);
                        }
                    }

                    function confirmUserForceDelete(id, name) {
                        if (confirm(`⚠️ Are you sure you want to permanently delete the user: ${name}?\n\nThis action cannot be undone.`)) {
                            @this.
                            call('forceDeleteUser', id);
                        }
                    }
                </script>
            </div>
        </div>
    </div>
</div>

