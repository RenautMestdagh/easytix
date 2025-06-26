<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;


class TenancyVerifyEmail extends VerifyEmail
{
    protected function verificationUrl($notifiable)
    {
        if($notifiable->organization) {
            return URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(config('auth.verification.expire', 60)),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                    'subdomain' => $notifiable->organization->subdomain
                ]
            );
        } else {
            return parent::verificationUrl($notifiable);
        }
    }
}
