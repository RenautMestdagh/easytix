<?php

namespace App\Observers;

use App\Models\Event;
use Illuminate\Auth\Access\AuthorizationException;

class EventObserver
{
    public function creating(Event $event): void
    {
        if (session('organization_id') && $event->organization_id != session('organization_id')) {
            throw new AuthorizationException('Invalid organization assignment');
        }
    }
    /**
     * Handle the Event "created" event.
     */
    public function created(Event $event): void
    {
        //
    }

    /**
     * Handle the Event "updated" event.
     */
    public function updated(Event $event): void
    {
        //
    }

    /**
     * Handle the Event "deleted" event.
     */
    public function deleted(Event $event): void
    {
        //
    }

    /**
     * Handle the Event "restored" event.
     */
    public function restored(Event $event): void
    {
        //
    }

    /**
     * Handle the Event "force deleted" event.
     */
    public function forceDeleted(Event $event): void
    {
        //
    }
}
