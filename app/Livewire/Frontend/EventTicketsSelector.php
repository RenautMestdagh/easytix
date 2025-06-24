<?php

namespace App\Livewire\Frontend;

use App\Models\Ticket;
use App\Models\TicketType;
use App\Traits\FlashMessage;
use App\Traits\NavigateEventCheckout;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class EventTicketsSelector extends Component
{
    use NavigateEventCheckout, FlashMessage;

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

        $this->event->loadCount(['tickets', 'reserved_tickets']);
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

        $this->event->loadCount(['tickets', 'reserved_tickets']);
        $this->remainingQuantities[$ticketType->id] = $this->determineAvailability($ticketType, $reserved, $sold);
    }

    protected function determineAvailability($ticketType, $reserved, $sold)
    {
        $soldTickets = $this->event->sold_tickets_count;
        $reservedTickets = $this->event->reserved_tickets_count;
        $ticketTypeMaxQuantity = $ticketType->available_quantity;

        // Initialize availability variables
        $availability = [
            'ticketsLeft' => null,
            'plusDisabledFrom' => null,
            'soldout' => false,
        ];

        // Calculate based on event capacity if it exists
        if ($this->event->capacity !== null) {
            $availability['ticketsLeft'] = $this->event->capacity - $soldTickets - $reservedTickets;
            $availability['plusDisabledFrom'] = $this->event->capacity - $soldTickets;
            $availability['soldout'] = $soldTickets >= $this->event->capacity;
        }

        // Handle ticket type specific limits
        if ($ticketTypeMaxQuantity !== null) {
            $ticketTypeAvailable = $ticketTypeMaxQuantity - $reserved - $sold;

            if ($this->event->capacity !== null) {
                // When both event capacity and ticket quantity are set, use the more restrictive value
                $availability['ticketsLeft'] = min($availability['ticketsLeft'], $ticketTypeAvailable);
                $availability['plusDisabledFrom'] = min(
                    $availability['plusDisabledFrom'],
                    $ticketTypeMaxQuantity - $sold
                );
            } else {
                // When only ticket quantity is set
                $availability['ticketsLeft'] = $ticketTypeAvailable;
                $availability['plusDisabledFrom'] = $ticketTypeMaxQuantity - $sold;
            }

            $availability['soldout'] = $availability['soldout'] || ($sold >= $ticketTypeMaxQuantity);
        }

        if($availability['soldout'] && $this->quantities[$ticketType->id]->amount) {
            $this->handleSoldOutTicketType($ticketType);
        }

        return (object) $availability;

    }

    protected function handleSoldOutTicketType($ticketType)
    {
        $this->quantities[$ticketType->id]->amount = 0;
        $this->tempOrder->tickets
            ->where('ticket_type_id', $ticketType->id)
            ->each(function($ticket) {
                $ticket->delete();
            });
    }

    public function increment($ticketTypeId)
    {
        if ($this->tempOrder->payment_id) return;

        $ticketType = $this->event->ticketTypes->firstWhere('id', $ticketTypeId);
        if (!$ticketType) return;

        for ($tries = 0; $tries < 3; $tries++) {
            try {
                DB::beginTransaction();

                // First, lock all ticket types for the event to prevent concurrent modifications
                $ticketTypes = TicketType::where('event_id', $this->event->id)
                    ->orderBy('id') // Ensure consistent ordering for lock
                    ->lockForUpdate()
                    ->get();

                // Then lock and count tickets for each ticket type
                $ticketCounts = Ticket::whereIn('ticket_type_id', $ticketTypes->pluck('id'))
                    ->selectRaw('ticket_type_id, COUNT(*) as count')
                    ->groupBy('ticket_type_id')
                    ->pluck('count', 'ticket_type_id')
                    ->toArray();

                $totalTickets = array_sum($ticketCounts);

                if (
                    $ticketType->available_quantity && ($ticketCounts[$ticketTypeId]??0) >= $ticketType->available_quantity ||
                    $this->event->capacity && $totalTickets >= $this->event->capacity
                ) {
                    DB::rollBack();
//                    $this->flashMessage('No more tickets available', 'error');
                    return;
                }

                // Create ticket
                Ticket::create([
                    'temporary_order_id' => $this->tempOrder->id,
                    'ticket_type_id' => $ticketTypeId,
                ]);

                DB::commit();

                // Update UI after success
                $this->calculateAvailableTickets($ticketTypeId);
                $this->quantities[$ticketTypeId]->amount++;

                if (collect($this->quantities)->sum('amount') === 1) {
                    $this->tempOrder->resetExpiry();
                    $this->updateTimeRemaining();
                }

                break; // success, exit loop
            } catch (\Exception $e) {
                DB::rollBack();

                if ($tries >= 2) {
                    Log::error('Error adding ticket to cart: ' . $e->getMessage());
                    $this->flashMessage('Error adding ticket to cart', 'error');
                }

                usleep(100 * 1000);
            }
        }
    }


    public function decrement($ticketTypeId)
    {
        if($this->tempOrder->payment_id) return;

        $deleted = false;
        try{
            $deleted = $this->tempOrder->tickets()->where('ticket_type_id', $ticketTypeId)->limit(1)?->delete();
        } catch(QueryException $e) {
            $this->flashMessage('An error occurred, please try again.', 'error');
            Log::error('Error decrementing quantity: ' . $e->getMessage());
        }

        if(!$deleted) return;

        $this->quantities[$ticketTypeId]->amount--;
        if (collect($this->quantities)->sum('amount') == 0) {
            $this->tempOrder->resetExpiry();
            $this->updateTimeRemaining();
        }
    }

    public function proceedToCheckout()
    {
        if ($this->tempOrder->tickets->count() == 0) {
            $this->flashMessage('You must select at least one ticket.', 'error');
            return;
        }

        $this->tempOrder->checkout_stage = 1;
        try {
            $this->tempOrder->save();
            redirect($this->event->checkout_url);
        } catch (QueryException $e) {
            $this->flashMessage('An error occurred, please try again.', 'error');
        }
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
