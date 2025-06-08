<?php

// app/Notifications/CustomVerifyEmail.php
namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Notifications\Messages\MailMessage;

class CustomVerifyEmail extends VerifyEmail
{

    protected bool $isMobile;

    public function __construct(bool $isMobile = false)
    {
        $this->isMobile = $isMobile;
    }


    protected function verificationUrl($notifiable)
    {
        // Decide redirect based on user type or flags
        $redirectUrl = $this->isMobile
            ? config('app.frontend_mobile_url') . 'email-verified'
            : config('app.frontend_web_url') . '/email-verified';

        if ($this->isMobile) {
            $redirectUrl = config('app.frontend_web_url') . '/deeplink?target=' . urlencode($redirectUrl);
        }

        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
                'redirect_url' => $redirectUrl
            ]
        );
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Verify Your Email')
            ->line('Please verify your email address by clicking below.')
            ->action('Verify Email', $this->verificationUrl($notifiable));
    }
}
