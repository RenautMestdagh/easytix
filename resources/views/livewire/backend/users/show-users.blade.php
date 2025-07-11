<div>
    <div class="mx-auto max-w-7xl p-4 sm:p-6 lg:p-8">
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
                    </svg>
                    <h1 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">{{ __('Users') }}</h1>
                </div>
                <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                    {{ __('Manage all users.') }}
                </p>
            </div>
            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                @can('users.create')
                <x-ui.button
                    href="{{ route('users.create') }}"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('New User') }}
                </x-ui.button>
                @endcan
            </div>
        </div>

        <!-- Search Field and Include Deleted -->
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
                       wire:model.live.debounce.150ms="search"
                       placeholder="{{ __('Search by name or email...') }}"
                       class="pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 p-2 text-sm w-full"
                />
            </div>

            <x-ui.forms.select wire:model.live="selectedRole">
                <option value="all">All Roles</option>
                @foreach($roles as $key => $value)
                    <option value="{{ $key }}">{{ ucfirst($value) }}</option>
                @endforeach
            </x-ui.forms.select>

            @role('superadmin')
            <x-ui.forms.select
                wire:model.live="selectedOrganization"
                class="{{ $selectedRole === 'superadmin' ? 'opacity-50' : '' }}"
                :disabled="$selectedRole === 'superadmin'"
            >
                <option value="">No organization selected</option>
                @foreach($organizations as $organization)
                    <option value="{{ $organization->id }}">{{ Str::limit($organization->name, 30) }}</option>
                @endforeach
            </x-ui.forms.select>
            @endrole

            <!-- Include Deleted Checkbox -->
            <label class="flex items-center ml-auto">
                <input type="checkbox" wire:model.live="includeDeleted" class="form-checkbox text-indigo-600">
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('Include deleted') }}</span>
            </label>
        </div>

        @if (session()->has('message'))
            <x-ui.flash-message/>
        @endif

        <!-- Users Table -->
        <div>
            <div class="pt-4 flow-root">
                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle">
                        <div class="overflow-hidden shadow-sm sm:rounded-lg bg-white dark:bg-gray-800 mx-8">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-white sm:pl-6 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-300 ease-in-out" wire:click="sortBy('name')">
                                        {{ __('Name') }}
                                        <span class="text-xs ml-1" style="visibility: {{ $sortField == 'name' ? 'visible' : 'hidden' }};">
                                            {{ $sortDirection == 'asc' ? '↑' : '↓' }}
                                        </span>
                                    </th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-300 ease-in-out" wire:click="sortBy('email')">
                                        {{ __('Email') }}
                                        <span class="text-xs ml-1" style="visibility: {{ $sortField == 'email' ? 'visible' : 'hidden' }};">
                                            {{ $sortDirection == 'asc' ? '↑' : '↓' }}
                                        </span>
                                    </th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ __('Role') }}
                                    </th>
                                    @role('superadmin')
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-300 ease-in-out" wire:click="sortBy('organization_id')">
                                            {{ __('Organization') }}
                                            <span class="text-xs ml-1" style="visibility: {{ $sortField == 'organization_id' ? 'visible' : 'hidden' }};">
                                                {{ $sortDirection == 'asc' ? '↑' : '↓' }}
                                            </span>
                                        </th>
                                    @endrole
                                    @if(auth()->user()->can('users.update') || auth()->user()->can('users.delete'))
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                            <span class="sr-only">{{ __('Actions') }}</span>
                                        </th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($users as $user)
                                    <tr wire:key="user-{{ $user->id }}" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition-colors duration-300 ease-in-out">
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-white sm:pl-6">
                                            <div class="flex items-center gap-2">
                                                {{ $user->name }}
                                                @if($user->trashed())
                                                    <x-ui.badge color="red" text="Deleted" size="sm"/>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">
                                            {{ $user->email }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">
                                            {{ ucfirst($user->getRoleNames()->first()) }}
                                        </td>
                                        @role('superadmin')
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">
                                                {{ $user->organization->name ?? '' }}
                                            </td>
                                        @endrole
                                        @if(auth()->user()->can('users.update') || auth()->user()->can('users.delete'))
                                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                                <div class="flex justify-end gap-2">
                                                    @if ($user->trashed())
                                                        @can('users.delete')
                                                            <!-- Restore Button -->
                                                            <button type="button"
                                                                    wire:click="restoreUser({{ $user->id }})"
                                                                    class="p-1 text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors duration-300 ease-in-out hover:cursor-pointer"
                                                                    title="{{ __('Restore') }}"
                                                            >
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                                                                </svg>
                                                            </button>

                                                            <x-ui.delete-button
                                                                type="forcedelete"
                                                                method="forceDeleteUser"
                                                                :args="[$user->id]"
                                                                confirmation="⚠️ Are you sure you want to permanently delete this user?"
                                                                title="{{ __('Delete permanently') }}"
                                                            />
                                                        @endcan
                                                    @else
                                                        @can('login-as.use')
                                                            @if(!$user->isSuperadmin())
                                                                <button type="button"
                                                                        wire:click.prevent="loginAsUser({{ $user->id }})"
                                                                        class="p-1 rounded-full transition-colors duration-300 ease-in-out text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:cursor-pointer"
                                                                        title="{{ __('Log in') }}">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H2.25" />
                                                                    </svg>
                                                                </button>
                                                           @endif
                                                        @endcan
                                                        @can('users.update')
                                                            <!-- Edit Button -->
                                                            <x-ui.edit-button
                                                                route="users.update"
                                                                :routeParams="['user' => $user]"
                                                                title="{{ __('Edit') }}"
                                                            />
                                                        @endcan

                                                        @can('users.delete')
                                                            <!-- Soft Delete Button -->
                                                            @php
                                                                $isOwnUser = $user->id === auth()->id();
                                                                $orgId = optional($user->organization)->id; // safe access
                                                                $isLastAdmin = $orgId && in_array($orgId, $singleAdminOrgIds) && $user->getRoleNames()->first() === 'admin';
                                                            @endphp
                                                            <x-ui.delete-button
                                                                type="delete"
                                                                method="deleteUser"
                                                                :args="[$user->id]"
                                                                confirmation="Are you sure you want to delete this user?"
                                                                title="{{ __('Delete') }}"
                                                                disabledTitle="{{ $isLastAdmin ? __('Cannot delete the last admin') : __('Cannot delete yourself') }}"
                                                                :disabled="$isLastAdmin || $isOwnUser"
                                                            />
                                                        @endcan
                                                    @endif
                                                </div>
                                            </td>
                                        @endif
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

    </div>

</div>
