<div>
    <section class="relative bg-gradient-to-r from-blue-600 to-purple-600 text-white py-20 overflow-hidden">
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-5xl mx-auto text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-6 animate-fade-in-down text-nowrap">
                    Easy Access, Unforgettable Events
                </h1>
                <p class="text-xl md:text-2xl mb-8 animate-fade-in-up delay-100">
                    The easiest way to create, manage and sell tickets for your events
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4 animate-fade-in-up delay-200">
                    <a href="#events" class="bg-white text-blue-600 hover:bg-blue-50 px-8 py-3 rounded-lg font-semibold text-lg transition-all transform hover:scale-105">
                        Browse Events
                    </a>
                </div>
            </div>
        </div>
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
            <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-t from-black/10 to-black/30"></div>
            <div class="circles-animation">
                <div class="circle"></div>
                <div class="circle"></div>
                <div class="circle"></div>
                <div class="circle"></div>
                <div class="circle"></div>
                <div class="circle"></div>
                <div class="circle"></div>
                <div class="circle"></div>
                <div class="circle"></div>
                <div class="circle"></div>
            </div>
        </div>
    </section>

    <section class="py-16 bg-gray-50 dark:bg-gray-800 section-bg-pattern-light dark:section-bg-pattern-dark">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white dark:bg-gray-700 p-8 rounded-xl shadow-lg text-center transform transition-all hover:scale-105 hover:shadow-xl">
                    <div class="text-5xl font-bold text-blue-600 mb-4 count-up" data-count="{{ $eventCount }}">0</div>
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Events Hosted</h3>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">Join our growing community of event organizers</p>
                </div>
                <div class="bg-white dark:bg-gray-700 p-8 rounded-xl shadow-lg text-center transform transition-all hover:scale-105 hover:shadow-xl">
                    <div class="text-5xl font-bold text-purple-600 mb-4 count-up" data-count="{{ $ticketCount }}">0</div>
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Tickets Sold</h3>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">Happy attendees across all our events</p>
                </div>
                <div class="bg-white dark:bg-gray-700 p-8 rounded-xl shadow-lg text-center transform transition-all hover:scale-105 hover:shadow-xl">
                    <div class="text-5xl font-bold text-green-600 mb-4">100%</div>
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Secure Payments</h3>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">Through Stripe & PayPal integration</p>
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="py-20 bg-gray-50 dark:bg-gray-700 section-bg-pattern-light dark:section-bg-pattern-dark">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-gray-200 mb-4">Why Choose Easytix?</h2>
                <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">Powerful features designed to make event management simple and ticket buying easy</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="feature-card p-8 bg-white dark:bg-gray-700 rounded-xl shadow-md hover:shadow-xl transition-all">
                    <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-900 dark:text-gray-100">Lightning Fast</h3>
                    <p class="text-gray-600 dark:text-gray-300">Our optimized platform handles high traffic with ease, ensuring smooth ticket sales even for popular events.</p>
                </div>

                <div class="feature-card p-8 bg-white dark:bg-gray-700 rounded-xl shadow-md hover:shadow-xl transition-all">
                    <div class="w-16 h-16 bg-purple-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-900 dark:text-gray-100">Secure Payments</h3>
                    <p class="text-gray-600 dark:text-gray-300">Industry-leading payment processors ensure your transactions are always safe and secure.</p>
                </div>

                <div class="feature-card p-8 bg-white dark:bg-gray-700 rounded-xl shadow-md hover:shadow-xl transition-all">
                    <div class="w-16 h-16 bg-green-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-900 dark:text-gray-100">QR Code Tickets</h3>
                    <p class="text-gray-600 dark:text-gray-300">Unique QR codes for each ticket make entry seamless and prevent fraud.</p>
                </div>

                <div class="feature-card p-8 bg-white dark:bg-gray-700 rounded-xl shadow-md hover:shadow-xl transition-all">
                    <div class="w-16 h-16 bg-yellow-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-900 dark:text-gray-100">Real-time Analytics</h3>
                    <p class="text-gray-600 dark:text-gray-300">Track ticket sales, revenue, and attendee demographics in real-time.</p>
                </div>

                <div class="feature-card p-8 bg-white dark:bg-gray-700 rounded-xl shadow-md hover:shadow-xl transition-all">
                    <div class="w-16 h-16 bg-red-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-900 dark:text-gray-100">Discount Codes</h3>
                    <p class="text-gray-600 dark:text-gray-300">Create and manage discount codes to boost your ticket sales.</p>
                </div>

                <div class="feature-card p-8 bg-white dark:bg-gray-700 rounded-xl shadow-md hover:shadow-xl transition-all">
                    <div class="w-16 h-16 bg-indigo-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-900 dark:text-gray-100">Custom Subdomains</h3>
                    <p class="text-gray-600 dark:text-gray-300">Your events get their own dedicated subdomain for a branded experience.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="events" class="py-20 bg-gray-50 dark:bg-gray-800 section-bg-pattern-light dark:section-bg-pattern-dark">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-gray-200 mb-4">Featured Events</h2>
                <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">Check out some upcoming events</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($featuredEvents as $event)
                    <div class="event-card bg-white dark:bg-gray-700 rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-2">
                        <div class="relative h-48 overflow-hidden">
                            @if($event->event_image_url)
                                <img src="{{ $event->event_image_url }}"
                                     alt="{{ $event->name }}"
                                     class="w-full h-full object-cover event-image">
                            @else
                                <svg class="w-full h-full text-gray-200 event-image" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="100" height="100" fill="currentColor"/>
                                    <path d="M30 35H70M30 50H70M30 65H50" stroke="#9CA3AF" stroke-width="2" stroke-linecap="round"/>
                                    <circle cx="20" cy="35" r="3" fill="#9CA3AF"/>
                                    <circle cx="20" cy="50" r="3" fill="#9CA3AF"/>
                                    <circle cx="20" cy="65" r="3" fill="#9CA3AF"/>
                                </svg>
                            @endif
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-4">
                                <span class="text-white font-semibold">{{ $event->organization?->name }}</span>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $event->name }}</h3>
                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                {{ $event->ticketTypes->count() }} ticket types
                            </span>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 mb-4 line-clamp-2">{{ $event->description }}</p>
                            <div class="flex items-center text-gray-500 dark:text-gray-400 mb-4 @if(!$event->venue) opacity-0 @endif">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>{{ $event->venue?->name }}</span>
                            </div>
                            <div class="flex items-center text-gray-500 dark:text-gray-400 mb-6">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span>{{ $event->date->format('F j, Y') }} at {{ $event->date->format('g:i A') }}</span>
                            </div>
                            <a href="{{ route('event.tickets', [$event->organization->subdomain, 'eventuniqid' => $event->uniqid]) }}"
                               class="w-full block text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                                Get Tickets
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-20 bg-gradient-to-r from-blue-600 to-purple-600 text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">Ready to Create Your Event?</h2>
            <p class="text-xl max-w-2xl mx-auto mb-8">Join thousands of organizers who trust Easytix for their event ticketing needs.</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="#features" class="bg-transparent border-2 border-white hover:bg-white hover:text-blue-600 px-8 py-3 rounded-lg font-semibold text-lg transition-all transform hover:scale-105">
                    Learn More
                </a>
            </div>
        </div>
    </section>
</div>

