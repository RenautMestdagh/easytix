<?php

namespace App\Livewire\Backend\Tickettypes;

use App\Models\Event;
use Livewire\Component;

class ShowTypes extends Component
{
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
        session(['events.edit.referrer' => url()->current()]);
        return redirect()->route('events.update', $event);
    }

    public function deleteTicketType($ticketTypeId)
    {
        $this->authorize('ticket-types.delete');
        $ticketType = $this->event->ticketTypes()->findOrFail($ticketTypeId);
        if($ticketType->tickets->count() > 0) {
            session()->flash('message', __('Cannot delete ticket type with (reserved) tickets.'));
            session()->flash('message_type', __('error'));
            $this->dispatch('flash-message');
            return;
        }

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
