<?php

namespace App\Livewire\Frontend;

use App\Models\Event;
use App\Models\TemporaryOrder;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Component;
use Stripe\StripeClient;

class PaymentConfirmation extends Component
{
    public Event $event;
    public TemporaryOrder $tempOrder;

    public function mount($subdomain, $eventuniqid)
    {
        $this->event = Event::with(['ticketTypes' => function ($query) {
            $query->where('is_published', true)->with('tickets');
        }])
            ->where('uniqid', $eventuniqid)
            ->firstOrFail();

        $paymentIntentId = request()->query('payment_intent');
        $this->tempOrder = TemporaryOrder::where('payment_intent_id', $paymentIntentId)
            ->firstOrFail();

        if ($this->tempOrder->checkout_stage === 0) {
            return $this->backToTickets();
        }

        $stripe = new StripeClient(config('app.stripe.secret'));
        $paymentIntent = $stripe->paymentIntents->retrieve($this->tempOrder->payment_intent_id);

        // https://docs.stripe.com/payments/paymentintents/lifecycle#intent-statuses
        switch ($paymentIntent->status) {
            case 'requires_payment_method':
            case 'requires_confirmation':
            case 'requires_action':
            case 'canceled':
                $this->backToPayment();
                break;
            case 'processing':

                break;
            case 'succeeded':

                break;
            case 'requires_capture':
                throw new Exception('Should not happen');
            default:
                throw new Exception('Unknown payment intent status: ' . $paymentIntent->status);
        }

//        $this->orderTickets = $this->tempOrder->tickets
//            ->groupBy('ticket_type_id')
//            ->sortBy(function ($tickets, $ticketTypeId) {
//                return $ticketTypeId;
//            })
//            ->map(function ($tickets) {
//                $firstTicket = $tickets->first();
//                return (object) [
//                    'name' => $firstTicket->ticketType->name,
//                    'price_cents' => $firstTicket->ticketType->price_cents,
//                    'amount' => $tickets->count(),
//                ];
//            })
//            ->values();
    }

    public function backToPayment()
    {
        $this->tempOrder->checkout_stage = 2;
        $this->tempOrder->save();

        return redirect()->route('event.payment', [$this->event->organization->subdomain, $this->event->uniqid]);
    }
//
//    public function backToCheckout()
//    {
//        return redirect()->route('event.checkout', [$this->event->organization->subdomain, $this->event->uniqid]);
//    }
//
    public function backToTickets()
    {
        $this->tempOrder->checkout_stage = 0;
        $this->tempOrder->save();
        return redirect()->route('event.tickets', [$this->event->organization->subdomain, $this->event->uniqid]);
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
