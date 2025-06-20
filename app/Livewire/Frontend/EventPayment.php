<?php

namespace App\Livewire\Frontend;

use App\Traits\NavigateEventCheckout;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Stripe\StripeClient;

class EventPayment extends Component
{
    use NavigateEventCheckout;

    public $stripeClientSecret;


    public function boot()
    {
        $this->initialize();
    }

    public function mount($subdomain, $eventuniqid)
    {
        if(!$this->checkCorrectFlow())
            return;
        $stripe = new StripeClient(config('app.stripe.secret'));
        $paymentIntent = $stripe->paymentIntents->retrieve($this->tempOrder->payment_id);
        if($paymentIntent->status === 'succeeded'){
            session()->put('payment_succeeded', true);
            return redirect()->route('stripe.payment.confirmation', [$subdomain, $eventuniqid]);
        }


        $this->stripeClientSecret = $paymentIntent->client_secret;
    }

    public function backToCheckout()
    {
        if($this->tempOrder->payment_id) {
            $stripe = new StripeClient(config('app.stripe.secret'));
            try {
                $paymentIntent = $stripe->paymentIntents->cancel($this->tempOrder->payment_id, [
                    'cancellation_reason' => 'requested_by_customer'
                ]);
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return;
            }

            $this->tempOrder->payment_id = null;
        }

        $this->tempOrder->checkout_stage = 1;
        $this->tempOrder->save();
        return redirect()->route('event.checkout', [$this->event->organization->subdomain, $this->event->uniqid]);
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
