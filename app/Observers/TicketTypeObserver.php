<?php

namespace App\Observers;

use App\Models\TicketType;
use Illuminate\Auth\Access\AuthorizationException;

class TicketTypeObserver
{
    public function creating(TicketType $ticketType): void
    {
        if (session('organization_id') && $ticketType->event->organization_id != session('organization_id')) {
            throw new AuthorizationException('Invalid organization assignment');
        }
    }
    /**
     * Handle the TicketType "created" event.
     */
    public function created(TicketType $ticketType): void
    {
        //
    }

    /**
     * Handle the TicketType "updated" event.
     */
    public function updated(TicketType $ticketType): void
    {
        //
    }

    /**
     * Handle the TicketType "deleted" event.
     */
    public function deleted(TicketType $ticketType): void
    {
        //
    }

    /**
     * Handle the TicketType "restored" event.
     */
    public function restored(TicketType $ticketType): void
    {
        //
    }

    /**
     * Handle the TicketType "force deleted" event.
     */
    public function forceDeleted(TicketType $ticketType): void
    {
        //
    }
}
