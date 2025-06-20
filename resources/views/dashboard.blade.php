<x-layouts.app :title="__('Dashboard')">


    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <!-- Main grid container with all 6 cards -->
        <div class="grid auto-rows-min gap-4 grid-cols-[repeat(auto-fit,minmax(400px,1fr))]">
            @role('superadmin')
            <!-- Organizations Card -->
            <div class="relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-gray-800">
                <div class="flex items-center gap-3">
                    <div class="rounded-lg bg-blue-100 p-3 dark:bg-blue-900/50">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Organizations') }}</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white countup" data-target="{{ $organizationsCount }}">0</p>
                    </div>
                </div>
            </div>
            @endrole

            @hasanyrole('superadmin|admin')
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
        </div>

        <!-- Additional content area -->
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
        </div>
    </div>

    @push('scripts')
        <script>
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

            document.addEventListener('livewire:navigated', () => {
                animateCounters();
            });

            // Optional: run it once if this script runs after the initial route load
            document.addEventListener('DOMContentLoaded', () => {
                animateCounters();
            });
        </script>
    @endpush

</x-layouts.app>
