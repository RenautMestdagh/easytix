<?php

namespace App\Livewire\Frontend;

use App\Models\Customer;
use App\Models\Event;
use App\Models\TemporaryOrder;
use Livewire\Component;
use Stripe\StripeClient;

class EventPayment extends Component
{
    public Event $event;
    public $tempOrderId;
    public $orderTickets;
    public $timeRemaining;
    public $pollInterval = 60000; // Default to 1 minute (60000ms)
    public $flowStage = 3;
    private $tempOrder;

    public function boot()
    {
        if(!$this->tempOrderId)
            return;
        $this->tempOrder = TemporaryOrder::find($this->tempOrderId);
        if(!$this->tempOrder) {
            // temporder was expired and cleaned up by scheduler
            $this->orderExpired();
        }  else if (!$this->tempOrder->at_checkout) {
            $this->backToTickets();
        }
    }
    public function mount($subdomain, $eventuniqid)
    {
        $this->event = Event::with(['ticketTypes' => function ($query) {
            $query->where('is_published', true)->with('tickets');
        }])
            ->where('uniqid', $eventuniqid)
            ->where('is_published', true)
            ->firstOrFail();

        $this->tempOrderId = session('temporary_order_id');
        $this->tempOrder = TemporaryOrder::with('tickets.ticketType')->find($this->tempOrderId);

        if (!$this->tempOrder->at_checkout) {
            $this->backToTickets();
        }

        if(!$this->tempOrder || $this->tempOrder->isExpired()) {
            $this->orderExpired();
        }

        $this->orderTickets = $this->tempOrder->tickets
            ->groupBy('ticket_type_id')
            ->sortBy(function ($tickets, $ticketTypeId) {
                return $ticketTypeId;
            })
            ->map(function ($tickets) {
                $firstTicket = $tickets->first();
                return (object) [
                    'name' => $firstTicket->ticketType->name,
                    'price_cents' => $firstTicket->ticketType->price_cents,
                    'amount' => $tickets->count(),
                ];
            })
            ->values();
    }

    public function newTemporaryOrder()
    {
        redirect()->route('event.tickets', [$this->event->organization->subdomain, $this->event->uniqid]);
    }

    public function orderExpired()
    {
        $basketIds = array_diff(session('basket_id', []), [$this->tempOrderId]);
        session(['basket_id' => $basketIds]);
        $this->timeRemaining = 'EXPIRED';
        $this->pollInterval = 999999999; // No need to poll if expired
    }

    public function backToTickets()
    {
        $this->tempOrder->at_checkout = false;
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
