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
    <meta property="og:title" content="Easytix | Easy Access, Unforgettable Events">
    <meta property="og:description" content="The easiest way to create, manage and sell tickets for your events. Secure payments, real-time analytics, and QR code tickets.">
    <meta property="og:image" content="{{ asset('resources/images/social-preview.jpg') }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url('/') }}">
    <meta name="twitter:title" content="Easytix | Easy Access, Unforgettable Events">
    <meta name="twitter:description" content="The easiest way to create, manage and sell tickets for your events. Secure payments, real-time analytics, and QR code tickets.">
    <meta name="twitter:image" content="{{ asset('resources/images/social-preview.jpg') }}">

    <link rel="icon" type="image/png" href="{{ asset('resources/images/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('resources/images/apple-touch-icon.png') }}">

    <link rel="canonical" href="{{ url('/') }}">

    <title>Easytix | @yield('title', 'Easy Access, Unforgettable Events')</title>

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
    @stack('styles')
</head>
<body class="font-sans antialiased text-gray-900 bg-white">
<nav class="bg-white dark:bg-gray-800 shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ url('/') }}" class="flex-shrink-0 flex items-center focus:outline-none ">
                    <x-app-logo />
                </a>
            </div>
            <div class="hidden sm:ml-6 sm:flex sm:items-center space-x-8">
                <a href="#features" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white px-3 py-2 text-sm font-medium">Features</a>
                <a href="#events" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white px-3 py-2 text-sm font-medium">Events</a>
                <a href="{{ route('help') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white px-3 py-2 text-sm font-medium">Help</a>
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
            <a href="{{ route('help') }}" class="block px-3 py-2 text-base font-medium text-gray-500 hover:text-gray-900 hover:bg-gray-50">Help</a>
        </div>
    </div>
</nav>

<main>
    {{$slot}}
</main>

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
                    <li><a href="{{ route('help') }}" class="text-gray-600 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Help</a></li>
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
            threshold: 0.5,
            rootMargin: '0px 0px -50px 0px'
        };

        // Create an Intersection Observer
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counter = entry.target;
                    const target = +counter.getAttribute('data-count');
                    const duration = 2000;
                    const startTime = performance.now();

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
                    observer.unobserve(counter);
                }
            });
        }, observerOptions);

        counters.forEach(counter => {
            observer.observe(counter);
        });

        // GSAP animations
        gsap.registerPlugin(ScrollTrigger);

        gsap.utils.toArray('.feature-card').forEach(card => {
            gsap.from(card, {
                scrollTrigger: {
                    trigger: card,
                    start: "top 80%",
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
                stagger: 0.1
            });
        });
    });
</script>
@stack('scripts')
</body>
</html>
