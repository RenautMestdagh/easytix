<div>
    <section class="relative py-20 bg-gradient-to-r from-blue-600 to-purple-600 text-white overflow-hidden">
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-3xl mx-auto text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-6">Help Center</h1>
                <p class="text-xl md:text-2xl">Find answers to common questions or get in touch with our support team.</p>
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

    <section class="py-20 bg-gray-50 dark:bg-gray-800 section-bg-pattern-light dark:section-bg-pattern-dark">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-16">
                <div class="bg-white dark:bg-gray-700 p-8 rounded-xl shadow-md hover:shadow-xl transition-all transform hover:-translate-y-1">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-900 dark:text-gray-100">FAQs</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Find answers to frequently asked questions about ticket purchasing, event management, and more.</p>
                    <a href="#faqs" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">View FAQs</a>
                </div>

                <div class="bg-white dark:bg-gray-700 p-8 rounded-xl shadow-md hover:shadow-xl transition-all transform hover:-translate-y-1">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-900 dark:text-gray-100">Contact Support</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Can't find what you're looking for? Our support team is ready to help you with any questions.</p>
                    <a href="#contact" class="text-purple-600 dark:text-purple-400 hover:underline font-medium">Contact Us</a>
                </div>
            </div>

            <div id="ticket-recovery" class="bg-blue-50 dark:bg-blue-900/40 rounded-xl p-8 mb-16">
                <div class="max-w-2xl mx-auto text-center">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">Lost Your Tickets?</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">Enter the email address you used to purchase your tickets and we'll send you a list of all your upcoming events.</p>

                    <form wire:submit.prevent="recoverTickets" class="max-w-md mx-auto">
                        <div class="mb-4">
                            <label for="email" class="sr-only">Email Address</label>
                            <input type="email" wire:model="email" id="email"
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="your@email.com" required>
                            @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors hover:cursor-pointer">
                            Recover Tickets
                        </button>
                    </form>

                    @if($recoverMessage)
                        <div class="mt-4 p-4 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-lg">
                            {{ $recoverMessage }}
                        </div>
                    @endif
                </div>
            </div>

            <div id="faqs" class="mb-16">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-8 text-center">Frequently Asked Questions</h2>

                <div class="max-w-3xl mx-auto space-y-4">
                    <div class="bg-white dark:bg-gray-700 rounded-xl shadow-md overflow-hidden transition-all duration-200">
                        <button class="faq-toggle w-full flex justify-between items-center p-6 text-left hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">How do I purchase tickets?</h3>
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-content px-6 py-6 border-t-2 border-gray-200 dark:border-gray-600 hidden">
                            <p class="text-gray-600 dark:text-gray-400">To purchase tickets, simply browse our events, select the one you're interested in, choose your ticket type and quantity, and proceed to checkout. You'll receive your tickets via email immediately after payment.</p>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-700 rounded-xl shadow-md overflow-hidden transition-all duration-200">
                        <button class="faq-toggle w-full flex justify-between items-center p-6 text-left hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Can I get a refund for my tickets?</h3>
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-content px-6 py-6 border-t-2 border-gray-200 dark:border-gray-600 hidden">
                            <p class="text-gray-600 dark:text-gray-400 pb-4">It is not possible to get a refund for your tickets. If you have any questions or concerns about your tickets, please contact our support team.</p>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-700 rounded-xl shadow-md overflow-hidden transition-all duration-200">
                        <button class="faq-toggle w-full flex justify-between items-center p-6 text-left hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">How do I access my tickets?</h3>
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="faq-content px-6 py-6 border-t-2 border-gray-200 dark:border-gray-600 hidden">
                            <p class="text-gray-600 dark:text-gray-400 pb-4">After purchasing tickets, you'll receive an email with on overview of your order. In the email are buttons to download your tickets and view your order details.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div id="contact" class="bg-white dark:bg-gray-700 rounded-xl p-8 shadow-md">
                <div class="max-w-2xl mx-auto">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6 text-center">Contact Our Support Team</h2>
                    <form wire:submit.prevent="sendSupportMessage" class="space-y-4">

                        <x-ui.forms.group label="Your Name" for="name" error="name">
                            <x-ui.forms.input
                                wire:model.lazy="name"
                                name="name"
                                placeholder="Enter your name"
                                error="{{ $errors->has('name') }}"
                                class="rounded-xl shadow-md focus:ring-2 focus:ring-indigo-500"
                            />
                        </x-ui.forms.group>

                        <x-ui.forms.group label="Email Address" for="contactEmail" error="contactEmail">
                            <x-ui.forms.input
                                wire:model.lazy="contactEmail"
                                name="contactEmail"
                                placeholder="Enter your email"
                                error="{{ $errors->has('contactEmail') }}"
                                class="rounded-xl shadow-md focus:ring-2 focus:ring-indigo-500"
                            />
                        </x-ui.forms.group>

                        <x-ui.forms.group label="Subject" for="subject" error="subject">
                            <x-ui.forms.select
                                wire:model.lazy="subject"
                                name="subject"
                                error="{{ $errors->has('subject') }}"
                                class="rounded-xl shadow-md focus:ring-2 focus:ring-indigo-500 px-5 w-full"
                            >
                                <option>General Inquiry</option>
                                <option>Technical Support</option>
                                <option>Billing Question</option>
                                <option>Event Organizer Support</option>
                            </x-ui.forms.select>
                        </x-ui.forms.group>

                        <x-ui.forms.group label="Message" for="message" error="message">
                            <x-ui.forms.textarea
                                wire:model.lazy="message"
                                name="message"
                                rows="4"
                                error="{{ $errors->has('message') }}"
                                class="rounded-xl shadow-md focus:ring-2 focus:ring-indigo-500"
                            />
                        </x-ui.forms.group>

                        <div>
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors hover:cursor-pointer">
                                Send Message
                            </button>
                        </div>
                    </form>

                    @if($supportMessage)
                        <div class="mt-4 p-4 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-lg">
                            {{ $supportMessage }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // FAQ toggle functionality
            document.querySelectorAll('.faq-toggle').forEach(button => {
                button.addEventListener('click', function() {
                    const content = this.nextElementSibling;
                    const icon = this.querySelector('svg');

                    // Toggle content with animation
                    if (content.classList.contains('hidden')) {
                        content.classList.remove('hidden');
                    } else {
                        content.classList.add('hidden');
                    }
                });
            });

            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();

                    const targetId = this.getAttribute('href');
                    if (targetId === '#') return;

                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        targetElement.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                });
            });
        });
    </script>
</div>
