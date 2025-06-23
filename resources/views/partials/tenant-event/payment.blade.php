<div>

    @if (session()->has('message'))
        <x-ui.flash-message/>
    @endif

    @include('partials.tenant-event.order-summary')
    <div class="flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200 mb-8">Payment</h1>

            <!-- Stripe Payment Element - visibility controlled by server-side state -->
            <div id="payment-element" class="rounded-md mb-8">
            </div>

            <!-- Processing message - visibility controlled by server-side state -->
            <div id="processing-message" class="rounded-md mb-8 p-4 bg-gray-100 dark:bg-gray-700">
                <div class="flex items-center justify-center space-x-2">
                    <svg class="animate-spin h-5 w-5 text-gray-600 dark:text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-gray-600 dark:text-gray-300">Processing your payment...</span>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-between">
        <x-ui.button
            variant="secondary"
            wire:click="backToCheckout"
            wire:loading.attr="disabled"
            wire:target="backToCheckout"
        >
                    <span wire:loading.remove wire:target="backToCheckout">
                        Back to Checkout
                    </span>
            <span wire:loading wire:target="backToCheckout">
                        Loading...
                    </span>
        </x-ui.button>
        <x-ui.button
            id="submit-button"
            variant="primary"
            wire:click="submitPayment"
            wire:loading.attr="disabled"
            wire:target="submitPayment"
        >
                    <span wire:loading.remove wire:target="submitPayment">
                        Submit
                    </span>
            <span wire:loading wire:target="submitPayment">
                        Processing...
                    </span>
        </x-ui.button>
    </div>

    <script src="https://js.stripe.com/v3/"></script>

    <script>
        const processingMessage = document.getElementById('processing-message');
        processingMessage.classList.add('hidden');
        if('{{$this->timeRemaining}}' !== 'EXPIRED') {
            // Order expiration timer
            const expiresAt = "{{$this->tempOrder->expires_at->timestamp}}";

            let pollInterval = 1000; // Initial interval (1 second)
            let timer = setInterval(() => {
                const newInterval = updateTimeRemaining(expiresAt);

                // Update polling interval dynamically
                if (newInterval !== pollInterval) {
                    clearInterval(timer);
                    pollInterval = newInterval;
                    setInterval(() => updateTimeRemaining(expiresAt), pollInterval);
                }
            }, pollInterval);

            @if(!empty($this->stripeClientSecret))
            // Only initialize Stripe if we're not already processing
            const stripe = Stripe("{{config('app.stripe.key')}}");

            const appearance = {
                theme: document.documentElement.classList.contains('dark') ? 'night' : 'stripe',
            };

            const elements = stripe.elements({
                clientSecret: "{{$this->stripeClientSecret}}",
                appearance,
            });

            var paymentElement = elements.create('payment');
            paymentElement.mount('#payment-element');

            // Submit payment details
            const submitBtn = document.getElementById('submit-button');
            const paymentElementDiv = document.getElementById('payment-element');

            function handleError(submitError) {
                // Tell Livewire we're no longer processing
                alert(submitError.message);
                return window.location.reload();
            }

            submitBtn.addEventListener('click', async (event) => {
                // Prevent multiple form submissions
                if (submitBtn.disabled) {
                    return;
                }

                // Disable form submission while loading
                submitBtn.disabled = true;
                clearInterval(timer);

                // Hide payment element and show processing message
                paymentElementDiv.style.display = 'none';
                processingMessage.style.display = 'block';

                // Trigger form validation and wallet collection
                const {error: submitError} = await elements.submit();
                if (submitError) {
                    handleError(submitError);
                    return;
                }

                const clientSecret = "{{session('stripe_client_secret')}}"

                // Confirm the PaymentIntent using the details collected by the Payment Element
                const result = await stripe.confirmPayment({
                    elements,
                    clientSecret,
                    confirmParams: {
                        return_url: '{{ route("stripe.payment.confirmation", [$this->event->organization->subdomain, $this->event->uniqid]) }}',
                    },
                });

                if (result.error) {
                    handleError(result.error);
                }
            });
            @endif
        }

        function updateTimeRemaining(expiresAt) {
            const now = Math.floor(Date.now() / 1000); // Current time in seconds
            let seconds = Math.max(0, expiresAt - now);

            if (seconds <= 0) {
                window.location.reload();
                return 99999;
            }

            const minutes = Math.floor(seconds / 60);
            seconds = seconds % 60;

            let timeRemainingText;
            let pollInterval;

            if (minutes >= 2) {
                timeRemainingText = minutes === 1 ? '1 minute' : `${minutes} minutes`;
                pollInterval = 60000; // 1 minute (60,000ms)
            } else {
                const minutesText = minutes === 1 ? '1 minute' : `${minutes} minutes`;
                const secondsText = seconds === 1 ? '1 second' : `${seconds} seconds`;

                if (minutes > 0) {
                    timeRemainingText = `${minutesText} ${secondsText}`;
                } else {
                    timeRemainingText = secondsText;
                }
                pollInterval = 1000; // 1 second
            }

            // Update the UI
            document.getElementById('timeRemaining').textContent = timeRemainingText;

            // Return the new polling interval (if using manual polling)
            return pollInterval;
        }
    </script>

</div>
