<?php

namespace App\Livewire\Frontend;

use App\Traits\FlashMessage;
use App\Traits\NavigateEventCheckout;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Stripe\StripeClient;

class EventPayment extends Component
{
    use NavigateEventCheckout, FlashMessage;

    public $stripeClientSecret;


    public function boot()
    {
        $this->initialize();
    }

    public function mount($subdomain, $eventuniqid)
    {
        if(!$this->checkCorrectFlow())
            return;
        try {
            $stripe = new StripeClient(config('app.stripe.secret'));
            $paymentIntent = $stripe->paymentIntents->retrieve($this->tempOrder->payment_id);
        } catch (\Exception $e) {
            Log::error('Error retrieving paymentIntent: ' . $e->getMessage());
            $this->flashMessage('An error occurred, please refresh this page.', 'error');
            return;
        }

        if($paymentIntent->status === 'succeeded'){
            session()->put('payment_succeeded', true);
            return redirect($this->event->confirmation_url);
        }

        $this->stripeClientSecret = $paymentIntent->client_secret;
    }

    public function backToCheckout()
    {
        $mustCancelPaymentIntent = !empty($this->tempOrder->payment_id);

        $this->tempOrder->payment_id = null;
        $this->tempOrder->checkout_stage = 1;
        try {
            $this->tempOrder->save();
        }  catch (\Exception $e) {
            Log::error('Error backing to checkout: ' . $e->getMessage());
            $this->flashMessage('An error occurred, please try again.', 'error');
            return;
        }

        if($mustCancelPaymentIntent) {
            try {
                $stripe = new StripeClient(config('app.stripe.secret'));
                $stripe->paymentIntents->cancel($this->tempOrder->payment_id, [
                    'cancellation_reason' => 'requested_by_customer'
                ]);
            } catch (\Exception $e) {}
        }

        redirect($this->event->checkout_url);
    }

    public function submitPayment()
    {
        $this->tempOrder->checkout_stage = 3;
        $this->tempOrder->save();
    }

    public function render()
    {
        return view('livewire.frontend.event-ticketflow')
            ->layout('components.layouts.organization', [
                'backgroundOverride' => $this->event->background_image_url ?? null,
                'logoOverride' => $this->event->header_image_url ?? null
            ]);
    }
}
