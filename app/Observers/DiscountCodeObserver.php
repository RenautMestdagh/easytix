<?php

namespace App\Observers;

use App\Models\DiscountCode;
use Illuminate\Auth\Access\AuthorizationException;

class DiscountCodeObserver
{
    public function creating(DiscountCode $discountCode): void
    {
        if (session('organization_id') && $discountCode->organization_id != session('organization_id')) {
            throw new AuthorizationException('Invalid organization assignment');
        }
    }
    /**
     * Handle the DiscountCode "created" event.
     */
    public function created(DiscountCode $discountCode): void
    {
        //
    }

    /**
     * Handle the DiscountCode "updated" event.
     */
    public function updated(DiscountCode $discountCode): void
    {
        //
    }

    /**
     * Handle the DiscountCode "deleted" event.
     */
    public function deleted(DiscountCode $discountCode): void
    {
        //
    }

    /**
     * Handle the DiscountCode "restored" event.
     */
    public function restored(DiscountCode $discountCode): void
    {
        //
    }

    /**
     * Handle the DiscountCode "force deleted" event.
     */
    public function forceDeleted(DiscountCode $discountCode): void
    {
        //
    }
}
