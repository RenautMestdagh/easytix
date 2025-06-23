<div>
    <div class="max-w-2xl mx-auto">
        @if (session()->has('message'))
            <x-ui.flash-message/>
        @endif
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden p-6 max-w-2xl mx-auto">
        @if(in_array($this->redirect_status, ['succeeded', 'complete']))
            {{-- Success State --}}
            <div class="text-center">
                <div class="flex justify-center mb-4">
                    <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-2">Payment Successful!</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Your payment has been processed successfully. You will receive a confirmation email shortly.</p>
                <a
                    href="{{ route('organization.home', $this->event->organization->subdomain) }}"
                    class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-300 ease-in-out"
                >
                    Return to Agenda
                </a>
            </div>

        @elseif($this->redirect_status === 'failed')
            {{-- Failed State --}}
            <div class="text-center">
                <div class="flex justify-center mb-4">
                    <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-2">Payment Failed</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-6">We couldn't process your payment. Please try again or use a different payment method.</p>
                <div class="flex justify-center gap-4">
                    <a
                        href="{{ route('organization.home', $this->event->organization->subdomain) }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-6 rounded-lg transition-colors duration-300 ease-in-out dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200"
                    >
                        Return to Agenda
                    </a>
                    <button
                        wire:click="backToPayment"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-300 ease-in-out"
                    >
                        Try Again
                    </button>
                </div>
            </div>

        @elseif($this->redirect_status === 'processing')
            {{-- Processing State --}}
            <div class="text-center">
                <div class="flex justify-center mb-4">
                    <svg class="w-12 h-12 text-yellow-500 animate-spin " xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                    </svg>

                </div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-2">Payment Processing</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Your payment is being processed. This may take a few moments. You'll receive an email confirmation once complete.</p>
                <a
                    href="{{ route('organization.home', $this->event->organization->subdomain) }}"
                    class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-300 ease-in-out"
                >
                    Return to Event
                </a>
            </div>

        @elseif($this->redirect_status === 'requires_action')
            {{-- Requires Action State --}}
            <div class="text-center">
                <div class="flex justify-center mb-4">
                    <svg class="w-12 h-12 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L4.316 15.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-2">Additional Action Required</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Your payment requires additional verification. Please complete the authentication process.</p>
                <div class="flex justify-center gap-4">
                    <a
                        href="{{ route('organization.home', $this->event->organization->subdomain) }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-6 rounded-lg transition-colors duration-300 ease-in-out dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200"
                    >
                        Return to Agenda
                    </a>
                    <button
                        wire:click="backToPayment"
                        class="bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-300 ease-in-out"
                    >
                        Complete Payment
                    </button>
                </div>
            </div>

        @elseif($this->redirect_status === 'canceled')
            {{-- Canceled State --}}
            <div class="text-center">
                <div class="flex justify-center mb-4">
                    <svg class="w-12 h-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-2">Payment Canceled</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-6">You canceled the payment process. No charges were made to your account.</p>
                <div class="flex justify-center gap-4">
                    <a
                        href="{{ route('organization.home', $this->event->organization->subdomain) }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-6 rounded-lg transition-colors duration-300 ease-in-out dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200"
                    >
                        Return to Agenda
                    </a>
                    <button
                        wire:click="backToPayment"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-300 ease-in-out"
                    >
                        Try Again
                    </button>
                </div>
            </div>

        @elseif(in_array($this->redirect_status, ['expired', 'requires_payment_method']))
            {{-- Expired/Failed State --}}
            <div class="text-center">
                <div class="flex justify-center mb-4">
                    <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-2">
                    @if($this->redirect_status === 'expired')
                        Payment Session Expired
                    @else
                        Payment Failed
                    @endif
                </h2>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    @if($this->redirect_status === 'expired')
                        Your payment session has expired. Please start a new payment process.
                    @else
                        There was an issue with your payment method. Please try with a different payment method.
                    @endif
                </p>
                <div class="flex justify-center gap-4">
                    <a
                        href="{{ route('organization.home', $this->event->organization->subdomain) }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-6 rounded-lg transition-colors duration-300 ease-in-out dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200"
                    >
                        Return to Agenda
                    </a>
                    <button
                        wire:click="backToPayment"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-300 ease-in-out"
                    >
                        Try Again
                    </button>
                </div>
            </div>

        @elseif($this->redirect_status === 'temporary_order_not_found')
            {{-- Temporary order not found --}}
            <div class="text-center">
                <div class="flex justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-red-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-2">An error occurred</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Your payment is being processed and may have succeeded. Please allow up to 15 minutes for confirmation. If no email arrives, kindly retry. We apologize for the inconvenience.</p>
                <div class="flex justify-center gap-4">
                    <a
                        href="{{ route('organization.home', $this->event->organization->subdomain) }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-6 rounded-lg transition-colors duration-300 ease-in-out dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200"
                    >
                        Return to Agenda
                    </a>
                </div>
            </div>

        @else
            {{-- Default/Unknown State --}}
            <div class="text-center">
                <div class="flex justify-center mb-4">
                    <svg class="w-12 h-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-2">Payment Status Unknown</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-6">We're unable to determine your payment status. Please contact support or check your email for confirmation.</p>
                <div class="flex justify-center gap-4">
                    <a
                        href="{{ route('event.tickets', [$this->event->organization->subdomain, $this->event->uniqid]) }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-6 rounded-lg transition-colors duration-300 ease-in-out dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200"
                    >
                        Return to Event
                    </a>
                    <button
                        wire:click="backToPayment"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-300 ease-in-out"
                    >
                        Try Again
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>
