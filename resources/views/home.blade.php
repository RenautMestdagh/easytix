<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.head')
    <meta name="description" content="Easytix - The easiest way to create, manage and sell tickets for your events. Powerful event ticketing platform with secure payments and real-time analytics.">
    <meta name="keywords" content="event ticketing, ticket sales, event management, online tickets, QR code tickets, event platform">
    <meta name="author" content="Easytix">
    <meta name="robots" content="index, follow">


    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="Easytix | Your Events, Our Platform">
    <meta property="og:description" content="The easiest way to create, manage and sell tickets for your events. Secure payments, real-time analytics, and QR code tickets.">
    <meta property="og:image" content="{{ asset('resources/images/social-preview.jpg') }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url('/') }}">
    <meta name="twitter:title" content="Easytix | Your Events, Our Platform">
    <meta name="twitter:description" content="The easiest way to create, manage and sell tickets for your events. Secure payments, real-time analytics, and QR code tickets.">
    <meta name="twitter:image" content="{{ asset('resources/images/social-preview.jpg') }}">

    <link rel="icon" type="image/png" href="{{ asset('resources/images/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('resources/images/apple-touch-icon.png') }}">

    <link rel="canonical" href="{{ url('/') }}">

    <title>Easytix | Your Events, Our Platform</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* Animation for circles in hero section */
        .circles-animation {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .circles-animation .circle {
            position: absolute;
            display: block;
            width: 20px;
            height: 20px;
            background: rgba(255, 255, 255, 0.2);
            animation: animate 25s linear infinite;
            bottom: -150px;
            border-radius: 50%;
        }

        .circles-animation .circle:nth-child(1) {
            left: 25%;
            width: 80px;
            height: 80px;
            animation-delay: 0s;
        }

        .circles-animation .circle:nth-child(2) {
            left: 10%;
            width: 20px;
            height: 20px;
            animation-delay: 2s;
            animation-duration: 12s;
        }

        .circles-animation .circle:nth-child(3) {
            left: 70%;
            width: 20px;
            height: 20px;
            animation-delay: 4s;
        }

        .circles-animation .circle:nth-child(4) {
            left: 40%;
            width: 60px;
            height: 60px;
            animation-delay: 0s;
            animation-duration: 18s;
        }

        .circles-animation .circle:nth-child(5) {
            left: 65%;
            width: 20px;
            height: 20px;
            animation-delay: 0s;
        }

        .circles-animation .circle:nth-child(6) {
            left: 75%;
            width: 110px;
            height: 110px;
            animation-delay: 3s;
        }

        .circles-animation .circle:nth-child(7) {
            left: 35%;
            width: 150px;
            height: 150px;
            animation-delay: 7s;
        }

        .circles-animation .circle:nth-child(8) {
            left: 50%;
            width: 25px;
            height: 25px;
            animation-delay: 15s;
            animation-duration: 45s;
        }

        .circles-animation .circle:nth-child(9) {
            left: 20%;
            width: 15px;
            height: 15px;
            animation-delay: 2s;
            animation-duration: 35s;
        }

        .circles-animation .circle:nth-child(10) {
            left: 85%;
            width: 150px;
            height: 150px;
            animation-delay: 0s;
            animation-duration: 11s;
        }

        @keyframes animate {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 1;
                border-radius: 0;
            }
            100% {
                transform: translateY(-1000px) rotate(720deg);
                opacity: 0;
                border-radius: 50%;
            }
        }

        /* Animation for feature cards */
        .feature-card:hover {
            transform: translateY(-5px);
        }

        /* Animation for counting numbers */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .animate-fade-in-down {
            animation: fadeInDown 1s ease-out forwards;
        }

        .animate-fade-in-up {
            animation: fadeInUp 1s ease-out forwards;
        }

        .delay-100 {
            animation-delay: 0.1s;
        }

        .delay-200 {
            animation-delay: 0.2s;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Line clamp for event descriptions */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* NEW: Subtle pattern for sections */
        .section-bg-pattern-light {
            background-color: #d0e7ff; /* Tailwind: bg-gray-50 */
            background-image: radial-gradient(rgba(0, 0, 0, 0.2) 1px, transparent 1px); /* Subtle dots */
            background-size: 20px 20px;
        }

        .dark .section-bg-pattern-light {
            background-color: #ff0707; /* Tailwind: bg-gray-800 */
            background-image: radial-gradient(#374151 1px, transparent 1px); /* Subtle dots for dark mode */
            background-size: 20px 20px;
        }

        /* NEW: Enhanced dark mode colors for sections */
        .dark .bg-gray-50 {
            background-color: #1a202c; /* Slightly darker than 800, almost black */
        }
        .dark .bg-gray-800 {
            background-color: #1f2937; /* Even darker for contrast */
        }
        .dark .bg-white {
            background-color: #2d3748; /* Darker card background */
        }
        .dark .text-gray-900 {
            color: #e2e8f0; /* Light text for dark mode */
        }
        .dark .text-gray-600 {
            color: #cbd5e0; /* Lighter gray text */
        }
        .dark .text-gray-400 {
            color: #a0aec0; /* Even lighter gray */
        }
        .dark .shadow-lg {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3), 0 4px 6px -2px rgba(0, 0, 0, 0.05); /* Darker shadow */
        }
        .dark .hover\\:shadow-xl:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4), 0 10px 10px -5px rgba(0, 0, 0, 0.04); /* Darker hover shadow */
        }

        /* NEW: Button hover effects */
        .btn-primary-hero {
            background-color: #fff;
            color: #2563eb; /* blue-600 */
            transition: all 0.3s ease;
        }
        .btn-primary-hero:hover {
            background-color: #eff6ff; /* blue-50 */
            transform: scale(1.05);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* NEW: Event Card specific animation on hover */
        .event-card:hover .event-image {
            transform: scale(1.1);
            filter: brightness(0.8); /* Slightly dim image on hover */
        }

        .event-image {
            transition: transform 0.5s ease, filter 0.5s ease;
        }

    </style>
</head>
<body class="font-sans antialiased text-gray-900 bg-white">
<nav class="bg-white dark:bg-gray-800 shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ url('/') }}" class="flex-shrink-0 flex items-center">
                    <x-app-logo />
                </a>
            </div>
            <div class="hidden sm:ml-6 sm:flex sm:items-center space-x-8">
                <a href="#features" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white px-3 py-2 text-sm font-medium">Features</a>
                <a href="#events" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white px-3 py-2 text-sm font-medium">Events</a>
            </div>
            <div class="-mr-2 flex items-center sm:hidden">
                <button type="button" class="mobile-menu-button inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500" aria-controls="mobile-menu" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div class="hidden mobile-menu sm:hidden" id="mobile-menu">
        <div class="pt-2 pb-3 space-y-1">
            <a href="#features" class="block px-3 py-2 text-base font-medium text-gray-500 hover:text-gray-900 hover:bg-gray-50">Features</a>
            <a href="#events" class="block px-3 py-2 text-base font-medium text-gray-500 hover:text-gray-900 hover:bg-gray-50">Events</a>
        </div>
    </div>
</nav>

<section class="relative bg-gradient-to-r from-blue-600 to-purple-600 text-white py-20 overflow-hidden">
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-3xl mx-auto text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6 animate-fade-in-down">
                Your Events, Our Platform
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
                                 class="w-full h-full object-cover event-image"> {{-- Added event-image class --}}
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

<footer class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 py-12">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="md:col-span-2">
                <div class="flex items-center mb-4">
                    <x-app-logo color="#00ff00" />
                </div>
                <p class="text-gray-600 dark:text-gray-400">The easiest way to create, manage and sell tickets for your events. Powerful event ticketing platform with secure payments and real-time analytics.</p>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                <ul class="space-y-2">
                    <li><a href="#features" class="text-gray-600 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Features</a></li>
                    <li><a href="#events" class="text-gray-600 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Events</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-4">Contact</h3>
                <ul class="space-y-2">
                    <li class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <a href="mailto:easytix.noreply@gmail.com" class="text-gray-600 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">easytix.noreply@gmail.com</a>
                    </li>
                    <li class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                        </svg>
                        <a href="tel:+15551234567" class="text-gray-600 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">+1 (555) 123-4567</a>
                    </li>
                </ul>
                <div class="mt-4 flex space-x-4">
                    <a href="#" class="text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-300 dark:border-gray-700 mt-8 pt-8 text-center text-gray-600 dark:text-gray-400">
            <p>&copy; {{ date('Y') }} Easytix. All rights reserved.</p>
        </div>
    </div>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/ScrollTrigger.min.js"></script>

<script>
    // Mobile menu toggle
    const mobileMenuButton = document.querySelector('.mobile-menu-button');
    const mobileMenu = document.querySelector('.mobile-menu');

    mobileMenuButton.addEventListener('click', function() {
        mobileMenu.classList.toggle('hidden');
        const expanded = mobileMenuButton.getAttribute('aria-expanded') === 'true';
        mobileMenuButton.setAttribute('aria-expanded', !expanded);
    });

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        if (!mobileMenu.contains(event.target) && !mobileMenuButton.contains(event.target)) {
            mobileMenu.classList.add('hidden');
            mobileMenuButton.setAttribute('aria-expanded', 'false');
        }
    });

    // Animate counters
    document.addEventListener('DOMContentLoaded', function() {
        const counters = document.querySelectorAll('.count-up');

        // Options for the Intersection Observer
        const observerOptions = {
            threshold: 0.5, // Trigger when 50% of the element is visible
            rootMargin: '0px 0px -50px 0px' // Adjust when the trigger happens
        };

        // Create an Intersection Observer
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counter = entry.target;
                    const target = +counter.getAttribute('data-count');
                    const duration = 2000; // 2 seconds
                    const startTime = performance.now();

                    // Format number with commas
                    const formatNumber = num => num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

                    const animateCount = (currentTime) => {
                        const elapsedTime = currentTime - startTime;
                        const progress = Math.min(elapsedTime / duration, 1);
                        const currentCount = Math.floor(progress * target);

                        counter.textContent = formatNumber(currentCount);

                        if (progress < 1) {
                            requestAnimationFrame(animateCount);
                        } else {
                            counter.textContent = formatNumber(target);
                        }
                    };

                    requestAnimationFrame(animateCount);
                    observer.unobserve(counter); // Stop observing after animation starts
                }
            });
        }, observerOptions);

        // Observe each counter element
        counters.forEach(counter => {
            observer.observe(counter);
        });


        // GSAP animations for feature cards
        gsap.registerPlugin(ScrollTrigger);

        gsap.utils.toArray('.feature-card').forEach(card => {
            gsap.from(card, {
                scrollTrigger: {
                    trigger: card,
                    start: "top 80%", // When the top of the card hits 80% down the viewport
                    toggleActions: "play none none none"
                },
                opacity: 0,
                y: 50,
                duration: 0.8,
                ease: "power2.out"
            });
        });

        gsap.utils.toArray('.event-card').forEach(card => {
            gsap.from(card, {
                scrollTrigger: {
                    trigger: card,
                    start: "top 80%",
                    toggleActions: "play none none none"
                },
                opacity: 0,
                y: 50,
                duration: 0.8,
                ease: "power2.out",
                stagger: 0.1 // Stagger the animation for each event card
            });
        });
    });
</script>
</body>
</html>
