<?php

namespace App\Livewire\Tickettypes;

use App\Models\Event;
use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Gate;

class ShowTypes extends Component
{
    public Event $event;

    public function mount(Event $event)
    {
        // Eager load ticketTypes with their ticket counts
        $this->event = $event->load([
            'ticketTypes' => function ($query) {
                $query->withCount('tickets');
            },
        ]);
        $this->authorize('ticket types.read');
    }

    public function deleteTicketType($ticketTypeId)
    {
        $ticketType = $this->event->ticketTypes()->findOrFail($ticketTypeId);
        if($ticketType->tickets->count() > 0)
            return false;

        Gate::authorize('tickets.delete', $ticketType);

        $ticketType->delete();

        $this->event->load([
            'ticketTypes' => function ($query) {
                $query->withCount('tickets');
            },
        ]);

        $this->dispatch('notify',
            type: 'success',
            content: 'Ticket type deleted successfully'
        );
    }

    public function render()
    {
        return view('livewire.tickettypes.show-types', [
            'event' => $this->event,
            'ticketTypes' => $this->event->ticketTypes, // corrected variable name
        ]);
    }
}
