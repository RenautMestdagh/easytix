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

        $this->tempOrder = TemporaryOrder::with(['tickets' => function($query) use ($event) {
            $query->whereIn('ticket_type_id', $event->ticketTypes->pluck('id'));
        }])
            ->where('basket_id', session('basket_id'))
            ->first();

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
        $uuid = \Str::uuid();
        $this->tempOrder = TemporaryOrder::create([
            'basket_id' => $uuid,
        ]);
        $this->tempOrderId = $this->tempOrder->id;
        session(['basket_id' => $uuid]);
        $this->tempOrder->resetExpiry();
        foreach ($this->ticketTypes as $ticketType) {
            $this->quantities[$ticketType->id] = 0;
        }
        $this->timeRemaining = null;
    }

    public function updateTimeRemaining()
    {
        if (!$this->tempOrder->expires_at) {
            $this->timeRemaining = 'EXPIRED';
            $this->pollInterval = 60000; // No need to poll frequently if expired
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
            $this->pollInterval = 60500; // 1 minute
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

        if (collect($this->quantities)->flatten()->sum() == 0) {
            $this->tempOrder->resetExpiry();
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

        $result = [];
        foreach ($this->ticketTypes as $type) {
            $reserved = $counts[$type->id]->reserved ?? 0;
            $sold = $counts[$type->id]->sold ?? 0;
            $this->remainingQuantities[$type->id] = $this->determineAvailability($type->available_quantity, $reserved, $sold);
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
            $ticketType->available_quantity,
            $counts->reserved ?? 0,
            $counts->sold ?? 0
        );
    }

    protected function determineAvailability($total, $reserved, $sold)
    {
        return $sold >= $total ? -99 : ($total - $reserved - $sold);
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
        if($this->remainingQuantities[$ticketType->id] < 0) {
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
        return view('livewire.event-tickets')
            ->layout('components.layouts.organization', [
                'backgroundOverride' => $this->event->background_image_url ?? null,
                'logoOverride' => $this->event->header_image_url ?? null
            ]);
    }
}
