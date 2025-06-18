<div>
    @include('partials.tenant-event.order-summary')
    <div class="flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200 mb-8">Payment</h1>
            <div id="payment-element" class="rounded-md mb-8"></div>

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
        </div>

        <script src="https://js.stripe.com/v3/"></script>

        <script>
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
                    // Stripe element creation
                    const stripe = Stripe("{{config('app.stripe.key')}}");

                    const appearance = {
                        theme: document.documentElement.classList.contains('dark') ? 'night' : 'stripe',
                    };

                    const elements = stripe.elements({
                        clientSecret: "{{$this->stripeClientSecret}}",
                        appearance,
                    });

                    var paymentElement = elements.create('payment', {
                        // layout: {
                        //     type: 'tabs',
                        //     visibleAccordionItemsCount: 5
                        // },
                    });
                    paymentElement.mount('#payment-element');



                    // Submit payment details
                    const submitBtn = document.getElementById('submit-button');

                    function handleError(submitError) {
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
                        // console.log(result);

                        if (result.error) {
                            // This point is only reached if there's an immediate error when
                            // confirming the payment. Show the error to your customer (for example, payment details incomplete)
                            handleError(result.error);
                        } else {
                            // Your customer is redirected to your `return_url`. For some payment
                            // methods like iDEAL, your customer is redirected to an intermediate
                            // site first to authorize the payment, then redirected to the `return_url`.
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
</div>

