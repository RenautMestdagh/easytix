<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <div class="hidden lg:block">
            @include('partials.logged-in-as')
        </div>
        <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.group :heading="__('Platform')" class="grid">
                    <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>

            @can('events.index')
            <flux:navlist.item
                icon="calendar"
                :href="route('events.index')"
                :current="request()->routeIs(['events.*', 'ticket-types.*'])"
                wire:navigate
            >
                {{ __('Events') }}
            </flux:navlist.item>
            @endcan

            @can('discount-codes.index')
            <flux:navlist.item
                icon="tag"
                :href="route('discount-codes.index')"
                :current="request()->routeIs('discount-codes.*')"
                wire:navigate
            >
                {{ __('Discount codes') }}
            </flux:navlist.item>
            @endcan

            @can('scanner.show')
                <flux:navlist.item
                    icon="camera"
                    :href="route('scanner.show')"
                    :current="request()->routeIs('scanner.show')"
                    wire:navigate
                >
                    {{ __('Scan tickets') }}
                </flux:navlist.item>
            @endcan

            @can('venues.index')
                <flux:navlist.item
                    icon="home-modern"
                    :href="route('venues.index')"
                    :current="request()->routeIs('venues.*')"
                    wire:navigate
                >
                    {{ __('Venues') }}
                </flux:navlist.item>
            @endcan

            @can('users.index')
                <flux:navlist.item
                    icon="users"
                    :href="route('users.index')"
                    :current="request()->routeIs('users.*')"
                    wire:navigate
                >
                    {{ __('Users') }}
                </flux:navlist.item>
            @endcan

            @can('organizations.index')
            <flux:navlist.item
                icon="briefcase"
                :href="route('organizations.index')"
                :current="request()->routeIs(['organizations.index', 'organizations.create', 'organizations.update'])"
                wire:navigate
            >
                {{ __('Organizations') }}
            </flux:navlist.item>
            @endcan

            <flux:spacer />

            @can('organizations.media')
            <flux:navlist.item
                icon="paint-brush"
                :href="route('organizations.media', $organization)"
                :current="request()->routeIs('organizations.media')"
                wire:navigate
            >
                {{ __('Personalization') }}
            </flux:navlist.item>
            @endcan

            @role('admin')
            @can('organizations.update')
            <flux:navlist.item
                icon="cog-6-tooth"
                :href="route('organizations.update', $organization->id)"
                :current="request()->routeIs('organizations.update')"
                wire:navigate
            >
                {{ __('Organization Settings') }}
            </flux:navlist.item>
            @endcan
            @endrole

{{--            <flux:navlist variant="outline">--}}
{{--                <flux:navlist.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">--}}
{{--                {{ __('Repository') }}--}}
{{--                </flux:navlist.item>--}}

{{--                <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits" target="_blank">--}}
{{--                {{ __('Documentation') }}--}}
{{--                </flux:navlist.item>--}}
{{--            </flux:navlist>--}}

            <!-- Desktop User Menu -->
            <flux:dropdown position="bottom" align="start">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevrons-up-down"
                />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="flex flex-col lg:hidden">
            <div class="w-screen relative">
                @include('partials.logged-in-as')
            </div>

            <flux:header class="lg:hidden w-full">
                <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

                <flux:spacer />

                <flux:dropdown position="top" align="end">
                    <flux:profile
                        :initials="auth()->user()->initials()"
                        icon-trailing="chevron-down"
                    />

                    <flux:menu>
                        <flux:menu.radio.group>
                            <div class="p-0 text-sm font-normal">
                                <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                    <div class="grid flex-1 text-start text-sm leading-tight">
                                        <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                        <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                    </div>
                                </div>
                            </div>
                        </flux:menu.radio.group>

                        <flux:menu.separator />

                        <flux:menu.radio.group>
                            <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                        </flux:menu.radio.group>

                        <flux:menu.separator />

                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                                {{ __('Log Out') }}
                            </flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            </flux:header>
        </flux:header>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
