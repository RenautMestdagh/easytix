<?php

namespace App\Livewire\Frontend;

use App\Models\Ticket;
use App\Traits\NavigateEventCheckout;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class EventTicketsSelector extends Component
{
    use NavigateEventCheckout;

    public $remainingQuantities = [];

    public function boot()
    {
        $this->initialize();
    }
    public function mount($subdomain, $eventuniqid)
    {
        if(!$this->checkCorrectFlow())
            return;
        $this->calculateAllAvailableTickets();
    }

    public function calculateAllAvailableTickets()
    {

        $counts = Ticket::whereIn('ticket_type_id', $this->event->ticketTypes->pluck('id'))
            ->select([
                'ticket_type_id',
                DB::raw('SUM(temporary_order_id IS NOT NULL) as reserved'),
                DB::raw('SUM(order_id IS NOT NULL) as sold')
            ])
            ->groupBy('ticket_type_id')
            ->get()
            ->keyBy('ticket_type_id');

        foreach ($this->event->ticketTypes as $type) {
            $reserved = $counts[$type->id]->reserved ?? 0;
            $sold = $counts[$type->id]->sold ?? 0;
            $this->remainingQuantities[$type->id] = $this->determineAvailability($type, $reserved, $sold);
        }
    }

    public function calculateAvailableTickets($ticketTypeId)
    {
        $ticketType = $this->event->ticketTypes->firstWhere('id', $ticketTypeId);
        if (!$ticketType) {
            throw new \Exception("Ticket type {$ticketTypeId} not found");
        }

        $counts = Ticket::where('ticket_type_id', $ticketTypeId)
            ->select([
                DB::raw('SUM(temporary_order_id IS NOT NULL) as reserved'),
                DB::raw('SUM(order_id IS NOT NULL) as sold')
            ])
            ->first();

        $reserved = $counts->reserved ?? 0;
        $sold = $counts->sold ?? 0;
        $this->remainingQuantities[$ticketType->id] = $this->determineAvailability($ticketType, $reserved, $sold);
    }

    protected function determineAvailability($ticketType, $reserved, $sold)
    {
        $soldTickets = $this->event->tickets->count();
        $reservedTickets = $this->event->reserved_tickets->count();

        $ticketType_max_quantity = $ticketType->available_quantity;

        // If tickettype has unlimited quantity
        $ticketsLeft = $this->event->max_capacity - $soldTickets - $reservedTickets;
        $plusDisabledFrom = $this->event->max_capacity - $soldTickets;
        $soldout = $soldTickets >= $this->event->max_capacity;

        if($ticketType_max_quantity !== null) {
            // If tickettype doesnt have unlimited quantity
            $ticketsLeft = min($ticketsLeft, $ticketType_max_quantity - $reserved - $sold);
            $plusDisabledFrom = min($plusDisabledFrom, $ticketType_max_quantity - $sold);
            $soldout |= $sold >= $ticketType_max_quantity;
        }

        if($soldout) {
            // This case should only happen if the organizer changes ticket quantity or event capacity
            $this->quantities[$ticketType->id]->amount = 0;
            $this->tempOrder->tickets->where('ticket_type_id', $ticketType->id)->each(function($ticket) {
                $ticket->delete();
            });
        }

        return (object)[
            'ticketsLeft' => $ticketsLeft,
            'plusDisabledFrom' => $plusDisabledFrom,
            'soldout' => $soldout,
        ];
    }

    public function increment($ticketTypeId)
    {
        if($this->tempOrder->payment_id) return;

        $ticketType = $this->event->ticketTypes->firstWhere('id', $ticketTypeId);
        if (!$ticketType) return;

        $ticket = null;
        while (true) {
            try {
                $ticket = Ticket::create([
                    'temporary_order_id' => $this->tempOrder->id,
                    'ticket_type_id' => $ticketTypeId,
                    'qr_code' => uniqid(),
                ]);
                break;
            } catch (QueryException $e) {}
        }

        $this->calculateAvailableTickets($ticketTypeId);
        if($this->remainingQuantities[$ticketType->id]->ticketsLeft < 0) {
            Ticket::where('id', $ticket->id)->delete();
            return;
        }

        $this->quantities[$ticketTypeId]->amount++;

        if (collect($this->quantities)->sum('amount') == 1){
            // on addition of first ticket to cart, reset expiration timer
            $this->tempOrder->resetExpiry();
            $this->updateTimeRemaining();
        }
    }

    public function decrement($ticketTypeId)
    {
        if($this->tempOrder->payment_id) return;

        $deleted = $this->tempOrder->tickets()->where('ticket_type_id', $ticketTypeId)->limit(1)?->delete();
        if(!$deleted) return;

        $this->quantities[$ticketTypeId]->amount--;

        if (collect($this->quantities)->sum('amount') == 0){
            $this->tempOrder->resetExpiry();
            $this->updateTimeRemaining();
        }
    }

    public function proceedToCheckout()
    {
        if ($this->tempOrder->tickets->count() == 0) {
            session()->flash('message', __('You must select at least one ticket.'));
            session()->flash('message_type', 'error');
            return;
        }

        $this->tempOrder->checkout_stage = 1;
        $this->tempOrder->save();

        return redirect()->route('event.checkout', [$this->event->organization->subdomain, $this->event->uniqid]);
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
