<?php

namespace App\Observers;

use App\Models\Ticket;
use Illuminate\Auth\Access\AuthorizationException;

class TicketObserver
{
    public function creating(Ticket $ticket): void
    {
        // Get the organization_id through the ticket type and event relationship
        $organizationId = $ticket->ticketType->event->organization_id;

        if (session('organization_id') && $organizationId != session('organization_id')) {
            throw new AuthorizationException('Invalid organization assignment');
        }
    }
    /**
     * Handle the Ticket "created" event.
     */
    public function created(Ticket $ticket): void
    {
        //
    }

    /**
     * Handle the Ticket "updated" event.
     */
    public function updated(Ticket $ticket): void
    {
        //
    }

    /**
     * Handle the Ticket "deleted" event.
     */
    public function deleted(Ticket $ticket): void
    {
        //
    }

    /**
     * Handle the Ticket "restored" event.
     */
    public function restored(Ticket $ticket): void
    {
        //
    }

    /**
     * Handle the Ticket "force deleted" event.
     */
    public function forceDeleted(Ticket $ticket): void
    {
        //
    }
}
