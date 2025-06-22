<?php

namespace App\Observers;

use App\Models\Venue;
use Illuminate\Auth\Access\AuthorizationException;

class VenueObserver
{
    /**
     * Handle the Venue "created" event.
     */
    public function created(Venue $venue): void
    {
        //
        if (session('organization_id') && $venue->organization_id != session('organization_id')) {
            throw new AuthorizationException('Invalid organization assignment');
        }
    }

    /**
     * Handle the Venue "updated" event.
     */
    public function updated(Venue $venue): void
    {
        //
    }

    /**
     * Handle the Venue "deleted" event.
     */
    public function deleted(Venue $venue): void
    {
        //
    }

    /**
     * Handle the Venue "restored" event.
     */
    public function restored(Venue $venue): void
    {
        //
    }

    /**
     * Handle the Venue "force deleted" event.
     */
    public function forceDeleted(Venue $venue): void
    {
        //
    }
}
