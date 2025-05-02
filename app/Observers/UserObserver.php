<?php

namespace App\Observers;

use App\Mail\NewUserWelcome;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        //
        $this->sendVerificationEmail($user);
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
        $this->sendVerificationEmail($user);
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
            $user->sendEmailVerificationNotification();
        }
    }
}
