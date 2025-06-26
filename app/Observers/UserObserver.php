<?php

namespace App\Observers;

use App\Models\User;
use App\Notifications\TenancyVerifyEmail;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Notifications\Notifiable;

class UserObserver
{
    use Notifiable;
    /**
     * Handle the User "creating" event.
     */
    public function creating(User $user): void
    {
        //
        if (session('organization_id') && $user->organization_id != session('organization_id')) {
            throw new AuthorizationException('Invalid organization assignment');
        }
    }


    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        //
        $this->sendVerificationEmail($user);
    }

    /**
     * Handle the User "updating" event.
     */
    public function updating(User $user): void
    {
        //
        if (session('organization_id') && $user->organization_id != session('organization_id')) {
            throw new AuthorizationException('Invalid organization assignment');
        }
    }

    /**
     * Handle the User "updated" event.
     */

    public function updated(User $user): void
    {
        if ($user->isDirty('email')) {
            // Temporarily disable model event dispatching
            User::withoutEvents(function () use ($user) {
                $user->email_verified_at = null;
                $user->save();
            });

            $this->sendVerificationEmail($user);
        }
    }


    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }

    protected function sendVerificationEmail(User $user)
    {
        if (! $user->hasVerifiedEmail()) {
            $user->notify(new TenancyVerifyEmail());
        }
    }
}
