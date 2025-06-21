<x-layouts.app :title="__('Dashboard')">


    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        <style>
            .dashboard-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(23rem, 1fr));
                grid-auto-rows: min-content;
            }

            /* Hide all cards initially to prevent flicker */
            .dashboard-grid > div {
                display: none;
            }

            /* Show cards that should be visible */
            .dashboard-grid.cards-processed > div.card-visible {
                display: block;
            }
        </style>

        <div class="dashboard-grid gap-4" id="dashboard-grid">
            @role('superadmin')
            <!-- Organizations Card -->
            <div class="relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-gray-800">
                <div class="flex items-center gap-3">
                    <div class="rounded-lg bg-blue-100 p-3 dark:bg-blue-900/50">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Organizations') }}</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white countup" data-target="{{ $organizationsCount }}">0</p>
                    </div>
                </div>
            </div>

            <!-- Users Card -->
            <div class="relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-gray-800">
                <div class="flex items-center gap-3">
                    <div class="rounded-lg bg-blue-100 p-3 dark:bg-blue-900/50">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Users') }}</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white countup" data-target="{{ $usersCount }}">0</p>
                    </div>
                </div>
            </div>

            <!-- Venues Card -->
            <div class="relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-gray-800">
                <div class="flex items-center gap-3">
                    <div class="rounded-lg bg-green-100 p-3 dark:bg-green-900/50">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205l3 1m1.5.5l-1.5-.5M6.75 7.364V3h-3v18m3-13.636l10.5-3.819" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Venues') }}</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white countup" data-target="{{ $venueCount }}">0</p>
                    </div>
                </div>
            </div>
            @endrole

            <!-- Events Card -->
            <div class="relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-gray-800">
                <div class="flex items-center gap-3">
                    <div class="rounded-lg bg-red-100 p-3 dark:bg-red-900/50">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Events') }}</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white countup" data-target="{{ $eventsCount }}">0</p>
                    </div>
                </div>
            </div>

            <!-- Tickets Card -->
            <div class="relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-gray-800">
                <div class="flex items-center gap-3">
                    <div class="rounded-lg bg-emerald-100 p-3 dark:bg-emerald-900/50">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Tickets') }}</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white countup" data-target="{{ $ticketsCount }}">0</p>
                    </div>
                </div>
            </div>

            <!-- Customers Card -->
            <div class="relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-gray-800">
                <div class="flex items-center gap-3">
                    <div class="rounded-lg bg-purple-100 p-3 dark:bg-purple-900/50">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Customers') }}</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white countup" data-target="{{ $customersCount }}">0</p>
                    </div>
                </div>
            </div>

            <!-- Discount Codes Card -->
            <div class="relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-gray-800">
                <div class="flex items-center gap-3">
                    <div class="rounded-lg bg-amber-100 p-3 dark:bg-amber-900/50">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185zM9.75 9h.008v.008H9.75V9zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm4.125 4.5h.008v.008h-.008V13.5zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Discount Codes') }}</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white countup" data-target="{{ $discountCodesCount }}">0</p>
                    </div>
                </div>
            </div>

            @role('admin')
            <!-- Venues Card -->
            <div class="relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-gray-800">
                <div class="flex items-center gap-3">
                    <div class="rounded-lg bg-green-100 p-3 dark:bg-green-900/50">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205l3 1m1.5.5l-1.5-.5M6.75 7.364V3h-3v18m3-13.636l10.5-3.819" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Venues') }}</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white countup" data-target="{{ $venueCount }}">0</p>
                    </div>
                </div>
            </div>

            <!-- Users Card -->
            <div class="relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-gray-800">
                <div class="flex items-center gap-3">
                    <div class="rounded-lg bg-blue-100 p-3 dark:bg-blue-900/50">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Users') }}</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white countup" data-target="{{ $usersCount }}">0</p>
                    </div>
                </div>
            </div>
            @endhasanyrole
        </div>

        <!-- Additional content area -->
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
        </div>
    </div>

    @push('scripts')
        <script>
            function limitGridToTwoRows() {
                const grid = document.getElementById('dashboard-grid');
                if (!grid) return;

                const cards = Array.from(grid.children);

                // Remove previous classes
                grid.classList.remove('cards-processed');
                cards.forEach(card => {
                    card.classList.remove('card-visible');
                });

                if (cards.length === 0) return;

                // Get computed style to find actual columns
                const gridStyle = window.getComputedStyle(grid);
                const gridTemplateColumns = gridStyle.getPropertyValue('grid-template-columns');
                const columnCount = gridTemplateColumns.split(' ').length;

                // Calculate how many cards should be visible (2 rows worth)
                const maxVisibleCards = columnCount * 2;

                // Mark visible cards
                cards.forEach((card, index) => {
                    if (index < maxVisibleCards) {
                        card.classList.add('card-visible');
                    }
                });

                // Enable display of processed cards
                grid.classList.add('cards-processed');
            }

            function animateCounters() {
                const counters = document.querySelectorAll('.countup');
                const animationDuration = 750;
                const frameDuration = 1000 / 60;
                const frames = Math.floor(animationDuration / frameDuration);

                counters.forEach(counter => {
                    const target = parseInt(counter.getAttribute('data-target'), 10);
                    if (isNaN(target)) return;

                    let current = 0;
                    const increment = target / frames;

                    const animate = () => {
                        current += increment;
                        if (current < target) {
                            counter.textContent = Math.floor(current).toLocaleString();
                            requestAnimationFrame(animate);
                        } else {
                            counter.textContent = target.toLocaleString();
                        }
                    };

                    animate();
                });
            }

            function initDashboard() {
                limitGridToTwoRows();
                animateCounters();
            }

            // Run on page load
            document.addEventListener('DOMContentLoaded', initDashboard);

            // Run on Livewire navigation
            document.addEventListener('livewire:navigated', initDashboard);

            // Run on window resize to handle responsive changes
            window.addEventListener('resize', limitGridToTwoRows);
        </script>
    @endpush

</x-layouts.app>
