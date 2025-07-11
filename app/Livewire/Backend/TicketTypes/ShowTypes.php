<?php

namespace App\Livewire\Backend\TicketTypes;

use App\Models\Event;
use App\Traits\FlashMessage;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class ShowTypes extends Component
{
    use FlashMessage;

    public Event $event;

    public function mount(Event $event)
    {
        // Eager load ticketTypes with their ticket counts
        $this->event = $event->load([
            'ticketTypes' => function ($query) {
                $query->withCount('tickets')
                    ->withCount('reservedTickets');
            },
        ]);
    }

    public function editEvent(Event $event)
    {
        session(['events.edit.referrer' => request()->headers->get('referer')]);
        return redirect()->route('events.update', $event);
    }

    public function deleteTicketType($ticketTypeId)
    {
        $this->authorize('ticket-types.delete');

        try {
            $ticketType = $this->event->ticketTypes()->findOrFail($ticketTypeId);
            if ($ticketType->allTickets->count() > 0)
                return $this->flashMessage('Cannot delete ticket type with (reserved) tickets.', 'error');

            $ticketType->delete();
            $this->flashMessage('Ticket type deleted successfully.');

            $this->event->load([
                'ticketTypes' => function ($query) {
                    $query->withCount('tickets');
                },
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting ticket type: ' . $e->getMessage());
            $this->flashMessage('Error deleting ticket type', 'error');
        }
    }

    public function render()
    {
        return view('livewire.backend.tickettypes.show-types', [
            'event' => $this->event,
            'ticketTypes' => $this->event->ticketTypes, // corrected variable name
        ]);
    }
}
