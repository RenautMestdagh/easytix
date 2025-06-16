<?php

namespace App\Livewire\Frontend;

use App\Models\Event;
use App\Models\TemporaryOrder;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class EventTicketsSelector extends Component
{
    public Event $event;
    public $ticketTypes;
    public $quantities = [];
    public $remainingQuantities = [];
    public $timeRemaining;
    public $pollInterval = 60000; // Default to 1 minute (60000ms)
    public $tempOrderId;
    private $tempOrder;

    public function boot()
    {
        if(!$this->tempOrderId)
            return;
        $this->tempOrder = TemporaryOrder::find($this->tempOrderId);
        if(!$this->tempOrder) {
            // temporder was expired and cleaned up by scheduler
            $basketIds = array_diff(session('basket_id', []), [$this->tempOrderId]);
            session(['basket_id' => $basketIds]);
            $this->orderExpired();
        }
    }
    public function mount($subdomain, $eventuniqid)
    {
        $event = Event::with(['ticketTypes' => function($query) {
            $query->where('is_published', true)->with('tickets');
        }])
            ->where('uniqid', $eventuniqid)
            ->where('is_published', true)
            ->firstOrFail();


        $basketIds = session('basket_id', []);
        $idsToRemove = []; // Store IDs that need removal

        foreach ($basketIds as $basketId) {
            // correct basket can be found by adding event id in query but i've done it this way to clean up expired baskets from session
            $tempOrder = TemporaryOrder::with(['tickets' => function($query) use ($event) {
                $query->whereIn('ticket_type_id', $event->ticketTypes->pluck('id'));
            }])
                ->where('id', $basketId)
                ->first();

            if (!$tempOrder || $tempOrder->isExpired()) {    // basket doesnt exist anymore or is expired
                $idsToRemove[] = $basketId;
            } else if($tempOrder->event_id == $event->id) {
                $this->tempOrder = $tempOrder;
                break;
            }
        }

        // Remove invalid IDs after loop completes
        if (!empty($idsToRemove)) {
            $updatedBasketIds = array_diff($basketIds, $idsToRemove);
            session(['basket_id' => $updatedBasketIds]);
        }


        $this->event = $event;
        $this->ticketTypes = $event->ticketTypes;

        if($this->tempOrder && !$this->tempOrder->isExpired()) {
            if ($this->tempOrder->tickets->isEmpty()) {
                $this->tempOrder->resetExpiry();
            }
            $ticketCounts = $this->tempOrder->tickets->groupBy('ticket_type_id')->map->count();
            foreach ($this->ticketTypes as $ticketType) {
                $this->quantities[$ticketType->id] = $ticketCounts[$ticketType->id] ?? 0;
            }
        } else {
            $this->newTemporaryOrder();
        }

        $this->tempOrderId = $this->tempOrder->id;

        $this->updatePageInfo();
    }

    public function updatePageInfo()
    {
        $this->updateTimeRemaining();
        $this->calculateAllAvailableTickets();
    }

    public function orderExpired()
    {
        $this->newTemporaryOrder();
        $this->updatePageInfo();
    }

    public function newTemporaryOrder() {
        $this->tempOrder = TemporaryOrder::create([
            'event_id' => $this->event->id
        ]);
        $this->tempOrderId = $this->tempOrder->id;
        session()->push('basket_id', $this->tempOrder->id);
        foreach ($this->ticketTypes as $ticketType) {
            $this->quantities[$ticketType->id] = 0;
        }
        $this->timeRemaining = null;
    }

    public function updateTimeRemaining()
    {
        if (collect($this->quantities)->flatten()->sum() == 0) {
            $this->tempOrder->resetExpiry();
            $this->timeRemaining = null;
            $this->pollInterval = 60000;
            return;
        }

        $seconds = max(0, $this->tempOrder->expires_at->timestamp - now()->timestamp);

        if ($seconds <= 0) {
            $this->timeRemaining = 'EXPIRED';
            $this->pollInterval = 60000; // No need to poll frequently if expired
            return;
        }

        $minutes = floor($seconds / 60);
        $seconds = $seconds % 60;

        // Update polling interval based on remaining time
        if ($minutes >= 2) {
            // Format minutes (singular/plural)
            $minutesText = $minutes == 1 ? '1 minute' : "$minutes minutes";
            $this->timeRemaining = $minutesText;
            $this->pollInterval = 60100; // 1 minute
        } else {
            // Format seconds and minutes (singular/plural)
            $minutesText = $minutes == 1 ? '1 minute' : "$minutes minutes";
            $secondsText = $seconds == 1 ? '1 second' : "$seconds seconds";

            if ($minutes > 0) {
                $this->timeRemaining = "$minutesText $secondsText";
            } else {
                $this->timeRemaining = $secondsText;
            }
            $this->pollInterval = 1000; // 1 second
        }
    }

    public function calculateAllAvailableTickets()
    {
        $counts = Ticket::whereIn('ticket_type_id', $this->ticketTypes->pluck('id'))
            ->select([
                'ticket_type_id',
                DB::raw('SUM(temporary_order_id IS NOT NULL) as reserved'),
                DB::raw('SUM(order_id IS NOT NULL) as sold')
            ])
            ->groupBy('ticket_type_id')
            ->get()
            ->keyBy('ticket_type_id');

        foreach ($this->ticketTypes as $type) {
            $reserved = $counts[$type->id]->reserved ?? 0;
            $sold = $counts[$type->id]->sold ?? 0;
            $this->remainingQuantities[$type->id] = $this->determineAvailability($type, $reserved, $sold);
        }
    }

    public function calculateAvailableTickets($ticketTypeId)
    {
        $ticketType = $this->ticketTypes->firstWhere('id', $ticketTypeId);
        if (!$ticketType) {
            throw new \Exception("Ticket type {$ticketTypeId} not found");
        }

        $counts = Ticket::where('ticket_type_id', $ticketTypeId)
            ->select([
                DB::raw('SUM(temporary_order_id IS NOT NULL) as reserved'),
                DB::raw('SUM(order_id IS NOT NULL) as sold')
            ])
            ->first();

        $this->remainingQuantities[$ticketTypeId] =  $this->determineAvailability(
            $ticketType,
            $counts->reserved ?? 0,
            $counts->sold ?? 0
        );
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
            $this->quantities[$ticketType->id] = 0;
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
        $ticketType = $this->ticketTypes->firstWhere('id', $ticketTypeId);
        if (!$ticketType) return;

        $ticket = Ticket::create([
            'temporary_order_id' => $this->tempOrder->id,
            'ticket_type_id' => $ticketTypeId,
            'qr_code' => uniqid()
        ]);

        $this->calculateAvailableTickets($ticketTypeId);
        if($this->remainingQuantities[$ticketType->id]->ticketsLeft < 0) {
            Ticket::where('id', $ticket->id)->delete();
            return;
        }

        $this->quantities[$ticketTypeId]++;

        if (collect($this->quantities)->flatten()->sum() == 1){
            $this->tempOrder->resetExpiry();
            $this->updateTimeRemaining();
        }

    }

    public function decrement($ticketTypeId)
    {
        $deleted = $this->tempOrder->tickets()->where('ticket_type_id', $ticketTypeId)->limit(1)?->delete();
        if(!$deleted) return;

        $this->quantities[$ticketTypeId]--;
    }

    public function render()
    {
        return view('livewire.frontend.event-tickets')
            ->layout('components.layouts.organization', [
                'backgroundOverride' => $this->event->background_image_url ?? null,
                'logoOverride' => $this->event->header_image_url ?? null
            ]);
    }
}
